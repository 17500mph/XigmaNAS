<?php
/*
	services_minidlna.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'services.inc';
require_once 'co_sphere.php';

function services_minidlna_get_sphere() {
	global $config;
	$sphere = new co_sphere_settings('services_minidlna','php');
	$sphere->row_default = [
		'enable' => false,
		'name' => '',
		'if' => '',
		'port' => '8200',
		'home' => '',
		'notify_int' => '300',
		'strict' => false,
		'loglevel' => 'info',
		'tivo' => false,
		'content' => [],
		'container' => 'B',
		'inotify' => true
	];
	$sphere->grid = &array_make_branch($config,'minidlna');
	if(empty($sphere->grid)):
		$sphere->grid = $sphere->row_default;
		write_config();
		header($sphere->get_location());
		exit;
	endif;
	array_make_branch($config,'minidlna','content');
	return $sphere;
}
$sphere = &services_minidlna_get_sphere();
$gt_button_apply_confirm = gtext('Do you want to apply these settings?');
$input_errors = [];
$a_message = [];
sort($sphere->grid['content']);
//	we need information about other DLNA services
array_make_branch($config,'upnp');
/*	calculate initial page mode and page action.
 *	at the end of this section a valid page mode and a valid page action are available.
 *	page_action cancel is switched to view mode.
 *	mode_page: page_action:
 *		PAGE_MODE_EDIT: edit
 *		PAGE_MODE_POST: enable, disable, rescan, save
 *		PAGE_MODE_VIEW: view
 */
$mode_page = ($_POST) ? PAGE_MODE_POST : PAGE_MODE_VIEW;
switch($mode_page):
	case PAGE_MODE_POST:
		if(isset($_POST['submit'])):
			$page_action = $_POST['submit'];
			switch($page_action):
				case 'edit':
					$mode_page = PAGE_MODE_EDIT;
					break;
				case 'rescan':
					break;
				case 'save':
					break;
				case 'enable':
					break;
				case 'disable':
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
		$source = $sphere->grid;
		break;
endswitch;
$sphere->row['enable'] = isset($source['enable']);
$sphere->row['name'] = $source['name'] ?? $sphere->row_default['name'];
$sphere->row['if'] = $source['if'] ?? $sphere->row_default['if'];
$sphere->row['port'] = $source['port'] ?? $sphere->row_default['port'];
$sphere->row['home'] = $source['home'] ?? $sphere->row_default['home'];
$sphere->row['notify_int'] = $source['notify_int'] ?? $sphere->row_default['notify_int'];
$sphere->row['strict'] = isset($source['strict']);
$sphere->row['loglevel'] = $source['loglevel'] ?? $sphere->row_default['loglevel'];
$sphere->row['tivo'] = isset($source['tivo']);
$sphere->row['content'] = $source['content'] ?? $sphere->row_default['content'];
$sphere->row['container'] = $source['container'] ?? $sphere->row_default['container'];
$sphere->row['inotify'] = isset($source['inotify']);
//	process enable and rescan
switch($page_action):
	case 'enable':
		if($sphere->row['enable']):
			$mode_page = PAGE_MODE_VIEW;
			$page_action = 'view'; 
		else: // enable and run a full validation
			$sphere->row['enable'] = true;
			$page_action = 'save'; // continue with save procedure
		endif;
		break;
	case 'rescan':
		if($sphere->row['enable']):
			mwexec_bg('service minidlna rescan');
			$a_message[] = gtext('A rescan has been issued.');
		endif;
		$mode_page = PAGE_MODE_VIEW;
		$page_action = 'view';
		break;
