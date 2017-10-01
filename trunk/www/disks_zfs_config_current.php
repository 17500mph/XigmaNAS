<?php
/*
	disks_zfs_config_current.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
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
require_once 'auth.inc';
require_once 'guiconfig.inc';

array_make_branch($config,'zfs','pools','pool');
array_make_branch($config,'zfs','vdevices','vdevice');
array_make_branch($config,'zfs','datasets','dataset');
array_make_branch($config,'zfs','volumes','volume');
$zfs = $config['zfs'];
foreach($zfs['pools']['pool'] as $index => $pool):
	$zfs['pools']['pool'][$index]['size'] = gtext('Unknown');
	$zfs['pools']['pool'][$index]['used'] = gtext('Unknown');
	$zfs['pools']['pool'][$index]['avail'] = gtext('Unknown');
	$zfs['pools']['pool'][$index]['cap'] = gtext('Unknown');
	$zfs['pools']['pool'][$index]['health'] = gtext('Unknown');
	foreach($pool['vdevice'] as $vdevice):
		if(false === ($index = array_search_ex($vdevice,$zfs['vdevices']['vdevice'],'name'))):
			continue;
		endif;
		$zfs['vdevices']['vdevice'][$index]['pool'] = $pool['name'];
	endforeach;
endforeach;
unset($rawdata);
unset($retval);
$cmd = 'zfs list -H -t filesystem -o name,used,available';
mwexec2($cmd,$rawdata,$retval);
if(0 == $retval):
	foreach($rawdata as $line):
		if($line == 'no datasets available'):
			continue;
		endif;
		list($fname,$used,$avail) = explode("\t",$line);
		if(false === ($index = array_search_ex($fname,$zfs['pools']['pool'],'name'))):
			continue;
		endif;
		if(strpos($fname,'/') === false): // zpool
			$zfs['pools']['pool'][$index]['used'] = $used;
			$zfs['pools']['pool'][$index]['avail'] = $avail;
		endif;
	endforeach;
endif;
unset($rawdata);
unset($retval);
$cmd = 'zpool list -H -o name,altroot,size,allocated,free,capacity,expandsz,frag,health,dedup';
mwexec2($cmd,$rawdata,$retval);
if(0 == $retval):
	foreach($rawdata as $line):
		if($line == 'no pools available'):
			continue;
		endif;
		list($pool,$root,$size,$alloc,$free,$cap,$expandsz,$frag,$health,$dedup) = explode("\t",$line);
		if(false === ($index = array_search_ex($pool,$zfs['pools']['pool'],'name'))):
			continue;
		endif;
		unset($row);
		$row = &$zfs['pools']['pool'][$index];
		if($root != '-'):
			$row['root'] = $root;
		endif;
		$row['size'] = $size;
		$row['alloc'] = $alloc;
		$row['free'] = $free;
		$row['expandsz'] = $expandsz;
		$row['frag'] = $frag;
		$row['cap'] = $cap;
		$row['health'] = $health;
		$row['dedup'] = $dedup;
	endforeach;
endif;
unset($rawdata);
unset($retval);
if(updatenotify_exists('zfs_import_config')):
	$notifications = updatenotify_get('zfs_import_config');
	$retval = 0;
	foreach($notifications as $notification):
		$retval |= !($notification['data'] == true);
	endforeach;
	$savemsg = get_std_save_message($retval);
	if($retval == 0):
		updatenotify_delete('zfs_import_config');
	endif;
endif;
$showusedavail = isset($config['zfs']['settings']['showusedavail']);
$pgtitle = [gtext('Disks'),gtext('ZFS'),gtext('Configuration'),gtext('Current')];
include 'fbegin.inc';
?>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gtext('Pools');?></span></a></li>
		<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gtext('Datasets');?></span></a></li>
		<li class="tabinact"><a href="disks_zfs_volume.php"><span><?=gtext('Volumes');?></span></a></li>
		<li class="tabinact"><a href="disks_zfs_snapshot.php"><span><?=gtext('Snapshots');?></span></a></li>
		<li class="tabact"><a href="disks_zfs_config.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Configuration');?></span></a></li>
	</ul></td></tr>
	<tr><td class="tabnavtbl"><ul id="tabnav2">
		<li class="tabact"><a href="disks_zfs_config_current.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Current');?></span></a></li>
		<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gtext('Detected');?></span></a></li>
		<li class="tabinact"><a href="disks_zfs_config_sync.php"><span><?=gtext('Synchronize');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
?>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:16%">
			<col style="width:10%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:10%">
			<col style="width:10%">
		</colgroup>
		<thead>
<?php
			html_titleline2(sprintf('%s (%d)',gtext('Pools'),count($zfs['pools']['pool'])),10);
?>
			<tr>
				<th class="lhell"><?=gtext('Name');?></th>
				<th class="lhell"><?=gtext('Size');?></th>
<?php
				if($showusedavail):
?>
					<th class="lhell"><?=gtext('Used');?></th>
					<th class="lhell"><?=gtext('Avail');?></th>
<?php
				else:
?>
					<th class="lhell"><?=gtext('Alloc');?></th>
					<th class="lhell"><?=gtext('Free');?></th>
<?php
				endif;
?>
				<th class="lhell"><?=gtext('Expandsz');?></th>
				<th class="lhell"><?=gtext('Frag');?></th>
				<th class="lhell"><?=gtext('Dedup');?></th>
				<th class="lhell"><?=gtext('Health');?></th>
				<th class="lhell"><?=gtext('Mount Point');?></th>
				<th class="lhebl"><?=gtext('AltRoot');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($zfs['pools']['pool'] as $pool):
?>
				<tr>
					<td class="lcell"><?=$pool['name'];?></td>
					<td class="lcell"><?=$pool['size'];?></td>
<?php
					if($showusedavail):
?>
						<td class="lcell"><?=$pool['used'];?></td>
						<td class="lcell"><?=$pool['avail'];?></td>
<?php
					else:
?>
						<td class="lcell"><?=$pool['alloc'];?> (<?=$pool['cap'];?>)</td>
						<td class="lcell"><?=$pool['free'];?></td>
<?php
					endif;
?>
					<td class="lcell"><?=$pool['expandsz']; ?></td>
					<td class="lcell"><?=$pool['frag']; ?></td>
					<td class="lcell"><?=$pool['dedup']; ?></td>
					<td class="lcell"><?=$pool['health']; ?></td>
					<td class="lcell"><?=empty($pool['mountpoint']) ? "/mnt/{$pool['name']}" : $pool['mountpoint'];?></td>
					<td class="lcebl"><?=empty($pool['root']) ? '-' : $pool['root'];?></td>
				</tr>
<?php
			endforeach;
?>
		</tbody>
		<tfoot>
			<tr>
				<td class="lcenl" colspan="10"></td>
			</tr>
		</tfoot>
	</table>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:16%">
			<col style="width:21%">
			<col style="width:21%">
			<col style="width:42%">
		</colgroup>
		<thead>
<?php
			html_titleline2(sprintf('%s (%d)',gtext('Virtual Devices'),count($zfs['vdevices']['vdevice'])),4);
?>
			<tr>
				<th class="lhell"><?=gtext('Name');?></th>
				<th class="lhell"><?=gtext('Type');?></th>
				<th class="lhell"><?=gtext('Pool');?></th>
				<th class="lhebl"><?=gtext('Devices');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($zfs['vdevices']['vdevice'] as $vdevice):
?>
				<tr>
					<td class="lcell"><?=$vdevice['name'];?></td>
					<td class="lcell"><?=$vdevice['type'];?></td>
					<td class="lcell"><?=!empty($vdevice['pool']) ? $vdevice['pool'] : '';?></td>
					<td class="lcebl"><?=implode(',',$vdevice['device']);?></td>
				</tr>
<?php
			endforeach;
?>
		</tbody>
		<tfoot>
			<tr>
				<td class="lcenl" colspan="4"></td>
			</tr>
		</tfoot>
	</table>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:14%">
			<col style="width:14%">
			<col style="width:7%">
			<col style="width:7%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:7%">
			<col style="width:8%">
<?php
/*
			<col style="width:8%">
 */
