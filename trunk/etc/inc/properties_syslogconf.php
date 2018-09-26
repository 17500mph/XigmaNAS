<?php
/*
	properties_syslogconf.php

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

class properties_syslogconf extends co_property_container_param {
	protected $x_comment;
	public function init_comment() {
		$property = $this->x_comment = new property_text($this);
		$property->
			set_name('comment')->
			set_title(gettext('Description'));
		return $property;
	}
	public function get_comment() {
		return $this->x_comment ?? $this->init_comment();
	}
	protected $x_facility;
	public function init_facility() {
		$property = $this->x_facility= new property_text($this);
		$property->
			set_name('facility')->
			set_title(gettext('Facility'));
		return $property;
	}
	public function get_facility() {
		return $this->x_facility ?? $this->init_facility();
	}
	protected $x_level;
	public function init_level() {
		$property = $this->x_level = new property_text($this);
		$property->
			set_name('level')->
			set_title(gettext('Level'));
		return $property;
	}
	public function get_level() {
		return $this->x_level ?? $this->init_level();
	}
	protected $x_value;
	public function init_value() {
		$property = $this->x_value = new property_text($this);
		$property->
			set_name('value')->
			set_title(gettext('Destination'));
		return $property;
	}
	public function get_value() {
		return $this->x_value ?? $this->init_value();
	}
}
class properties_syslogconf_edit extends properties_syslogconf {
	public function init_comment() {
		$property = parent::init_comment();
		$description = '';
		$placeholder = gettext('Enter a description');
		$property->
			set_id('comment')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_UNSAFE_RAW)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => ''])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_facility() {
		$property = parent::init_facility();
		$description = '';
		$placeholder = gettext('Enter facility name');
		$property->
			set_id('facility')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_level() {
		$property = parent::init_level();
		$description = '';
		$placeholder = gettext('Enter level name');
		$property->
			set_id('level')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_value() {
		$property = parent::init_value();
		$description = '';
		$placeholder = gettext('Enter destination');
		$property->
			set_id('value')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
}
