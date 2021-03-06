<?php
/*
	properties_services_ctl.php

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

class ctl_properties extends co_property_container {
	protected $x_enable;
	public function get_enable() {
		return $this->x_enable ?? $this->init_enable();
	}
	public function init_enable() {
		$property = $this->x_enable = new property_enable($this);
		$property->
			set_defaultvalue(false);
		return $property;
	}
	protected $x_debug;
	public function get_debug() {
		return $this->x_debug ?? $this->init_debug();	
	}
	public function init_debug() {
		$property = $this->x_debug = new property_int($this);
		$description = gettext('The debug verbosity level. The default is 0.');
		$placeholder = '0';
		$property->
			set_name('debug')->
			set_title(gettext('Debug Level'));
		$property->
			set_id('debug')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_maxlength(5)->
			set_size(4)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			set_min(0)->
			set_max(99)->
			filter_use_default_or_empty()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	protected $x_maxproc;
	public function get_maxproc() {
		return $this->x_maxproc ?? $this->init_maxproc();
	}
	public function init_maxproc() {
		$property = $this->x_maxproc = new property_int($this);
		$description = gettext('The limit for concurrently running child processes handling incoming connections. The default is 30. A setting of 0 disables the limit.');
		$placeholder = '30';
		$property->
			set_name('maxproc')->
			set_title(gettext('Max Processes'));
		$property->
			set_id('maxproc')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_size(10)->
			set_maxlength(5)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			set_min(0)->
			set_max(65535)->
			filter_use_default_or_empty()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	protected $x_timeout;
	public function get_timeout() {
		return $this->x_timeout ?? $this->init_timeout();
	}
	public function init_timeout() {
		$property = $this->x_timeout = new property_int($this);
		$description = gettext('The timeout for login sessions, after which the connection will be forcibly terminated. The default is 60. A setting of 0 disables the timeout.');
		$placeholder = '60';
		$property->
			set_name('timeout')->
			set_title(gettext('Timeout'));
		$property->
			set_id('timeout')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_size(10)->
			set_maxlength(5)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			set_min(0)->
			set_max(65535)->
			filter_use_default_or_empty()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	protected $x_isns_period;
	public function get_isns_period() {
		return $this->x_isns_period ?? $this->init_isns_period();
	}
	public function init_isns_period() {
		$property = $this->x_isns_period = new property_int($this);
		$description = gettext('iSNS registration period. Registered Network Entity not updated during this period will be unregistered. The default is 900.');
		$placeholder = '900';
		$property->
			set_name('isns_period')->
			set_title(gettext('iSNS Period'));
		$property->
			set_id('isns_period')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_maxlength(5)->
			set_size(10)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			set_min(0)->
			set_max(65535)->
			filter_use_default_or_empty()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	protected $x_isns_timeout;
	public function get_isns_timeout() {
		return $this->x_isns_timeout ?? $this->init_isns_timeout();
	}
	public function init_isns_timeout() {
		$property = $this->x_isns_timeout = new property_int($this);
		$description = gettext('Timeout for iSNS requests. The default is 5.');
		$placeholder = '5';
		$property->
			set_name('isns_timeout')->
			set_title(gettext('iSNS Timeout'));
		$property->
			set_id('isns_timeout')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_maxlength(5)->
			set_size(10)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			set_min(0)->
			set_max(65535)->
			filter_use_default_or_empty()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	protected $x_auxparam;
	public function get_auxparam() {
		return $this->x_auxparam ?? $this->init_auxparam();
	}
	public function init_auxparam() {
		$property = $this->x_auxparam = new property_textarea($this);
		$description = gettext('These parameter will be added to the global section of ctl.conf');
		$placeholder = gettext('Enter additional parameter');
		$property->
			set_name('auxparam')->
			set_title(gettext('Additional Parameter'));
		$property->
			set_id('auxparam')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
}
