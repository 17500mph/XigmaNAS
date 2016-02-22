<?php
/*
	disks_zfs_dataset.php

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

if (!(isset($config['zfs']['datasets']['dataset']) && is_array($config['zfs']['datasets']['dataset']))) {
	$config['zfs']['datasets']['dataset'] = [];
}
array_sort_key($config['zfs']['datasets']['dataset'], "name");
$a_dataset = &$config['zfs']['datasets']['dataset'];

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			// Process notifications
			$retval |= updatenotify_process("zfsdataset", "zfsdataset_process_updatenotification");
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("zfsdataset");
		}
		header("Location: disks_zfs_dataset.php");
		exit;
	}
	if (isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_dataset, "uuid"))) {
				$mode_updatenotify = updatenotify_get_mode("zfsdataset", $a_dataset[$index]['uuid']);
				switch ($mode_updatenotify) {
					case UPDATENOTIFY_MODE_NEW:  
						updatenotify_clear("zfsdataset", $a_dataset[$index]['uuid']);
						updatenotify_set("zfsdataset", UPDATENOTIFY_MODE_DIRTY_CONFIG, $a_dataset[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						updatenotify_clear("zfsdataset", $a_dataset[$index]['uuid']);
						updatenotify_set("zfsdataset", UPDATENOTIFY_MODE_DIRTY, $a_dataset[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_UNKNOWN:
						updatenotify_set("zfsdataset", UPDATENOTIFY_MODE_DIRTY, $a_dataset[$index]['uuid']);
						break;
				}
			}
		}
		header("Location: disks_zfs_dataset.php");
		exit;
	}	
}

function zfsdataset_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
			$retval = zfs_dataset_configure($data);
			break;

		case UPDATENOTIFY_MODE_MODIFIED:
			$retval = zfs_dataset_properties($data);
			break;

		case UPDATENOTIFY_MODE_DIRTY:
			zfs_dataset_destroy($data);
			if (false !== ($cnid = array_search_ex($data, $config['zfs']['datasets']['dataset'], "uuid"))) {
				unset($config['zfs']['datasets']['dataset'][$cnid]);
				write_config();
			}
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if (false !== ($cnid = array_search_ex($data, $config['zfs']['datasets']['dataset'], "uuid"))) {
				unset($config['zfs']['datasets']['dataset'][$cnid]);
				write_config();
			}
			break;
	}
	return $retval;
}

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Datasets"), gettext("Dataset"));
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!-- Begin JavaScript
function togglecheckboxesbyname(ego, byname) {
	var a_members = document.getElementsByName(byname);
	var numberofmembers = a_members.length;
	var i = 0;
	for (; i < numberofmembers; i++) {
		if (a_members[i].type === 'checkbox') {
			if (a_members[i].disabled == false) {
				a_members[i].checked = !a_members[i].checked;
			}
		}
	}
	if (ego.type == 'checkbox') {
		ego.checked = false;
	}
}
// End JavaScript -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Datasets");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_volume.php"><span><?=gettext("Volumes");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot.php"><span><?=gettext("Snapshots");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext("Configuration");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Dataset");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset_info.php"><span><?=gettext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_zfs_dataset.php" method="post">
				<?php
					if (!empty($savemsg)) {
						print_info_box($savemsg);
					} else {
						if (file_exists($d_sysrebootreqd_path)) {
							print_info_box(get_std_save_message(0));
						}
					}
				?>
				<?php if (updatenotify_exists("zfsdataset")) { print_config_change_box(); }?>
				<div id="submit" style="margin-bottom:10px">
					<input name="delete_selected_rows" type="submit" class="formbtn" value="<?=gettext("Delete Selected Datasets");?>" onclick="return confirm('<?=gettext("Do you want to delete selected datasets?");?>')" />
				</div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<colgroup>
						<col style="width:1%">
						<col style="width:19%">
						<col style="width:25%">
						<col style="width:8%">
						<col style="width:32%">
						<col style="width:10%">
					</colgroup>
					<thead>
						<tr>
							<td class="listhdrlr"><input type="checkbox" name="togglemembers" onclick="javascript:togglecheckboxesbyname(this,'members[]')"/></td>
							<td class="listhdrr"><?=gettext("Pool");?></td>
							<td class="listhdrr"><?=gettext("Name");?></td>
							<td class="listhdrr"><?=gettext("Compression");?></td>
							<td class="listhdrr"><?=gettext("Description");?></td>
							<td class="list"></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td class="list" colspan="5"></td>
							<td class="list"><a href="disks_zfs_dataset_edit.php"><img src="plus.gif" title="<?=gettext("Add Dataset");?>" border="0" alt="<?=gettext("Add Dataset");?>" /></a></td>
						</tr>
					</tfoot>
					<tbody>
						<?php foreach ($a_dataset as $r_dataset):?>
							<?php $notificationmode = updatenotify_get_mode("zfsdataset", $r_dataset['uuid']);?>
							<?php $notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);?>
							<tr>
								<?php if ($notdirty):?>
									<td class="listlr"><input type="checkbox" name="members[]" value="<?=$r_dataset['uuid'];?>" id="<?=$r_dataset['uuid'];?>"/></td>
								<?php else:?>
									<td class="listlr"><input type="checkbox" name="members[]" value="<?=$r_dataset['uuid'];?>" id="<?=$r_dataset['uuid'];?>" disabled="disabled"/></td>
								<?php endif;?>
								<td class="listr"><?=htmlspecialchars($r_dataset['pool'][0]);?>&nbsp;</td>
								<td class="listr"><?=htmlspecialchars($r_dataset['name']);?>&nbsp;</td>
								<td class="listr"><?=htmlspecialchars($r_dataset['compression']);?>&nbsp;</td>
								<td class="listbg"><?=htmlspecialchars($r_dataset['desc']);?>&nbsp;</td>
								<?php if ($notdirty):?>
									<td valign="middle" nowrap="nowrap" class="list"><a href="disks_zfs_dataset_edit.php?uuid=<?=$r_dataset['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit dataset");?>" border="0" alt="<?=gettext("Edit dataset");?>" /></a>&nbsp;</td>
								<?php else:?>
									<td valign="middle" nowrap="nowrap" class="list"><img src="del.gif" border="0" alt="" /></td>
								<?php endif;?>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