?>
			<col style="width:7%">
			<col style="width:9%">
		</colgroup>
		<thead>
<?php
			html_titleline2(sprintf('%s (%d)',gtext('Datasets'),count($zfs['datasets']['dataset'])),11);
?>
			<tr>
				<th class="lhell"><?=gtext('Name');?></th>
				<th class="lhell"><?=gtext('Pool');?></th>
				<th class="lhell"><?=gtext('Compression');?></th>
				<th class="lhell"><?=gtext('Dedup');?></th>
				<th class="lhell"><?=gtext('Sync');?></th>
				<th class="lhell"><?=gtext('ACL Inherit');?></th>
				<th class="lhell"><?=gtext('ACL Mode');?></th>
				<th class="lhell"><?=gtext('Canmount');?></th>
				<th class="lhell"><?=gtext('Quota');?></th>
<?php
/*
				<th class="lhell"><?=gtext('Extended Attributes');?></th>
 */
?>
				<th class="lhell"><?=gtext('Readonly');?></th>
				<th class="lhebl"><?=gtext('Snapshot Visibility');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($zfs['datasets']['dataset'] as $dataset):
?>
				<tr>
					<td class="lcell"><?=$dataset['name'];?></td>
					<td class="lcell"><?=$dataset['pool'][0];?></td>
					<td class="lcell"><?=$dataset['compression'];?></td>
					<td class="lcell"><?=$dataset['dedup'];?></td>
					<td class="lcell"><?=$dataset['sync'];?></td>
					<td class="lcell"><?=$dataset['aclinherit'];?></td>
					<td class="lcell"><?=$dataset['aclmode'];?></td>
					<td class="lcell"><?=isset($dataset['canmount']) ? 'on' : 'off';?></td>
					<td class="lcell"><?=empty($dataset['quota']) ? 'none' : $dataset['quota'];?></td>
