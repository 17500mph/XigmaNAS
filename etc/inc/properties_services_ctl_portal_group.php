<?php
/*
	properties_services_ctl_portal_group.php

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

class ctl_portal_group_properties extends co_property_container_param {
	protected $x_name;
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$property = $this->x_name = new property_text($this);
		$property->
			set_name('name')->
			set_title(gtext('Portal Group Name'));
		return $property;
	}
	protected $x_discovery_auth_group;
	public function get_discovery_auth_group() {
		return $this->x_discovery_auth_group ?? $this->init_discovery_auth_group();
	}
	public function init_discovery_auth_group() {
		$property = $this->x_discovery_auth_group = new property_list($this);
		$property->
			set_name('discovery_auth_group')->
			set_title(gtext('Discovery Auth Group'));
		return $property;
	}
	protected $x_discovery_filter;
	public function get_discovery_filter() {
		return $this->x_discovery_filter ?? $this->init_discovery_filter();
	}
	public function init_discovery_filter() {
		$property = $this->x_discovery_filter = new property_list($this);
		$property->
			set_name('discovery_filter')->
			set_title(gtext('Discovery Filter'));
		return $property;
	}
	protected $x_offload;
	public function get_offload() {
		return $this->x_offload ?? $this->init_offload();
	}
	public function init_offload() {
		$property = $this->x_offload = new property_text($this);
		$property->
			set_name('offload')->
			set_title(gtext('Offload'));
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
	protected $x_tag;
	public function get_tag() {
		return $this->x_tag ?? $this->init_tag();
	}
	public function init_tag() {
		$property = $this->x_tag = new property_int($this);
		$property->
			set_name('tag')->
			set_title(gtext('Tag'));
		return $property;
	}
	protected $x_foreign;
	public function get_foreign() {
		return $this->x_foreign ?? $this->init_foreign();
	}
	public function init_foreign() {
		$property = $this->x_foreign = new property_bool($this);
		$property->
			set_name('foreign')->
			set_title(gtext('Foreign'));
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
class ctl_portal_group_edit_properties extends ctl_portal_group_properties {
	public function init_name() {
		$property = parent::init_name();
		$description = gtext('Name of the Portal Group.');
		$placeholder = gtext('Portal Group Name');
		$regexp = '/^\S{1,223}$/';
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
		$description = gtext('These parameter will be added to this portal-group.');
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
	public function init_discovery_auth_group() {
		$property = parent::init_discovery_auth_group();
		$description = gtext('Assign a previously defined authentication group to the portal group, to be used for target discovery.');
		$options = [
			'' => gtext('Deny discovery'),
			'no-authentication' => gtext('Permit discovery without authentication')
		];
		$property->
			set_id('discovery_auth_group')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_discovery_filter() {
		$property = parent::init_discovery_filter();
		$description = gtext('Determines which targets are returned during discovery.');
		$options = [
			'' => gtext('Discovery will return all targets assigned to this portal group.'),
			'portal' => gtext('Discovery will not return targets that cannot be accessed by the initiator because of their initiator-portal.'),
			'portal-name' => gtext('The check will include both initiator-portal and initiator-name.'),
			'portal-name-auth' => gtext('The check will include initiator-portal, initiator-name, and authentication credentials. The target is returned if it does not require CHAP authentication, or if the CHAP user and secret used during discovery match those used by the target.')
		];
		$property->
			set_id('discovery_filter')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_foreign() {
		$property = parent::init_foreign();
		$caption = gtext('Specifies that this portal-group is listened by some other host. This host will announce it on discovery stage, but won\'t listen.');
		$property->
			set_id('foreign')->
			set_caption($caption)->
			set_defaultvalue(false)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_offload() {
		$property = parent::init_offload();
		$description = gtext('Define iSCSI hardware offload driver to use for this portal-group. The default is "none".');
		$placeholder = gtext('Driver');
		$regexp = '/^\S{0,60}$/';
		$property->
			set_id('offload')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(60)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_redirect() {
		$property = parent::init_redirect();
		$description = gtext('IPv4 or IPv6 address to redirect initiators to. When configured, all initiators attempting to connect to portal belonging to this portal-group will get redirected using "Target moved temporarily" login response.');
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
	public function init_tag() {
		$property = parent::init_tag();
		$description = gtext('Unique 16-bit tag value of this portal-group. If not specified, the value is generated automatically.');
		$placeholder = gtext('Tag');
		$property->
			set_id('tag')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(20)->
			set_maxlength(20)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_min(0)->
			set_max(65535)->
			filter_use_default()->
			filter_use_empty()->
			set_filter_group('ui',['empty','ui'])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}
