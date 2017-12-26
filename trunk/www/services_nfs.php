<?php
/*
	services_nfs.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2018 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';

function services_nfs_get_sphere() {
	global $config;
	$sphere = new co_sphere_settings('services_nfs','php');
	$sphere->row_default = [
		'enable' => false,
		'v4enable' => false,
		'numproc' => 4,
		'share' => []
	];
	$sphere->grid = &array_make_branch($config,'nfsd');
	if(empty($sphere->grid)):
		$sphere->grid = $sphere->row_default;
		write_config();
		header($sphere->header());
		exit;
	endif;
	array_make_branch($config,'nfsd','share');
	return $sphere;
}
$sphere = &services_nfs_get_sphere();
/*
$a_share = &array_make_branch($config,'nfsd','share');
if(empty($a_share)):
else:
	array_sort_key($a_share,'path');
endif;
*/
$gt_button_apply_confirm = gtext('Do you want to apply these settings?');
$input_errors = [];
$a_message = [];
//	identify page mode
$mode_page = ($_POST) ? PAGE_MODE_POST : PAGE_MODE_VIEW;
switch($mode_page):
	case PAGE_MODE_POST:
		if(isset($_POST['submit'])):
			$page_action = $_POST['submit'];
			switch($page_action):
				case 'edit':
					$mode_page = PAGE_MODE_EDIT;
					break;
				case 'save':
					break;
				case 'enable':
					break;
				case 'disable':
					break;
				default:
					$mode_page = PAGE_MODE_VIEW;
					$page_action = 'view';
					break;
			endswitch;
		else:
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view';
		endif;
		break;
	case PAGE_MODE_VIEW:
		$page_action = 'view';
		break;
endswitch;
//	get configuration data, depending on the source
switch($page_action):
	case 'save':
		$source = $_POST;
		break;
	default:
		$source = $sphere->grid;
		break;
endswitch;
$sphere->row['enable'] = isset($source['enable']);
$sphere->row['v4enable'] = isset($source['v4enable']);
$sphere->row['numproc'] = $source['numproc'] ?? $sphere->row_default['numproc'];
//	process enable
switch($page_action):
	case 'enable':
		if($sphere->row['enable']):
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view';
		else: // enable and run a full validation
			$sphere->row['enable'] = true;
			$page_action = 'save'; // continue with save procedure
		endif;
		break;
endswitch;
//	process save and disable
switch($page_action):
	case 'save':
		// Input validation.
		$reqdfields = ['numproc'];
		$reqdfieldsn = [gtext('Servers')];
		$reqdfieldst = ['numeric'];
		do_input_validation($sphere->row,$reqdfields,$reqdfieldsn,$input_errors);
		do_input_validation_type($sphere->row,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
		if(empty($input_errors)):
			$sphere->copyrowtogrid();
			write_config();
			$retval = 0;
			config_lock();
			rc_exec_script('/etc/rc.d/nfsuserd forcestop');
			if($sphere->row['v4enable']):
				$retval |= mwexec('/usr/local/sbin/rconf service enable nfsv4_server');
				$retval |= mwexec('/usr/local/sbin/rconf service enable nfsuserd');
				if($sphere->row['enable']):
					$retval |= rc_exec_script("/etc/rc.d/nfsuserd start");
				endif;
			else:
				$retval |= mwexec('/usr/local/sbin/rconf service disable nfsv4_server');
				$retval |= mwexec('/usr/local/sbin/rconf service disable nfsuserd');
			endif;
			$retval |= rc_update_service('rpcbind'); // !!! Do
			$retval |= rc_update_service('mountd');  // !!! not
			$retval |= rc_update_service('nfsd');    // !!! change
			$retval |= rc_update_service('statd');   // !!! this
			$retval |= rc_update_service('lockd');   // !!! order
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			header($sphere->header());
			exit;
		else:
			$mode_page = PAGE_MODE_EDIT;
			$page_action = 'edit';
		endif;
		break;
	case 'disable':
/*
	Feb 17 10:25:29	nas4free	root: Failed to stop service nfsd
	Feb 17 10:25:29	nas4free	nfsd[2856]: rpcb_unset failed
	Feb 17 10:25:28	nas4free	root: mountd service stopped
	Feb 17 10:25:28	nas4free	root: rpcbind service stopped
 */
		if($sphere->row['enable']): // if enabled, disable it
			$sphere->row['enable'] = false;
			$sphere->grid['enable'] = $sphere->row['enable'];
			write_config();
			$retval = 0;
			config_lock();
			rc_exec_script('/etc/rc.d/nfsuserd forcestop');
			$retval |= rc_update_service('rpcbind'); // !!! Do
			$retval |= rc_update_service('mountd');  // !!! not
			$retval |= rc_update_service('nfsd');    // !!! change
			$retval |= rc_update_service('statd');   // !!! this
			$retval |= rc_update_service('lockd');   // !!! order
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			header($sphere->header());
			exit;
		endif;
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;
endswitch;
//	determine final page mode
switch($mode_page):
	case PAGE_MODE_EDIT:
		break;
	default:
		if(isset($config['system']['skipviewmode'])):
			$mode_page = PAGE_MODE_EDIT;
			$page_action = 'edit';
		else:
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view';
		endif;
		break;
endswitch;
//  prepare lookups
//	nothing to do
$pgtitle = [gtext('Services'),gtext('NFS')];
include 'fbegin.inc';
switch($mode_page):
	case PAGE_MODE_VIEW:
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
});
//]]>
</script>
<?php
		break;
	case PAGE_MODE_EDIT:
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	$("#iform").submit(function() {	spinner(); });
	$(".spin").click(function() { spinner(); });
	$("#button_save").click(function () {
		return confirm("<?=$gt_button_apply_confirm;?>");
	});
});
//]]>
</script>
<?php
		break;
