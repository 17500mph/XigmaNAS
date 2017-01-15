<?php
/*
	services_minidlna.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright
	   notice, this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE NAS4FREE PROJECT ``AS IS'' AND ANY
	EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
	IN NO EVENT SHALL THE NAS4FREE PROJECT OR ITS CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
	THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
require 'auth.inc';
require 'guiconfig.inc';
require 'services.inc';

$sphere_scriptname = basename(__FILE__);
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = 'Location: index.php';
$sphere_record = [];
$a_message = [];
$gt_apply_confirm = gtext('Do you want to apply these settings?');

array_make_branch($config,'minidlna','content');
$sphere_array = &$config['minidlna'];
sort($sphere_array['content']);
// we need information about other DLNA services
array_make_branch($config,'upnp','content');

/*	calculate initial page mode and page action.
 *	at the end of this section a valid page mode and a valid page action are available.
 *	page_action cancel is switched to view mode.
 *	mode_page: page_action:
 *		PAGE_MODE_EDIT: edit
 *		PAGE_MODE_POST: enable, disable, rescan, save
 *		PAGE_MODE_VIEW: view
 */
$mode_page = ($_POST) ? PAGE_MODE_POST : PAGE_MODE_VIEW;
switch($mode_page):
	case PAGE_MODE_POST:
		if(isset($_POST['submit'])):
			$page_action = $_POST['submit'];
			switch($page_action):
				case 'edit':
					$mode_page = PAGE_MODE_EDIT;
					break;
				case 'rescan':
					break;
				case 'save': // save = edit < POST and save
					break;
				case 'rows.enable':
					$page_action = 'enable';
					break;
				case 'rows.disable':
					$page_action = 'disable';
					break;
				case 'cancel':
					$mode_page = PAGE_MODE_VIEW;
					$page_action = 'view';
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
	case PAGE_MODE_EDIT:
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;			
	default:
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;
endswitch;
/*	initial source of data
 *	only page_action save takes information from _POST
 */
switch($page_action):
	case 'save': 
		$sphere_record['enable'] = isset($_POST['enable']);
		$sphere_record['name'] = $_POST['name'] ?? '';
		$sphere_record['if'] = $_POST['if'] ?? '';
		$sphere_record['port'] = $_POST['port'] ?? '8200';
		$sphere_record['home'] = is_string($_POST['home']) ?? '';
		$sphere_record['notify_int'] = $_POST['notify_int'] ?? '300';
		$sphere_record['strict'] = isset($_POST['strict']);
		$sphere_record['loglevel'] = $_POST['loglevel'] ?? 'info';
		$sphere_record['tivo'] = isset($_POST['tivo']);
		$sphere_record['content'] = is_array($_POST['content']) ? $_POST['content'] : [];
		$sphere_record['container'] = $_POST['container'] ?? 'B';
		$sphere_record['inotify'] = isset($_POST['inotify']);
		break;
	default:
		$sphere_record['enable'] = isset($sphere_array['enable']);
		$sphere_record['name'] = $sphere_array['name'] ?? '';
		$sphere_record['if'] = $sphere_array['if'] ?? '';
		$sphere_record['port'] = $sphere_array['port'] ?? '8200';
		$sphere_record['home'] = $sphere_array['home'] ?? '';
		$sphere_record['notify_int'] = $sphere_array['notify_int'] ?? '300';
		$sphere_record['strict'] = isset($sphere_array['strict']);
		$sphere_record['loglevel'] = $sphere_array['loglevel'] ?? 'warn';
		$sphere_record['tivo'] = isset($$sphere_array['tivo']);
		$sphere_record['content'] = $sphere_array['content'] ?? [];
		$sphere_record['container'] = $sphere_array['container'] ?? 'B';
		$sphere_record['inotify'] = isset($sphere_array['inotify']);
		break;
endswitch;

switch($page_action):
	case 'enable':
/*	
 *	page_action enable requires full validation of parameter.
 *	switch to page mode view if it's already enabled.
 */
		if($sphere_record['enable']):
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view'; 
		else: // enable and run a full validation
			$sphere_record['enable'] = true;
			$page_action = 'save'; // continue with save procedure
		endif;
		break;
	case 'disable':
/*
 *	page_action disable modifies $config but doesn't require validation.
 *	switch to page mode view if it's already disabled.
 */
		if($sphere_record['enable']): // if enabled, disable it
			$sphere_record['enable'] = false;
			$sphere_array = $sphere_record;
			write_config();
			$retval = 0;
			config_lock();
			$retval |= rc_update_service('minidlna');
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			$a_message[] = gtext('MniDLNA has been disabled.');
		endif;
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;
	case 'rescan':
