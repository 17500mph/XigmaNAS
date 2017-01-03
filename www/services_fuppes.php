<?php
/*
	services_fuppes.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright
	   notice, this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE NAS4FREE PROJECT ``AS IS'' AND ANY
	EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
	IN NO EVENT SHALL THE NAS4FREE PROJECT OR ITS CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
	THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
require 'auth.inc';
require 'guiconfig.inc';
require 'services.inc';

$sphere_scriptname = basename(__FILE__);
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = 'Location: index.php';
$sphere_array = [];
$sphere_record = [];

$mode_page = ($_POST) ? PAGE_MODE_POST : PAGE_MODE_EDIT; // detect page mode
if(PAGE_MODE_POST == $mode_page):
	if(isset($_POST['submit'])):
		switch($_POST['submit']):
			case 'save':
				break;
			case 'cancel':
				header($sphere_header_parent);
				exit;
				break;
			default:
				header($sphere_header_parent);
				exit;
				break;
		endswitch;
	endif;
endif;

$sphere_array = &array_make_branch($config,'upnp');
array_make_branch($config,'upnp','content');
sort($config['upnp']['content']);
// we need information about other DLNA services
array_make_branch($config,'minidlna','content');

if(PAGE_MODE_POST === $mode_page):
	unset($input_errors);

	$sphere_record['enable'] = isset($_POST['enable']);
	$sphere_record['name'] = $_POST['name'] ?? '';	
	$sphere_record['if'] = $_POST['if'] ?? '';
	$sphere_record['port'] = $_POST['port'] ?? '';
	$sphere_record['home'] = $_POST['home'] ?? '';
	$sphere_record['profile'] = $_POST['profile'] ?? '';
	$sphere_record['deviceip'] = $_POST['deviceip'] ?? '';
	$sphere_record['transcoding'] = isset($_POST['transcoding']);
	$sphere_record['tempdir'] = $_POST['tempdir'] ?? '';
	$sphere_record['content'] = is_array($_POST['content']) ? $_POST['content'] : [];

	// Input validation.
	$reqdfields = ['name','if','port','content','home'];
	$reqdfieldsn = [gtext('Name'),gtext('Interface'),gtext('Port'),gtext('Media library'),gtext('Database directory')];
	$reqdfieldst = ['string','string','port','array','string'];
	if(0 === strcmp('Terratec_Noxon_iRadio',$sphere_record['profile'])):
		$reqdfields[] = 'deviceip';
		$reqdfieldsn[] = gtext('Device IP');
		$reqdfieldst[] = 'ipaddr';
	endif;
	if($sphere_record['transcoding']):
		$reqdfields[] = 'tempdir';
		$reqdfieldsn[] = gtext('Temporary directory');
		$reqdfieldst[] = 'string';
	endif;
	do_input_validation($sphere_record,$reqdfields,$reqdfieldsn,$input_errors);
	do_input_validation_type($sphere_record,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);

	// Check if port is already used.
	if(services_is_port_used($sphere_record['port'],'upnp')):
		$input_errors[] = sprintf(gtext("The attribute 'Port': port '%ld' is already taken by another service."),$sphere_record['port']);
	endif;
	
	// Check port range.
	if($sphere_record['port'] && ((1024 > $sphere_record['port']) || (65535 < $sphere_record['port']))):
		$input_errors[] = sprintf(gtext("The attribute '%s': use a port in the range from %d to %d."),gtext('Port'),1025,65535);
	endif;
	
	// all checks passed
	if(empty($input_errors)):
		$sphere_array = $sphere_record;
		write_config();
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			config_lock();
			$retval |= rc_update_service('fuppes');
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
		if($retval == 0):
			if(file_exists($d_upnpconfdirty_path)):
				unlink($d_upnpconfdirty_path);
			endif;
		endif;
	endif;
else:
	$sphere_record['enable'] = isset($sphere_array['enable']);
	$sphere_record['name'] = $sphere_array['name'] ?? '';
	$sphere_record['if'] = $sphere_array['if'] ?? '';
	$sphere_record['port'] = $sphere_array['port'] ?? '';
	$sphere_record['home'] = $sphere_array['home'] ?? '';
	$sphere_record['profile'] = $sphere_array['profile'] ?? '';
	$sphere_record['deviceip'] = $sphere_array['deviceip'] ?? '';
	$sphere_record['transcoding'] = isset($sphere_array['transcoding']);
	$sphere_record['tempdir'] = $sphere_array['tempdir'] ?? '';
	$sphere_record['content'] = $sphere_array['content'] ?? [];
endif;
$a_interface = get_interface_list();
$l_interfaces = [];
foreach($a_interface as $k_interface => $ifinfo):
	$ifinfo = get_interface_info($k_interface);
	switch($ifinfo['status']):
		case 'up':
		case 'associated':
			$l_interfaces[$k_interface] = $k_interface;
			break;
	endswitch;
endforeach;
$l_dlna = [
	'default' => gtext('Default'),
	'DLNA' => 'DLNA',
	'Denon_AVR' => 'DENON Network A/V Receiver',
	'PS3' => 'Sony Playstation 3',
	'Telegent_TG100' => 'Telegent TG100',
	'ZyXEL_DMA1000' => 'ZyXEL DMA-1000',
	'Helios_X3000' => 'Helios X3000',
	'DLink_DSM320' => 'D-Link DSM-320',
	'Microsoft_XBox360' => 'Microsoft XBox 360',
	'Terratec_Noxon_iRadio' => 'Terratec Noxon iRadio',
	'Yamaha_RXN600' => 'Yamaha RX-N600',
	'Loewe_Connect' => 'Loewe Connect'
];
// Identifiy enabled DLNA services
$dlna_count = 0;
if(isset($config['upnp']['enable'])):
	$dlna_count += 1;
endif;
if(isset($config['minidlna']['enable'])):
	$dlna_count += 2;
endif;
// everything greater than 1 indicates that another DLNA service is running somewhere else
// every odd number  indicates that this DLNA service is enabled.
switch($dlna_count):
	case 0:
		$dlna_option = 0; // DLNA can be enabled, no access to link
		break;
	case 1:
		$dlna_option = 1; // DLNA can be disabled, access to link
		break;
	default:
		if($dlna_count & 1):
			$dlna_option = 3; // Warning, DLNA can be disabled, access to link
			$savemsg = gtext('More than one DLNA/UPnP service is active. This configuration might cause issues.');
		else:
			$dlna_option = 2; // Warning, DLNA no access to enable, no access to link
			$savemsg = gtext('Another DLNA/UPnP service is already running. Enabling Fuppes might cause issues.');
		endif;
		break;
endswitch;
$pgtitle = [gtext('Services'),gtext('DLNA/UPnP Fuppes')];
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init onsubmit()
	$("#iform").submit(function() {
		onsubmit_content();
		spinner();
	});
});
function profile_change() {
	switch(document.iform.profile.value) {
		case "Terratec_Noxon_iRadio":
			showElementById('deviceip_tr','show');
			break;
		default:
			showElementById('deviceip_tr','hide');
			break;
	}
}
function transcoding_change() {
	switch(document.iform.transcoding.checked) {
		case false:
			showElementById('tempdir_tr','hide');
			break;
		case true:
			showElementById('tempdir_tr','show');
			break;
	}
}
//]]>
</script>
<table id="area_navigator"><tbody><tr><td class="tabnavtbl">
	<ul id="tabnav">
		<li class="tabact"><a href="services_fuppes.php"><span><?=gtext('Fuppes')?></span></a></li>
		<li class="tabinact"><a href="services_minidlna.php"><span><?=gtext('MiniDLNA');?></span></a></li>
	</ul>
</td></tr></tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform">
	<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
	if(file_exists($d_upnpconfdirty_path)):
		print_config_change_box();
	endif;
	?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
			<?php html_titleline_checkbox2('enable',gtext('Fuppes Media Server'),$sphere_record['enable'],gtext('Enable'));?>
		</thead>
		<tbody>
			<?php
			html_inputbox2('name',gtext('Name'),$sphere_record['name'],gtext('Give your media library a friendly name.'),true,35,false,false,35,gtext('Media server name'));
			html_combobox2('if',gtext('Interface Selection'),$sphere_record['if'],$l_interfaces,gtext('Select which interface to use. (Only selectable if your server has more than one interface)'),true);
			html_inputbox2('port',gtext('Port'),$sphere_record['port'],sprintf(gtext('Port to listen on. Only dynamic or private ports can be used (from %d through %d). Default port is %d.'),1025,65535,49152),true,5);
			html_filechooser2('home',gtext('Database Directory'),$sphere_record['home'],gtext('Location of the media content database.'), $g['media_path'],true,67);
			html_folderbox2('content',gtext('Media Library'),$sphere_record['content'],gtext("Set the content location(s) to or from the media library."),$g['media_path'],true);
			html_combobox2('profile',gtext('Profile'), $sphere_record['profile'],$l_dlna,gtext('Compliant profile to be used.'),true,false,'profile_change()');
			html_inputbox2('deviceip',gtext('Device IP'),$sphere_record['deviceip'], gtext('The IP address of the device.'),true,20);
			html_checkbox2('transcoding',gtext('Transcoding'),$sphere_record['transcoding'],gtext('Enable transcoding.'),'',false,false,'transcoding_change()');
			html_filechooser2('tempdir',gtext('Transcoding Directory'),$sphere_record['tempdir'],gtext('Temporary directory to store transcoded files.'),$g['media_path'],true,67);
			if($dlna_option & 1):
				html_separator2();
				html_titleline2(gtext('Fuppes Media Server Administration'));
				$if = get_ifname($sphere_record['if']);
				$ipaddr = get_ipaddr($if);
				$url = htmlspecialchars(sprintf('http://%s:%s',$ipaddr,$sphere_record['port']));
				$text = sprintf('<a href="%s" target="_blank">%s</a>',$url,$url);
				html_text2('url',gtext('URL'),$text);
			endif;
			?>
		</tbody>
	</table>
	<div id="submit">
		<?php
		echo html_button_save(gtext('Apply'));
		echo html_button_cancel(gtext('Cancel'));
		?>
	</div>
	<?php
	include 'formend.inc';?>
</form></td></tr></tbody></table>
<script type="text/javascript">
//<![CDATA[
profile_change();
transcoding_change();
//]]>
</script>
<?php include 'fend.inc';?>
