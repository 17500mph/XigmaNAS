<?php
/*
	auth.inc

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
require_once 'session.inc';

Session::start();

// Check if session is valid
if(!Session::isLogin()):
	header('Location: login.php');
	exit;
endif;

if(array_key_exists('SERVER_PROTOCOL',$_SERVER) && is_string($_SERVER['SERVER_PROTOCOL'])):
	$server_protocol = htmlspecialchars($_SERVER['SERVER_PROTOCOL']);
else:
	$server_protocol = 'HTTP/1.0';
endif;

// Check page permission
if(!Session::isAdmin() && (!isset($pgperm['allowuser']) || !$pgperm['allowuser'])):
	header(sprintf('%s 403 Forbidden',$server_protocol));
	header('Status: 403 Forbidden');
	echo <<<EOD
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="format-detection" content="telephone=no">
		<title>403 - Forbidden</title>
	</head>
	<body>
		<h1>403 - Forbidden</h1>
	</body>
</html>
EOD;
	Session::destroy();
	exit;
endif;

// Check if request is valid and authorized
if($_SERVER['REQUEST_METHOD'] === 'POST'):
	if(!isset($_POST['authtoken']) || !Session::isValidAuthToken($_POST['authtoken'])):
		header(sprintf('%s 401 Unauthorized',$server_protocol));
		header('Status: 401 Unauthorized');
		echo <<<EOH
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="format-detection" content="telephone=no">
		<title>401 - Unauthorized</title>
	</head>
	<body>
		<h1>401 - Unauthorized</h1>
	</body>
</html>
EOH;
		Session::destroy();
		exit;
	endif;
endif;
?>