/*
 *	page action rescan calls the rescan option of the service
 *	switch to page mode view after that.
 */
		if($sphere_record['enable']):
			$retval = rc_rescan_service('minidlna'); 
			$a_message[] = gtext('A rescan has been issued.');
		endif;
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;
endswitch;

switch($page_action):
	case 'save':
		$input_errors = [];
		// Validate content.
		if(empty($sphere_record['content'])):
			$input_errors[] = gtext('Please define one or more content locations.');
		endif;
		// Validate home
		if(preg_match('/\S/',$sphere_record['home'])):
			if(is_dir($sphere_record['home'])):
				var_dump(sprintf('Value of home: [%s]',$sphere_record['home']));
			else:
				$input_errors[] = gtext('The location of the media library is not a valid folder.');
			endif;
		else:
			$input_errors[] = gtext('Please define the location for the media library.');
		endif;
		//	process if no errors
		if(empty($input_errors)):
			$sphere_array = $sphere_record;
			write_config();
			$retval = 0;
			chown($sphere_record['home'],'dlna');
			chmod($sphere_record['home'],0755);
			config_lock();
			$retval != rc_stop_service('minidlna');
			$retval = $retval << 1;
			$retval |=  rc_update_service('minidlna');
			$retval = $retval << 1;
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			$a_message[] = gtext('Changes have been applied.');
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view';
		else:
			$mode_page = PAGE_MODE_EDIT;
			$page_action = 'edit';
		endif;
		break;
endswitch;

$a_interface = get_interface_list();
$l_interfaces = [];
foreach($a_interface as $if => $ifinfo):
	$ifinfo = get_interface_info($if);
	if(('up' == $ifinfo['status']) || ('associated' == $ifinfo['status'])):
		$l_interfaces[$if] = $if;
	endif;
endforeach;
$l_container = [
	'.' => gtext('Standard'),
	'B' => gtext('Browse Directory'),
	'M' => gtext('Music'),
	'V' => gtext('Video'),
	'P' => gtext('Pictures')
];
$l_loglevel = [
	'off' => gtext('Off'),
	'fatal' => gtext('Fatal'),
	'error' => gtext('Error'),
	'warn' => gtext('Warning'),
	'info' => gtext('Info'),
	'debug' => gtext('Debug')
];
// Identifiy enabled DLNA services
$dlna_count = 0;
$dlna_count += (isset($config['minidlna']['enable'])) ? 1 : 0;
$dlna_count += (isset($config['upnp']['enable'])) ? 2 : 0;

// everything greater than 1 indicates that another DLNA service is running somewhere else
// every odd number  indicates that this DLNA service is enabled.
switch($dlna_count):
	case 0:
		$dlna_option = 0; // DLNA can be enabled, no access to link
		break;
	case 1:
		$dlna_option = 1; // DLNA can be disabled, access to link
		break;
	default:
		if($dlna_count & 1):
			$dlna_option = 3; // Warning, DLNA can be disabled, access to link
			$a_message[] = gtext('More than one DLNA/UPnP service is active. This configuration might cause issues.');
		else:
			$dlna_option = 2; // Warning, DLNA no access to enable, no access to link
			$a_message[] = gtext('Another DLNA/UPnP service is already running. Enabling MiniDLNA might cause issues.');
		endif;
		break;
endswitch;
$pgtitle = [gtext('Services'),gtext('DLNA/UPnP MiniDLNA')];
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init onsubmit()
	$("#iform").submit(function() {
		spinner();
	});
	$("#button_save").click(function () {
		return confirm('<?=$gt_apply_confirm;?>');
	});
});
//]]>
</script>
<table id="area_navigator"><tbody><tr><td class="tabnavtbl">
	<ul id="tabnav">
		<li class="tabinact"><a href="services_fuppes.php"><span><?=gtext('Fuppes')?></span></a></li>
		<li class="tabact"><a href="services_minidlna.php"><span><?=gtext('MiniDLNA');?></span></a></li>
	</ul>
