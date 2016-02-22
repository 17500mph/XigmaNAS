<?php
/*
	system_sysctl.php

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

if (!(isset($config['system']['sysctl']['param']) && is_array($config['system']['sysctl']['param']))) {
	$config['system']['sysctl']['param'] = [];
}
$a_sysctl = &$config['system']['sysctl']['param'];
if (!empty($a_sysctl)) {
	$key1 = array_column($a_sysctl, "name");
	$key2 = array_column($a_sysctl, "uuid");
	array_multisort($key1, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $key2, SORT_ASC, SORT_STRING | SORT_FLAG_CASE, $a_sysctl);
}

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("sysctl", "sysctl_process_updatenotification");
			config_lock();
			$retval |= rc_update_service("sysctl");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("sysctl");
		}
		header("Location: system_sysctl.php");
		exit;
	}
	if (isset($_POST['enable_selected_rows']) && $_POST['enable_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : [];
		$updateconfig = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_sysctl, "uuid"))) {
				if (!(isset($a_sysctl[$index]['enable']))) {
					$a_sysctl[$index]['enable'] = true;
					$updateconfig = true;
					$mode_updatenotify = updatenotify_get_mode("sysctl", $a_sysctl[$index]['uuid']);
					if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
						updatenotify_set("sysctl", UPDATENOTIFY_MODE_MODIFIED, $a_sysctl[$index]['uuid']);
					}
				}
			}
		}
		if ($updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header("Location: system_sysctl.php");
		exit;
	}
	if (isset($_POST['disable_selected_rows']) && $_POST['disable_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : [];
		$updateconfig = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_sysctl, "uuid"))) {
				if (isset($a_sysctl[$index]['enable'])) {
					unset($a_sysctl[$index]['enable']);
					$updateconfig = true;
					$mode_updatenotify = updatenotify_get_mode("sysctl", $a_sysctl[$index]['uuid']);
					if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
						updatenotify_set("sysctl", UPDATENOTIFY_MODE_MODIFIED, $a_sysctl[$index]['uuid']);
					}
				}	
			}	
		}
		if ($updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header("Location: system_sysctl.php");
		exit;
	}
	if (isset($_POST['toggle_selected_rows']) && $_POST['toggle_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : [];
		$updateconfig = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_sysctl, "uuid"))) {
				if (isset($a_sysctl[$index]['enable'])) {
					unset($a_sysctl[$index]['enable']);
				} else {
					$a_sysctl[$index]['enable'] = true;					
				}
				$updateconfig = true;
				$mode_updatenotify = updatenotify_get_mode("sysctl", $a_sysctl[$index]['uuid']);
				if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
					updatenotify_set("sysctl", UPDATENOTIFY_MODE_MODIFIED, $a_sysctl[$index]['uuid']);
				}
			}	
		}
		if ($updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header("Location: system_sysctl.php");
		exit;
	}
	if (isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : [];
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_sysctl, "uuid"))) {
				$mode_updatenotify = updatenotify_get_mode("sysctl", $a_sysctl[$index]['uuid']);
				switch ($mode_updatenotify) {
					case UPDATENOTIFY_MODE_NEW:  
						updatenotify_clear("sysctl", $a_sysctl[$index]['uuid']);
						updatenotify_set("sysctl", UPDATENOTIFY_MODE_DIRTY_CONFIG, $a_sysctl[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						updatenotify_clear("sysctl", $a_sysctl[$index]['uuid']);
						updatenotify_set("sysctl", UPDATENOTIFY_MODE_DIRTY, $a_sysctl[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_UNKNOWN:
						updatenotify_set("sysctl", UPDATENOTIFY_MODE_DIRTY, $a_sysctl[$index]['uuid']);
						break;
				}
			}
		}
		header("Location: system_sysctl.php");
		exit;
	}
}

function sysctl_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if (is_array($config['system']['sysctl']['param'])) {
				$index = array_search_ex($data, $config['system']['sysctl']['param'], "uuid");
				if (false !== $index) {
					unset($config['system']['sysctl']['param'][$index]);
					write_config();
				}
			}
			break;
	}
	return $retval;
}

$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("sysctl.conf"));
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
				<li class="tabinact"><a href="system_advanced.php"><span><?=gettext("Advanced");?></span></a></li>
				<li class="tabinact"><a href="system_email.php"><span><?=gettext("Email");?></span></a></li>
				<li class="tabinact"><a href="system_proxy.php"><span><?=gettext("Proxy");?></span></a></li>
				<li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
				<li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command scripts");?></span></a></li>
				<li class="tabinact"><a href="system_cron.php"><span><?=gettext("Cron");?></span></a></li>
				<li class="tabinact"><a href="system_loaderconf.php"><span><?=gettext("loader.conf");?></span></a></li>
				<li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
				<li class="tabact"><a href="system_sysctl.php" title="<?=gettext("Reload page");?>"><span><?=gettext("sysctl.conf");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="system_sysctl.php" method="post">
				<?php
					if (!empty($savemsg)) {
						print_info_box($savemsg);
					} else {
						if (file_exists($d_sysrebootreqd_path)) {
							print_info_box(get_std_save_message(0));
						}
					}
				?>
				<?php if (updatenotify_exists("sysctl")) print_config_change_box();?>
				<div id="submit" style="margin-bottom:10px">
					<input name="enable_selected_rows" type="submit" class="formbtn" value="<?=gettext("Enable Selected Options");?>" onclick="return confirm('<?=gettext("Do you want to enable selected options?");?>')" />
					<input name="disable_selected_rows" type="submit" class="formbtn" value="<?=gettext("Disable Selected Options");?>" onclick="return confirm('<?=gettext("Do you want to disable selected options?");?>')" />
					<input name="toggle_selected_rows" type="submit" class="formbtn" value="<?=gettext("Toggle Selected Options");?>" onclick="return confirm('<?=gettext("Do you want to toggle selected options?");?>')" />
					<input name="delete_selected_rows" type="submit" class="formbtn" value="<?=gettext("Delete Selected Options");?>" onclick="return confirm('<?=gettext("Do you want to delete selected options?");?>')" />
				</div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<colgroup>
						<col style="width:1%">
						<col style="width:34%">
						<col style="width:20%">
						<col style="width:5%">
						<col style="width:30%">
						<col style="width:10%">
					</colgroup>
					<thead>
						<tr>
							<td class="listhdrlr"><input type="checkbox" name="togglemembers" onclick="javascript:togglecheckboxesbyname(this,'members[]')"/></td>
							<td class="listhdrr"><?=gettext("MIB");?></td>
							<td class="listhdrr"><?=gettext("Value");?></td>
							<td class="listhdrr"><?=gettext("Status");?></td>
							<td class="listhdrr"><?=gettext("Comment");?></td>
							<td class="list"></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td class="list" colspan="5"></td>
							<td class="list"><a href="system_sysctl_edit.php"><img src="plus.gif" title="<?=gettext("Add MIB");?>" border="0" alt="<?=gettext("Add MIB");?>" /></a></td>
						</tr>
					</tfoot>
					<tbody>
						<?php foreach($a_sysctl as $r_sysctl):?>
							<tr>
								<?php $notificationmode = updatenotify_get_mode("sysctl", $r_sysctl['uuid']);?>
								<?php $notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);?>
								<?php $enable = isset($r_sysctl['enable']);?>
								<?php if ($notdirty):?>
									<td class="<?=$enable?"listlr":"listlrd";?>"><input type="checkbox" name="members[]" value="<?=$r_sysctl['uuid'];?>" id="<?=$r_sysctl['uuid'];?>"/></td>
								<?php else:?>
									<td class="<?=$enable?"listlr":"listlrd";?>"><input type="checkbox" name="members[]" value="<?=$r_sysctl['uuid'];?>" id="<?=$r_sysctl['uuid'];?>" disabled="disabled"/></td>
								<?php endif;?>
								<td class="<?=$enable?"listr":"listrd";?>"><?=htmlspecialchars($r_sysctl['name']);?>&nbsp;</td>
								<td class="<?=$enable?"listr":"listrd";?>"><?=htmlspecialchars($r_sysctl['value']);?>&nbsp;</td>
								<td class="<?=$enable?"listr":"listrd";?>">
									<?php if ($enable):?>
										<a title="<?=gettext("Enabled");?>"><img src="status_enabled.png" border="0" alt=""/></a>
									<?php else:?>
										<a title="<?=gettext("Disabled");?>"><img src="status_disabled.png" border="0" alt=""/></a>
									<?php endif;?>
								</td>
								<td class="listbg"><?=htmlspecialchars($r_sysctl['comment']);?>&nbsp;</td>
								<?php if ($notdirty):?>
									<td valign="middle" nowrap="nowrap" class="list"><a href="system_sysctl_edit.php?uuid=<?=$r_sysctl['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit MIB");?>" border="0" alt="<?=gettext("Edit MIB");?>" /></a></td>
								<?php else:?>
									<td valign="middle" nowrap="nowrap" class="list"><img src="del.gif" border="0" alt=""/></td>
								<?php endif;?>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("These MIBs will be added to /etc/sysctl.conf. This allow you to make changes to a running system."));?>
				</div>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