endswitch;	
?>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="<?=$sphere->scriptname();?>" title="<?=gtext('Reload page');?>"><span><?=gtext('Settings');?></span></a></li>
		<li class="tabinact"><a href="services_nfs_share.php"><span><?=gtext('Shares');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<form action="<?=$sphere->scriptname();?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(file_exists($d_sysrebootreqd_path)):
		print_info_box(get_std_save_message(0));
	endif;
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
	foreach($a_message as $r_message):
		print_info_box($r_message);
	endforeach;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			switch($mode_page):
				case PAGE_MODE_VIEW:
					html_titleline2(gtext('Network File System'));
					break;
				case PAGE_MODE_EDIT:
					html_titleline_checkbox2('enable',gtext('Network File System'),$sphere->row['enable'],gtext('Enable'));
					break;
			endswitch;
?>
		</thead>
		<tbody>
<?php
			switch($mode_page):
				case PAGE_MODE_VIEW:
					html_textinfo2('enable',gtext('Service Enabled'),$sphere->row['enable'] ? gtext('Yes') : gtext('No'));
					html_textinfo2('numproc',gtext('Servers'), htmlspecialchars($sphere->row['numproc']));
					html_checkbox2('v4enable',gtext('NFSv4'),$sphere->row['v4enable'],'','',false,true);
					break;
				case PAGE_MODE_EDIT:
					html_inputbox2('numproc',gtext('Servers'),$sphere->row['numproc'],gtext('Specifies how many servers to create.') . ' ' . gtext('There should be enough to handle the maximum level of concurrency from its clients, typically four to six.'),false,2);
					html_checkbox2('v4enable',gtext('NFSv4'),$sphere->row['v4enable'],gtext('Enable NFSv4 server.'),'',false);
					break;
			endswitch;
?>
		</tbody>
	</table>
	<div id="submit">
<?php
		switch($mode_page):
			case PAGE_MODE_VIEW:
				echo $sphere->html_button('edit',gtext('Edit'));
				if($sphere->row['enable']):
					echo $sphere->html_button('disable',gtext('Disable'));
				else:
					echo $sphere->html_button('enable',gtext('Enable'));
				endif;
				break;
			case PAGE_MODE_EDIT:
				echo $sphere->html_button('save',gtext('Apply'));
				echo $sphere->html_button('cancel',gtext('Cancel'));
				break;
		endswitch;
?>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></table></form>
<?php
include 'fend.inc';
?>
