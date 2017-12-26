<?php
/*
	properties.php

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
require_once 'util.inc';

abstract class properties {
	protected $owner = NULL;
	protected $v_id = NULL;
	protected $v_name = NULL;
	protected $v_title = NULL;
	protected $v_description = NULL;
	protected $v_caption = NULL;
	protected $v_defaultvalue = NULL;
	protected $v_editableonadd = NULL;
	protected $v_editableonmodify = NULL;
	protected $v_filter = [];
	protected $v_message_error = NULL;
	protected $v_message_info = NULL;
	protected $v_message_warning = NULL;

	public function __construct($owner = NULL) {
		$this->setOwner($owner);
		return $this;
	}
	abstract public function filter_use_default();
	public function setOwner($owner = NULL) {
		if(is_object($owner)):
			$this->owner = $owner;
		endif;
		return $this;
	}
	public function getOwner() {
		return $this->owner;
	}
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
	public function set_description($value = NULL) {
		$this->v_description = $value;
		return $this;
	}
	public function get_description() {
		return $this->v_description;
	}
	public function set_caption(string $value = NULL) {
		$this->v_caption = $value;
		return $this;
	}
	public function get_caption() {
		return $this->v_caption;
	}
	public function set_defaultvalue($value = NULL) {
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
 * @param int $value Filter type.
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
 * Method to apply filter to an input element.
 * A list of filters will be processed until a filter does not return NULL.
 * @param int $input_type Input type. Check the PHP manual for supported input types.
 * @param mixed $filter Single filter name or list of filters names to validate, default is ['ui'].
 * @return mixed Filter result.
 */
	public function validate_input(int $input_type = INPUT_POST,$filter = ['ui']) {
		$result = NULL;
		$filter_names = [];
		if(is_array($filter)):
			$filter_names = $filter;
		elseif(is_string($filter)):
			$filter_names = [$filter];
		endif;
		foreach($filter_names as $filter_name):
			if(is_string($filter_name)):
				$filter_parameter = $this->get_filter($filter_name);
				if(isset($filter_parameter)):
					$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
					switch($action):
						case 3:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
							break;
						case 2:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter'],['options' => $filter_parameter['options']]);
							break;
						case 1:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter'],$filter_parameter['flags']);
							break;
						case 0:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter']);
							break;
					endswitch;
				endif;
				if(isset($result)):
					break; // foreach
				endif;
			endif;
		endforeach;
		return $result;
	}
/**
 * Method to validate a value.
 * A list of filters will be processed until a filter does not return NULL.
 * @param mixed $value The value to be tested.
 * @param mixed $filter Single filter name or list of filters names to validate, default is ['ui'].
 * @return mixed Filter result.
 */
	public function validate_value($value,$filter = ['ui']) {
		$result = NULL;
		$filter_names = [];
		if(is_array($filter)):
			$filter_names = $filter;
		elseif(is_string($filter)):
			$filter_names = [$filter];
		endif;
		foreach($filter_names as $filter_name):
			if(is_string($filter_name)):
				$filter_parameter = $this->get_filter($filter_name);
				if(isset($filter_parameter)):
					$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
					switch($action):
						case 3:
							$result = filter_var($value,$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
							break;
						case 2:
							$result = filter_var($value,$filter_parameter['filter'],['options' => $filter_parameter['options']]);
							break;
						case 1:
							$result = filter_var($value,$filter_parameter['filter'],$filter_parameter['flags']);
							break;
						case 0:
							$result = filter_var($value,$filter_parameter['filter']);
							break;
					endswitch;
				endif;
				if(isset($result)):
					break; // foreach
				endif;
			endif;
		endforeach;
		return $result;
	}
/**
 * Method to validate an array value. Index is the name property of $this.
 * @param array $variable The variable to be tested.
 * @param mixed $filter Single filter name or list of filters names to validate, default is ['ui'].
 * @return mixed Filter result.
 */
	public function validate_array_element(array $variable,$filter = ['ui']) {
		$result = NULL;
		$filter_names = [];
		if(is_array($filter)):
			$filter_names = $filter;
		elseif(is_string($filter)):
			$filter_names = [$filter];
		endif;
		if(array_key_exists($this->get_name(),$variable)):
			$value = $variable[$this->get_name()];
		else:
			$value = NULL;
		endif;
		foreach($filter_names as $filter_name):
			if(is_string($filter_name)):
				$filter_parameter = $this->get_filter($filter_name);
				if(isset($filter_parameter)):
					$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
					switch($action):
						case 3:
							$result = filter_var($value,$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
							break;
						case 2:
							$result = filter_var($value,$filter_parameter['filter'],['options' => $filter_parameter['options']]);
							break;
						case 1:
							$result = filter_var($value,$filter_parameter['filter'],$filter_parameter['flags']);
							break;
						case 0:
							$result = filter_var($value,$filter_parameter['filter']);
							break;
					endswitch;
				endif;
				if(isset($result)):
					break; // foreach
				endif;
			endif;
		endforeach;
		return $result;
	}
}
class properties_text extends properties {
	public $v_maxlength = 0;
	public $v_placeholder = NULL;
	public $v_size = 40;
	
	public function set_maxlength(int $value = 0) {
		$this->v_maxlength = $value;
		return $this;
	}
	public function get_maxlength() {
		return $this->v_maxlength;
	}
	public function set_placeholder(string $value = NULL) {
		$this->v_placeholder = $value;
		return $this;
	}
	public function get_placeholder() {
		return $this->v_placeholder;
	}
	public function set_size(int $value = 40) {
		$this->v_size = $value;
		return $this;
	}
	public function get_size() {
		return $this->v_size;
	}
/**
 * Method to apply the default class filter to a filter name.
 * Filter expects a string containing at least one non-whitespace character.
 * @param string $filter_name Name of the filter, default = 'ui'.
 * @return object Returns $this.
 */
	public function filter_use_default() {
		//	not empty, does contain at least one printable character
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL,'regexp' => '/\S/'],$filter_name);
		return $this;
	}
}
class properties_ipaddress extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_maxlength(45);
		$this->set_placeholder(gtext('Enter IP Address'));
		$this->set_size(60);
		return $this;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_IP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL],$filter_name);
		return $this;
	}
}
class properties_ipv4 extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_maxlength(15);
		$this->set_placeholder(gtext('Enter IP Address'));
		$this->set_size(20);
		return $this;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_IP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR | FILTER_FLAG_IPV4,$filter_name);
		$this->set_filter_options(['default' => NULL],$filter_name);
		return $this;
	}
}
class properties_ipv6 extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_maxlength(45);
		$this->set_placeholder(gtext('Enter IP Address'));
		$this->set_size(60);
		return $this;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_IP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR | FILTER_FLAG_IPV6,$filter_name);
		$this->set_filter_options(['default' => NULL],$filter_name);
		return $this;
	}
}
class properties_int extends properties_text {
	public $v_min = NULL;
	public $v_max = NULL;

