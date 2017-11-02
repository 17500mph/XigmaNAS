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
$cmd = 'zfs list -pH -t filesystem -o name,used,available';
mwexec2($cmd,$rawdata,$retval);
if(0 == $retval):
	foreach($rawdata as $line):
		if($line == 'no datasets available'):
			continue;
		endif;
		list($fname,$used,$avail) = explode("\t",$line);
		if(false !== ($index = array_search_ex($fname,$zfs['pools']['pool'],'name'))):
			if(strpos($fname,'/') === false): // zpool
				$zfs['pools']['pool'][$index]['used'] = format_bytes($used,2,false,is_sidisksizevalues());
				$zfs['pools']['pool'][$index]['avail'] = format_bytes($avail,2,false,is_sidisksizevalues());
			endif;
		endif;
	endforeach;
endif;
unset($rawdata);
unset($retval);
$cmd = 'zpool list -pH -o name,altroot,size,allocated,free,capacity,expandsz,frag,health,dedup';
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
		$row['size'] = format_bytes($size,2,false,is_sidisksizevalues());
		$row['alloc'] = format_bytes($alloc,2,false,is_sidisksizevalues());
		$row['free'] = format_bytes($free,2,false,is_sidisksizevalues());
		$row['expandsz'] = $expandsz;
		$row['frag'] = $frag;
		$row['cap'] = sprintf('%d%%',$cap);
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
$document = new_page($pgtitle);
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add tab navigation
$document->
	add_area_tabnav()->
		push()->add_tabnav_upper()->
			mount_tabnav_record('disks_zfs_zpool.php',gtext('Pools'))->
			mount_tabnav_record('disks_zfs_dataset.php',gtext('Datasets'))->
			mount_tabnav_record('disks_zfs_volume.php',gtext('Volumes'))->
			mount_tabnav_record('disks_zfs_snapshot.php',gtext('Snapshots'))->
			mount_tabnav_record('disks_zfs_config.php',gtext('Configuration'),gtext('Reload page'),true)->
		pop()->add_tabnav_lower()->
			mount_tabnav_record('disks_zfs_config_current.php',gtext('Current'),gtext('Reload page'),true)->
			mount_tabnav_record('disks_zfs_config.php',gtext('Detected'))->
			mount_tabnav_record('disks_zfs_config_sync.php',gtext('Synchronize'));
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->mount_info_box($savemsg);
$table = $content->add_table_data_selection();
$a_col_width = ['16%','10%','9%','9%','9%','9%','9%','9%','10%','10%'];
$n_col_width = count($a_col_width);
$table->mount_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$thead->mount_titleline(sprintf('%s (%d)',gtext('Pools'),count($zfs['pools']['pool'])),$n_col_width);
$tr = $thead->addTR();
$tr->
	mountTH_class('lhell',gtext('Name'))->
	mountTH_class('lhell',gtext('Size'));
if($showusedavail):
	$tr->
		mountTH_class('lhell',gtext('Used'))->
		mountTH_class('lhell',gtext('Avail'));
else:
	$tr->
		mountTH_class('lhell',gtext('Alloc'))->
		mountTH_class('lhell',gtext('Free'));
endif;
$tr->
	mountTH_class('lhell',gtext('Expandsz'))->
	mountTH_class('lhell',gtext('Frag'))->
	mountTH_class('lhell',gtext('Dedup'))->
	mountTH_class('lhell',gtext('Health'))->
	mountTH_class('lhell',gtext('Mount Point'))->
	mountTH_class('lhebl',gtext('AltRoot'));
$tbody = $table->addTBODY();
foreach($zfs['pools']['pool'] as $pool):
	$tr = $tbody->addTR();
	$tr->
		mountTD_class('lcell',$pool['name'])->
		mountTD_class('lcell',$pool['size']);
		if($showusedavail):
			$tr->
				mountTD_class('lcell',$pool['used'])->
				mountTD_class('lcell',$pool['avail']);
		else:
			$tr->
				mountTD_class('lcell',$pool['alloc'])->
				mountTD_class('lcell',$pool['free']);
		endif;
	$tr->
		mountTD_class('lcell',$pool['expandsz'])->
		mountTD_class('lcell',$pool['frag'])->
		mountTD_class('lcell',$pool['dedup'])->
		mountTD_class('lcell',$pool['health'])->
		mountTD_class('lcell',empty($pool['mountpoint']) ? sprintf('/mnt/%s',$pool['name']) : $pool['mountpoint'])->
		mountTD_class('lcebl',empty($pool['root']) ? '-' : $pool['root']);
