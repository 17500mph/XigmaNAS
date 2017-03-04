<?php
/*
	fbegin.inc

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
function gentitle(array $title = []) {
	$navlevelsep = htmlspecialchars(' > '); // Navigation level separator string.
	return implode($navlevelsep,$title);
}
function genhtmltitle(array $title = []) {
	return htmlspecialchars(system_get_hostname()) . (empty($title) ? '' : ' - ' . gentitle($title));
}
$isAdminSession = Session::isAdmin();
//	headermenu is reset / reread when
//	- language is changed (system.php)
//	- session is initiated via Session::_init()
if(empty($_SESSION['g']['headermenu'])):
	$menu = [];
	//	system
	$menu_name = 'system';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('System');
	$menu[$menu_name]['visible'] = TRUE;
	$menu[$menu_name]['link'] = '';
	$menu_items[] = ['desc' => gtext('General'),'link' => 'system.php','visible' => $isAdminSession];
	$menu_items[] = ['desc' => gtext('Advanced'),'link' => 'system_advanced.php','visible' => $isAdminSession];
	$menu_items[] = ['desc' => gtext('Password'),'link' => 'userportal_system_password.php','visible' => !$isAdminSession];
	$menu_items[] = ['type' => 'separator','visible' =>  $isAdminSession];
	if('full' == $g['platform']):
		$menu_items[] = ['desc' => gtext('Packages'),'link' => 'system_packages.php','visible' => $isAdminSession];
	else:
		$menu_items[] = ['desc' => gtext('Firmware Update'),'link' => 'system_firmware.php','visible' => $isAdminSession];
	endif;
	$menu_items[] = ['desc' => gtext('Backup/Restore'),'link' => 'system_backup.php','visible' => $isAdminSession];
	$menu_items[] = ['desc' => gtext('Factory Defaults'),'link' => 'system_defaults.php','visible' => $isAdminSession];
	$menu_items[] = ['type' => 'separator','visible' => $isAdminSession];
	$menu_items[] = ['desc' => gtext('Reboot'),'link' => 'reboot.php','visible' => $isAdminSession];
	$menu_items[] = ['desc' => gtext('Shutdown'),'link' => 'shutdown.php','visible' => $isAdminSession];
	$menu_items[] = ['type' => 'separator','visible' => true];
	$menu_items[] = ['desc' => gtext('Logout'),'link' => 'logout.php','visible' => true];
	unset($menu_items);
	//	network
	$menu_name = 'network';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Network');
	$menu[$menu_name]['visible'] = $isAdminSession;
	$menu[$menu_name]['link'] = '';
	$menu_items[] = ['desc' => gtext('Interface Management'),'link' => 'interfaces_assign.php','visible' => true];
	$menu_items[] = ['desc' => gtext('LAN Management'),'link' => 'interfaces_lan.php','visible' => true];
	for($i = 1;isset($config['interfaces']['opt' . $i]);$i++):
		$desc = $config['interfaces']['opt'.$i]['descr'];
		$link = 'interfaces_opt.php?index=' . $i;
		$menu_items[] = ['desc' => $desc,'link' => $link,'visible' => true];
	endfor;
	$menu_items[] = ['type' => 'separator','visible' => true];
	$menu_items[] = ['desc' => gtext('Hosts'),'link' => 'system_hosts.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Static Routes'),'link' => 'system_routes.php','visible' => true];
	$menu_items[] = ['type' => 'separator','visible' => true];
	$menu_items[] = ['desc' => gtext('Proxy'),'link' => 'system_proxy.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Firewall'),'link' => 'system_firewall.php','visible' => true];
	unset($menu_items);
	//	disks
	$menu_name = 'disks';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Disks');
	$menu[$menu_name]['visible'] = $isAdminSession;
	$menu[$menu_name]['link'] = '';
	$menu_items[] = ['desc' => gtext('Management'),'link' => 'disks_manage.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Software RAID'),'link' => 'disks_raid_geom.php','visible' => true];
	$menu_items[] = ['desc' => gtext('ZFS'),'link' => 'disks_zfs_zpool.php','visible' => true];
	$menu_items[] = ['type' => 'separator','visible' => true];
	$menu_items[] = ['desc' => gtext('Encryption'),'link' => 'disks_crypt.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Mount Point'),'link' => 'disks_mount.php','visible' => true];
	unset($menu_items);
	//	services
	$menu_name = 'services';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Services');
	$menu[$menu_name]['visible'] = $isAdminSession;
	$menu[$menu_name]['link'] = 'status_services.php';
	if('dom0' !== $g['arch']):
		$menu_items[] = ['desc' => gtext('HAST'),'link' => 'services_hast.php','visible' => true];
		$menu_items[] = ['desc' => gtext('Samba AD'),'link' => 'services_samba_ad.php','visible' => true];
		$menu_items[] = ['type' => 'separator','visible' => true];
		$menu_items[] = ['desc' => gtext('CIFS/SMB'),'link' => 'services_samba.php','visible' => true];
		$menu_items[] = ['desc' => gtext('FTP'),'link' => 'services_ftp.php','visible' => true];
		$menu_items[] = ['desc' => gtext('TFTP'),'link' => 'services_tftp.php','visible' => true];
		$menu_items[] = ['desc' => gtext('SSH'),'link' => 'services_sshd.php','visible' => true];
		$menu_items[] = ['desc' => gtext('NFS'),'link' => 'services_nfs.php','visible' => true];
		$menu_items[] = ['desc' => gtext('AFP'),'link' => 'services_afp.php','visible' => true];
		$menu_items[] = ['desc' => gtext('Rsync'),'link' => 'services_rsyncd.php','visible' => true];
		$menu_items[] = ['desc' => gtext('Syncthing'),'link' => 'services_syncthing.php','visible' => true];
		$menu_items[] = ['desc' => gtext('Unison'),'link' => 'services_unison.php','visible' => true];
		$menu_items[] = ['desc' => gtext('iSCSI Target'),'link' => 'services_iscsitarget.php','visible' => true];
		$menu_items[] = ['desc' => gtext('DLNA/UPnP'),'link' => 'services_fuppes.php','visible' => true];
		$menu_items[] = ['desc' => gtext('iTunes/DAAP'),'link' => 'services_daap.php','visible' => true];
		$menu_items[] = ['desc' => gtext('Dynamic DNS'),'link' => 'services_dynamicdns.php','visible' => true];
		$menu_items[] = ['desc' => gtext('SNMP'),'link' => 'services_snmp.php','visible' => true];
		$menu_items[] = ['desc' => gtext('UPS'),'link' => 'services_ups.php','visible' => true];
		$menu_items[] = ['desc' => gtext('Webserver'),'link' => 'services_websrv.php','visible' => true];
		$menu_items[] = ['desc' => gtext('BitTorrent'),'link' => 'services_bittorrent.php','visible' => true];
		$menu_items[] = ['desc' => gtext('LCDproc'),'link' => 'services_lcdproc.php','visible' => true];
	else:
		$menu_items[] = ['desc' => gtext('SSH'),'link' => 'services_sshd.php','visible' => true];
		$menu_items[] = ['desc' => gtext('NFS'),'link' => 'services_nfs.php','visible' => true];
		$menu_items[] = ['desc' => gtext('iSCSI Target'),'link' => 'services_iscsitarget.php','visible' => true];
		$menu_items[] = ['desc' => gtext('UPS'),'link' => 'services_ups.php','visible' => true];
	endif;
	unset($menu_items);
	//	virtualization
	$menu_name = 'vm';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	if('x64' == $g['arch']):
		$menu[$menu_name]['desc'] = gtext('Virtualization');
		$menu[$menu_name]['visible'] = $isAdminSession;
		$menu[$menu_name]['link'] = '';
		$menu_items[] = ['desc' => gtext('VirtualBox'),'link' => 'vm_vbox.php','visible' => $isAdminSession];
	endif;
	unset($menu_items);
	//	access
	$menu_name = 'access';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Access');
	$menu[$menu_name]['visible'] = $isAdminSession;
	$menu[$menu_name]['link'] = '';
	$menu_items[] = ['desc' => gtext('Users & Groups'),'link' => 'access_users.php','visible' => true];
	if('dom0' !== $g['arch']):
		$menu_items[] = ['desc' => gtext('Active Directory'),'link' => 'access_ad.php','visible' => true];
		$menu_items[] = ['desc' => gtext('LDAP'),'link' => 'access_ldap.php','visible' => true];
		$menu_items[] = ['desc' => gtext('NIS'),'link' => 'notavailable.php','visible' => false];
	endif;
	unset($menu_items);
	//	status
	$menu_name = 'status';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Status');
	$menu[$menu_name]['visible'] = $isAdminSession;
	$menu[$menu_name]['link'] = 'index.php';
	$menu_items[] = ['desc' => gtext('System'),'link' => 'index.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Processes'),'link' => 'status_process.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Services'),'link' => 'status_services.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Interfaces'),'link' => 'status_interfaces.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Disks'),'link' => 'status_disks.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Monitoring'),'link' => 'status_graph.php','visible' => true];
	unset($menu_items);
	//	tools
	$menu_name = 'tools';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Tools');
	$menu[$menu_name]['visible'] = true;
	$menu[$menu_name]['link'] = '';
	$menu_items[] = ['desc' => gtext('File Editor'),'link' => 'system_edit.php','visible' => $isAdminSession];
	if(!isset($config['system']['disablefm'])):
		$menu_items[] = ['desc' => gtext('File Manager'),'link' => '/quixplorer/system_filemanager.php','visible' => true];
	endif;
	$menu_items[] = ['type' => 'separator','visible' => $isAdminSession];
	$menu_items[] = ['desc' => gtext('Command'),'link' => 'exec.php','visible' => $isAdminSession];
	unset($menu_items);
	//	extensions
	$menu_name = 'extensions';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Extensions');
	$menu[$menu_name]['visible'] = false;
	$menu[$menu_name]['link'] = '';
	unset($menu_items);
	//	diagnostics
	$menu_name = 'diagnostics';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Diagnostics');
	$menu[$menu_name]['visible'] = $isAdminSession;
	$menu[$menu_name]['link'] = '';
	$menu_items[] = ['desc' => gtext('Log'),'link' => 'diag_log.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Information'),'link' => 'diag_infos_disks.php','visible' => true];
	$menu_items[] = ['type' => 'separator','visible' => true];
	$menu_items[] = ['desc' => gtext('Ping/Traceroute'),'link' => 'diag_ping.php','visible' => true];
	$menu_items[] = ['desc' => gtext('ARP Tables'),'link' => 'diag_arp.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Routes'),'link' => 'diag_routes.php','visible' => true];
	unset($menu_items);
	//	help
	$menu_name = 'help';
	$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
	$menu[$menu_name]['desc'] = gtext('Help');
	$menu[$menu_name]['visible'] = true;
	$menu[$menu_name]['link'] = '';
	$menu_items[] = ['desc' => gtext('Forum'),'link' => 'https://www.nas4free.org/forums/','visible' => true,'target' => '_blank'];
	$menu_items[] = ['desc' => gtext('Information & Manual'),'link' => 'https://www.nas4free.org/wiki/doku.php','visible' => true,'target' => '_blank'];
	$menu_items[] = ['desc' => gtext('IRC Live Support'),'link' => 'https://webchat.freenode.net/?channels=#nas4free','visible' => true,'target' => '_blank'];
	$menu_items[] = ['type' => 'separator','visible' => true];
	$menu_items[] = ['desc' => gtext('Release Notes'),'link' => 'changes.php','visible' => true];
	$menu_items[] = ['desc' => gtext('License & Credits'),'link' => 'license.php','visible' => true];
	$menu_items[] = ['desc' => gtext('Donate'),'link' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=info%40nas4free%2eorg&lc=US&item_name=NAS4Free%20Project&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest','visible' => TRUE,'target' => '_blank'];
	unset($menu_items);
	$_SESSION['g']['headermenu'] = $menu;
else:
	$menu = $_SESSION['g']['headermenu'];
endif;
function make_menu_extensions() {
	global $g;
	global $config;
	global $menu;
	
	if(!Session::isAdmin()): // only admins allowed
		return;
	endif;
	if(isset($config['system']) && is_array($config['system']) && isset($config['system']['disableextensionmenu'])): // do nothing when view extension is disabled
		return;
	endif;
	$dir_path = $g['www_path'] . '/ext/'; // calculate the path to the extension folder
	if(!is_dir($dir_path)): // does it exist?
		return;
	endif;
	if(false !== ($dir_handle = @opendir($dir_path))): // folder exists, open it
		$hard_link_regex = '~^[a-z]+://~';
		$menu_name = 'extensions';
		$menu_items = &array_make_branch($menu,$menu_name,'menuitem');
		while(false !== ($file_found_name = readdir($dir_handle))): // scan through each name
			$file_found_full_name = $dir_path . $file_found_name; // calculate full path of the name
			if(($file_found_name === '.') || ($file_found_name === '..') || ('dir' !== filetype($file_found_full_name))): // ignore files and special folder
				continue;
			endif;
			$menu_file_full_name = $file_found_full_name . '/menu.inc'; // calculate the expected menu name
			$previous_setting = libxml_use_internal_errors(true); // suppress exeptions
			libxml_clear_errors();
			$dom = new DOMDocument(); // import menu into DOM object, let it do it's job
			if(false !== ($dom->loadHTMLFile($menu_file_full_name,LIBXML_NOERROR || LIBXML_NOWARNING || LIBXML_HTML_NOIMPLIED || LIBXML_HTML_NODEFDTD))):
				$node_list = $dom->getElementsByTagName('a'); // get <a> elements
				foreach($node_list as $node_item):
					$link = trim($node_item->getAttribute('href')); // extract attribute href
					$description = $node_item->nodeValue; // get display value
					if(!empty($link)): // add target attribute when link looks like a hard link
						write_log('Menu found for: ' . $link);
						if(preg_match($hard_link_regex,$link)): // hard link?
							$menu_items[] = ['desc' => htmlspecialchars($description),'link' => $link,'visible' => true,'target' => '_blank'];
						else:
							$menu_items[] = ['desc' => htmlspecialchars($description),'link' => $link,'visible' => true];
						endif;
						$menu[$menu_name]['visible'] = true; // extension menu found, make menu head visible 
					endif;
				endforeach;
			endif;
			libxml_clear_errors();
			libxml_use_internal_errors($previous_setting);
		endwhile;
		unset($menu_items);
		closedir($dir_handle);
	endif;
}
function display_menu($menuid) {
	global $menu;
	
	if($menu[$menuid]['visible']): // render menu when visible
		$link = $menu[$menuid]['link'];
		if($link == ''):
			$link = 'index.php';
		endif;
		$hard_link_regex = '~^[a-z]+://~';
		echo "<li>\n";
		$agent = $_SERVER['HTTP_USER_AGENT']; // Put browser name into local variable for desktop/mobile detection
		if(preg_match('/(iphone|android)/i',$agent)):
			echo '<a href="javascript:mopen(\'',$menuid,'\');" onmouseout="mclosetime()">',$menu[$menuid]['desc'],"</a>\n";
		elseif(preg_match($hard_link_regex,$link)): // hard link = no spinner
			echo '<a href="',$link,'" onmouseover="mopen(\'',$menuid,'\')" onmouseout="mclosetime()">',$menu[$menuid]['desc'],"</a>\n";
		else: // local link = spinner
			echo '<a href="',$link,'" onclick="spinner()" onmouseover="mopen(\'',$menuid,'\')" onmouseout="mclosetime()">',$menu[$menuid]['desc'],"</a>\n";
		endif;
		echo '<div id="',$menuid,'" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">',"\n";
		// Display menu items.
		foreach($menu[$menuid]['menuitem'] as $menuk => $menuv):
			if($menuv['visible']): // render menuitem when visible
				if(!isset($menuv['type']) || 'separator' !== $menuv['type']): // Display menuitem.
					$link = $menuv['link'];
					if($link == ''):
						$link = 'index.php';
					endif;
					$target = $menuv['target'];
					if(empty($target)):
						$target = '_self';
					endif; 
					if(preg_match($hard_link_regex,$link)): // hard link = no spinner
						echo '<a href="',$link,'" target="',$target,'" title="',$menuv['desc'],'">',$menuv['desc'],'</a>',"\n";
					else: // local link = spinner
						echo '<a href="',$link,'" onclick="spinner()" target="',$target,'" title="',$menuv['desc'],'">',$menuv['desc'],'</a>',"\n";
					endif;
				else: // Display separator.
					echo '<span class="tabseparator">&nbsp;</span>';
				endif;
			endif;
		endforeach;
		echo '</div>',"\n";
		echo "</li>\n";
	endif;
}
header('Content-Type: text/html; charset=' . system_get_language_codeset());
  // XML declarations
/*
  some browser might be broken.
  echo '<?xml version="1.0" encoding="'.system_get_language_codeset().'"?>';
  echo "\n";
*/
echo '<!DOCTYPE html>',"\n"
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=system_get_language_code();?>" lang="<?=system_get_language_code();?>">
<head>
	<meta charset="<?=system_get_language_codeset();?>"/>
	<title><?=genhtmltitle($pgtitle ?? []);?></title>
	<?php if (isset($pgrefresh) && $pgrefresh):?>
	<meta http-equiv="refresh" content="<?=$pgrefresh;?>" />
	<?php endif;?>
	<link href="/css/gui.css" rel="stylesheet" type="text/css" />
	<link href="/css/navbar.css" rel="stylesheet" type="text/css" />
	<link href="/css/tabs.css" rel="stylesheet" type="text/css" />	
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/gui.js"></script>
	<script type="text/javascript" src="/js/spinner.js"></script>
	<script type="text/javascript" src="/js/spin.min.js"></script>
