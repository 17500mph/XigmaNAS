<?php
/*
	properties_services_ctl_target_lun.php

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
require_once 'properties.php';

class ctl_target_lun_properties extends co_property_container_param {
	protected $x_number;
	public function get_number() {
		return $this->x_number ?? $this->init_number();
	}
	public function init_number() {
		$property = $this->x_number = new property_int($this);
		$property->
			set_name('number')->
			set_title(gtext('LUN Number'));
		return $property;
	}
	protected $x_name;
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$property = $this->x_name = new property_list($this);
		$property->
			set_name('name')->
			set_title(gtext('Target Name'));
		return $property;
	}
}
class ctl_target_lun_edit_properties extends ctl_target_lun_properties {
	public function init_number() {
		$property = parent::init_number();
		$description = gtext('LUN number');
		$property->
			set_id('number')->
			set_description($description)->
			set_size(10)->
			set_maxlength(4)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			set_min(0)->
			set_max(1023)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_name() {
		$property = parent::init_name();
		$description = gtext('Name of the target.');
		$options = [];
		$property->
			set_id('name')->
			set_description($description)->
			set_options($options)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}
