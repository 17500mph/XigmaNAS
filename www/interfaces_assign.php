<?php
/*
	interfaces_assign.php

	Part of XigmaNAS (https://www.xigmanas.com).
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
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS, either expressed or implied.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';

/*
	In this file, "port" refers to the physical port name,
	while "interface" refers to LAN, WAN, or OPTn.
*/

/* get list without VLAN interfaces */
$portlist = get_interface_list();

// Add WLAN interfaces.
array_make_branch($config,'vinterfaces','wlan');
if(count($config['vinterfaces']['wlan'])):
	foreach($config['vinterfaces']['wlan'] as $wlanv):
		$portlist[$wlanv['if']] = $wlanv;
		$portlist[$wlanv['if']]['isvirtual'] = true;
	endforeach;
endif;

// Add VLAN interfaces.
array_make_branch($config,'vinterfaces','vlan');
if(count($config['vinterfaces']['vlan'])):
	foreach($config['vinterfaces']['vlan'] as $vlanv):
		$portlist[$vlanv['if']] = $vlanv;
		$portlist[$vlanv['if']]['isvirtual'] = true;
	endforeach;
endif;

// Add LAGG interfaces.
array_make_branch($config,'vinterfaces','lagg');
if(count($config['vinterfaces']['lagg'])):
	foreach($config['vinterfaces']['lagg'] as $laggv):
		$portlist[$laggv['if']] = $laggv;
		$portlist[$laggv['if']]['isvirtual'] = true;
	endforeach;
endif;

if ($_POST) {
	unset($input_errors);

	/* Build a list of the port names so we can see how the interfaces map */
	$portifmap = [];
	foreach ($portlist as $portname => $portinfo)
		$portifmap[$portname] = [];

	/* Go through the list of ports selected by the user,
	   build a list of port-to-interface mappings in portifmap */
	foreach ($_POST as $ifname => $ifport) {
		if (($ifname == 'lan') || (substr($ifname, 0, 3) == 'opt'))
			$portifmap[$ifport][] = strtoupper($ifname);
	}

	/* Deliver error message for any port with more than one assignment */
	foreach ($portifmap as $portname => $ifnames) {
		if (count($ifnames) > 1) {
			$errstr = gtext("Port ") . $portname .
				gtext(" was assigned to ") . count($ifnames) .
				gtext(" interfaces:");

			foreach ($portifmap[$portname] as $ifn)
				$errstr .= " " . $ifn;

			$input_errors[] = $errstr;
		}
	}

	if(empty($input_errors)):
		/* No errors detected, so update the config */
		foreach ($_POST as $ifname => $ifport):
			if(($ifname == 'lan') || (substr($ifname,0,3) == 'opt')):
				if(!is_array($ifport)):
					$config['interfaces'][$ifname]['if'] = $ifport;
					/* check for wireless interfaces, set or clear ['wireless'] */
					if(preg_match($g['wireless_regex'],$ifport)):
						array_make_branch($config,'interfaces',$ifname,'wireless');
					else:
						unset($config['interfaces'][$ifname]['wireless']);
					endif;
					/* make sure there is a name for OPTn */
					if(substr($ifname, 0, 3) == 'opt'):
						if(!isset($config['interfaces'][$ifname]['descr'])):
							$config['interfaces'][$ifname]['descr'] = strtoupper($ifname);
						endif;
					endif;
				endif;
			endif;
		endforeach;
		write_config();
		touch($d_sysrebootreqd_path);
	endif;
}

if (isset($_GET['act']) && $_GET['act'] == "del") {
	$id = $_GET['id'];

	$ifn = $config['interfaces'][$id]['if'];
	// Stop interface.
	rc_exec_service("netif stop {$ifn}");
	// Remove ifconfig_xxx and ipv6_ifconfig_xxx entries.
	mwexec("/usr/local/sbin/rconf attribute remove 'ifconfig_{$ifn}'");
	mwexec("/usr/local/sbin/rconf attribute remove 'ipv6_ifconfig_{$ifn}'");

	unset($config['interfaces'][$id]);	/* delete the specified OPTn */

	/* shift down other OPTn interfaces to get rid of holes */
	$i = substr($id, 3); /* the number of the OPTn port being deleted */
	$i++;

	/* look at the following OPTn ports */
	while (isset($config['interfaces']['opt' . $i]) && is_array($config['interfaces']['opt' . $i])) {
		$config['interfaces']['opt' . ($i - 1)] =
			$config['interfaces']['opt' . $i];

		if ($config['interfaces']['opt' . ($i - 1)]['descr'] == "OPT" . $i)
			$config['interfaces']['opt' . ($i - 1)]['descr'] = "OPT" . ($i - 1);

		unset($config['interfaces']['opt' . $i]);
		$i++;
	}

	write_config();
	touch($d_sysrebootreqd_path);
	header("Location: interfaces_assign.php");
	exit;
}

