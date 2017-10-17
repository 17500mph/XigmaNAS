<?php
/*
	smartmontools_umass_edit.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';
require_once 'properties_smartmontools_umass.php';

function get_sphere_smartmontools_umass_edit() {
	global $config;
	
//	sphere structure
	$sphere = new co_sphere_grid('smartmontools_umass_edit','php');
	$sphere->parent->basename('smartmontools_umass');
	$sphere->notifier('smartmontools_umass');
	$sphere->row_identifier('uuid');
	$sphere->enadis(false);
	$sphere->lock(false);
	$sphere->sym_add(gtext('Add Record'));
	$sphere->sym_mod(gtext('Edit Record'));
	$sphere->sym_del(gtext('Record is marked for deletion'));
	$sphere->sym_loc(gtext('Record is locked'));
	$sphere->sym_unl(gtext('Record is unlocked'));
	$sphere->cbm_delete(gtext('Delete Selected Records'));
	$sphere->cbm_delete_confirm(gtext('Do you want to delete selected records?'));
	$sphere->grid = &array_make_branch($config,'smartmontools','umass','param');
	return $sphere;
}
//	init properties and sphere
$property = new properties_smartmontools_umass();
$sphere = &get_sphere_smartmontools_umass_edit();
$prerequisites_ok = true;
//	init indicators
$input_errors = [];
$prerequisites_ok = true;
//	request method
$server_request_method_regexp = '/^(GET|POST)$/';
$server_request_method = filter_input(
	INPUT_SERVER,
	'REQUEST_METHOD',
	FILTER_VALIDATE_REGEXP,
	[
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => $server_request_method_regexp
]]);
//	resource id
$resource_id_filter_options = [
	'flags' => FILTER_REQUIRE_SCALAR,
	'options' => [
		'default' => NULL,
		'regexp' => '/^[\da-f]{4}([\da-f]{4}-){2}4[\da-f]{3}-[89ab][\da-f]{3}-[\da-f]{12}$/'
]];
//	determine page mode and validate resource id
switch($server_request_method):
	default: // unsupported request method
		$sphere->row[$sphere->row_identifier()] = NULL;
		break;
	case 'GET':
		$action = filter_input(
			INPUT_GET,
			'submit',
			FILTER_VALIDATE_REGEXP,
			[
				'flags' => FILTER_REQUIRE_SCALAR,
				'options' => [
					'default' => '',
					'regexp' => '/^(add|edit)$/'
		]]);
		switch($action):
			default: // unsupported action
				$sphere->row[$sphere->row_identifier()] = NULL;
				break;
			case 'add': // bring up a form with default values and let the user modify it
				$page_mode = PAGE_MODE_ADD;
				$sphere->row[$sphere->row_identifier()] = uuid();
				break;
			case 'edit': // modify the data of the provided resource id and let the user modify it
				$page_mode = PAGE_MODE_EDIT;
				$sphere->row[$sphere->row_identifier()] = filter_input(INPUT_GET,$sphere->row_identifier(),FILTER_VALIDATE_REGEXP,$resource_id_filter_options);
				break;
		endswitch;
		break;
	case 'POST':
		$action = filter_input(
			INPUT_POST,
			'submit',
			FILTER_VALIDATE_REGEXP,
			[
				'flags' => FILTER_REQUIRE_SCALAR,
				'options' => [
					'default' => '',
					'regexp' => '/^(add|cancel|edit|save)$/'
		]]);
		switch($action):
			default:  // unsupported action
				$sphere->row[$sphere->row_identifier()] = NULL;
				break;
			case 'add': // bring up a form with default values and let the user modify it
				$page_mode = PAGE_MODE_ADD;
				$sphere->row[$sphere->row_identifier()] = uuid();
				break;
			case 'cancel': // cancel - nothing to do
				$sphere->row[$sphere->row_identifier()] = NULL;
				break;
			case 'edit': // edit requires a resource id, get it from input and validate
				$page_mode = PAGE_MODE_EDIT;
				$sphere->row[$sphere->row_identifier()] = filter_input(INPUT_POST,$sphere->row_identifier(),FILTER_VALIDATE_REGEXP,$resource_id_filter_options);
				break;
			case 'save': // modify requires a resource id, get it from input and validate
				$page_mode = PAGE_MODE_POST;
				$sphere->row[$sphere->row_identifier()] = filter_input(INPUT_POST,$sphere->row_identifier(),FILTER_VALIDATE_REGEXP,$resource_id_filter_options);
				break;
		endswitch;
		break;
endswitch;
/*
 *	exit if $sphere->row[$sphere->row_identifier()] is NULL
 */
if(!isset($sphere->row[$sphere->row_identifier()])):
	header($sphere->parent->header());
	exit;
endif;
/*
 *	search resource id in sphere
 */
$sphere->row_id = array_search_ex($sphere->row[$sphere->row_identifier()],$sphere->grid,$sphere->row_identifier());
/*
 *	start determine record update mode
 */
