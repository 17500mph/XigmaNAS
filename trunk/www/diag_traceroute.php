<?php
/*
	diag_traceroute.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';

function diag_traceroute_get_sphere() {
	$sphere = new co_sphere_settings('diag_traceroute','php');
	return $sphere;
}
$sphere = diag_traceroute_get_sphere();
$do_traceroute = false;
if($_POST):
	unset($input_errors);
	// Input validation
	$reqdfields = ['target','max_ttl'];
	$reqdfieldsn = [gtext('Target'),gtext('Count')];
	$reqdfieldst = ['string','numeric'];
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	if(empty($input_errors)):
		$do_traceroute = true;
		$target = $_POST['target'];
		$resolve = isset($_POST['resolve']);
		$max_ttl = $_POST['max_ttl'];
		if($resolve):
			$cmd = sprintf('/usr/sbin/traceroute -w 2 -m %1$s %2$s',escapeshellarg($max_ttl),escapeshellarg($target));
		else:
			$cmd = sprintf('/usr/sbin/traceroute -n -w 2 -m %1$s %2$s',escapeshellarg($max_ttl),escapeshellarg($target));
		endif;
	endif;
endif;
if(!$do_traceroute):
	$target = '';
	$max_ttl = 10;
	$resolve = false;
endif;
$pgtitle = [gtext('Diagnostics'),gtext('Traceroute')];
include 'fbegin.inc';
?>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="diag_ping.php"><span><?=gtext('Ping');?></span></a></li>
		<li class="tabact"><a href="diag_traceroute.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Traceroute');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<form action="<?=$sphere->get_scriptname();?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($input_errors)):
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
			html_titleline2(gtext('Traceroute Host'));
?>
		</thead>
		<tbody>
<?php
			html_inputbox2('target',gtext('Target'),$target,gtext('Enter hostname or IP address.'),true,32);
			html_checkbox2('resolve',gtext('Resolve IP'),$resolve ? true : false,gtext('Resolve IP addresses to hostnames.'),'',false);
			$a_max_ttl = [];
			for($i = 1;$i <= 64;$i++):
				$a_max_ttl[$i] = $i;
			endfor;
			html_combobox2('max_ttl',gtext('Count'),$max_ttl,$a_max_ttl,gtext('Select max time-to-live (max number of hops) used in outgoing probe packets.'),true);
?>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=gtext('Traceroute');?>"/>
	</div>
	<div id="remarks">
<?php
		html_remark2('note',gtext('Note'),gtext('Traceroute may take a while, please be patient.'));
?>
	</div>
<?php
	if($do_traceroute):?>
		<table class="area_data_settings">
			<colgroup>
				<col class="area_data_settings_col_tag">
				<col class="area_data_settings_col_data">
			</colgroup>
			<thead>
<?php
				html_separator2();
				html_titleline2(gtext('Traceroute Output'));
?>
			</thead>
			<tbody><tr>
				<td class="celltag"><?=gtext('Output');?></td>
				<td class="celldata">
<?php
					echo '<pre class="cmdoutput">';
					mwexec2($cmd,$rawdata);
					echo htmlspecialchars(implode(PHP_EOL,$rawdata));
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
