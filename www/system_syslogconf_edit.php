<?php
/*
	system_syslogconf_edit.php

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
require_once 'co_sphere.php';
require_once 'properties_syslogconf.php';
require_once 'co_request_method.php';

function system_syslogconf_edit_get_sphere() {
	global $config;

//	sphere structure
	$sphere = new co_sphere_row('system_syslogconf_edit','php');
	$sphere->parent->set_basename('system_syslogconf');
	$sphere->set_notifier('syslogconf');
	$sphere->set_row_identifier('uuid');
	$sphere->enadis(false);
	$sphere->lock(false);
	$sphere->grid = &array_make_branch($config,'system','syslogconf','param');
	return $sphere;
}
//	init properties and sphere
$cop = new properties_syslogconf_edit();
$sphere = &system_syslogconf_edit_get_sphere();
$rmo = new co_request_method();
$rmo->add('GET','add',PAGE_MODE_ADD);
$rmo->add('GET','edit',PAGE_MODE_EDIT);
$rmo->add('POST','add',PAGE_MODE_ADD);
$rmo->add('POST','cancel',PAGE_MODE_POST);
$rmo->add('POST','edit',PAGE_MODE_EDIT);
$rmo->add('POST','save',PAGE_MODE_POST);
$rmo->set_default('POST','cancel',PAGE_MODE_POST);
list($page_method,$page_action,$page_mode) = $rmo->validate();
//	init indicators
$input_errors = [];
$prerequisites_ok = true;
//	determine page mode and validate resource id
switch($page_method):
	case 'GET':
		switch($page_action):
			case 'add': // bring up a form with default values and let the user modify it
				$sphere->row[$sphere->get_row_identifier()] = $cop->{$sphere->get_row_identifier()}->get_defaultvalue();
				break;
			case 'edit': // modify the data of the provided resource id and let the user modify it
				$sphere->row[$sphere->get_row_identifier()] = $cop->{$sphere->get_row_identifier()}->validate_input(INPUT_GET);
				break;
		endswitch;
		break;
	case 'POST':
		switch($page_action):
			case 'add': // bring up a form with default values and let the user modify it
				$sphere->row[$sphere->get_row_identifier()] = $cop->{$sphere->get_row_identifier()}->get_defaultvalue();
				break;
			case 'cancel': // cancel - nothing to do
				$sphere->row[$sphere->get_row_identifier()] = NULL;
				break;
			case 'edit': // edit requires a resource id, get it from input and validate
				$sphere->row[$sphere->get_row_identifier()] = $cop->{$sphere->get_row_identifier()}->validate_input();
				break;
			case 'save': // modify requires a resource id, get it from input and validate
				$sphere->row[$sphere->get_row_identifier()] = $cop->{$sphere->get_row_identifier()}->validate_input();
				break;
		endswitch;
		break;
endswitch;
/*
 *	exit if $sphere->row[$sphere->row_identifier()] is NULL
 */
if(is_null($sphere->get_row_identifier_value())):
	header($sphere->parent->get_location());
	exit;
endif;
/*
 *	search resource id in sphere
 */
$sphere->row_id = array_search_ex($sphere->get_row_identifier_value(),$sphere->grid,$sphere->get_row_identifier());
/*
 *	start determine record update mode
 */
$updatenotify_mode = updatenotify_get_mode($sphere->get_notifier(),$sphere->get_row_identifier_value()); // get updatenotify mode
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
	header($sphere->parent->get_location());
	exit;
endif;
$isrecordnew = (RECORD_NEW === $record_mode);
$isrecordnewmodify = (RECORD_NEW_MODIFY === $record_mode);
$isrecordmodify = (RECORD_MODIFY === $record_mode);
$isrecordnewornewmodify = ($isrecordnew || $isrecordnewmodify);
/*
 *	end determine record update mode
 */