if (isset($_GET['act']) && $_GET['act'] == "add") {
	/* find next free optional interface number */
	$i = 1;
	while (isset($config['interfaces']['opt' . $i]) && is_array($config['interfaces']['opt' . $i]))
		$i++;

	$newifname = 'opt' . $i;
	array_make_branch($config,'interfaces',$newifname);
	$config['interfaces'][$newifname] = [];
	$config['interfaces'][$newifname]['descr'] = 'OPT' . $i;

	// Set IPv4 to 'DHCP' and IPv6 to 'Auto' per default.
	$config['interfaces'][$newifname]['ipaddr'] = 'dhcp';
	$config['interfaces'][$newifname]['ipv6addr'] = 'auto';

	/* Find an unused port for this interface */
	foreach($portlist as $portname => $portinfo):
		$portused = false;
		foreach($config['interfaces'] as $ifname => $ifdata):
			if(isset($ifdata['if']) && $ifdata['if'] == $portname):
				$portused = true;
				break;
			endif;
		endforeach;
		if(!$portused):
			$config['interfaces'][$newifname]['if'] = $portname;
			if(preg_match($g['wireless_regex'], $portname)):
				$config['interfaces'][$newifname]['wireless'] = []; // OK, see array_make_branch above
			endif;
			break;
		endif;
	endforeach;

	write_config();
	touch($d_sysrebootreqd_path);
	header("Location: interfaces_assign.php");
	exit;
}
$pgtitle = [gtext('Network'),gtext('Interface Management')];
include 'fbegin.inc';
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
		  <ul id="tabnav">
				<li class="tabact"><a href="interfaces_assign.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Management");?></span></a></li>
				<li class="tabinact"><a href="interfaces_wlan.php"><span><?=gtext("WLAN");?></span></a></li>
				<li class="tabinact"><a href="interfaces_vlan.php"><span><?=gtext("VLAN");?></span></a></li>
				<li class="tabinact"><a href="interfaces_lagg.php"><span><?=gtext("LAGG");?></span></a></li>
				<li class="tabinact"><a href="interfaces_bridge.php"><span><?=gtext("Bridge");?></span></a></li>
				<li class="tabinact"><a href="interfaces_carp.php"><span><?=gtext("CARP");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="interfaces_assign.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<?php
				if(!empty($input_errors)):
					print_input_errors($input_errors);
				endif;
				if(file_exists($d_sysrebootreqd_path)):
					print_info_box(get_std_save_message(0));
				endif;
?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
					html_titleline2(gettext('Overview'), 3);
?>
					<tr>
						<td class="listhdrlr"><?=gtext("Interface");?></td>
						<td class="listhdrr"><?=gtext("Network port");?></td>
						<td class="list">&nbsp;</td>
					</tr>
<?php
					foreach ($config['interfaces'] as $ifname => $iface):
						if (isset($iface['descr']) && $iface['descr']):
							$ifdescr = $iface['descr'];
						else:
							$ifdescr = strtoupper($ifname);
						endif;
?>
						<tr>
							<td class="listlr" valign="middle"><strong><?=$ifdescr;?></strong></td>
							<td valign="middle" class="listr">
								<select name="<?=$ifname;?>" class="formfld" id="<?=$ifname;?>">
<?php
									foreach ($portlist as $portname => $portinfo):
?>
										<option value="<?=$portname;?>" <?php if ($portname == $iface['if']) echo "selected=\"selected\"";?>>
<?php
											if(isset($portinfo['isvirtual']) && $portinfo['isvirtual']):
												$descr = $portinfo['if'];
												if($portinfo['desc']):
													$descr .= " ({$portinfo['desc']})";
												endif;
												echo htmlspecialchars($descr);
											else:
												echo htmlspecialchars($portname . " (" . $portinfo['mac'] . ")");
											endif;
?>
										</option>
<?php
									endforeach;
?>
								</select>
							</td>
							<td valign="middle" class="list">
<?php
								if (($ifname != 'lan') && ($ifname != 'wan')):
?>
									<a href="interfaces_assign.php?act=del&amp;id=<?=$ifname;?>"><img src="images/delete.png" title="<?=gtext("Delete interface");?>" border="0" alt="<?=gtext("Delete interface");?>" /></a>
<?php
								endif;
?>
							</td>
						</tr>
<?php
					endforeach;
					if(count($config['interfaces']) < count($portlist)):
?>
						<tr>
							<td class="list" colspan="2"></td>
							<td class="list" nowrap="nowrap">
								<a href="interfaces_assign.php?act=add"><img src="images/add.png" title="<?=gtext("Add interface");?>" border="0" alt="<?=gtext("Add interface");?>" /></a>
							</td>
						</tr>
<?php
					else:
?>
						<tr>
							<td class="list" colspan="3" height="10"></td>
						</tr>
<?php
					endif;
?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save");?>" />
				</div>
				<div id="remarks">
<?php
					$helpinghand = gtext('After you click "Save" you must reboot the server to make the changes take effect.')
					. ' '
					. gtext('You may also have to do one or more of the following steps before you can access your server again:')
					. '<ul>'
					. '<li><span class="vexpl">' . gtext('Change the IP address of your server') . '</span></li>'
					. '<li><span class="vexpl">' . gtext('Access the webGUI with the new IP address') . '</span></li>'
					. '</ul>';
					html_remark("warning", gtext('Warning'), $helpinghand);
?>
				</div>
<?php
				include 'formend.inc';
?>
			</form>
		</td>
	</tr>
</table>
<?php
include 'fend.inc';
