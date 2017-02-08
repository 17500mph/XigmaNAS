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
$sphere_record = [];
$a_message = [];
$sphere_default = [
	'enable' => false,
	'name' => '',
	'if' => '',
	'port' => '49152',
	'home' => '',
	'profile' => 'default',
	'deviceip' => '',
	'transcoding' => '',
	'tempdir' => '',
	'content' => []
];
$gt_apply_confirm = gtext('Do you want to apply these settings?');
$input_errors = [];
//	sunrise
array_make_branch($config,'upnp','content');
$sphere_array = &$config['upnp'];
sort($sphere_array['content']);
// we need information about other DLNA services
array_make_branch($config,'minidlna','content');
/*	calculate initial page mode and page action.
 *	at the end of this section a valid page mode and a valid page action are available.
 *	page_action cancel is switched to view mode.
 *	mode_page: page_action:
 *		PAGE_MODE_EDIT: edit
 *		PAGE_MODE_POST: enable, disable, save
 *		PAGE_MODE_VIEW: view
 */
$mode_page = ($_POST) ? PAGE_MODE_POST : PAGE_MODE_EDIT; // detect page mode
switch($mode_page):
	case PAGE_MODE_POST:
		if(isset($_POST['submit'])):
			$page_action = $_POST['submit'];
			switch($page_action):
				case 'edit':
					$mode_page = PAGE_MODE_EDIT;
					break;
				case 'save':
					break;
				case 'rows.enable':
					$page_action = 'enable';
					break;
				case 'rows.disable':
					$page_action = 'disable';
					break;
				case 'cancel':
					$mode_page = PAGE_MODE_VIEW;
					$page_action = 'view';
					break;
				default:
					$mode_page = PAGE_MODE_VIEW;
					$page_action = 'view';
					break;
			endswitch;
		else:
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view';
		endif;
		break;
	case PAGE_MODE_VIEW:
		$page_action = 'view';
		break;
	case PAGE_MODE_EDIT:
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;			
	default:
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;
endswitch;
//	get configuration data, depending on the source
switch($page_action):
	case 'save':
		$source = $_POST;
		break;
	default:
		$source = $sphere_array;
		break;
endswitch;
$sphere_record['enable'] = isset($source['enable']);
$sphere_record['name'] = $source['name'] ?? $sphere_default['name'];
$sphere_record['if'] = $source['if'] ?? $sphere_default['if'];
$sphere_record['port'] = $source['port'] ?? $sphere_default['port'];
$sphere_record['home'] = $source['home'] ?? $sphere_default['home'];
$sphere_record['profile'] = $source['profile'] ?? $sphere_default['profile'];
$sphere_record['deviceip'] = $source['deviceip'] ?? $sphere_default['deviceip'];
$sphere_record['transcoding'] = isset($source['transcoding']);
$sphere_record['tempdir'] = $source['tempdir'] ?? $sphere_default['tempdir'];
$sphere_record['content'] = $source['content'] ?? $sphere_default['content'];
//	process enable
switch($page_action):
	case 'enable':
		if($sphere_record['enable']):
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view'; 
		else: // enable and run a full validation
			$sphere_record['enable'] = true;
			$page_action = 'save'; // continue with save procedure
		endif;
		break;
endswitch;
//	process save and disable
switch($page_action):
	case 'save':
		//	input validation.
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
		//	check if port is already used.
		if(services_is_port_used($sphere_record['port'],'upnp')):
			$input_errors[] = sprintf(gtext("The attribute 'Port': port '%ld' is already taken by another service."),$sphere_record['port']);
		endif;
		//	check port range.
		if($sphere_record['port'] && ((1024 > $sphere_record['port']) || (65535 < $sphere_record['port']))):
			$input_errors[] = sprintf(gtext("Port number must be in the range between %d and %d."),gtext('Port'),1024,65535);
		endif;
		//	all checks passed
		if(empty($input_errors)):
			$sphere_array = $sphere_record;
			write_config();
			$retval = 0;
			config_lock();
			$retval |= rc_update_service('fuppes');
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			$a_message[] = gtext('Changes have been applied.');
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view';
		else:
			$mode_page = PAGE_MODE_EDIT;
			$page_action = 'edit';
		endif;
		break;
	case 'disable':
		if($sphere_record['enable']): // if enabled, disable it
			$sphere_record['enable'] = false;
			$sphere_array = $sphere_record;
			write_config();
			$retval = 0;
			config_lock();
			$retval |= rc_update_service('fuppes');
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			$a_message[] = gtext('Fuppes has been disabled.');
		endif;
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;
endswitch;
//	determine final page mode
switch($mode_page):
	case PAGE_MODE_EDIT:
		break;
/*
	case PAGE_MODE_VIEW:
 */
	default:
		if(isset($config['system']['skipviewmode'])):
			$mode_page = PAGE_MODE_EDIT;
			$page_action = 'edit';
		else:
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view';
		endif;
		break;
