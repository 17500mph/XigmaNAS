<?php
/*
	diag_infos_part.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
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
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS, either expressed or implied.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';

$a_disk = get_physical_disks_list();
$pgtitle = [gtext('Diagnostics'),gtext('Information'),gtext('Partitions')];
include 'fbegin.inc';
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('diag_infos_disks.php',gettext('Disks'))->
			ins_tabnav_record('diag_infos_disks_info.php',gettext('Disks (Info)'))->
			ins_tabnav_record('diag_infos_part.php',gettext('Partitions'),gettext('Reload page'),true)->
			ins_tabnav_record('diag_infos_smart.php',gettext('S.M.A.R.T.'))->
			ins_tabnav_record('diag_infos_space.php',gettext('Space Used'))->
			ins_tabnav_record('diag_infos_swap.php',gettext('Swap'))->
			ins_tabnav_record('diag_infos_mount.php',gettext('Mounts'))->
			ins_tabnav_record('diag_infos_raid.php',gettext('Software RAID'))->
			ins_tabnav_record('diag_infos_iscsi.php',gettext('iSCSI Initiator'))->
			ins_tabnav_record('diag_infos_ad.php',gettext('MS Domain'))->
			ins_tabnav_record('diag_infos_samba.php',gettext('CIFS/SMB'))->
			ins_tabnav_record('diag_infos_ftpd.php',gettext('FTP'))->
			ins_tabnav_record('diag_infos_rsync_client.php',gettext('RSYNC Client'))->
			ins_tabnav_record('diag_infos_netstat.php',gettext('Netstat'))->
			ins_tabnav_record('diag_infos_sockets.php',gettext('Sockets'))->
			ins_tabnav_record('diag_infos_ipmi.php',gettext('IPMI Stats'))->
			ins_tabnav_record('diag_infos_ups.php',gettext('UPS'));
$document->render();
?>
<table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(empty($a_disk)):
		print_info_box(gtext('No disks found.'));
?>
		<table class="area_data_settings">
			<colgroup>
				<col class="area_data_settings_col_tag">
				<col class="area_data_settings_col_data">
			</colgroup>
			<thead>
<?php
				html_titleline2(gettext('Disk Partition Information'));
?>
			</thead>
		</table>
<?php
	else:
		$do_seperator = false;
		foreach($a_disk as $diskk => $diskv):
?>
			<table class="area_data_settings">
				<colgroup>
					<col class="area_data_settings_col_tag">
					<col class="area_data_settings_col_data">
				</colgroup>
				<thead>
<?php
					if($do_seperator):
						html_separator2();
					else:
						$do_seperator = true;
					endif;
					html_titleline2(sprintf(gettext('Device /dev/%s - %s'),$diskk,$diskv['desc']));
?>
				</thead>
				<tbody>
					<tr>
						<td class="celltag"><?=gtext('GEOM Partition Information');?></td>
						<td class="celldata">
<?php
							echo '<pre>';
							unset($rawdata);
							$cmd = sprintf('/sbin/gpart show %s',escapeshellarg($diskk));
							exec($cmd,$rawdata);
							$output = htmlspecialchars(implode("\n",$rawdata));
							if(preg_match('/\S/',$output)):
								echo $output;
							else:
								echo gtext('No partition information found.');
							endif;
							unset($rawdata);
							echo '</pre>';
?>
						</td>
					</tr>
					<tr>
						<td class="celltag"><?=gtext('Fdisk Partition Information');?></td>
						<td class="celldata">
<?php
							echo '<pre>';
							unset($rawdata);
							$cmd = sprintf('/sbin/fdisk %s',escapeshellarg($diskk));
							exec($cmd,$rawdata);
							$output = htmlspecialchars(implode("\n",$rawdata));
							if(preg_match('/\S/',$output)):
								echo $output;
							else:
								echo gtext('No fdisk information found.');
							endif;
							unset($rawdata);
							echo '</pre>';
?>
						</td>
					</tr>
				</tbody>
			</table>
<?php
		endforeach;
	endif;
?>
</td></tr></tbody></table>
<?php
include 'fend.inc';
?>