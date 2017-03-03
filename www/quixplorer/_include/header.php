<?php
/*
	header.php

	Part of NAS4Free (https://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of Quixplorer (http://quixplorer.sourceforge.net).
	Authors: quix@free.fr, ck@realtime-projects.com.
	The Initial Developer of the Original Code is The QuiX project.

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
/* NAS4FREE CODE */
require '/usr/local/www/guiconfig.inc';
require_once 'session.inc';

Session::start();
// Check if session is valid
if (!Session::isLogin()) {
	header('Location: /login.php');
	exit;
}
// Navigation level separator string.
function gentitle(array $title = []) {
	$navlevelsep = htmlspecialchars(' > '); // Navigation level separator string.
	return implode($navlevelsep, $title);
}
function genhtmltitle(array $title = []) {
	return htmlspecialchars(system_get_hostname()) . (empty($title) ? '' : ' - ' . gentitle($title));
}
// Menu items.
// System
$menu['system']['desc'] = gtext('System');
$menu['system']['visible'] = TRUE;
$menu['system']['link'] = '../index.php';
$menu['system']['menuitem'] = [];
$menu['system']['menuitem'][] = ['desc' => gtext('General'),'link' => '../system.php', 'visible' => Session::isAdmin()];
$menu['system']['menuitem'][] = ['desc' => gtext('Advanced'),'link' => '../system_advanced.php', 'visible' => Session::isAdmin()];
$menu['system']['menuitem'][] = ['desc' => gtext('Password'),'link' => '../userportal_system_password.php', 'visible' => !Session::isAdmin()];
$menu['system']['menuitem'][] = ['type' => 'separator','visible' => Session::isAdmin()];
if ('full' === $g['platform']) {
	$menu['system']['menuitem'][] = ['desc' => gtext('Packages'), 'link' => '../system_packages.php','visible' => Session::isAdmin()];
} else {
	$menu['system']['menuitem'][] = ['desc' => gtext('Firmware'), 'link' => '../system_firmware.php','visible' => Session::isAdmin()];
}
$menu['system']['menuitem'][] = ['desc' => gtext('Backup/Restore'),'link' => '../system_backup.php','visible' => Session::isAdmin()];
$menu['system']['menuitem'][] = ['desc' => gtext('Factory Defaults'),'link' => '../system_defaults.php','visible' => Session::isAdmin()];
$menu['system']['menuitem'][] = ['type' => 'separator','visible' => Session::isAdmin()];
$menu['system']['menuitem'][] = ['desc' => gtext('Reboot'),'link' => '../reboot.php','visible' => Session::isAdmin()];
$menu['system']['menuitem'][] = ['desc' => gtext('Shutdown'),'link' => '../shutdown.php','visible' => Session::isAdmin()];
$menu['system']['menuitem'][] = ['type' => 'separator','visible' => TRUE];
$menu['system']['menuitem'][] = ['desc' => gtext('Logout'),'link' => '../logout.php','visible' => TRUE];

// Network
$menu['network']['desc'] = gtext('Network');
$menu['network']['visible'] = Session::isAdmin();
$menu['network']['link'] = '../index.php';
$menu['network']['menuitem'] = [];
$menu['network']['menuitem'][] = ['desc' => gtext('Interface Management'),'link' => '../interfaces_assign.php','visible' => TRUE];
$menu['network']['menuitem'][] = ['desc' => gtext('LAN Management'),'link' => '../interfaces_lan.php','visible' => TRUE];
for($i = 1;isset($config['interfaces']['opt' . $i]);$i++):
	$desc = $config['interfaces']['opt' . $i]['descr'];
	$menu['network']['menuitem'][] = ['desc' => $desc,'link' => sprintf('../interfaces_opt.php?index=%d',$i),'visible' => true];
endfor;
$menu['network']['menuitem'][] = ['type' => 'separator','visible' => TRUE];
$menu['network']['menuitem'][] = ['desc' => gtext('Hosts'),'link' => '../system_hosts.php','visible' => TRUE];
$menu['network']['menuitem'][] = ['desc' => gtext('Static Routes'),'link' => '../system_routes.php', 'visible' => TRUE];
$menu['network']['menuitem'][] = ['desc' => gtext('Firewall'),'link' => '../system_firewall.php', 'visible' => TRUE];

