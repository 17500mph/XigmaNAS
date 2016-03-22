<?php
/*
	disks_zfs_volume.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of freenas (http://www.freenas.org).
	Copyright (c) 2005-2011 by Olivier Cochard <olivier@freenas.org>.
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
require("zfs.inc");

$sphere_scriptname = basename(__FILE__);
$sphere_scriptname_child = 'disks_zfs_volume_edit.php';
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = $sphere_header;
$sphere_notifier = 'zfsvolume';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_add = gettext('Add Volume');
$gt_record_mod = gettext('Edit Volume');
$gt_record_del = gettext('Volume is marked for deletion');
$gt_record_loc = gettext('Volume is locked');
$gt_record_mup = gettext('Move up');
$gt_record_mdn = gettext('Move down');

// sunrise: verify if setting exists, otherwise run init tasks
if (!(isset($config['zfs']['volumes']['volume']) && is_array($config['zfs']['volumes']['volume']))) {
	$config['zfs']['volumes']['volume'] = [];
}
array_sort_key($config['zfs']['volumes']['volume'], 'name');
$sphere_array = &$config['zfs']['volumes']['volume'];

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			// Process notifications
			$retval |= updatenotify_process($sphere_notifier, 'zfsvolume_process_updatenotification');
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
		header($sphere_header);
		exit;
	}
}

function get_volsize($pool, $name) {
	mwexec2('zfs get -H -o value volsize '.escapeshellarg($pool.'/'.$name).' 2>&1', $rawdata);
	return $rawdata[0];
}

function get_volused($pool, $name) {
	mwexec2('zfs get -H -o value used '.escapeshellarg($pool.'/'.$name).' 2>&1', $rawdata);
	return $rawdata[0];
}

function get_volblock($pool, $name) {
	mwexec2('zfs get -H -o value volblock '.escapeshellarg($pool.'/'.$name).' 2>&1', $rawdata);
	return $rawdata[0];
}

function zfsvolume_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
			$retval = zfs_volume_configure($data);
			break;

		case UPDATENOTIFY_MODE_MODIFIED:
			$retval = zfs_volume_properties($data);
			break;

		case UPDATENOTIFY_MODE_DIRTY:
			zfs_volume_destroy($data);
			if (false !== ($index = array_search_ex($data, $config['zfs']['volumes']['volume'], 'uuid'))) {
				unset($config['zfs']['volumes']['volume'][$index]);
				write_config();
			}
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if (false !== ($index = array_search_ex($data, $config['zfs']['volumes']['volume'], 'uuid'))) {
				unset($config['zfs']['volumes']['volume'][$index]);
				write_config();
			}
			break;
	}
	return $retval;
}

$pgtitle = array(gettext('Disks'), gettext('ZFS'), gettext('Volumes'), gettext('Volume'));
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!-- Begin JavaScript
function disableactionbuttons(ab_disable) {
	var ab_element;
	ab_element = document.getElementById('toggle_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
	ab_element = document.getElementById('enable_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
	ab_element = document.getElementById('disable_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
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
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext('Pools');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext('Datasets');?></span></a></li>
				<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gettext('Reload page');?>"><span><?=gettext('Volumes');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot.php"><span><?=gettext('Snapshots');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext('Configuration');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gettext('Reload page');?>"><span><?=gettext('Volume');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_volume_info.php"><span><?=gettext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="<?=$sphere_scriptname;?>" method="post">
				<?php
					if (!empty($savemsg)) {
						print_info_box($savemsg);
					} else {
						if (file_exists($d_sysrebootreqd_path)) {
							print_info_box(get_std_save_message(0));
						}
					}
				?>
				<?php if (updatenotify_exists($sphere_notifier)) { print_config_change_box(); }?>
				<div id="submit" style="margin-bottom:10px">
					<input name="delete_selected_rows" id="delete_selected_rows" type="submit" class="formbtn" value="<?=gettext('Delete Selected Volumes');?>" onclick="return confirm('<?=gettext('Do you want to delete selected volumes?');?>')" />
				</div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<colgroup>
						<col style="width:1%">
						<col style="width:18%">
						<col style="width:18%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:20%">
						<col style="width:3%">
					</colgroup>
					<thead>
						<tr>
							<td class="listhdrlr"><input type="checkbox" name="togglemembers" onclick="javascript:togglecheckboxesbyname(this,'<?=$checkbox_member_name;?>[]')" title="<?=gettext('Invert Selection');?>"/></td>
							<td class="listhdrr"><?=gettext('Pool');?></td>
							<td class="listhdrr"><?=gettext('Name');?></td>
							<td class="listhdrr"><?=gettext('Size');?></td>
							<td class="listhdrr"><?=gettext('Compression');?></td>
							<td class="listhdrr"><?=gettext('Sparse');?></td>
							<td class="listhdrr"><?=gettext('Block Size');?></td>
							<td class="listhdrr"><?=gettext('Description');?></td>
							<td class="list"></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td class="list" colspan="8"></td>
							<td class="list"><a href="<?=$sphere_scriptname_child;?>"><img src="images/add.png" title="<?=$gt_record_add;?>" border="0" alt="<?=$gt_record_add;?>" /></a></td>
						</tr>
					</tfoot>
					<tbody>
						<?php foreach ($sphere_array as $sphere_record):?>
							<?php $notificationmode = updatenotify_get_mode($sphere_notifier, $sphere_record['uuid']);?>
							<?php $notdirty = ((UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode));?>
							<?php $notprotected = !isset($sphere_record['protected']);?>
							<tr>
								<td class="listlr">
									<?php if ($notdirty && $notprotected):?>
										<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" onclick="javascript:controlactionbuttons(this,'<?=$checkbox_member_name;?>[]')"/>
									<?php else:?>
										<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" disabled="disabled"/>
									<?php endif;?>
								</td>
								<td class="listr"><?=htmlspecialchars($sphere_record['pool'][0]);?>&nbsp;</td>
								<td class="listr"><?=htmlspecialchars($sphere_record['name']);?>&nbsp;</td>
								<?php if (UPDATENOTIFY_MODE_MODIFIED == $notificationmode || UPDATENOTIFY_MODE_NEW == $notificationmode || UPDATENOTIFY_MODE_DIRTY_CONFIG ==$notificationmode):?>
									<td class="listr"><?=htmlspecialchars($sphere_record['volsize']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($sphere_record['compression']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars((!isset($sphere_record['sparse']) ? '-' : 'on'));?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($sphere_record['volblocksize']);?>&nbsp;</td>
								<?php else:?>
									<td class="listr"><?=htmlspecialchars(get_volsize($sphere_record['pool'][0], $sphere_record['name']));?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($sphere_record['compression']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars(isset($sphere_record['sparse']) ? get_volused($sphere_record['pool'][0], $sphere_record['name']) : '-');?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars(get_volblock($sphere_record['pool'][0], $sphere_record['name']));?>&nbsp;</td>
								<?php endif;?>
								<td class="listbg"><?=htmlspecialchars($sphere_record['desc']);?>&nbsp;</td>
								<td valign="middle" nowrap="nowrap" class="list">
									<?php if ($notdirty && $notprotected):?>
										<a href="<?=$sphere_scriptname_child;?>?uuid=<?=$sphere_record['uuid'];?>"><img src="images/edit.png" title="<?=$gt_record_mod;?>" border="0" alt="<?=$gt_record_mdn;?>" /></a>
									<?php else:?>
										<?php if ($notprotected):?>
											<img src="images/delete.png" title="<?=gettext($gt_record_del);?>" border="0" alt="<?=gettext($gt_record_del);?>" />
										<?php else:?>
											<img src="images/locked.png" title="<?=gettext($gt_record_loc);?>" border="0" alt="<?=gettext($gt_record_loc);?>" />
										<?php endif;?>
									<?php endif;?>
								</td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<?php include("formend.inc");?>
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
<?php include("fend.inc");?>