endswitch;
//	process save and disable
switch($page_action):
	case 'save':
		//	validate name
		if(is_string($sphere->row['name'])):
			if(preg_match('/\S/',$sphere->row['name'])):
			else:
				$input_errors[] = gtext('The name of the media server cannot be empty.');
				$sphere->row['name'] = '';
			endif;
		else:
			$input_errors[] = gtext('The name of the media server is missing.');
			$sphere->row['name'] = '';
		endif;
		//	validate interface
		if(is_string($sphere->row['if'])):
			if(preg_match('/\S/',$sphere->row['if'])):
				// check if if is on the list of interfaces
				if(true):
				else:
					$input_errors[] = gtext('The interface is unknown.');
				endif;
			endif;
		else:
			$sphere->row['if'] = '';
		endif;
		//	check port range.
		if(!is_string($sphere->row['port'])):
			$sphere->row['port'] = $sphere->row_default['port'];
		endif;
		if((1024 > $sphere->row['port']) || (65535 < $sphere->row['port'])):
			$input_errors[] = sprintf(gtext("Port number must be in the range between %d and %d."),1024,65535);
		endif;
		//	check home folder
		if(!is_string($sphere->row['home'])):
			$sphere->row['home'] = '';
		endif;
		if(preg_match('/\S/',$sphere->row['home'])):
			if(is_dir($sphere->row['home'])):
			else:
				$input_errors[] = gtext('The location of the "Database Directory" is not a valid location.');
			endif;
		else:
			$input_errors[] = gtext('Please define the location for the database directory.');
		endif;
		if(!is_string($sphere->row['notify_int'])):
			$sphere->row['notify_int'] = '300';
		endif;
		if(!is_string($sphere->row['loglevel'])):
			$sphere->row['loglevel'] = 'info';
		endif;
		if(!is_array($sphere->row['content'])):
			$sphere->row['content'] = [];
		endif;
		if(empty($sphere->row['content'])):
			$input_errors[] = gtext('Please define one or more content locations.');
		endif;
		if(!is_string($sphere->row['container'])):
			$sphere->row['container'] = 'B';
		endif;
		// all checks passed
		if(empty($input_errors)):
			$sphere->copyrowtogrid();
			write_config();
			$retval = 0;
			chown($sphere->row['home'],'dlna');
			chmod($sphere->row['home'],0755);
			config_lock();
			$retval != rc_stop_service('minidlna');
			$retval = $retval << 1;
			$retval |=  rc_update_service('minidlna');
			$retval = $retval << 1;
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			header($sphere->get_location());
			exit;
		else:
			$mode_page = PAGE_MODE_EDIT;
			$page_action = 'edit';
		endif;
		break;
	case 'disable':
		if($sphere->row['enable']): // if enabled, disable it
			$sphere->row['enable'] = false;
			$sphere->grid['enable'] = $sphere->row['enable'];
			write_config();
			$retval = 0;
			config_lock();
			$retval |= rc_update_service('minidlna');
			$retval |= rc_update_service('mdnsresponder');
			config_unlock();
			header($sphere->get_location());
			exit;
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
//	list of container types
$l_container = [
	'.' => gettext('Standard'),
	'B' => gettext('Browse Directory'),
	'M' => gettext('Music'),
	'V' => gettext('Video'),
	'P' => gettext('Pictures')
];
//	list of log levels
$l_loglevel = [
	'off' => gettext('Off'),
	'fatal' => gettext('Fatal'),
	'error' => gettext('Error'),
	'warn' => gettext('Warning'),
	'info' => gettext('Info'),
	'debug' => gettext('Debug')
];
//	identifiy enabled DLNA services
$dlna_count = 0;
$dlna_count += isset($config['minidlna']['enable']) ? 1 : 0;
$dlna_count += isset($config['upnp']['enable']) ? 2 : 0;
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
			$a_message[] = gtext('Another DLNA/UPnP service is already running. Enabling MiniDLNA might cause issues.');
		endif;
		break;
endswitch;
$pgtitle = [gtext('Services'),gtext('DLNA/UPnP MiniDLNA')];
include 'fbegin.inc';
switch($mode_page):
	case PAGE_MODE_VIEW:
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
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
	$("#iform").submit(function() {
		onsubmit_content();
		spinner();
	});
	$(".spin").click(function() { spinner(); });
	$("#button_save").click(function () {
		return confirm("<?=$gt_button_apply_confirm;?>");
	});
});
//]]>
</script>
<?php
		break;
endswitch;	
?>
<table id="area_navigator"><tbody><tr><td class="tabnavtbl">
	<ul id="tabnav">
		<li class="tabinact"><a href="services_fuppes.php"><span><?=gtext('Fuppes')?></span></a></li>
		<li class="tabact"><a href="services_minidlna.php"><span><?=gtext('MiniDLNA');?></span></a></li>
	</ul>
</td></tr></tbody></table>
<form action="<?=$sphere->get_scriptname();?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
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
					html_titleline2(gettext('MiniDLNA A/V Media Server'));
					break;
				case PAGE_MODE_EDIT:
					html_titleline_checkbox2('enable',gettext('MiniDLNA A/V Media Server'),$sphere->row['enable'],gettext('Enable'));
					break;
			endswitch;
?>
		</thead>
		<tbody>
