<?php
/*
	disks_manage_iscsi_edit.php

	Part of XigmaNAS (http://www.xigmanas.com).
	Copyright (c) 2018 The XigmaNAS Project <info@xigmanas.com>.
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

if(isset($_GET['uuid'])):
	$uuid = $_GET['uuid'];
endif;
if(isset($_POST['uuid'])):
	$uuid = $_POST['uuid'];
endif;
$pgtitle = [gtext('Disks'),gtext('Management'),gtext('iSCSI Initiator'),isset($uuid) ? gtext('Edit') : gtext('Add')];
$a_iscsiinit = &array_make_branch($config,'iscsiinit','vdisk');
if(empty($a_iscsiinit)):
else:
	array_sort_key($a_iscsiinit,'name');
endif;
if(isset($uuid) && (false !== ($cnid = array_search_ex($uuid,$a_iscsiinit,'uuid')))):
	$pconfig['uuid'] = $a_iscsiinit[$cnid]['uuid'];
	$pconfig['name'] = $a_iscsiinit[$cnid]['name'];
	$pconfig['targetname'] = $a_iscsiinit[$cnid]['targetname'];
	$pconfig['targetaddress'] = $a_iscsiinit[$cnid]['targetaddress'];
	$pconfig['initiatorname'] = $a_iscsiinit[$cnid]['initiatorname'];
else:
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = '';
	$pconfig['targetname'] = '';
	$pconfig['targetaddress'] = '';
	$pconfig['initiatorname'] = 'iqn.2018-02.org.nas4free:nas4free';
endif;
if(isset($config['iscsitarget']['nodebase']) && !empty($config['iscsitarget']['nodebase'])):
	$ex_nodebase = $config['iscsitarget']['nodebase'];
	$ex_disk = 'disk0';
else:
	$ex_nodebase = 'iqn.2007-09.jp.ne.peach.istgt';
	$ex_disk = 'disk0';
endif;
$ex_iscsitarget = $ex_nodebase . ' : ' . $ex_disk;
if($_POST):
	unset($input_errors);
	unset($errormsg);
	unset($do_crypt);
	$pconfig = $_POST;
	if(isset($_POST['Cancel']) && $_POST['Cancel']):
		header('Location: disks_manage_iscsi.php');
		exit;
	endif;
	// Check for duplicates.
	foreach($a_iscsiinit as $iscsiinit):
		if(isset($uuid) && (false !== $cnid) && ($iscsiinit['uuid'] === $uuid)):
			continue;
		endif;
		if(($iscsiinit['targetname'] === $_POST['targetname']) && ($iscsiinit['targetaddress'] === $_POST['targetaddress'])):
			$input_errors[] = gtext('This couple targetname/targetaddress already exists in the disk list.');
			break;
		endif;
		if($iscsiinit['name'] == $_POST['name']):
			$input_errors[] = gtext('This name already exists in the disk list.');
			break;
		endif;
	endforeach;
	// Input validation
	$reqdfields = ['name','targetname','targetaddress','initiatorname'];
	$reqdfieldsn = [gtext('Name'),gtext('Target Name'),gtext('Target Address'),gtext('Initiator Name')];
	$reqdfieldst = ['alias','string','string','string'];
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
	if(empty($input_errors)):
		$iscsiinit = [];
		$iscsiinit['uuid'] = $_POST['uuid'];
		$iscsiinit['name'] = $_POST['name'];
		$iscsiinit['targetname'] = $_POST['targetname'];
		$iscsiinit['targetaddress'] = $_POST['targetaddress'];
		$iscsiinit['initiatorname'] = $_POST['initiatorname'];
		if(isset($uuid) && (false !== $cnid)):
			$a_iscsiinit[$cnid] = $iscsiinit;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		else:
			$a_iscsiinit[] = $iscsiinit;
			$mode = UPDATENOTIFY_MODE_NEW;
		endif;
		updatenotify_set('iscsiinitiator',$mode,$iscsiinit['uuid']);
		write_config();
		header('Location: disks_manage_iscsi.php');
		exit;
	endif;
endif;
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load",function() {
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
});
//]]>
</script>
<?php
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('disks_manage.php',gtext('HDD Management'))->
			ins_tabnav_record('disks_init.php',gtext('HDD Format'))->
			ins_tabnav_record('disks_manage_smart.php',gtext('S.M.A.R.T.'))->
			ins_tabnav_record('disks_manage_iscsi.php',gtext('iSCSI Initiator'),gtext('Reload page'),true);
$document->
	render();
?>
<form action="disks_manage_iscsi_edit.php" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
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
			html_titleline2(gtext('iSCSI Initiator Settings'));
?>
		</thead>
		<tbody>
<?php
		html_inputbox2('name',gtext('Name'),$pconfig['name'],gtext('This is for information only. (not used during iSCSI negotiation).'),true,20);
		html_inputbox2('initiatorname',gtext('Initiator Name'),$pconfig['initiatorname'],gtext('This name is for example: iqn.2005-01.il.ac.huji.cs:somebody.'),true,60);
		html_inputbox2('targetname',gtext('Target Name'),$pconfig['targetname'],sprintf(gtext('This name is for example: %s.'),$ex_iscsitarget),true,60);
		html_inputbox2('targetaddress',gtext('Target Address'),$pconfig['targetaddress'],gtext('Enter the IP address or DNS name of the iSCSI target.'),true,20);
?>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (false !== $cnid)) ? gtext('Save') : gtext('Add')?>" />
		<input name="Cancel" type="submit" class="formbtn" value="<?=gtext('Cancel');?>"/>
		<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
?>
