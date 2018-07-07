<?php
/*
	co_request_methods.php

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
class co_request_method {
	public $_activities;
	public $_default;
	public $_method;
	public $_action;
	
	public function __construct() {
		$this->_activities = [];
		$this->_default = [NULL,NULL,NULL];
	}
	public function add(string $method,string $submit,$value = NULL) {
		if(array_key_exists($method,$this->_activities)):
			$this->_activities[$method][$submit] = $value;
		else:
			$this->_activities[$method] = [$submit => $value];
		endif;
		return $this;
	}
	public function set_default(string $method = NULL,string $submit = NULL,$value = NULL) {
		$this->_default = [$method,$submit,$value];
		return $this;
	}
	public function get_default() : array {
		return $this->_default;
	}
	public function validate() : array {
		//	check $_SESSION settings first
		$this->_method = 'SESSION';
		if(array_key_exists($this->_method,$this->_activities) && array_key_exists('submit',$_SESSION)):
//			switch($this->_method):
//				case 'SESSION': // Validate $_SESSION['submit']
					$this->_action = filter_var($_SESSION['submit'],FILTER_CALLBACK,['options' =>
						function(string $value) { return array_key_exists($value,$this->_activities[$this->_method]) ? $value : NULL; }
					]);
					if(isset($this->_action)):
						return [$this->_method,$this->_action,$this->_activities[$this->_method][$this->_action]];
					endif;
//					break;
//			endswitch;
		endif;
		//	check inputs
		$this->_method = filter_input(INPUT_SERVER,'REQUEST_METHOD',FILTER_CALLBACK,['options' =>
			function(string $value) { return array_key_exists($value,$this->_activities) ? $value : NULL; }
		]);
		if(isset($this->_method)):
			switch($this->_method):
				case 'POST': // Validate $_POST['submit']
					$this->_action = filter_input(INPUT_POST,'submit',FILTER_CALLBACK,['options' =>
						function(string $value) { return array_key_exists($value,$this->_activities[$this->_method]) ? $value : NULL; }
					]);
					if(isset($this->_action)):
						return [$this->_method,$this->_action,$this->_activities[$this->_method][$this->_action]];
					endif;
					break;
				case 'GET': // Validate $_GET['submit']
					$this->_action = filter_input(INPUT_GET,'submit',FILTER_CALLBACK,['options' =>
						function(string $value) { return array_key_exists($value,$this->_activities[$this->_method]) ? $value : NULL; }
					]);
					if(isset($this->_action)):
						return [$this->_method,$this->_action,$this->_activities[$this->_method][$this->_action]];
					endif;
					break;
/*
				case 'HEAD':
					break;
				case 'PUT':
					break;
				case 'DELETE':
					break;
				case 'CONNECT':
					break;
				case 'OPTIONS':
					break;
				case 'TRACE':
					break;
				case 'PATCH':
					break;
 */
				default:
					break;
			endswitch;
		endif;
		return $this->get_default();
	}
}