<?php
/*
	diag_ping.php

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
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the XigmaNAS Project.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';

function get_interface_addr($ifdescr) {
	global $config;

	// Find out interface name.
	$if = $config['interfaces'][$ifdescr]['if'];
	return get_ipaddr($if);
}
function diag_ping_get_sphere() {
	$sphere = new co_sphere_settings('diag_ping','php');
	return $sphere;
}
$sphere = diag_ping_get_sphere();
$do_ping = false;
if($_POST):
	unset($input_errors);
	// Input validation
	$reqdfields = ['target','count'];
	$reqdfieldsn = [gtext('Target'),gtext('Count')];
	$reqdfieldst = ['string','numeric'];
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	if(empty($input_errors)):
		$do_ping = true;
		$target = $_POST['target'];
		$interface = $_POST['interface'];
		$count = $_POST['count'];
		$ifaddr = get_interface_addr($interface);
		if($ifaddr):
			$cmd = sprintf('/sbin/ping -S %3$s -c %1$s %2$s',$count,escapeshellarg($target),$ifaddr);
		else:
			$cmd = sprintf('/sbin/ping -c %1$s %2$s',$count,escapeshellarg($target));
		endif;
	endif;
endif;
if(!$do_ping):
	$target = '';
	$count = 5;
	$cmd = '';
endif;
$pgtitle = [gtext('Diagnostics'),gtext('Ping')];
include 'fbegin.inc';
?>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="diag_ping.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Ping');?></span></a></li>
		<li class="tabinact"><a href="diag_traceroute.php"><span><?=gtext('Traceroute');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<form action="<?=$sphere->get_scriptname();?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if (!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('Ping Host'));
?>
		</thead>
		<tbody>
<?php
			html_inputbox2('target',gtext('Target'),$target,gtext('Enter hostname or IP address.'),true,32);
			html_interfacecombobox2('interface',gtext('Interface'),!empty($interface) ? $interface : '',gtext('Select which interface to use.'),true);
			$a_count = [];
			for($i = 1;$i <= 15; $i++):
				$a_count[$i] = $i;
			endfor;
			html_combobox2('count',gtext('Count'),$count,$a_count,gtext('Select number of ICMP ECHO REQUEST packets.'),true);
?>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=gtext('Ping');?>"/>
	</div>
	<div id="remarks">
<?php
		html_remark('note',gtext('Note'),gtext('Ping may take a while, please be patient.'));
?>
	</div>
<?php
	if($do_ping):
?>
		<table class="area_data_settings">
			<colgroup>
				<col class="area_data_settings_col_tag">
				<col class="area_data_settings_col_data">
			</colgroup>
			<thead>
<?php
				html_separator2();
				html_titleline2(gtext('Ping Output'));
?>
			</thead>
			<tbody><tr>
				<td class="celltag"><?=gtext('Output');?></td>
				<td class="celldata">
<?php
					echo '<pre class="cmdoutput">';
					mwexec2($cmd,$rawdata);
					echo implode(PHP_EOL,$rawdata);
					unset($rawdata);
					echo '</pre>';
?>
				</td>
			</tr></tbody>
		</table>
<?php
	endif;
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
?>