<?php
	if (isset($pglocalheader) && !empty($pglocalheader)) {
		if (is_array($pglocalheader)) {
			foreach ($pglocalheader as $pglocalheaderv) {
		 		echo $pglocalheaderv;
				echo "\n";
			}
		} else {
			echo $pglocalheader;
			echo "\n";
		}
	}
?>
</head>
<body id="main">
<div id="spinner_main"></div>
<div id="spinner_overlay" style="display: none; background-color: white; position: fixed; left:0; top:0; height:100%; width:100%; opacity: 0.25;"></div>
<header id="g4h">
<?php
	if(!$_SESSION['g']['shrinkpageheader']):
?>
	<div id="header">
		<div id="headerrlogo">
			<div class="hostname">
				<span><?=system_get_hostname();?>&nbsp;</span>
			</div>
		</div>
		<div id="headerlogo">
			<a title="www.<?=get_product_url();?>" href="https://www.<?=get_product_url();?>" target="_blank"><img src="/images/header_logo.png" alt="logo" /></a>
		</div>
	</div>
<?php
	endif; 
?>
	<div id="headernavbar">
		<ul id="navbarmenu">
<?php
			display_menu('system');
			display_menu('network');
			display_menu('disks');
			display_menu('access');
			display_menu('services');
			display_menu('vm');
			display_menu('status');
			display_menu('diagnostics');
			display_menu('tools');
			make_menu_extensions();
			display_menu('extensions');
			display_menu('help');
?>
		</ul>
		<div style="clear:both"></div>
	</div>
	<div id="gapheader"></div>
</header>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
<?php // init spinner on tabnav.?>
	$("#tabnav").on('click', function() { spinner(); });
<?php // and his mate.?>
	$("#tabnav2").on('click', function() { spinner(); });
});
//]]>
</script>
<main id="g4m">
<div id="pagecontent">
<?php
if(!isset($pgtitle_omit) || !$pgtitle_omit):
?>
	<p class="pgtitle"><?=gentitle($pgtitle);?></p>
<?php
endif;
?>
