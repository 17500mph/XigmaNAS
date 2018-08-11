<?php
/*
	properties_access_users.php

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

class access_users_properties extends co_property_container_param {
	protected $x_user;
	public function get_user() {
		return $this->x_user ?? $this->init_user();
	}
	public function init_user() {
		$property = $this->x_user = new property_text($this);
		$property->
			set_name('login')->
			set_title(gettext('User'));
		return $property;
	}
	protected $x_fullname;
	public function get_fullname() {
		return $this->x_fullname ?? $this->init_fullname();
	}
	public function init_fullname() {
		$property = $this->x_fullname = new property_text($this);
		$property->
			set_name('fullname')->
			set_title(gettext('Full Name'));
		return $property;
	}
	protected $x_uid;
	public function get_uid() {
		return $this->x_uid ?? $this->init_uid();
	}
	public function init_uid() {
		$property = $this->x_uid = new property_text($this);
		$property->
			set_name('id')->
			set_title(gettext('UID'));
		return $property;
	}
	protected $x_group;
	public function get_group() {
		return $this->x_group ?? $this->init_group();
	}
	public function init_group() {
		$property = $this->x_group = new property_text($this);
		$property->
			set_name('group')->
			set_title(gettext('Group'));
		return $property;
	}
	protected $x_primary_group;
	public function get_primary_group() {
		return $this->x_primary_group ?? $this->init_primary_group();
	}
	public function init_primary_group() {
		$property = $this->x_primary_group = new property_text($this);
		$property->
			set_name('primarygroup')->
			set_title(gettext('Primary Group'));
		return $property;
	}
}
