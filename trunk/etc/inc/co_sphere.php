<?php
/*
  co_sphere.php

  Part of NAS4Free (http://www.nas4free.org).
  Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
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
class co_sphere_grid extends co_sphere_scriptname {
//	parent
	public $parent = NULL;
//	children
	public $mod = NULL; // modify
	public $mai = NULL; // maintenance
	public $inf = NULL; // information
//	transaction manager
	protected $_notifier = NULL;
	protected $_notifier_processor = NULL;
//	grid related
	public $grid = [];
	public $row = [];
	public $row_id = NULL;
	protected $_row_identifier = NULL;
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
//	modes
	protected $_enadis = NULL;
	protected $_lock = NULL;
	protected $_toggle = NULL;
//	methods	
	public function __construct(string $basename = NULL,string $extension = NULL) {
		parent::__construct($basename,$extension);
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
	public function notifier_processor() {
		return $this->_notifier_processor ?? false;
	}
	public function row_identifier(string $row_identifier = NULL) {
		if(isset($row_identifier)):
			if(1 === preg_match('/^[a-z]+$/',$row_identifier)):
				$this->_row_identifier = $row_identifier;
			endif;
		endif;
		return $this->_row_identifier ?? false;
	}
	public function enadis(bool $flag = NULL) {
		if(isset($flag)):
			$this->_enadis = $flag;
		endif;
		return $this->_enadis ?? false;
	}
	public function lock(bool $flag = NULL) {
		if(isset($flag)):
			$this->_lock = $flag;
		endif;
		return $this->_lock ?? false;
	}
	public function toggle() {
		global $config;
		return $this->enadis() && isset($config['system']['enabletogglemode']);
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
}
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