endswitch;
//	list of configured interfaces
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
//	list of supported DLNA devices
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
//	identifiy enabled DLNA services
$dlna_count = 0;
$dlna_count += (isset($config['upnp']['enable'])) ? 1 : 0;
$dlna_count += (isset($config['minidlna']['enable'])) ? 2 : 0;
//	everything greater than 1 indicates that another DLNA service is running somewhere else
//	every odd number indicates that this DLNA service is enabled.
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
			$a_message[] = gtext('More than one DLNA/UPnP service is active. This configuration might cause issues.');
		else:
			$dlna_option = 2; // Warning, DLNA no access to enable, no access to link
			$a_message[] = gtext('Another DLNA/UPnP service is already running. Enabling Fuppes might cause issues.');
		endif;
		break;
endswitch;
$pgtitle = [gtext('Services'),gtext('DLNA/UPnP Fuppes')];
include 'fbegin.inc';
switch($mode_page):
	case PAGE_MODE_VIEW:
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init onsubmit()
	$("#iform").submit(function() {
		spinner();
	});
});
//]]>
</script>
<?php
		break;
	case PAGE_MODE_EDIT:
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init onsubmit()
	$("#iform").submit(function() {
		onsubmit_content();
		spinner();
	});
	$("#button_save").click(function () {
		return confirm('<?=$gt_apply_confirm;?>');
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
<?php
		break;
endswitch;	
?>
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
	foreach($a_message as $r_message):
		print_info_box($r_message);
	endforeach;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			switch($mode_page):
				case PAGE_MODE_VIEW:
					html_titleline2(gtext('Fuppes Media Server'));
					break;
				case PAGE_MODE_EDIT:
					html_titleline_checkbox2('enable',gtext('Fuppes Media Server'),$sphere_record['enable'],gtext('Enable'));
					break;
			endswitch;
?>
		</thead>
		<tbody>
<?php
			switch($mode_page):
				case PAGE_MODE_VIEW:
					html_text2('enable',gtext('Service Enabled'),$sphere_record['enable'] ? gtext('Yes') : gtext('No'));
					html_text2('name',gtext('Name'), htmlspecialchars($sphere_record['name']));
					html_text2('if',gtext('Interface Selection'), htmlspecialchars($sphere_record['if']));
					html_text2('port',gtext('Port'), htmlspecialchars($sphere_record['port']));
					html_text2('home',gtext('Database Directory'), htmlspecialchars($sphere_record['home']));
					$helpinghand = implode("\n",$sphere_record['content']);
					html_textarea2('content',gtext('Content Locations'),$helpinghand,'',false,67,5,true,false);
					html_text2('profile',gtext('Profile'),$l_dlna[$sphere_record['profile']] ?? '');
					switch($sphere_record['profile']):
						case 'Terratec_Noxon_iRadio':
							html_text2('deviceip',gtext('Device IP'), htmlspecialchars($sphere_record['deviceip']));
							break;
					endswitch;
					html_checkbox2('transcoding',gtext('Transcoding'),$sphere_record['transcoding'],'','',false,true);
					if($sphere_record['transcoding']):
						html_text2('tempdir',gtext('Transcoding Directory'),htmlspecialchars($sphere_record['tempdir']));
					endif;
					break;
				case PAGE_MODE_EDIT:
					html_inputbox2('name',gtext('Name'),$sphere_record['name'],gtext('Give your media library a friendly name.'),true,35,false,false,35,gtext('Media server name'));
					html_combobox2('if',gtext('Interface Selection'),$sphere_record['if'],$l_interfaces,gtext('Select which interface to use. (Only selectable if your server has more than one interface)'),true);
					html_inputbox2('port',gtext('Port'),$sphere_record['port'],sprintf(gtext('Port to listen on. Only dynamic or private ports can be used (from %d through %d). Default port is %d.'),1025,65535,49152),true,5);
					html_filechooser2('home',gtext('Database Directory'),$sphere_record['home'],gtext('Location of the media content database.'), $g['media_path'],true,67);
					html_folderbox2('content',gtext('Media Library'),$sphere_record['content'],gtext("Set the content location(s) to or from the media library."),$g['media_path'],true);
					html_combobox2('profile',gtext('Profile'), $sphere_record['profile'],$l_dlna,gtext('Compliant profile to be used.'),true,false,'profile_change()');
					html_inputbox2('deviceip',gtext('Device IP'),$sphere_record['deviceip'], gtext('The IP address of the device.'),true,20);
					html_checkbox2('transcoding',gtext('Transcoding'),$sphere_record['transcoding'],gtext('Enable transcoding.'),'',false,false,'transcoding_change()');
					html_filechooser2('tempdir',gtext('Transcoding Directory'),$sphere_record['tempdir'],gtext('Temporary directory to store transcoded files.'),$g['media_path'],true,67);
				break;
			endswitch;
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
		switch($mode_page):
			case PAGE_MODE_VIEW;
				echo html_button_edit(gtext('Edit'));
				if($sphere_record['enable']):
					echo html_button_disable_rows(gtext('Disable'));
				else:
					echo html_button_enable_rows(gtext('Enable'));
				endif;
				break;
			case PAGE_MODE_EDIT:
				echo html_button_save(gtext('Apply'));
				echo html_button_cancel(gtext('Cancel'));
				break;
		endswitch;
?>
	</div>
<?php
	include 'formend.inc';
?>
</form></td></tr></tbody></table>
<?php
switch($mode_page):
	case PAGE_MODE_EDIT:
?>
<script type="text/javascript">
//<![CDATA[
profile_change();
transcoding_change();
//]]>
</script>
<?php
		break;
endswitch;
include 'fend.inc';
?>
