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
// Menu items.
$isAdminSession = Session::isAdmin();
$menu = [];
// System
$mi = &array_make_branch($menu,'system','menuitem');
$menu['system']['desc'] = gtext('System');
$menu['system']['visible'] = TRUE;
$menu['system']['link'] = '';
$mi[] = ['desc' => gtext('General'),'link' => 'system.php','visible' => $isAdminSession];
$mi[] = ['desc' => gtext('Advanced'),'link' => 'system_advanced.php','visible' => $isAdminSession];
$mi[] = ['desc' => gtext('Password'),'link' => 'userportal_system_password.php','visible' => !$isAdminSession];
$mi[] = ['type' => 'separator','visible' =>  $isAdminSession];
if('full' == $g['platform']):
	$mi[] = ['desc' => gtext('Packages'),'link' => 'system_packages.php','visible' => $isAdminSession];
else:
	$mi[] = ['desc' => gtext('Firmware Update'),'link' => 'system_firmware.php','visible' => $isAdminSession];
endif;
$mi[] = ['desc' => gtext('Backup/Restore'),'link' => 'system_backup.php','visible' => $isAdminSession];
$mi[] = ['desc' => gtext('Factory Defaults'),'link' => 'system_defaults.php','visible' => $isAdminSession];
$mi[] = ['type' => 'separator','visible' => $isAdminSession];
$mi[] = ['desc' => gtext('Reboot'),'link' => 'reboot.php','visible' => $isAdminSession];
$mi[] = ['desc' => gtext('Shutdown'),'link' => 'shutdown.php','visible' => $isAdminSession];
$mi[] = ['type' => 'separator','visible' => true];
$mi[] = ['desc' => gtext('Logout'),'link' => 'logout.php','visible' => true];
unset($mi);
// Network
$mi = &array_make_branch($menu,'network','menuitem');
$menu['network']['desc'] = gtext('Network');
$menu['network']['visible'] = $isAdminSession;
$menu['network']['link'] = '';
$mi[] = ['desc' => gtext('Interface Management'),'link' => 'interfaces_assign.php','visible' => true];
$mi[] = ['desc' => gtext('LAN Management'),'link' => 'interfaces_lan.php','visible' => true];
for($i = 1;isset($config['interfaces']['opt' . $i]);$i++):
	$desc = $config['interfaces']['opt'.$i]['descr'];
	$mi[] = ['desc' => "{$desc}",'link' => "interfaces_opt.php?index={$i}",'visible' => true];
endfor;
$mi[] = ['type' => 'separator','visible' => true];
$mi[] = ['desc' => gtext('Hosts'),'link' => 'system_hosts.php','visible' => true];
$mi[] = ['desc' => gtext('Static Routes'),'link' => 'system_routes.php','visible' => true];
$mi[] = ['type' => 'separator','visible' => true];
$mi[] = ['desc' => gtext('Proxy'),'link' => 'system_proxy.php','visible' => true];
$mi[] = ['desc' => gtext('Firewall'),'link' => 'system_firewall.php','visible' => true];
unset($mi);
// Disks
$mi = &array_make_branch($menu,'disks','menuitem');
$menu['disks']['desc'] = gtext('Disks');
$menu['disks']['visible'] = $isAdminSession;
$menu['disks']['link'] = '';
$mi[] = ['desc' => gtext('Management'),'link' => 'disks_manage.php','visible' => true];
$mi[] = ['desc' => gtext('Software RAID'),'link' => 'disks_raid_geom.php','visible' => true];
$mi[] = ['desc' => gtext('ZFS'),'link' => 'disks_zfs_zpool.php','visible' => true];
$mi[] = ['type' => 'separator','visible' => true];
$mi[] = ['desc' => gtext('Encryption'),'link' => 'disks_crypt.php','visible' => true];
$mi[] = ['desc' => gtext('Mount Point'),'link' => 'disks_mount.php','visible' => true];
unset($mi);
// Services
$mi = &array_make_branch($menu,'services','menuitem');
$menu['services']['desc'] = gtext('Services');
$menu['services']['visible'] = $isAdminSession;
$menu['services']['link'] = 'status_services.php';
if('dom0' !== $g['arch']):
	$mi[] = ['desc' => gtext('HAST'),'link' => 'services_hast.php','visible' => true];
	$mi[] = ['desc' => gtext('Samba AD'),'link' => 'services_samba_ad.php','visible' => true];
	$mi[] = ['type' => 'separator','visible' => true];
	$mi[] = ['desc' => gtext('CIFS/SMB'),'link' => 'services_samba.php','visible' => true];
	$mi[] = ['desc' => gtext('FTP'),'link' => 'services_ftp.php','visible' => true];
	$mi[] = ['desc' => gtext('TFTP'),'link' => 'services_tftp.php','visible' => true];
	$mi[] = ['desc' => gtext('SSH'),'link' => 'services_sshd.php','visible' => true];
	$mi[] = ['desc' => gtext('NFS'),'link' => 'services_nfs.php','visible' => true];
	$mi[] = ['desc' => gtext('AFP'),'link' => 'services_afp.php','visible' => true];
	$mi[] = ['desc' => gtext('Rsync'),'link' => 'services_rsyncd.php','visible' => true];
	$mi[] = ['desc' => gtext('Syncthing'),'link' => 'services_syncthing.php','visible' => true];
	$mi[] = ['desc' => gtext('Unison'),'link' => 'services_unison.php','visible' => true];
	$mi[] = ['desc' => gtext('iSCSI Target'),'link' => 'services_iscsitarget.php','visible' => true];
	$mi[] = ['desc' => gtext('DLNA/UPnP'),'link' => 'services_fuppes.php','visible' => true];
	$mi[] = ['desc' => gtext('iTunes/DAAP'),'link' => 'services_daap.php','visible' => true];
	$mi[] = ['desc' => gtext('Dynamic DNS'),'link' => 'services_dynamicdns.php','visible' => true];
	$mi[] = ['desc' => gtext('SNMP'),'link' => 'services_snmp.php','visible' => true];
	$mi[] = ['desc' => gtext('UPS'),'link' => 'services_ups.php','visible' => true];
	$mi[] = ['desc' => gtext('Webserver'),'link' => 'services_websrv.php','visible' => true];
	$mi[] = ['desc' => gtext('BitTorrent'),'link' => 'services_bittorrent.php','visible' => true];
	$mi[] = ['desc' => gtext('LCDproc'),'link' => 'services_lcdproc.php','visible' => true];
