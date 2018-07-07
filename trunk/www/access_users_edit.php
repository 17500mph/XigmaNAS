<?php
/*
	access_users_edit.php

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

//	Return next available user id.
function get_nextuser_id() {
	global $config;

	//	Get next free user id.
	exec('/usr/sbin/pw nextuser',$output);
	$output = explode(':',$output[0]);
	$id = intval($output[0]);
	//	Check if id is already in use. If the user does not press the 'Apply'
	//	button 'pw' does not recognize that there are already several new users
	//	configured because the user db is not updated until 'Apply' is pressed.
	$a_user = array_make_branch($config,'access','user');
	while(false !== array_search_ex(strval($id),$a_user,'id')):
		$id++;
	endwhile;
	return $id;
}

if (isset($_GET['uuid'])):
	$uuid = $_GET['uuid'];
endif;
if(isset($_POST['uuid'])):
	$uuid = $_POST['uuid'];
endif;

$a_user = &array_make_branch($config,'access','user');
if(empty($a_user)):
else:
	array_sort_key($a_user,'login');
endif;

$a_user_system = system_get_user_list();
$a_group = system_get_group_list();

$cnid = array_search_ex($uuid, $a_user,'uuid');
if(isset($uuid) && (false !== $cnid)):
	$mode_page = PAGE_MODE_EDIT;
	$pconfig['uuid'] = $a_user[$cnid]['uuid'];
	$pconfig['login'] = $a_user[$cnid]['login'];
	$pconfig['fullname'] = $a_user[$cnid]['fullname'];
	$pconfig['password'] = '';
	$pconfig['passwordconf'] = '';
	$pconfig['userid'] = $a_user[$cnid]['id'];
	$pconfig['primarygroup'] = $a_user[$cnid]['primarygroup'];
	$pconfig['group'] = !empty($a_user[$cnid]['group']) ? $a_user[$cnid]['group'] : [];
	$pconfig['shell'] = $a_user[$cnid]['shell'];
	$pconfig['homedir'] = $a_user[$cnid]['homedir'];
	$pconfig['userportal'] = isset($a_user[$cnid]['userportal']);
else:
	$mode_page = PAGE_MODE_ADD;
	$pconfig['uuid'] = uuid();
	$pconfig['login'] = '';
	$pconfig['fullname'] = '';
	$pconfig['password'] = '';
	$pconfig['passwordconf'] = '';
	$pconfig['userid'] = get_nextuser_id();
	$pconfig['primarygroup'] = $a_group['guest'];
	$pconfig['group'] = [];
	$pconfig['shell'] = 'nologin';
	$pconfig['homedir'] = '';
	$pconfig['userportal'] = false;
endif;

if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	if(isset($_POST['Cancel']) && $_POST['Cancel']):
		header('Location: access_users.php');
		exit;
	endif;
	// Input validation
	switch($mode_page):
		case PAGE_MODE_ADD:
			$reqdfields = ['login','fullname','password','primarygroup','userid','shell'];
			$reqdfieldsn = [gtext('Name'),gtext('Full Name'),gtext('Password'),gtext('Primary Group'),gtext('User ID'),gtext('Shell')];
			$reqdfieldst = ['string','string','string','numeric','numeric','string'];
			break;
		case PAGE_MODE_EDIT:
			$reqdfields = ['login','fullname','primarygroup','userid','shell'];
			$reqdfieldsn = [gtext('Name'),gtext('Full Name'),gtext('Primary Group'),gtext('User ID'),gtext('Shell')];
			$reqdfieldst = ['string','string','numeric','numeric','string'];
			break;
	endswitch;
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
	//	Check for valid login name.
	if(($_POST['login'] && !is_validlogin($_POST['login']))):
		$input_errors[] = gtext('The login name contains invalid characters.');
	endif;
	if(($_POST['login'] && strlen($_POST['login']) > 16)):
		$input_errors[] = gtext('The login name is limited to 16 characters.');
	endif;
	if(($_POST['login'] && in_array($_POST['login'],$reservedlogin))):
		$input_errors[] = gtext("The login name is a reserved login name.");
	endif;
	//	Check for valid Full name.
	if(($_POST['fullname'] && !is_validdesc($_POST['fullname']))):
		$input_errors[] = gtext('The full name contains invalid characters.');
	endif;
	//	Check for name conflicts. Only check if user is created.
	if(!(isset($uuid) && (false !== $cnid)) && ((is_array($a_user_system) && array_key_exists($_POST['login'],$a_user_system)) || (false !== array_search_ex($_POST['login'],$a_user,'login')))):
		$input_errors[] = gtext('This user already exists in the user list.');
	endif;
	//	Check for a password mismatch.
	if($_POST['password'] !== $_POST['passwordconf']):
		$input_errors[] = gtext("Passwords don't match.");
	endif;
	//	Check if primary group is also selected in additional group.
	if(isset($_POST['group']) && is_array($_POST['group']) && in_array($_POST['primarygroup'],$_POST['group'])):
		$input_errors[] = gtext('Primary group is also selected in additional group.');
	endif;
	//	Check additional group count. Max=15 (Primary+14) 
	if(isset($_POST['group']) && is_array($_POST['group']) && count($_POST['group']) > 14):
		$input_errors[] = gtext('There are too many additional groups.');
	endif;
	//	Validate if ID is unique. Only check if user is created.
	if(!(isset($uuid) && (false !== $cnid)) && (false !== array_search_ex($_POST['userid'],$a_user,'id'))):
		$input_errors[] = gtext('The unique user ID is already used.');
	endif;
	//	Check Webserver document root if auth is required
	if(isset($config['websrv']['enable']) && isset($config['websrv']['authentication']['enable']) && !is_dir($config['websrv']['documentroot'])):
		$input_errors[] = gtext('Webserver document root is missing.');
	endif;
	if(empty($input_errors)):
		$user = [];
		$user['uuid'] = $_POST['uuid'];
		$user['login'] = $_POST['login'];
		$user['fullname'] = $_POST['fullname'];
		if($_POST['password'] !== ''):
			$user['passwordsha'] = mkpasswd($_POST['password']);
			$user['passwordmd4'] = mkpasswdmd4($_POST['password']);
		elseif(PAGE_MODE_EDIT === $mode_page):
			$user['passwordsha'] = $a_user[$cnid]['passwordsha'] ?? mkpasswd($a_user[$cnid]['password'] ?? '');
			$user['passwordmd4'] = $a_user[$cnid]['passwordmd4'] ?? mkpasswdmd4($a_user[$cnid]['password'] ?? '');
		endif;
		$user['shell'] = $_POST['shell'];
		$user['primarygroup'] = $_POST['primarygroup'];
		if (isset($_POST['group']) && is_array($_POST['group']))
			$user['group'] = $_POST['group'];
		$user['homedir'] = $_POST['homedir'];
		$user['id'] = $_POST['userid'];
		$user['userportal'] = isset($_POST['userportal']) ? true : false;
		if(isset($uuid) && (false !== $cnid)):
			$a_user[$cnid] = $user;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		else:
			$a_user[] = $user;
			$mode = UPDATENOTIFY_MODE_NEW;
		endif;
		updatenotify_set('userdb_user',$mode,$user['uuid']);
		write_config();
		header('Location: access_users.php');
		exit;
	endif;
endif;
$pgtitle = [gtext('Access'),gtext('Users'),isset($uuid) ? gtext('Edit') : gtext('Add')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load",function() {
<?php // Init spinner.?>
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
});
//]]>
</script>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="access_users.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Users');?></span></a></li>
		<li class="tabinact"><a href="access_users_groups.php"><span><?=gtext('Groups');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<form action="access_users_edit.php" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($nogroup_errors)):
		print_input_errors($nogroup_errors);
	endif;
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('User Settings'));
?>
		</thead>
		<tbody>
<?php

			html_inputbox2('login',gtext('Name'),$pconfig['login'],gtext('Enter login name of the user.'),true,28,isset($uuid) && (false !== $cnid),false,0,gtext('Enter login name'));
			html_inputbox2('fullname',gtext('Full Name'),$pconfig['fullname'],gtext('Enter full name of the user.'),true,28,false,false,0,gtext('Enter full user name'));
			html_passwordconfbox2('password','passwordconf',gtext('Password'),'','',gtext('Set or reset user password.'),($mode_page === PAGE_MODE_ADD));
			html_inputbox2('userid',gtext('User ID'),$pconfig['userid'],gtext('User numeric id.'),true,12,isset($uuid) && (false !== $cnid));
			$l_shell = [
				'nologin' => 'nologin',
				'scponly' => 'scponly',
				'sh' => 'sh',
				'bash' => 'bash',
				'csh' => 'csh',
				'tcsh' => 'tcsh'
			];
			html_radiobox2('shell',gtext('Shell'),$pconfig['shell'],$l_shell,gtext('Set user login shell.'),true);
			$l_grouplist = [];
			foreach($a_group as $groupk => $groupv):
				$l_grouplist[$groupv] = $groupk;
			endforeach;
			html_combobox2('primarygroup',gtext('Primary Group'),$pconfig['primarygroup'],$l_grouplist,gtext("Set the account's primary group to the given group."),true);
			html_listbox2('group',gtext('Additional Group'),!empty($pconfig['group']) ? $pconfig['group'] : [],$l_grouplist,gtext('Set additional group memberships for this account.')."<br />".gtext('Note: Ctrl-click (or command-click on the Mac) to select and deselect groups.'));
			html_filechooser2('homedir',gtext('Home Directory'),$pconfig['homedir'],gtext('Enter the path to the home directory of that user. Leave this field empty to use default path /mnt.'),$g['media_path'],false,60);
			html_checkbox2('userportal',gtext('User Portal'),!empty($pconfig['userportal']) ? true : false,gtext('Grant access to the user portal.'),'',false);
?>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (false !== $cnid)) ? gtext('Save') : gtext('Add')?>"/>
		<input name="Cancel" type="submit" class="formbtn" value="<?=gtext('Cancel');?>"/>
		<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>"/>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
?>
