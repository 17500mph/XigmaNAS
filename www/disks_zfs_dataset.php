<?php
/*
	disks_zfs_dataset.php

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
require_once 'properties.php';
require_once 'co_sphere.php';
require_once 'co_request_method.php';
require_once 'zfs.inc';

class properties_disks_zfs_dataset {
	public $pool;
	public $name;
	public $compression;
	public $description;
	public $enabled;
	public $protected;
	
	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->pool = $this->prop_pool();
		$this->name = $this->prop_name();
		$this->compression = $this->prop_compression();
		$this->description = $this->prop_description();
		$this->enabled = $this->prop_enabled();
		$this->protected = $this->prop_protected();
		return $this;
	}
	public function prop_pool() : properties {
		$o = new properties_text($this);
		$o->set_name('pool');
		$o->set_title(gtext('Pool'));
		return $o;
	}
	private function prop_name() : properties {
		$o = new properties_text($this);
		$o->set_name('name');
		$o->set_title(gtext('Name'));
		return $o;
	}
	public function prop_compression() : properties {
		$o = new properties_text($this);
		$o->set_name('compression');
		$o->set_title(gtext('Compression'));
		return $o;
	}
	public function prop_description() : properties {
		$o = new properties_text($this);
		$o->set_name('desc');
		$o->set_title(gtext('Description'));
		return $o;
	}
	public function prop_enabled() : properties {
		$o = new properties_text($this);
		$o->set_name('enabled');
		$o->set_title(gtext('Enabled'));
		return $o;
	}
	public function prop_protected() : properties {
		$o = new properties_text($this);
		$o->set_name('protected');
		$o->set_title(gtext('Protected'));
		return $o;
	}
}
function get_sphere_disks_zfs_dataset() {
	global $config;
	
//	sphere structure
	$sphere = new co_sphere_grid('disks_zfs_dataset','php');
	$sphere->modify->set_basename($sphere->get_basename() . '_edit');
	$sphere->inform->set_basename($sphere->get_basename() . '_info');
	$sphere->set_notifier('zfsdataset');
	$sphere->set_row_identifier('uuid');
	$sphere->enadis(false);
	$sphere->lock(false);
	$sphere->sym_add(gtext('Add Dataset'));
	$sphere->sym_mod(gtext('Edit Dataset'));
	$sphere->sym_del(gtext('Dataset is marked for deletion'));
	$sphere->sym_loc(gtext('Dataset is locked'));
	$sphere->sym_unl(gtext('Dataset is unlocked'));
	$sphere->sym_mai(gtext('Maintenance'));
	$sphere->sym_inf(gtext('Information'));
	$sphere->cbm_delete(gtext('Delete Selected Datasets'));
	$sphere->cbm_delete_confirm(gtext('Do you want to delete selected datasets?'));
//	sphere external content
	$sphere->grid = &array_make_branch($config,'zfs','datasets','dataset');
	array_sort_key($sphere->grid,'name');
	return $sphere;
}
function zfsdataset_process_updatenotification($mode,$data) {
	global $config;
	
	$retval = 0;
	$sphere = get_sphere_disks_zfs_dataset();
	switch ($mode):
		case UPDATENOTIFY_MODE_NEW:
			$retval |= zfs_dataset_configure($data);
			if(isset($config['rrdgraphs']['enable'])):
				if(!file_exists($d_sysrebootreqd_path)):
					config_lock();
					$retval |= rc_update_service('cron');
					config_unlock();
				endif;
			endif;
			break;
		case UPDATENOTIFY_MODE_MODIFIED:
			$retval |= zfs_dataset_properties($data);
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				$retval |= zfs_dataset_destroy($data);
				if($retval === 0):
					unset($sphere->grid[$sphere->row_id]);
					write_config();
				endif;
			endif;
			break;
	endswitch;
	return $retval;
}
$cop = new properties_disks_zfs_dataset();
$sphere = get_sphere_disks_zfs_dataset();
//	determine request method
$rmo = new co_request_method();
$rmo->add('POST','apply',PAGE_MODE_VIEW);
$rmo->add('POST',$sphere->get_cbm_button_val_delete(),PAGE_MODE_POST);
$rmo->set_default('GET','view',PAGE_MODE_VIEW);
list($page_method,$page_action,$page_mode) = $rmo->validate();
switch($page_action):
	case 'apply':
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			//	Process notifications
			$retval |= updatenotify_process($sphere->get_notifier(),$sphere->get_notifier_processor());
		endif;
		$savemsg = get_std_save_message($retval);
		if($retval == 0):
			updatenotify_delete($sphere->get_notifier());
		endif;
		header($sphere->get_location());
		exit;
		break;
	case $sphere->get_cbm_button_val_delete(): // rows.delete
		$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
		foreach($sphere->cbm_grid as $sphere->cbm_row):
			if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier()))):
				$sphere->row = $sphere->grid[$sphere->row_id];
				$is_protected = is_bool($test = $sphere->row['protected'] ?? false) ? $test : true;
				if($is_protected):
					//	activity if record is protected
				else:
					$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->row[$sphere->get_row_identifier()]);
					switch ($mode_updatenotify):
						case UPDATENOTIFY_MODE_NEW:  
							updatenotify_clear($sphere->get_notifier(),$sphere->row[$sphere->get_row_identifier()]);
							updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY_CONFIG,$sphere->row[$sphere->get_row_identifier()]);
							break;
						case UPDATENOTIFY_MODE_MODIFIED:
							updatenotify_clear($sphere->get_notifier(),$sphere->row[$sphere->get_row_identifier()]);
							updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->row[$sphere->get_row_identifier()]);
							break;
						case UPDATENOTIFY_MODE_UNKNOWN:
							updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->row[$sphere->get_row_identifier()]);
							break;
					endswitch;
				endif;
			endif;
		endforeach;
		header($sphere->get_location());
		exit;
		break;
endswitch;
$pgtitle = [gtext('Disks'),gtext('ZFS'),gtext('Datasets'),gtext('Dataset')];
$record_exists = count($sphere->grid) > 0;
$a_col_width = ['5%','15%','15%','10%','45%','10%'];
$n_col_width = count($a_col_width);
//	prepare additional javascript code
$jcode = $sphere->doj(false);
if($record_exists):
	$document = new_page($pgtitle,$sphere->get_scriptname(),'tablesort');
else:
	$document = new_page($pgtitle,$sphere->get_scriptname());
endif;
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
		push()->
		add_tabnav_upper()->
			ins_tabnav_record('disks_zfs_zpool.php',gtext('Pools'))->
			ins_tabnav_record('disks_zfs_dataset.php',gtext('Datasets'),gtext('Reload page'),true)->
			ins_tabnav_record('disks_zfs_volume.php',gtext('Volumes'))->
			ins_tabnav_record('disks_zfs_snapshot.php',gtext('Snapshots'))->
			ins_tabnav_record('disks_zfs_config.php',gtext('Configuration'))->
			ins_tabnav_record('disks_zfs_settings.php',gtext('Settings'))->
		pop()->
		add_tabnav_lower()->
			ins_tabnav_record('disks_zfs_dataset.php',gtext('Dataset'),gtext('Reload page'),true)->
			ins_tabnav_record('disks_zfs_dataset_info.php',gtext('Information'));
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
$table = $content->add_table_data_selection();
$table->ins_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$tbody = $table->addTBODY();
$tfoot = $table->addTFOOT();
$thead->ins_titleline(gtext('Overview'),$n_col_width);
if($record_exists):
	$thead->
		addTR()->
			push()->
			addTHwC('lhelc sorter-false parser-false')->
				ins_cbm_checkbox_toggle($sphere)->
			pop()->
			insTHwC('lhell',$cop->pool->get_title())->
			insTHwC('lhell',$cop->name->get_title())->
			insTHwC('lhell',$cop->compression->get_title())->
			insTHwC('lhell',$cop->description->get_title())->
			insTHwC('lhebl sorter-false parser-false',gtext('Toolbox'));
else:
	$thead->
		addTR()->
			insTHwC('lhelc')->
			insTHwC('lhell',$cop->pool->get_title())->
			insTHwC('lhell',$cop->name->get_title())->
			insTHwC('lhell',$cop->compression->get_title())->
			insTHwC('lhell',$cop->description->get_title())->
			insTHwC('lhebl',gtext('Toolbox'));
endif;
if($record_exists):
	foreach($sphere->grid as $sphere->row_id => $sphere->row):
		$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->get_row_identifier_value());
		$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
		$is_enabled = $sphere->enadis() ? isset($sphere->row[$cop->enable->get_name()]) : true;
		$is_notprotected = $sphere->lock() ? !$sphere->row[$cop->protected->get_name()] : true;
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
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->pool->get_name()][0] ?? ''))->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->name->get_name()] ?? ''))->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->compression->get_name()] ?? ''))->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->description->get_name()] ?? ''))->
				add_toolbox_area()->
					ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
					ins_maintainbox($sphere,false)->
					ins_informbox($sphere,true);
	endforeach;
else:
	$tbody->ins_no_records_found($n_col_width);
endif;
$tfoot->ins_record_add($sphere,$n_col_width);
$document->add_area_buttons()->ins_cbm_button_delete($sphere);
$document->render();
