<?php
/*
	diag_log_settings.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'diag_log.inc';
require_once 'co_sphere.php';
require_once 'properties_diag_log_settings.php';

function get_sphere_diag_log_settings() {
	global $config;
	$sphere = new co_sphere_row('diag_log_settings','php');
	$sphere->grid = &array_make_branch($config,'syslogd');
	array_make_branch($config,'syslogd','remote');
	return $sphere;
}
//	init properties and sphere
$property = new properties_diag_log_settings();
$sphere = &get_sphere_diag_log_settings();
$input_errors = [];
$savemsg = '';
//	determine server request method
$allowed_srm = ['POST'];
$allowed_srmr = sprintf('/^(%s)$/',implode('|',$allowed_srm));
$srm = filter_input(INPUT_SERVER,'REQUEST_METHOD',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => $allowed_srmr]]);
//	determine page mode
$page_mode = PAGE_MODE_VIEW;
switch($srm):
	case 'POST':
		//	determine allowed POST actions
		$apa = ['edit','save','cancel'];
		$apar = sprintf('/^(%s)$/',implode('|',$apa));
		$action = filter_input(INPUT_POST,'submit',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => '','regexp' => $apar]]);
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
		$sphere->row['reverse'] = $property->reverse->validate_config($sphere->grid);
		$ref = 'nentries';
		$sphere->row[$ref] = $property->$ref->validate_array_element($sphere->grid);
		if(is_null($sphere->row[$ref])):
			$input_errors[] = $property->$ref->get_message_error();
			if(array_key_exists($ref,$sphere->grid) && is_scalar($sphere->grid[$ref])): 
				$sphere->row[$ref] = $sphere->grid[$ref];
			else:
				$sphere->row[$ref] = $property->$ref->get_defaultvalue();
			endif;
		endif;
		$sphere->row['resolve'] = $property->resolve->validate_config($sphere->grid);
		$sphere->row['disablecomp'] = $property->disablecomp->validate_config($sphere->grid);
		$sphere->row['disablesecure'] = $property->disablesecure->validate_config($sphere->grid);
		$sphere->row['enable'] = $property->enable->validate_config($sphere->grid['remote']);
		$ref = 'ipaddr';
		$sphere->row[$ref] = $property->$ref->validate_array_element($sphere->grid['remote']);
		if(is_null($sphere->row[$ref])):
			$throw_error = $sphere->row['enable'];
			if(array_key_exists($ref,$sphere->grid['remote']) && is_string($sphere->grid['remote'][$ref])):
				$sphere->row[$ref] = $sphere->grid['remote'][$ref];
				if(preg_match('/\S/',$sphere->grid['remote'][$ref])):
					$throw_error = true;
				endif;
			else:
				$sphere->row[$ref] = $property->$ref->get_defaultvalue();
			endif;
			if($throw_error):
				$input_errors[] = $property->$ref->get_message_error();
			endif;
		endif;
		$sphere->row['daemon'] = $property->daemon->validate_config($sphere->grid['remote']);
		$sphere->row['ftp'] = $property->ftp->validate_config($sphere->grid['remote']);
		$sphere->row['rsyncd'] = $property->rsyncd->validate_config($sphere->grid['remote']);
		$sphere->row['smartd'] = $property->smartd->validate_config($sphere->grid['remote']);
		$sphere->row['sshd'] = $property->sshd->validate_config($sphere->grid['remote']);
		$sphere->row['system'] = $property->system->validate_config($sphere->grid['remote']);
		break;
	case PAGE_MODE_POST:
		$sphere->row['reverse'] = $property->reverse->validate_input();
		$ref = 'nentries';
		$sphere->row[$ref] = $property->$ref->validate_input();
		if(is_null($sphere->row[$ref])):
			$input_errors[] = $property->$ref->get_message_error();
			if(array_key_exists($ref,$_POST) && is_scalar($_POST[$ref])): 
				$sphere->row[$ref] = $_POST[$ref];
			else:
				$sphere->row[$ref] = $property->$ref->get_defaultvalue();
			endif;
		endif;
		$sphere->row['resolve'] = $property->resolve->validate_input();
		$sphere->row['disablecomp'] = $property->disablecomp->validate_input();
		$sphere->row['disablesecure'] = $property->disablesecure->validate_input();
		$sphere->row['enable'] = $property->enable->validate_input();
		$ref = 'ipaddr';
		$sphere->row[$ref] = $property->$ref->validate_input();
		if(is_null($sphere->row[$ref])):
			$throw_error = $sphere->row['enable'];
			if(array_key_exists($ref,$_POST) && is_string($_POST[$ref])):
				$sphere->row[$ref] = $_POST[$ref];
				if(preg_match('/\S/',$_POST[$ref])):
					$throw_error = true;
				endif;
			else:
				$sphere->row[$ref] = $property->$ref->get_defaultvalue();
				$throw_error = true;
			endif;
			if($throw_error):
				$input_errors[] = $property->$ref->get_message_error();
			endif;
		endif;
		$sphere->row['daemon'] = $property->daemon->validate_input();
		$sphere->row['ftp'] = $property->ftp->validate_input();
		$sphere->row['rsyncd'] = $property->rsyncd->validate_input();
		$sphere->row['smartd'] = $property->smartd->validate_input();
		$sphere->row['sshd'] = $property->sshd->validate_input();
		$sphere->row['system'] = $property->system->validate_input();
		if(empty($input_errors)):
			$sphere->grid['reverse'] = $sphere->row['reverse'];
			$sphere->grid['nentries'] = $sphere->row['nentries'];
			$sphere->grid['resolve'] = $sphere->row['resolve'];
			$sphere->grid['disablecomp'] = $sphere->row['disablecomp'];
			$sphere->grid['disablesecure'] = $sphere->row['disablesecure'];
			$sphere->grid['remote']['enable'] = $sphere->row['enable'];
			$sphere->grid['remote']['ipaddr'] = $sphere->row['ipaddr'];
			$sphere->grid['remote']['system'] = $sphere->row['system'];
			$sphere->grid['remote']['ftp'] = $sphere->row['ftp'];
			$sphere->grid['remote']['rsyncd'] = $sphere->row['rsyncd'];
			$sphere->grid['remote']['sshd'] = $sphere->row['sshd'];
			$sphere->grid['remote']['smartd'] = $sphere->row['smartd'];
			$sphere->grid['remote']['daemon'] = $sphere->row['daemon'];
			write_config();
			$retval = 0;
			if(!file_exists($d_sysrebootreqd_path)):
				config_lock();
				$retval = rc_restart_service('syslogd');
				config_unlock();
			endif;
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
$document = new_page([gtext('Diagnostics'),gtext('Log'),gtext('Settings')],$sphere->scriptname());
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
			mount_tabnav_record('diag_log.php',gtext('Log'))->
			mount_tabnav_record('diag_log_settings.php',gtext('Settings'),gtext('Reload page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	mount_input_errors($input_errors)->
	mount_info_box($savemsg);
//	add content
$content->
	add_table_data_settings()->
		mount_colgroup_data_settings()->
		addTHEAD()->
			c2_titleline(gtext('Log Settings'))->
			parentNode->
		addTBODY()->
			c2_checkbox($property->reverse,$sphere->row['reverse'],false,$is_readonly)->
			c2_input_text($property->nentries,htmlspecialchars($sphere->row['nentries']),false,$is_readonly)->
			c2_checkbox($property->resolve,$sphere->row['resolve'],false,$is_readonly)->
			c2_checkbox($property->disablecomp,$sphere->row['disablecomp'],false,$is_readonly)->
			c2_checkbox($property->disablesecure,$sphere->row['disablesecure'],false,$is_readonly);
$content->
	add_table_data_settings()->
		mount_colgroup_data_settings()->
		addTHEAD()->
			c2_separator()->
			c2_titleline_with_checkbox($property->enable,$sphere->row['enable'],false,$is_readonly)->
			parentNode->
		addTBODY()->
			c2_input_text($property->ipaddr,htmlspecialchars($sphere->row['ipaddr']),false,$is_readonly)->
			c2_checkbox($property->system,$sphere->row['system'],false,$is_readonly)->
			c2_checkbox($property->ftp,$sphere->row['ftp'],false,$is_readonly)->
			c2_checkbox($property->rsyncd,$sphere->row['rsyncd'],false,$is_readonly)->
			c2_checkbox($property->sshd,$sphere->row['sshd'],false,$is_readonly)->
			c2_checkbox($property->smartd,$sphere->row['smartd'],false,$is_readonly)->
			c2_checkbox($property->daemon,$sphere->row['daemon'],false,$is_readonly);
//	add buttons
switch($page_mode):
	case PAGE_MODE_VIEW:
		$document->add_area_buttons()->mount_button_edit();
		break;
	case PAGE_MODE_EDIT:
		$document->add_area_buttons()->mount_button_save()->mount_button_cancel();
		break;
endswitch;
//	add remarks
$content->add_area_remarks()->
	mount_remark('note',gtext('Note'),sprintf(gtext('Syslog sends UDP datagrams to port 514 on the specified remote syslog server. Be sure to set syslogd on the remote server to accept syslog messages from this server.')));
//	done
$document->render();
?>
