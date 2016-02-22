<?php
/*
	disks_zfs_volume_edit.php

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
		header("Location: disks_zfs_volume.php");
		exit;
	}
}

function get_volblocksize($pool, $name) {
	mwexec2('zfs get -H -o value volblocksize '.escapeshellarg($pool.'/'.$name).' 2>&1', $rawdata);
	return $rawdata[0];
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

$cnid = array_search_ex($pconfig['uuid'], $a_volume, "uuid"); // get index from config for volume by looking up uuid
$mode_updatenotify = updatenotify_get_mode("zfsvolume", $pconfig['uuid']); // get updatenotify mode for uuid
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
	header("Location: disks_zfs_volume.php");
	exit;
}

if (PAGE_MODE_POST == $mode_page) { // POST Submit, already confirmed
	unset($input_errors);
	$pconfig['name'] = $_POST['name'];
	$pconfig['pool'] = $_POST['pool'];
	$pconfig['volsize'] = $_POST['volsize'];
	$pconfig['volmode'] = $_POST['volmode'];
	$pconfig['volblocksize'] = $_POST['volblocksize'];
	$pconfig['compression'] = $_POST['compression'];
	$pconfig['dedup'] = $_POST['dedup'];
	$pconfig['sync'] = $_POST['sync'];
	$pconfig['sparse'] = isset($_POST['sparse']) ? true : false;
	$pconfig['desc'] = $_POST['desc'];

	// Input validation
	$reqdfields = explode(" ", "pool name volsize");
	$reqdfieldsn = array(gettext("Pool"), gettext("Name"), gettext("Size"));
	$reqdfieldst = explode(" ", "string string string");

	do_input_validation($pconfig, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($pconfig, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if (empty($input_errors)) {
		// check for a valid name with the format name[/name], blanks are not supported.
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
		if ((RECORD_MODIFY == $mode_record) && (0 !== strcmp($a_volume[$cnid]['pool'][0], $pconfig['pool']))) {
			$input_errors[] = 'pool cannot be changed.';
		}
	}
	// 2., 3., 4.
	if (empty($input_errors)) {
		$poolslashname = escapeshellarg($pconfig['pool']."/".$pconfig['name']); // create quoted full dataset name
		if ((RECORD_NEW == $mode_record) || ((RECORD_NEW != $mode_record) && (0 !== strcmp(escapeshellarg($a_volume[$cnid]['pool'][0]."/".$a_volume[$cnid]['name']), $poolslashname)))) {
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
			// throw error when pool/name exists in configuration file, zfs->datasets->dataset[] 
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
		if (RECORD_NEW == $mode_record) {
			$a_volume[] = $pconfig;
			updatenotify_set("zfsvolume", UPDATENOTIFY_MODE_NEW, $pconfig['uuid']);
		} else {
			$a_volume[$cnid] = $pconfig;
			if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
				updatenotify_set("zfsvolume", UPDATENOTIFY_MODE_MODIFIED, $pconfig['uuid']);
			}
		}
		write_config();
		header("Location: disks_zfs_volume.php"); // cleanup
		exit;
	}
} else {
	switch ($mode_record) {
		case RECORD_NEW:
			$pconfig['name'] = "";
			$pconfig['pool'] = "";
			$pconfig['volsize'] = "";
			$pconfig['volmode'] = "default";
			$pconfig['volblocksize'] = "";
			$pconfig['compression'] = "off";
			$pconfig['dedup'] = "off";
			$pconfig['sync'] = "standard";
			$pconfig['sparse'] = false;
			$pconfig['desc'] = "";
			break;
		case RECORD_NEW_MODIFY: // get from config only
			$pconfig['name'] = $a_volume[$cnid]['name'];
			$pconfig['pool'] = $a_volume[$cnid]['pool'][0];
			$pconfig['volsize'] = $a_volume[$cnid]['volsize'];
			$pconfig['volmode'] = $a_volume[$cnid]['volmode'];
			$pconfig['volblocksize'] = $a_volume[$cnid]['volblocksize'];
			$pconfig['compression'] = $a_volume[$cnid]['compression'];
			$pconfig['dedup'] = $a_volume[$cnid]['dedup'];
			$pconfig['sync'] = $a_volume[$cnid]['sync'];
			$pconfig['sparse'] = isset($a_volume[$cnid]['sparse']);
			$pconfig['desc'] = $a_volume[$cnid]['desc'];
			break;
		case RECORD_MODIFY: // get from config or system
			$pconfig['name'] = $a_volume[$cnid]['name'];
			$pconfig['pool'] = $a_volume[$cnid]['pool'][0];
			$pconfig['volsize'] = $a_volume[$cnid]['volsize'];
			$pconfig['volmode'] = $a_volume[$cnid]['volmode'];
			$pconfig['volblocksize'] = get_volblocksize($pconfig['pool'], $pconfig['name']);
			$pconfig['compression'] = $a_volume[$cnid]['compression'];
			$pconfig['dedup'] = $a_volume[$cnid]['dedup'];
			$pconfig['sync'] = $a_volume[$cnid]['sync'];
			$pconfig['sparse'] = isset($a_volume[$cnid]['sparse']);
			$pconfig['desc'] = $a_volume[$cnid]['desc'];
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
$l_volmode = ["default" => gettext("Default"), "geom" => "geom", "dev" => "dev", "none" => "none"];
$l_compressionmode = ["on" => gettext("On"), "off" => gettext("Off"), "lz4" => "lz4", "lzjb" => "lzjb", "gzip" => "gzip", "zle" => "zle"];
for ($n = 1; $n <= 9; $n++) {
	$helpinghand = sprintf('gzip-%d',$n);
	$l_compressionmode[$helpinghand] = $helpinghand;
}
$l_dedup = ["on" => gettext("On"), "off" => gettext("Off"), "verify" => "verify", "sha256" => "sha256", "sha256,verify" => "sha256,verify"];
$l_sync = ["standard" => "standard", "always" => "always", "disabled" => "disabled"];
$l_volblocksize = ["" => gettext("Default"), "512B" => "512B", "1K" => "1K", "2K" => "2K", "4K" => "4K", "8K" => "8K", "16K" => "16K", "32K" => "32K", "64K" => "64K", "128K" => "128K"];

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Volumes"), gettext("Volume"), (RECORD_NEW !== $mode_record) ? gettext("Edit") : gettext("Add"));
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!--
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
	document.iform.pool.disabled = !enable_change;
	document.iform.volblocksize.disabled = !enable_change;
}
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Datasets");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_volume.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Volumes");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot.php"><span><?=gettext("Snapshots");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext("Configuration");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabact"><a href="disks_zfs_volume.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Volume");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_volume_info.php"><span><?=gettext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_zfs_volume_edit.php" method="post" name="iform" id="iform">
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
						<?php html_inputbox("volsize", gettext("Size"), $pconfig['volsize'], gettext("ZFS volume size. To specify the size use the following human-readable suffixes (for example, 'k', 'KB', 'M', 'Gb', etc.)."), true, 10);?>
						<?php html_combobox("volmode", gettext("Volume mode"), $pconfig['volmode'], $l_volmode, gettext("Specifies how the volume should be exposed to the OS."), true);?>
						<?php html_combobox("compression", gettext("Compression"), $pconfig['compression'], $l_compressionmode, gettext("Controls the compression algorithm used for this volume. The 'lzjb' compression algorithm is optimized for performance while providing decent data compression. Setting compression to 'On' uses the 'lzjb' compression algorithm. You can specify the 'gzip' level by using the value 'gzip-N', where N is an integer from 1 (fastest) to 9 (best compression ratio). Currently, 'gzip' is equivalent to 'gzip-6'."), true);?>
						<?php html_combobox("dedup", gettext("Dedup"), $pconfig['dedup'], $l_dedup, gettext("Controls the dedup method. <br><b><font color='red'>NOTE/WARNING</font>: See <a href='http://wiki.nas4free.org/doku.php?id=documentation:setup_and_user_guide:disks-zfs-volumes-volume' target='_blank'>ZFS volumes & deduplication</a> wiki article BEFORE using this feature.</b></br>"), true);?>
						<?php html_combobox("sync", gettext("Sync"), $pconfig['sync'], $l_sync, gettext("Controls the behavior of synchronous requests."), true);?>
						<?php html_checkbox("sparse", gettext("Sparse Volume"), !empty($pconfig['sparse']) ? true : false, gettext("Use as sparse volume. (thin provisioning)"), "", false);?>
						<?php html_combobox("volblocksize", gettext("Block size"), $pconfig['volblocksize'], $l_volblocksize, gettext("ZFS volume block size. This value can not be changed after creation."), true);?>
						<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
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
