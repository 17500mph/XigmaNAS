#!/usr/local/bin/php
<?php
/*
	disks_raid_graid5_edit.php
	
	Part of NAS4Free (http://www.nas4free.org).
	Copyright (C) 2012 by NAS4Free Team <info@nas4free.org>.
	All rights reserved.
	
	Modified for XHTML by Daisuke Aoyama <aoyama@peach.ne.jp>
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.	
	All rights reserved.

	Portions of freenas (http://www.freenas.org).
	Copyright (C) 2005-2011 by Olivier Cochard <olivier@freenas.org>.
	All rights reserved.
	
	Portions of m0n0wall (http://m0n0.ch/wall).
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Disks"), gettext("Software RAID"), gettext("RAID5"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['graid5']['vdisk']))
	$config['graid5']['vdisk'] = array();

array_sort_key($config['graid5']['vdisk'], "name");

$a_raid = &$config['graid5']['vdisk'];
$all_raid = get_conf_sraid_disks_list();
$a_disk = get_conf_disks_filtered_ex("fstype", "softraid");

if (!sizeof($a_disk)) {
	$nodisk_errors[] = gettext("You must add disks first.");
}

if (isset($id) && $a_raid[$id]) {
	$pconfig['uuid'] = $a_raid[$id]['uuid'];
	$pconfig['name'] = $a_raid[$id]['name'];
	$pconfig['devicespecialfile'] = $a_raid[$id]['devicespecialfile'];
	$pconfig['type'] = $a_raid[$id]['type'];
	$pconfig['device'] = $a_raid[$id]['device'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if ($_POST['Cancel']) {
		header("Location: disks_raid_graid5.php");
		exit;
	}

	// Input validation
	$reqdfields = explode(" ", "name");
	$reqdfieldsn = array(gettext("Raid name"));

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);

	if (($_POST['name'] && !is_validaliasname($_POST['name']))) {
		$input_errors[] = gettext("The device name may only consist of the characters a-z, A-Z, 0-9.");
	}

	// Check for duplicate name.
	foreach ($all_raid as $raid) {
		if ($raid['name'] === $_POST['name']) {
			$input_errors[] = gettext("This device already exists in the raid volume list.");
			break;
		}
	}

	/* check the number of RAID disk for volume */
	if (count($_POST['device']) < 2)
		$input_errors[] = gettext("There must be a minimum of 2 disks in a RAID 5 volume.");

	if (!$input_errors) {
		$raid = array();
		$raid['uuid'] = uuid();
		$raid['name'] = substr($_POST['name'], 0, 15); // Make sure name is only 15 chars long (GEOM limitation).
		$raid['type'] = 5;
		$raid['device'] = $_POST['device'];
		$raid['desc'] = "Software graid5 RAID 5";
		$raid['devicespecialfile'] = "/dev/raid5/{$raid['name']}";

		if (isset($id) && $a_raid[$id]) {
			$a_raid[$id] = $raid;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_raid[] = $raid;
			if ($_POST['init'])
				$mode = UPDATENOTIFY_MODE_NEW;
			else
				$mode = UPDATENOTIFY_MODE_MODIFIED;
		}

		updatenotify_set("raid_graid5", $mode, $raid['uuid']);
		write_config();

		header("Location: disks_raid_graid5.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
		  <ul id="tabnav">
				<li class="tabinact"><a href="disks_raid_gconcat.php"><span><?=gettext("JBOD"); ?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gstripe.php"><span><?=gettext("RAID 0"); ?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gmirror.php"><span><?=gettext("RAID 1"); ?></span></a></li>
				<li class="tabact"><a href="disks_raid_graid5.php" title="<?=gettext("Reload page");?>" ><span><?=gettext("RAID 5");?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum.php"><span><?=gettext("RAID 0/1/5");?></span></a></li>
		  </ul>
	  </td>
	</tr>
  <tr>
		<td class="tabnavtbl">
		  <ul id="tabnav2">
				<li class="tabact"><a href="disks_raid_graid5.php" title="<?=gettext("Reload page");?>" ><span><?=gettext("Management");?></span></a></li>
				<li class="tabinact"><a href="disks_raid_graid5_tools.php"><span><?=gettext("Tools"); ?></span></a></li>
				<li class="tabinact"><a href="disks_raid_graid5_info.php"><span><?=gettext("Information"); ?></span></a></li>
		  </ul>
	  </td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_raid_graid5_edit.php" method="post" name="iform" id="iform">
				<?php if ($nodisk_errors) print_input_errors($nodisk_errors); ?>
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td valign="top" class="vncellreq"><?=gettext("Raid name");?></td>
			      <td width="78%" class="vtable">
			        <input name="name" type="text" class="formfld" id="name" size="15" value="<?=htmlspecialchars($pconfig['name']);?>" <?php if (isset($id)) echo "readonly=\"readonly\"";?> />
			      </td>
			    </tr>
			    <tr>
			      <td valign="top" class="vncellreq"><?=gettext("Type"); ?></td>
			      <td width="78%" class="vtable">
			      RAID 5 (<?=gettext("rotated block-interleaved parity"); ?>)
			      </td>
			    </tr>
			    <?php $a_provider = array(); foreach ($a_disk as $diskv) { if (isset($id) && !(is_array($pconfig['device']) && in_array($diskv['devicespecialfile'], $pconfig['device']))) { continue; } if (!isset($id) && false !== array_search_ex($diskv['devicespecialfile'], $all_raid, "device")) { continue; } $a_provider[$diskv[devicespecialfile]] = htmlspecialchars("$diskv[name] ($diskv[size], $diskv[desc])"); }?>
			    <?php html_listbox("device", gettext("Provider"), $pconfig['device'], $a_provider, gettext("Note: Ctrl-click (or command-click on the Mac) to select multiple entries."), true, isset($id));?>
			    <?php if (!isset($id)):?>
			    <tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Initialize");?></td>
			      <td width="78%" class="vtable">
							<input name="init" type="checkbox" id="init" value="yes" <?php if (true === $pconfig['init']) echo "checked=\"checked\""; ?> />
							<?=gettext("Create and initialize RAID.");?><br />
							<span class="vexpl"><?=gettext("This will erase ALL data on the selected disks! Do not use this option if you want to add an already existing RAID again.");?></span>
			      </td>
			    </tr>
					<?php endif;?>
			  </table>
			  <?php if (!isset($id)):?>
			  <div id="submit">
			  	<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Add");?>" />
			  	<input name="Cancel" type="submit" class="formbtn" value="<?=gettext("Cancel");?>" />
				</div>
			  <?php endif;?>
			  <?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
