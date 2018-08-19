<?php
/*
	properties_smartmontools_umass.php

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

class smartmontools_umass_properties extends co_property_container_param {
	protected $x_name;
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$property = $this->x_name = new property_text($this);
		$property->
			set_name('name')->
			set_title(gettext('Identifier'));
		return $property;
	}
	protected $x_type;
	public function get_type() {
		return $this->x_type ?? $this->init_type();
	}
	public function init_type() {
		$property = $this->x_type = new property_text($this);
		$property->
			set_name('type')->
			set_title(gettext('Type'));
		return $property;
	}
}
class smartmontools_umass_edit_properties extends smartmontools_umass_properties {
	public function init_name() {
		$property = parent::init_name();
		$property->
			set_id('name')->
			set_description(gettext('The identifier reported as unknown by smartctl including brackets.'))->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(64)->
			set_placeholder(gettext('Enter Identifier'))->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_type() {
		$property = parent::init_type();
		$property->
			set_id('type')->
			set_description('')->
			set_defaultvalue('sat')->
			set_size(60)->
			set_maxlength(64)->
			set_placeholder(gettext('Enter smartctl pass-through type.'))->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => '/.*/'])->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
}