$updatenotify_mode = updatenotify_get_mode($sphere->notifier(),$sphere->row[$sphere->row_identifier()]); // get updatenotify mode
$record_mode = RECORD_ERROR;
if(false === $sphere->row_id): // record does not exist in config
	if(in_array($page_mode,[PAGE_MODE_ADD,PAGE_MODE_POST],true)): // ADD or POST
		switch($updatenotify_mode):
			case UPDATENOTIFY_MODE_UNKNOWN:
				$record_mode = RECORD_NEW;
				break;
		endswitch;
	endif;
else: // record found in configuration
	if(in_array($page_mode,[PAGE_MODE_EDIT,PAGE_MODE_POST,PAGE_MODE_VIEW],true)): // EDIT or POST or VIEW
		switch($updatenotify_mode):
			case UPDATENOTIFY_MODE_NEW:
				$record_mode = RECORD_NEW_MODIFY;
				break;
			case UPDATENOTIFY_MODE_MODIFIED:
				$record_mode = RECORD_MODIFY;
				break;
			case UPDATENOTIFY_MODE_UNKNOWN:
				$record_mode = RECORD_MODIFY;
				break;
		endswitch;
	endif;
endif;
if(RECORD_ERROR === $record_mode): // oops, something went wrong
	header($sphere->parent->header());
	exit;
endif;
$isrecordnew = (RECORD_NEW === $record_mode);
$isrecordnewmodify = (RECORD_NEW_MODIFY === $record_mode);
$isrecordmodify = (RECORD_MODIFY === $record_mode);
$isrecordnewornewmodify = ($isrecordnew || $isrecordnewmodify);
/*
 *	end determine record update mode
 */
$a_referer = ['name','type','description'];
switch($page_mode):
	case PAGE_MODE_ADD:
		foreach($a_referer as $referer):
			$sphere->row[$referer] = $property->$referer->get_defaultvalue();
		endforeach;
		break;
	case PAGE_MODE_EDIT:
		foreach($a_referer as $referer):
			$sphere->row[$referer] = $property->$referer->validate_value($sphere->grid[$sphere->row_id][$referer]) ?? $property->$referer->get_defaultvalue();
		endforeach;
		break;
	case PAGE_MODE_POST:
		// apply post values that are applicable for all record modes
		foreach($a_referer as $referer):
			$sphere->row[$referer] = $property->$referer->validate_input();
		endforeach;
		foreach($a_referer as $referer):
			if(!isset($sphere->row[$referer])):
				$sphere->row[$referer] = $_POST[$referer] ?? $property->$referer->get_defaultvalue();
				$input_errors[] = $property->$referer->get_message_error();
			endif;
		endforeach;
		if($prerequisites_ok && empty($input_errors)):
			if($isrecordnew):
				$sphere->grid[] = $sphere->row;
				updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_NEW,$sphere->row[$sphere->row_identifier()]);
			else:
				foreach($sphere->row as $key => $value):
					$sphere->grid[$sphere->row_id][$key] = $value;
				endforeach;
				if(UPDATENOTIFY_MODE_UNKNOWN == $updatenotify_mode):
					updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->row[$sphere->row_identifier()]);
				endif;
			endif;
			write_config();
			header($sphere->parent->header()); // cleanup
			exit;
		endif;
		break;
endswitch;
$pgtitle = [gtext('Disks'),gtext('Management'),gtext('S.M.A.R.T.'),gtext('USB Mass Storage Devices'),($isrecordnew) ? gtext('Add') : gtext('Edit')];
$jcode = $sphere->doj(false);
$document = new_page($pgtitle,$sphere->scriptname());
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add additional javascript code
if(isset($jcode)):
	$body->addJavaScript($jcode);
endif;
//	add tab navigation
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			mount_tabnav_record('disks_manage.php',gtext('HDD Management'))->
			mount_tabnav_record('disks_init.php',gtext('HDD Format'))->
			mount_tabnav_record('disks_manage_smart.php',gtext('S.M.A.R.T.'),gtext('Reload Page'),true)->
			mount_tabnav_record('disks_manage_iscsi.php',gtext('iSCSI Initiator'))->
			parentNode->
		add_tabnav_lower()->
			mount_tabnav_record('disks_manage_smart.php',gtext('Settings'))->
			mount_tabnav_record('smartmontools_umass.php',gtext('USB Mass Storage Devices'),gtext('Reload Page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	mount_input_errors($input_errors)->
	mount_info_box($savemsg)->
	mount_error_box($errormsg);
if(file_exists($d_sysrebootreqd_path)):
	$content->mount_info_box(get_std_save_message(0));
endif;
$content->add_table_data_settings()->
	mount_colgroup_data_settings()->
		addTHEAD()->
			c2_titleline(gtext('Settings'))->
			parentNode->
		addTBODY()->
			c2_input_text($property->name,htmlspecialchars($sphere->row['name']),true,false)->
			c2_input_text($property->type,htmlspecialchars($sphere->row['type']),false,false)->
			c2_input_text($property->description,$sphere->row['description'],false,false)->
			addElement('input',['name' => $sphere->row_identifier(),'type' => 'hidden','value' => $sphere->get_row_identifier_value()]);
$buttons = $document->add_area_buttons();
if($isrecordnew):
	$buttons->mount_button_add();
else:
	$buttons->mount_button_save();
endif;
$buttons->mount_button_cancel();
$document->render();
?>
