<?php
/*
	services_ctl_portal_group.php

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
require_once 'properties_services_ctl_portal_group.php';
require_once 'co_request_method.php';

function ctl_portal_group_sphere() {
	global $config;
	
	$sphere = new co_sphere_grid('services_ctl_portal_group','php');
	$sphere->modify->set_basename($sphere->get_basename() . '_edit');
	$sphere->set_notifier('ctl_portal_group');
	$sphere->set_row_identifier('uuid');
	$sphere->enadis(true);
	$sphere->lock(false);
	$sphere->sym_add(gtext('Add Portal Group'));
	$sphere->sym_mod(gtext('Edit Portal Group'));
	$sphere->sym_del(gtext('Portal Group is marked for deletion'));
	$sphere->sym_loc(gtext('Portal Group is locked'));
	$sphere->sym_unl(gtext('Portal Group is unlocked'));
	$sphere->cbm_delete(gtext('Delete Selected Portal Groups'));
	$sphere->cbm_disable(gtext('Disable Selected Portal Groups'));
	$sphere->cbm_enable(gtext('Enable Selected Portal Groups'));
	$sphere->cbm_toggle(gtext('Toggle Selected Portal Groups'));
	$sphere->cbm_delete_confirm(gtext('Do you want to delete selected portal groups?'));
	$sphere->cbm_disable(gtext('Do you want to disable selected portal groups?'));
	$sphere->cbm_enable_confirm(gtext('Do you want to enable selected portal groups?'));
	$sphere->cbm_toggle_confirm(gtext('Do you want to toggle selected portal groups?'));
//	sphere external content
	$sphere->grid = &array_make_branch($config,'ctld','ctl_portal_group','param');
	if(!empty($sphere->grid)):
		array_sort_key($sphere->grid,'name');
	endif;
	return $sphere;
}
function ctl_portal_group_process_updatenotification($mode,$data) {
	$retval = 0;
	$sphere = &ctl_portal_group_sphere();
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
//	init properties and sphere
$cop = new ctl_portal_group_properties();
$sphere = &ctl_portal_group_sphere();
//	determine request method
$rmo = new co_request_method();
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
$rmo->set_default('GET','view',PAGE_MODE_VIEW);
list($page_method,$page_action,$page_mode) = $rmo->validate();
switch($page_method):
	case 'POST':
		switch($page_action):
			case 'apply':
				$retval = 0;
				if(!file_exists($d_sysrebootreqd_path)):
					$retval |= updatenotify_process($sphere->get_notifier(),$sphere->get_notifier_processor());
				endif;
				$savemsg = get_std_save_message($retval);
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
$input_errors = [];
$prerequisites_ok = true;
$errormsg = '';
$pgtitle = [gtext('Services'),gtext('CAM Target Layer'),gtext('Portal Groups')];
$record_exists = count($sphere->grid) > 0;
$a_col_width = ['5%','25%','10%','50%','10%'];
$n_col_width = count($a_col_width);
if($record_exists):
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
			ins_tabnav_record('services_ctl.php',gtext('Global Settings'))->
			ins_tabnav_record('services_ctl_target.php',gtext('Targets'))->
			ins_tabnav_record('services_ctl_lun.php',gtext('LUNs'))->
			ins_tabnav_record('services_ctl_portal_group.php',gtext('Portal Groups'),gtext('Reload page'),true)->
			ins_tabnav_record('services_ctl_auth_group.php',gtext('Auth Groups'));
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
$tbody = $table->addTBODY();
$tfoot = $table->addTFOOT();
$thead->ins_titleline(gtext('Overview'),$n_col_width);
$tr = $thead->addTR();
if($record_exists):
	$tr->
		push()->
		addTHwC('lhelc sorter-false parser-false')->
			ins_cbm_checkbox_toggle($sphere)->
		pop()->
		insTHwC('lhell',$cop->get_name()->get_title())->
		insTHwC('lhelc sorter-false parser-false',gtext('Status'))->
		insTHwC('lhell',$cop->get_description()->get_title())->
		insTHwC('lhebl sorter-false parser-false',gtext('Toolbox'));
else:
	$tr->
		insTHwC('lhelc')->
		insTHwC('lhell',$cop->get_name()->get_title())->
		insTHwC('lhelc',gtext('Status'))->
		insTHwC('lhell',$cop->get_description()->get_title())->
		insTHwC('lhebl',gtext('Toolbox'));
endif;
if($record_exists):
	$gt_enabled = gtext('Enabled');
	$gt_disabled = gtext('Disabled');
	foreach($sphere->grid as $sphere->row_id => $sphere->row):
		$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->get_row_identifier_value());
		$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
		$is_enabled = $sphere->enadis() ? isset($sphere->row[$cop->get_enable()->get_name()]) : true;
		$is_notprotected = $sphere->lock() ? !$sphere->row[$cop->get_protected()->get_name()] : true;
		if($is_enabled):
			$src = $g_img['ena'];
			$title = $gt_enabled;
			$dc = '';
		else:
			$src = $g_img['dis'];
			$title = $gt_disabled;
			$dc = 'd';
		endif;
		$tbody->
			addTR()->
				push()->
				addTDwC('lcelc' . $dc)->
					ins_cbm_checkbox($sphere,!($is_notdirty && $is_notprotected))->
				pop()->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->get_name()->get_name()] ?? ''))->
				push()->
				addTDwC('lcelc' . $dc)->
					addA(['title' => $title])->
						insIMG(['src' => $src,'alt' => ''])->
				pop()->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->get_description()->get_name()] ?? ''))->
				add_toolbox_area()->
					ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
					insTD()->
					insTD();
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
$body->
	addJavaScript($sphere->get_js());
$body->
	add_js_on_load($sphere->get_js_on_load());
$body->
	add_js_document_ready($sphere->get_js_document_ready());
$document->
	render();
