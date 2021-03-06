<?php
/*
	services_ctl_sub_option.php

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
require_once 'properties_services_ctl_sub_option.php';
require_once 'co_request_method.php';

function ctl_sub_option_sphere() {
	global $config;

	$sphere = new co_sphere_grid('services_ctl_sub_option','php');
	$sphere->get_modify()->set_basename('services_ctl_sub_option_edit');
	$sphere->get_parent()->set_basename('services_ctl_auth_group');
	$sphere->set_notifier('ctl_sub_option');
	$sphere->set_row_identifier('uuid');
	$sphere->enadis(true);
	$sphere->lock(false);
	$sphere->sym_add(gettext('Add Option Record'));
	$sphere->sym_mod(gettext('Edit Option Record'));
	$sphere->sym_del(gettext('Option record is marked for deletion'));
	$sphere->sym_loc(gettext('Option record is locked'));
	$sphere->sym_unl(gettext('Option record is unlocked'));
	$sphere->cbm_delete(gettext('Delete Selected Option Records'));
	$sphere->cbm_disable(gettext('Disable Selected Option Records'));
	$sphere->cbm_enable(gettext('Enable Selected Option Records'));
	$sphere->cbm_toggle(gettext('Toggle Selected Option Records'));
	$sphere->cbm_delete_confirm(gettext('Do you want to delete selected option records?'));
	$sphere->cbm_disable_confirm(gettext('Do you want to disable selected option records?'));
	$sphere->cbm_enable_confirm(gettext('Do you want to enable selected option records?'));
	$sphere->cbm_toggle_confirm(gettext('Do you want to toggle selected option records?'));
//	sphere external content
	$sphere->grid = &array_make_branch($config,'ctld','ctl_sub_option','param');
	if(!empty($sphere->grid)):
		array_sort_key($sphere->grid,'name');
	endif;
	return $sphere;
}
function ctl_sub_option_process_updatenotification($mode,$data) {
	$retval = 0;
	$sphere = &ctl_sub_option_sphere();
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
		case UPDATENOTIFY_MODE_DIRTY:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
function ctl_sub_option_selection($cop,$sphere) {
	global $d_sysrebootreqd_path;
	global $savemsg;

	$input_errors = [];
	$errormsg = '';
	$pgtitle = [gettext('Services'),gettext('CAM Target Layer'),gettext('Portal Groups'),gettext('Option')];
	$record_exists = count($sphere->grid) > 0;
	$use_tablesort = count($sphere->grid) > 1;
	$a_col_width = ['5%','25%','25%','10%','25%','10%'];
	$n_col_width = count($a_col_width);
	if($use_tablesort):
		$document = new_page($pgtitle,$sphere->get_scriptname(),'tablesort');
	else:
		$document = new_page($pgtitle,$sphere->get_scriptname());
	endif;
	//	get areas
	$body = $document->getElementById('main');
	$pagecontent = $document->getElementById('pagecontent');
	//	add tab navigation
	$document->
		add_area_tabnav()->
			push()->
			add_tabnav_upper()->
				ins_tabnav_record('services_ctl.php',gettext('Global Settings'))->
				ins_tabnav_record('services_ctl_target.php',gettext('Targets'))->
				ins_tabnav_record('services_ctl_lun.php',gettext('LUNs'))->
				ins_tabnav_record('services_ctl_portal_group.php',gettext('Portal Groups'),gettext('Reload page'),true)->
				ins_tabnav_record('services_ctl_auth_group.php',gettext('Auth Groups'))->
			pop()->
			add_tabnav_lower()->
				ins_tabnav_record('services_ctl_portal_group.php',gettext('Portal Groups'))->
				ins_tabnav_record('services_ctl_sub_listen.php',gettext('Listen'))->
				ins_tabnav_record('services_ctl_sub_option.php',gettext('Option'),gettext('Reload page'),true);
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
	if(updatenotify_exists($sphere->get_notifier())):
		$content->ins_config_has_changed_box();
	endif;
	//	add content
	$table = $content->add_table_data_selection();
	$table->ins_colgroup_with_styles('width',$a_col_width);
	$thead = $table->addTHEAD();
	if($record_exists):
		$tbody = $table->addTBODY();
	else:
		$tbody = $table->addTBODY(['class' => 'donothighlight']);
	endif;
	$tfoot = $table->addTFOOT();
	$thead->ins_titleline(gettext('Overview'),$n_col_width);
	$tr = $thead->addTR();
	if($record_exists):
		$tr->
			push()->
			addTHwC('lhelc sorter-false parser-false')->
				ins_cbm_checkbox_toggle($sphere)->
			pop()->
			insTHwC('lhell',$cop->get_name()->get_title())->
			insTHwC('lhell',$cop->get_value()->get_title())->
			insTHwC('lhelc sorter-false parser-false',gettext('Status'))->
			insTHwC('lhell',$cop->get_description()->get_title())->
			insTHwC('lhebl sorter-false parser-false',$cop->get_toolbox()->get_title());
	else:
		$tr->
			insTHwC('lhelc')->
			insTHwC('lhell',$cop->get_name()->get_title())->
			insTHwC('lhell',$cop->get_value()->get_title())->
			insTHwC('lhelc',gettext('Status'))->
			insTHwC('lhell',$cop->get_description()->get_title())->
			insTHwC('lhebl',$cop->get_toolbox()->get_title());
	endif;
	if($record_exists):
		foreach($sphere->grid as $sphere->row_id => $sphere->row):
			$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->get_row_identifier_value());
			$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
			$is_enabled = $sphere->enadis() ? (is_bool($test = $sphere->row[$cop->get_enable()->get_name()] ?? false) ? $test : true): true;
			$is_notprotected = $sphere->lock() ? !(is_bool($test = $sphere->row[$cop->get_protected()->get_name()] ?? false) ? $test : true) : true;
			$dc = $is_enabled ? '' : 'd';
			$tbody->
				addTR()->
					push()->
					addTDwC('lcelc' . $dc)->
						ins_cbm_checkbox($sphere,!($is_notdirty && $is_notprotected))->
					pop()->
					insTDwC('lcell' . $dc,$sphere->row[$cop->get_name()->get_name()] ?? '')->
					insTDwC('lcell' . $dc,$sphere->row[$cop->get_value()->get_name()] ?? '')->
					ins_enadis_icon($is_enabled)->
					insTDwC('lcell' . $dc,$sphere->row[$cop->get_description()->get_name()] ?? '')->
					add_toolbox_area()->
						ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
						ins_maintainbox($sphere,false)->
						ins_informbox($sphere,false);
		endforeach;
	else:
		$tbody->ins_no_records_found($n_col_width);
	endif;
	$tfoot->ins_record_add($sphere,$n_col_width);
	$document->
		add_area_buttons()->
			ins_cbm_button_enadis($sphere)->
			ins_cbm_button_delete($sphere);
	//	additional javascript code
	$body->addJavaScript($sphere->get_js());
	$body->add_js_on_load($sphere->get_js_on_load());
	$body->add_js_document_ready($sphere->get_js_document_ready());
	$document->render();
}
//	init properties and sphere
$cop = new ctl_sub_option_properties();
$sphere = &ctl_sub_option_sphere();
//	determine request method
$rmo = new co_request_method();
$rmo->add('GET','view',PAGE_MODE_VIEW);
$rmo->add('POST','apply',PAGE_MODE_VIEW);
$rmo->add('POST',$sphere->get_cbm_button_val_delete(),PAGE_MODE_POST);
if($sphere->enadis() && method_exists($cop,'get_enable')):
	if($sphere->toggle()):
		$rmo->add('POST',$sphere->get_cbm_button_val_toggle(),PAGE_MODE_POST);
	else:
		$rmo->add('POST',$sphere->get_cbm_button_val_enable(),PAGE_MODE_POST);
		$rmo->add('POST',$sphere->get_cbm_button_val_disable(),PAGE_MODE_POST);
	endif;
endif;
$rmo->add('SESSION',$sphere->get_basename(),PAGE_MODE_VIEW);
$rmo->set_default('GET','view',PAGE_MODE_VIEW);
list($page_method,$page_action,$page_mode) = $rmo->validate();
switch($page_method):
	case 'SESSION':
		switch($page_action):
			case $sphere->get_basename():
				//	catch error code
				$retval = filter_var($_SESSION[$sphere->get_basename()],FILTER_VALIDATE_INT,['options' => ['default' => 0]]);
				unset($_SESSION['submit']);
				unset($_SESSION[$sphere->get_basename()]);
				$savemsg = get_std_save_message($retval);
				ctl_sub_option_selection($cop,$sphere);
				break;
		endswitch;
		break;
	case 'GET':
		switch($page_action):
			case 'view':
				ctl_sub_option_selection($cop,$sphere);
				break;
		endswitch;
		break;
	case 'POST':
		switch($page_action):
			case 'apply':
				$retval = 0;
				if(!file_exists($d_sysrebootreqd_path)):
					$retval |= updatenotify_process($sphere->get_notifier(),$sphere->get_notifier_processor());
					config_lock();
					$retval |= rc_update_reload_service('ctld');
					config_unlock();
					$_SESSION['submit'] = $sphere->get_basename();
					$_SESSION[$sphere->get_basename()] = $retval;
				endif;
				if($retval == 0):
					updatenotify_delete($sphere->get_notifier());
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_delete():
				updatenotify_cbm_delete($sphere,$cop);
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_toggle():
				if(updatenotify_cbm_toggle($sphere,$cop)):
					write_config();
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_enable():
				if(updatenotify_cbm_enable($sphere,$cop)):
					write_config();
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_disable():
				if(updatenotify_cbm_disable($sphere,$cop)):
					write_config();
				endif;
				header($sphere->get_location());
				exit;
				break;
		endswitch;
		break;
endswitch;
