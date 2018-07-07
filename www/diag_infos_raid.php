<?php
/*
	diag_infos_raid.php

	Part of XigmaNAS (http://www.xigmanas.com).
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
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the XigmaNAS Project.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';

$sphere_array = ['concat','mirror','raid5','stripe','vinum'];
$pgtitle = [gtext('Diagnostics'),gtext('Information'),gtext('Software RAID')];
include 'fbegin.inc';
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('diag_infos_disks.php',gtext('Disks'))->
			ins_tabnav_record('diag_infos_disks_info.php',gtext('Disks (Info)'))->
			ins_tabnav_record('diag_infos_part.php',gtext('Partitions'))->
			ins_tabnav_record('diag_infos_smart.php',gtext('S.M.A.R.T.'))->
			ins_tabnav_record('diag_infos_space.php',gtext('Space Used'))->
			ins_tabnav_record('diag_infos_swap.php',gtext('Swap'))->
			ins_tabnav_record('diag_infos_mount.php',gtext('Mounts'))->
			ins_tabnav_record('diag_infos_raid.php',gtext('Software RAID'),gtext('Reload page'),true)->
			ins_tabnav_record('diag_infos_iscsi.php',gtext('iSCSI Initiator'))->
			ins_tabnav_record('diag_infos_ad.php',gtext('MS Domain'))->
			ins_tabnav_record('diag_infos_samba.php',gtext('CIFS/SMB'))->
			ins_tabnav_record('diag_infos_ftpd.php',gtext('FTP'))->
			ins_tabnav_record('diag_infos_rsync_client.php',gtext('RSYNC Client'))->
			ins_tabnav_record('diag_infos_netstat.php',gtext('Netstat'))->
			ins_tabnav_record('diag_infos_sockets.php',gtext('Sockets'))->
			ins_tabnav_record('diag_infos_ipmi.php',gtext('IPMI Stats'))->
			ins_tabnav_record('diag_infos_ups.php',gtext('UPS'));
$document->render();
?>
<table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	$do_seperator = false;
	foreach($sphere_array as $sphere_record):
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
				html_titleline2(sprintf('GEOM %s',$sphere_record));
?>
			</thead>
			<tbody>
				<tr>
					<td class="celltag"><?=gtext('Information');?></td>
					<td class="celldata">
						<pre><?php
							if(0 >= count(get_conf_disks_filtered_ex('class',sprintf('g%s',$sphere_record)))):
								echo gtext('n/a');
							else:
								unset ($rawdata);
								disks_geom_cmd($sphere_record,'list','',true,false,$rawdata);
								echo htmlspecialchars(implode("\n",$rawdata));
							endif;
						?></pre>
					</td>
				</tr>
			</tbody>
		</table>
<?php
	endforeach;
?>
</td></tr></tbody></table>
<?php
include 'fend.inc';
?>
