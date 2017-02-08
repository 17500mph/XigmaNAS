<?php
/*
	access_users_groups.php

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
$sphere_scriptname_child = 'access_users_groups_edit.php';
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = $sphere_header;
$sphere_notifier = 'userdb_group';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_add = gtext('Add Group');
$gt_record_mod = gtext('Edit Group');
$gt_record_del = gtext('Group is marked for deletion');
$gt_record_loc = gtext('Group is locked');
$gt_selection_delete = gtext('Delete Selected Groups');
$gt_selection_delete_confirm = gtext('Do you want to delete selected groups?');
//	sunrise
$sphere_array = &array_make_branch($config,'access','group');
//	settings for config['access']
$access_settings = &array_make_branch($config,'access','settings');
$showsystemgroups = !isset($access_settings['hidesystemgroups']);

if($_POST):
	if(isset($_POST['apply']) && $_POST['apply']):
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			$retval |= updatenotify_process($sphere_notifier,'userdbgroup_process_updatenotification');
			config_lock();
			$retval |= rc_exec_service('userdb');
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
		if($retval == 0):
			updatenotify_delete($sphere_notifier);
		endif;
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
			case 'rows.enable':
				if(!$showsystemgroups):
					$access_settings['hidesystemgroups'] = false;
					write_config();
					header($sphere_header);
					exit;
				endif;
				break;
			case 'rows.disable':
				if($showsystemgroups):
					$access_settings['hidesystemgroups'] = true;
					write_config();
					header($sphere_header);
					exit;
				endif;
				break;
		endswitch;
	endif;
endif;

function userdbgroup_process_updatenotification($mode,$data) {
	global $config;

	$retval = 0;
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if (false !== ($index_uuid = array_search_ex($data,$config['access']['group'],'uuid'))):
				unset($config['access']['group'][$index_uuid]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
$a_group = system_get_group_list();
/*
 *	a_group[groupname] => groupid
 *	sphere_array[] => [name,id,desc,uuid]
 */
$l_group = [];
if($showsystemgroups):
	if(is_array($a_group)):
		$helpinghand = gtext('System');
		foreach($a_group as $key => $val):
			$l_group[$key] = ['name' => $key,'id' => $val,'desc' => $helpinghand,'uuid' => '','protected' => true,'enable' => false];
		endforeach;
	endif;
endif;
foreach($sphere_array as $sphere_record):
	$key = $sphere_record['name'];
	if(preg_match('/\S/',$key)):
		if(!isset($l_group[$key])): // add or update group
			$l_group[$key] = [];
			$l_group[$key]['name'] = $key;
			$l_group[$key]['id'] = $sphere_record['id'];
		endif;
		$l_group[$key]['desc'] = $sphere_record['desc'] ?? '';
		$l_group[$key]['uuid'] = $sphere_record['uuid'];
		$l_group[$key]['protected'] = false;
		$l_group[$key]['enable'] = true;
	endif;
endforeach;
array_sort_key($l_group,'name');

$pgtitle = [gtext('Access'), gtext('Groups')];
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
		<li class="tabinact"><a href="access_users.php"><span><?=gtext('Users');?></span></a></li>
		<li class="tabact"><a href="access_users_groups.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Groups');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" id="iform" name="iform">
	<?php
	if($savemsg):
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
			<col style="width:10%">
			<col style="width:50%">
			<col style="width:10%">
		</colgroup>
		<thead>
			<?php html_titleline2(gtext('Overview'),5);?>
			<tr>
				<th class="lhelc"><input type="checkbox" id="togglemembers" name="togglemembers" title="<?=gtext('Invert Selection');?>"/></th>
				<th class="lhell"><?=gtext('Group');?></th>
				<th class="lhell"><?=gtext('GID');?></th>
				<th class="lhell"><?=gtext('Description');?></th>
				<th class="lhebl"><?=gtext('Toolbox');?></th>
			</tr>
		</thead>
		<tfoot>
			<?=html_row_add($sphere_scriptname_child,$gt_record_add,5);?>
		</tfoot>
		<tbody>
			<?php foreach($l_group as $sphere_record):?>
				<tr>
					<?php
					$notprotected = !$sphere_record['protected'];
					if($notprotected):
						$notificationmode = updatenotify_get_mode($sphere_notifier,$sphere_record['uuid']);
					else:
						$notificationmode = UPDATENOTIFY_MODE_UNKNOWN;
					endif;
					$notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
					$enabled = $sphere_record['enable'];
					?>
					<td class="<?=$enabled ? "lcelc" : "lcelcd";?>">
						<?php if($notdirty && $notprotected):?>
							<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>"/>
						<?php else:?>
							<input type="checkbox" name="notapplicable" value="notapplicable" disabled="disabled"/>
						<?php endif;?>
					</td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['name']);?></td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['id']);?></td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['desc']);?></td>
					<td class="lcebld">
						<table class="area_data_selection_toolbox"><tbody><tr>
							<?php
							if($notprotected):
								$helpinghand = sprintf('%s?uuid=%s',$sphere_scriptname_child,$sphere_record['uuid']);
								echo html_row_toolbox($helpinghand,$gt_record_mod,$gt_record_del,$gt_record_loc,$notprotected,$notdirty);
							else:
								echo html_row_toolbox('','','',$gt_record_loc,$notprotected,$notdirty);
							endif;
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
		if($showsystemgroups):
			echo html_button_disable_rows(gtext('Hide Default Groups'));
		else:
			echo html_button_enable_rows(gtext('Show Default Groups'));
		endif;
		?>
	</div>
	<?php include 'formend.inc';?>
</form></td></tr></tbody></table>
<?php include 'fend.inc';
