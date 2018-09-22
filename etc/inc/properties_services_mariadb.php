<?php
/*
	properties_services_mariadb.php

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

class mariadb_properties extends co_property_container {
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
	protected $x_homedir;
	public function get_homedir() {
		return $this->x_homedir ?? $this->init_homedir();
	}
	public function init_homedir() {
		$property = $this->x_homedir = new property_text($this);
		$description = 
			gettext('Enter the path of the home directory for databases and configuration files.') .
			'<br />' .
			gettext('The server will be started with the minimum required parameters.') .
			'<br />' .
			sprintf(gettext('In this directory, you can create a %s file with your additional parameters.'),"'my.cnf'") .
			'  ' .
			'<a href="https://mariadb.com/kb/en/mariadb/configuring-mariadb-with-mycnf" target="_blank">' . gettext('Please read the documentation') . '</a>.';
		$placeholder = gettext('Path');
		$property->
			set_name('homedir')->
			set_title(gettext('Home Directory'));
		$property->
			set_id('homedir')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	protected $x_phrasecookieauth;
	public function get_phrasecookieauth() {
		return $this->x_phrasecookieauth ?? $this->init_phrasecookieauth();
	}
	public function init_phrasecookieauth() {
		$property = $this->x_phrasecookieauth = new property_text($this);
		$description = gettext('The cookie-based auth_type uses AES algorithm to encrypt the password. Enter a random passphrase of your choice. It will be used internally by the AES algorithm - you won’t be prompted for this passphrase. The secret should be 32 characters long.');
		$placeholder = gettext('Passphrase');
		$regexp = '/^\S{32,128}$/';
		$property->
			set_name('phrasecookieauth')->
			set_title(gettext('Passphrase'));
		$property->
			set_id('phrasecookieauth')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(128)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	protected $x_auxparam;
	public function get_auxparam() {
		return $this->x_auxparam ?? $this->init_auxparam();
	}
	public function init_auxparam() {
		$property = $this->x_auxparam = new property_textarea($this);
		$description = gettext('These parameter will be added to my.cnf');
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