endforeach;
$table = $content->add_table_data_selection();
$a_col_width = ['16%','21%','21%','42%'];
$n_col_width = count($a_col_width);
$table->mount_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$thead->
	mount_separator($n_col_width)->
	mount_titleline(sprintf('%s (%d)',gtext('Virtual Devices'),count($zfs['vdevices']['vdevice'])),$n_col_width);
$thead->
	addTR()->
		mountTH_class('lhell',gtext('Name'))->
		mountTH_class('lhell',gtext('Type'))->
		mountTH_class('lhell',gtext('Pool'))->
		mountTH_class('lhebl',gtext('Devices'));
$tbody = $table->addTBODY();
foreach($zfs['vdevices']['vdevice'] as $vdevice):
	$tbody->
		addTR()->
			mountTD_class('lcell',$vdevice['name'])->
			mountTD_class('lcell',$vdevice['type'])->
			mountTD_class('lcell',!empty($vdevice['pool']) ? $vdevice['pool'] : '')->
			mountTD_class('lcebl',implode(',',$vdevice['device']));
endforeach;
$table = $content->add_table_data_selection();
$a_col_width = ['14%','14%','7%','7%','9%','9%','9%','7%','8%','7%','9%'];
$n_col_width = count($a_col_width);
$table->mount_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$thead->
	mount_separator($n_col_width)->
	mount_titleline(sprintf('%s (%d)',gtext('Datasets'),count($zfs['datasets']['dataset'])),$n_col_width);
$thead->
	addTR()->
		mountTH_class('lhell',gtext('Name'))->
		mountTH_class('lhell',gtext('Pool'))->
		mountTH_class('lhell',gtext('Compression'))->
		mountTH_class('lhell',gtext('Dedup'))->
		mountTH_class('lhell',gtext('Sync'))->
		mountTH_class('lhell',gtext('ACL Inherit'))->
		mountTH_class('lhell',gtext('ACL Mode'))->
		mountTH_class('lhell',gtext('Canmount'))->
		mountTH_class('lhell',gtext('Quota'))->
		mountTH_class('lhell',gtext('Readonly'))->
		mountTH_class('lhebl',gtext('Snapshot Visibility'));
$tbody = $table->addTBODY();
foreach($zfs['datasets']['dataset'] as $dataset):
	$tbody->
		addTR()->
			mountTD_class('lcell',$dataset['name'])->
			mountTD_class('lcell',$dataset['pool'][0])->
			mountTD_class('lcell',$dataset['compression'])->
			mountTD_class('lcell',$dataset['dedup'])->
			mountTD_class('lcell',$dataset['sync'])->
			mountTD_class('lcell',$dataset['aclinherit'])->
			mountTD_class('lcell',$dataset['aclmode'])->
			mountTD_class('lcell',isset($dataset['canmount']) ? gtext('on') : gtext('off'))->
			mountTD_class('lcell',empty($dataset['quota']) ? gtext('none') : $dataset['quota'])->
			mountTD_class('lcell',isset($dataset['readonly']) ? gtext('on') : gtext('off'))->
			mountTD_class('lcebl',isset($dataset['snapdir']) ? gtext('visible') : gtext('hidden'));
endforeach;
$table = $content->add_table_data_selection();
$a_col_width = ['16%','12%','12%','12%','12%','12%','12%','12%'];
$n_col_width = count($a_col_width);
$table->mount_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$thead->
	mount_separator($n_col_width)->
	mount_titleline(sprintf('%s (%d)',gtext('Volumes'),count($zfs['volumes']['volume'])),$n_col_width);
$thead->
	addTR()->
		mountTH_class('lhell',gtext('Name'))->
		mountTH_class('lhell',gtext('Pool'))->
		mountTH_class('lhell',gtext('Size'))->
		mountTH_class('lhell',gtext('Blocksize'))->
		mountTH_class('lhell',gtext('Sparse'))->
		mountTH_class('lhell',gtext('Compression'))->
		mountTH_class('lhell',gtext('Dedup'))->
		mountTH_class('lhebl',gtext('Sync'));
$tbody = $table->addTBODY();
foreach($zfs['volumes']['volume'] as $volume):
	$tbody->
		addTR()->
			mountTD_class('lcell',$volume['name'])->
			mountTD_class('lcell',$volume['pool'][0])->
			mountTD_class('lcell',$volume['volsize'])->
			mountTD_class('lcell',!empty($volume['volblocksize']) ? $volume['volblocksize'] : '-')->
			mountTD_class('lcell',!isset($volume['sparse']) ? '-' : gtext('on'))->
			mountTD_class('lcell',$volume['compression'])->
			mountTD_class('lcell',$volume['dedup'])->
			mountTD_class('lcebl',$volume['sync']);
endforeach;
$content->
	add_area_remarks()->
		mount_remark('note',gtext('Note'),gtext('This page reflects the configuration that has been created with the WebGUI.'));
$document->render();
?>
