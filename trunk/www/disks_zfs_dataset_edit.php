<?php
/*
	disks_zfs_dataset_edit.php

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

$mode_page = ($_POST) ? PAGE_MODE_POST : (($_GET) ? PAGE_MODE_EDIT : PAGE_MODE_ADD); // detect page mode

if (PAGE_MODE_POST == $mode_page) { // POST is Cancel or not Submit => cleanup
	if ((isset($_POST['Cancel']) && $_POST['Cancel']) || !(isset($_POST['Submit']) && $_POST['Submit'])) {
		header("Location: disks_zfs_dataset.php");
		exit;
	}
}

$pconfig = []; // prepare default array
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

if (!(isset($config['zfs']['datasets']['dataset']) && is_array($config['zfs']['datasets']['dataset']))) {
	$config['zfs']['datasets']['dataset'] = [];
}
array_sort_key($config['zfs']['datasets']['dataset'], "name");
$a_dataset = &$config['zfs']['datasets']['dataset'];

if (!(isset($config['zfs']['volumes']['volume']) && is_array($config['zfs']['volumes']['volume']))) {
	$config['zfs']['volumes']['volume'] = [];
}
array_sort_key($config['zfs']['volumes']['volume'], "name");
$a_volume = &$config['zfs']['volumes']['volume'];

if (!(isset($config['zfs']['pools']['pool']) && is_array($config['zfs']['pools']['pool']))) {
	$config['zfs']['pools']['pool'] = [];
}
array_sort_key($config['zfs']['pools']['pool'], "name");
$a_pool = &$config['zfs']['pools']['pool'];

if (empty($a_pool)) { // Throw error message if no pool exists
	$errormsg = sprintf(gettext("No configured pools. Please add new <a href='%s'>pools</a> first."), "disks_zfs_zpool.php");
	$prerequisites_ok = false;
}

$cnid = array_search_ex($pconfig['uuid'], $a_dataset, "uuid"); // get index from config for dataset by looking up uuid
$mode_updatenotify = updatenotify_get_mode("zfsdataset", $pconfig['uuid']); // get updatenotify mode for uuid
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
	header("Location: disks_zfs_dataset.php");
	exit;
}

if (PAGE_MODE_POST == $mode_page) { // POST Submit, already confirmed
	unset($input_errors);
	$pconfig['name'] = $_POST['name'];
	$pconfig['pool'] = $_POST['pool'];
	$pconfig['compression'] = $_POST['compression'];
	$pconfig['dedup'] = $_POST['dedup'];
	$pconfig['sync'] = $_POST['sync'];
	$pconfig['atime'] = $_POST['atime'];
	$pconfig['aclinherit'] = $_POST['aclinherit'];
	$pconfig['aclmode'] = $_POST['aclmode'];
	$pconfig['canmount'] = isset($_POST['canmount']) ? true : false;
	$pconfig['readonly'] = isset($_POST['readonly']) ? true : false;
	$pconfig['xattr'] = isset($_POST['xattr']) ? true : false;
	$pconfig['snapdir'] = isset($_POST['snapdir']) ? true : false;
	$pconfig['quota'] = $_POST['quota'];
	$pconfig['reservation'] = $_POST['reservation'];
	$pconfig['desc'] = $_POST['desc'];
	$pconfig['accessrestrictions']['owner'] = $_POST['owner'];
	$pconfig['accessrestrictions']['group'] = $_POST['group'];
	$helpinghand = 0;
	if (isset($_POST['mode_access']) && is_array($_POST['mode_access']) && count($_POST['mode_access'] < 10)) {
		foreach ($_POST['mode_access'] as $r_mode_access) {
			$helpinghand |= (257 > $r_mode_access) ? $r_mode_access : 0;
		}
	}
	$pconfig['accessrestrictions']['mode'] = sprintf( "%04o", $helpinghand);
	
	// Input validation
	$reqdfields = explode(" ", "pool name");
	$reqdfieldsn = array(gettext("Pool"), gettext("Name"));
	$reqdfieldst = explode(" ", "string string");

	do_input_validation($pconfig, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($pconfig, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if (empty($input_errors)) { // check for a valid name with format name[/name], blanks are excluded.
		$helpinghand = preg_quote('.:-_', '/');
		if (!(preg_match('/^[a-z\d][a-z\d'.$helpinghand.']*(?:\/[a-z\d][a-z\d'.$helpinghand.']*)*$/i', $pconfig['name']))) {
			$input_errors[] = sprintf(gettext("The attribute '%s' contains invalid characters."), gettext('Name'));
		}
	}
	
	// 1. RECORD_MODIFY: throw error if posted pool is different from configured pool.
	// 2. RECORD_NEW: posted pool/name must not exist in configuration or live.
	// 3. RECORD_NEW_MODIFY: if posted pool/name is different from configured pool/name: posted pool/name must not exist in configuration or live.
	// 4. RECORD_MODIFY: if posted name is different from configured name: pool/posted name must not exist in configuration or live.
	// 
	// 1.
	if (empty($input_errors)) {
		if ((RECORD_MODIFY == $mode_record) && (0 !== strcmp($a_dataset[$cnid]['pool'][0], $pconfig['pool']))) {
			$input_errors[] = 'pool cannot be changed.';
		}
	}
	// 2., 3., 4.
	if (empty($input_errors)) {
		$poolslashname = escapeshellarg($pconfig['pool']."/".$pconfig['name']); // create quoted full dataset name
		if ((RECORD_NEW == $mode_record) || ((RECORD_NEW != $mode_record) && (0 !== strcmp(escapeshellarg($a_dataset[$cnid]['pool'][0]."/".$a_dataset[$cnid]['name']), $poolslashname)))) {
			// throw error when pool/name already exists in live
			if (empty($input_errors)) {
				mwexec2(sprintf("zfs get -H -o value type %s 2>&1", $poolslashname), $retdat, $retval);
				switch ($retval) {
					case 1: // An error occured. => zfs dataset doesn't exist
						break;
					case 0: // Successful completion. => zfs dataset found
						$input_errors[] = sprintf(gettext('%s already exists as a %s.'), $poolslashname, $retdat[0]);
						break;
 					case 2: // Invalid command line options were specified.
						$input_errors[] = gettext("Failed to execute command zfs.");
						break;
				}
			}
			// throw error when pool/name exists in configuration file, zfs->volumes->volume[]
			if (empty($input_errors)) {
				foreach ($a_volume as $r_volume) {
					if (0 === strcmp(escapeshellarg($r_volume['pool'][0]."/".$r_volume['name']), $poolslashname)) {
						$input_errors[] = sprintf(gettext('%s is already configured as a volume.'), $poolslashname);
						break;
					}
				}
			}
			// throw error when  pool/name exists in configuration file, zfs->datasets->dataset[] 
			if (empty($input_errors)) {
				foreach ($a_dataset as $r_dataset) {
					if (0 === strcmp(escapeshellarg($r_dataset['pool'][0]."/".$r_dataset['name']), $poolslashname)) {
						$input_errors[] = sprintf(gettext('%s is already configured as a filesystem.'), $poolslashname);
						break;
					}
				}
			}
		}
	}

	if ($prerequisites_ok && empty($input_errors)) {
		// convert listtags to arrays
		$helpinghand = $pconfig['pool'];
		$pconfig['pool'] = [$helpinghand]; 
		$helpinghand = $pconfig['accessrestrictions']['group'];
		$pconfig['accessrestrictions']['group'] = [$helpinghand];
		if (RECORD_NEW == $mode_record) {
			$a_dataset[] = $pconfig;
			updatenotify_set("zfsdataset", UPDATENOTIFY_MODE_NEW, $pconfig['uuid']);
		} else {
			$a_dataset[$cnid] = $pconfig;
			// avoid unnecessary notifications, avoid mode modify if mode new already exists
			if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
				updatenotify_set("zfsdataset", UPDATENOTIFY_MODE_MODIFIED, $pconfig['uuid']);
			}
		}
		write_config();
		header("Location: disks_zfs_dataset.php");
		exit;		
	}
} else { // EDIT / ADD
	switch ($mode_record) {
		case RECORD_NEW:
			$pconfig['name'] = "";
			$pconfig['pool'] = "";
			$pconfig['compression'] = "off";
			$pconfig['dedup'] = "off";
			$pconfig['sync'] = "standard";
			$pconfig['atime'] = "off";
			$pconfig['aclinherit'] = "restricted";
			$pconfig['aclmode'] = "discard";
			$pconfig['canmount'] = true;
			$pconfig['readonly'] = false;
			$pconfig['xattr'] = true;
			$pconfig['snapdir'] = false;
			$pconfig['quota'] = "";
			$pconfig['reservation'] = "";
			$pconfig['desc'] = "";
			$pconfig['accessrestrictions']['owner'] = "root";
			$pconfig['accessrestrictions']['group'] = "wheel";
			$pconfig['accessrestrictions']['mode'] = "0777";
			break;
		case RECORD_NEW_MODIFY:
		case RECORD_MODIFY:
			$pconfig['name'] = $a_dataset[$cnid]['name'];
			$pconfig['pool'] = $a_dataset[$cnid]['pool'][0];
			$pconfig['compression'] = $a_dataset[$cnid]['compression'];
			$pconfig['dedup'] = $a_dataset[$cnid]['dedup'];
			$pconfig['sync'] = $a_dataset[$cnid]['sync'];
			$pconfig['atime'] = $a_dataset[$cnid]['atime'];	
			$pconfig['aclinherit'] = $a_dataset[$cnid]['aclinherit'];
			$pconfig['aclmode'] = $a_dataset[$cnid]['aclmode'];
			$pconfig['canmount'] = isset($a_dataset[$cnid]['canmount']);
			$pconfig['readonly'] = isset($a_dataset[$cnid]['readonly']);
			$pconfig['xattr'] = isset($a_dataset[$cnid]['xattr']);
			$pconfig['snapdir'] = isset($a_dataset[$cnid]['snapdir']);
			$pconfig['quota'] = $a_dataset[$cnid]['quota'];
			$pconfig['reservation'] = $a_dataset[$cnid]['reservation'];
			$pconfig['desc'] = $a_dataset[$cnid]['desc'];
			$pconfig['accessrestrictions']['owner'] = $a_dataset[$cnid]['accessrestrictions']['owner'];
			$pconfig['accessrestrictions']['group'] = $a_dataset[$cnid]['accessrestrictions']['group'][0];
			$pconfig['accessrestrictions']['mode'] = $a_dataset[$cnid]['accessrestrictions']['mode'];
			break;
	}					
}

$a_poollist = zfs_get_pool_list();
$l_poollist = [];
foreach ($a_pool as $r_pool) {
	$r_poollist = $a_poollist[$r_pool['name']];
	$helpinghand = $r_pool['name'].': '.$r_poollist['size'];
	if (!empty($r_pool['desc'])) {
		$helpinghand .= ' '.$r_pool['desc'];
	}
	$l_poollist[$r_pool['name']] = htmlspecialchars($helpinghand);
}
$l_compressionmode = ["on" => gettext("On"), "off" => gettext("Off"), "lz4" => "lz4", "lzjb" => "lzjb", "gzip" => "gzip", "zle" => "zle"];
for ($n = 1; $n <= 9; $n++) {
	$helpinghand = sprintf('gzip-%d',$n);
	$l_compressionmode[$helpinghand] = $helpinghand;
}
$l_dedup = ["on" => gettext("On"), "off" => gettext("Off"), "verify" => "verify", "sha256" => "sha256", "sha256,verify" => "sha256,verify"];		
$l_sync = ["standard" => "standard", "always" => "always", "disabled" => "disabled"];
$l_atime = ["on" => gettext("On"), "off" => gettext("Off")];
$l_aclinherit = ["discard" => "discard", "noallow" => "noallow", "restricted" => "restricted", "passthrough" => "passthrough", "passthrough-x" => "passthrough-x"];
$l_aclmode = ["discard" => "discard", "groupmask" => "groupmask", "passthrough" => "passthrough", "restricted" => "restricted"];
$l_users = [];
foreach (system_get_user_list() as $r_key => $r_value) {
	$l_users[$r_key] = htmlspecialchars($r_key);
}
$l_groups = [];
foreach (system_get_group_list() as $r_key => $r_value) {
	$l_groups[$r_key] = htmlspecialchars($r_key);
}
// Calculate value of access right checkboxes, contains a) 0 for not checked or b) the required bit mask value
$mode_access = [];
$helpinghand = octdec($pconfig['accessrestrictions']['mode']);
for ($i = 0; $i < 9; $i++) {
	$mode_access[$i] = $helpinghand & (1 << $i);
}

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Datasets"), gettext("Dataset"), (RECORD_NEW !== $mode_record) ? gettext("Edit") : gettext("Add"));
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!--
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
	document.iform.pool.disabled = !enable_change;
}
// -->
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
			<form action="disks_zfs_dataset_edit.php" method="post" name="iform" id="iform">
				<?php if (!empty($errormsg)) print_error_box($errormsg);?>
				<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<thead>
						<?php html_titleline(gettext("Settings"));?>
					</thead>
					<tbody>
						<?php html_inputbox("name", gettext("Name"), $pconfig['name'], "", true, 20);?>
						<?php html_combobox("pool", gettext("Pool"), $pconfig['pool'], $l_poollist, "", true);?>
						<?php html_combobox("compression", gettext("Compression"), $pconfig['compression'], $l_compressionmode, gettext("Controls the compression algorithm used for this dataset. The 'lzjb' compression algorithm is optimized for performance while providing decent data compression. Setting compression to 'On' uses the 'lzjb' compression algorithm. You can specify the 'gzip' level by using the value 'gzip-N', where N is an integer from 1 (fastest) to 9 (best compression ratio). Currently, 'gzip' is equivalent to 'gzip-6'."), true);?>
						<?php html_combobox("dedup", gettext("Dedup"), $pconfig['dedup'], $l_dedup, gettext("Controls the dedup method. <br><b><font color='red'>NOTE/WARNING</font>: See <a href='http://wiki.nas4free.org/doku.php?id=documentation:setup_and_user_guide:disks_zfs_datasets_dataset' target='_blank'>ZFS datasets & deduplication</a> wiki article BEFORE using this feature.</b></br>"), true);?>
						<?php html_combobox("sync", gettext("Sync"), $pconfig['sync'], $l_sync, gettext("Controls the behavior of synchronous requests."), true);?>
						<?php html_combobox("atime", gettext("Access Time (atime)"), $pconfig['atime'], $l_atime, gettext("Turn access time on or off for this dataset."), true);?>
						<?php html_combobox("aclinherit", gettext("ACL inherit"), $pconfig['aclinherit'], $l_aclinherit, "", true);?>
						<?php html_combobox("aclmode", gettext("ACL mode"), $pconfig['aclmode'], $l_aclmode, "", true);?>
						<?php html_checkbox("canmount", gettext("Canmount"), !empty($pconfig['canmount']) ? true : false, gettext("If this property is disabled, the file system cannot be mounted."), "", false);?>
						<?php html_checkbox("readonly", gettext("Readonly"), !empty($pconfig['readonly']) ? true : false, gettext("Controls whether this dataset can be modified."), "", false);?>
						<?php html_checkbox("xattr", gettext("Extended attributes"), !empty($pconfig['xattr']) ? true : false, gettext("Enable extended attributes for this file system."), "", false);?>
						<?php html_checkbox("snapdir", gettext("Snapshot Visibility"), !empty($pconfig['snapdir']) ? true : false, gettext("If this property is enabled, the snapshots are displayed into .zfs directory."), "", false);?>
						<?php html_inputbox("reservation", gettext("Reservation"), $pconfig['reservation'], gettext("The minimum amount of space guaranteed to a dataset (usually empty). To specify the size use the following human-readable suffixes (for example, 'k', 'KB', 'M', 'Gb', etc.)."), false, 10);?>
						<?php html_inputbox("quota", gettext("Quota"), $pconfig['quota'], gettext("Limits the amount of space a dataset and its descendants can consume. This property enforces a hard limit on the amount of space used. This includes all space consumed by descendants, including file systems and snapshots. To specify the size use the following human-readable suffixes (for example, 'k', 'KB', 'M', 'Gb', etc.)."), false, 10);?>
						<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
						<?php html_separator();?>
						<?php html_titleline(gettext("Access Restrictions"));?>
						<?php html_combobox("owner", gettext("Owner"), $pconfig['accessrestrictions']['owner'], $l_users, "", false);?>
						<?php html_combobox("group", gettext("Group"), $pconfig['accessrestrictions']['group'], $l_groups, "", false);?>
						<tr>
							<td style="width:22%" valign="top" class="vncell"><?=gettext("Mode");?></td>
							<td style="width:78%" class="vtable">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<colgroup>
										<col style="width:20%">
										<col style="width:20%">
										<col style="width:50%">
										<col style="width:20%">
										<col style="width:10%">
									</colgroup>
									<thead>
										<tr>
											<td class="listhdrlr">&nbsp;</td>
											<td class="listhdrc"><?=gettext("Read");?></td>
											<td class="listhdrc"><?=gettext("Write");?></td>
											<td class="listhdrc"><?=gettext("Execute");?></td>
											<td class="list"></td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="listlr"><?=gettext("Owner");?>&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="owner_r" value="256" <?php if ($mode_access[8] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="owner_w" value="128" <?php if ($mode_access[7] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="owner_x" value= "64" <?php if ($mode_access[6] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
										</tr>
										<tr>
											<td class="listlr"><?=gettext("Group");?>&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="group_r" value= "32" <?php if ($mode_access[5] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="group_w" value= "16" <?php if ($mode_access[4] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="group_x" value=  "8" <?php if ($mode_access[3] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
										</tr>
										<tr>
											<td class="listlr"><?=gettext("Others");?>&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="other_r" value=  "4" <?php if ($mode_access[2] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="other_w" value=  "2" <?php if ($mode_access[1] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
											<td class="listrc" align="center"><input type="checkbox" name="mode_access[]" id="other_x" value=  "1" <?php if ($mode_access[0] > 0) echo "checked=\"checked\"";?> />&nbsp;</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(RECORD_NEW != $mode_record) ? gettext("Save") : gettext("Add");?>" onclick="enable_change(true)" />
					<input name="Cancel" type="submit" class="formbtn" value="<?=gettext("Cancel");?>" />
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
				</div>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!--
<?php if (RECORD_MODIFY == $mode_record):?>
<!-- Disable controls that should not be modified anymore in edit mode. -->
enable_change(false);
<?php endif;?>
//-->
</script>
<?php include("fend.inc");?>