<?php
			switch($mode_page):
				case PAGE_MODE_VIEW:
					html_textinfo2('enable',gettext('Service Enabled'),$sphere->row['enable'] ? gettext('Yes') : gettext('No'));
					html_textinfo2('name',gettext('Name'),$sphere->row['name']);
					html_textinfo2('if',gettext('Interface Selection'),$sphere->row['if']);
					html_textinfo2('port',gettext('Port'),$sphere->row['port']);
					html_textinfo2('notify_int',gettext('Broadcast Interval'),$sphere->row['notify_int']);
					html_textinfo2('home',gettext('Database Directory'),$sphere->row['home']);
					$helpinghand = implode(PHP_EOL,$sphere->row['content']);
					html_textarea2('content',gettext('Content Locations'),$helpinghand,'',false,67,5,true,false);
					html_checkbox2('inotify',gettext('Inotify'),$sphere->row['inotify'],'','',false,true);
					html_textinfo2('container',gettext('Container'),$l_container[$sphere->row['container']] ?? '');
					html_checkbox2('strict',gettext('Strict DLNA'),$sphere->row['strict'],'','',false,true);
					html_checkbox2('tivo',gettext('TiVo Support'),$sphere->row['tivo'],'','',false,true);
					html_textinfo2('loglevel',gettext('Log Level'),$l_loglevel[$sphere->row['loglevel']] ?? '');
					break;
				case PAGE_MODE_EDIT:
					html_inputbox2('name',gettext('Name'),$sphere->row['name'],gettext('Give your media library a friendly name.'),true,35,false,false,35,gettext('Media server name'));
					html_combobox2('if',gettext('Interface Selection'),$sphere->row['if'],$l_interfaces,gettext('Select which interface to use. (Only selectable if your server has more than one interface)'),true);
					html_inputbox2('port',gettext('Port'),$sphere->row['port'],sprintf(gettext('Port to listen on. Only dynamic or private ports can be used (from %d through %d). Default port is %d.'),1025,65535, 8200),true,5);
					html_inputbox2('notify_int',gettext('Broadcast Interval'),$sphere->row['notify_int'],gettext('Broadcasts its availability every N seconds on the network. (Default 300 seconds)'),true,5);
					html_filechooser2('home',gettext('Database Directory'),$sphere->row['home'],gettext('Location of the media content database.'),$g['media_path'],true,67);
					html_minidlnabox2('content',gettext('Content Locations'),$sphere->row['content'],gettext('Manage content locations.'),$g['media_path'],true);
					html_checkbox2('inotify',gettext('Inotify'),$sphere->row['inotify'],gettext('Enable inotify.'),gettext('Use inotify monitoring to automatically discover new files.'),false);
					html_combobox2('container',gettext('Container'),$sphere->row['container'],$l_container,gettext('Use different container as root of the tree.'),false,false,'');
					html_checkbox2('strict',gettext('Strict DLNA'),$sphere->row['strict'],gettext('Enable to strictly adhere to DLNA standards.'),gettext('This will allow server-side downscaling of very large JPEG images, it can impact JPEG serving performance on some DLNA products.'),false);
					html_checkbox2('tivo',gettext('TiVo Support'),$sphere->row['tivo'],gettext('Enable TiVo support.'),gettext('This will support streaming .jpg and .mp3 files to a TiVo supporting HMO.'),false);
					html_combobox2('loglevel',gettext('Log Level'),$sphere->row['loglevel'],$l_loglevel,'',false,false,'');
					break;
			endswitch;
			if($dlna_option & 1):
				html_separator2();
				html_titleline2(gettext('MiniDLNA Media Server WebGUI'));
				$if = get_ifname($sphere->row['if']);
				$ipaddr = get_ipaddr($if);
				$url = sprintf('http://%s:%s/status',htmlspecialchars($ipaddr),htmlspecialchars($sphere->row['port']));
				$text = sprintf('<a href="%s" target="_blank">%s</a>',$url,$url);
				html_textinfo2('url',gettext('URL'),$text);
			endif;
?>
		</tbody>
	</table>
	<div id="submit">
<?php
		switch($mode_page):
			case PAGE_MODE_VIEW:
				echo $sphere->html_button('edit',gettext('Edit'));
				if($dlna_option & 1):
					echo $sphere->html_button('rescan',gettext('Rescan'));
				endif;
				if($sphere->row['enable']):
					echo $sphere->html_button('disable',gettext('Disable'));
				else:
					echo $sphere->html_button('enable',gettext('Enable'));
				endif;
				break;
			case PAGE_MODE_EDIT:
				echo $sphere->html_button('save',gettext('Apply'));
				echo $sphere->html_button('cancel',gettext('Cancel'));
				break;
		endswitch;
?>
	</div>
<?php
include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
