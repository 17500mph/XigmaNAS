<?php
/*
	access_users.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
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
require 'auth.inc';
require 'guiconfig.inc';

$sphere_scriptname = basename(__FILE__);
$sphere_scriptname_child = 'access_users_edit.php';
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = $sphere_header;
$sphere_notifier = 'userdb_user';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_add = gtext('Add User Account');
$gt_record_mod = gtext('Edit User Account');
$gt_record_del = gtext('User account is marked for deletion');
$gt_record_loc = gtext('User account is locked');
$gt_selection_delete = gtext('Delete Selected User Accounts');
$gt_selection_delete_confirm = gtext('Do you want to delete selected user accounts?');
//	sunrise
$sphere_array = &array_make_branch($config,'access','user');
array_sort_key($sphere_array,'login');
$a_group = system_get_group_list();

if($_POST):
	if(isset($_POST['apply']) && $_POST['apply']):
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			$retval |= updatenotify_process($sphere_notifier,'userdbuser_process_updatenotification');
			config_lock();
			$retval |= rc_exec_service('userdb');
			$retval |= rc_exec_service('websrv_htpasswd');
			$retval |= rc_exec_service('fmperm');
			if(isset($config['samba']['enable'])):
				$retval |= rc_exec_service('passdb');
				$retval |= rc_update_service('samba');
			endif;
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
		if($retval == 0):
			updatenotify_delete($sphere_notifier);
		endif;
//		header($sphere_header);
//		exit;
	endif;
	if(isset($_POST['submit'])):
		switch($_POST['submit']):
			case 'rows.delete':
				$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
				foreach($checkbox_member_array as $checkbox_member_record):
					if(false !== ($index_uuid = array_search_ex($checkbox_member_record,$sphere_array,'uuid'))):
						$mode_updatenotify = updatenotify_get_mode($sphere_notifier,$sphere_array[$index_uuid]['uuid']);
						switch ($mode_updatenotify):
							case UPDATENOTIFY_MODE_NEW:  
								updatenotify_clear($sphere_notifier,$sphere_array[$index_uuid]['uuid']);
								updatenotify_set($sphere_notifier,UPDATENOTIFY_MODE_DIRTY_CONFIG,$sphere_array[$index_uuid]['uuid']);
								break;
							case UPDATENOTIFY_MODE_MODIFIED:
								updatenotify_clear($sphere_notifier,$sphere_array[$index_uuid]['uuid']);
								updatenotify_set($sphere_notifier,UPDATENOTIFY_MODE_DIRTY,$sphere_array[$index_uuid]['uuid']);
								break;
							case UPDATENOTIFY_MODE_UNKNOWN:
								updatenotify_set($sphere_notifier,UPDATENOTIFY_MODE_DIRTY,$sphere_array[$index_uuid]['uuid']);
								break;
						endswitch;
					endif;
				endforeach;
				header($sphere_header);
				exit;
				break;
		endswitch;
	endif;
endif;

function userdbuser_process_updatenotification($mode,$data) {
	global $config;

	$retval = 0;
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if(false !== ($index_uuid = array_search_ex($data,$config['access']['user'],'uuid'))):
				unset($config['access']['user'][$index_uuid]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
$pgtitle = [gtext('Access'),gtext('Users')];
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load",function() {
<?php // Init action buttons.?>
	$("#delete_selected_rows").click(function () {
		return confirm('<?=$gt_selection_delete_confirm;?>');
	});
<?php // Disable action buttons.?>
	disableactionbuttons(true);
<?php // Init toggle checkbox.?>
	$("#togglemembers").click(function() {
		togglecheckboxesbyname(this,"<?=$checkbox_member_name;?>[]");
	});
<?php // Init member checkboxes.?>
	$("input[name='<?=$checkbox_member_name;?>[]']").click(function() {
		controlactionbuttons(this,'<?=$checkbox_member_name;?>[]');
	});
<?php // Init spinner on submit for id form.?>
	$("#iform").submit(function() { spinner(); });
<?php // Init spinner on click for class spin.?>
	$(".spin").click(function() { spinner(); });
}); 
function disableactionbuttons(ab_disable) {
	var ab_element;
	ab_element = document.getElementById('delete_selected_rows'); if((ab_element !== null) && (ab_element.disabled !== ab_disable)) { ab_element.disabled = ab_disable; }
}
function togglecheckboxesbyname(ego,triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if(a_trigger[i].type === 'checkbox') {
			if(!a_trigger[i].disabled) {
				a_trigger[i].checked = !a_trigger[i].checked;
				if(a_trigger[i].checked) {
					ab_disable = false;
				}
			}
		}
	}
	if(ego.type === 'checkbox') { ego.checked = false; }
	disableactionbuttons(ab_disable);
}
function controlactionbuttons(ego,triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if(a_trigger[i].type === 'checkbox') {
			if(a_trigger[i].checked) {
				ab_disable = false;
				break;
			}
		}
	}
	disableactionbuttons(ab_disable);
}
//]]>
</script>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="access_users.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Users');?></span></a></li>
		<li class="tabinact"><a href="access_users_groups.php"><span><?=gtext('Groups');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" id="iform" name="iform">
	<?php
	if(!empty($savemsg)): 
		print_info_box($savemsg);
	endif;
	if(file_exists($d_sysrebootreqd_path)):
		print_info_box(get_std_save_message(0));
	endif;
	if(updatenotify_exists($sphere_notifier)):
		print_config_change_box();
	endif;
	?>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:5%">
			<col style="width:25%">
			<col style="width:25%">
			<col style="width:10%">
			<col style="width:25%">
			<col style="width:10%">
		</colgroup>
		<thead>
			<?php html_titleline2(gtext('Overview'),6);?>
			<tr>
				<th class="lhelc"><input type="checkbox" id="togglemembers" name="togglemembers" title="<?=gtext('Invert Selection');?>"/></th>
				<th class="lhell"><?=gtext('User');?></th>
				<th class="lhell"><?=gtext('Full Name');?></th>
				<th class="lhell"><?=gtext('UID');?></th>
				<th class="lhell"><?=gtext('Group');?></th>
				<th class="lhebl"><?=gtext('Toolbox');?></th>
			</tr>
		</thead>
		<tfoot>
			<?=html_row_add($sphere_scriptname_child,$gt_record_add,6);?>
		</tfoot>
		<tbody>
			<?php foreach($sphere_array as $sphere_record):?>
				<tr>
					<?php
					$notificationmode = updatenotify_get_mode($sphere_notifier,$sphere_record['uuid']);
					$notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
					$enabled = true; // isset($sphere_record['enable']);
					$notprotected = !isset($sphere_record['protected']);
					?>
					<td class="<?=$enabled ? "lcelc" : "lcelcd";?>">
						<?php if($notdirty && $notprotected):?>
							<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>"/>
						<?php else:?>
							<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" disabled="disabled"/>
						<?php endif;?>
					</td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['login']);?></td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['fullname']);?></td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['id']);?></td>
					
					<?php
					$a_group_key = [];
					$a_group_key[] = array_search($sphere_record['primarygroup'],$a_group);
					if(is_array($sphere_record['group'])):
						foreach($sphere_record['group'] as $needle):
							$a_group_key[] = array_search($needle,$a_group);
						endforeach;
					endif;
					$helpinghand = implode(', ',$a_group_key);
					?>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($helpinghand);?></td>
					<td class="lcebld">
						<table class="area_data_selection_toolbox"><tbody><tr>
							<?php
							$helpinghand = sprintf('%s?uuid=%s',$sphere_scriptname_child,$sphere_record['uuid']);
							echo html_row_toolbox($helpinghand,$gt_record_mod,$gt_record_del,$gt_record_loc,$notprotected,$notdirty);
							?>
							<td></td>
							<td></td>
						</tr></tbody></table>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<div id="submit">
		<?php
		echo html_button_delete_rows($gt_selection_delete);
		?>
	</div>
	<?php include 'formend.inc';?>
</form></td></tr></tbody></table>
<?php include 'fend.inc';?>
