<?php
/*
	properties_syslogconf.php

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
/*
 * To activate a property:
 * - Enable property variable.
 * - Enable init call in method load.
 * - Enable property method.
 */
class properties_syslogconf {
	public $comment;
	public $enable;
	public $facility;
	public $level;
	public $protected;
	public $uuid;
	public $value;

	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->comment = $this->prop_comment();
		$this->enable = $this->prop_enable();
		$this->facility = $this->prop_facility();
		$this->level = $this->prop_level();
		$this->protected = $this->prop_protected();
		$this->uuid = $this->prop_uuid();
		$this->value = $this->prop_value();
		return $this;
	}
	private function prop_comment(): properties_text {
		$o = new properties_text($this);
		$o->set_id('comment');
		$o->set_name('comment');
		$o->set_title(gtext('Description'));
		$o->set_description('');
		$o->set_placeholder(gtext('Enter a description'));
		$o->set_defaultvalue('');
		$o->set_size(60);
		$o->set_maxlength(80);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_filter(FILTER_UNSAFE_RAW);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => '']);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_enable(): property_enable {
		$o = new property_enable($this);
		return $o;
	}
	private function prop_facility(): properties_text {
		$o = new properties_text($this);
		$o->set_id('facility');
		$o->set_name('facility');
		$o->set_title(gtext('Facility'));
		$o->set_description('');
		$o->set_placeholder(gtext('Enter facility name'));
		$o->set_defaultvalue('');
		$o->set_size(60);
		$o->set_maxlength(80);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->filter_use_default();
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_level(): properties_text {
		$o = new properties_text($this);
		$o->set_id('level');
		$o->set_name('level');
		$o->set_title(gtext('Level'));
		$o->set_description('');
		$o->set_placeholder(gtext('Enter level name'));
		$o->set_defaultvalue('');
		$o->set_size(60);
		$o->set_maxlength(80);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->filter_use_default();
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_protected(): property_protected {
		$o = new property_protected($this);
		return $o;
	}
	private function prop_uuid(): property_uuid {
		$o = new property_uuid($this);
		return $o;
	}
	private function prop_value(): properties_text {
		$o = new properties_text($this);
		$o->set_id('value');
		$o->set_name('value');
		$o->set_title(gtext('Destination'));
		$o->set_description('');
		$o->set_placeholder(gtext('Enter destination'));
		$o->set_defaultvalue('');
		$o->set_size(60);
		$o->set_maxlength(80);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->filter_use_default();
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
}
