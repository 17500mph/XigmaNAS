<?php
/*
	system_sysctl_edit.php

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

$mode_page = ($_POST) ? PAGE_MODE_POST : (($_GET) ? PAGE_MODE_EDIT : PAGE_MODE_ADD); // detect page mode

if (PAGE_MODE_POST == $mode_page) { // POST is Cancel or not Submit => cleanup
	if ((isset($_POST['Cancel']) && $_POST['Cancel']) || !(isset($_POST['Submit']) && $_POST['Submit'])) {
		header("Location: system_sysctl.php");
		exit;
	}
}

$pconfig = [];
$prerequisites_ok = true;

if ((PAGE_MODE_POST == $mode_page) && isset($_POST['uuid']) && is_uuid_v4($_POST['uuid'])) {
	$pconfig['uuid'] = $_POST['uuid'];
} else {
	if ((PAGE_MODE_EDIT == $mode_page) && isset($_GET['uuid']) && is_uuid_v4($_GET['uuid'])) {
		$pconfig['uuid'] = $_GET['uuid'];
	} else {
		$mode_page = PAGE_MODE_ADD; // Force ADD
		$pconfig['uuid'] = uuid();
	}
}

if (!(isset($config['system']['sysctl']['param']) && is_array($config['system']['sysctl']['param']))) {
	$config['system']['sysctl']['param'] = [];
}
array_sort_key($config['system']['sysctl']['param'], "name");
$a_sysctl = &$config['system']['sysctl']['param'];

$cnid = array_search_ex($pconfig['uuid'], $a_sysctl, "uuid");
$mode_updatenotify = updatenotify_get_mode("sysctl", $pconfig['uuid']); // get updatenotify mode for uuid
$mode_record = RECORD_ERROR;
if (false !== $cnid) { // uuid found
	if ((PAGE_MODE_POST == $mode_page || (PAGE_MODE_EDIT == $mode_page))) { // POST or EDIT
		switch ($mode_updatenotify) {
			case UPDATENOTIFY_MODE_NEW:
				$mode_record = RECORD_NEW_MODIFY;
				break;
			case UPDATENOTIFY_MODE_MODIFIED:
			case UPDATENOTIFY_MODE_UNKNOWN:
				$mode_record = RECORD_MODIFY;
				break;
		}
	}
} else { // uuid not found
	if ((PAGE_MODE_POST == $mode_page) || (PAGE_MODE_ADD == $mode_page)) { // POST or ADD
		switch ($mode_updatenotify) {
			case UPDATENOTIFY_MODE_UNKNOWN:
				$mode_record = RECORD_NEW;
				break;
		}
	}
}
if (RECORD_ERROR == $mode_record) { // oops, someone tries to cheat, over and out
	header("Location: system_sysctl.php");
	exit;
}

if (PAGE_MODE_POST == $mode_page) { // POST Submit, already confirmed
	unset($input_errors);
	$pconfig['enable'] = isset($_POST['enable']) ? true : false;
	$pconfig['name'] = trim($_POST['name']);
	$pconfig['value'] = $_POST['value'];
	$pconfig['comment'] = $_POST['comment'];
				
	// Input validation.
	$reqdfields = explode(" ", "name value");
	$reqdfieldsn = array(gettext("Name"), gettext("Value"));
	$reqdfieldst = explode(" ", "string string");

	do_input_validation($pconfig, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($pconfig, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	// Check if MIB name exists.
	exec("/sbin/sysctl -NA", $helper);
	if (!in_array($pconfig['name'], $helper)) {
		$input_errors[] = sprintf(gettext("The MIB '%s' doesn't exist in sysctl."), $pconfig['name']);
	}

	// Check if MIB is already configured.
	if ((RECORD_NEW == $mode_record) || (RECORD_NEW_MODIFY == $mode_record)) {
		if (false !== array_search_ex($pconfig['name'], $a_sysctl, "name")) {
			$input_errors[] = sprintf(gettext("The MIB '%s' already exist."), $pconfig['name']);
		}
	}

	if ($prerequisites_ok && empty($input_errors)) {
		if (RECORD_NEW == $mode_record) {
			$a_sysctl[] = $pconfig;
			updatenotify_set("sysctl", UPDATENOTIFY_MODE_NEW, $pconfig['uuid']);
		} else {
			$a_sysctl[$cnid] = $pconfig;
			if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
				updatenotify_set("sysctl", UPDATENOTIFY_MODE_MODIFIED, $pconfig['uuid']);
			}
		}
		write_config();
		header("Location: system_sysctl.php");
		exit;
	}
} else { // EDIT / ADD
	switch ($mode_record) {
		case RECORD_NEW:
			$pconfig['enable'] = true;
			$pconfig['name'] = "";
			$pconfig['value'] = "";
			$pconfig['comment'] = "";
			break;
		case RECORD_NEW_MODIFY:
		case RECORD_MODIFY:
			$pconfig['enable'] = isset($a_sysctl[$cnid]['enable']);
			$pconfig['name'] = trim($a_sysctl[$cnid]['name']);
			$pconfig['value'] = $a_sysctl[$cnid]['value'];
			$pconfig['comment'] = $a_sysctl[$cnid]['comment'];
			break;
	}
}
$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("sysctl.conf"), (RECORD_NEW !== $mode_record) ? gettext("Edit") : gettext("Add"));
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="system_advanced.php"><span><?=gettext("Advanced");?></span></a></li>
				<li class="tabinact"><a href="system_email.php"><span><?=gettext("Email");?></span></a></li>
				<li class="tabinact"><a href="system_proxy.php"><span><?=gettext("Proxy");?></span></a></li>
				<li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
				<li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command Scripts");?></span></a></li>
				<li class="tabinact"><a href="system_cron.php"><span><?=gettext("Cron");?></span></a></li>
				<li class="tabinact"><a href="system_loaderconf.php"><span><?=gettext("loader.conf");?></span></a></li>
				<li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
				<li class="tabact"><a href="system_sysctl.php" title="<?=gettext("Reload page");?>"><span><?=gettext("sysctl.conf");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="system_sysctl_edit.php" method="post" name="iform" id="iform">
				<?php if (!empty($errormsg)) print_error_box($errormsg);?>
				<?php if (!empty($input_errors)) print_input_errors($input_errors); ?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", "", $pconfig['enable'] ? true : false, gettext("Enable"));?>
					<?php html_inputbox("name", gettext("Name"), $pconfig['name'], gettext("Enter a valid sysctl MIB name."), true, 40);?>
					<?php html_inputbox("value", gettext("Value"), $pconfig['value'], gettext("A valid systctl MIB value."), true);?>
					<?php html_inputbox("comment", gettext("Comment"), $pconfig['comment'], gettext("You may enter a description here for your reference."), false, 40);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(RECORD_NEW != $mode_record) ? gettext("Save") : gettext("Add")?>" />
					<input name="Cancel" type="submit" class="formbtn" value="<?=gettext("Cancel");?>" />
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
				</div>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
