#!/usr/local/bin/php-cgi -f
<?php
/*
	/etc/health.zfs.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
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
require_once 'config.inc';
require_once 'functions.inc';
require_once 'email.inc';
/*
	check zfs pool status if any pool is having issues
*/
$body_rows = [];
// collect pool names
$cmd = '/sbin/zpool list -H -o name';
$pool_names = [];
$return_value = 0;
mwexec2($cmd,$pool_names,$return_value);
// scan each pool
foreach($pool_names as $pool_name):
	$issue_found = false;
	//	check for pool status 'ONLINE'. Any other status will cause an alert email.
	$cmd = sprintf('/sbin/zpool list -H -o health %s',escapeshellarg($pool_name));
	$output = [];
	$return_value = 0;
	mwexec2($cmd,$output,$return_value);
	foreach($output as $row):
		switch($row):
			case 'ONLINE':
				break;
			default:
				$body_rows[] = sprintf('The status of pool "%s" is %s.',$pool_name,$row);
				$issue_found = true;
				break;
			endswitch;
	endforeach;
	//	check for read, write or checksum errors.
	$cmd = sprintf('/sbin/zpool status %s',escapeshellarg($pool_name));
	$output = [];
	$return_value = 0;
	mwexec2($cmd,$output,$return_value);
	$fun_starts_now = false;
	$errors_read = false;
	$errors_write = false;
	$errors_checksum = false;
	foreach($output as $row):
		if(preg_match('/.*NAME.+STATE.+READ.+WRITE.+CKSUM/',$row)):
			$fun_starts_now = true;
			continue;
		endif;
		if($fun_starts_now):
			$parameters = preg_split('/[\s]+/',$row,-1,PREG_SPLIT_NO_EMPTY);
			if((4 < count($parameters)) && ('ONLINE' == $parameters[1])):
				if("0" != $parameters[2]):
					$errors_read = true;
					$issue_found = true;
				endif;
				if("0" != $parameters[3]):
					$errors_write = true;
					$issue_found = true;
				endif;
				if("0" != $parameters[4]):
					$errors_checksum = true;
					$issue_found = true;
				endif;
			endif;
		endif;
	endforeach;
	if($errors_read):
		$body_rows[] = sprintf('Pool "%s" encountered read errors.',$pool_name);
	endif;
	if($errors_write):
		$body_rows[] = sprintf('Pool "%s" encountered write errors.',$pool_name); 
	endif;
	if($errors_checksum):
		$body_rows[] = sprintf('Pool "%s" encountered checksum errors.',$pool_name); 
	endif;
	//	append pool status if an issue was found
	if($issue_found):
		$body_rows[] = '';
		foreach($output as $row):
			$body_rows[] = $row;
		endforeach;
	endif;
endforeach;
//	compile and send email if an issue was found
if(!empty($body_rows)):
	$subject = '%h: ZFS pool status check failure report.';
	$body = implode("\n",$body_rows);
	$error = 0;
	@email_send($config['system']['email']['sendto'],$subject,$body,$error);
endif;
?>
