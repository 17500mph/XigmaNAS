<?php
/*
	services_websrv.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'services.inc';

array_make_branch($config,'websrv','authentication','url');
array_make_branch($config,'websrv','auxparam');

$default_uploaddir = "/var/tmp/ftmp";
$default_runas = "server.username = \"www\"";
$pconfig['enable'] = isset($config['websrv']['enable']);
$pconfig['protocol'] = $config['websrv']['protocol'];
$pconfig['port'] = $config['websrv']['port'];
$pconfig['documentroot'] = $config['websrv']['documentroot'];
$pconfig['uploaddir'] = !empty($config['websrv']['uploaddir']) ? $config['websrv']['uploaddir'] : $default_uploaddir;
$pconfig['runasuser'] = isset($config['websrv']['runasuser']) ? $config['websrv']['runasuser'] : $default_runas;
$pconfig['privatekey'] = base64_decode($config['websrv']['privatekey']);
$pconfig['certificate'] = base64_decode($config['websrv']['certificate']);
$pconfig['authentication'] = isset($config['websrv']['authentication']['enable']);
$pconfig['dirlisting'] = isset($config['websrv']['dirlisting']);
$pconfig['auxparam'] = "";
if (isset($config['websrv']['auxparam']) && is_array($config['websrv']['auxparam']))
	$pconfig['auxparam'] = implode("\n", $config['websrv']['auxparam']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if (isset($_POST['enable']) && $_POST['enable']) {
		$reqdfields = ['port','documentroot'];
		$reqdfieldsn = [gtext('Port'),gtext('Document Root')];
		$reqdfieldst = ['port','string'];

		if ("https" === $_POST['protocol']) {
			$reqdfields = array_merge($reqdfields, ['certificate','privatekey']);
			$reqdfieldsn = array_merge($reqdfieldsn, [gtext('Certificate'),gtext('Private key')]);
			$reqdfieldst = array_merge($reqdfieldst, ['certificate','privatekey']);
		}

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

		// Check if port is already used.
		if (services_is_port_used($_POST['port'], "websrv"))
			$input_errors[] = sprintf(gtext("Port %ld is already used by another service."), $_POST['port']);

		// Check Webserver document root if auth is required
		if (isset($_POST['authentication'])
		    && !is_dir($_POST['documentroot'])) {
			$input_errors[] = gtext("Webserver document root is missing.");
		}
	}

	if (empty($input_errors)) {
		$config['websrv']['enable'] = isset($_POST['enable']) ? true : false;
		$config['websrv']['protocol'] = $_POST['protocol'];
		$config['websrv']['port'] = $_POST['port'];
		$config['websrv']['documentroot'] = $_POST['documentroot'];
		$config['websrv']['uploaddir'] = $_POST['uploaddir'];
		$config['websrv']['runasuser'] = $_POST['runasuser'];
		$config['websrv']['privatekey'] = base64_encode($_POST['privatekey']);
		$config['websrv']['certificate'] = base64_encode($_POST['certificate']);
		$config['websrv']['authentication']['enable'] = isset($_POST['authentication']) ? true : false;
		$config['websrv']['dirlisting'] = isset($_POST['dirlisting']) ? true : false;

		// Write additional parameters.
		unset($config['websrv']['auxparam']);
		foreach (explode("\n", $_POST['auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$config['websrv']['auxparam'][] = $auxparam;
		}

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("websrvauth", "websrvauth_process_updatenotification");
			config_lock();
			$retval |= rc_exec_service("websrv_htpasswd");
			$retval |= rc_update_service("websrv");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);

		if (0 == $retval) {
			updatenotify_delete("websrvauth");
		}
	}
}

if(isset($_GET['act']) && $_GET['act'] === "del") {
	updatenotify_set("websrvauth", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
	header("Location: services_websrv.php");
	exit;
}

function websrvauth_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			$cnid = array_search_ex($data, $config['websrv']['authentication']['url'], "uuid");
			if (FALSE !== $cnid) {
				unset($config['websrv']['authentication']['url'][$cnid]);
				write_config();
			}
			break;
	}

	return $retval;
}
$pgtitle = [gtext('Services'),gtext('Webserver')];
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.protocol.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.documentroot.disabled = endis;
	document.iform.documentrootbrowsebtn.disabled = endis;
	document.iform.uploaddir.disabled = endis;
	document.iform.uploaddirbrowsebtn.disabled = endis;
	document.iform.runasuser.disabled = endis;
	document.iform.privatekey.disabled = endis;
	document.iform.certificate.disabled = endis;
	document.iform.authentication.disabled = endis;
	document.iform.dirlisting.disabled = endis;
	document.iform.auxparam.disabled = endis;
}

function protocol_change() {
	switch(document.iform.protocol.selectedIndex) {
		case 0:
			showElementById('privatekey_tr','hide');
			showElementById('certificate_tr','hide');
			break;

		default:
			showElementById('privatekey_tr','show');
			showElementById('certificate_tr','show');
			break;
	}
}

function authentication_change() {
	switch(document.iform.authentication.checked) {
		case false:
			showElementById('authdirs_tr','hide');
			break;

		case true:
			showElementById('authdirs_tr','show');
			break;
	}
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabcont">
			<form action="services_websrv.php" method="post" name="iform" id="iform" onsubmit="spinner()">
				<?php
				if (!empty($input_errors)) print_input_errors($input_errors);
				if (!empty($savemsg)) print_info_box($savemsg);
				if (updatenotify_exists("websrvauth")) print_config_change_box();
				?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php
					html_titleline_checkbox("enable", gtext("Webserver"), !empty($pconfig['enable']) ? true : false, gtext("Enable"), "enable_change(false)");
					html_combobox("protocol", gtext("Protocol"), $pconfig['protocol'],['http' => 'HTTP','https' => 'HTTPS'], "", true, false, "protocol_change()");
					html_inputbox("port", gtext("Port"), $pconfig['port'], gtext("TCP port to bind the server to."), true, 5);
					$helpinghand = gtext('Select the permission for running this service. (www by default).')
						. '<br><b>'
						. '<font color="red">' . gtext('NOTE') . '</font>: '
						. gtext('Running this service as root is not recommended for security reasons, use at your own risk!')
						. '</b></br>';
					html_combobox("runasuser", gtext("Permission"), $pconfig['runasuser'], ["server.username = \"www\"" => "www", "" => "root"], $helpinghand, true);
					html_textarea("certificate", gtext("Certificate"), $pconfig['certificate'], gtext("Paste a signed certificate in X.509 PEM format here."), true, 76, 7, false, false);
					html_textarea("privatekey", gtext("Private key"), $pconfig['privatekey'], gtext("Paste an private key in PEM format here."), true, 76, 7, false, false);
					html_filechooser("documentroot", gtext("Document Root"), $pconfig['documentroot'], gtext("Document root of the webserver. Home of the web page files."), $g['media_path'], true, 76);
					html_filechooser("uploaddir", gtext("Upload Directory"), $pconfig['uploaddir'], sprintf(gtext("Upload directory of the webserver. The default is %s."), $default_uploaddir), $default_uploaddir, true, 76);
					html_checkbox("authentication", gtext("Authentication"), !empty($pconfig['authentication']) ? true : false, gtext("Enable authentication."), gtext("Give only local users access to the web page."), false, "authentication_change()");
					?>
					<tr id="authdirs_tr">
						<td width="22%" valign="top" class="vncell">&nbsp;</td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="45%" class="listhdrlr"><?=gtext("URL");?></td>
									<td width="45%" class="listhdrr"><?=gtext("Realm");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php foreach ($config['websrv']['authentication']['url'] as $urlv):?>
								<?php $notificationmode = updatenotify_get_mode("websrvauth", $urlv['uuid']);?>
								<tr>
									<td class="listlr"><?=htmlspecialchars($urlv['path']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($urlv['realm']);?>&nbsp;</td>
									<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
									<td valign="middle" nowrap="nowrap" class="list">
										<?php if (isset($config['websrv']['enable'])):?>
										<a href="services_websrv_authurl.php?uuid=<?=$urlv['uuid'];?>"><img src="images/edit.png" title="<?=gtext("Edit URL");?>" border="0" alt="<?=gtext("Edit URL");?>" /></a>&nbsp;
										<a href="services_websrv.php?act=del&amp;uuid=<?=$urlv['uuid'];?>" onclick="return confirm('<?=gtext("Do you really want to delete this URL?");?>')"><img src="images/delete.png" title="<?=gtext("Delete URL");?>" border="0" alt="<?=gtext("Delete URL");?>" /></a>
										<?php endif;?>
									</td>
									<?php else:?>
									<td valign="middle" nowrap="nowrap" class="list">
										<img src="images/delete.png" border="0" alt="" />
									</td>
									<?php endif;?>
								</tr>
								<?php endforeach;?>
								<tr>
									<td class="list" colspan="2"></td>
									<td class="list">
										<a href="services_websrv_authurl.php"><img src="images/add.png" title="<?=gtext("Add URL");?>" border="0" alt="<?=gtext("Add URL");?>" /></a>
									</td>
								</tr>
							</table>
							<span class="vexpl"><?=gtext("Define directories/URL's that require authentication.");?></span>
						</td>
					</tr>
					<?php
					html_checkbox("dirlisting", gtext("Directory listing"), !empty($pconfig['dirlisting']) ? true : false, gtext("Enable directory listing."), gtext("A directory listing is generated when no index-files (index.php, index.html, index.htm or default.htm) are found in a directory."), false);
					$helpinghand = '<a href="'
						. 'https://redmine.lighttpd.net/projects/lighttpd/wiki'
						. '" target="_blank">'
						. gtext('Please check the documentation')
						. '</a>.';
					html_textarea("auxparam", gtext("Additional Parameters"), !empty($pconfig['auxparam']) ? $pconfig['auxparam'] : "", sprintf(gtext("These parameters will be added to %s."), "websrv.conf")  . " " . $helpinghand, false, 85, 7, false, false);
					?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save & Restart");?>" onclick="enable_change(true)" />
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!--
enable_change(false);
protocol_change();
authentication_change();
//-->
</script>
<?php include 'fend.inc';?>
