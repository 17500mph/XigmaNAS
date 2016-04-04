<?php
/*
	disks_raid_gconcat.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
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
require("auth.inc");
require("guiconfig.inc");

$sphere_scriptname = basename(__FILE__);
$sphere_scriptname_child = 'disks_raid_gconcat_edit.php';
$sphere_header = 'Location: ' . $sphere_scriptname;
$sphere_header_parent = $sphere_header;
$sphere_notifier = 'raid_gconcat';
$sphere_notifier_processor = 'gconcat_process_updatenotification';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_add = gettext('Add RAID');
$gt_record_mod = gettext('Edit RAID');
$gt_record_del = gettext('RAID is marked for removal');
$gt_record_loc = gettext('RAID is protected');

// sunrise: verify if setting exists, otherwise run init tasks
if (!(isset($config['gconcat']['vdisk']) && is_array($config['gconcat']['vdisk']))) {
	$config['gconcat']['vdisk'] = [];
}
array_sort_key($config['gconcat']['vdisk'], 'name');
$sphere_array = &$config['gconcat']['vdisk'];

// get active mounts
mwexec2("mount | awk '{print $1}'", $a_mount);

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			// Process notifications
			$retval = updatenotify_process($sphere_notifier, $sphere_notifier_processor);
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete($sphere_notifier);
		}
		header($sphere_header);
		exit;
	}
	if (isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']) {
		$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
		foreach ($checkbox_member_array as $checkbox_member_record) {
			if (false !== ($index = array_search_ex($checkbox_member_record, $sphere_array, 'uuid'))) {
				if (!isset($sphere_array[$index]['protected'])) {
					$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_array[$index]['uuid']);
					switch ($mode_updatenotify) {
						case UPDATENOTIFY_MODE_NEW:  
							updatenotify_clear($sphere_notifier, $sphere_array[$index]['uuid']);
							updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY_CONFIG, $sphere_array[$index]['uuid']);
							break;
						case UPDATENOTIFY_MODE_MODIFIED:
							updatenotify_clear($sphere_notifier, $sphere_array[$index]['uuid']);
							updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY, $sphere_array[$index]['uuid']);
							break;
						case UPDATENOTIFY_MODE_UNKNOWN:
							updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY, $sphere_array[$index]['uuid']);
							break;
					}
				}
			}
		}
		header($sphere_header);
		exit;
	}
}

function gconcat_process_updatenotification($mode, $data) {
	global $config;
	$retval = 0;
	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
			$retval |= rc_exec_service('geom load concat');
			$retval |= disks_raid_gconcat_configure($data);
			break;
		case UPDATENOTIFY_MODE_MODIFIED:
			$retval |= rc_exec_service('geom start concat');
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if (is_array($config['gconcat']['vdisk'])) {
				$index = array_search_ex($data, $config['gconcat']['vdisk'], 'uuid');
				if (false !== $index) {
					unset($config['gconcat']['vdisk'][$index]);
					write_config();
				}
			}
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			$retval |= disks_raid_gconcat_delete($data);
			if (is_array($config['gconcat']['vdisk'])) {
				$index = array_search_ex($data, $config['gconcat']['vdisk'], 'uuid');
				if (false !== $index) {
					unset($config['gconcat']['vdisk'][$index]);
					write_config();
				}
			}
			break;
	}
	return $retval;
}

function is_gconcat_mounted($nameofdevice, &$arrayofmounts) {
	foreach ($arrayofmounts as $mount) {
		if (0 === strpos($mount, $nameofdevice)) {
			return true;
			break;
		}
	}
	return false;
}

$pgtitle = array(gettext('Disks'), gettext('Software RAID'), gettext('JBOD'), gettext('Management'));
?>
<?php include("fbegin.inc"); ?>
<script type="text/javascript">
<!-- Begin JavaScript
function disableactionbuttons(ab_disable) {
	var ab_element;
	ab_element = document.getElementById('delete_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
}
function togglecheckboxesbyname(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type == 'checkbox') {
			if (!a_trigger[i].disabled) {
				a_trigger[i].checked = !a_trigger[i].checked;
				if (a_trigger[i].checked) {
					ab_disable = false;
				}
			}
		}
	}
	if (ego.type == 'checkbox') { ego.checked = false; }
	disableactionbuttons(ab_disable);
}
function controlactionbuttons(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type == 'checkbox') {
			if (a_trigger[i].checked) {
				ab_disable = false;
				break;
			}
		}
	}
	disableactionbuttons(ab_disable);
}
// End JavaScript -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gettext('Reload page'); ?>"><span><?=gettext('JBOD');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gstripe.php"><span><?=gettext('RAID 0');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gmirror.php"><span><?=gettext('RAID 1');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_graid5.php"><span><?=gettext('RAID 5');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum.php"><span><?=gettext('RAID 0/1/5');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gettext('Reload page');?>"><span><?=gettext('Management');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gconcat_tools.php"><span><?=gettext('Tools');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gconcat_info.php"><span><?=gettext('Information');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="<?=$sphere_scriptname;?>" method="post">
				<?php
					if (!empty($errormsg)) { print_error_box($errormsg); }
					if (!empty($savemsg)) { print_info_box($savemsg); }
					if (updatenotify_exists($sphere_notifier)) { print_config_change_box(); }
				?>
				<div id="submit" style="margin-bottom:10px">
					<input name="delete_selected_rows" id="delete_selected_rows" type="submit" class="formbtn" value="<?=gettext('Delete Selected RAID Volumes');?>" onclick="return confirm('<?=gettext('Do you want to delete selected RAID volumes?');?>')"/>
				</div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<colgroup>
						<col style="width:1%"><!-- checkbox -->
						<col style="width:24%"><!-- Volume Name -->
						<col style="width:25%"><!-- Type -->
						<col style="width:20%"><!-- Size -->
						<col style="width:20%"><!-- Status -->
						<col style="width:10%"><!-- Icons -->
					</colgroup>
					<thead>
						<tr>
							<td class="listhdrlr"><input type="checkbox" name="togglemembers" onclick="javascript:togglecheckboxesbyname(this,'<?=$checkbox_member_name;?>[]')" title="<?php gettext('Invert Selection');?>"/></td>
							<td class="listhdrr"><?=gettext('Volume Name');?></td>
							<td class="listhdrr"><?=gettext('Type');?></td>
							<td class="listhdrr"><?=gettext('Size');?></td>
							<td class="listhdrr"><?=gettext('Status');?></td>
							<td class="list"></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td class="list" colspan="5"></td>
							<td class="list"> <a href="<?=$sphere_scriptname_child;?>"><img src="images/add.png" title="<?=$gt_record_add;?>" border="0" alt="<?=$gt_record_add;?>"/></a></td>
						</tr>
					</tfoot>
					<tbody>
						<?php $raidstatus = get_gconcat_disks_list(); ?>
						<?php foreach ($sphere_array as $sphere_record): ?>
							<?php
								$size = gettext('Unknown');
								$status = gettext('Stopped');
								if (is_array($raidstatus) && array_key_exists($sphere_record['name'], $raidstatus)) {
									$size = $raidstatus[$sphere_record['name']]['size'];
									$status = $raidstatus[$sphere_record['name']]['state'];
								}
								$notificationmode = updatenotify_get_mode($sphere_notifier, $sphere_record['uuid']);
								switch ($notificationmode) {
									case UPDATENOTIFY_MODE_NEW:
										$status = $size = gettext('Initializing');
										break;
									case UPDATENOTIFY_MODE_MODIFIED:
										$status = $size = gettext('Modifying');
										break;
									case UPDATENOTIFY_MODE_DIRTY:
									case UPDATENOTIFY_MODE_DIRTY_CONFIG:
										$status = gettext('Deleting');
										break;
								}
								$notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
								$notprotected = !isset($sphere_record['protected']);
								$notmounted = !is_gconcat_mounted($sphere_record['devicespecialfile'], $a_mount);
							?>
							<tr>
								<td class="listlr">
									<?php if ($notdirty && $notprotected && $notmounted):?>
										<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" onclick="javascript:controlactionbuttons(this,'<?=$checkbox_member_name;?>[]')"/>
									<?php else:?>
										<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" disabled="disabled"/>
									<?php endif;?>
								</td>
								<td class="listr"><?=htmlspecialchars($sphere_record['name']);?></td>
								<td class="listr"><?=htmlspecialchars($sphere_record['type']);?></td>
								<td class="listr"><?=$size;?>&nbsp;</td>
								<td class="listbg"><?=$status;?>&nbsp;</td>
								<td class="list" valign="middle" nowrap="nowrap">
									<?php if ($notdirty && $notprotected):?>
										<a href="<?=$sphere_scriptname_child;?>?uuid=<?=$sphere_record['uuid'];?>"><img src="images/edit.png" title="<?=$gt_record_mod;?>" alt="<?=$gt_record_mod;?>" /></a>
									<?php else:?>
										<?php if ($notprotected && $notmounted):?>
											<img src="images/delete.png" title="<?=gettext($gt_record_del);?>" alt="<?=gettext($gt_record_del);?>"/>
										<?php else:?>
											<img src="images/locked.png" title="<?=gettext($gt_record_loc);?>" alt="<?=gettext($gt_record_loc);?>"/>
										<?php endif;?>
									<?php endif;?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<div id="remarks">
					<?php html_remark("info", gettext('Info'), sprintf(gettext('%s is used to create %s volumes.'), 'GEOM Concat', 'JBOD'));?>
					<?php html_remark("warning", gettext('Warning'), sprintf(gettext("A mounted RAID volume cannot be deleted. Remove the <a href='%s'>mount point</a> first before proceeding."), 'disks_mount.php'));?>
				</div>
				<?php include("formend.inc"); ?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!-- Disable action buttons and give their control to checkbox array. -->
window.onload=function() {
	disableactionbuttons(true);
}
</script>
<?php include("fend.inc"); ?>