<?php
/*
					<td class="lcell"><?=isset($dataset['xattr']) ? 'on' : 'off';?></td>
 */
?>
					<td class="lcell"><?=isset($dataset['readonly']) ? 'on' : 'off';?></td>
					<td class="lcebl"><?=isset($dataset['snapdir']) ? 'visible' : 'hidden';?></td>
				</tr>
<?php
			endforeach;
?>
		</tbody>
		<tfoot>
			<tr>
				<td class="lcenl" colspan="11"></td>
			</tr>
		</tfoot>
	</table>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:16%">
			<col style="width:12%">
			<col style="width:12%">
			<col style="width:12%">
			<col style="width:12%">
			<col style="width:12%">
			<col style="width:12%">
			<col style="width:12%">
		</colgroup>
		<thead>
<?php
			html_titleline2(sprintf('%s (%d)',gtext('Volumes'),count($zfs['volumes']['volume'])),8);
?>
			<tr>
				<th class="lhell"><?=gtext('Name');?></th>
				<th class="lhell"><?=gtext('Pool');?></th>
				<th class="lhell"><?=gtext('Size');?></th>
				<th class="lhell"><?=gtext('Blocksize');?></th>
				<th class="lhell"><?=gtext('Sparse');?></th>
				<th class="lhell"><?=gtext('Compression');?></th>
				<th class="lhell"><?=gtext('Dedup');?></th>
				<th class="lhebl"><?=gtext('Sync');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($zfs['volumes']['volume'] as $volume):
?>
				<tr>
					<td class="lcell"><?=$volume['name']; ?></td>
					<td class="lcell"><?=$volume['pool'][0];?></td>
					<td class="lcell"><?=$volume['volsize'];?></td>
					<td class="lcell"><?=!empty($volume['volblocksize']) ? $volume['volblocksize'] : '-';?></td>
					<td class="lcell"><?=!isset($volume['sparse']) ? '-' : 'on';?></td>
					<td class="lcell"><?=$volume['compression'];?></td>
					<td class="lcell"><?=$volume['dedup'];?></td>
					<td class="lcebl"><?=$volume['sync'];?></td>
				</tr>
<?php
			endforeach;
?>
		</tbody>
	</table>
	<div id="remarks">
<?php
		html_remark2('note',gtext('Note'),gtext('This page reflects the configuration that has been created with the WebGUI.'));
?>
	</div>
</td></tr></tbody></table>
<?php
include 'fend.inc';
?>
