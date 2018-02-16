<?php
/*
	disks_zfs_settings.php

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
require_once 'co_sphere.php';
require_once 'properties_disks_zfs_settings.php';

function get_sphere_disks_zfs_settings() {
	global $config;
	$sphere = new co_sphere_row('disks_zfs_settings','php');
	$sphere->grid = &array_make_branch($config,'zfs','settings');
	return $sphere;
}
//	init properties and sphere
$property = new properties_disks_zfs_settings();
$sphere = &get_sphere_disks_zfs_settings();
$input_errors = [];
$savemsg = '';
//	request method
$methods = ['GET','POST'];
$methods_regexp = sprintf('/^(%s)$/',implode('|',array_map(function($element) { return preg_quote($element,'/'); },$methods)));
$method = filter_input(INPUT_SERVER,'REQUEST_METHOD',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => $methods_regexp]]);
//	determine page mode
$page_mode = PAGE_MODE_VIEW;
switch($method):
	case 'POST':
		$actions = ['edit','save','cancel'];
		$actions_regexp = sprintf('/^(%s)$/',implode('|',array_map(function($element) { return preg_quote($element,'/'); },$actions)));
		$action = filter_input(INPUT_POST,'submit',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => '','regexp' => $actions_regexp]]);
		switch($action):
			case 'edit':
				$page_mode = PAGE_MODE_EDIT;
				break;
			case 'save':
				$page_mode = PAGE_MODE_POST;
				break;
		endswitch;
		break;
endswitch;
switch($page_mode):
	case PAGE_MODE_VIEW:
	case PAGE_MODE_EDIT:
		$a_referrer = [
			$property->showusedavail->get_name(),
		];
		foreach($a_referrer as $referrer):
			$sphere->row[$referrer] = $property->{$referrer}->validate_config($sphere->grid);
		endforeach;
		$referrer = $property->capacity_warning->get_name();
		$sphere->row[$referrer] = $property->{$referrer}->validate_array_element($sphere->grid);
		if(is_null($sphere->row[$referrer])):
			$input_errors[] = $property->{$referrer}->get_message_error();
			if(array_key_exists($referrer,$sphere->grid) && is_scalar($sphere->grid[$referrer])): 
				$sphere->row[$referrer] = $sphere->grid[$referrer];
			else:
				$sphere->row[$referrer] = $property->{$referrer}->get_defaultvalue();
			endif;
		endif;
		$referrer = $property->capacity_critical->get_name();
		$sphere->row[$referrer] = $property->{$referrer}->validate_array_element($sphere->grid);
		if(is_null($sphere->row[$referrer])):
			$input_errors[] = $property->{$referrer}->get_message_error();
			if(array_key_exists($referrer,$sphere->grid) && is_scalar($sphere->grid[$referrer])): 
				$sphere->row[$referrer] = $sphere->grid[$referrer];
			else:
				$sphere->row[$referrer] = $property->{$referrer}->get_defaultvalue();
			endif;
		endif;
		break;
	case PAGE_MODE_POST:
		$a_referrer = [
			$property->showusedavail->get_name(),
		];
		foreach($a_referrer as $referrer):
			$sphere->row[$referrer] = $property->{$referrer}->validate_input();
		endforeach;
		$referrer = $property->capacity_warning->get_name();
		$sphere->row[$referrer] = $property->{$referrer}->validate_input();
		if(is_null($sphere->row[$referrer])):
			$input_errors[] = $property->{$referrer}->get_message_error();
			if(array_key_exists($referrer,$_POST) && is_scalar($_POST[$referrer])): 
				$sphere->row[$referrer] = $_POST[$referrer];
			else:
				$sphere->row[$referrer] = $property->{$referrer}->get_defaultvalue();
			endif;
		endif;
		$referrer = $property->capacity_critical->get_name();
		$sphere->row[$referrer] = $property->{$referrer}->validate_input();
		if(is_null($sphere->row[$referrer])):
			$input_errors[] = $property->{$referrer}->get_message_error();
			if(array_key_exists($referrer,$_POST) && is_scalar($_POST[$referrer])): 
				$sphere->row[$referrer] = $_POST[$referrer];
			else:
				$sphere->row[$referrer] = $property->{$referrer}->get_defaultvalue();
			endif;
		endif;
		if(empty($input_errors)):
			$a_referrer = [
				$property->showusedavail->get_name(),
				$property->capacity_warning->get_name(),
				$property->capacity_critical->get_name(),
			];
			foreach($a_referrer as $referrer):
				$sphere->grid[$referrer] = $sphere->row[$referrer];
			endforeach;
			write_config();
			$retval = 0;
			$savemsg = get_std_save_message($retval);
			$page_mode = PAGE_MODE_VIEW;
		else:
			$page_mode = PAGE_MODE_EDIT;
		endif;
		break;
endswitch;
//	determine final page mode and calculate readonly flag
switch($page_mode):
	case PAGE_MODE_EDIT:
		$is_readonly = false;
		break;
	default:
		if(isset($config['system']['skipviewmode']) && (is_bool($config['system']['skipviewmode']) ? $config['system']['skipviewmode'] : true)):
			$page_mode = PAGE_MODE_EDIT;
			$is_readonly = false;
		else:
			$page_mode = PAGE_MODE_VIEW;
			$is_readonly = true;
		endif;
		break;
endswitch;
//	prepare additional javascript code
$jcode = [];
$jcode[PAGE_MODE_EDIT] = NULL;
$jcode[PAGE_MODE_VIEW] = NULL;
//	create document
$document = new_page([gtext('Disks'),gtext('ZFS'),gtext('Settings')],$sphere->scriptname());
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add additional javascript code
if(isset($jcode[$page_mode])):
	$body->addJavaScript($jcode[$page_mode]);
endif;
//	add tab navigation
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('disks_zfs_zpool.php',gtext('Pools'))->
			ins_tabnav_record('disks_zfs_dataset.php',gtext('Datasets'))->
			ins_tabnav_record('disks_zfs_volume.php',gtext('Volumes'))->
			ins_tabnav_record('disks_zfs_snapshot.php',gtext('Snapshots'))->
			ins_tabnav_record('disks_zfs_config.php',gtext('Configuration'))->
			ins_tabnav_record('disks_zfs_settings.php',gtext('Settings'),gtext('Reload page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	ins_input_errors($input_errors)->
	ins_info_box($savemsg);
//	add content
$content->
	add_table_data_settings()->
		push()->
		ins_colgroup_data_settings()->
		addTHEAD()->
			c2_titleline(gtext('ZFS Settings'))->
		pop()->
		addTBODY()->
			c2_checkbox($property->showusedavail,$sphere->row[$property->showusedavail->get_name()],false,$is_readonly);
$content->
	add_table_data_settings()->
		push()->
		ins_colgroup_data_settings()->
		addTHEAD()->
			c2_separator()->
			c2_titleline(gtext('Capacity Alert Thresholds'))->
		pop()->
		addTBODY()->
			c2_input_text($property->capacity_warning,htmlspecialchars($sphere->row[$property->capacity_warning->get_name()]),false,$is_readonly)->
			c2_input_text($property->capacity_critical,htmlspecialchars($sphere->row[$property->capacity_critical->get_name()]),false,$is_readonly);
//	add buttons
switch($page_mode):
	case PAGE_MODE_VIEW:
		$document->add_area_buttons()->ins_button_edit();
		break;
	case PAGE_MODE_EDIT:
		$document->add_area_buttons()->ins_button_save()->ins_button_cancel();
		break;
endswitch;
//	done
$document->render();
?>
