<?php
/*
	properties_services_ctl_sub_chap_mutual.php

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
require_once 'properties.php';

class ctl_sub_chap_mutual_properties extends co_property_container_param {
	protected $x_name;
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$property = $this->x_name = new property_text($this);
		$property->
			set_name('name')->
			set_title(gtext('User'));
		return $property;
	}
	protected $x_secret;
	public function get_secret() {
		return $this->x_secret ?? $this->init_secret();
	}
	public function init_secret() {
		$property = $this->x_secret = new property_text($this);
		$property->
			set_name('secret')->
			set_title(gtext('Secret'));
		return $property;
	}
	protected $x_group;
	public function get_group() {
		return $this->x_group ?? $this->init_group();
	}
	public function init_group() {
		$property = $this->x_group = new property_list_multi($this);
		$property->
			set_name('group')->
			set_title(gtext('Auth Group'));
		return $property;
	}
	protected $x_mutual_name;
	public function get_mutual_name() {
		return $this->x_mutual_name ?? $this->init_mutual_name();
	}
	public function init_mutual_name() {
		$property = $this->x_mutual_name = new property_text($this);
		$property->
			set_name('mutual_name')->
			set_title(gtext('Mutual User'));
		return $property;
	}
	protected $x_mutual_secret;
	public function get_mutual_secret() {
		return $this->x_mutual_secret ?? $this->init_mutual_secret();
	}
	public function init_mutual_secret() {
		$property = $this->x_mutual_secret = new property_text($this);
		$property->
			set_name('mutual_secret')->
			set_title(gtext('Mutual Secret'));
		return $property;
	}
}
class ctl_sub_chap_mutual_edit_properties extends ctl_sub_chap_mutual_properties {
	public function init_name() {
		$property = parent::init_name();
		$description = gtext('Enter user name.');
		$placeholder = gtext('User');
		$regexp = '/^\S{1,32}$/';
		$property->
			set_id('name')->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_maxlength(32)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_secret() {
		$property = parent::init_secret();
		$description = gtext('Enter secret.');
		$placeholder = gtext('Secret');
		$regexp = '/^[^"]{1,32}$/';
		$property->
			set_id('secret')->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_maxlength(32)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			filter_use_empty()->
			set_filter_group('ui',['empty','ui'])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_group() {
		$property = parent::init_group();
		$description = gtext('Select auth groups.');
		$options = [];
		$property->
			set_id('group')->
			set_description($description)->
			set_defaultvalue([])->
			set_options($options)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_mutual_name() {
		$property = parent::init_mutual_name();
		$description = gtext('Enter mutual user name.');
		$placeholder = gtext('Mutual User');
		$regexp = '/^\S{1,32}$/';
		$property->
			set_id('mutual_name')->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_maxlength(32)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			filter_use_empty()->
			set_filter_group('ui',['empty','ui'])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_mutual_secret() {
		$property = parent::init_mutual_secret();
		$description = 'Enter mutual secret';
		$placeholder = gtext('Mutual Secret');
		$regexp = '/^[^"]{1,32}$/';
		$property->
			set_id('secret')->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_maxlength(32)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}
