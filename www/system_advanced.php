<?php
/*
	system_advanced.php

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
require_once 'properties_system_advanced.php';

function sysctl_tune($mode) {
	global $config;

	$a_sysctlvar = &array_make_branch($config,'system','sysctl','param');
	if(empty($a_sysctlvar)):
	else:
		array_sort_key($a_sysctlvar,'name');
	endif;
	$a_mib = [
		'kern.maxvnodes' => 3339551,
		'kern.maxfiles' =>  65536,
		'kern.ipc.nmbclusters' =>  12255534,
		'kern.ipc.nmbjumbop' =>  6127766,
		'kern.ipc.nmbjumbo9' =>  5446902,
		'kern.ipc.nmbjumbo16' =>  4085176,
		'kern.ipc.maxsockets' =>  1035072,
		'kern.ipc.maxsockbuf' =>  2097152,
		'kern.ipc.somaxconn' =>  2048,
		'net.inet.tcp.sendbuf_auto' =>  1,
		'net.inet.tcp.recvbuf_auto' =>  1,
		'net.inet.tcp.sendspace' =>  32768,
		'net.inet.tcp.recvspace' =>  65536,
		'net.inet.tcp.sendbuf_max' =>  2097152,
		'net.inet.tcp.recvbuf_max' =>  2097152,
		'net.inet.tcp.sendbuf_inc' =>  8192,
		'net.inet.tcp.recvbuf_inc' =>  16384,
		'net.inet.tcp.tcbhashsize' =>  2097152,
		'net.inet.ip.intr_queue_maxlen' =>  256,
		'net.route.netisr_maxqlen' =>  256,
		'hw.igb.max_interrupt_rate' =>  8000,
		'hw.ix.max_interrupt_rate' =>  31250,
		'hw.igb.rxd' =>  1024,
		'hw.igb.txd' =>  1024,
		'hw.ix.txd' =>  2048,
		'hw.ix.rxd' =>  2048,
		'hw.igb.num_queues' =>  0,
		'hw.ix.num_queues' =>  8,
		'net.inet.tcp.delayed_ack' => 1,
		'net.inet.tcp.rfc1323' => 1,
		'net.inet.udp.recvspace' => 65536,
		'net.inet.udp.maxdgram' => 57344,
		'net.local.stream.recvspace' => 65536,
		'net.local.stream.sendspace' => 65536,
		'net.inet.icmp.icmplim' => 300,
		'net.inet.icmp.icmplim_output' => 1,
		'net.inet.tcp.path_mtu_discovery' => 0,
		'hw.intr_storm_threshold' => 9000,
	];
	switch($mode):
		case 0: // Remove system tune MIB's.
			foreach($a_mib as $name => $value):
				$id = array_search_ex($name,$a_sysctlvar,'name');
				if(false !== $id):
					unset($a_sysctlvar[$id]);
				endif;
			endforeach;
			break;
		case 1: // Add system tune MIB's.
			foreach($a_mib as $name => $value):
				$id = array_search_ex($name,$a_sysctlvar,'name');
				if(false === $id):
					$param = [];
					$param['uuid'] = uuid();
					$param['name'] = $name;
					$param['value'] = $value;
					$param['comment'] = gtext('System tuning');
					$param['enable'] = true;
					$a_sysctlvar[] = $param;
				endif;
			endforeach;
			break;
	endswitch;
}
function get_sysctl_kern_vty() {
	return trim(`/sbin/sysctl -n kern.vty`);
}
function get_sphere_system_advanced() {
	global $config;
	
	$sphere = new co_sphere_settings('system_advanced','php');
	$sphere->grid = &array_make_branch($config,'system');
	return $sphere;
}
//	init properties and sphere
$cop = new properties_system_advanced();
$sphere = &get_sphere_system_advanced();
$is_sc = 'sc' == get_sysctl_kern_vty();
//	ensure boolean parameter are set properly
//	take the value of the parameter if its type is bool
//	set value to true if the parameter exists and is not NULL
//	set value to false if the parameter doesn't exist or is NULL
$pconfig['adddivsubmittodataframe'] = $cop->adddivsubmittodataframe->validate_config($config['system']);
$pconfig['disableconsolemenu'] = $cop->disableconsolemenu->validate_config($config['system']);
$pconfig['disablefm'] = isset($config['system']['disablefm']);
$pconfig['disablefirmwarecheck'] = isset($config['system']['disablefirmwarecheck']);
$pconfig['disablebeep'] = isset($config['system']['disablebeep']);
$pconfig['microcode_update'] = isset($config['system']['microcode_update']);
$pconfig['enabletogglemode'] = isset($config['system']['enabletogglemode']);
$pconfig['nonsidisksizevalues'] = isset($config['system']['nonsidisksizevalues']);
$pconfig['skipviewmode'] = isset($config['system']['skipviewmode']);
$pconfig['disableextensionmenu'] = isset($config['system']['disableextensionmenu']);
$pconfig['tune_enable'] = isset($config['system']['tune']);
$pconfig['zeroconf'] = isset($config['system']['zeroconf']);
$pconfig['powerd'] = isset($config['system']['powerd']);
$pconfig['pwmode'] = $config['system']['pwmode'];
$pconfig['pwmax'] = !empty($config['system']['pwmax']) ? $config['system']['pwmax'] : "";
$pconfig['pwmin'] = !empty($config['system']['pwmin']) ? $config['system']['pwmin'] : "";
$pconfig['motd'] = str_replace(chr(27),'&#27;',base64_decode($config['system']['motd'] ?? ''));
if($is_sc):
	$pconfig['sysconsaver'] = isset($config['system']['sysconsaver']['enable']);
	$pconfig['sysconsaverblanktime'] = $config['system']['sysconsaver']['blanktime'];
endif;
$pconfig['enableserialconsole'] = isset($config['system']['enableserialconsole']);

if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	if(!isset($pconfig['pwmax'])):
		$pconfig['pwmax'] = '';
	endif;
	if(!isset($pconfig['pwmin'])):
		$pconfig['pwmin'] = '';
	endif;
	// Input validation.
	if($is_sc):
		if(isset($_POST['sysconsaver'])):
			$reqdfields = ['sysconsaverblanktime'];
			$reqdfieldsn = [gtext('Blank Time')];
			$reqdfieldst = ['numeric'];
			do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
			do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
		endif;
	endif;
	if(isset($_POST['powerd'])):
		$reqdfields = ['pwmax','pwmin'];
		$reqdfieldsn = [gtext('CPU Maximum Frequency'),gtext('CPU Minimum Frequency')];
		$reqdfieldst = ['numeric','numeric'];
		//do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
		do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
	endif;
	if(empty($input_errors)):
		// Process system tuning.
		if($_POST['tune_enable']):
			sysctl_tune(1);
		elseif(isset($config['system']['tune']) && (!$_POST['tune_enable'])):
			// Simply force a reboot to reset to default values.
			// This makes programming easy :-) Also we are sure that
			// system will use origin values (maybe default values
			// change from one FreeBSD release to the next. This will
			// reduce maintenance).
			sysctl_tune(0);
			touch($d_sysrebootreqd_path);
		endif;
		$bootconfig="boot.config";
		if(!isset($_POST['enableserialconsole'])):
			if(file_exists("/$bootconfig")):
				unlink("/$bootconfig");
			endif;
			if(file_exists("{$g['cf_path']}/mfsroot.uzip") && file_exists("{$g['cf_path']}/$bootconfig")):
				config_lock();
				conf_mount_rw();
				unlink("{$g['cf_path']}/$bootconfig");
				conf_mount_ro();
				config_unlock();
			endif;
		else:
			if(file_exists("/$bootconfig")):
				unlink("/$bootconfig");
			endif;
			file_put_contents("/$bootconfig","-Dh\n");
			if(file_exists("{$g['cf_path']}/mfsroot.uzip")):
				config_lock();
				conf_mount_rw();
				if(file_exists("{$g['cf_path']}/$bootconfig")):
					unlink("{$g['cf_path']}/$bootconfig");
				endif;
				file_put_contents("{$g['cf_path']}/$bootconfig","-Dh\n");
				conf_mount_ro();
				config_unlock();
			endif;
		endif;
		$config['system']['adddivsubmittodataframe'] = $cop->adddivsubmittodataframe->validate_input();
		$helpinghand = $cop->disableconsolemenu->validate_input();
		if(isset($config['system']['disableconsolemenu']) !== $helpinghand):
			//	server needs to be restarted to activate setting.
			touch($d_sysrebootreqd_path);
		endif;
		$config['system']['disableconsolemenu'] = $helpinghand;
		$helpinghand = $cop->disablefm->validate_input();
		if(isset($config['system']['disablefm']) !== $helpinghand):
			//	server needs to be restarted to export/clear .htusers.php by fmperm.
			touch($d_sysrebootreqd_path);
			$_SESSION['g']['headermenu'] = []; // reset header menu
		endif;
		$config['system']['disablefm'] = $helpinghand;
		$config['system']['disablefirmwarecheck'] = $cop->disablefirmwarecheck->validate_input();
		$config['system']['enabletogglemode'] = $cop->enabletogglemode->validate_input();
		$config['system']['nonsidisksizevalues'] = $cop->nonsidisksizevalues->validate_input();
		$config['system']['skipviewmode'] = $cop->skipviewmode->validate_input();
		$_SESSION['g']['shrinkpageheader'] = $cop->shrinkpageheader->validate_input();
		$helpinghand = $cop->disableextensionmenu->validate_input();
		if(isset($config['system']['disableextensionmenu']) !== $helpinghand):
			//	reset header menu
			$_SESSION['g']['headermenu'] = [];
		endif;
		$config['system']['disableextensionmenu'] = $helpinghand;
		$config['system']['disablebeep'] = $cop->disablebeep->validate_input();
		$helpinghand = $cop->microcode_update->validate_input();
		if(isset($config['system']['microcode_update']) !== $helpinghand):
			//	microcode update flag has changed, server must be restarted.
			touch($d_sysrebootreqd_path);
		endif;
		$config['system']['microcode_update'] = $helpinghand;
		$config['system']['tune'] = $cop->tune_enable->validate_input();
		$config['system']['zeroconf'] = $cop->zeroconf->validate_input();
		$config['system']['powerd'] = $cop->powerd->validate_input();
		$config['system']['pwmode'] = $cop->pwmode->validate_input() ?? $cop->pwmode->get_defaultvalue();
		$config['system']['pwmax'] = $_POST['pwmax'];
		$config['system']['pwmin'] = $_POST['pwmin'];
		$config['system']['motd'] = base64_encode(str_replace('&#27;',chr(27),$_POST['motd'] ?? '')); // Encode string, otherwise line breaks will get lost
		if($is_sc):
			$config['system']['sysconsaver']['enable'] = $cop->sysconsaver->validate_input();
			$config['system']['sysconsaver']['blanktime'] = $_POST['sysconsaverblanktime'];
		endif;
		$config['system']['enableserialconsole'] = $cop->enableserialconsole->validate_input();
		//	adjust power mode
		$pwmode = $config['system']['pwmode'];
		$pwmax = $config['system']['pwmax'];
		$pwmin = $config['system']['pwmin'];
		$pwopt = "-a {$pwmode} -b {$pwmode} -n {$pwmode}";
		if(!empty($pwmax)):
			$pwopt .= " -M {$pwmax}";
		endif;
		if(!empty($pwmin)):
			$pwopt .= " -m {$pwmin}";
		endif;
		$grid_rcconf = &array_make_branch($config,'system','rcconf','param');
		$index = array_search_ex('powerd_flags',$grid_rcconf,'name');
		if($index !== false):
			$grid_rcconf[$index]['value'] = $pwopt;
		else:
			$grid_rcconf[] = [
				'uuid' => uuid(),
				'name' => 'powerd_flags',
				'value' => $pwopt,
				'comment' => 'System power control options',
				'enable' => true
			];
		endif;
		write_config();
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			config_lock();
			$retval |= rc_exec_service('rcconf');
			$retval |= rc_update_service('powerd');
			$retval |= rc_update_service('mdnsresponder');
			$retval |= rc_exec_service('motd');
			if(isset($config['system']['tune'])):
				$retval |= rc_update_service('sysctl');
			endif;
			$retval |= rc_update_service('syscons');
			$retval |= rc_update_service('fmperm');
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
	endif;
endif;
$pgtitle = [gtext('System'),gtext('Advanced Setup')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
<?php	// Init spinner onsubmit()?>
	$("#iform").submit(function() { spinner(); });
<?php	// Init click events?>
	$("#powerd").on("click",function(){ powerd_change(); });
<?php
if($is_sc):
?>
	$("#sysconsaver").on("click",function(){ sysconsaver_change() });
<?php
endif;
?>
});
function enable_change(enable_change) {
	var endis = !(enable_change);
	document.iform.pwmax.disabled = endis;
	document.iform.pwmin.disabled = endis;
<?php
if($is_sc):
?>
	document.iform.sysconsaverblanktime.disabled = endis;
<?php
endif;
?>
}
function powerd_change() {
	switch (document.iform.powerd.checked) {
		case true:
			showElementById('pwmode_tr','show');
			showElementById('pwmax_tr','show');
			showElementById('pwmin_tr','show');
			break;
		case false:
			showElementById('pwmode_tr','hide');
			showElementById('pwmax_tr','hide');
			showElementById('pwmin_tr','hide');
			break;
	}
}
<?php
if($is_sc):
?>
function sysconsaver_change() {
	switch (document.iform.sysconsaver.checked) {
		case true:
			showElementById('sysconsaverblanktime_tr','show');
			break;
		case false:
			showElementById('sysconsaverblanktime_tr','hide');
			break;
	}
}
<?php
endif;
?>
//]]>
</script>
<?php
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('system_advanced.php',gtext('Advanced'),gtext('Reload page'),true)->
			ins_tabnav_record('system_email.php',gtext('Email'))->
			ins_tabnav_record('system_email_reports.php',gtext('Email Reports'))->
			ins_tabnav_record('system_monitoring.php',gtext('Monitoring'))->
			ins_tabnav_record('system_swap.php',gtext('Swap'))->
			ins_tabnav_record('system_rc.php',gtext('Command Scripts'))->
			ins_tabnav_record('system_cron.php',gtext('Cron'))->
			ins_tabnav_record('system_loaderconf.php',gtext('loader.conf'))->
			ins_tabnav_record('system_rcconf.php',gtext('rc.conf'))->
			ins_tabnav_record('system_sysctl.php',gtext('sysctl.conf'))->
			ins_tabnav_record('system_syslogconf.php',gtext('syslog.conf'));
$document->render();
?>
<form action="system_advanced.php" method="post" id="iform" name="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('System Settings'));
?>
		</thead>
		<tbody>
<?php
			$node = new co_DOMDocument();
			$node->c2_checkbox($cop->zeroconf,!empty($pconfig['zeroconf']));
			$node->c2_checkbox($cop->disablefm,!empty($pconfig['disablefm']));
			if(('true' == $g['zroot']) || ('full' !== $g['platform'])):
				$node->c2_checkbox($cop->disablefirmwarecheck,!empty($pconfig['disablefirmwarecheck']));
			endif;
			$node->c2_checkbox($cop->enabletogglemode,!empty($pconfig['enabletogglemode']));
			$node->c2_checkbox($cop->skipviewmode,!empty($pconfig['skipviewmode']));
			$node->c2_checkbox($cop->adddivsubmittodataframe,!empty($pconfig['adddivsubmittodataframe']));
			$node->c2_checkbox($cop->shrinkpageheader,$_SESSION['g']['shrinkpageheader']);
			$node->c2_checkbox($cop->disableextensionmenu,!empty($pconfig['disableextensionmenu']));
			$node->c2_checkbox($cop->nonsidisksizevalues,!empty($pconfig['nonsidisksizevalues']));
			$node->render();
?>
		</tbody>
	</table>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_separator2();
			html_titleline2(gtext('Platform and Performance Settings'));
?>
		</thead>
		<tbody>
<?php
			$node = new co_DOMDocument();
			$node->c2_checkbox($cop->disablebeep,!empty($pconfig['disablebeep']));
			$node->c2_checkbox($cop->microcode_update,!empty($pconfig['microcode_update']));
			$node->c2_checkbox($cop->tune_enable,!empty($pconfig['tune_enable']));
			$node->c2_checkbox($cop->powerd,!empty($pconfig['powerd']));
			$node->c2_radio_grid($cop->pwmode,$pconfig['pwmode']);
			$node->render();
			$clocks = @exec("/sbin/sysctl -q -n dev.cpu.0.freq_levels");
			$a_freq = [];
			if(!empty($clocks)):
				$a_tmp = preg_split("/\s/",$clocks);
				foreach($a_tmp as $val):
					list($freq,$tmp) = preg_split("/\//",$val);
					if(!empty($freq)):
						$a_freq[] = $freq;
					endif;
				endforeach;
			endif;
			html_inputbox2('pwmax',gtext('CPU Maximum Frequency'),$pconfig['pwmax'],sprintf('%s %s',gtext('CPU frequencies:'),join(', ',$a_freq)) . '.<br />' . gtext('An empty field is default.'),false,5);
			html_inputbox2('pwmin',gtext('CPU Minimum Frequency'),$pconfig['pwmin'],gtext('An empty field is default.'),false,5);
?>
		</tbody>
	</table>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_separator2();
			html_titleline2(gtext('Console Settings'));
?>
		</thead>
		<tbody>
<?php
			$node = new co_DOMDocument();
			$node->c2_checkbox($cop->disableconsolemenu,!empty($pconfig['disableconsolemenu']));
			$node->c2_checkbox($cop->enableserialconsole,!empty($pconfig['enableserialconsole']));
			if($is_sc):
				$node->c2_checkbox($cop->sysconsaver,!empty($pconfig['sysconsaver']));
			endif;
			$node->render();
			if($is_sc):
				html_inputbox2('sysconsaverblanktime',gtext('Blank Time'),$pconfig['sysconsaverblanktime'],gtext('Turn the monitor to standby after N seconds.'),true,5);
			endif;
			$n_rows = min(64,max(8,1 + substr_count($pconfig['motd'],PHP_EOL)));
			html_textarea2('motd',gtext('MOTD'),$pconfig['motd'],gtext('Message of the day.'),false,65,$n_rows,false,false);
?>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=gtext('Save');?>" onclick="enable_change(true)"/>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<script type="text/javascript">
//<![CDATA[
<?php
if($is_sc):
?>
sysconsaver_change();
<?php
endif;
?>
powerd_change();
//]]>
</script>
<?php
include 'fend.inc';
?>