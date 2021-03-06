<?php
/*
	co_sphere.php

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
/*
 *	require_once 'wui2.php';
 *	global $config;
 *	global $g_img;
 */

class co_sphere_scriptname {
	protected $_basename = NULL;
	protected $_extension = NULL;
//	methods
	public function __construct(string $basename = NULL,string $extension = NULL) {
		$this->set_basename($basename);
		$this->set_extension($extension);
	}
	public function basename(string $basename = NULL) {
		if(isset($basename) && (1 === preg_match('/^[\w]+$/',$basename))):
			//	allow [0..9A-Za-z_] for filename
			$this->_basename = $basename;
		endif;
		return $this->_basename ?? false;
	}
	public function set_basename(string $basename = NULL) {
		$this->_basename = $basename;
		return $this->get_basename();
	}
	public function get_basename() {
		return $this->_basename ?? false;
	}
	public function extension(string $extension = NULL) {
		if(isset($extension) && (1 === preg_match('/^[\w]+$/',$extension))):
			//	allow [0..9A-Za-z_] for extension
			$this->_extension = $extension;
		endif;
		return $this->_extension ?? false;
	}
	public function set_extension(string $extension = NULL) {
		$this->_extension = $extension;
		return $this->get_extension();
	}
	public function get_extension() {
		return $this->_extension ?? false;
	}
	public function scriptname() {
		return sprintf('%s.%s',$this->get_basename(),$this->get_extension());
	}
	public function get_scriptname() {
		return sprintf('%s.%s',$this->get_basename(),$this->get_extension());
	}
	public function header() {
		return sprintf('Location: %s.%s',$this->get_basename(),$this->get_extension());
	}
	public function get_location() {
		return sprintf('Location: %s',$this->get_scriptname());
	}
}
class co_sphere_level1 extends co_sphere_scriptname { // for settings, services, row and grid
//	parent
	public $parent = NULL;
//	grid related
	public $grid = [];
	public $row = [];
	public $row_default = [];
//	modes
	protected $_enadis = NULL;
//	html class tags
	protected $_class_button = 'formbtn';
//	constructor
	public function __construct(string $basename = NULL,string $extension = NULL) {
		parent::__construct($basename,$extension);
		$this->parent = new co_sphere_scriptname($basename,$extension);
	}
//	methods
	public function get_parent() {
		return $this->parent;
	}
	public function enadis(bool $flag = NULL) {
		if(isset($flag)):
			$this->_enadis = $flag;
		endif;
		return $this->_enadis ?? false;
	}
	public function doj() {
		$output = [];
		$output[] = '';
		return implode(PHP_EOL,$output);
	}
	public function get_js_on_load() {
		return '';
	}
	public function get_js_document_ready() {
		return '';
	}
	public function get_js() {
		return '';
	}
	public function html_button(string $value = NULL,string $content = NULL,string $id = NULL) {
		$element = 'button';
		if(is_null($value)):
			$value = 'cancel';
		endif;
		if(is_null($id)):
			$id = sprintf('%1$s_%2$s',$element,$value);
		endif;
		if(is_null($content)):
			$content = gettext('Cancel');
		endif;
		$button_attributes = [
			'name' => 'submit',
			'type' => 'submit',
			'class' => $this->_class_button,
			'value' => $value,
			'id' => $id
		];
		if($value === 'cancel'):
			$button_attributes['formnovalidate'] = 'formnovalidate';
		endif;
		$root = new co_DOMDocument();
		$o_button = $root->addElement($element,$button_attributes,$content);
		return $root->get_html();
	}
}
class co_sphere_level2 extends co_sphere_level1 { // for row and grid
//	transaction manager
	protected $_notifier = NULL;
//	grid related
	public $row_id = NULL;
	protected $_row_identifier = NULL;
//	modes
	protected $_lock = NULL;
//	methods
	public function lock(bool $flag = NULL) {
		if(isset($flag)):
			$this->_lock = $flag;
		endif;
		return $this->_lock ?? false;
	}
	public function notifier(string $notifier = NULL) {
		if(isset($notifier)):
			if(1 === preg_match('/^[\w]+$/',$notifier)):
				$this->_notifier = $notifier;
				$this->_notifier_processor = $notifier . '_process_updatenotification';
			endif;
		endif;
		return $this->_notifier ?? false;
	}
	public function set_notifier(string $notifier = NULL) {
		if(isset($notifier)):
			$this->_notifier = $notifier;
			$this->_notifier_processor = $notifier . '_process_updatenotification';
		else:
			$this->_notifier = $notifier;
			$this->_notifier_processor = '_process_updatenotification';
		endif;
		return $this->get_notifier();
	}
	public function get_notifier() {
		return $this->_notifier ?? false;
	}
	public function row_identifier(string $row_identifier = NULL) {
		if(isset($row_identifier)):
			if(1 === preg_match('/^[a-z]+$/',$row_identifier)):
				$this->_row_identifier = $row_identifier;
			endif;
		endif;
		return $this->_row_identifier ?? false;
	}
	public function set_row_identifier(string $row_identifier = NULL) {
		$this->_row_identifier = $row_identifier;
		return $this->get_row_identifier();
	}
	public function get_row_identifier() {
		return $this->_row_identifier ?? false;
	}
	public function get_row_identifier_value() {
		return $this->row[$this->_row_identifier] ?? NULL;
	}
}
class co_sphere_settings extends co_sphere_level1 {
//	methods
	public function copyrowtogrid() {
		//	settings uses one record, therefore row can be soft-copied to grid
		foreach($this->row as $row_key => $row_val):
			$this->grid[$row_key] = $row_val;
		endforeach;
	}
}
class co_sphere_row extends co_sphere_level2 {
//	modes
	protected $_protectable;
//	methods
	public function protectable(bool $flag = NULL) {
		if(isset($flag)):
			$this->_protectable = $flag;
		endif;
		return $this->_protectable ?? false;
	}
	public function doj(bool $with_envelope = true) {
		$output = [];
		if($with_envelope):
			$output[] = '<script type="text/javascript">';
			$output[] = '//<![CDATA[';
		endif;
		$output[] = '$(window).on("load", function() {';
		//	Init spinner.
		$output[] = "\t" . '$("#iform").submit(function() { spinner(); });';
		$output[] = "\t" . '$(".spin").click(function() { spinner(); });';
		$output[] = '});';
		if($with_envelope):
			$output[] = '//]]>';
			$output[] = '</script>';
			$output[] = '';
		endif;
		return implode(PHP_EOL,$output);
	}
	public function upsert() {
		//	update existing grid record with row record or add row record to grid
		if(false === $this->row_id):
			$this->grid[] = $this->row;
		else:
			foreach($this->row as $row_key => $row_val):
				$this->grid[$this->row_id][$row_key] = $row_val;
			endforeach;
		endif;
	}
}
class co_sphere_grid extends co_sphere_level2 {
//	children
	public $modify = NULL; // modify
	public $maintain = NULL; // maintenance
	public $inform = NULL; // information
//	transaction manager
	protected $_notifier_processor = NULL;
	protected $_cbm_suffix = '';
//	checkbox member array
	protected $_cbm_name = 'cbm_grid';
	public $cbm_grid = [];
	public $cbm_row = [];
//	gtext
	protected $_cbm_delete = NULL;
	protected $_cbm_disable = NULL;
	protected $_cbm_enable = NULL;
	protected $_cbm_lock = NULL;
	protected $_cbm_toggle = NULL;
	protected $_cbm_unlock = NULL;
	protected $_cbm_delete_confirm = NULL;
	protected $_cbm_disable_confirm = NULL;
	protected $_cbm_enable_confirm = NULL;
	protected $_cbm_lock_confirm = NULL;
	protected $_cbm_toggle_confirm = NULL;
	protected $_cbm_unlock_confirm = NULL;
	protected $_sym_add = NULL;
	protected $_sym_mod = NULL;
	protected $_sym_del = NULL;
	protected $_sym_loc = NULL;
	protected $_sym_unl = NULL;
	protected $_sym_mai = NULL;
	protected $_sym_inf = NULL;
	protected $_sym_mup = NULL;
	protected $_sym_mdn = NULL;
//	html id tags
	protected $_cbm_button_id_delete = 'delete_selected_rows';
	protected $_cbm_button_id_disable = 'disable_selected_rows';
	protected $_cbm_button_id_enable = 'enable_selected_rows';
	protected $_cbm_button_id_toggle = 'toggle_selected_rows';
	protected $_cbm_checkbox_id_toggle = 'togglemembers';
//	html value tags
	protected $_cbm_button_val_delete = 'rows.delete';
	protected $_cbm_button_val_disable = 'rows.disable';
	protected $_cbm_button_val_enable = 'rows.enable';
	protected $_cbm_button_val_toggle = 'rows.toggle';
//	constructor
	public function __construct(string $basename = NULL,string $extension = NULL) {
		parent::__construct($basename,$extension);
		$this->modify = new co_sphere_scriptname($basename,$extension);
		$this->maintain = new co_sphere_scriptname($basename,$extension);
		$this->inform = new co_sphere_scriptname($basename,$extension);
	}
//	methods
	public function get_modify() {
		return $this->modify;
	}
	public function get_maintain() {
		return $this->maintain;
	}
	public function get_inform() {
		return $this->inform;
	}
	public function get_notifier_processor() {
		return $this->_notifier_processor ?? false;
	}
	public function toggle() {
		global $config;
		return $this->enadis() && isset($config['system']['enabletogglemode']) && (is_bool($config['system']['enabletogglemode']) ? $config['system']['enabletogglemode'] : true);
	}
	public function get_cbm_suffix() {
		return $this->_cbm_suffix;
	}
	public function set_cbm_suffix(string $value) {
		if(preg_match('/^[a-z\d_]+$/i',$value)):
			$this->_cbm_suffix = $value;
		endif;
		return $this;
	}
	public function get_cbm_name() {
		return $this->_cbm_name . $this->get_cbm_suffix();
	}
	public function set_cbm_name(string $value) {
		if(preg_match('/^\S+$/i',$value)):
			$this->_cbm_name = $value;
		endif;
		return $this;
	}
	public function get_cbm_button_id_delete() {
		return $this->_cbm_button_id_delete . $this->get_cbm_suffix();
	}
	public function set_cbm_button_id_delete(string $id) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_id_delete = $id;
		endif;
		return $this;
	}
	public function get_cbm_button_id_disable() {
		return $this->_cbm_button_id_disable . $this->get_cbm_suffix();
	}
	public function set_cbm_button_id_disable(string $id) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_id_disable = $id;
		endif;
		return $this;
	}
	public function get_cbm_button_id_enable() {
		return $this->_cbm_button_id_enable . $this->get_cbm_suffix();
	}
	public function set_cbm_button_id_enable(string $id) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_id_enable = $id;
		endif;
		return $this;
	}
	public function get_cbm_button_id_toggle() {
		return $this->_cbm_button_id_toggle . $this->get_cbm_suffix();
	}
	public function set_cbm_button_id_toggle(string $id) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_id_toggle = $id;
		endif;
		return $this;
	}
	public function get_cbm_checkbox_id_toggle() {
		return $this->_cbm_checkbox_id_toggle . $this->get_cbm_suffix();
	}
	public function set_cbm_checkbox_id_toggle(string $id) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_checkbox_id_toggle = $id;
		endif;
		return $this;
	}
	public function get_cbm_button_val_delete() {
		return $this->_cbm_button_val_delete . $this->get_cbm_suffix();
	}
	public function set_cbm_button_val_delete(string $value) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_val_delete = $value;
		endif;
		return $this;
	}
	public function get_cbm_button_val_disable() {
		return $this->_cbm_button_val_disable . $this->get_cbm_suffix();
	}
	public function set_cbm_button_val_disable(string $value) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_val_disable = $value;
		endif;
		return $this;
	}
	public function get_cbm_button_val_enable() {
		return $this->_cbm_button_val_enable . $this->get_cbm_suffix();
	}
	public function set_cbm_button_val_enable(string $value) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_val_enable = $value;
		endif;
		return $this;
	}
	public function get_cbm_button_val_toggle() {
		return $this->_cbm_button_val_toggle . $this->get_cbm_suffix();
	}
	public function set_cbm_button_val_toggle(string $value) {
		if(preg_match('/^\S+$/',$id)):
			$this->_cbm_button_val_toggle = $value;
		endif;
		return $this;
	}
	public function cbm_delete(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_delete = $message;
		endif;
		return $this->_cbm_delete ?? gettext('Delete Selected Records');
	}
	public function cbm_disable(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_disable = $message;
		endif;
		return $this->_cbm_disable ?? gettext('Disable Selected Records');
	}
	public function cbm_enable(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_enable = $message;
		endif;
		return $this->_cbm_enable ?? gettext('Enable Selected Records');
	}
	public function cbm_lock(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_lock = $message;
		endif;
		return $this->_cbm_lock ?? gettext('Lock Selected Records');
	}
	public function cbm_toggle(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_toggle = $message;
		endif;
		return $this->_cbm_toggle ?? gettext('Toggle Selected Records');
	}
	public function cbm_unlock(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_unlock = $message;
		endif;
		return $this->_cbm_unlock ?? gettext('Unlock Selected Records');
	}
	public function cbm_delete_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_delete_confirm = $message;
		endif;
		return $this->_cbm_delete_confirm ?? gettext('Do you want to delete selected records?');
	}
	public function cbm_disable_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_disable_confirm = $message;
		endif;
		return $this->_cbm_disable_confirm ?? gettext('Do you want to disable selected records?');
	}
	public function cbm_enable_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_enable_confirm = $message;
		endif;
		return $this->_cbm_enable_confirm ?? gettext('Do you want to enable selected records?');
	}
	public function cbm_lock_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_lock_confirm = $message;
		endif;
		return $this->_cbm_lock_confirm ?? gettext('Do you want to lock selected records?');
	}
	public function cbm_toggle_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_toggle_confirm = $message;
		endif;
		return $this->_cbm_toggle_confirm ?? gettext('Do you want to toggle selected records?');
	}
	public function cbm_unlock_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_unlock_confirm = $message;
		endif;
		return $this->_cbm_unlock_confirm ?? gettext('Do you want to unlock selected records?');
	}
	public function sym_add(string $message = NULL) {
		if(isset($message)):
			$this->_sym_add = $message;
		endif;
		return $this->_sym_add ?? gettext('Add Record');
	}
	public function sym_mod(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mod = $message;
		endif;
		return $this->_sym_mod ?? gettext('Edit Record');
	}
	public function sym_del(string $message = NULL) {
		if(isset($message)):
			$this->_sym_del = $message;
		endif;
		return $this->_sym_del ?? gettext('Record is marked for deletion');
	}
	public function sym_loc(string $message = NULL) {
		if(isset($message)):
			$this->_sym_loc = $message;
		endif;
		return $this->_sym_loc ?? gettext('Record is protected');
	}
	public function sym_unl(string $message = NULL) {
		if(isset($message)):
			$this->_sym_unl = $message;
		endif;
		return $this->_sym_unl ?? gettext('Record is unlocked');
	}
	public function sym_mai(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mai = $message;
		endif;
		return $this->_sym_mai ?? gettext('Record Maintenance');
	}
	public function sym_inf(string $message = NULL) {
		if(isset($message)):
			$this->_sym_inf = $message;
		endif;
		return $this->_sym_inf ?? gettext('Record Information');
	}
	public function sym_mup(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mup = $message;
		endif;
		return $this->_sym_mup ?? gettext('Move up');
	}
	public function sym_mdn(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mdn = $message;
		endif;
		return $this->_sym_mdn ?? gettext('Move down');
	}
	public function doj(bool $with_envelope = true) {
		$output = [];
		if($with_envelope):
			$output[] = '<script type="text/javascript">';
			$output[] = '//<![CDATA[';
		endif;
		$output[] = '$(window).on("load", function() {';
		//	Init action buttons.
		if($this->enadis()):
			if($this->toggle()):
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_toggle() . '").click(function () {';
				$output[] = "\t\t" . 'return confirm("' . $this->cbm_toggle_confirm() . '");';
				$output[] = "\t" . '});';
			else:
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_enable() . '").click(function () {';
				$output[] = "\t\t" . 'return confirm("' . $this->cbm_enable_confirm() . '");';
				$output[] = "\t" . '});';
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_disable() . '").click(function () {';
				$output[] = "\t\t" . 'return confirm("' . $this->cbm_disable_confirm() . '");';
				$output[] = "\t" . '});';
			endif;
		endif;
		$output[] = "\t" . '$("#' . $this->get_cbm_button_id_delete() . '").click(function () {';
		$output[] = "\t\t" . 'return confirm("' . $this->cbm_delete_confirm() . '");';
		$output[] = "\t" . '});';
		//	Disable action buttons.
		$output[] = "\t" . 'ab_disable' . $this->get_cbm_suffix() . '(true);';
		//	Init toggle checkbox.
		$output[] = "\t" . '$("#' . $this->get_cbm_checkbox_id_toggle() . '").click(function() {';
		$output[] = "\t\t" . 'cb_tbn' . $this->get_cbm_suffix() . '(this,"' . $this->get_cbm_name() . '[]");';
		$output[] = "\t" . '});';
		//	Init member checkboxes.
		$output[] = "\t" . '$("input[name=\'' . $this->get_cbm_name() . '[]\']").click(function() {';
		$output[] = "\t\t" . 'ab_control' . $this->get_cbm_suffix() . '(this,"' . $this->get_cbm_name() . '[]");';
		$output[] = "\t" . '});';
		//	Init spinner.
		if($with_envelope):
			$output[] = "\t" . '$("#iform").submit(function() { spinner(); });';
			$output[] = "\t" . '$(".spin").click(function() { spinner(); });';
		endif;
		$output[] = '});';
		$output[] = 'function ab_disable' . $this->get_cbm_suffix() . '(flag) {';
		if($this->enadis()):
			if($this->toggle()):
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_toggle() . '").prop("disabled",flag);';
			else:
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_enable() . '").prop("disabled",flag);';
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_disable() . '").prop("disabled",flag);';
			endif;
		endif;
		$output[] = "\t" . '$("#' . $this->get_cbm_button_id_delete() . '").prop("disabled",flag);';
		$output[] = '}';
		$output[] = 'function cb_tbn' . $this->get_cbm_suffix() . '(ego,tbn) {';
		$output[] = "\t" . 'var cba = $("input[name=\'"+tbn+"\']").filter(":enabled");';
		$output[] = "\t" . 'cba.prop("checked", function(_, checked) { return !checked; });';
		$output[] = "\t" . 'ab_disable' . $this->get_cbm_suffix() . '(1 > cba.filter(":checked").length);';
		$output[] = "\t" . 'ego.checked = false;';
		$output[] = '}';
		$output[] = 'function ab_control' . $this->get_cbm_suffix() . '(ego,tbn) {';
		$output[] = "\t" . 'var cba = $("input[name=\'"+tbn+"\']").filter(":enabled");';
		$output[] = "\t" . 'ab_disable' . $this->get_cbm_suffix() . '(1 > cba.filter(":checked").length);';
		$output[] = '}';
		if($with_envelope):
			$output[] = '//]]>';
			$output[] = '</script>';
			$output[] = '';
		endif;
		return implode(PHP_EOL,$output);
	}
	public function get_js_on_load() {
		$output = [];
		//	Init action buttons.
		if($this->enadis()):
			if($this->toggle()):
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_toggle() . '").click(function () {';
				$output[] = "\t\t" . 'return confirm("' . $this->cbm_toggle_confirm() . '");';
				$output[] = "\t" . '});';
			else:
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_enable() . '").click(function () {';
				$output[] = "\t\t" . 'return confirm("' . $this->cbm_enable_confirm() . '");';
				$output[] = "\t" . '});';
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_disable() . '").click(function () {';
				$output[] = "\t\t" . 'return confirm("' . $this->cbm_disable_confirm() . '");';
				$output[] = "\t" . '});';
			endif;
		endif;
		$output[] = "\t" . '$("#' . $this->get_cbm_button_id_delete() . '").click(function () {';
		$output[] = "\t\t" . 'return confirm("' . $this->cbm_delete_confirm() . '");';
		$output[] = "\t" . '});';
		//	Disable action buttons.
		$output[] = "\t" . 'ab_disable' . $this->get_cbm_suffix() . '(true);';
		//	Init toggle checkbox.
		$output[] = "\t" . '$("#' . $this->get_cbm_checkbox_id_toggle() . '").click(function() {';
		$output[] = "\t\t" . 'cb_tbn' . $this->get_cbm_suffix() . '(this,"' . $this->get_cbm_name() . '[]");';
		$output[] = "\t" . '});';
		//	Init member checkboxes.
		$output[] = "\t" . '$("input[name=\'' . $this->get_cbm_name() . '[]\']").click(function() {';
		$output[] = "\t\t" . 'ab_control' . $this->get_cbm_suffix() . '(this,"' . $this->get_cbm_name() . '[]");';
		$output[] = "\t" . '});';
		return implode(PHP_EOL,$output);
	}
	public function get_js() {
		$output = [];
		$output[] = 'function ab_disable' . $this->get_cbm_suffix() . '(flag) {';
		if($this->enadis()):
			if($this->toggle()):
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_toggle() . '").prop("disabled",flag);';
			else:
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_enable() . '").prop("disabled",flag);';
				$output[] = "\t" . '$("#' . $this->get_cbm_button_id_disable() . '").prop("disabled",flag);';
			endif;
		endif;
		$output[] = "\t" . '$("#' . $this->get_cbm_button_id_delete() . '").prop("disabled",flag);';
		$output[] = '}';
		$output[] = 'function cb_tbn' . $this->get_cbm_suffix() . '(ego,tbn) {';
		$output[] = "\t" . 'var cba = $("input[name=\'"+tbn+"\']").filter(":enabled");';
		$output[] = "\t" . 'cba.prop("checked", function(_, checked) { return !checked; });';
		$output[] = "\t" . 'ab_disable' . $this->get_cbm_suffix() . '(1 > cba.filter(":checked").length);';
		$output[] = "\t" . 'ego.checked = false;';
		$output[] = '}';
		$output[] = 'function ab_control' . $this->get_cbm_suffix() . '(ego,tbn) {';
		$output[] = "\t" . 'var cba = $("input[name=\'"+tbn+"\']").filter(":enabled");';
		$output[] = "\t" . 'ab_disable' . $this->get_cbm_suffix() . '(1 > cba.filter(":checked").length);';
		$output[] = '}';
		return implode(PHP_EOL,$output);
	}
	public function html_button_delete_rows() {
		return $this->html_button($this->get_cbm_button_val_delete(),$this->cbm_delete(),$this->get_cbm_button_id_delete());
	}
	public function html_button_disable_rows() {
		return $this->html_button($this->get_cbm_button_val_disable(),$this->cbm_disable(),$this->get_cbm_button_id_disable());
	}
	public function html_button_enable_rows() {
		return $this->html_button($this->get_cbm_button_val_enable(),$this->cbm_enable(),$this->get_cbm_button_id_enable());
	}
	public function html_button_toggle_rows() {
		return $this->html_button($this->get_cbm_button_val_toggle(),$this->cbm_toggle(),$this->get_cbm_button_id_toggle());
	}
	public function html_checkbox_cbm(bool $disabled = false) {
		$element = 'input';
		$identifier = $this->get_row_identifier_value();
		$input_attributes = [
			'type' => 'checkbox',
			'name' => $this->get_cbm_name() . '[]',
			'value' => $identifier,
			'id' => $identifier,
			'class' => 'oneemhigh'
		];
		if($disabled):
			$input_attributes['disabled'] = 'disabled';
		endif;
		$root = new co_DOMDocument();
		$o_input = $root->addElement($element,$input_attributes);
		return $root->get_html();
	}
	public function html_checkbox_toggle_cbm() {
		$element = 'input';
		$input_attributes = [
			'type' => 'checkbox',
			'name' => $this->get_cbm_checkbox_id_toggle(),
			'id' => $this->get_cbm_checkbox_id_toggle(),
			'title' => gettext('Invert Selection'),
			'class' => 'oneemhigh'
		];
		$root = new co_DOMDocument();
		$o_input = $root->addElement($element,$input_attributes);
		return $root->get_html();
	}
	public function html_toolbox(bool $notprotected = true,bool $notdirty = true) {
/*
 *	<td>
 *		<a href="scriptname_edit.php?submit=edit&uuid=12345678-1234-1234-1234-1234567890AB"><img="images/edit.png" title="Edit Record" alt="Edit Record" class="spin"/></a>
 *		or
 *		<img src="images/delete.png" title="Record is marked for deletion" alt="Record is marked for deletion"/>
 *		or
 *		<img src="images/locked.png" title="Record is protected" alt="Record is protected"/>
 *	</td>
 */
		global $g_img;

		$root = new co_DOMDocument();
		$o_td = $root->addTD();
		if($notdirty && $notprotected):
			//	record is editable
			$link = sprintf('%s?submit=edit&%s=%s',$this->modify->get_scriptname(),$this->get_row_identifier(),$this->get_row_identifier_value());
			$img_attributes = [
				'src' => $g_img['mod'],
				'title' => $this->sym_mod(),
				'alt' => $this->sym_mod(),
				'class' => 'spin oneemhigh'
			];
			$o_td->
				addA(['href' => $link])->
					insIMG($img_attributes);
		elseif($notprotected):
			//	record is dirty
			$img_attributes = [
				'src' => $g_img['del'],
				'title' => $this->sym_del(),
				'alt' => $this->sym_del(),
				'class' => 'oneemhigh'
			];
			$o_td->insIMG($img_attributes);
		else:
			//	record is protected
			$img_attributes = [
				'src' => $g_img['loc'],
				'title' => $this->sym_loc(),
				'alt' => $this->sym_loc(),
				'class' => 'oneemhigh'
			];
			$o_td->insIMG($img_attributes);
		endif;
		return $root->get_html();
	}
	public function html_maintainbox() {
		global $g_img;

		$link = sprintf('%s?%s=%s',$this->maintain->get_scriptname(),$this->get_row_identifier(),$this->get_row_identifier_value());
		$img_attributes = [
			'src' => $g_img['mai'],
			'title' => $this->sym_mai(),
			'alt' => $this->sym_mai(),
			'class' => 'spin oneemhigh'
		];
		$root = new co_DOMDocument();
		$root->
			addTD()->
				addA(['href' => $link])->
					insIMG($img_attributes);
		return $root->get_html();
	}
	public function html_informbox() {
		global $g_img;

		$link = sprintf('%s?%s=%s',$this->inform->get_scriptname(),$this->get_row_identifier(),$this->get_row_identifier_value());
		$img_attributes = [
			'src' => $g_img['inf'],
			'title' => $this->sym_inf(),
			'alt' => $this->sym_inf(),
			'class' => 'spin oneemhigh'
		];
		$root = new co_DOMDocument();
		$root->
			addTD()->
				addA(['href' => $link])->
					insIMG($img_attributes);
		return $root->get_html();
	}
	public function html_footer_add(int $colspan = 2) {
/*
 *	<tr>
 *		<th class="lcenl" colspan="1">
 *		</th>
 *		<th class="lceadd">
 *			<a href="scriptname_edit.php?submit=add"><img src="images/add.png" title="Add Record" alt="Add Record" class="spin"/></a>
 *		</th>
 *	</tr>
 */
		global $g_img;
		
		$img_attributes = [
			'src' => $g_img['add'],
			'title' => $this->sym_add(),
			'alt' => $this->sym_add(),
			'class' => 'spin oneemhigh'
		];
		$link = sprintf('%s?submit=add',$this->modify->get_scriptname());
		$root = new co_DOMDocument();
		$o_tr = $root->addTR();
		if($colspan > 1):
			$o_tr->insTH(['class' => 'lcenl','colspan' => $colspan - 1]);
		endif;
		$o_tr->
			addTH(['class' => 'lceadd'])->
				addA(['href' => $link])->
					insIMG($img_attributes);
		return $root->get_html();
	}
}