// Disks
$menu['disks']['desc'] = gtext('Disks');
$menu['disks']['visible'] = Session::isAdmin();
$menu['disks']['link'] = '../index.php';
$menu['disks']['menuitem'] = [];
$menu['disks']['menuitem'][] = ['desc' => gtext('Management'),'link' => '../disks_manage.php', 'visible' => TRUE];
$menu['disks']['menuitem'][] = ['desc' => gtext('Software RAID'),'link' => '../disks_raid_geom.php', 'visible' => TRUE];
$menu['disks']['menuitem'][] = ['desc' => gtext('ZFS'), 'link' => '../disks_zfs_zpool.php', 'visible' => TRUE];
$menu['disks']['menuitem'][] = ['type' => 'separator', 'visible' => TRUE];
$menu['disks']['menuitem'][] = ['desc' => gtext('Encryption'),'link' => '../disks_crypt.php', 'visible' => TRUE];
$menu['disks']['menuitem'][] = ['desc' => gtext('Mount Point'),'link' => '../disks_mount.php', 'visible' => TRUE];

// Services
$menu['services']['desc'] = gtext('Services');
$menu['services']['visible'] = Session::isAdmin();
$menu['services']['link'] = '../status_services.php';
$menu['services']['menuitem'] = [];
if ('dom0' !== $g['arch']) {
$menu['services']['menuitem'][] = ['desc' => gtext('HAST'),'link' => '../services_hast.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('Samba AD'),'link' => '../services_samba_ad.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['type' => 'separator','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('CIFS/SMB'),'link' => '../services_samba.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('FTP'),'link' => '../services_ftp.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('TFTP'),'link' => '../services_tftp.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('SSH'),'link' => '../services_sshd.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('NFS'),'link' => '../services_nfs.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('AFP'),'link' => '../services_afp.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('Rsync'),'link' => '../services_rsyncd.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('Syncthing'),'link' => '../services_syncthing.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('Unison'),'link' => '../services_unison.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('iSCSI Target'),'link' => '../services_iscsitarget.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('DLNA/UPnP'),'link' => '../services_fuppes.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('iTunes/DAAP'),'link' => '../services_daap.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('Dynamic DNS'),'link' => '../services_dynamicdns.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('SNMP'),'link' => '../services_snmp.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('UPS'),'link' => '../services_ups.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('Webserver'),'link' => '../services_websrv.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('BitTorrent'),'link' => '../services_bittorrent.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('LCDproc'),'link' => '../services_lcdproc.php','visible' => TRUE];
} else {
$menu['services']['menuitem'][] = ['desc' => gtext('SSH'),'link' => '../services_sshd.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('NFS'),'link' => '../services_nfs.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('iSCSI Target'),'link' => '../services_iscsitarget.php','visible' => TRUE];
$menu['services']['menuitem'][] = ['desc' => gtext('UPS'),'link' => '../services_ups.php','visible' => TRUE];
}

// Virtualization
if ('x64' == $g['arch']) {
$menu['vm']['desc'] = gtext('Virtualization');
$menu['vm']['visible'] = Session::isAdmin();
$menu['vm']['link'] = '../index.php';
$menu['vm']['menuitem'] = [];
}
if ('dom0' !== $g['arch']) {
$menu['vm']['menuitem'][] = ['desc' => gtext('VirtualBox'),'link' => '../vm_vbox.php','visible' => Session::isAdmin()];
} else {
$menu['vm']['menuitem'][] = ['desc' => gtext('Virtual Machine'),'link' => '../vm_xen.php','visible' => TRUE];
}

// Access
$menu['access']['desc'] = gtext('Access');
$menu['access']['visible'] = Session::isAdmin();
$menu['access']['link'] = '../index.php';
$menu['access']['menuitem'] = [];
$menu['access']['menuitem'][] = ['desc' => gtext('Users & Groups'),'link' => '../access_users.php','visible' => TRUE];
if ("dom0" !== $g['arch']) {
$menu['access']['menuitem'][] = ['desc' => gtext('Active Directory'),'link' => '../access_ad.php','visible' => TRUE];
$menu['access']['menuitem'][] = ['desc' => gtext('LDAP'),'link' => '../access_ldap.php','visible' => TRUE];
$menu['access']['menuitem'][] = ['desc' => gtext('NIS'),'link' => '../notavailable.php','visible' => false];
}

// Status
$menu['status']['desc'] = gtext('Status');
$menu['status']['visible'] = Session::isAdmin();
$menu['status']['link'] = '../index.php';
$menu['status']['menuitem'] = [];
$menu['status']['menuitem'][] = ['desc' => gtext('System'),'link' => '../index.php','visible' => TRUE];
$menu['status']['menuitem'][] = ['desc' => gtext('Process'),'link' => '../status_process.php','visible' => TRUE];
$menu['status']['menuitem'][] = ['desc' => gtext('Services'),'link' => '../status_services.php','visible' => TRUE];
$menu['status']['menuitem'][] = ['desc' => gtext('Interfaces'),'link' => '../status_interfaces.php','visible' => TRUE];
$menu['status']['menuitem'][] = ['desc' => gtext('Disks'),'link' => '../status_disks.php','visible' => TRUE];
$menu['status']['menuitem'][] = ['desc' => gtext('Monitoring'),'link' => '../status_graph.php','visible' => TRUE];

// Tools
$menu['tools']['desc'] = gtext('Tools');
$menu['tools']['visible'] = TRUE;
$menu['tools']['link'] = '../index.php';
$menu['tools']['menuitem'] = [];
$menu['tools']['menuitem'][] = ['desc' => gtext('File Editor'),'link' => '../system_edit.php','visible' => Session::isAdmin()] ;
if (!isset($config['system']['disablefm'])) {
	$menu['tools']['menuitem'][] = ['desc' => gtext('File Manager'),'link' => '../quixplorer/system_filemanager.php','visible' => TRUE];
}
$menu['tools']['menuitem'][] = ['type' => 'separator','visible' => Session::isAdmin()];
$menu['tools']['menuitem'][] = ['desc' => gtext('Command'),'link' => '../exec.php','visible' => Session::isAdmin()];

// Diagnostics
$menu['diagnostics']['desc'] = gtext('Diagnostics');
$menu['diagnostics']['visible'] = Session::isAdmin();
$menu['diagnostics']['link'] = '../index.php';
$menu['diagnostics']['menuitem'] = [];
$menu['diagnostics']['menuitem'][] = ['desc' => gtext('Log'), 'link' => '../diag_log.php', 'visible' => TRUE];
$menu['diagnostics']['menuitem'][] = ['desc' => gtext('Information'),'link' => '../diag_infos_disks.php','visible' => TRUE];
$menu['diagnostics']['menuitem'][] = ['type' => 'separator','visible' => TRUE];
$menu['diagnostics']['menuitem'][] = ['desc' => gtext('Ping/Traceroute'),'link' => '../diag_ping.php','visible' => TRUE];
$menu['diagnostics']['menuitem'][] = ['desc' => gtext('ARP Tables'),'link' => '../diag_arp.php','visible' => TRUE];
$menu['diagnostics']['menuitem'][] = ['desc' => gtext('Routes'),'link' => '../diag_routes.php','visible' => TRUE];

// Help
$menu['help']['desc'] = gtext('Help');
$menu['help']['visible'] = TRUE;
$menu['help']['link'] = '../index.php';
$menu['help']['menuitem'] = [];
$menu['help']['menuitem'][] = ['type' => 'separator','visible' => TRUE];
$menu['help']['menuitem'][] = ['desc' => gtext('Forum'),'link' => 'https://www.nas4free.org/forums/', 'visible' => TRUE,'target' => '_blank'];
$menu['help']['menuitem'][] = ['desc' => gtext('Information & Manual'),'link' => 'https://www.nas4free.org/wiki/doku.php','visible' => TRUE,'target' => '_blank'];
$menu['help']['menuitem'][] = ['desc' => gtext('IRC Live Support'),'link' => 'https://webchat.freenode.net/?channels=#nas4free','visible' => TRUE,'target' => '_blank'];
$menu['help']['menuitem'][] = ['type' => 'separator','visible' => TRUE];
$menu['help']['menuitem'][] = ['desc' => gtext('Release Notes'),'link' => '../changes.php','visible' => TRUE];
$menu['help']['menuitem'][] = ['desc' => gtext('License & Credits'),'link' => '../license.php','visible' => TRUE];
$menu['help']['menuitem'][] = ['desc' => gtext('Donate'),'link' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=SAW6UG4WBJVGG&lc=US&item_name=NAS4Free&item_number=Donation%20to%20NAS4Free&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted','visible' => TRUE,'target' => '_blank'];
function make_menu_extensions() {
	global $g;
	global $config;
	global $menu;
	
	$isAdminSession = Session::isAdmin();
	$mi = &array_make_branch($menu,'extensions','menuitem');
	$m = &$menu['extensions'];
	$m['desc'] = gtext('Extensions');
	$m['visible'] = false;
	$m['link'] = '../index.php';
	if(!(isset($config['system']) && is_array($config['system']) && isset($config['system']['disableextensionmenu']))): // continue when view extension menu is allowed
		// Begin extension section.
		if($isAdminSession): // only admins
			$dir_path = $g['www_path'] . '/ext/'; // calculate the path to the extension folder
			if(is_dir($dir_path)): // does it exist?
				if(false !== ($dir_handle = @opendir($dir_path))): // folder exists, open it
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
									if(preg_match('~^[a-z]+://~',$link)): // hard link?
										$mi[] = ['desc' => htmlspecialchars($description),'link' => $link,'visible' => true,'target' => '_blank'];
									else:
										$mi[] = ['desc' => htmlspecialchars($description),'link' => '../' . $link,'visible' => true];
									endif;
									$m['visible'] = true; // extension menu found, make menu head visible 
								endif;
							endforeach;
						endif;
						libxml_clear_errors();
						libxml_use_internal_errors($previous_setting);
					endwhile;
					closedir($dir_handle);
				endif;
			endif;
		endif;
		// End extension section.
	endif;
	unset($m);
	unset($mi);
}
function display_menu($menuid) {
	global $menu;

	if($menu[$menuid]['visible']): // render menu when visible
		$link = $menu[$menuid]['link'];
		if($link == ''):
			$link = 'index.php';
		endif;
		echo "<li>\n";
		$hard_link_regex = '~^[a-z]+://~';
		$agent = $_SERVER['HTTP_USER_AGENT']; // Put browser name into local variable for desktop/mobile detection
		if (preg_match('/(iphone|android)/i',$agent)):
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
		echo "</div>\n";
		echo "</li>\n";
	endif;
}
/* QUIXPLORER CODE */
// header for html-page
function show_header($title, $additional_header_content = null) {
	global $g;
	global $config;
    global $site_name;
	
	$pgtitle = [gtext('Tools'), gtext('File Manager')];

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-Type: text/html; charset=".$GLOBALS["charset"]);
/* NAS4FREE & QUIXPLORER CODE*/
	// Html & Page Headers
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".system_get_language_code()."\" lang=\"".system_get_language_code()."\" dir=\"".$GLOBALS["text_dir"]."\">\n";
	echo "<head>\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"".$GLOBALS["charset"]."\">\n";
	echo "<title>", genhtmltitle($pgtitle ?? []), "</title>\n";
	if (isset($pgrefresh) && $pgrefresh):
		echo "<meta http-equiv='refresh' content=\"".$pgrefresh."\"/>\n";
	endif;
	echo "<link href=\"./_style/style.css\" rel=\"stylesheet\"	type=\"text/css\">\n";
	echo "<link href=\"../css/gui.css\" rel=\"stylesheet\" type=\"text/css\">\n";
	echo "<link href=\"../css/navbar.css\" rel=\"stylesheet\" type=\"text/css\">\n";
	echo "<link href=\"../css/tabs.css\" rel=\"stylesheet\" type=\"text/css\">\n";	
	echo "<script type=\"text/javascript\" src=\"../js/jquery.min.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"../js/gui.js\"></script>\n";
	echo '<script type="text/javascript" src="../js/spinner.js"></script>',"\n";
	echo '<script type="text/javascript" src="../js/spin.min.js"></script>',"\n";
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
	echo '</head>',"\n";
	// NAS4Free Header
	echo '<body id="main">',"\n";
	echo '<div id="spinner_main"></div>',"\n";
	echo '<div id="spinner_overlay" style="display: none; background-color: white; position: fixed; left:0; top:0; height:100%; width:100%; opacity: 0.25;"></div>',"\n";
	echo '<header id="g4h">',"\n";
	if(!(isset($config['system']) && is_array($config['system']) && isset($config['system']['shrinkpageheader']))):
		echo '<div id="header">',"\n";
		echo '<div id="headerlogo">',"\n";
		echo '<a title="www.',get_product_url(),'" href="https://www.',get_product_url(),'" target="_blank"><img src="../images/header_logo.png" alt="logo"/></a>',"\n";
		echo '</div>',"\n";
		echo '<div id="headerrlogo">',"\n";
		echo '<div class="hostname">',"\n";
		echo '<span>',system_get_hostname(),'&nbsp;</span>',"\n";
		echo '</div>',"\n";
		echo '</div>',"\n";
		echo '</div>',"\n";
	endif;
	echo "<div id=\"headernavbar\">\n";
	echo "<ul id=\"navbarmenu\">\n";
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
	echo "</ul>\n";
	echo "<div style=\"clear:both\"></div>\n";
	echo "</div>\n";
	echo '<div id="gapheader"></div>', "\n";
	echo "</header>\n";
	echo '<main id="g4m">', "\n";
	echo '<div id="pagecontent">';
	// QuiXplorer Header
	if (!isset($pgtitle_omit) || !$pgtitle_omit) {
		echo '<p class="pgtitle">', gentitle($pgtitle), "</p>\n";
	}
	echo '<table border="0" width="100%" cellspacing="0" cellpadding="5"><tbody><tr>', "\n";
	echo "<td class=\"title\" aligh=\"left\">\n";
	if($GLOBALS["require_login"] && isset($GLOBALS['__SESSION']["s_user"]))
	echo "[".$GLOBALS['__SESSION']["s_user"]."] "; echo $title;
	echo "</td>\n";
	echo '<td class="title_version" align="right">', "\n";
	echo "Powered by QuiXplorer";
	echo "</td>\n";
	echo "</tr></tbody></table>\n";
	echo '<table id="area_data"><tbody><tr><td id="area_data_frame">';
}
?>
