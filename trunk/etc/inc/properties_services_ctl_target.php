<?php
/*
	properties_services_ctl_target.php

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
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the XigmaNAS Project.
*/
require_once 'properties.php';

class ctl_target_properties extends co_property_container_param {
	protected $x_name;
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$property = $this->x_name = new property_text($this);
		$property->
			set_name('name')->
			set_title(gtext('Target Name'));
		return $property;
	}
	protected $x_alias;
	public function get_alias() {
		return $this->x_alias ?? $this->init_alias();
	}
	public function init_alias() {
		$property = $this->x_alias = new property_text($this);
		$property->
			set_name('alias')->
			set_title(gtext('Alias'));
		return $property;
	}
	protected $x_auth_group;
	public function get_auth_group() {
		return $this->x_auth_group ?? $this->init_auth_group();
	}
	public function init_auth_group() {
		$property = $this->x_auth_group = new property_list($this);
		$property->
			set_name('auth_group')->
			set_title(gtext('Auth Group'));
		return $property;
	}
	protected $x_portal_group;
	public function get_portal_group() {
		return $this->x_portal_group ?? $this->init_portal_group();
	}
	public function init_portal_group() {
		$property = $this->x_portal_group = new property_list($this);
		$property->
			set_name('portal_group')->
			set_title(gtext('Portal Group'));
		return $property;
	}
	protected $x_redirect;
	public function get_redirect() {
		return $this->x_redirect ?? $this->init_redirect();
	}
	public function init_redirect() {
		$property = $this->x_redirect = new property_ipaddress($this);
		$property->
			set_name('redirect')->
			set_title(gtext('Redirect'));
		return $property;
	}
	protected $x_auxparam;
	public function get_auxparam() {
		return $this->x_auxparam ?? $this->init_auxparam();
	}
	public function init_auxparam() {
		$property = $this->x_auxparam = new property_textarea($this);
		$property->
			set_name('auxparam')->
			set_title(gtext('Additional Parameter'));
		return $property;
	}
}
class ctl_target_edit_properties extends ctl_target_properties {
	public function init_name() {
		$property = parent::init_name();
		$description = gtext('Name of the target.');
		$placeholder = gtext('Name');
		$regexp = '/^(?:iqn|eui|naa)\.\S{1,219}$/';
		$property->
			set_id('name')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(223)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_auxparam() {
		$property = parent::init_auxparam();
		$description = gtext('These parameter will be added to this target.');
		$placeholder = gtext('Enter additional parameter');
		$property->
			set_id('auxparam')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_alias() {
		$property = parent::init_alias();
		$description = gtext('Assign a human-readable description to the target.');
		$placeholder = gtext('Alias');
		$regexp = '/^.{0,128}$/';
		$property->
			set_id('alias')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(128)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_auth_group() {
		$property = parent::init_auth_group();
		$description = gtext('Assign a previously defined authentication group to the target.');
		$options = [
			'' => gtext('Deny discovery'),
			'no-authentication' => gtext('Permit access without authentication')
		];
		$property->
			set_id('auth_group')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_portal_group() {
		$property = parent::init_portal_group();
		$description = gtext('Assign a previously defined portal group to the target.');
		$options = [
			'' => gtext('Default')
		];
		$property->
			set_id('portal_group')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_redirect() {
		$property = parent::init_redirect();
		$description = gtext('IPv4 or IPv6 address to redirect initiators to. When configured, all initiators attempting to connect to this target will get redirected using "Target moved temporarily" login response.');
		$property->
			set_id('redirect')->
			set_description($description)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			filter_use_empty()->
			set_filter_group('ui',['empty','ui'])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}