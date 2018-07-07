<?php
/*
	services_tftp.php

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
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS, either expressed or implied.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';
require_once 'properties_services_tftp.php';
require_once 'co_request_method.php';

function services_tftp_get_sphere() {
	global $config;
	$sphere = new co_sphere_settings('services_tftp','php');
	$sphere->row_default = [
		'enable' => false,
		'dir' => $g['media_path'],
		'allowfilecreation' => true,
		'port' => 69,
		'username' => 'nobody',
		'umask' => 0,
		'timeout' => 1000000,
		'maxblocksize' => 16384,
		'extraoptions' => ''
	];
	$sphere->grid = &array_make_branch($config,'tftpd');
	if(empty($sphere->grid)):
		$sphere->grid = $sphere->row_default;
		write_config();
		header($sphere->get_location());
		exit;
	endif;
	return $sphere;
}
$sphere = &services_tftp_get_sphere();
$cop = new properties_tftp();			
$input_errors = [];
$a_message = [];
//	determine request method
$rmo = new co_request_method();
$rmo->add('GET','edit',PAGE_MODE_EDIT);
$rmo->add('GET','view',PAGE_MODE_VIEW);
$rmo->add('POST','edit',PAGE_MODE_EDIT);
$rmo->add('POST','enable',PAGE_MODE_VIEW);
$rmo->add('POST','disable',PAGE_MODE_VIEW);
$rmo->add('POST','save',PAGE_MODE_POST);
$rmo->add('POST','view',PAGE_MODE_VIEW);
$rmo->add('SESSION',$sphere->get_basename(),PAGE_MODE_VIEW);
$rmo->set_default('GET','view',PAGE_MODE_VIEW);
list($page_method,$page_action,$page_mode) = $rmo->validate();
//	get configuration data, depending on the source
switch($page_mode):
	case PAGE_MODE_POST:
		$source = $_POST;
		break;
	default:
		$source = $sphere->grid;
		break;
endswitch;
$sphere->row['enable'] = isset($source['enable']) ? (is_bool($source['enable']) ? $source['enable'] : true) : false;
$sphere->row['dir'] = $source['dir'] ?? $sphere->row_default['dir'];
$sphere->row['allowfilecreation'] = isset($source['allowfilecreation']);
$sphere->row['port'] = $source['port'] ?? $sphere->row_default['port'];
$sphere->row['username'] = $source['username'] ?? $sphere->row_default['username'];
$sphere->row['umask'] = $source['umask'] ?? $sphere->row_default['umask'];
$sphere->row['timeout'] = $source['timeout'] ?? $sphere->row_default['timeout'];
$sphere->row['maxblocksize'] = $source['maxblocksize'] ?? $sphere->row_default['maxblocksize'];
$sphere->row['extraoptions'] = $source['extraoptions'] ?? $sphere->row_default['extraoptions'];
//	set defaults
if(preg_match('/\S/',$sphere->row['username'])):
else:
	$sphere->row['username'] = $sphere->row_default['username'];
endif;
//	process enable
switch($page_action):
	case 'enable':
		if($sphere->row['enable']):
			$page_mode = PAGE_MODE_VIEW;
			$page_action = 'view';
		else: // enable and run a full validation
			$sphere->row['enable'] = true;
			$page_action = 'save'; // continue with save procedure
		endif;
		break;
endswitch;
//	process save and disable
switch($page_action):
	case $sphere->get_basename():
		$retval = filter_var($_SESSION[$sphere->get_basename()],FILTER_VALIDATE_INT,['options' => ['default' => 0]]);
		unset($_SESSION['submit']);
		unset($_SESSION[$sphere->get_basename()]);
		$savemsg = get_std_save_message($retval);
		$page_mode = PAGE_MODE_VIEW;
//		$page_action = 'view';
		break;
	case 'save':
		// Input validation.
		$reqdfields = ['dir'];
		$reqdfieldsn = [gtext('Directory')];
		$reqdfieldst = ['string'];
		do_input_validation($sphere->row,$reqdfields,$reqdfieldsn,$input_errors);
		$reqdfields = array_merge($reqdfields,['port','umask','timeout','maxblocksize']);
		$reqdfieldsn = array_merge($reqdfieldsn,[gtext('Port'),gtext('Umask'),gtext('Timeout'),gtext('Max. Block Size')]);
		$reqdfieldst = array_merge($reqdfieldst,['port','numeric','numeric','numeric']);
		do_input_validation_type($sphere->row,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
		if((512 > $sphere->row['maxblocksize']) || (65464 < $sphere->row['maxblocksize'])):
			$input_errors[] = sprintf(gtext('Invalid maximum block size! It must be in the range from %d to %d.'),512,65464);
		endif;
		if(empty($input_errors)):
			$sphere->copyrowtogrid();
			write_config();
			$retval = 0;
			config_lock();
			$retval |= rc_update_service('tftpd');
			config_unlock();
			$_SESSION['submit'] = $sphere->get_basename();
			$_SESSION[$sphere->get_basename()] = $retval;
			header($sphere->get_location());
			exit;
		else:
			$page_mode = PAGE_MODE_EDIT;
//			$page_action = 'edit';
		endif;
		break;
	case 'disable':
		if($sphere->row['enable']): // if enabled, disable it
			$sphere->row['enable'] = false;
			$sphere->grid['enable'] = $sphere->row['enable'];
			write_config();
			$retval = 0;
			config_lock();
			$retval |= rc_update_service('tftpd');
			config_unlock();
			$_SESSION['submit'] = $sphere->get_basename();
			$_SESSION[$sphere->get_basename()] = $retval;
			header($sphere->get_location());
			exit;
		endif;
		$page_mode = PAGE_MODE_VIEW;
//		$page_action = 'view';
		break;
endswitch;
//	determine final page mode and calculate readonly flag
list($page_mode,$is_readonly) = calc_skipviewmode($page_mode);
$l_user = [];
foreach(system_get_user_list() as $key => $val):
	$l_user[$key] = htmlspecialchars($key);
endforeach;
$cop->get_username()->set_options($l_user);
$pgtitle = [gtext('Services'),gtext('TFTP')];
$document = new_page($pgtitle,$sphere->get_scriptname(),'notabnav');
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	ins_input_errors($input_errors)->
	ins_info_box($savemsg);
//	add content
$content->
	add_table_data_settings()->
		ins_colgroup_data_settings()->
		push()->
		addTHEAD()->
			c2_titleline_with_checkbox($cop->get_enable(),$sphere->row[$cop->get_enable()->get_name()],false,$is_readonly,gtext('Trivial File Transfer Protocol'))->
		pop()->
		addTBODY()->
			c2_filechooser($cop->get_dir(),htmlspecialchars($sphere->row[$cop->get_dir()->get_name()]),true,$is_readonly)->
			c2_checkbox($cop->get_allowfilecreation(),$sphere->row[$cop->get_allowfilecreation()->get_name()],false,$is_readonly);
$content->
	add_table_data_settings()->
		ins_colgroup_data_settings()->
		push()->
		addTHEAD()->
			c2_separator()->
			c2_titleline(gtext('Advanced Settings'))->
		pop()->
		addTBODY()->
			c2_input_text($cop->get_port(),htmlspecialchars($sphere->row[$cop->get_port()->get_name()]),false,$is_readonly)->
			c2_select($cop->get_username(),htmlspecialchars($sphere->row[$cop->get_username()->get_name()]),false,$is_readonly)->
			c2_input_text($cop->get_umask(),htmlspecialchars($sphere->row[$cop->get_umask()->get_name()]),false,$is_readonly)->
			c2_input_text($cop->get_timeout(),htmlspecialchars($sphere->row[$cop->get_timeout()->get_name()]),false,$is_readonly)->
			c2_input_text($cop->get_maxblocksize(),htmlspecialchars($sphere->row[$cop->get_maxblocksize()->get_name()]),false,$is_readonly)->
			c2_input_text($cop->get_extraoptions(),htmlspecialchars($sphere->row[$cop->get_extraoptions()->get_name()]),false,$is_readonly);
//	add buttons
switch($page_mode):
	case PAGE_MODE_VIEW:
		$document->
			add_area_buttons()->
				ins_button_edit()->
				ins_button_enadis(!$sphere->row[$cop->get_enable()->get_name()]);
		break;
	case PAGE_MODE_EDIT:
		$document->
			add_area_buttons()->
				ins_button_save()->
				ins_button_cancel();
		break;
endswitch;
$document->render();
