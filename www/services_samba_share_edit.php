<?php
/*
	services_samba_share_edit.php

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
require 'auth.inc';
require 'guiconfig.inc';

if (isset($_GET['uuid']))
	$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

array_make_branch($config,'mounts','mount');
array_make_branch($config,'samba','share');

array_sort_key($config['mounts']['mount'],'devicespecialfile');
array_sort_key($config['samba']['share'],'name');

$a_mount = &$config['mounts']['mount'];
$a_share = &$config['samba']['share'];
$default_shadowformat = "auto-%Y%m%d-%H%M%S";

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_share, "uuid")))) {
	$pconfig['uuid'] = $a_share[$cnid]['uuid'];
	$pconfig['name'] = $a_share[$cnid]['name'];
	$pconfig['path'] = $a_share[$cnid]['path'];
	$pconfig['comment'] = $a_share[$cnid]['comment'];
	$pconfig['readonly'] = isset($a_share[$cnid]['readonly']);
	$pconfig['browseable'] = isset($a_share[$cnid]['browseable']);
	$pconfig['guest'] = isset($a_share[$cnid]['guest']);
	$pconfig['inheritpermissions'] = isset($a_share[$cnid]['inheritpermissions']);
	$pconfig['inheritacls'] = isset($a_share[$cnid]['inheritacls']);
	$pconfig['recyclebin'] = isset($a_share[$cnid]['recyclebin']);
	$pconfig['hidedotfiles'] = isset($a_share[$cnid]['hidedotfiles']);
	$pconfig['shadowcopy'] = isset($a_share[$cnid]['shadowcopy']);
	$pconfig['shadowformat'] = !empty($a_share[$cnid]['shadowformat']) ? $a_share[$cnid]['shadowformat'] : "";
	$pconfig['zfsacl'] = isset($a_share[$cnid]['zfsacl']);
	$pconfig['storealternatedatastreams'] = isset($a_share[$cnid]['storealternatedatastreams']);
	$pconfig['storentfsacls'] = isset($a_share[$cnid]['storentfsacls']);
	$pconfig['afpcompat'] = isset($a_share[$cnid]['afpcompat']);
	$pconfig['hostsallow'] = $a_share[$cnid]['hostsallow'];
	$pconfig['hostsdeny'] = $a_share[$cnid]['hostsdeny'];
	$pconfig['auxparam'] = "";
	if (isset($a_share[$cnid]['auxparam']) && is_array($a_share[$cnid]['auxparam']))
		$pconfig['auxparam'] = implode("\n", $a_share[$cnid]['auxparam']);
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['path'] = "";
	$pconfig['comment'] = "";
	$pconfig['readonly'] = false;
	$pconfig['browseable'] = true;
	$pconfig['guest'] = true;
	$pconfig['inheritpermissions'] = true;
	$pconfig['inheritacls'] = true;
	$pconfig['recyclebin'] = true;
	$pconfig['hidedotfiles'] = true;
	$pconfig['shadowcopy'] = true;
	$pconfig['shadowformat'] = $default_shadowformat;
	$pconfig['zfsacl'] = false;
	$pconfig['storealternatedatastreams'] = false;
	$pconfig['storentfsacls'] = false;
	$pconfig['afpcompat'] = false;
	$pconfig['hostsallow'] = "";
	$pconfig['hostsdeny'] = "";
	$pconfig['auxparam'] = "";
}
if ($pconfig['shadowformat'] == "") {
	$pconfig['shadowformat'] = $default_shadowformat;
}

// get mount info specified path
function get_mount_info($path){
	if (file_exists($path) === FALSE)
		return FALSE;

// get all mount points
	$a_mounts = [];
	mwexec2('/sbin/mount -p', $rawdata);
	foreach($rawdata as $line) {
		list($dev,$dir,$fs,$opt,$dump,$pass) = preg_split('/\s+/', $line);
		$a_mounts[] = [
			'dev' => $dev,
			'dir' => $dir,
			'fs' => $fs,
			'opt' => $opt,
			'dump' => $dump,
			'pass' => $pass,
		];
	}
	if (empty($a_mounts))
		return FALSE;

	// check path with mount list
	do {
		foreach ($a_mounts as $mountv) {
			if (strcmp($mountv['dir'], $path) == 0) {
				// found mount point
				return $mountv;
			}
		}
		// path don't have parent?
		if (strpos($path, '/') === FALSE)
			break;
		// retry with parent
		$pathinfo = pathinfo($path);
		$path = $pathinfo['dirname'];
	} while (1);
	return FALSE;
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: services_samba_share.php");
		exit;
	}

	// Input validation.
	$reqdfields = ['name','comment'];
	$reqdfieldsn = [gtext('Name'),gtext('Comment')];
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);

	$reqdfieldst = ['string','string'];
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	// Check for duplicates.
	$index = array_search_ex($_POST['name'], $a_share, "name");
	if (FALSE !== $index) {
		if (!((FALSE !== $cnid) && ($a_share[$cnid]['uuid'] === $a_share[$index]['uuid']))) {
			$input_errors[] = gtext("The share name is already used.");
		}
	}

	// Enable ZFS ACL on ZFS mount point
	$zfsacl = isset($_POST['zfsacl']) ? true : false;
	$mntinfo = get_mount_info($_POST['path']);
	if ($mntinfo !== FALSE && $mntinfo['fs'] === "zfs") {
		if ($cnid === FALSE) {
			// first creation
			$zfsacl = true;
		}
	}

	if (empty($input_errors)) {
		$share = [];
		$share['uuid'] = $_POST['uuid'];
		$share['name'] = $_POST['name'];
		$share['path'] = $_POST['path'];
		$share['comment'] = $_POST['comment'];
		$share['readonly'] = isset($_POST['readonly']) ? true : false;
		$share['browseable'] = isset($_POST['browseable']) ? true : false;
		$share['guest'] = isset($_POST['guest']) ? true : false;
		$share['inheritpermissions'] = isset($_POST['inheritpermissions']) ? true : false;
		$share['inheritacls'] = isset($_POST['inheritacls']) ? true : false;
		$share['recyclebin'] = isset($_POST['recyclebin']) ? true : false;
		$share['hidedotfiles'] = isset($_POST['hidedotfiles']) ? true : false;
		$share['shadowcopy'] = isset($_POST['shadowcopy']) ? true : false;
		$share['shadowformat'] = $_POST['shadowformat'];
		//$share['zfsacl'] = isset($_POST['zfsacl']) ? true : false;
		$share['zfsacl'] = $zfsacl;
		$share['storealternatedatastreams'] = isset($_POST['storealternatedatastreams']) ? true : false;
		$share['storentfsacls'] = isset($_POST['storentfsacls']) ? true : false;
		$share['afpcompat'] = isset($_POST['afpcompat']) ? true : false;
		$share['hostsallow'] = $_POST['hostsallow'];
		$share['hostsdeny'] = $_POST['hostsdeny'];

		# Write additional parameters.
		unset($share['auxparam']);
		foreach (explode("\n", $_POST['auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$share['auxparam'][] = $auxparam;
		}

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_share[$cnid] = $share;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_share[] = $share;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("smbshare", $mode, $share['uuid']);
		write_config();

		header("Location: services_samba_share.php");
		exit;
	}
}
$pgtitle = [gtext('Services'),gtext('CIFS/SMB'),gtext('Share'),isset($uuid) ? gtext('Edit') : gtext('Add')];
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="services_samba.php"><span><?=gtext("Settings");?></span></a></li>
		<li class="tabact"><a href="services_samba_share.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Shares");?></span></a></li>
	</ul></td></tr>
	<tr>
		<td class="tabcont">
			<form action="services_samba_share_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
				<?php if (!empty($input_errors)) print_input_errors($input_errors); ?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline(gtext("Share Settings"));?>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gtext("Name");?></td>
						<td width="78%" class="vtable">
							<input name="name" type="text" class="formfld" id="name" size="30" value="<?=htmlspecialchars($pconfig['name']);?>" />
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gtext("Comment");?></td>
						<td width="78%" class="vtable">
							<input name="comment" type="text" class="formfld" id="comment" size="30" value="<?=htmlspecialchars($pconfig['comment']);?>" />
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gtext("Path"); ?></td>
						<td width="78%" class="vtable">
							<input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlspecialchars($pconfig['path']);?>" />
							<input name="browse" type="button" class="formbtn" id="Browse" onclick='ifield = form.path; filechooser = window.open("filechooser.php?p="+encodeURIComponent(ifield.value)+"&amp;sd=<?=$g['media_path'];?>", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." /><br />
							<span class="vexpl"><?=gtext("Path to be shared.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Read Only");?></td>
						<td width="78%" class="vtable">
							<input name="readonly" type="checkbox" id="readonly" value="yes" <?php if (isset($pconfig['readonly']) && $pconfig['readonly']) echo "checked=\"checked\""; ?> />
							<?=gtext("Set read only.");?><br />
							<span class="vexpl"><?=gtext("If this parameter is set, then users may not create or modify files in the share.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Browseable");?></td>
						<td width="78%" class="vtable">
							<input name="browseable" type="checkbox" id="browseable" value="yes" <?php if (isset($pconfig['browseable']) && $pconfig['browseable']) echo "checked=\"checked\""; ?> />
							<?=gtext("Set browseable.");?><br />
							<span class="vexpl"><?=gtext("This controls whether this share is seen in the list of available shares in a net view and in the browse list.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Guest");?></td>
						<td width="78%" class="vtable">
							<input name="guest" type="checkbox" id="guest" value="yes" <?php if (isset($pconfig['guest']) && $pconfig['guest']) echo "checked=\"checked\""; ?> />
							<?=gtext("Enable guest access.");?><br />
							<span class="vexpl"><?=gtext("This controls whether this share is accessible by guest account.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Inherit Permissions");?></td>
						<td width="78%" class="vtable">
							<input name="inheritpermissions" type="checkbox" id="inheritpermissions" value="yes" <?php if (isset($pconfig['inheritpermissions']) && $pconfig['inheritpermissions']) echo "checked=\"checked\""; ?> />
							<?=gtext("Enable permission inheritance.");?><br />
							<span class="vexpl"><?=gtext("The permissions on new files and directories are normally governed by create mask and directory mask but the inherit permissions parameter overrides this. This can be particularly useful on systems with many users to allow a single share to be used flexibly by each user.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Recycle Bin");?></td>
						<td width="78%" class="vtable">
							<input name="recyclebin" type="checkbox" id="recyclebin" value="yes" <?php if (isset($pconfig['recyclebin']) && $pconfig['recyclebin']) echo "checked=\"checked\""; ?> />
							<?=gtext("Enable recycle bin.");?><br />
							<span class="vexpl"><?=gtext("This will create a recycle bin on the share.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Hide Dot Files");?></td>
						<td width="78%" class="vtable">
							<input name="hidedotfiles" type="checkbox" id="hidedotfiles" value="yes" <?php if (isset($pconfig['hidedotfiles']) && $pconfig['hidedotfiles']) echo "checked=\"checked\"";?> />
							<span class="vexpl"><?=gtext("This parameter controls whether files starting with a dot appear as hidden files.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Shadow Copy");?></td>
						<td width="78%" class="vtable">
							<input name="shadowcopy" type="checkbox" id="shadowcopy" value="yes" <?php if (isset($pconfig['shadowcopy']) && $pconfig['shadowcopy']) echo "checked=\"checked\""; ?> />
							<?=gtext("Enable shadow copy.");?><br />
							<span class="vexpl"><?=gtext("This will provide shadow copy created by auto snapshot. (ZFS only).");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Shadow Copy Format");?></td>
						<td width="78%" class="vtable">
							<input name="shadowformat" type="text" class="formfld" id="shadowformat" size="60" value="<?=htmlspecialchars($pconfig['shadowformat']);?>" /><br />
							<span class="vexpl"><?=sprintf(gtext("The custom format of the snapshot for shadow copy service can be specified. The default format is %s used for ZFS auto snapshot."), $default_shadowformat);?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("ZFS ACL");?></td>
						<td width="78%" class="vtable">
							<input name="zfsacl" type="checkbox" id="zfsacl" value="yes" <?php if (isset($pconfig['zfsacl']) && $pconfig['zfsacl']) echo "checked=\"checked\""; ?> />
							<?=gtext("Enable ZFS ACL.");?><br />
							<span class="vexpl"><?=gtext("This will provide ZFS ACL support. (ZFS only).");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Inherit ACL");?></td>
						<td width="78%" class="vtable">
							<input name="inheritacls" type="checkbox" id="inheritacls" value="yes" <?php if (isset($pconfig['inheritacls']) && $pconfig['inheritacls']) echo "checked=\"checked\""; ?> />
							<?=gtext("Enable ACL inheritance.");?>
						</td>
					</tr>
					<?php html_checkbox("storealternatedatastreams", gtext("Alternate Data Streams"), !empty($pconfig['storealternatedatastreams']) ? true : false, gtext("Store alternate data streams in Extended Attributes."), "", false);?>
					<?php html_checkbox("storentfsacls", gtext("NTFS ACLs"), !empty($pconfig['storentfsacls']) ? true : false, gtext("Store NTFS ACLs in Extended Attributes."), gtext("This will provide NTFS ACLs without ZFS ACL support such as UFS."), false);?>
					<?php html_checkbox("afpcompat", gtext("AFP Compatibility"), !empty($pconfig['afpcompat']) ? true : false, gtext("Enhanced compatibility with Netatalk AFP server."), "", false);?>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Hosts Allow");?></td>
						<td width="78%" class="vtable">
							<input name="hostsallow" type="text" class="formfld" id="hostsallow" size="60" value="<?=htmlspecialchars($pconfig['hostsallow']);?>" /><br />
							<span class="vexpl"><?=gtext("This option is a comma, space, or tab delimited set of hosts which are permitted to access this share. You can specify the hosts by name or IP number. Leave this field empty to use default settings.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Hosts Deny");?></td>
						<td width="78%" class="vtable">
							<input name="hostsdeny" type="text" class="formfld" id="hostsdeny" size="60" value="<?=htmlspecialchars($pconfig['hostsdeny']);?>" /><br />
							<span class="vexpl"><?=gtext("This option is a comma, space, or tab delimited set of host which are NOT permitted to access this share. Where the lists conflict, the allow list takes precedence. In the event that it is necessary to deny all by default, use the keyword ALL (or the netmask 0.0.0.0/0) and then explicitly specify to the hosts allow parameter those hosts that should be permitted access. Leave this field empty to use default settings.");?></span>
						</td>
					</tr>
					<?php
					$helpinghand =  '<a href="' .
							'http://us1.samba.org/samba/docs/man/manpages-3/smb.conf.5.html' .
							'" target="_blank">' .
							gtext('Please check the documentation') .
							'</a>.';
					html_textarea("auxparam", gtext("Additional Parameters"), !empty($pconfig['auxparam']) ? $pconfig['auxparam'] : "", sprintf(gtext("These parameters are added to [Share] section of %s."), "smb4.conf") . " " . $helpinghand, false, 65, 5, false, false);
					?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gtext("Save") : gtext("Add")?>" />
					<input name="Cancel" type="submit" class="formbtn" value="<?=gtext("Cancel");?>" />
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
