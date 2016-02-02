<?php
/*
	system_cron.php

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

$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("Cron"));

if (false === (isset($config['cron']['job']) && is_array($config['cron']['job']))) {
	$config['cron']['job'] = array();
}
$a_cronjob = &$config['cron']['job'];

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("cronjob", "cronjob_process_updatenotification");
			config_lock();
			$retval |= rc_update_service("cron");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("cronjob");
		}
		header("Location: system_cron.php");
		exit;
	}
	if (isset($_POST['enable_selected_rows']) && $_POST['enable_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		$updateconfigfile = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_cronjob, "uuid"))) {
				if (false === isset($a_cronjob[$index]['enable'])) {
					$a_cronjob[$index]['enable'] = true;
					$updateconfigfile = true;
					$updatenotifymode = updatenotify_get_mode("cronjob", $a_cronjob[$index]['uuid']);
					switch ($updatenotifymode) {
						case UPDATENOTIFY_MODE_NEW:
							break;
						case UPDATENOTIFY_MODE_MODIFIED:
							break;
						case UPDATENOTIFY_MODE_DIRTY:
							break;
						default:
							updatenotify_set("cronjob", UPDATENOTIFY_MODE_MODIFIED, $a_cronjob[$index]['uuid']);
							break;
					}
				}
			}
		}
		if (true === $updateconfigfile) {
			write_config();
			$updateconfigfile = false;
		}
		header("Location: system_cron.php");
		exit;
	}
	if (isset($_POST['disable_selected_rows']) && $_POST['disable_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		$updateconfigfile = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_cronjob, "uuid"))) {
				if (true === isset($a_cronjob[$index]['enable'])) {
					unset($a_cronjob[$index]['enable']);
					$updateconfigfile = true;
					$updatenotifymode = updatenotify_get_mode("cronjob", $a_cronjob[$index]['uuid']);
					switch ($updatenotifymode) {
						case UPDATENOTIFY_MODE_NEW:
							break;
						case UPDATENOTIFY_MODE_MODIFIED:
							break;
						case UPDATENOTIFY_MODE_DIRTY:
							break;
						default:
							updatenotify_set("cronjob", UPDATENOTIFY_MODE_MODIFIED, $a_cronjob[$index]['uuid']);
							break;
					}
				}
			}
		}
		if (true === $updateconfigfile) {
			write_config();
			$updateconfigfile = false;
		}
		header("Location: system_cron.php");
		exit;
	}
	if (isset($_POST['toggle_selected_rows']) && $_POST['toggle_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		$updateconfigfile = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_cronjob, "uuid"))) {
				if (true === isset($a_cronjob[$index]['enable'])) {
					unset($a_cronjob[$index]['enable']);
				} else {
					$a_cronjob[$index]['enable'] = true;
				}
				$updateconfigfile = true;
				$updatenotifymode = updatenotify_get_mode("cronjob", $a_cronjob[$index]['uuid']);
				switch ($updatenotifymode) {
					case UPDATENOTIFY_MODE_NEW:
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						break;
					case UPDATENOTIFY_MODE_DIRTY:
						break;
					default:
						updatenotify_set("cronjob", UPDATENOTIFY_MODE_MODIFIED, $a_cronjob[$index]['uuid']);
						break;
				}
			}
		}
		if (true === $updateconfigfile) {
			write_config();
			$updateconfigfile = false;
		}
		header("Location: system_cron.php");
		exit;
	}
	if (isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_cronjob, "uuid"))) {
				$updatenotifymode = updatenotify_get_mode("cronjob", $a_cronjob[$index]['uuid']);
				switch ($updatenotifymode) {
					case UPDATENOTIFY_MODE_NEW:
						updatenotify_clear("cronjob", $a_cronjob[$index]['uuid']);
						updatenotify_set("cronjob", UPDATENOTIFY_MODE_DIRTY, $a_cronjob[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						updatenotify_clear("cronjob", $a_cronjob[$index]['uuid']);
						updatenotify_set("cronjob", UPDATENOTIFY_MODE_DIRTY, $a_cronjob[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_DIRTY:
						break;
					default:
						updatenotify_set("cronjob", UPDATENOTIFY_MODE_DIRTY, $a_cronjob[$index]['uuid']);
						break;
				}
			}
		}
		header("Location: system_cron.php");
		exit;
	}
}

function cronjob_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['cron']['job'])) {
				$index = array_search_ex($data, $config['cron']['job'], "uuid");
				if (false !== $index) {
					unset($config['cron']['job'][$index]);
					write_config();
				}
			}
			break;
	}
	return $retval;
}
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!-- Begin JavaScript
function togglecheckboxesbyname(ego, byname) {
	var a_members = document.getElementsByName(byname);
	var i;
	for (i = 0; i < a_members.length; i++) {
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
				<li class="tabact"><a href="system_cron.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Cron");?></span></a></li>
				<li class="tabinact"><a href="system_loaderconf.php"><span><?=gettext("loader.conf");?></span></a></li>
				<li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
				<li class="tabinact"><a href="system_sysctl.php"><span><?=gettext("sysctl.conf");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="system_cron.php" method="post">
				<?php
					if (!empty($savemsg)) {
						print_info_box($savemsg);
					} else {
						if (file_exists($d_sysrebootreqd_path)) {
							print_info_box(get_std_save_message(0));
						}
					}
				?>
				<?php if (updatenotify_exists("cronjob")) { print_config_change_box(); } ?>
				<div id="submit">
					<input name="enable_selected_rows" type="submit" class="formbtn" value="<?=gettext("Enable Selected Jobs");?>" onclick="return confirm('<?=gettext("Do you want to enable selected jobs?");?>')" />
					<input name="disable_selected_rows" type="submit" class="formbtn" value="<?=gettext("Disable Selected Jobs");?>" onclick="return confirm('<?=gettext("Do you want to disable selected jobs?");?>')" />
					<input name="toggle_selected_rows" type="submit" class="formbtn" value="<?=gettext("Toggle Selected Jobs");?>" onclick="return confirm('<?=gettext("Do you really to toggle selected jobs?");?>')" />
					<input name="delete_selected_rows" type="submit" class="formbtn" value="<?=gettext("Delete Selected Jobs");?>" onclick="return confirm('<?=gettext("Do you want to delete selected jobs?");?>')" />
				</div>
				<br />
				<br />
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="1%" class="listhdrlr"><input type="checkbox" name="togglemembers" onclick="javascript:togglecheckboxesbyname(this,'members[]')"/></td>
						<td width="39%" class="listhdrlr"><?=gettext("Command");?></td>
						<td width="10%" class="listhdrr"><?=gettext("Who");?></td>
						<td width="5%" class="listhdrr"><?=gettext("Status");?></td>
						<td width="35%" class="listhdrr"><?=gettext("Description");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php foreach($a_cronjob as $r_cronjob):?>
						<?php $notificationmode = updatenotify_get_mode("cronjob", $r_cronjob['uuid']);?>
						<tr>
							<?php $notificationmode = updatenotify_get_mode("cronjob", $r_cronjob['uuid']);?>
							<?php $enable = isset($r_cronjob['enable']);?>
							<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
								<td class="<?=$enable ? "listlr" : "listlrd";?>"><input type="checkbox" name="members[]" value="<?=$r_cronjob['uuid'];?>" id="<?=$r_cronjob['uuid'];?>"/></td>
							<?php else:?>
								<td class="<?=$enable ? "listlr" : "listlrd";?>"><input type="checkbox" name="members[]" value="<?=$r_cronjob['uuid'];?>" id="<?=$r_cronjob['uuid'];?>" disabled="disabled"/></td>
							<?php endif;?>
							<td class="<?=$enable?"listlr":"listlrd";?>"><?=htmlspecialchars($r_cronjob['command']);?>&nbsp;</td>
							<td class="<?=$enable?"listr":"listrd";?>"><?=htmlspecialchars($r_cronjob['who']);?>&nbsp;</td>
							<td class="<?=$enable ? "listr" : "listrd";?>">
								<?php if ($enable):?>
									<a title="<?=gettext("Enabled");?>"><img src="status_enabled.png" border="0" alt="" /></a>
								<?php else:?>
									<a title="<?=gettext("Disabled");?>"><img src="status_disabled.png" border="0" alt="" /></a>
								<?php endif;?>
							</td>
							<td class="listbg"><?=htmlspecialchars($r_cronjob['desc']);?>&nbsp;</td>
							<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
								<td valign="middle" nowrap="nowrap" class="list">
									<a href="system_cron_edit.php?uuid=<?=$r_cronjob['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit job");?>" border="0" alt="<?=gettext("Edit job");?>" /></a>
								</td>
							<?php else:?>
								<td valign="middle" nowrap="nowrap" class="list">
									<img src="del.gif" border="0" alt="" />
								</td>
							<?php endif;?>
						</tr>
					<?php endforeach;?>
					<tr>
						<td class="list" colspan="5"></td>
						<td class="list">
							<a href="system_cron_edit.php"><img src="plus.gif" title="<?=gettext("Add job");?>" border="0" alt="<?=gettext("Add job");?>" /></a>
						</td>
					</tr>
				</table>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
