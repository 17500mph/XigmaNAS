<?php
/*
	properties_services_tftp.php

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
class properties_tftp {
	public $allowfilecreation;
	public $dir;
	public $enable;
	public $extraoptions;
	public $maxblocksize;
	public $port;
	public $timeout;
	public $umask;
	public $username;

	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->allowfilecreation = $this->prop_allowfilecreation();
		$this->dir = $this->prop_dir();
		$this->enable = $this->prop_enable();
		$this->extraoptions = $this->prop_extraoptions();
		$this->maxblocksize = $this->prop_maxblocksize();
		$this->port = $this->prop_port();
		$this->timeout = $this->prop_timeout();
		$this->umask = $this->prop_umask();
		$this->username = $this->prop_username();
		return $this;
	}
	private function prop_allowfilecreation(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('allowfilecreation')->
			set_name('allowfilecreation')->
			set_title(gtext('Allow New Files'))->
			set_caption(gtext('Allow new files to be created.'))->
			set_description(gtext('By default, only already existing files can be uploaded.'))->
			set_defaultvalue(true)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_dir(): properties_text {
		global $g;
		$o = new properties_text($this);
		$o->set_id('dir')->
			set_name('dir')->
			set_title(gtext('Directory'))->
			set_description(gtext('The directory containing the files you want to publish. The remote host does not need to pass along the directory as part of the transfer.'))->
			set_placeholder(gtext('Enter a directory'))->
			set_defaultvalue($g['media_path'])->
			set_size(60)->
			set_maxlength(4096)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_UNSAFE_RAW)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => $g['media_path']])->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_enable(): property_enable {
		$o = new property_enable($this);
		$o->set_defaultvalue(false);
		return $o;
	}
	private function prop_extraoptions(): properties_text {
		$o = new properties_text($this);
		$o->set_id('extraoptions')->
			set_name('extraoptions')->
			set_title(gtext('Extra Options'))->
			set_description(gtext('Extra options (usually empty).'))->
			set_placeholder(gtext('Enter extra options'))->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(4096)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_UNSAFE_RAW)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => ''])->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_maxblocksize(): properties_int {
		$o = new properties_int($this);
		$o->set_id('maxblocksize')->
			set_name('maxblocksize')->
			set_title(gtext('Max. Block Size'))->
//			set_caption()->
			set_description(gtext('Specifies the maximum permitted block size. The permitted range for this parameter is from 512 to 65464.'))->
//			set_placeholder()->
			set_defaultvalue(16384)->
			set_size(6)->
			set_maxlength(5)->
			set_min(512)->
			set_max(65464)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_filter(FILTER_VALIDATE_REGEXP,'empty')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'empty')->
			set_filter_options(['default' => NULL,'regexp' => '/^$/'],'empty')->
			set_filter(FILTER_UNSAFE_RAW,'scalar')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'scalar')->
			set_filter_options(['default' => ''],'scalar')->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_port(): properties_int {
		$o = new properties_int($this);
		$o->set_id('port')->
			set_name('port')->
			set_title(gtext('Port'))->
			set_caption(gtext('Port of the TFTP service. Leave blank to use the default port.'))->
			set_description(gtext('Enter a custom port number if you do not want to use default port 69.'))->
			set_placeholder(gtext('69'))->
			set_defaultvalue(69)->
			set_size(6)->
			set_maxlength(5)->
			set_min(1024)->
			set_max(49151)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_filter(FILTER_VALIDATE_INT,'69')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'69')->
			set_filter_options(['default' => NULL,'min_range' => 69,'max_range' => 69],'69')->
			set_filter(FILTER_VALIDATE_REGEXP,'empty')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'empty')->
			set_filter_options(['default' => NULL,'regexp' => '/^$/'],'empty')->
			set_filter(FILTER_UNSAFE_RAW,'scalar')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'scalar')->
			set_filter_options(['default' => ''],'scalar')->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('Port number must be 69 or a number between 1024 and 49151.')));
		return $o;
	}
	private function prop_timeout(): properties_int {
		$o = new properties_int($this);
		$o->set_id('timeout')->
			set_name('timeout')->
			set_title(gtext('Timeout'))->
//			set_caption()->
			set_description(gtext('Determine the default timeout, in microseconds, before the first packet is retransmitted. The default is 1000000 (1 second).'))->
//			set_placeholder()->
			set_defaultvalue(1000000)->
			set_size(10)->
			set_maxlength(9)->
			set_min(10)->
			set_max(255000000)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_filter(FILTER_VALIDATE_REGEXP,'empty')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'empty')->
			set_filter_options(['default' => NULL,'regexp' => '/^$/'],'empty')->
			set_filter(FILTER_UNSAFE_RAW,'scalar')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'scalar')->
			set_filter_options(['default' => ''],'scalar')->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('Timeout must be a number between 10 and 255000000.')));
		return $o;
	}
	private function prop_umask(): properties_text {
		$o = new properties_text($this);
		$o->set_id('umask')->
			set_name('umask')->
			set_title(gtext('Umask'))->
			set_description(gtext('Sets the umask for newly created files to the specified value. The default is zero (anyone can read or write).'))->
			set_placeholder('000')->
			set_defaultvalue('0')->
			set_size(4)->
			set_maxlength(3)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => '/^(?:[0..7]{1,3}?$/'])->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_username(): properties_list {
		$o = new properties_list($this);
		$o->set_id('username')->
			set_name('username')->
			set_title(gtext('Username'))->
			set_caption(gtext('Specifies the username under which the TFTP service should run.'))->
			set_description('')->
			set_defaultvalue('nobody')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
}
