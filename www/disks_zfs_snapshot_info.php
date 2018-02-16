<?php
/*
	disks_zfs_snapshot_info.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2018 The NAS4Free Project <info@nas4free.org>.
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

function zfs_snapshot_display_list() {
	$cmd = 'zfs list -t snapshot 2>&1';
	unset($output);
	mwexec2($cmd,$output);
	return implode(PHP_EOL,$output);
}
function zfs_snapshot_display_properties() {
	$cmd = 'zfs list -H -o name -t snapshot 2>&1';
	unset($a_names);
	mwexec2($cmd,$a_names);
	if(is_array($a_names) && count($a_names) > 0):
		$names = implode(' ',array_map('escapeshellarg',$a_names));
		$cmd = sprintf('zfs get all %s 2>&1',$names);
		unset($output);
		mwexec2($cmd,$output);
	else:
		$output = [gtext('No snapshot information available.')];
	endif;
	return implode(PHP_EOL,$output);
}
$pgtitle = [gtext('Disks'),gtext('ZFS'),gtext('Snapshots'),gtext('Information')];
include 'fbegin.inc';
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		push()->
		add_tabnav_upper()->
			ins_tabnav_record('disks_zfs_zpool.php',gtext('Pools'))->
			ins_tabnav_record('disks_zfs_dataset.php',gtext('Datasets'))->
			ins_tabnav_record('disks_zfs_volume.php',gtext('Volumes'))->
			ins_tabnav_record('disks_zfs_snapshot.php',gtext('Snapshots'),gtext('Reload page'),true)->
			ins_tabnav_record('disks_zfs_config.php',gtext('Configuration'))->
			ins_tabnav_record('disks_zfs_settings.php',gtext('Settings'))->
		pop()->
		add_tabnav_lower()->
			ins_tabnav_record('disks_zfs_snapshot.php',gtext('Snapshot'))->
			ins_tabnav_record('disks_zfs_snapshot_clone.php',gtext('Clone'))->
			ins_tabnav_record('disks_zfs_snapshot_auto.php',gtext('Auto Snapshot'))->
			ins_tabnav_record('disks_zfs_snapshot_info.php',gtext('Information'),gtext('Reload page'),true);
$document->render();
?>
<table id="area_data"><tbody><tr><td id="area_data_frame">
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('ZFS Snapshot Information & Status'));
?>
		</thead>
		<tbody>
			<tr>
				<td class="celltag"><?=gtext('Information & Status');?></td>
				<td class="celldata">
					<pre><span id="zfs_snapshot_list"><?=zfs_snapshot_display_list();?></span></pre>
				</td>
			</tr>
		</tbody>
		<tfoot>
<?php
			html_separator2();
?>
		</tfoot>
	</table>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('ZFS Snapshot Properties'));
?>
		</thead>
		<tbody>
			<tr>
				<td class="celltag"><?=gtext('Properties');?></td>
				<td class="celldata">
					<pre><span id="zfs_snapshot_properties"><?=zfs_snapshot_display_properties();?></span></pre>
				</td>
			</tr>
		<tbody>
	</table>
</td></tr></tbody></table>
<?php
include 'fend.inc';
?>
