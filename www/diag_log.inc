<?php
/*
	diag_log.inc

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright
	   notice, this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

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
require_once 'globals.inc';
require_once 'rc.inc';

// Get the location of the log files (may be modified via rc.conf variables).
$clogdir = rc_getenv_ex('clog_logdir',$g['varlog_path']);
$rsync_client_logfile = rc_getenv_ex('rsync_client_logfile',"{$g['varlog_path']}/rsync_client.log");
$rsync_local_logfile = rc_getenv_ex('rsync_local_logfile',"{$g['varlog_path']}/rsync_local.log");
$fuppes_logfile = rc_getenv_ex('fuppes_logfile',"{$g['varlog_path']}/fuppes.log");
$minidlna_logdir = rc_getenv_ex('minidlna_logdir',"{$g['varlog_path']}");
$minidlna_logfile = "$minidlna_logdir/minidlna.log";
$mtdaapd_logfile = rc_getenv_ex('mtdaapd_logfile',"{$g['varlog_path']}/mt-daapd.log");
$proftpd_modban_logfile = rc_getenv_ex('proftpd_modban_banlog',"{$g['varlog_path']}/proftpd/ban.log");

// The log configuration
$loginfo = [
	[
		'visible' => true,
		'desc' => gtext('System'),
		'logfile' => "{$clogdir}/system.log",
		'filename' => 'system.log',
		'type' => 'clog',
		'size' => '262144',
		'pattern' => '/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('User'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 2],
			['title' => gtext('Event'),'width' => '75%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 3]
		]],
	[
		'visible' => true,
		'desc' => gtext('SSH'),
		'logfile' => "{$clogdir}/sshd.log",
		'filename' => 'sshd.log',
		'type' => 'clog',
		"size" => '32768',
		'pattern' => '/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('User'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 2],
			['title' => gtext('Event'),'width' => '75%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 3]
		]],
	[
		'visible' => true,
		'desc' => gtext('Daemon'),
		'logfile' => "{$clogdir}/daemon.log",
		'filename' => 'daemon.log',
		'type' => 'clog',
		"size" => '32768',
		'pattern' => '/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('User'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 2],
			['title' => gtext('Event'),'width' => '75%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 3]
		]],
	[
		'visible' => true,
		'desc' => gtext('FTP'),
		'logfile' => "{$clogdir}/ftp.log",
		'filename' => 'ftp.log',
		'type' => 'clog',
		"size" => '32768',
		'pattern' => '/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('User'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 2],
			['title' => gtext('Event'),'width' => '75%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 3]
		]],
	[
		'visible' => isset($config['ftpd']['mod_ban']['enable']),
		'desc' => gtext('FTP - Ban Module'),
		'logfile' => $proftpd_modban_logfile,
		'filename' => 'ban.log',
		'type' => 'plain',
		'pattern' => '/^(\S+\s+\S+)\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('Event'),'width' => '85%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 2]
		]],
	[
		'visible' => true,
		'desc' => gtext('FTP - File Transfer'),
		'logfile' => "{$g['varlog_path']}/xferlog",
		'filename' => 'xferlog',
		'type' => 'plain',
		'pattern' => '/^(\S+\s+\S+\s+\d+\s+\S+\s+\d+) ((\d+) (\S+) (\d+) (\S+) (a|b) (C|U|T|_) (o|i|d) (a|g|r) (\S+) (\S+) (0|1) (\S+) (c|i))$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('IP'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => '','pmid' => 4],
			['title' => gtext('User'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => '','pmid' => 11],
//		['title' => gtext('File'),'hdrclass' => 'lhell','class' => 'lcell','param' => '','pmid' => 6],
//		['title' => gtext('Filesize'),'hdrclass' => 'lhell','class' => 'lcell','param' => '','pmid' => 5],
			['title' => gtext('Event'),'width' => '65%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 2]
		]],
	[
		'visible' => true,
		'desc' => gtext('S.M.A.R.T.'),
		'logfile' => "{$clogdir}/smartd.log",
		'filename' => 'smartd.log',
		'type' => 'clog',
		"size" => '32768',
		'pattern' => '/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('User'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 2],
			['title' => gtext('Event'),'width' => '75%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 3]
		]],
	[
		'visible' => true,
		'desc' => gtext('RSYNC - Server'),
		'logfile' => "{$clogdir}/rsyncd.log",
		'filename' => 'rsyncd.log',
		'type' => 'clog',
		"size" => '32768',
		'pattern' => '/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('User'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 2],
			['title' => gtext('Event'),'width' => '75%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 3]
		]],
	[
		'visible' => true,
		'desc' => gtext('RSYNC - Client'),
		'logfile' => $rsync_client_logfile,
		'filename' => 'rsync_client.log',
		'type' => 'plain',
		'pattern' => '/^(.*)$/',
		'columns' => [
			['title' => gtext('Event'),'width' => '100%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 1]
		]],
	[
		'visible' => true,
		'desc' => gtext('RSYNC - Local'),
		'logfile' => $rsync_local_logfile,
		'filename' => 'rsync_local.log',
		'type' => 'plain',
		'pattern' => '/^(.*)$/',
		'columns' => [
			['title' => gtext('Event'),'width' => '100%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 1]
		]],
	[
		'visible' => true,
		'desc' => gtext('UPnP/DLNA - Fuppes'),
		'logfile' => $fuppes_logfile,
		'filename' => 'fuppes.log',
		'type' => 'plain',
		'pattern' => '/^(.*)$/',
		'columns' => [
			['title' => gtext('Event'),'width' => '100%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 1]
		]],
	[
		'visible' => true,
		'desc' => gtext('UPnP/DLNA - MiniDLNA'),
		'logfile' =>  $minidlna_logfile,
		'filename' => 'minidlna.log',
		'type' => 'plain',
		'pattern' => '/^\[(.*?)\]\s+(.*?)\..*?:\d+:\s+.*?:\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('Who'),'width' => '10%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 2],
			['title' => gtext('Event'),'width' => '75%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 3]
		]],
	[
		'visible' => true,
		'desc' => gtext('DAAP'),
		'logfile' => $mtdaapd_logfile,
		'filename' => 'daap.log',
		'type' => 'plain',
		'pattern' => '/^(\S+\s+\S+)\s+\S+:\s+(.*)$/',
		'columns' => [
			['title' => gtext('Date & Time'),'width' => '15%','hdrclass' => 'lhell','class' => 'lcell','param' => 'style="white-space: nowrap;"','pmid' => 1],
			['title' => gtext('Event'),'width' => '85%','hdrclass' => 'lhebl','class' => 'lcebl','param' => '','pmid' => 2]
		]],
];
function log_get_contents($logfile,$type) {
	global $config;

	$content = [];
	$param = (isset($config['syslogd']['reverse']) ? "-r " : '');
	$param .= "-n " . $config['syslogd']['nentries'];
	switch($type):
		case 'clog':
			$searchlog = isset($_POST['searchlog']) ? $_POST['searchlog'] : '';
		    $filter_str = ($searchlog == '') ? '' : "| grep -i \"".htmlspecialchars($searchlog)."\"";
			exec("/usr/sbin/clog {$logfile} | /usr/bin/tail {$param} {$filter_str}",$content);
			break;
		case 'plain':
			$searchlog = isset($_POST['searchlog']) ? $_POST['searchlog'] : '';
		    $filter_str = ($searchlog == '') ? '' : "| grep -i \"".htmlspecialchars($searchlog)."\"";
			exec("/bin/cat {$logfile} | /usr/bin/tail {$param} {$filter_str}",$content);
	endswitch;
	return $content;
}
function log_clear($loginfo) {
	if(!is_array($loginfo)):
		return;
	endif;
	switch($loginfo['type']):
		case 'clog':
			exec("/usr/sbin/clog -i -s {$loginfo['size']} {$loginfo['logfile']}");
			break;
		case 'plain':
			exec("/bin/cat /dev/null > {$loginfo['logfile']}");
			break;
	endswitch;
}
function log_download($loginfo) {
	$gt_logisempty = gtext('Log file is empty.');
	if(is_array($loginfo)):
		switch ($loginfo['type']):
			case 'clog':
				if(file_exists($loginfo['logfile'])):
					exec(sprintf('/usr/sbin/clog %s',escapeshellarg($loginfo['logfile'])),$output);
					$content = implode("\n",$output);
				else:
					$content = $gt_logisempty;
				endif;
				header('Content-Type: application/octet-stream');
				header(sprintf('Content-Disposition: attachment; filename="%s"',$loginfo['filename']));
				header(sprintf('Content-Length: %d',strlen($content)));
				header('Pragma: hack');
				echo $content;
				break;
			case 'plain':
				if(file_exists($loginfo['logfile'])):
					if(false === ($content = file_get_contents($loginfo['logfile']))):
						$content = $gt_logisempty;
					endif;
				else:
					$content = $gt_logisempty;
				endif;
				if(empty($content)):
					$content = $gt_logisempty;
				endif;
				header('Content-Type: application/octet-stream');
				header(sprintf('Content-Disposition: attachment; filename="%s"',$loginfo['filename']));
				header(sprintf('Content-Length: %d',strlen($content)));
				header('Pragma: hack');
				echo $content;
				break;
		endswitch;
	endif;
}
?>