$a_referer = [
	$cop->get_enable(),
	$cop->get_facility(),
	$cop->get_level(),
	$cop->get_value(),
	$cop->get_comment()
];
switch($page_mode):
	case PAGE_MODE_ADD:
		foreach($a_referer as $referer):
			$sphere->row[$referer->get_name()] = $referer->get_defaultvalue();
		endforeach;
		break;
	case PAGE_MODE_EDIT:
		$source = $sphere->grid[$sphere->row_id];
		foreach($a_referer as $referer):
			$sphere->row[$referer->get_name()] = $referer->validate_config($source);
		endforeach;
		break;
	case PAGE_MODE_POST:
		// apply post values that are applicable for all record modes
		foreach($a_referer as $referer):
			$name = $referer->get_name();
			$sphere->row[$name] = $referer->validate_input();
			if(!isset($sphere->row[$name])):
				$sphere->row[$name] = $_POST[$name] ?? '';
				$input_errors[] = $referer->get_message_error();
			endif;
		endforeach;
		if($prerequisites_ok && empty($input_errors)):
			if($isrecordnew):
				$sphere->grid[] = $sphere->row;
				updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_NEW,$sphere->get_row_identifier_value());
			else:
				foreach($sphere->row as $key => $value):
					$sphere->grid[$sphere->row_id][$key] = $value;
				endforeach;
				if(UPDATENOTIFY_MODE_UNKNOWN == $updatenotify_mode):
					updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->get_row_identifier_value());
				endif;
			endif;
			write_config();
			header($sphere->parent->get_location()); // cleanup
			exit;
		endif;
		break;
endswitch;
$pgtitle = [gettext('System'),gettext('Advanced'),gettext('syslog.conf'),($isrecordnew) ? gettext('Add') : gettext('Edit')];
$jcode = NULL;
$document = new_page($pgtitle,$sphere->get_scriptname());
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
			ins_tabnav_record('system_advanced.php',gettext('Advanced'))->
			ins_tabnav_record('system_email.php',gettext('Email'))->
			ins_tabnav_record('system_email_reports.php',gettext('Email Reports'))->
			ins_tabnav_record('system_monitoring.php',gettext('Monitoring'))->
			ins_tabnav_record('system_swap.php',gettext('Swap'))->
			ins_tabnav_record('system_rc.php',gettext('Command Scripts'))->
			ins_tabnav_record('system_cron.php',gettext('Cron'))->
			ins_tabnav_record('system_loaderconf.php',gettext('loader.conf'))->
			ins_tabnav_record('system_rcconf.php',gettext('rc.conf'))->
			ins_tabnav_record('system_sysctl.php',gettext('sysctl.conf'))->
			ins_tabnav_record('system_syslogconf.php',gettext('syslog.conf'),gettext('Reload page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	ins_input_errors($input_errors)->
	ins_info_box($savemsg)->
	ins_error_box($errormsg);
if(file_exists($d_sysrebootreqd_path)):
	$content->ins_info_box(get_std_save_message(0));
endif;
$content->add_table_data_settings()->
	ins_colgroup_data_settings()->
	push()->
	addTHEAD()->
		c2_titleline_with_checkbox($cop->get_enable(),$sphere->row[$cop->get_enable()->get_name()],false,false,gettext('Configuration'))->
	pop()->
	addTBODY()->
		c2_input_text($cop->get_facility(),$sphere->row[$cop->get_facility()->get_name()],true,false)->
		c2_input_text($cop->get_level(),$sphere->row[$cop->get_level()->get_name()],false,false)->
		c2_input_text($cop->get_value(),$sphere->row[$cop->get_value()->get_name()],false,false)->
		c2_input_text($cop->get_comment(),$sphere->row[$cop->get_comment()->get_name()],false,false);
$buttons = $document->add_area_buttons();
if($isrecordnew):
	$buttons->ins_button_add();
else:
	$buttons->ins_button_save();
endif;
$buttons->ins_button_cancel();
$buttons->addElement('input',['name' => $sphere->get_row_identifier(),'type' => 'hidden','value' => $sphere->get_row_identifier_value()]);
$document->render();