else:
	$mi[] = ['desc' => gtext('SSH'),'link' => 'services_sshd.php','visible' => true];
	$mi[] = ['desc' => gtext('NFS'),'link' => 'services_nfs.php','visible' => true];
	$mi[] = ['desc' => gtext('iSCSI Target'),'link' => 'services_iscsitarget.php','visible' => true];
	$mi[] = ['desc' => gtext('UPS'),'link' => 'services_ups.php','visible' => true];
endif;
unset($mi);
// Virtualization
$mi = &array_make_branch($menu,'vm','menuitem');
if('x64' == $g['arch']):
	$menu['vm']['desc'] = gtext('Virtualization');
	$menu['vm']['visible'] = $isAdminSession;
	$menu['vm']['link'] = '';
endif;
if('dom0' !== $g['arch']):
	$mi[] = ['desc' => gtext('VirtualBox'),'link' => 'vm_vbox.php','visible' => $isAdminSession];
else:
	$mi[] = ['desc' => gtext('Virtual Machine'),'link' => 'vm_xen.php','visible' => true];
endif;
unset($mi);
// Access
$mi = &array_make_branch($menu,'access','menuitem');
$menu['access']['desc'] = gtext('Access');
$menu['access']['visible'] = $isAdminSession;
$menu['access']['link'] = '';
$mi[] = ['desc' => gtext('Users & Groups'),'link' => 'access_users.php','visible' => true];
if('dom0' !== $g['arch']):
	$mi[] = ['desc' => gtext('Active Directory'),'link' => 'access_ad.php','visible' => true];
	$mi[] = ['desc' => gtext('LDAP'),'link' => 'access_ldap.php','visible' => true];
	$mi[] = ['desc' => gtext('NIS'),'link' => 'notavailable.php','visible' => false];
endif;
unset($mi);
// Status
$mi = &array_make_branch($menu,'status','menuitem');
$menu['status']['desc'] = gtext('Status');
$menu['status']['visible'] = $isAdminSession;
$menu['status']['link'] = 'index.php';
$mi[] = ['desc' => gtext('System'),'link' => 'index.php','visible' => true];
$mi[] = ['desc' => gtext('Processes'),'link' => 'status_process.php','visible' => true];
$mi[] = ['desc' => gtext('Services'),'link' => 'status_services.php','visible' => true];
$mi[] = ['desc' => gtext('Interfaces'),'link' => 'status_interfaces.php','visible' => true];
$mi[] = ['desc' => gtext('Disks'),'link' => 'status_disks.php','visible' => true];
$mi[] = ['desc' => gtext('Monitoring'),'link' => 'status_graph.php','visible' => true];
unset($mi);
// Tools
$mi = &array_make_branch($menu,'tools','menuitem');
$menu['tools']['desc'] = gtext('Tools');
$menu['tools']['visible'] = true;
$menu['tools']['link'] = '';
$mi[] = ['desc' => gtext('File Editor'),'link' => 'system_edit.php','visible' => $isAdminSession];
if(!isset($config['system']['disablefm'])):
	$mi[] = ['desc' => gtext('File Manager'),'link' => '/quixplorer/system_filemanager.php','visible' => true];
