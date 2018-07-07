<?php
/*
	system_rcconf.php

	Part of XigmaNAS (http://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
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
	either expressed or implied, of the XigmaNAS Project.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';

function system_rcconf_get_sphere() {
	global $config;
	$sphere = new co_sphere_grid('system_rcconf','php');
	$sphere->modify->set_basename($sphere->get_basename() . '_edit');
	$sphere->set_notifier('rcconf');
	$sphere->set_row_identifier('uuid');
	$sphere->enadis(true);
	$sphere->lock(false);
	$sphere->sym_add(gtext('Add Option'));
	$sphere->sym_mod(gtext('Edit Option'));
	$sphere->sym_del(gtext('Option is marked for deletion'));
	$sphere->sym_loc(gtext('Option is protected'));
	$sphere->sym_unl(gtext('Option is unlocked'));
	$sphere->cbm_delete(gtext('Delete Selected Options'));
	$sphere->cbm_delete_confirm(gtext('Do you want to delete selected options?'));
	$sphere->cbm_disable(gtext('Disable Selected Options'));
	$sphere->cbm_disable_confirm(gtext('Do you want to disable selected options?'));
	$sphere->cbm_enable(gtext('Enable Selected Options'));
	$sphere->cbm_enable_confirm(gtext('Do you want to enable selected options?'));
	$sphere->cbm_toggle(gtext('Toggle Selected Options'));
	$sphere->cbm_toggle_confirm(gtext('Do you want to toggle selected options?'));
	$sphere->grid = &array_make_branch($config,'system','rcconf','param');
	return $sphere;
}
function rcconf_process_updatenotification($mode,$data) {
	global $config;
	$retval = 0;
	$sphere = &system_rcconf_get_sphere();
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				$cmd = sprintf('/usr/local/sbin/rconf attribute remove %s',escapeshellarg($sphere->grid[$sphere->row_id]['name']));
				mwexec2($cmd);
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
$sphere = &system_rcconf_get_sphere();
if($_POST):
	if(isset($_POST['apply']) && $_POST['apply']):
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			$retval |= updatenotify_process($sphere->get_notifier(),$sphere->get_notifier_processor());
			config_lock();
			$retval |= rc_exec_service($sphere->get_notifier());
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
		if($retval == 0):
			updatenotify_delete($sphere->get_notifier());
		endif;
		header($sphere->get_location());
		exit;
	endif;
	if(isset($_POST['submit'])):
		switch( $_POST['submit']):
			case $sphere->get_cbm_button_val_delete():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier()))):
						$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
						switch($mode_updatenotify):
							case UPDATENOTIFY_MODE_NEW:  
								updatenotify_clear($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY_CONFIG,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								break;
							case UPDATENOTIFY_MODE_MODIFIED:
								updatenotify_clear($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								break;
							case UPDATENOTIFY_MODE_UNKNOWN:
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								break;
						endswitch;
					endif;
				endforeach;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_disable():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier()))):
						if(isset($sphere->grid[$sphere->row_id]['enable'])):
							unset($sphere->grid[$sphere->row_id]['enable']);
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_enable():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier()))):
						if(!(isset($sphere->grid[$sphere->row_id]['enable']))):
							$sphere->grid[$sphere->row_id]['enable'] = true;
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_toggle():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier()))):
						if(isset($sphere->grid[$sphere->row_id]['enable'])):
							unset($sphere->grid[$sphere->row_id]['enable']);
						else:
							$sphere->grid[$sphere->row_id]['enable'] = true;
						endif;
						$updateconfig = true;
						$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
						if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
							updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
		endswitch;
	endif;
endif;
if(empty($sphere->grid)):
else:
	$key1 = array_column($sphere->grid,'name');
	$key2 = array_column($sphere->grid,'uuid');
	array_multisort($key1,SORT_ASC,SORT_NATURAL | SORT_FLAG_CASE,$key2,SORT_ASC,SORT_STRING | SORT_FLAG_CASE,$sphere->grid);
endif;
$pgtitle = [gtext('System'),gtext('Advanced'),gtext('rc.conf')];
include 'fbegin.inc';
echo $sphere->doj();
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('system_advanced.php',gtext('Advanced'))->
			ins_tabnav_record('system_email.php',gtext('Email'))->
			ins_tabnav_record('system_email_reports.php',gtext('Email Reports'))->
			ins_tabnav_record('system_monitoring.php',gtext('Monitoring'))->
			ins_tabnav_record('system_swap.php',gtext('Swap'))->
			ins_tabnav_record('system_rc.php',gtext('Command Scripts'))->
			ins_tabnav_record('system_cron.php',gtext('Cron'))->
			ins_tabnav_record('system_loaderconf.php',gtext('loader.conf'))->
			ins_tabnav_record('system_rcconf.php',gtext('rc.conf'),gtext('Reload page'),true)->
			ins_tabnav_record('system_sysctl.php',gtext('sysctl.conf'))->
			ins_tabnav_record('system_syslogconf.php',gtext('syslog.conf'));
$document->render();
?>
<form action="<?=$sphere->get_scriptname();?>" method="post" id="iform" name="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
	if(file_exists($d_sysrebootreqd_path)):
		print_info_box(get_std_save_message(0));
	endif;
	if(updatenotify_exists($sphere->get_notifier())):
		print_config_change_box();
	endif;
?>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:5%">
			<col style="width:30%">
			<col style="width:20%">
			<col style="width:5%">
			<col style="width:30%">
			<col style="width:10%">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('Overview'),6);
?>
			<tr>
				<th class="lhelc"><?=$sphere->html_checkbox_toggle_cbm();?></th>
				<th class="lhell"><?=gtext('Variable');?></th>
				<th class="lhell"><?=gtext('Value');?></th>
				<th class="lhell"><?=gtext('Status');?></th>
				<th class="lhell"><?=gtext('Comment');?></th>
				<th class="lhebl"><?=gtext('Toolbox');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach ($sphere->grid as $sphere->row):
				$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->row[$sphere->get_row_identifier()]);
				$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
				$is_enabled = $sphere->enadis() ? isset($sphere->row['enable']) : true;
				$is_notprotected = $sphere->lock() ? !isset($sphere->row['protected']) : true;
				$src = ($is_enabled) ? $g_img['ena'] : $g_img['dis'];
				$title = ($is_enabled) ? gtext('Enabled') : gtext('Disabled');
?>
				<tr>
					<td class="<?=$is_enabled ? "lcelc" : "lcelcd";?>">
<?php
						if($is_notdirty && $is_notprotected):
							echo $sphere->html_checkbox_cbm(false);
						else:
							echo $sphere->html_checkbox_cbm(true);
						endif;
?>
					</td>
					<td class="<?=$is_enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere->row['name']);?></td>
					<td class="<?=$is_enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere->row['value']);?></td>
					<td class="<?=$is_enabled ? "lcelc" : "lcelcd";?>">
						<a title="<?=$title;?>"><img src="<?=$src;?>" alt="" class="oneemhigh"/></a>
					</td>
					<td class="<?=$is_enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere->row['comment']);?></td>
					<td class="lcebld">
						<table class="area_data_selection_toolbox">
							<colgroup>
								<col style="width:33%">
								<col style="width:34%">
								<col style="width:33%">
							</colgroup>
							<tbody><tr>
<?php
								echo $sphere->html_toolbox($is_notprotected,$is_notdirty);
?>
								<td></td>
								<td></td>
							</tr></tbody>
						</table>
					</td>
				</tr>
<?php
			endforeach;
?>
		</tbody>
		<tfoot>
<?php
			echo $sphere->html_footer_add(6);
?>
		</tfoot>
	</table>
	<div id="submit">
<?php
		if($sphere->enadis()):
			if($sphere->toggle()):
				echo $sphere->html_button_toggle_rows();
			else:
				echo $sphere->html_button_enable_rows();
				echo $sphere->html_button_disable_rows();
			endif;
		endif;
		echo $sphere->html_button_delete_rows();
?>
	</div>
	<div id="remarks">
<?php 
		html_remark2('note',gtext('Note'),gtext('These option(s) will be added to /etc/rc.conf. This allow you to overwrite options used by various generic startup scripts.'));
?>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
