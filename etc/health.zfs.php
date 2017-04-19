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
				$body_rows[] = sprintf('Status of pool "%s" is %s.',$pool_name,$row);
				break;
			endswitch;
	endforeach;
	//	check for read, write or checksum errors.
	$cmd = sprintf('/sbin/zpool status %s',escapeshellarg($pool_name));
	$output = [];
	$return_value = 0;
	mwexec2($cmd,$output,$return_value);
	$fun_starts_now = false;
	foreach($output as $row):
		if(preg_match('/.*NAME.+STATE.+READ.+WRITE.+CKSUM/',$row)):
			$fun_starts_now = true;
			continue;
		endif;
		if($fun_starts_now):
			$parameters = preg_split('/[\s]+/',$row,-1,PREG_SPLIT_NO_EMPTY);
			if(4 < count($parameters)):
				if(is_numeric($parameters[2]) && (0 != $parameters[2])):
					$body_rows[] = sprintf('Pool "%s" encountered %d read errors',$pool_name,$parameters[2]); 
				endif;
				if(is_numeric($parameters[3]) && (0 != $parameters[3])):
					$body_rows[] = sprintf('Pool "%s" encountered %d write errors',$pool_name,$parameters[3]); 
				endif;
				if(is_numeric($parameters[4]) && (0 != $parameters[4])):
					$body_rows[] = sprintf('Pool "%s" encountered %d checksum errors',$pool_name,$parameters[4]); 
				endif;
			endif;
		endif;
	endforeach;
endforeach;
if(!empty($body_rows)):
	$subject = '%h: ZFS pool status check failure report.';
	$body = implode("\n",$body_rows);
	$error = 0;
	@email_send($config['system']['email']['sendto'],$subject,$body,$error);
endif;
?>
