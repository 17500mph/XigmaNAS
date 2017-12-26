<?php
/*
	co_sphere.php

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
		if(isset($basename)):
			$this->basename($basename);
		endif;
		if(isset($extension)):
			$this->extension($extension);
		endif;
	}
	public function basename(string $basename = NULL) {
		if(isset($basename)):
			//	allow [0..9A-Za-z_] for filename
			if(1 === preg_match('/^[\w]+$/',$basename)):
				$this->_basename = $basename;
			endif;
		endif;
		return $this->_basename ?? false;
	}
	public function extension(string $extension = NULL) {
		if(isset($extension)):
			//	allow [0..9A-Za-z_] for extension
			if(1 === preg_match('/^[\w]+$/',$extension)):
				$this->_extension = $extension;
			endif;
		endif;
		return $this->_extension ?? false;
	}
	public function scriptname() {
		return sprintf('%s.%s',$this->basename(),$this->extension());
	}
	public function header() {
		return sprintf('Location: %s.%s',$this->basename(),$this->extension());
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
	public function html_button(string $value = NULL,string $content = NULL,string $id = NULL) {
		$element = 'button';
		if(is_null($value)):
			$value = 'cancel';
		endif;
		if(is_null($id)):
			$id = sprintf('%1$s_%2$s',$element,$value);
		endif;
		if(is_null($content)):
			$content = gtext('Cancel');
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
	public function row_identifier(string $row_identifier = NULL) {
		if(isset($row_identifier)):
			if(1 === preg_match('/^[a-z]+$/',$row_identifier)):
				$this->_row_identifier = $row_identifier;
			endif;
		endif;
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
//	checkbox member array
	public $cbm_name = 'cbm_grid';
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
	protected $_cbm_button_id_delete = NULL;
	protected $_cbm_button_id_disable = NULL;
	protected $_cbm_button_id_enable = NULL;
	protected $_cbm_button_id_toggle = NULL;
	protected $_cbm_checkbox_id_toggle = NULL;
//	html value tags
	protected $_cbm_button_val_delete = NULL;
	protected $_cbm_button_val_disable = NULL;
	protected $_cbm_button_val_enable = NULL;
	protected $_cbm_button_val_toggle = NULL;
//	constructor
	public function __construct(string $basename = NULL,string $extension = NULL) {
		parent::__construct($basename,$extension);
		$this->modify = new co_sphere_scriptname($basename,$extension);
		$this->maintain = new co_sphere_scriptname($basename,$extension);
		$this->inform = new co_sphere_scriptname($basename,$extension);
	}
//	methods
	public function notifier_processor() {
		return $this->_notifier_processor ?? false;
	}
	public function toggle() {
		global $config;
		return $this->enadis() && isset($config['system']['enabletogglemode']) && (is_bool($config['system']['enabletogglemode']) ? $config['system']['enabletogglemode'] : true);
	}
	
	public function get_cbm_button_id_delete() {
		return $this->_cbm_button_id_delete ?? 'delete_selected_rows';
	}
	public function set_cbm_button_id_delete(string $id = NULL) {
		$this->_cbm_button_id_delete = $id;
		return $this;
	}
	public function get_cbm_button_id_enable() {
		return $this->_cbm_button_id_enable ?? 'enable_selected_rows';
	}
	public function set_cbm_button_id_enable(string $id = NULL) {
		$this->_cbm_button_id_enable = $id;
		return $this;
	}
	public function get_cbm_button_id_disable() {
		return $this->_cbm_button_id_disable ?? 'disable_selected_rows';
	}
	public function set_cbm_button_id_disable(string $id = NULL) {
		$this->_cbm_button_id_disable = $id;
		return $this;
	}
	public function get_cbm_button_id_toggle() {
		return $this->_cbm_button_id_toggle ?? 'toggle_selected_rows';
	}
	public function set_cbm_button_id_toggle(string $id = NULL) {
		$this->_cbm_button_id_toggle = $id;
		return $this;
	}
	public function get_cbm_checkbox_id_toggle() {
		return $this->_cbm_checkbox_id_toggle ?? 'togglemembers';
	}
	public function set_cbm_checkbox_id_toggle(string $id = NULL) {
		$this->_cbm_checkbox_id_toggle = $id;
		return $this;
	}
	public function set_cbm_button_val_delete(string $value = NULL) {
		$this->_cbm_button_val_delete = $value;
		return $this;
	}
	public function set_cbm_button_val_enable(string $value = NULL) {
		$this->_cbm_button_val_enable = $value;
		return $this;
	}
	public function set_cbm_button_val_disable(string $value = NULL) {
		$this->_cbm_button_val_disable = $value;
		return $this;
	}
	public function set_cbm_button_val_toggle(string $value = NULL) {
		$this->_cbm_button_val_toggle = $value;
		return $this;
	}
	public function get_cbm_button_val_delete() {
		return $this->_cbm_button_val_delete ?? 'rows.delete';
	}
	public function get_cbm_button_val_enable() {
		return $this->_cbm_button_val_enable ?? 'rows.enable';
	}
	public function get_cbm_button_val_disable() {
		return $this->_cbm_button_val_disable ?? 'rows.disable';
	}
	public function get_cbm_button_val_toggle() {
		return $this->_cbm_button_val_toggle ?? 'rows.toggle';
	}
	public function cbm_delete(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_delete = $message;
		endif;
		return $this->_cbm_delete ?? gtext('Delete Selected Records');
	}
	public function cbm_disable(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_disable = $message;
		endif;
		return $this->_cbm_disable ?? gtext('Disable Selected Records');
	}
	public function cbm_enable(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_enable = $message;
		endif;
		return $this->_cbm_enable ?? gtext('Enable Selected Records');
	}
	public function cbm_lock(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_lock = $message;
		endif;
		return $this->_cbm_lock ?? gtext('Lock Selected Records');
	}
	public function cbm_toggle(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_toggle = $message;
		endif;
		return $this->_cbm_toggle ?? gtext('Toggle Selected Records');
	}
	public function cbm_unlock(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_unlock = $message;
		endif;
		return $this->_cbm_unlock ?? gtext('Unlock Selected Records');
	}
	public function cbm_delete_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_delete_confirm = $message;
		endif;
		return $this->_cbm_delete_confirm ?? gtext('Do you want to delete selected records?');
	}
	public function cbm_disable_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_disable_confirm = $message;
		endif;
		return $this->_cbm_disable_confirm ?? gtext('Do you want to disable selected records?');
	}
	public function cbm_enable_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_enable_confirm = $message;
		endif;
		return $this->_cbm_enable_confirm ?? gtext('Do you want to enable selected records?');
	}
	public function cbm_lock_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_lock_confirm = $message;
		endif;
		return $this->_cbm_lock_confirm ?? gtext('Do you want to lock selected records?');
	}
	public function cbm_toggle_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_toggle_confirm = $message;
		endif;
		return $this->_cbm_toggle_confirm ?? gtext('Do you want to toggle selected records?');
	}
	public function cbm_unlock_confirm(string $message = NULL) {
		if(isset($message)):
			$this->_cbm_unlock_confirm = $message;
		endif;
		return $this->_cbm_unlock_confirm ?? gtext('Do you want to unlock selected records?');
	}
	public function sym_add(string $message = NULL) {
		if(isset($message)):
			$this->_sym_add = $message;
		endif;
		return $this->_sym_add ?? gtext('Add Record');
	}
	public function sym_mod(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mod = $message;
		endif;
		return $this->_sym_mod ?? gtext('Edit Record');
	}
	public function sym_del(string $message = NULL) {
		if(isset($message)):
			$this->_sym_del = $message;
		endif;
		return $this->_sym_del ?? gtext('Record is marked for deletion');
	}
	public function sym_loc(string $message = NULL) {
		if(isset($message)):
			$this->_sym_loc = $message;
		endif;
		return $this->_sym_loc ?? gtext('Record is protected');
	}
	public function sym_unl(string $message = NULL) {
		if(isset($message)):
			$this->_sym_unl = $message;
		endif;
		return $this->_sym_unl ?? gtext('Record is unlocked');
	}
	public function sym_mai(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mai = $message;
		endif;
		return $this->_sym_mai ?? gtext('Record Maintenance');
	}
	public function sym_inf(string $message = NULL) {
		if(isset($message)):
			$this->_sym_inf = $message;
		endif;
		return $this->_sym_inf ?? gtext('Record Information');
	}
	public function sym_mup(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mup = $message;
		endif;
		return $this->_sym_mup ?? gtext('Move up');
	}
	public function sym_mdn(string $message = NULL) {
		if(isset($message)):
			$this->_sym_mdn = $message;
		endif;
		return $this->_sym_mdn ?? gtext('Move down');
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
		$output[] = "\t" . 'ab_disable(true);';
		//	Init toggle checkbox.
		$output[] = "\t" . '$("#' . $this->get_cbm_checkbox_id_toggle() . '").click(function() {';
		$output[] = "\t\t" . 'cb_tbn(this,"' . $this->cbm_name . '[]");';
		$output[] = "\t" . '});';
		//	Init member checkboxes.
		$output[] = "\t" . '$("input[name=\'' . $this->cbm_name . '[]\']").click(function() {';
		$output[] = "\t\t" . 'ab_control(this,"' . $this->cbm_name . '[]");';
		$output[] = "\t" . '});';
		//	Init spinner.
		if($with_envelope):
			$output[] = "\t" . '$("#iform").submit(function() { spinner(); });';
			$output[] = "\t" . '$(".spin").click(function() { spinner(); });';
		endif;
		$output[] = '});';
		$output[] = 'function ab_disable(flag) {';
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
		$output[] = 'function cb_tbn(ego,tbn) {';
		$output[] = "\t" . 'var cba = $("input[name=\'"+tbn+"\']").filter(":enabled");';
		$output[] = "\t" . 'cba.prop("checked", function(_, checked) { return !checked; });';
		$output[] = "\t" . 'ab_disable(1 > cba.filter(":checked").length);';
		$output[] = "\t" . 'ego.checked = false;';
		$output[] = '}';
		$output[] = 'function ab_control(ego,tbn) {';
		$output[] = "\t" . 'var cba = $("input[name=\'"+tbn+"\']").filter(":enabled");';
		$output[] = "\t" . 'ab_disable(1 > cba.filter(":checked").length);';
		$output[] = '}';
		if($with_envelope):
			$output[] = '//]]>';
			$output[] = '</script>';
			$output[] = '';
		endif;
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
			'name' => $this->cbm_name . '[]',
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
			'title' => gtext('Invert Selection'),
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
			$link = sprintf('%s?submit=edit&%s=%s',$this->modify->scriptname(),$this->row_identifier(),$this->get_row_identifier_value());
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

		$link = sprintf('%s?%s=%s',$this->maintain->scriptname(),$this->row_identifier(),$this->get_row_identifier_value());
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

		$link = sprintf('%s?%s=%s',$this->inform->scriptname(),$this->row_identifier(),$this->get_row_identifier_value);
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
		$link = sprintf('%s?submit=add',$this->modify->scriptname());
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
