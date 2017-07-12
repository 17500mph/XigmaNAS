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
	protected $v_name = '';
	protected $v_title = '';
	protected $v_description = '';
	protected $v_defaultvalue = NULL;
	protected $v_editableonadd = true;
	protected $v_editableonmodify = true;
	protected $v_filter = [];
	protected $v_message_error = '';
	protected $v_message_info = '';
	protected $v_message_warning = '';

//	get/set methods
	public function name(string $value = NULL) {
		if(func_num_args() > 0):
			$this->v_name = $value;
		endif;
		return $this->v_name;
	}
	public function title(string $value = NULL) {
		if(func_num_args() > 0):
			$this->v_title = $value;
		endif;
		return $this->v_title;
	}
	public function description(string $value = NULL) {
		if(func_num_args() > 0):
			$this->v_description = $value;
		endif;
		return $this->v_description;
	}
	public function defaultvalue(string $value = NULL) {
		if(func_num_args() > 0):
			$this->v_defaultvalue = $value;
		endif;
		return $this->v_defaultvalue;
	}
	public function editableonadd(bool $value = NULL) {
		if(func_num_args() > 0):
			$this->v_editableonadd = $value;
		endif;
		return $this->v_editableonadd;
	}
	public function editableonmodify(bool $value = NULL) {
		if(func_num_args() > 0):
			$this->v_editableonmodify = $value;
		endif;
		return $this->v_editableonmodify;
	}
	public function message_error(string $value = NULL) {
		if(func_num_args() > 0):
			$this->v_message_error = $value;
		endif;
		return $this->v_message_error;
	}
	public function message_info(string $value = NULL) {
		if(func_num_args() > 0):
			$this->v_message_info = $value;
		endif;
		return $this->v_message_info;
	}
	public function message_warning(string $value = NULL) {
		if(func_num_args() > 0):
			$this->v_message_warning = $value;
		endif;
		return $this->v_message_warning;
	}
/**
 * Method to set filter type.
 * @param type $value Filter type.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */	
	public function filter($value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(!array_key_exists($filter_name,$this->v_filter)):
			$this->v_filter[$filter_name] = ['filter' => NULL,'flags' => NULL,'options' => NULL];
		endif;
		$this->v_filter[$filter_name]['filter'] = $value;
		return $this;
	}
/**
 * Method to set filter flags.
 * @param type $value Flags for filter.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */
	public function filter_flags($value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(!array_key_exists($filter_name,$this->v_filter)):
			$this->v_filter[$filter_name] = ['filter' => NULL,'flags' => NULL,'options' => NULL];
		endif;
		$this->v_filter[$filter_name]['flags'] = $value;
		return $this;
	}
/**
 * Method to set filter options.
 * @param array $value Filter options.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */
	public function filter_options(array $value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(!array_key_exists($filter_name,$this->v_filter)):
			$this->v_filter[$filter_name] = ['filter' => NULL,'flags' => NULL,'options' => NULL];
		endif;
		$this->v_filter[$filter_name]['options'] = $value;
		return $this;
	}
/**
 * Method to apply the default class filter to a filter name.
 * Filter expects a string containing at least one non-whitespace character.
 * @param string $filter_name Name of the filter, default = 'ui'.
 * @return object Returns $this.
 */
	public function filter_use_default(string $filter_name = 'ui') {
		$this->filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->filter_options(['default' => NULL,'regexp' => '/\S/'],$filter_name);
		return $this;
	}
/**
 * Method returns the filter settings of $filter_name:
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return array If $filter_name exists the filter configuration is returned, otherwise NULL is returned.
 */
	public function get_filter(string $filter_name = 'ui') {
		if(array_key_exists($filter_name,$this->v_filter)):
			return $this->v_filter[$filter_name];
		endif;
		return NULL;
	}
/**
 * Method to apply a filter to an input elemet.
 * @param int $input_type Input type. Check the PHP manual for supported input types.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return mixed Filter result.
 */
	public function validate_input(int $input_type = INPUT_POST,string $filter_name = 'ui') {
		$fv = $this->get_filter($filter_name);
		if(isset($fv)):
			$action  = (isset($fv['flags']) ? 1 : 0) + (isset($fv['options']) ? 2 : 0);
			switch($action):
				case 3: return filter_input($input_type,$this->name(),$fv['filter'],['flags' => $fv['flags'],'options' => $fv['options']]);
				case 2: return filter_input($input_type,$this->name(),$fv['filter'],['options' => $fv['options']]);
				case 1: return filter_input($input_type,$this->name(),$fv['filter'],$fv['flags']);
				case 0: return filter_input($input_type,$this->name(),$fv['filter']);
			endswitch;
		endif;
		return NULL;
	}
/**
 * Method to apply a filter to a value.
 * @param mixed $value The value to be tested.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return mixed Filter result.
 */
	public function validate_value($value,string $filter_name = 'ui') {
		$fv = $this->get_filter($filter_name);
		if(isset($fv)):
			$action  = (isset($fv['flags']) ? 1 : 0) + (isset($fv['options']) ? 2 : 0);
			switch($action):
				case 3: return filter_var($value,$fv['filter'],['flags' => $fv['flags'],'options' => $fv['options']]);
				case 2: return filter_var($value,$fv['filter'],['options' => $fv['options']]);
				case 1: return filter_var($value,$fv['filter'],$fv['flags']);
				case 0: return filter_var($value,$fv['filter']);
			endswitch;
		endif;
		return NULL;
	}
/**
 * Method to apply a filter to index 'name' of an array variable.
 * @param array $variable The variable to be tested.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return mixed Filter result.
 */
	public function validate_variable(array $variable,string $filter_name = 'ui') {
		if(array_key_exists($this->name(),$variable)):
			$value = $variable[$this->name()];
			$fv = $this->get_filter($filter_name);
			if(isset($fv)):
				$action  = (isset($fv['flags']) ? 1 : 0) + (isset($fv['options']) ? 2 : 0);
				switch($action):
					case 3: return filter_var($value,$fv['filter'],['flags' => $fv['flags'],'options' => $fv['options']]);
					case 2: return filter_var($value,$fv['filter'],['options' => $fv['options']]);
					case 1: return filter_var($value,$fv['filter'],$fv['flags']);
					case 0: return filter_var($value,$fv['filter']);
				endswitch;
			endif;
		endif;
		return NULL;
	}
}
class properties_list extends properties {
	public $v_option = NULL;
	
//	get/set methods
	public function options(array $value = NULL) {
		if(func_num_args() > 0):
			$this->v_option = $value;
		endif;
		return $this->v_option;
	}
/**
 * Method to apply the default class filter to a filter name.
 * The filter is a regex to match any of the option array keys.
 * @param string $filter_name Name of the filter, default = 'ui'.
 * @return object Returns $this.
 */
	public function filter_use_default(string $filter_name = 'ui') {
		$this->filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->filter_options(['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($this->v_option)))],$filter_name);
		return $this;
	}
}
