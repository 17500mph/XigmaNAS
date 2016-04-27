<?php
/*
	disks_zfs_zpool_vdevice_edit.php

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
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = 'Location: disks_zfs_zpool_vdevice.php';
$sphere_notifier = 'zfsvdev';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$prerequisites_ok = true;

$mode_page = ($_POST) ? PAGE_MODE_POST : (($_GET) ? PAGE_MODE_EDIT : PAGE_MODE_ADD); // detect page mode
if (PAGE_MODE_POST == $mode_page) { // POST is Cancel
	if ((isset($_POST['Cancel']) && $_POST['Cancel'])) {
		header($sphere_header_parent);
		exit;
	}
}

if ((PAGE_MODE_POST == $mode_page) && isset($_POST['uuid']) && is_uuid_v4($_POST['uuid'])) {
	$sphere_record['uuid'] = $_POST['uuid'];
} else {
	if ((PAGE_MODE_EDIT == $mode_page) && isset($_GET['uuid']) && is_uuid_v4($_GET['uuid'])) {
		$sphere_record['uuid'] = $_GET['uuid'];
	} else {
		$mode_page = PAGE_MODE_ADD; // Force ADD
		$sphere_record['uuid'] = uuid();
	}
}

if (!(isset($config['zfs']['vdevices']['vdevice']) && is_array($config['zfs']['vdevices']['vdevice']))) {
	$config['zfs']['vdevices']['vdevice'] = [];
}
array_sort_key($config['zfs']['vdevices']['vdevice'], 'name');
$sphere_array = &$config['zfs']['vdevices']['vdevice'];

$index = array_search_ex($sphere_record['uuid'], $sphere_array, 'uuid'); // find index of uuid
$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_record['uuid']); // get updatenotify mode for uuid
$mode_record = RECORD_ERROR;
if (false !== $index) { // uuid found
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
	header($sphere_header_parent);
	exit;
}

function strip_dev($device) {
	// returns the device name that follows after '/dev/' , i.e. ada0, ada0p1 or otherwise returns an empty array 
	if (preg_match("/^\/dev\/(.+)$/", $device, $m)) {
		$device = $m[1];
	}
	return $device;
}
function strip_partition($device) {
	// returns the device name without partition information, .e. /dev/ada0p1 -> /dev/ada0 or an empty array 
	if (preg_match("/^(.*)p\d+$/", $device, $m)) {
		$device = $m[1];
	}
	return $device;
}
function strip_exists($device, &$sphere_array) {
	if (false !== array_search_ex($diskv['devicespecialfile'], $sphere_array, "device"))
		return true;
	foreach ($sphere_array as $vdevs) {
		foreach ($vdevs['device'] as $dev) {
			// label
			$tmp = disks_label_to_device($dev);
			if (strcmp($tmp, $device) == 0)
				return true;
			// label+partition
			$tmp = strip_partition($tmp);
			if (strcmp($tmp, $device) == 0)
				return true;
			// partition
			$tmp = strip_partition($dev);
			if (strcmp($tmp, $device) == 0)
				return true;
		}
	}
	return false;
}

$a_disk = get_conf_disks_filtered_ex('fstype', 'zfs');
if (((RECORD_NEW == $mode_record) || (RECORD_NEW_MODIFY == $mode_record)) && (empty($a_disk)) && (empty($a_encrypteddisk))) {
	$errormsg = sprintf(gettext("No disks available. Please add new <a href='%s'>disk</a> first."), "disks_manage.php");
	$prerequisites_ok = false;
}

$a_device = [];
foreach ($a_disk as $r_disk) {
	$helpinghand = $r_disk['devicespecialfile'] . (isset($r_disk['zfsgpt']) ? $r_disk['zfsgpt'] : '');
	$a_device[$helpinghand] = [
		'name' => htmlspecialchars($r_disk['name']),
		'uuid' => $r_disk['uuid'],
		'model' => htmlspecialchars($r_disk['model']),
		'devicespecialfile' => htmlspecialchars($helpinghand),
		'partition' => ((isset($r_disk['zfsgpt']) && (!empty($r_disk['zfsgpt'])))? $r_disk['zfsgpt'] : gettext('Entire Device')),
		'controller' => $r_disk['controller'].$r_disk['controller_id'].' ('.$r_disk['controller_desc'].')',
		'size' => $r_disk['size'],
		'serial' => $r_disk['serial'],
		'desc' => htmlspecialchars($r_disk['desc'])
	];
}

if (PAGE_MODE_POST == $mode_page) { // at this point we know it's a POST but (except Cancel) we don't know which one
	unset($input_errors);
	if (isset($_POST['Submit']) && $_POST['Submit']) { // Submit is coming from Save button which is only shown when an existing vdevice is modified (RECORD_MODIFY)
		$sphere_record['name'] = $sphere_array[$index]['name'];
		$sphere_record['type'] = $sphere_array[$index]['type'];
		$sphere_record['device'] = $sphere_array[$index]['device'];
		$sphere_record['aft4k'] = isset($sphere_array[$index]['aft4k']);
		$sphere_record['desc'] = $_POST['desc'];
	}
	if (isset($_POST['Action']) && $_POST['Action']) { // RECORD_NEW or RECORD_NEW_MODIFY
		if (!isset($_POST[$checkbox_member_name])) { $_POST[$checkbox_member_name] = []; }
		$sphere_record['name'] = $_POST['name'];
		$sphere_record['type'] = $_POST['Action'];
		$sphere_record['device'] = $_POST[$checkbox_member_name];
		$sphere_record['aft4k'] = isset($_POST['aft4k']);
		$sphere_record['desc'] = $_POST['desc'];
	}

	// Input validation
	$reqdfields = explode(' ', 'name type');
	$reqdfieldsn = array(gettext('Name'), gettext('Type'));
	$reqdfieldst = explode(' ', 'string string');

	do_input_validation($sphere_record, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($sphere_record, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	// Check for duplicate name
	if ($prerequisites_ok && empty($input_errors)) {
		switch ($mode_record) {
			case RECORD_NEW: // Error if name is found in the list of vdevice names
				if (false !== array_search_ex($sphere_record['name'], $sphere_array, 'name')) { 
					$input_errors[] = gettext('This virtual device name already exists.');
				}
				break;
			case RECORD_NEW_MODIFY: // Error if modified name is found in the list of vdevice names
				if ($sphere_record['name'] !== $sphere_array[$index]['name']) {
					if (false !== array_search_ex($sphere_record['name'], $sphere_array, 'name')) {
						$input_errors[] = gettext('This virtual device name already exists.');
					}
				}
				break;
			case RECORD_MODIFY: // Error if name is changed, this error should never occur, just to cover all options
				if ($sphere_record['name'] !== $sphere_array[$index]['name']) {
					$input_errors[] = gettext('The name of this virtual device cannot be changed.');
				}
				break;
		}
	}
	if ($prerequisites_ok && empty($input_errors)) {
		if (isset($_POST['Action'])) { // RECORD_NEW or RECORD_NEW_MODIFY
			switch ($_POST['Action']) {
				case 'log-mirror':
				case 'mirror':
					if (count($sphere_record['device']) <  2) {
						$input_errors[] = gettext('There must be at least 2 disks in a mirror.');
					}
					break;
				case 'raidz':
				case 'raidz1':
					if (count($sphere_record['device']) <  2) {
						$input_errors[] = gettext('There must be at least 2 disks in a raidz.');
					}
					break;
				case 'raidz2':
					if (count($sphere_record['device']) <  3) {
						$input_errors[] = gettext('There must be at least 3 disks in a raidz2.');
					}
					break;
				case 'raidz3':
						if (count($sphere_record['device']) <  4) {
							$input_errors[] = gettext('There must be at least 4 disks in a raidz3.');
					}
					break;
				default:
					if (count($sphere_record['device']) <  1) {
						$input_errors[] = gettext('There must be at least 1 disks selected.');
					}
					break;
			}
		}
	}
	if ($prerequisites_ok && empty($input_errors)) {
		if (RECORD_NEW == $mode_record) {
			$sphere_array[] = $sphere_record;
			updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_NEW, $sphere_record['uuid']);
		} else {
			$sphere_array[$index] = $sphere_record;
			if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
				updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_MODIFIED, $sphere_record['uuid']);
			}
		}
		write_config();
		header($sphere_header_parent);
		exit;
	}
} else { // EDIT / ADD
	switch ($mode_record) {
		case RECORD_NEW:
			$sphere_record['name'] = '';
			$sphere_record['type'] = 'stripe';
			$sphere_record['device'] = [];
			$sphere_record['aft4k'] = false;
			$sphere_record['desc'] = '';
			break;
		case RECORD_NEW_MODIFY:
		case RECORD_MODIFY:
			$sphere_record['name'] = $sphere_array[$index]['name'];
			$sphere_record['type'] = $sphere_array[$index]['type'];
			$sphere_record['device'] = $sphere_array[$index]['device'];
			$sphere_record['aft4k'] = isset($sphere_array[$index]['aft4k']);
			$sphere_record['desc'] = $sphere_array[$index]['desc'];
			break;
	}
}

$pgtitle = array(gettext('Disks'), gettext('ZFS'), gettext('Pools'), gettext('Virtual Device'), (RECORD_NEW !== $mode_record) ? gettext('Edit') : gettext('Add'));
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!-- Begin JavaScript
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
	document.iform.type.disabled = !enable_change;
	document.iform.aft4k.disabled = !enable_change;
}
function disableactionbuttons(n) {
	var ab_element;
	var ab_disable = [];
	if (typeof(n) !== 'number') { n = 0; }
 	switch (n) { //           stripe, mirror, raidz1, raidz2, raidz3, hotspa, cache , log   , log mirror
		case  0: ab_disable = [true  , true  , true  , true  , true  , true  , true  , true  , true  ]; break;
		case  1: ab_disable = [false , true  , true  , true  , true  , false , false , false , true  ]; break;
		case  2: ab_disable = [false , false , true  , true  , true  , true  , true  , true  , false ]; break;
		case  3: ab_disable = [false , false , false , true  , true  , true  , true  , true  , false ]; break;
		case  4: ab_disable = [false , false , false , false , true  , true  , true  , true  , false ]; break;
		default: ab_disable = [false , false , false , false , false , true  , true  , true  , false ]; break; // setting for 5 or more disks
	}		
	ab_element = document.getElementById('disk_stripe'); if ((ab_element !== null) && (ab_element.disabled !== ab_disable[0])) { ab_element.disabled = ab_disable[0]; }
	ab_element = document.getElementById('disk_mirror'); if ((ab_element !== null) && (ab_element.disabled !== ab_disable[1])) { ab_element.disabled = ab_disable[1]; }
	ab_element = document.getElementById('disk_raidz1'); if ((ab_element !== null) && (ab_element.disabled !== ab_disable[2])) { ab_element.disabled = ab_disable[2]; }
	ab_element = document.getElementById('disk_raidz2'); if ((ab_element !== null) && (ab_element.disabled !== ab_disable[3])) { ab_element.disabled = ab_disable[3]; }
	ab_element = document.getElementById('disk_raidz3'); if ((ab_element !== null) && (ab_element.disabled !== ab_disable[4])) { ab_element.disabled = ab_disable[4]; }
	ab_element = document.getElementById('disk_spare') ; if ((ab_element !== null) && (ab_element.disabled !== ab_disable[5])) { ab_element.disabled = ab_disable[5]; }
	ab_element = document.getElementById('disk_cache') ; if ((ab_element !== null) && (ab_element.disabled !== ab_disable[6])) { ab_element.disabled = ab_disable[6]; }
	ab_element = document.getElementById('disk_log')   ; if ((ab_element !== null) && (ab_element.disabled !== ab_disable[7])) { ab_element.disabled = ab_disable[7]; }
	ab_element = document.getElementById('disk_logmir'); if ((ab_element !== null) && (ab_element.disabled !== ab_disable[8])) { ab_element.disabled = ab_disable[8]; }
}
function controlactionbuttons(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var i = 0;
	var n = 0;
	for (; i < n_trigger; i++) {
		if ((a_trigger[i].type === 'checkbox') && !a_trigger[i].disabled && a_trigger[i].checked) {
			n++;
		}
	}
	disableactionbuttons(n);
}
// End JavaScript -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext('Reload page');?>"><span><?=gettext('Pools');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext('Datasets');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_volume.php"><span><?=gettext('Volumes');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot.php"><span><?=gettext('Snapshots');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext('Configuration');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabact"><a href="disks_zfs_zpool_vdevice.php" title="<?=gettext('Reload page');?>"><span><?=gettext('Virtual Device');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext('Management');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><span><?=gettext('Tools');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_info.php"><span><?=gettext('Information');?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><span><?=gettext('I/O Statistics');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="<?php $sphere_scriptname;?>" method="post" name="iform" id="iform">
				<?php
					if (!empty($errormsg)) print_error_box($errormsg);
					if (!empty($input_errors)) print_input_errors($input_errors);
					if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));
				?>
				<div id="submit" style="margin-bottom:10px">
					<button name="Action" id="disk_stripe" type="submit" class="formbtn" value="stripe"     onclick="return confirm('<?=gettext('Do you want to create a striped virtual device from selected disks?') ;?>')"><?=gettext('Stripe')      ;?></button>
					<button name="Action" id="disk_mirror" type="submit" class="formbtn" value="mirror"     onclick="return confirm('<?=gettext('Do you want to create a mirrored virtual device from selected disks?');?>')"><?=gettext('Mirror')      ;?></button>
					<button name="Action" id="disk_raidz1" type="submit" class="formbtn" value="raidz1"     onclick="return confirm('<?=gettext('Do you want to create a RAID-Z1 from selected disks?')                ;?>')"><?=gettext('RAID-Z1')     ;?></button>
					<button name="Action" id="disk_raidz2" type="submit" class="formbtn" value="raidz2"     onclick="return confirm('<?=gettext('Do you want to create a RAID-Z2 from selected disks?')                ;?>')"><?=gettext('RAID-Z2')     ;?></button>
					<button name="Action" id="disk_raidz3" type="submit" class="formbtn" value="raidz3"     onclick="return confirm('<?=gettext('Do you want to create a RAID-Z3 from selected disks?')                ;?>')"><?=gettext('RAID-Z3')     ;?></button>
					<button name="Action" id="disk_spare"  type="submit" class="formbtn" value="spare"      onclick="return confirm('<?=gettext('Do you want to create a hot spare device from selected disk?')        ;?>')"><?=gettext('Hot Spare')   ;?></button>
					<button name="Action" id="disk_cache"  type="submit" class="formbtn" value="cache"      onclick="return confirm('<?=gettext('Do you want to create a cache device from selected disks?')           ;?>')"><?=gettext('Cache')       ;?></button>
					<button name="Action" id="disk_log"    type="submit" class="formbtn" value="log"        onclick="return confirm('<?=gettext('Do you want to create a log device from selected disk?')              ;?>')"><?=gettext('Log')         ;?></button>
					<button name="Action" id="disk_logmir" type="submit" class="formbtn" value="log-mirror" onclick="return confirm('<?=gettext('Do you want to create a mirrored log device from selected disks?')    ;?>')"><?=gettext('Log (Mirror)');?></button>
				</div>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<thead>
						<?php html_titleline(gettext('Settings'));?>
					</thead>
					<tbody>
						<?php
							html_inputbox('name', gettext('Name'), $sphere_record['name'], '', true, 20, RECORD_MODIFY == $mode_record);
							if (RECORD_MODIFY == $mode_record) {
								html_inputbox('type', gettext('Type'), $sphere_record['type'], '', true, 20, true);
							} 
							html_checkbox('aft4k', gettext('4KB wrapper'), !empty($sphere_record['aft4k']) ? true : false, gettext('Create 4KB wrapper (nop device).'), '', false, '');
							html_inputbox('desc', gettext("Description"), $sphere_record['desc'], gettext('You may enter a description here for your reference.'), false, 40);
							html_separator();
						?>
					</tbody>
				</table>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<thead>
						<?php html_titleline(gettext('Device List'));?>
					</thead>
				</table>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<colgroup>
						<col style="width:1%"> <!--// checkbox -->
						<col style="width:10%"><!--// Device -->
						<col style="width:10%"><!--// Partition -->
						<col style="width:15%"><!--// Model -->
						<col style="width:12%"><!--// Serial -->
						<col style="width:12%"><!--// Size -->
						<col style="width:20%"><!--// Controller -->
						<col style="width:15%"><!--// Description -->
						<col style="width:5%"> <!--// Icons -->
					</colgroup>
					<thead>
						<tr>
							<td class="listhdrlr"><input type="checkbox" name="togglemembers" disabled="disabled"/></td>
							<td class="listhdrr"><?=gettext('Device');?></td>
							<td class="listhdrr"><?=gettext('Partition');?></td>
							<td class="listhdrr"><?=gettext('Model');?></td>
							<td class="listhdrr"><?=gettext('Serial Number');?></td>
							<td class="listhdrr"><?=gettext('Size');?></td>
							<td class="listhdrr"><?=gettext('Controller');?></td>
							<td class="listhdrr"><?=gettext('Name');?></td>
							<td class="listhdrr">&nbsp;</td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($a_device as $r_device):?>
							<?php $isnotmemberofavdev = (false === array_search_ex($r_device['devicespecialfile'], $sphere_array, 'device'));?>
							<?php $ismemberofthisvdev = (isset($sphere_record['device']) && is_array($sphere_record['device']) && in_array($r_device['devicespecialfile'], $sphere_record['device']));?>
							<?php if (($isnotmemberofavdev || $ismemberofthisvdev) && ((RECORD_NEW == $mode_record) || (RECORD_NEW_MODIFY == $mode_record))):?>
								<tr>
									<td class="listlr">
										<?php if ($ismemberofthisvdev):?>
											<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$r_device['devicespecialfile'];?>" id="<?=$r_device['uuid'];?>" onclick="javascript:controlactionbuttons(this,'<?=$checkbox_member_name;?>[]')" checked="checked"/>
										<?php else:?>
											<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$r_device['devicespecialfile'];?>" id="<?=$r_device['uuid'];?>" onclick="javascript:controlactionbuttons(this,'<?=$checkbox_member_name;?>[]')"/>
										<?php endif;?>	
									</td>
									<td class="listr"><?=htmlspecialchars($r_device['name']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($r_device['partition']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($r_device['model']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($r_device['serial']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($r_device['size']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($r_device['controller']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($r_device['desc']);?>&nbsp;</td>
									<td valign="middle" nowrap="nowrap" class="listbgc">
										<?php if ($ismemberofthisvdev):?>
											<img src="images/unlocked.png" title="<?=gettext($gt_record_opn);?>" border="0" alt="<?=gettext($gt_record_opn);?>" />
										<?php else:?>
											&nbsp;
										<?php endif;?>
									</td>
								</tr>
							<?php endif;?>
							<?php if ($ismemberofthisvdev && (RECORD_MODIFY == $mode_record)):?>
								<tr>
									<td class="<?=!$ismemberofthisvdev ? "listlr" : "listlrd";?>">
										<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$r_device['devicespecialfile'];?>" id="<?=$r_device['uuid'];?>" checked="checked" disabled="disabled"/>
									</td>
									<td class="<?=!$ismemberofthisvdev ? "listr" : "listrd";?>"><?=htmlspecialchars($r_device['name']);?>&nbsp;</td>
									<td class="<?=!$ismemberofthisvdev ? "listr" : "listrd";?>"><?=htmlspecialchars($r_device['partition']);?>&nbsp;</td>
									<td class="<?=!$ismemberofthisvdev ? "listr" : "listrd";?>"><?=htmlspecialchars($r_device['model']);?>&nbsp;</td>
									<td class="<?=!$ismemberofthisvdev ? "listr" : "listrd";?>"><?=htmlspecialchars($r_device['serial']);?>&nbsp;</td>
									<td class="<?=!$ismemberofthisvdev ? "listr" : "listrd";?>"><?=htmlspecialchars($r_device['size']);?>&nbsp;</td>
									<td class="<?=!$ismemberofthisvdev ? "listr" : "listrd";?>"><?=htmlspecialchars($r_device['controller']);?>&nbsp;</td>
									<td class="<?=!$ismemberofthisvdev ? "listr" : "listrd";?>"><?=htmlspecialchars($r_device['desc']);?>&nbsp;</td>
									<td valign="middle" nowrap="nowrap" class="listbgc">
										<img src="images/locked.png" title="<?=gettext($gt_record_loc);?>" border="0" alt="<?=gettext($gt_record_loc);?>" />
									</td>
								</tr>
							<?php endif;?>
						<?php endforeach;?>
					</tbody>
				</table>
				
				<div id="submit">
					<?php if (RECORD_MODIFY === $mode_record):?>
						<input name="Submit" type="submit" class="formbtn" value="<?=gettext('Save');?>" onclick="enable_change(true)"/>
					<?php endif;?>
					<input name="Cancel" type="submit" class="formbtn" value="<?=gettext('Cancel');?>" />
					<input name="uuid" type="hidden" value="<?=$sphere_record['uuid'];?>" />
				</div>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), sprintf(gettext("Make sure to select the correct number of devices:<div id='enumeration'><ul><li>RAID-Z1 should have 3, 5, or 9 disks in each vdev</li><li>RAID-Z2 should have 4, 6, or 10 disks in each vdev</li><li>RAID-Z3 should have 5, 7, or 11 disks in each vdev</li></ul></div>"), ""));?>
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
<!-- Disable action buttons and give their control to checkbox array. -->
window.onload=function() {
	controlactionbuttons(this,'<?=$checkbox_member_name;?>[]');
}
//-->
</script>
<?php include("fend.inc");?>
