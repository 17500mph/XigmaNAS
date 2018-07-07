<?php
/*
	status_disks.php

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
require_once 'co_sphere.php';

function get_sphere_status_disks() {
	global $config;
	$sphere = new co_sphere_row('status_disks','php');
	return $sphere;
}
function status_disks_render($root = NULL) {
	global $config;

	if(isset($root)):
		$is_DOM = true;
	else:
		$is_DOM = false;
		$root = new co_DOMDocument();
	endif;
	$pconfig = [];
	$pconfig['temp_info'] = $config['smartd']['temp']['info'] ?? 0;
	$pconfig['temp_crit'] = $config['smartd']['temp']['crit'] ?? 0;
	$a_phy_hast = array_merge((array)get_hast_disks_list());
	$a_disk_conf = &array_make_branch($config,'disks','disk');
	if(empty($a_disk_conf)):
	else:
		array_sort_key($a_disk_conf,'name');
	endif;
	$raidstatus = get_sraid_disks_list();
	foreach($a_disk_conf as $disk):
		$iostat_value = system_get_device_iostat($disk['name']);
		$iostat_available = (false !== $iostat_value);
		if($iostat_available):
			$gt_iostat = htmlspecialchars(sprintf("%s KiB/t, %s tps, %s MiB/s",$iostat_value['kpt'],$iostat_value['tps'],$iostat_value['mps']));
		else:
			$gt_iostat = gtext('n/a');
		endif;
		$temp_value = system_get_device_temp($disk['devicespecialfile']);
		$temp_available = (false !== $temp_value);
		if($temp_available):
			$gt_temp = htmlspecialchars(sprintf("%s °C",$temp_value));
		endif;
		$gt_name = htmlspecialchars($disk['name']);
		if($disk['type'] == 'HAST'):
			$role = $a_phy_hast[$disk['name']]['role'];
			$gt_size = htmlspecialchars($a_phy_hast[$disk['name']]['size']);
			$gt_status = htmlspecialchars(sprintf("%s (%s)", (0 == disks_exists($disk['devicespecialfile'])) ? gtext('ONLINE') : gtext('MISSING'),$role));
		else:
			$gt_size = htmlspecialchars($disk['size']);
			$gt_status = (0 == disks_exists($disk['devicespecialfile'])) ? gtext('ONLINE') : gtext('MISSING');
		endif;
		$gt_model = htmlspecialchars($disk['model']);
		$gt_description = empty($disk['desc']) ? gtext('n/a') : htmlspecialchars($disk['desc']);
		$gt_serial = empty($disk['serial']) ? gtext('n/a') : htmlspecialchars($disk['serial']);
		$gt_fstype = empty($disk['fstype']) ? gtext('Unknown or unformatted') : htmlspecialchars(get_fstype_shortdesc($disk['fstype']));
		$tr = $root->addTR();
		$tr->
			insTDwC('lcell',$gt_name)->
			insTDwC('lcell',$gt_size)->
			insTDwC('lcell',$gt_model)->
			insTDwC('lcell',$gt_description)->
			insTDwC('lcell',$gt_serial)->
			insTDwC('lcell',$gt_fstype)->
			insTDwC('lcell',$gt_iostat);
		if($temp_available):
			if(!empty($pconfig['temp_crit']) && $temp_value >= $pconfig['temp_crit']):
				$tr->addTDwC('lcell')->addDIV(['class'=> 'errortext'],$gt_temp);
			elseif(!empty($pconfig['temp_info']) && $temp_value >= $pconfig['temp_info']):
				$tr->addTDwC('lcell')->addDIV(['class'=> 'warningtext'],$gt_temp);
			else:
				$tr->insTDwC('lcell',$gt_temp);
			endif;  
		else:
			$tr->insTDwC('lcell',gtext('n/a'));
		endif;
		$tr->insTDwC('lcebld',$gt_status);
	endforeach;
	foreach($raidstatus as $diskk => $diskv):
		$iostat_value = system_get_device_iostat($diskk);
		$iostat_available = (false !== $iostat_value);
		if($iostat_available):
			$gt_iostat = htmlspecialchars(sprintf("%s KiB/t, %s tps, %s MiB/s",$iostat_value['kpt'],$iostat_value['tps'],$iostat_value['mps']));
		else:
			$gt_iostat = gtext('n/a');
		endif;
		$temp_value = system_get_device_temp($disk['devicespecialfile']);
		$temp_available = (false !== $temp_value);
		if($temp_available):
			$gt_temp = htmlspecialchars(sprintf("%s °C",$temp_value));
		endif;
		$gt_name = htmlspecialchars($diskk);
		$gt_size = htmlspecialchars($diskv['size']);
		$gt_model = gtext('n/a');
		$gt_description = gtext('Software RAID');
		$gt_serial = gtext('n/a');
		$gt_fstype = empty($diskv['fstype']) ? gtext('UFS') : htmlspecialchars(get_fstype_shortdesc($diskv['fstype']));
		$gt_status = htmlspecialchars($diskv['state']);
		$tr = $root->addTR();
		$tr->
			insTDwC('lcell',$gt_name)->
			insTDwC('lcell',$gt_size)->
			insTDwC('lcell',$gt_model)->
			insTDwC('lcell',$gt_description)->
			insTDwC('lcell',$gt_serial)->
			insTDwC('lcell',$gt_fstype)->
			insTDwC('lcell',$gt_iostat);
		if($temp_available):
			if(!empty($pconfig['temp_crit']) && $temp_value >= $pconfig['temp_crit']):
				$tr->addTDwC('lcell')->addDIV(['class'=> 'errortext'],$gt_temp);
			elseif(!empty($pconfig['temp_info']) && $temp_value >= $pconfig['temp_info']):
				$tr->addTDwC('lcell')->addDIV(['class'=> 'warningtext'],$gt_temp);
			else:
				$tr->insTDwC('lcell',$gt_temp);
			endif;  
		else:
			$tr->insTDwC('lcell',gtext('n/a'));
		endif;
		$tr->insTDwC('lcebld',$gt_status);
	endforeach;
	if($is_DOM):
		return $root;
	else:
		return $root->get_html();
	endif;
}
if(is_ajax()):
	$status = status_disks_render();
	render_ajax($status);
endif;
$sphere = &get_sphere_status_disks();
$jcode = <<<EOJ
$(document).ready(function(){
	var gui = new GUI;
	gui.recall(15000, 15000, 'status_disks.php', null, function(data) {
		if ($('#area_refresh').length > 0) {
			$('#area_refresh').html(data.data);
			$('.area_data_selection').trigger('update');
		}
	});
});
EOJ;
$a_colwidth = ['5%','7%','15%','17%','13%','10%','18%','8%','7%'];
$n_colwidth = count($a_colwidth);
$document = new_page([gtext('Status'),gtext('Disks')],NULL,'tablesort');
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
$body->addJavaScript($jcode);
$content = $pagecontent->add_area_data();
$tbody_inner = $content->
	add_table_data_selection()->
		ins_colgroup_with_styles('width',$a_colwidth)->
		push()->addTHEAD()->
			ins_titleline(gtext('Status & Information'),$n_colwidth)->
			addTR()->
				insTHwC('lhell',gtext('Device'))->
				insTHwC('lhell',gtext('Size'))->
				insTHwC('lhell',gtext('Device Model'))->
				insTHwC('lhell',gtext('Description'))->
				insTHwC('lhell',gtext('Serial Number'))->
				insTHwC('lhell',gtext('Filesystem'))->
				insTHwC('lhell sorter-false parser-false',gtext('I/O Statistics'))->
				insTHwC('lhell',gtext('Temperature'))->
				insTHwC('lhebl',gtext('Status'))->
		pop()->addTBODY(['id' => 'area_refresh']);
status_disks_render($tbody_inner);
$document->render();
