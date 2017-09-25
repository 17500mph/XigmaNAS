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
require 'auth.inc';
require 'guiconfig.inc';
require 'diag_log.inc';
require 'co_sphere.php';
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
//	request method
$server_request_method = filter_input(INPUT_SERVER,'REQUEST_METHOD',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => '/^POST$/']]);
//	determine page mode
$page_mode = PAGE_MODE_VIEW;
switch($server_request_method):
	case 'POST':
		$action = filter_input(INPUT_POST,'submit',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => '','regexp' => '/^(edit|save)$/']]);
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
		$sphere->row['nentries'] = $property->nentries->validate_array_element($sphere->grid) ?? $property->nentries->get_defaultvalue();
		$sphere->row['resolve'] = $property->resolve->validate_config($sphere->grid);
		$sphere->row['disablecomp'] = $property->disablecomp->validate_config($sphere->grid);
		$sphere->row['disablesecure'] = $property->disablesecure->validate_config($sphere->grid);
		$sphere->row['enable'] = $property->enable->validate_config($sphere->grid['remote']);
		$sphere->row['ipaddr'] = $property->ipaddr->validate_array_element($sphere->grid['remote']) ?? $property->ipaddr->get_defaultvalue();
		$sphere->row['daemon'] = $property->daemon->validate_config($sphere->grid['remote']);
		$sphere->row['ftp'] = $property->ftp->validate_config($sphere->grid['remote']);
		$sphere->row['rsyncd'] = $property->rsyncd->validate_config($sphere->grid['remote']);
		$sphere->row['smartd'] = $property->smartd->validate_config($sphere->grid['remote']);
		$sphere->row['sshd'] = $property->sshd->validate_config($sphere->grid['remote']);
		$sphere->row['system'] = $property->system->validate_config($sphere->grid['remote']);
		break;
	case PAGE_MODE_POST:
		$sphere->row['reverse'] = $property->reverse->validate_input();
		$sphere->row['nentries'] = $property->nentries->validate_input();
		if(is_null($sphere->row['nentries'])):
			$input_errors[] = $property->nentries->get_message_error();
			$sphere->row['nentries'] = $_POST['nentries'];
		endif;
		$sphere->row['resolve'] = $property->resolve->validate_input();
		$sphere->row['disablecomp'] = $property->disablecomp->validate_input();
		$sphere->row['disablesecure'] = $property->disablesecure->validate_input();
		$sphere->row['enable'] = $property->enable->validate_input();
		$sphere->row['ipaddr'] = $property->ipaddr->validate_input();
		if(is_null($sphere->row['ipaddr'])):
			if($sphere->row['enable']):
				$input_errors[] = $property->ipaddr->get_message_error();
			endif;
			$sphere->row['ipaddr'] = $_POST['ipaddr'];
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
//	determine final page mode
switch($page_mode):
	case PAGE_MODE_EDIT:
		break;
	default:
		if(isset($config['system']['skipviewmode']) && (is_bool($config['system']['skipviewmode']) ? $config['system']['skipviewmode'] : true)):
			$page_mode = PAGE_MODE_EDIT;
		else:
			$page_mode = PAGE_MODE_VIEW;
		endif;
		break;
endswitch;
$jcode = [];
$jcode[PAGE_MODE_EDIT] = <<<EOJ
$(window).on("load", function() {
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
	$("#enable").on("click",function() { enable_change(false) });
	$("#button_save").on("click",function() { enable_change(true) });
	enable_change(false);
});
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.ipaddr.disabled = endis;
	document.iform.sshd.disabled = endis;
	document.iform.system.disabled = endis;
	document.iform.ftp.disabled = endis;
	document.iform.rsyncd.disabled = endis;
	document.iform.smartd.disabled = endis;
	document.iform.daemon.disabled = endis;
}
EOJ;
$jcode[PAGE_MODE_VIEW] = <<<EOJ
$(window).on("load", function() {
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
});
EOJ;
$pgtitle = [gtext('Diagnostics'),gtext('Log'),gtext('Settings')];
include 'fbegin.inc';
$pagecontent = new co_DOMDocument();
$pagecontent->addJavaScript($jcode[$page_mode]);
$pagecontent->
	ins_nav_table()->
		ins_nav_upper()->
			add_nav_record('diag_log.php',gtext('Log'))->
			add_nav_record('diag_log_settings.php',gtext('Settings'),gtext('Reload page'),true);