	public function set_min(int $value = NULL) {
		$this->v_min = $value;
		return $this;
	}
	public function get_min() {
		return $this->v_min;
	}
	public function set_max(int $value = NULL) {
		$this->v_max = $value;
		return $this;
	}
	public function get_max() {
		return $this->v_max;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$options = [];
		$options['default'] = NULL;
		$min = $this->get_min();
		if(isset($min)):
			$options['min_range'] = $min;
		endif;
		$max = $this->get_max();
		if(isset($max)):
			$options['max_range'] = $max;
		endif;
		$this->set_filter(FILTER_VALIDATE_INT,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options($options,$filter_name);
		return $this;
	}
}
class property_uuid extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_id('uuid');
		$this->set_name('uuid');
		$this->set_title(gtext('Universally Unique Identifier'));
		$this->set_description(gtext('The UUID of the record.'));
		$this->set_size(45);
		$this->set_maxlength(36);
		$this->set_placeholder(gtext('Enter Universally Unique Identifier'));
		$this->filter_use_default();
		$this->set_editableonadd(false);
		$this->set_editableonmodify(false);
		$this->set_message_error(sprintf('%s: %s',$this->get_title(),gtext('The value is invalid.')));
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL,'regexp' => '/^[\da-f]{4}([\da-f]{4}-){2}4[\da-f]{3}-[89ab][\da-f]{3}-[\da-f]{12}$/i'],$filter_name);
		return $this;
	}
	public function get_defaultvalue() {
		return uuid();
	}
}
class properties_list extends properties {
	public $v_options = NULL;
	
	public function set_options(array $value = NULL) {
		$this->v_options = $value;
		return $this;
	}
	public function get_options() {
		return $this->v_options;
	}
	public function validate_option($option) {
		if(array_key_exists($option,$this->get_options())):
			return $option;
		else:
			return NULL;
		endif;
	}
/**
 * Method to apply the default class filter to a filter name.
 * The filter is a regex to match any of the option array keys.
 * @param string $filter_name Name of the filter, default = 'ui'.
 * @return object Returns $this.
 */
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_CALLBACK,$filter_name);
		$this->set_filter_options([$this,'validate_option'],$filter_name);
		return $this;
	}
}
class properties_bool extends properties {
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_BOOLEAN,$filter_name);
		$this->set_filter_flags(FILTER_NULL_ON_FAILURE,$filter_name);
		$this->set_filter_options(['default' => false],$filter_name);
		return $this;
	}
	public function validate_config(array $variable) {
		if(array_key_exists($this->get_name(),$variable)):
			$value = $variable[$this->get_name()];
			return (is_bool($value) ? $value : true);
		else:
			return false;
		endif;
	}
}
class property_enable extends properties_bool {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_id('enable');
		$this->set_name('enable');
		$this->set_title(gtext('Enable Setting'));
		$this->set_caption(gtext('Enable'));
		$this->set_description('');
		$this->set_defaultvalue(true);
		$this->filter_use_default();
		$this->set_editableonadd(true);
		$this->set_editableonmodify(true);
		$this->set_message_error(sprintf('%s: %s',$this->get_title(),gtext('The value is invalid.')));
		return $this;
	}
}
class property_protected extends properties_bool {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_id('protected');
		$this->set_name('protected');
		$this->set_title(gtext('Protect Setting'));
		$this->set_caption(gtext('Protect'));
		$this->set_description('');
		$this->set_defaultvalue(false);
		$this->filter_use_default();
		$this->set_editableonadd(true);
		$this->set_editableonmodify(true);
		$this->set_message_error(sprintf('%s: %s',$this->get_title(),gtext('The value is invalid.')));
		return $this;
	}
}
