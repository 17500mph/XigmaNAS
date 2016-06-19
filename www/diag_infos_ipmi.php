<?php
/*
	diag_infos_ipmi.php

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

function get_ipmi_sensor() {
	$a_sensor = [];
	mwexec2("ipmitool sensor list all", $a_output);
	foreach ($a_output as $r_output) {
		$r_sensor = explode('|', $r_output);
		$c_sensor = count($r_sensor);
		for ($i = 0; $i < $c_sensor; $i++) {
			$r_sensor[$i] = trim($r_sensor[$i]);
		}
		$a_sensor[] = $r_sensor;
	}
	unset($a_output);
	return $a_sensor;
}

function get_ipmi_fru() {
	$a_fru = [];
	mwexec2("ipmitool fru", $a_output);
	foreach ($a_output as $r_output) {
		$r_fru = explode(': ', $r_output, 2); // we need 2 columns only, tag and value
		$c_fru = count($r_fru);
		for ($i = 0; $i < $c_fru; $i++) {
			$r_fru[$i] = trim($r_fru[$i]);
		}
		$a_fru[] = $r_fru;
	}
	unset($a_output);
	return $a_fru;
}

$a_ipmi_sensor = get_ipmi_sensor();
$a_ipmi_fru = get_ipmi_fru();

$pgtitle = [gettext_gen2('Diagnostics'), gettext_gen2('Information'), gettext_gen2('IPMI Stats')];
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init spinner onsubmit()
	$("#iform").submit(function() { spinner(); });
});
//]]>
</script>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="diag_infos.php"><span><?=gettext_gen2('Disks');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_ata.php"><span><?=gettext_gen2('Disks (ATA)');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_part.php"><span><?=gettext_gen2('Partitions');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_smart.php"><span><?=gettext_gen2('S.M.A.R.T.');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_space.php"><span><?=gettext_gen2('Space Used');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_mount.php"><span><?=gettext_gen2('Mounts');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_raid.php"><span><?=gettext_gen2('Software RAID');?></span></a></li>
	</ul></td></tr>
	<tr><td class="tabnavtbl"><ul id="tabnav2">
		<li class="tabinact"><a href="diag_infos_iscsi.php"><span><?=gettext_gen2('iSCSI Initiator');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_ad.php"><span><?=gettext_gen2('MS Domain');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_samba.php"><span><?=gettext_gen2('CIFS/SMB');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_ftpd.php"><span><?=gettext_gen2('FTP');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_rsync_client.php"><span><?=gettext_gen2('RSYNC Client');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_swap.php"><span><?=gettext_gen2('Swap');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_sockets.php"><span><?=gettext_gen2('Sockets');?></span></a></li>
		<li class="tabact"><a href="diag_infos_ipmi.php" title="<?=gettext_gen2('Reload page');?>"><span><?=gettext_gen2('IPMI Stats');?></span></a></li>
		<li class="tabinact"><a href="diag_infos_ups.php"><span><?=gettext_gen2('UPS');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" id="iform" name="iform">
	<?php if(empty($a_ipmi_sensor)):?>
		<?php html_remark2('sensor', gettext_gen2('System Message'), gettext_gen2('No IPMI sensor data available.'));?>
	<?php else:?>
		<table id="area_data_selection">
			<colgroup>
				<col style="width:11%"><!-- // Sensor -->
				<col style="width:12%"><!-- // Reading -->
				<col style="width:5%"><!-- // Status-->
				<col style="width:12%"><!-- // Lower Non-Recoverable [4] -->
				<col style="width:12%"><!-- // Upper Non-Recoverable [9] -->
				<col style="width:12%"><!-- // Lower Non-Critical [6] -->
				<col style="width:12%"><!-- // Upper Non-Critical [7] -->
				<col style="width:12%"><!-- // Lower Critical [5] -->
				<col style="width:12%"><!-- // Upper Critical [8] -->
			</colgroup>
			<thead>
				<?php html_titleline2(gettext_gen2('IPMI Sensor'), 9);?>
				<tr>
					<td class="lhelc" colspan="3">&nbsp;</th>
					<td class="lhelc" colspan="2"><?=gettext_gen2('Non-Recoverable');?></td>
					<td class="lhelc" colspan="2"><?=gettext_gen2('Non-Critical');?></td>
					<td class="lhebc" colspan="2"><?=gettext_gen2('Critical');?></td>
				</tr>
				<tr>
					<td class="lhell"><?=gettext_gen2('Sensor');?></td>
					<td class="lhell"><?=gettext_gen2('Reading');?></td>
					<td class="lhell"><?=gettext_gen2('Status');?></td>
					<td class="lhell"><?=gettext_gen2('Lower');?></td>
					<td class="lhell"><?=gettext_gen2('Upper');?></td>
					<td class="lhell"><?=gettext_gen2('Lower');?></td>
					<td class="lhell"><?=gettext_gen2('Upper');?></td>
					<td class="lhell"><?=gettext_gen2('Lower');?></td>
					<td class="lhebl"><?=gettext_gen2('Upper');?></td>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
				<?php foreach ($a_ipmi_sensor as $r_ipmi_sensor):?>
					<tr>
						<td class="lcell"><?=htmlspecialchars($r_ipmi_sensor[0]);?>&nbsp;</td>
						<td class="lcell"><?=htmlspecialchars($r_ipmi_sensor[1] . ' ' . $r_ipmi_sensor[2]);?></td>
						<td class="lcell"><?=htmlspecialchars($r_ipmi_sensor[3]);?>&nbsp;</td>
						<td class="lcelr"><?=htmlspecialchars($r_ipmi_sensor[4]);?>&nbsp;</td>
						<td class="lcelr"><?=htmlspecialchars($r_ipmi_sensor[9]);?>&nbsp;</td>
						<td class="lcelr"><?=htmlspecialchars($r_ipmi_sensor[6]);?>&nbsp;</td>
						<td class="lcelr"><?=htmlspecialchars($r_ipmi_sensor[7]);?>&nbsp;</td>
						<td class="lcelr"><?=htmlspecialchars($r_ipmi_sensor[5]);?>&nbsp;</td>
						<td class="lcebr"><?=htmlspecialchars($r_ipmi_sensor[8]);?>&nbsp;</td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>
	<?php if(empty($a_ipmi_fru)):?>
		<?php html_remark2('sensor', gettext_gen2('System Message'), gettext_gen2('No IPMI FRU data available.'));?>
	<?php else:?>
		<table id="area_data_settings">
			<colgroup>
				<col id="area_data_settings_col_tag">
				<col id="area_data_settings_col_data">
			</colgroup>
			<thead>
				<?php
				html_separator2();
				html_titleline2(gettext_gen2('IPMI FRU'), 2);
				?>
				<tr>
					<td class="lhell"><?=gettext_gen2('Tag');?></td>
					<td class="lhebl"><?=gettext_gen2('Value');?></td>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
				<?php foreach ($a_ipmi_fru as $r_ipmi_fru):?>
					<tr>
						<td class="lcell"><?=htmlspecialchars($r_ipmi_fru[0]);?>&nbsp;</td>
						<td class="lcebl"><?=htmlspecialchars($r_ipmi_fru[1]);?>&nbsp;</td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=gettext_gen2('Refresh');?>"/>
	</div>
	<?php include("formend.inc");?>
</form></td></tr></tbody></table>
<?php include("fend.inc");?>
