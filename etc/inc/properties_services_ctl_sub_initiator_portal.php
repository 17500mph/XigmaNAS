<?php
/*
	properties_services_ctl_auth_group_initiator_portal.php

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

class ctl_sub_initiator_portal_properties extends co_property_container_param {
	protected $x_ipaddress;
	public function get_ipaddress() {
		return $this->x_ipaddress ?? $this->init_ipaddress();
	}
	public function init_ipaddress() {
		$property = $this->x_ipaddress = new property_ipaddress($this);
		$property->
			set_name('ipaddress')->
			set_title(gettext('IP Address'));
		return $property;
	}
	protected $x_prefixlen;
	public function get_prefixlen() {
		return $this->x_prefixlen ?? $this->init_prefixlen();
	}
	public function init_prefixlen() {
		$property = $this->x_prefixlen = new property_int($this);
		$property->
			set_name('prefixlen')->
			set_title(gettext('IP Address Prefix'));
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
			set_title(gettext('Auth Group'));
		return $property;
	}
}
class ctl_sub_initiator_portal_edit_properties extends ctl_sub_initiator_portal_properties {
	public function init_ipaddress() {
		$property = parent::init_ipaddress();
		$description = gettext('An IPv4 or IPv6 address of an iSCSI initiator portal.');
		$placeholder = gettext('IP Address');
		$property->
			set_id('ipaddress')->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_prefixlen() {
		$property = parent::init_prefixlen();
		$description = gettext('Enter IP address prefix length.');
		$placeholder = '';
		$property->
			set_id('prefixlen')->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			set_size(10)->
			set_maxlength(3)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_min(0)->
			set_max(128)->
			filter_use_default_or_empty()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_group() {
		$property = parent::init_group();
		$description = gettext('Select auth groups.');
		$options = [];
		$property->
			set_id('group')->
			set_description($description)->
			set_defaultvalue([])->
			set_options($options)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
}