</td></tr></tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform">
	<?php 
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
			switch($page_action):
				case 'view':
					html_titleline2(gtext('MiniDLNA A/V Media Server'));
					break;
				case 'edit':
					html_titleline_checkbox2('enable',gtext('MiniDLNA A/V Media Server'),$sphere_array['enable'],gtext('Enable'));
					break;
			endswitch;
			?>
		</thead>
		<tbody>
			<?php
			switch($page_action):
				case 'view':
					html_checkbox2('enable',gtext('Service Enabled'),$sphere_record['enable'],'','',false,true);
					html_text2('name',gtext('Name'),$sphere_record['name']);
					html_text2('if',gtext('Interface Selection'),$sphere_record['if']);
					html_text2('port',gtext('Port'),$sphere_record['port']);
					html_text2('notify_int',gtext('Broadcast Interval'),$sphere_record['notify_int']);
					html_text2('home',gtext('Library Folder'),$sphere_record['home']);
					$helpinghand = implode("\n",$sphere_record['content']);
					html_textarea2('content',gtext('Content Locations'),$helpinghand,'',false,67,5,true,false);
					html_checkbox2('inotify',gtext('Inotify'),$sphere_record['inotify'],'','',false,true);
					html_text2('container',gtext('Container'),$l_container[$sphere_record['container']] ?? '');
					html_checkbox2('strict',gtext('Strict DLNA'),$sphere_record['strict'],'','',false,true);
					html_checkbox2('tivo',gtext('TiVo Support'),$sphere_record['tivo'],'','',false,true);
					html_text2('loglevel',gtext('Log Level'),$l_loglevel[$sphere_record['loglevel']] ?? '');
					if($dlna_option & 1):
						html_separator2();
						html_titleline2(gtext('MiniDLNA Media Server WebGUI'));
						$if = get_ifname($sphere_record['if']);
						$ipaddr = get_ipaddr($if);
						$url = htmlspecialchars(sprintf('http://%s:%s/status',$ipaddr,$sphere_record['port']));
						$text = sprintf('<a href="%s" target="_blank">%s</a>',$url,$url);
						html_text2('url',gtext('URL'),$text);
					endif;
					break;
				case 'edit':
					html_inputbox2('name',gtext('Name'),$sphere_record['name'],gtext('Give your media library a friendly name.'),true,35,false,false,35,gtext('Media server name'));
					html_combobox2('if',gtext('Interface Selection'),$sphere_record['if'],$l_interfaces,gtext('Select which interface to use. (Only selectable if your server has more than one interface)'),true);
					html_inputbox2('port',gtext('Port'),$sphere_record['port'],sprintf(gtext('Port to listen on. Only dynamic or private ports can be used (from %d through %d). Default port is %d.'),1025,65535, 8200),true,5);
					html_inputbox2('notify_int',gtext('Broadcast Interval'),$sphere_record['notify_int'],gtext('Broadcasts its availability every N seconds on the network. (Default 300 seconds)'),true,5);
					html_filechooser2('home',gtext('Library Folder'),$sphere_record['home'],gtext('Location of the media content database.'),$g['media_path'],true,67);
					html_minidlnabox2('content',gtext('Content Locations'),$sphere_record['content'],gtext('Manage content locations.'),$g['media_path'],true);
					html_checkbox2('inotify',gtext('Inotify'),$sphere_record['inotify'],gtext('Enable inotify.'),gtext('Use inotify monitoring to automatically discover new files.'),false);
					html_combobox2('container',gtext('Container'),$sphere_record['container'],$l_container,gtext('Use different container as root of the tree.'),false,false,'');
					html_checkbox2('strict',gtext('Strict DLNA'),$sphere_record['strict'],gtext('Enable to strictly adhere to DLNA standards.'),gtext('This will allow server-side downscaling of very large JPEG images, it can impact JPEG serving performance on some DLNA products.'),false);
					html_checkbox2('tivo',gtext('TiVo Support'),$sphere_record['tivo'],gtext('Enable TiVo support.'),gtext('This will support streaming .jpg and .mp3 files to a TiVo supporting HMO.'),false);
					html_combobox2('loglevel',gtext('Log Level'),$sphere_record['loglevel'],$l_loglevel,'',false,false,'');
				break;
			endswitch;
			?>
		</tbody>
	</table>
	<div id="submit">
		<?php
		switch($page_action):
			case 'view';
				echo html_button_edit(gtext('Edit'));
				if($dlna_option & 1):
					echo html_button_rescan(gtext('Rescan'));
				endif;
				if($sphere_record['enable']):
					echo html_button_disable_rows(gtext('Disable'));
				else:
					echo html_button_enable_rows(gtext('Enable'));
				endif;
				break;
			case 'edit':
				echo html_button_save(gtext('Apply'));
				echo html_button_cancel(gtext('Cancel'));
				break;
		endswitch;
		?>
	</div>
	<?php include 'formend.inc';?>
</form></td></tr></tbody></table>
<?php include 'fend.inc';?>
