<?php
/*
	interfaces_lagg_edit.php

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
require 'auth.inc';
require 'guiconfig.inc';
require 'co_sphere.php';

function interfaces_lagg_edit_get_sphere() {
	global $config;
	$sphere = new co_sphere_row('interfaces_lagg_edit','php');
	$sphere->row_identifier('uuid');
	$sphere->parent->basename('interfaces_lagg','php');
	$sphere->row_default = [
		'enable' => true,
		'protected' => false,
		'laggproto' => 'failover',
		'laggport' => [],
		'desc' => ''
	];
	$sphere->grid = &array_make_branch($config,'vinterfaces','lagg');
	if(!empty($sphere->grid)):
		array_sort_key($sphere->grid,'if');
	endif;
	return $sphere;
}
$sphere = &interfaces_lagg_edit_get_sphere();
$l_lagg_protocol = [
	'failover' => gtext('Failover'),
	'lacp' => gtext('LACP (Link Aggregation Control Protocol)'),
	'loadbalance' => gtext('Loadbalance'),
	'roundrobin' => gtext('Roundrobin'),
	'none' => gtext('None')
];
$input_errors = [];
$disable_button_save = false;
$mode_page = PAGE_MODE_ADD;
if($_POST && isset($_POST['submit']) && is_string($_POST['submit'])):
	switch($_POST['submit']):
		case 'add':
			break;
		case 'cancel':
			header($sphere->parent->header());
			exit;
			break;
		case 'clone':
			$mode_page = PAGE_MODE_CLONE;
			break;
		case 'save':
			if(isset($_POST[$sphere->row_identifier()]) && is_string($_POST[$sphere->row_identifier()]) && is_uuid_v4($_POST[$sphere->row_identifier()])):
				$mode_page = PAGE_MODE_POST;
			else:
				header($sphere->parent->header());
				exit;
			endif;
			break;
		default:
			if(is_uuid_v4($_POST['submit'])):
				$mode_page = PAGE_MODE_EDIT;
			else:
				header($sphere->parent->header());
				exit;
			endif;
			break;
	endswitch;
endif;
switch($mode_page):
	case PAGE_MODE_ADD:
		$sphere->row[$sphere->row_identifier()] = uuid();
		break;
	case PAGE_MODE_CLONE:
		$sphere->row[$sphere->row_identifier()] = uuid();
		break;
	case PAGE_MODE_EDIT:
		$sphere->row[$sphere->row_identifier()] = $_POST['submit'];
		break;
	case PAGE_MODE_POST:
		$sphere->row[$sphere->row_identifier()] = $_POST[$sphere->row_identifier()];
		break;
endswitch;
$sphere->row_id = array_search_ex($sphere->row[$sphere->row_identifier()],$sphere->grid,$sphere->row_identifier());
$isrecordnew = (false === $sphere->row_id);
switch($mode_page):
	case PAGE_MODE_ADD:
		if(!$isrecordnew): // add cannot have an uuid in config
			header($sphere->parent->header());
			exit;
		endif;	
		$sphere->row['enable'] = $sphere->row_default['enable'];
		$sphere->row['protected'] = $sphere->row_default['protected'];
		$interface_id = 0;
		$interface_format = 'lagg%d';
		do {
			$interface_name = sprintf($interface_format,$interface_id);
			$interface_id++;
		} while(false !== array_search_ex($interface_name,$sphere->grid,'if'));
		$sphere->row['if'] = $interface_name;
		$sphere->row['laggproto'] = $sphere->row_default['laggproto'];
		$sphere->row['laggport'] = $sphere->row_default['laggport'];
		$sphere->row['desc'] = $sphere->row_default['desc'];
		break;
	case PAGE_MODE_CLONE: // clone cannot have an uuid in config
		if(!$isrecordnew):
			header($sphere->parent->header());
			exit;
		endif;
		$sphere->row['enable'] = filter_has_var(INPUT_POST,'enable') ? filter_input(INPUT_POST,'enable',FILTER_VALIDATE_BOOLEAN) : $sphere->row_default['enable'];
		$sphere->row['protected'] = filter_has_var(INPUT_POST,'protected') ? filter_input(INPUT_POST,'protected',FILTER_VALIDATE_BOOLEAN) : $sphere->row_default['protected'];
		$interface_id = 0;
		$interface_format = 'lagg%d';
		do {
			$interface_name = sprintf($interface_format,$interface_id);
			$interface_id++;
		} while(false !== array_search_ex($interface_name,$sphere->grid,'if'));
		$sphere->row['if'] = $interface_name;
		$sphere->row['laggproto'] = filter_has_var(INPUT_POST,'laggproto') ? filter_input(INPUT_POST,'laggproto') : $sphere->row_default['laggproto'];
		$sphere->row['laggport'] = filter_has_var(INPUT_POST,'laggport') ? filter_input(INPUT_POST,'laggport',FILTER_DEFAULT,FILTER_FORCE_ARRAY) : $sphere->row_default['laggport'];
		$sphere->row['desc'] = filter_has_var(INPUT_POST,'desc') ? filter_input(INPUT_POST,'desc') : $sphere->row_default['desc'];
		// adjust page mode information
		$mode_page = PAGE_MODE_ADD;
		break;
	case PAGE_MODE_EDIT:
		if($isrecordnew): // edit relies on an existing record
			header($sphere->parent->header());
			exit;
		endif;
		$sphere->row['enable'] = !empty($sphere->grid[$sphere->row_id]['enable']);
		$sphere->row['protected'] = !empty($sphere->grid[$sphere->row_id]['protected']);
		$sphere->row['if'] = $sphere->grid[$sphere->row_id]['if'];
		$sphere->row['laggproto'] = $sphere->grid[$sphere->row_id]['laggproto'];
		$sphere->row['laggport'] = $sphere->grid[$sphere->row_id]['laggport'];
		$sphere->row['desc'] = $sphere->grid[$sphere->row_id]['desc'];
		break;
	case PAGE_MODE_POST:
		$sphere->row['enable'] = filter_has_var(INPUT_POST,'enable') ? filter_input(INPUT_POST,'enable',FILTER_VALIDATE_BOOLEAN) : $sphere->row_default['enable'];
		$sphere->row['protected'] = filter_has_var(INPUT_POST,'protected') ? filter_input(INPUT_POST,'protected',FILTER_VALIDATE_BOOLEAN) : $sphere->row_default['protected'];
		$sphere->row['if'] = filter_has_var(INPUT_POST,'if') ? filter_input(INPUT_POST,'if') : '';
		$sphere->row['laggproto'] = filter_has_var(INPUT_POST,'laggproto') ? filter_input(INPUT_POST,'laggproto') : $sphere->row_default['laggproto'];
		$sphere->row['laggport'] = filter_has_var(INPUT_POST,'laggport') ? filter_input(INPUT_POST,'laggport',FILTER_DEFAULT,FILTER_FORCE_ARRAY) : $sphere->row_default['laggport'];
		$sphere->row['desc'] = filter_has_var(INPUT_POST,'desc') ? filter_input(INPUT_POST,'desc') : $sphere->row_default['desc'];
		$reqdfields = ['laggproto'];
		$reqdfieldsn = [gtext('Aggregation Protocol')];
		$reqdfieldst = ['string'];
		do_input_validation($sphere->row,$reqdfields,$reqdfieldsn,$input_errors);
		do_input_validation_type($sphere->row,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
		if(empty($input_errors)):
			if(!preg_match('/^lagg[\d]+$/',$sphere->row['if'])):
				$input_errors[] = gtext('The name of the interface is wrong.');
				$disable_button_save = true;
			endif;
		endif;
		if(empty($input_errors)):
			if(!array_key_exists($sphere->row['laggproto'],$l_lagg_protocol)):
				$input_errors[] = gtext('The LAGG protocol is invalid.');
			endif;
		endif;
		if(empty($input_errors)):
			if(count($sphere->row['laggport']) < 1):
				$input_errors[] = gtext('At least one interface must be selected.');
			endif;
		endif;
		if(empty($input_errors)):
			if($isrecordnew):
				if(false !== array_search_ex($sphere->row['if'],$sphere->grid,'if')):
					$input_errors[] = gtext('The LAGG interface cannot be added because the interface name already exists.');
					$disable_button_save = true;
				endif;
			endif;
		endif;
		if(empty($input_errors)):
			$sphere->upsert();
			write_config();
			touch($d_sysrebootreqd_path);
			header($sphere->parent->header());
			exit;
		endif;
		break;
endswitch;
$l_port = [];
foreach(get_interface_list() as $interface_name => $interface_detail):
	if(preg_match('/lagg/i',$interface_name)): // skip lagg interfaces
		continue;
	endif;
	foreach($sphere->grid as $row): // test all lagg
		if(($row['if'] !== $sphere->row['if']) && in_array($interface_name,$row['laggport'])): // exclude interfaces in laggs other than self
			continue 2;
		endif;
	endforeach;
	$l_port[$interface_name] = htmlspecialchars(sprintf('%s (%s)',$interface_name,$interface_detail['mac']));
endforeach;
$pgtitle = [gtext('Network'),gtext('Interface Management'),gtext('LAGG'),$isrecordnew ? gtext('Add') : gtext('Modify')];
include 'fbegin.inc';
$sphere->doj();
?>
<table id="area_navigator">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="interfaces_assign.php"><span><?=gtext('Management');?></span></a></li>
		<li class="tabinact"><a href="interfaces_wlan.php"><span><?=gtext('WLAN');?></span></a></li>
		<li class="tabinact"><a href="interfaces_vlan.php"><span><?=gtext('VLAN');?></span></a></li>
		<li class="tabact"><a href="interfaces_lagg.php" title="<?=gtext('Reload page');?>"><span><?=gtext('LAGG');?></span></a></li>
		<li class="tabinact"><a href="interfaces_bridge.php"><span><?=gtext('Bridge');?></span></a></li>
		<li class="tabinact"><a href="interfaces_carp.php"><span><?=gtext('CARP');?></span></a></li>
	</ul></td></tr>
</table>
<form action="<?=$sphere->scriptname();?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('LAGG Settings'));
?>
		</thead>
		<tbody>
<?php
			html_inputbox2('if',gtext('Interface'),$sphere->row['if'],'',true,5,true);
			html_combobox2('laggproto',gtext('Aggregation Protocol'),$sphere->row['laggproto'],$l_lagg_protocol,'',true);
			html_listbox2('laggport',gtext('Ports'),$sphere->row['laggport'],$l_port,gtext('Note: Ctrl-click (or command-click on the Mac) to select multiple entries.'),true);
			html_inputbox2('desc',gtext('Description'),$sphere->row['desc'],gtext('You may enter a description here for your reference.'),false,40);
?>
		</tbody>
	</table>
	<div id="submit">
<?php
		echo $sphere->html_button('save',$isrecordnew ? gtext('Add') : gtext('Save'),NULL,$disable_button_save);
		if(empty($input_errors) && !$isrecordnew):
			echo $sphere->html_button('clone',gtext('Clone'));
		endif;
		echo $sphere->html_button('cancel',gtext('Cancel'));
?>
		<input name="enable" type="hidden" value="<?=$sphere->row['enable'];?>"/>
		<input name="if" type="hidden" value="<?=$sphere->row['if'];?>"/>
		<input name="<?=$sphere->row_identifier();?>" type="hidden" value="<?=$sphere->row[$sphere->row_identifier()];?>"/>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
?>
