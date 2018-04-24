<?php
/*
	properties_services_ctl_auth_group_chap.php

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
class ctl_auth_group_chap_properties extends co_property_container_param {
	protected $x_user;
	public function init_user() {
		$property = new property_text($this);
		$property->
			set_name('user')->
			set_title(gtext('User'));
		return $property;
	}
	public function get_user() {
		return $this->x_user ?? $this->init_user();
	}
	protected $x_secret;
	public function init_secret() {
		$property = new property_text($this);
		$property->
			set_name('secret')->
			set_title(gtext('Secret'));
		return $property;
	}
	public function get_secret() {
		return $this->x_secret ?? $this->init_secret();
	}
}
class ctl_ag_chap_edit_properties extends ctl_ag_chap_properties {
	public function test_user($value) {
		if(1 === preg_match('/^\S[1,16}$/',$value)):
			return $value;
		else:
			return NULL;
		endif;
	}
	public function get_user() {
		$property = parent::get_user();
		$description = gtext('Enter user name');
		$placeholder = gtext('User');
		$property->
			set_id('user')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(20)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_CALLBACK)->
			set_filter_options([$this,'test_user'])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function test_secret($value) {
		if(1 === preg_match('/^\S[1,16}$/',$value)):
			return $value;
		else:
			return NULL;
		endif;
	}
	public function get_secret() {
		$property = parent::get_secret();
		$description = gtext('Enter secret');
		$placeholder = gtext('Secret');
		$property->
			set_id('secret')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(20)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_CALLBACK)->
			set_filter_options([$this,'test_secret'])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}
