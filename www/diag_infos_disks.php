<?php
/*
	diag_infos_disks.php

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

global $config_disks;
// Get all physical disks.
$a_phy_disk = array_merge((array)get_physical_disks_list());
$pgtitle = [gtext('Diagnostics'),gtext('Information'),gtext('Disks')];
$a_colwidth = ['5%','12%','11%','8%','10%','7%','7%','8%','5%','14%','7%','6%'];
$n_colwidth = count($a_colwidth);
$document = new_page($pgtitle);
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add tab navigation
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('diag_infos_disks.php',gtext('Disks'),gtext('Reload page'),true)->
			ins_tabnav_record('diag_infos_disks_info.php',gtext('Disks (Info)'))->
			ins_tabnav_record('diag_infos_part.php',gtext('Partitions'))->
			ins_tabnav_record('diag_infos_smart.php',gtext('S.M.A.R.T.'))->
			ins_tabnav_record('diag_infos_space.php',gtext('Space Used'))->
			ins_tabnav_record('diag_infos_swap.php',gtext('Swap'))->
			ins_tabnav_record('diag_infos_mount.php',gtext('Mounts'))->
			ins_tabnav_record('diag_infos_raid.php',gtext('Software RAID'))->
			ins_tabnav_record('diag_infos_iscsi.php',gtext('iSCSI Initiator'))->
			ins_tabnav_record('diag_infos_ad.php',gtext('MS Domain'))->
			ins_tabnav_record('diag_infos_samba.php',gtext('CIFS/SMB'))->
			ins_tabnav_record('diag_infos_ftpd.php',gtext('FTP'))->
			ins_tabnav_record('diag_infos_rsync_client.php',gtext('RSYNC Client'))->
			ins_tabnav_record('diag_infos_netstat.php',gtext('Netstat'))->
			ins_tabnav_record('diag_infos_sockets.php',gtext('Sockets'))->
			ins_tabnav_record('diag_infos_ipmi.php',gtext('IPMI Stats'))->
			ins_tabnav_record('diag_infos_ups.php',gtext('UPS'));
//	create data area
$content = $pagecontent->add_area_data();
$tbody = $content->
	add_table_data_selection()->
		ins_colgroup_with_styles('width',$a_colwidth)->
		push()->addTHEAD()->
			ins_titleline(gtext('Detected Disks'),$n_colwidth)->
			addTR()->
				insTHwC('lhell',gtext('Device'))->
				insTHwC('lhell',gtext('Device Model'))->
				insTHwC('lhell',gtext("Description"))->
				insTHwC('lhell',gtext('Size'))->
				insTHwC('lhell',gtext('Serial Number'))->
				insTHwC('lhell',gtext('Rotation Rate'))->
				insTHwC('lhell',gtext('Transfer Rate'))->
				insTHwC('lhell',gtext('S.M.A.R.T.'))->
				insTHwC('lhell',gtext('Controller'))->
				insTHwC('lhell',gtext('Controller Model'))->
				insTHwC('lhell',gtext('Temperature'))->
				insTHwC('lhebl',gtext('Status'))->
		pop()->addTBODY();
foreach($a_phy_disk as $disk):
	$disk['desc'] = $config_disks[$disk['devicespecialfile']]['desc'];
	$temperature = (false !== ($device_temperature = system_get_device_temp($disk['devicespecialfile']))) ? htmlspecialchars(sprintf('%s °C',$device_temperature)) : gtext('n/a');
	if($disk['type'] == 'HAST'):
		$role = $a_phy_disk[$disk['name']]['role'];
		$status = sprintf("%s (%s)", (0 == disks_exists($disk['devicespecialfile'])) ? gtext('ONLINE') : gtext('MISSING'),$role);
		$disk['size'] = $a_phy_disk[$disk['name']]['size'];
	else:
		$status = (0 == disks_exists($disk['devicespecialfile'])) ? gtext('ONLINE') : gtext('MISSING');
	endif;
	$matches = preg_split('/[\s\,]+/',$disk['smart']['smart_support']);
	$smartsupport = '';
	if(isset($matches[0])):
		if(0 == strcasecmp($matches[0],'available')):
			$smartsupport .= gtext('Available');
			if(isset($matches[1])):
				if(0 == strcasecmp($matches[1],'enabled')):
					$smartsupport .= (', ' . gtext('Enabled'));
				elseif(0 ==  strcasecmp($matches[1],'disabled')):
					$smartsupport .= (', ' . gtext('Disabled'));
				endif;
			endif;
		elseif(0 == strcasecmp($matches[0],'unavailable')):
			$smartsupport .= gtext('Unavailable');
		endif;
	endif;
	$tbody->
		addTR()->
			insTDwC('lcell',htmlspecialchars($disk['name']))->
			insTDwC('lcell',htmlspecialchars($disk['model']))->
			insTDwC('lcell',empty($disk['desc']) ?  gtext('n/a') : htmlspecialchars($disk['desc']))->
			insTDwC('lcell',htmlspecialchars($disk['size']))->
			insTDwC('lcell',empty($disk['serial']) ? gtext('n/a') : htmlspecialchars($disk['serial']))->
			insTDwC('lcell',empty($disk['rotation_rate']) ? gtext('Unknown') : htmlspecialchars($disk['rotation_rate']))->
			insTDwC('lcell',empty($disk['transfer_rate']) ? gtext('n/a') : htmlspecialchars($disk['transfer_rate']))->
			insTDwC('lcell',$smartsupport)->
			insTDwC('lcell',htmlspecialchars($disk['controller'] . $disk['controller_id']))->
			insTDwC('lcell',htmlspecialchars($disk['controller_desc']))->
			insTDwC('lcell',$temperature)->
			insTDwC('lcebld',$status);
endforeach;
$document->render();
?>
