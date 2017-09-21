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
	protected $v_id = NULL;
	protected $v_name = NULL;
	protected $v_title = NULL;
	protected $v_description = NULL;
	protected $v_defaultvalue = NULL;
	protected $v_editableonadd = NULL;
	protected $v_editableonmodify = NULL;
	protected $v_filter = [];
	protected $v_message_error = NULL;
	protected $v_message_info = NULL;
	protected $v_message_warning = NULL;

//	get/set methods
	public function set_id(string $value = NULL) {
		$this->v_id = $value;
		return $this;
	}
	public function get_id() {
		return $this->v_id;
	}
	public function set_name(string $value = NULL) {
		$this->v_name = $value;
		return $this;
	}
	public function get_name() {
		return $this->v_name;
	}
	public function set_title(string $value = NULL) {
		$this->v_title = $value;
		return $this;
	}
	public function get_title() {
		return $this->v_title;
	}
	public function set_description(string $value = NULL) {
		$this->v_description = $value;
		return $this;
	}
	public function get_description() {
		return $this->v_description;
	}
	public function set_defaultvalue(string $value = NULL) {
		$this->v_defaultvalue = $value;
		return $this;
	}
	public function get_defaultvalue() {
		return $this->v_defaultvalue;
	}
	public function set_editableonadd(bool $value = NULL) {
		$this->v_editableonadd = $value;
		return $this;
	}
	public function get_editableonadd() {
		return $this->v_editableonadd;
	}
	public function set_editableonmodify(bool $value = NULL) {
		$this->v_editableonmodify = $value;
		return $this;
	}
	public function get_editableonmodify() {
		return $this->v_editableonmodify;
	}
	public function set_message_error(string $value = NULL) {
		$this->v_message_error = $value;
		return $this;
	}
	public function get_message_error() {
		return $this->v_message_error;
	}
	public function set_message_info(string $value = NULL) {
		$this->v_message_info = $value;
		return $this;
	}
	public function get_message_info() {
		return $this->v_message_info;
	}
	public function set_message_warning(string $value = NULL) {
		$this->v_message_warning = $value;
		return $this;
	}
	public function get_message_warning() {
		return $this->v_message_warning;
	}
/**
 * Method to set filter type.
 * @param type $value Filter type.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */	
	public function set_filter($value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(array_key_exists($filter_name,$this->v_filter)):
			$this->v_filter[$filter_name]['filter'] = $value;
		else:
			$this->v_filter[$filter_name] = ['filter' => $value,'flags' => NULL,'options' => NULL];
		endif;
		return $this;
	}
/**
 * Method to set filter flags.
 * @param type $value Flags for filter.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */
	public function set_filter_flags($value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(array_key_exists($filter_name,$this->v_filter)):
			$this->v_filter[$filter_name]['flags'] = $value;
		else:
			$this->v_filter[$filter_name] = ['filter' => NULL,'flags' => $value,'options' => NULL];
		endif;
		return $this;
	}
/**
 * Method to set filter options.
 * @param array $value Filter options.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */
	public function set_filter_options(array $value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(array_key_exists($filter_name,$this->v_filter)):
			$this->v_filter[$filter_name]['options'] = $value;
		else:
			$this->v_filter[$filter_name] = ['filter' => NULL,'flags' => NULL,'options' => $value];
		endif;
		return $this;
	}
/**
 * Method to apply the default class filter to a filter name.
 * Filter expects a string containing at least one non-whitespace character.
 * @param string $filter_name Name of the filter, default = 'ui'.
 * @return object Returns $this.
 */
	public function filter_use_default(string $filter_name = 'ui') {
		$this->set_filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL,'regexp' => '/\S/'],$filter_name);
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
		$filter_parameter = $this->get_filter($filter_name);
		if(isset($filter_parameter)):
			$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
			switch($action):
				case 3: return filter_input($input_type,$this->get_name(),$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
				case 2: return filter_input($input_type,$this->get_name(),$filter_parameter['filter'],['options' => $filter_parameter['options']]);
				case 1: return filter_input($input_type,$this->get_name(),$filter_parameter['filter'],$filter_parameter['flags']);
				case 0: return filter_input($input_type,$this->get_name(),$filter_parameter['filter']);
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
		$filter_parameter = $this->get_filter($filter_name);
		if(isset($filter_parameter)):
			$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
			switch($action):
				case 3: return filter_var($value,$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
				case 2: return filter_var($value,$filter_parameter['filter'],['options' => $filter_parameter['options']]);
				case 1: return filter_var($value,$filter_parameter['filter'],$filter_parameter['flags']);
				case 0: return filter_var($value,$filter_parameter['filter']);
			endswitch;
		endif;
		return NULL;
	}
/**
 * Method to apply a filter to index [$name] of an array variable.
 * @param array $variable The variable to be tested.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return mixed Filter result.
 */
	public function validate_array_element(array $variable,string $filter_name = 'ui') {
		if(array_key_exists($this->get_name(),$variable)):
			$value = $variable[$this->get_name()];
			$filter_parameter = $this->get_filter($filter_name);
			if(isset($filter_parameter)):
				$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
				switch($action):
					case 3: return filter_var($value,$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
					case 2: return filter_var($value,$filter_parameter['filter'],['options' => $filter_parameter['options']]);
					case 1: return filter_var($value,$filter_parameter['filter'],$filter_parameter['flags']);
					case 0: return filter_var($value,$filter_parameter['filter']);
				endswitch;
			endif;
		endif;
		return NULL;
	}
}
class properties_list extends properties {
	public $v_options = NULL;
	
//	get/set methods
	public function set_options(array $value = NULL) {
		$this->v_options = $value;
		return $this;
	}
	public function get_options() {
		return $this->v_options;
	}
/**
 * Method to apply the default class filter to a filter name.
 * The filter is a regex to match any of the option array keys.
 * @param string $filter_name Name of the filter, default = 'ui'.
 * @return object Returns $this.
 */
	public function filter_use_default(string $filter_name = 'ui') {
		$this->set_filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($this->v_options)))],$filter_name);
		return $this;
	}
}
class properties_bool extends properties {
	public $v_caption = NULL;
	
	public function set_defaultvalue(bool $value = NULL) {
		$this->v_defaultvalue = $value;
		return $this;
	}
	public function set_caption(string $value = NULL) {
		$this->v_caption = $value;
		return $this;
	}
	public function get_caption() {
		return $this->v_caption;
	}
	public function filter_use_default(string $filter_name = 'ui') {
		$this->set_filter(FILTER_VALIDATE_BOOLEAN,$filter_name);
//		$this->set_filter_flags(FILTER_NULL_ON_FAILURE,$filter_name);
//		$this->set_filter_options(['default' => false],$filter_name);
		return $this;
	}
}