endif;
$mi[] = ['type' => 'separator','visible' => $isAdminSession];
$mi[] = ['desc' => gtext('Command'),'link' => 'exec.php','visible' => $isAdminSession];
unset($mi);
// Diagnostics
$mi = &array_make_branch($menu,'diagnostics','menuitem');
$menu['diagnostics']['desc'] = gtext('Diagnostics');
$menu['diagnostics']['visible'] = $isAdminSession;
$menu['diagnostics']['link'] = '';
$mi[] = ['desc' => gtext('Log'),'link' => 'diag_log.php','visible' => true];
$mi[] = ['desc' => gtext('Information'),'link' => 'diag_infos_disks.php','visible' => true];
$mi[] = ['type' => 'separator','visible' => true];
$mi[] = ['desc' => gtext('Ping/Traceroute'),'link' => 'diag_ping.php','visible' => true];
$mi[] = ['desc' => gtext('ARP Tables'),'link' => 'diag_arp.php','visible' => true];
$mi[] = ['desc' => gtext('Routes'),'link' => 'diag_routes.php','visible' => true];
unset($mi);
// Help
$mi = &array_make_branch($menu,'help','menuitem');
$menu['help']['desc'] = gtext('Help');
$menu['help']['visible'] = true;
$menu['help']['link'] = '';
$mi[] = ['desc' => gtext('Forum'),'link' => 'https://www.nas4free.org/forums/','visible' => true,'target' => '_blank'];
$mi[] = ['desc' => gtext('Information & Manual'),'link' => 'https://www.nas4free.org/wiki/doku.php','visible' => true,'target' => '_blank'];
$mi[] = ['desc' => gtext('IRC Live Support'),'link' => 'https://webchat.freenode.net/?channels=#nas4free','visible' => true,'target' => '_blank'];
$mi[] = ['type' => 'separator','visible' => true];
$mi[] = ['desc' => gtext('Release Notes'),'link' => 'changes.php','visible' => true];
$mi[] = ['desc' => gtext('License & Credits'),'link' => 'license.php','visible' => true];
$mi[] = ['desc' => gtext('Donate'),'link' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=info%40nas4free%2eorg&lc=US&item_name=NAS4Free%20Project&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest','visible' => TRUE,'target' => '_blank'];
unset($mi);
function display_menu($menuid) {
	global $menu;
	
	if($menu[$menuid]['visible']): // render menu when visible
		$link = $menu[$menuid]['link'];
		if($link == ''):
			$link = 'index.php';
		endif;
		echo "<li>\n";
		$agent = $_SERVER['HTTP_USER_AGENT']; // Put browser name into local variable for desktop/mobile detection
		if(preg_match('/(iphone|android)/i',$agent)):
			echo '<a href="javascript:mopen(\'',$menuid,'\');" onmouseout="mclosetime()">',$menu[$menuid]['desc'],"</a>\n";
		elseif(preg_match('~^http[s]?://~',$link)): // hard link = no spinner
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
					if(preg_match('~^http[s]?://~',$link)): // hard link = no spinner
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
?>
<?php header('Content-Type: text/html; charset=' . system_get_language_codeset());?>
<?php
  // XML declarations
/*
  some browser might be broken.
  echo '<?xml version="1.0" encoding="'.system_get_language_codeset().'"?>';
  echo "\n";
*/
?>
<?php
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
	if(!(isset($config['system']) && is_array($config['system']) && isset($config['system']['shrinkpageheader']))):
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
			// Begin extension section.
			if ($isAdminSession && is_dir("{$g['www_path']}/ext")):
?>
				<li>
<?php
					$agent = $_SERVER['HTTP_USER_AGENT']; // Put browser name into local variable for desktop/mobile detection
					if ((preg_match('/iPhone/i',$agent)) || (preg_match('/android/i',$agent))) {
						echo "<a href=\"javascript:mopen('extensions');\" onmouseout=\"mclosetime()\">".gtext('Extensions')."</a>";
					} else {
						echo "<a href=\"index.php\" onclick=\"spinner()\" onmouseover=\"mopen('extensions')\" onmouseout=\"mclosetime()\">".gtext('Extensions')."</a>";
					}
?>
					<div id="extensions" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
<?php
					$dh = @opendir("{$g['www_path']}/ext");
					if ($dh) {
						while (($extd = readdir($dh)) !== false) {
							if (($extd === '.') || ($extd === '..'))
								continue;
							@include("{$g['www_path']}/ext/" . $extd . "/menu.inc");
						}
						closedir($dh);
					}
?>
					</div>
				</li>
<?php
			endif;
			// End extension section.
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
<?php if (!isset($pgtitle_omit) || !$pgtitle_omit):?>
	<p class="pgtitle"><?=gentitle($pgtitle);?></p>
<?php endif;?>
