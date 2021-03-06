<?php
/*
	services_mariadb.php

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
require_once 'properties_services_mariadb.php';
require_once 'co_request_method.php';

function mariadb_sphere() {
	global $config;

	$sphere = new co_sphere_row('services_mariadb','php');
	$sphere->enadis(true);
	$sphere->grid = &array_make_branch($config,'mariadb');
	return $sphere;
}
//	get some environment variables
//	$mysql_user = rc_getenv_ex('mysql_user','mysql');
//	$mysql_group = rc_getenv_ex('mysql_group','mysql');
//	init properties and sphere
$cop = new mariadb_properties();
$sphere = &mariadb_sphere();
$a_referer = [
	$cop->get_enable(),
	$cop->get_homedir(),
	$cop->get_auxparam(),
	$cop->get_phrasecookieauth()
];
$input_errors = [];
//	determine request method
$rmo = new co_request_method();
$rmo->add('GET','edit',PAGE_MODE_EDIT);
$rmo->add('GET','view',PAGE_MODE_VIEW);
$rmo->add('POST','edit',PAGE_MODE_EDIT);
if($sphere->enadis()):
	$rmo->add('POST','enable',PAGE_MODE_VIEW);
	$rmo->add('POST','disable',PAGE_MODE_VIEW);
endif;
$rmo->add('POST','restart',PAGE_MODE_VIEW);
$rmo->add('POST','save',PAGE_MODE_POST);
$rmo->add('POST','view',PAGE_MODE_VIEW);
$rmo->add('SESSION',$sphere->get_basename(),PAGE_MODE_VIEW);
$rmo->set_default('GET','view',PAGE_MODE_VIEW);
list($page_method,$page_action,$page_mode) = $rmo->validate();
//	catch error code
switch($page_action):
	case $sphere->get_basename():
		$retval = filter_var($_SESSION[$sphere->get_basename()],FILTER_VALIDATE_INT,['options' => ['default' => 0]]);
		unset($_SESSION['submit']);
		unset($_SESSION[$sphere->get_basename()]);
		$savemsg = get_std_save_message($retval);
		if($retval !== 0):
			$page_action = 'edit';
			$page_mode = PAGE_MODE_EDIT;
		else:
			$page_action = 'view';
			$page_mode = PAGE_MODE_VIEW;
		endif;
		break;
endswitch;
//	validate
switch($page_action):
	case 'edit':
	case 'view':
	case 'disable':
	case 'enable':
	case 'restart':
		$source = $sphere->grid;
		foreach($a_referer as $referer):
			$name = $referer->get_name();
			switch($name):
				case 'auxparam':
					if(array_key_exists($name,$source)):
						if(is_array($source[$name])):
							$source[$name] = implode(PHP_EOL,$source[$name]);
						endif;
					endif;
					break;
			endswitch;
			$sphere->row[$name] = $referer->validate_array_element($source);
			if(is_null($sphere->row[$name])):
				if(array_key_exists($name,$source) && is_scalar($source[$name])): 
					switch($page_action):
						case 'enable':
							$input_errors[] = $referer->get_message_error();
							break;
					endswitch;
					$sphere->row[$name] = $source[$name];
				else:
					$sphere->row[$name] = $referer->get_defaultvalue();
				endif;
			endif;
		endforeach;
		break;
	case 'save':
		$source = $_POST;
		foreach($a_referer as $referer):
			$name = $referer->get_name();
			$sphere->row[$name] = $referer->validate_input();
			if(is_null($sphere->row[$name])):
				$input_errors[] = $referer->get_message_error();
				if(array_key_exists($name,$source) && is_scalar($source[$name])): 
					$sphere->row[$name] = $source[$name];
				else:
					$sphere->row[$name] = $referer->get_defaultvalue();
				endif;
			endif;
		endforeach;
		break;
endswitch;
//	reclassify
switch($page_action):
	case 'enable':
		$name = $cop->get_enable()->get_name();
		if($sphere->row[$name]):
			$page_action = 'view';
			$page_mode = PAGE_MODE_VIEW;
		else:
			$sphere->row[$name] = true;
			$page_action = 'save';
			$page_mode = PAGE_MODE_POST;
		endif;
		break;
	case 'disable':
		$name = $cop->get_enable()->get_name();
		if($sphere->row[$name]):
			$sphere->row[$name] = false;
			$page_action = 'save';
			$page_mode = PAGE_MODE_POST;
		else:
			$page_action = 'view';
			$page_mode = PAGE_MODE_VIEW;
		endif;
		break;
endswitch;
//	save configuration
switch($page_action):
	case 'restart':
		$retval = 0;
		config_lock();
		$retval |= rc_restart_service_if_running_and_enabled('mysqldb');
		config_unlock();
		$_SESSION['submit'] = $sphere->get_basename();
		$_SESSION[$sphere->get_basename()] = $retval;
		header($sphere->get_location());
		exit;
		break;
	case 'save':
		if(empty($input_errors)):
			foreach($a_referer as $referer):
				$name = $referer->get_name();
				switch($name):
					case 'auxparam':
						$auxparam_grid = [];
						foreach(explode(PHP_EOL,$sphere->row[$name]) as $auxparam_row):
							$auxparam_grid[] = trim($auxparam_row,"\t\n\r");
						endforeach;
						$sphere->row[$name] = $auxparam_grid;
						break;
				endswitch;
				$sphere->grid[$name] = $sphere->row[$name];
			endforeach;
			$usermysqlhomedir = $sphere->row[$cop->get_homedir()->get_name()] ?? '';
			if(1 === preg_match('/\S/',$usermysqlhomedir) && file_exists($usermysqlhomedir)):
			else:
				$usermysqlhomedir = '/nonexistent';
			endif;
			// update user mysql home dir
			$extraoptions_changed = false;
			$users = &array_make_branch($config,'system','usermanagement','user');
			$index = array_search_ex('mysql',$users,'name');
			if($index !== false):
				$extraoptions_new = sprintf('-c "MySQL user" -d "%s" -s /usr/sbin/nologin',$usermysqlhomedir);
				$extraoptions_current = $users[$index]['extraoptions'] ?? '';
				$extraoptions_changed = $extraoptions_current !== $extraoptions_new;
				if($extraoptions_changed):
					$users[$index]['extraoptions'] = $extraoptions_new;
				endif;
			endif;
			write_config();
			$retval = 0;
			config_lock();
			if($extraoptions_changed):
				$retval |= rc_exec_service('userdb');
			endif;
			$retval |= rc_update_service('mysqldb');
			config_unlock();
			$_SESSION['submit'] = $sphere->get_basename();
			$_SESSION[$sphere->get_basename()] = $retval;
			header($sphere->get_location());
			exit;
		else:
			$page_mode = PAGE_MODE_EDIT;
		endif;
		break;
endswitch;
//	determine final page mode and calculate readonly flag
list($page_mode,$is_readonly) = calc_skipviewmode($page_mode);
$is_enabled = $sphere->row[$cop->get_enable()->get_name()];
//	create document
$pgtitle = [gettext('Services'),gettext('MariaDB'),gettext('Settings')];
$document = new_page($pgtitle,$sphere->get_scriptname());
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add tab navigation
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('services_mariadb.php',gettext('Global Settings'),gettext('Reload page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	ins_input_errors($input_errors)->
	ins_info_box($savemsg);
//	add content
$n_auxparam_rows = min(64,max(5,1 + substr_count($sphere->row[$cop->get_auxparam()->get_name()],PHP_EOL)));
/* @var $content co_DOMElement */
$content->
	add_table_data_settings()->
		ins_colgroup_data_settings()->
		push()->
		addTHEAD()->
			c2_titleline_with_checkbox($cop->get_enable(),$is_enabled,false,$is_readonly,gettext('MariaDB'))->
		last()->
		addTBODY()->
			c2_filechooser($cop->get_homedir(),$sphere->row[$cop->get_homedir()->get_name()],true,$is_readonly)->
			c2_textarea($cop->get_auxparam(),$sphere->row[$cop->get_auxparam()->get_name()],false,$is_readonly,60,$n_auxparam_rows)->
		pop()->
		addTFOOT()->
			c2_separator();
