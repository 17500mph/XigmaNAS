<?php
/*
  properties.php

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
class properties {
	public $_type;
	
	public function __construct() {
	}
	public function type(string $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
}
class properties_base extends properties {
	public $_name;
	public $_title;
	public $_description;
	public $_defaultvalue;
	public $_editableonadd;
	public $_editableonmodify;
	public $_filter;
	public $_filteroptions;
	public $_errormessage;
	
	public function __construct() {
		parent::__construct();
	}
	public function name(string $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function title(string $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function description(string $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function defaultvalue(string $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function editableonadd(bool $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function editableonmodify(bool $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function filter($value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function filteroptions(array $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
	public function errormessage(string $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
}
class properties_list extends properties_base {
	public $_options;
	
	public function __construct() {
		parent::__construct();
		$this->type('list');
	}
	public function options(array $value = NULL) {
		$ref = '_' . __FUNCTION__;
		if(isset($value)):
			$this->$ref = $value;
		endif;
		return $this->$ref;
	}
}