$content = $pagecontent->ins_area_data($sphere->scriptname());
$content->
	add_input_errors($input_errors)->
	add_info_box($savemsg);
switch($page_mode):
	case PAGE_MODE_VIEW:
		$content->
			ins_table_data_settings()->
				add_colgroup_data_settings()->
				ins_thead()->
					add_titleline(gtext('Log Settings'))->
					parentNode->
				ins_tbody()->
					add_checkbox($property->reverse,$sphere->row['reverse'],false,true)->
					add_input($property->nentries,htmlspecialchars($sphere->row['nentries']),false,true,4)->
					add_checkbox($property->resolve,$sphere->row['resolve'],false,true)->
					add_checkbox($property->disablecomp,$sphere->row['disablecomp'],false,true)->
					add_checkbox($property->disablesecure,$sphere->row['disablesecure'],false,true)->
					parentNode->
				parentNode->
			ins_table_data_settings()->
				add_colgroup_data_settings()->
				ins_thead()->
					add_separator()->
					add_titleline($property->enable->get_title())->
					parentNode->
				ins_tbody()->
					add_textinfo($property->enable->get_id(),gtext('Service Enabled'),$sphere->row['enable'] ? gtext('Yes') : gtext('No'))->
					add_input($property->ipaddr,htmlspecialchars($sphere->row['ipaddr']),false,true,39)->
					add_checkbox($property->system,$sphere->row['system'],false,true)->
					add_checkbox($property->ftp,$sphere->row['ftp'],false,true)->
					add_checkbox($property->rsyncd,$sphere->row['rsyncd'],false,true)->
					add_checkbox($property->sshd,$sphere->row['sshd'],false,true)->
					add_checkbox($property->smartd,$sphere->row['smartd'],false,true)->
					add_checkbox($property->daemon,$sphere->row['daemon'],false,true);
		$content->
			ins_submit()->
				add_edit_button();
		break;
	case PAGE_MODE_EDIT:
		$content->
			ins_table_data_settings()->
				add_colgroup_data_settings()->
				ins_thead()->
					add_titleline(gtext('Log Settings'))->
					parentNode->
				ins_tbody()->
					add_checkbox($property->reverse,$sphere->row['reverse'])->
					add_input($property->nentries,htmlspecialchars($sphere->row['nentries']),false,false,4)->
					add_checkbox($property->resolve,$sphere->row['resolve'])->
					add_checkbox($property->disablecomp,$sphere->row['disablecomp'])->
					add_checkbox($property->disablesecure,$sphere->row['disablesecure'])->
					parentNode->
				parentNode->
			ins_table_data_settings()->
				add_colgroup_data_settings()->
				ins_thead()->
					add_separator()->
					add_titleline_checkbox($property->enable,$sphere->row['enable'])->
					parentNode->
				ins_tbody()->
					add_input($property->ipaddr,htmlspecialchars($sphere->row['ipaddr']),false,false,39)->
					add_checkbox($property->system,$sphere->row['system'])->
					add_checkbox($property->ftp,$sphere->row['ftp'])->
					add_checkbox($property->rsyncd,$sphere->row['rsyncd'])->
					add_checkbox($property->sshd,$sphere->row['sshd'])->
					add_checkbox($property->smartd,$sphere->row['smartd'])->
					add_checkbox($property->daemon,$sphere->row['daemon']);
		$content->ins_submit()->
			add_save_button()->
			add_cancel_button();
		break;
endswitch;
$content->ins_remarks()->
	add_remark('note',gtext('Note'),sprintf(gtext('Syslog sends UDP datagrams to port 514 on the specified remote syslog server. Be sure to set syslogd on the remote server to accept syslog messages from this server.')));
$content->
	add_form_end();
$pagecontent->render();
include 'fend.inc';
?>
