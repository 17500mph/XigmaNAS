<?php
/*
	properties_smartmontools_umass.php

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

class properties_smartmontools_umass {
	public $description;
	public $enable;
	public $name;
	public $protected;
	public $type;
	public $uuid;

	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->description = $this->prop_description();
		$this->enable = $this->prop_enable();
		$this->name = $this->prop_name();
		$this->protected = $this->prop_protected();
		$this->type = $this->prop_type();
		$this->uuid = $this->prop_uuid();
		return $this;
	}
	private function prop_description(): properties_text {
		$o = new properties_text($this);
		$o->set_id('description');
		$o->set_name('description');
		$o->set_title(gtext('Description'));
//		$o->set_description('');
		$o->set_defaultvalue('');
		$o->set_size(60);
		$o->set_maxlength(256);
		$o->set_placeholder(gtext('Enter a description for your reference'));
		$o->set_filter(FILTER_VALIDATE_REGEXP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'regexp' => '/.*/']);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_enable(): property_enable {
		$o = new property_enable($this);
		return $o;
	}
	private function prop_name(): properties_text {
		$o = new properties_text($this);
		$o->set_id('name');
		$o->set_name('name');
		$o->set_title(gtext('Identifier'));
		$o->set_description(gtext('The identifier reported as unknown by smartctl including brackets.'));
		$o->set_defaultvalue('');
		$o->set_size(60);
		$o->set_maxlength(64);
		$o->set_placeholder(gtext('Enter Identifier'));
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_protected(): property_protected {
		$o = new property_protected($this);
		return $o;
	}
	private function prop_type(): properties_text {
		$o = new properties_text($this);
		$o->set_id('type');
		$o->set_name('type');
		$o->set_title(gtext('Type'));
//		$o->set_description('');
		$o->set_defaultvalue('sat');
		$o->set_size(60);
		$o->set_maxlength(64);
		$o->set_placeholder(gtext('Enter smartctl pass-through type.'));
		$o->set_filter(FILTER_VALIDATE_REGEXP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'regexp' => '/.*/']);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_uuid(): property_uuid {
		$o = new property_uuid($this);
		return $o;
	}
}
?>