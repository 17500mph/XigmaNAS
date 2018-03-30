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
class properties_tftp extends co_property_container {
	protected $x_allowfilecreation;
	protected $x_dir;
	protected $x_enable;
	protected $x_extraoptions;
	protected $x_maxblocksize;
	protected $x_port;
	protected $x_timeout;
	protected $x_umask;
	protected $x_username;

	public function get_allowfilecreation() {
		return $this->x_allowfilecreation ?? $this->init_allowfilecreation();
	}
	public function init_allowfilecreation() {
		$property = $this->x_allowfilecreation = new property_bool($this);
		$property->
			set_name('allowfilecreation')->
			set_title(gtext('Allow New Files'));
		$caption = gtext('Allow new files to be created.');
		$description = gtext('By default, only already existing files can be uploaded.');
		$property->
			set_id('allowfilecreation')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_dir() {
		return $this->x_dir ?? $this->init_dir();
	}
	public function init_dir() {
		global $g;
		
		$property = $this->x_dir = new property_text($this);
		$property->
			set_name('dir')->
			set_title(gtext('Directory'));
		$description = gtext('The directory containing the files you want to publish. The remote host does not need to pass along the directory as part of the transfer.');
		$property->
			set_id('dir')->
			set_description($description)->
			set_placeholder(gtext('Enter a directory'))->
			set_defaultvalue($g['media_path'])->
			set_size(60)->
			set_maxlength(4096)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_UNSAFE_RAW)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => $g['media_path']])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_enable() {
		return $this->x_enable ?? $this->init_enable();
	}
	public function init_enable() {
		$property = $this->x_enable = new property_enable($this);
		$property->set_defaultvalue(false);
		return $property;
	}
	public function get_extraoptions() {
		return $this->x_extraoptions ?? $this->init_extraoptions();
	}
	public function init_extraoptions() {
		$property = $this->x_extraoptions = new property_text($this);
		$property->
			set_name('extraoptions')->
			set_title(gtext('Extra Options'));
		$description = gtext('Extra options (usually empty).');
		$placeholder = gtext('Enter extra options');
		$property->
			set_id('extraoptions')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(4096)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_UNSAFE_RAW)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => ''])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_maxblocksize() {
		return $this->x_maxblocksize ?? $this->init_maxblocksize();
	}
	public function test_maxblocksize($value) {
		if(is_scalar($value)):
			if(preg_match('/^$/',$value)):
				return $value;
			endif;
			if(is_int($value)):
				if(($value >= 512) && ($value <= 65464)):
					return $value;
				endif;
			endif;
		endif;
		return NULL;
	}
	public function init_maxblocksize() {
		$property = $this->x_maxblocksize = new property_int($this);
		$property->
			set_name('maxblocksize')->
			set_title(gtext('Max. Block Size'));
		$description = gtext('Specifies the maximum permitted block size. The permitted range for this parameter is from 512 to 65464.');
		$property->
			set_id('maxblocksize')->
//			set_caption()->
			set_description($description)->
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_port() {
		return $this->x_port ?? $this->init_port();
	}
	public function test_port() {
		if(is_scalar($value)):
			if(preg_match('/^$/',$value)):
				return $value;
			endif;
			if(is_int($value)):
				if($value == 69):
					return $value;
				endif;
				if(($value >= 1024) && ($value <= 45151)):
					return $value;
				endif;
			endif;
		endif;
		return NULL;
	}
	public function init_port() {
		$property = $this->x_port = new property_int($this);
		$property->
			set_name('port')->
			set_title(gtext('Port'));
		$caption = gtext('Port of the TFTP service. Leave blank to use the default port.');
		$description = gtext('Enter a custom port number if you do not want to use default port 69.');
		$placeholder = gtext('69');
		$property->
			set_id('port')->
			set_caption($caption)->
			set_description($description)->
			set_placeholder($placeholder)->
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Port number must be 69 or a number between 1024 and 49151.')));
		return $property;
	}
	public function get_timeout() {
		return $this->x_timeout ?? $this->init_timeout();
	}
	public function init_timeout() {
		$property = $this->x_timeout = new property_int($this);
		$property->
			set_name('timeout')->
			set_title(gtext('Timeout'));
		$description = gtext('Determine the default timeout, in microseconds, before the first packet is retransmitted. The default is 1000000 (1 second).');
		$property->
			set_id('timeout')->
//			set_caption()->
			set_description($description)->
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Timeout must be a number between 10 and 255000000.')));
		return $property;
	}
	public function get_umask() {
		return $this->x_umask ?? $this->init_umask();
	}
	public function init_umask() {
		$property = $this->x_umask = new property_text($this);
		$property->
			set_name('umask')->
			set_title(gtext('Umask'));
		$description = gtext('Sets the umask for newly created files to the specified value. The default is zero (anyone can read or write).');
		$property->
			set_id('umask')->
			set_description($description)->
			set_placeholder('000')->
			set_defaultvalue('0')->
			set_size(4)->
			set_maxlength(3)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => '/^(?:[0..7]{1,3}?$/'])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_username() {
		return $this->x_username ?? $this->init_username();
	}
	public function init_username() {
		$property = $this->x_username = new property_list($this);
		$property->
			set_name('username')->
			set_title(gtext('Username'));
		$caption = gtext('Specifies the username under which the TFTP service should run.');
		$property->
			set_id('username')->
			set_caption($caption)->
//			set_description('')->
			set_defaultvalue('nobody')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}
