<?php
/*
	login.php

	Part of XigmaNAS (http://www.xigmanas.com).
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
require_once 'guiconfig.inc';

unset($input_errors);
if(filter_input(INPUT_SERVER,'REQUEST_METHOD',FILTER_CALLBACK,['options' =>	function($value) { return $value === 'POST'; }])):
	$username = filter_input(INPUT_POST,'username',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => '/^[a-z\d\.\-_]+$/i']]);
	$remote_addr = (isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '';
	if(isset($username)):
		Session::start();
		array_make_branch($config,'system');
		if(isset($config['system']['username']) && is_string($config['system']['username']) && ($username === $config['system']['username'])):
			$success = true;
			if($success):
				$password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : NULL;
				if(isset($password)):
				else:
					$success = false;
					write_log(sprintf('AUTH: No password provided for user: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				if(isset($config['system']['password']) && is_string($config['system']['password'])):
				else:
					$success = false;
					write_log(sprintf('AUTH: No password configured for user: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				if(password_verify($password,$config['system']['password'])):
					Session::initAdmin();
					header('Location: index.php');
					exit;
				else:
					$success = false;
					write_log(sprintf('AUTH: Invalid password entererd for user: %s from %s',$username,$remote_addr));
				endif;
			endif;
		else:
			$success = true;
			if($success):
				//	Check if username is listed as a system user
				$users = system_get_user_list();
				$system_user_row_id = array_search_ex($username,$users,'name');
				if(false !== $system_user_row_id):
					$system_user = $users[$system_user_row_id];
				else:
					$success = false;
					write_log(sprintf('AUTH: Username not found: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				//	Check if UID column exists
				if(array_key_exists('uid',$system_user)):
				else:
					$success = false;
					write_log(sprintf('AUTH: UID for username not found: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				//	Check if it is a local user
				array_make_branch($config,'access','user');
				$portal_user_row_id = array_search_ex($system_user['uid'],$config['access']['user'],'id');
				if(false !== $portal_user_row_id):
					$portal_user = $config['access']['user'][$portal_user_row_id];
				else:
					$success = false;
					write_log(sprintf('AUTH: Username not found in portal configuration: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				//	check if a password has been received
				$password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : NULL;
				if(isset($password)):
				else:
					$success = false;
					write_log(sprintf('AUTH: No password provided for user: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				//	Check if password has been configured for user
				if(isset($system_user['password']) && is_string($system_user['password'])):
				else:
					$success = false;
					write_log(sprintf('AUTH: No password configured for user: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				//	Verify password
				if(password_verify($password,$system_user['password'])):
				else:
					write_log(sprintf('AUTH: Invalid password entererd for user: %s from %s',$username,$remote_addr));
				endif;
			endif;
			if($success):
				//	Check if user is allowed to access the user portal
				if(isset($portal_user['userportal'])):
					Session::initUser($system_user['uid'],$system_user['name']);
					header('Location: index.php');
					exit;
				else:
					$success = false;
					write_log(sprintf('AUTH: No portal access for username: %s from %s',$username,$remote_addr));
				endif;
			endif;
		endif;
		$input_errors = gtext('Invalid login credentials.');
	else:
		write_log(sprintf('AUTH: Username contains invalid character(s): %s from %s',$username,$remote_addr));
		$input_errors = gtext('Invalid username or password.');
	endif;
endif;
$document = new_page([],'login.php','login');
$pagecontent = $document->getElementById('pagecontent');

$loginpagedata = $pagecontent->
	addDIV(['class' => 'loginpageworkspace'])->
		addDIV(['class' => 'loginpageframe'])->
			addDIV(['class' => 'loginpagedata']);

$loginpagedata->
	addElement('header',['class' => 'lph'])->
		push()->
		addDIV(['class' => 'lphl'])->
			push()->
			addDIV(['class' => 'lphll'])->
				insIMG(['src' => '/images/lock.png','alt' => ''])->
			pop()->
			addDIV(['class' => 'lphlr'])->
				addA(['title' => sprintf('www.%s',get_product_url()),'href' => sprintf('http://www.%s',get_product_url()),'target' => '_blank'])->
					insIMG(['src' => '/images/login_logo.png','alt' => 'logo'])->
		pop()->
		addDIV(['class' => 'lphh'])->
			insDIV(['class' => 'hostname'],system_get_hostname());
$loginpagedata->
	addDIV(['class' => 'lpm'])->
		push()->
		addDIV(['class' => 'lpmi'])->
			insINPUT(['type' => 'text','id' => 'username','name' => 'username','placeholder' => gtext('Username'),'autofocus' => 'autofocus'])->
		last()->
		addDIV(['class' => 'lpmi'])->
			insINPUT(['type' => 'password','id' => 'password','name' => 'password','placeholder' => gtext('Password')])->
		pop()->
		addDIV(['class' => 'lpmi'])->
			insINPUT(['class' => 'formbtn','type' => 'submit','value' => gtext('Login')]);
$loginpagedata->
	addElement('footer',['class' => 'lpf'])->
		addUL()->
			push()->addLI(['style' => 'padding-right: 4px;'])->
				insA(['target' => '_blank','href' => 'http://www.xigmanas.com/forums/'],gtext('Forum'))->
			last()->addLI(['style' => 'padding: 0 4px;'])->
				insA(['target' => '_blank','href' => 'http://www.xigmanas.com/wiki/doku.php'],gtext('Information & Manuals'))->
			last()->addLI(['style' => 'padding: 0 4px;'])->
				insA(['target' => '_blank','href' => 'https://webchat.freenode.net/?channels=#xigmanas'],gtext('IRC XigmaNAS'))->
			pop()->addLI(['style' => 'padding-left: 4px;'])->
				insA(['target' => '_blank','href' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=info%40xigmanas%2ecom&lc=US&item_name=XigmaNAS&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted'],gtext('Donate'));
if(!empty($input_errors)):
	$loginpagedata->
		insDIV(['class' => 'lpe'],$input_errors);
endif;
$document->render();