$tbody = $content->
	add_table_data_settings()->
		ins_colgroup_data_settings()->
		push()->
		addTHEAD()->
			c2_titleline(sprintf('%s (%s)',gettext('Administrative WebGUI'),gettext('phpMyAdmin')))->
		pop()->
		addTBODY();
$tbody->c2_input_password($cop->get_phrasecookieauth(),$sphere->row[$cop->get_phrasecookieauth()->get_name()],false,$is_readonly);
if($is_enabled):
	$tbody->c2_textinfo('url1',gettext('URL'),sprintf('<a href="%1$s" id="a_url1" target="_blank">%1$s</a>',htmlspecialchars('/phpMyAdmin/index.php')));
endif;
//	add buttons
switch($page_mode):
	case PAGE_MODE_VIEW:
		$document->
			add_area_buttons()->
				ins_button_edit()->
				ins_button_enadis(!$is_enabled)->
				ins_button_restart($is_enabled);
		break;
	case PAGE_MODE_EDIT:
		$document->
			add_area_buttons()->
				ins_button_save()->
				ins_button_cancel();
		break;
endswitch;
//	additional javascript code
$js_code = [];
$js_code[PAGE_MODE_VIEW] = '';
$js_code[PAGE_MODE_EDIT] = '';
//	additional javascript code
$js_on_load = [];
$js_on_load[PAGE_MODE_EDIT] = '';
$js_on_load[PAGE_MODE_VIEW] = '';
//	additional javascript code
$js_document_ready = [];
$js_document_ready[PAGE_MODE_EDIT] = '';
$js_document_ready[PAGE_MODE_VIEW] = '';
//	add additional javascript code
$body->addJavaScript($js_code[$page_mode]);
$body->add_js_on_load($js_on_load[$page_mode]);
$body->add_js_document_ready($js_document_ready[$page_mode]);
//	showtime
$document->render();
