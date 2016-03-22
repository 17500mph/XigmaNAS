<?php
/*
	system_rcconf.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of freenas (http://www.freenas.org).
	Copyright (c) 2005-2011 by Olivier Cochard <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");

$sphere_scriptname = basename(__FILE__);
$sphere_scriptname_child = 'system_rcconf_edit.php';
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = $sphere_header;
$sphere_notifier = 'rcconf';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_add = gettext('Add option');
$gt_record_mod = gettext('Edit option');
$gt_record_del = gettext('Option is marked for deletion');
$gt_record_loc = gettext('Option is locked');
$gt_record_mup = gettext('Move up');
$gt_record_mdn = gettext('Move down');

// sunrise: verify if setting exists, otherwise run init tasks
if (!(isset($config['system']['rcconf']['param']) && is_array($config['system']['rcconf']['param']))) {
	$config['system']['rcconf']['param'] = [];
}
$sphere_array = &$config['system']['rcconf']['param'];
if (!empty($sphere_array)) {
	$key1 = array_column($sphere_array, 'name');
	$key2 = array_column($sphere_array, 'uuid');
	array_multisort($key1, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $key2, SORT_ASC, SORT_STRING | SORT_FLAG_CASE, $sphere_array);
}

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process($sphere_notifier, 'rcconf_process_updatenotification');
			config_lock();
			$retval |= rc_exec_service($sphere_notifier);
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete($sphere_notifier);
		}
		header($sphere_header);
		exit;
	}
	if (isset($_POST['enable_selected_rows']) && $_POST['enable_selected_rows']) {
		$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
		$updateconfig = false;
		foreach ($checkbox_member_array as $checkbox_member_record) {
			if (false !== ($index = array_search_ex($checkbox_member_record, $sphere_array, 'uuid'))) {
				if (!(isset($sphere_array[$index]['enable']))) {
					$sphere_array[$index]['enable'] = true;
					$updateconfig = true;
					$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_array[$index]['uuid']);
					if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_MODIFIED, $sphere_array[$index]['uuid']);
					}
				}
			}
		}
		if ($updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header($sphere_header);
		exit;
	}
	if (isset($_POST['disable_selected_rows']) && $_POST['disable_selected_rows']) {
		$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
		$updateconfig = false;
		foreach ($checkbox_member_array as $checkbox_member_record) {
			if (false !== ($index = array_search_ex($checkbox_member_record, $sphere_array, 'uuid'))) {
				if (isset($sphere_array[$index]['enable'])) {
					unset($sphere_array[$index]['enable']);
					$updateconfig = true;
					$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_array[$index]['uuid']);
					if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_MODIFIED, $sphere_array[$index]['uuid']);
					}
				}
			}
		}
		if ($updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header($sphere_header);
		exit;
	}
	if (isset($_POST['toggle_selected_rows']) && $_POST['toggle_selected_rows']) {
		$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
		$updateconfig = false;
		foreach ($checkbox_member_array as $checkbox_member_record) {
			if (false !== ($index = array_search_ex($checkbox_member_record, $sphere_array, 'uuid'))) {
				if (isset($sphere_array[$index]['enable'])) {
					unset($sphere_array[$index]['enable']);
				} else {
					$sphere_array[$index]['enable'] = true;
				}
				$updateconfig = true;
				$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_array[$index]['uuid']);
				if (UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify) {
					updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_MODIFIED, $sphere_array[$index]['uuid']);
				}
			}
		}
		if ($updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header($sphere_header);
		exit;
	}
	if (isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']) {
		$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
		foreach ($checkbox_member_array as $checkbox_member_record) {
			if (false !== ($index = array_search_ex($checkbox_member_record, $sphere_array, 'uuid'))) {
				$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_array[$index]['uuid']);
				switch ($mode_updatenotify) {
					case UPDATENOTIFY_MODE_NEW:  
						updatenotify_clear($sphere_notifier, $sphere_array[$index]['uuid']);
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY_CONFIG, $sphere_array[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						updatenotify_clear($sphere_notifier, $sphere_array[$index]['uuid']);
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY, $sphere_array[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_UNKNOWN:
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY, $sphere_array[$index]['uuid']);
						break;
				}
			}
		}
		header($sphere_header);
		exit;
	}
}

function rcconf_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['system']['rcconf']['param'])) {
				$index = array_search_ex($data, $config['system']['rcconf']['param'], 'uuid');
				if (false !== $index) {
					mwexec2("/usr/local/sbin/rconf attribute remove {$config['system']['rcconf']['param'][$index]['name']}");
					unset($config['system']['rcconf']['param'][$index]);
					write_config();
				}
			}
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if (is_array($config['system']['rcconf']['param'])) {
				$index = array_search_ex($data, $config['system']['rcconf']['param'], 'uuid');
				if (false !== $index) {
					unset($config['system']['rcconf']['param'][$index]);
					write_config();
				}
			}
			break;
	}
	return $retval;
}

$enabletogglemode = isset($config['system']['enabletogglemode']);
$pgtitle = array(gettext('System'), gettext('Advanced'), gettext('rc.conf'));
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!-- Begin JavaScript
function disableactionbuttons(ab_disable) {
	var ab_element;
	ab_element = document.getElementById('toggle_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
	ab_element = document.getElementById('enable_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
	ab_element = document.getElementById('disable_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
	ab_element = document.getElementById('delete_selected_rows'); if ((ab_element != null) && (ab_element.disabled != ab_disable)) { ab_element.disabled = ab_disable; }
}
function togglecheckboxesbyname(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type == 'checkbox') {
			if (!a_trigger[i].disabled) {
				a_trigger[i].checked = !a_trigger[i].checked;
				if (a_trigger[i].checked) {
					ab_disable = false;
				}
			}
		}
	}
	if (ego.type == 'checkbox') { ego.checked = false; }
	disableactionbuttons(ab_disable);
}
function controlactionbuttons(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type == 'checkbox') {
			if (a_trigger[i].checked) {
				ab_disable = false;
				break;
			}
		}
	}
	disableactionbuttons(ab_disable);
}
// End JavaScript -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="system_advanced.php"><span><?=gettext('Advanced');?></span></a></li>
				<li class="tabinact"><a href="system_email.php"><span><?=gettext('Email');?></span></a></li>
				<li class="tabinact"><a href="system_swap.php"><span><?=gettext('Swap');?></span></a></li>
				<li class="tabinact"><a href="system_rc.php"><span><?=gettext('Command Scripts');?></span></a></li>
				<li class="tabinact"><a href="system_cron.php"><span><?=gettext('Cron');?></span></a></li>
				<li class="tabinact"><a href="system_loaderconf.php"><span><?=gettext('loader.conf');?></span></a></li>
				<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gettext('Reload page');?>"><span><?=gettext('rc.conf');?></span></a></li>
				<li class="tabinact"><a href="system_sysctl.php"><span><?=gettext('sysctl.conf');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="<?=$sphere_scriptname;?>" method="post">
				<?php
					if (!empty($savemsg)) {
						print_info_box($savemsg);
					} else {
						if (file_exists($d_sysrebootreqd_path)) {
							print_info_box(get_std_save_message(0));
						}
					}
				?>
				<?php if (updatenotify_exists($sphere_notifier)) { print_config_change_box(); }?>
				<div id="submit" style="margin-bottom:10px">
					<?php if ($enabletogglemode):?>
						<input name="toggle_selected_rows" type="submit" id="toggle_selected_rows" class="formbtn" value="<?=gettext('Toggle Selected Options');?>" onclick="return confirm('<?= gettext('Do you want to toggle selected options?'); ?>')"/>
					<?php else:?>
						<input name="enable_selected_rows" type="submit" id="enable_selected_rows" class="formbtn" value="<?=gettext('Enable Selected Options');?>" onclick="return confirm('<?=gettext('Do you want to enable selected options?'); ?>')"/>
						<input name="disable_selected_rows" type="submit" id="disable_selected_rows" class="formbtn" value="<?=gettext('Disable Selected Options');?>" onclick="return confirm('<?= gettext('Do you want to disable selected options?'); ?>')"/>
					<?php endif;?>
					<input name="delete_selected_rows" type="submit" id="delete_selected_rows" class="formbtn" value="<?=gettext('Delete Selected Options');?>" onclick="return confirm('<?= gettext('Do you want to delete selected options?'); ?>')"/>
				</div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<colgroup>
						<col style="width:1%">
						<col style="width:34%">
						<col style="width:20%">
						<col style="width:5%">
						<col style="width:30%">
						<col style="width:10%">
					</colgroup>
					<thead>
						<tr>
							<td class="listhdrlr"><input type="checkbox" name="togglemembers" onclick="javascript:togglecheckboxesbyname(this,'<?=$checkbox_member_name;?>[]')" title="<?=gettext('Invert Selection');?>"/></td>
							<td class="listhdrr"><?=gettext('Variable');?></td>
							<td class="listhdrr"><?=gettext('Value');?></td>
							<td class="listhdrr"><?=gettext('Status');?></td>
							<td class="listhdrr"><?=gettext('Comment');?></td>
							<td class="list"></td>
						</tr>
					</thead>
					<tfoot>
					<tr>
						<td class="list" colspan="5"></td>
						<td class="list"><a href="<?=$sphere_scriptname_child;?>"><img src="images/add.png" title="<?=$gt_record_add;?>" border="0" alt="<?=$gt_record_add;?>" /></a></td>
					</tr>
					</tfoot>
					<tbody>
						<?php foreach ($sphere_array as $sphere_record):?>
							<tr>
								<?php $notificationmode = updatenotify_get_mode($sphere_notifier, $sphere_record['uuid']);?>
								<?php $notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);?>
								<?php $enabled = isset($sphere_record['enable']);?>
								<?php $notprotected = !isset($sphere_record['protected']);?>
								<td class="<?=$enabled ? "listlr" : "listlrd";?>">
									<?php if ($notdirty && $notprotected):?>
										<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" onclick="javascript:controlactionbuttons(this,'<?=$checkbox_member_name;?>[]')"/>
									<?php else:?>
										<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" disabled="disabled"/>
									<?php endif;?>
								</td>
								<td class="<?=$enabled ? "listr" : "listrd";?>"><?=htmlspecialchars($sphere_record['name']);?>&nbsp;</td>
								<td class="<?=$enabled ? "listr" : "listrd";?>"><?=htmlspecialchars($sphere_record['value']);?>&nbsp;</td>
								<td class="<?=$enabled ? "listr" : "listrd";?>">
									<?php if ($enabled):?>
										<a title="<?=gettext('Enabled');?>"><img src="images/status_enabled.png" border="0" alt=""/></a>
									<?php else:?>
										<a title="<?=gettext('Disabled');?>"><img src="images/status_disabled.png" border="0" alt=""/></a>
									<?php endif;?>
								</td>
								<td class="listbg"><?= htmlspecialchars($sphere_record['comment']);?>&nbsp;</td>
								<td valign="middle" nowrap="nowrap" class="list">
									<?php if ($notdirty && $notprotected):?>
										<a href="<?=$sphere_scriptname_child;?>?uuid=<?=$sphere_record['uuid'];?>"><img src="images/edit.png" title="<?=$gt_record_mod;?>" border="0" alt="<?=$gt_record_mod;?>" /></a>
									<?php else:?>
										<?php if ($notprotected):?>
											<img src="images/delete.png" title="<?=gettext($gt_record_del);?>" border="0" alt="<?=gettext($gt_record_del);?>" />
										<?php else:?>
											<img src="images/locked.png" title="<?=gettext($gt_record_loc);?>" border="0" alt="<?=gettext($gt_record_loc);?>" />
										<?php endif;?>
									<?php endif;?>
								</td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<div id="remarks">
					<?php html_remark("note", gettext('Note'), gettext('These option(s) will be added to /etc/rc.conf. This allow you to overwrite options used by various generic startup scripts.'));?>
				</div>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!-- Disable action buttons and give their control to checkbox array. -->
window.onload=function() {
	disableactionbuttons(true);
}
</script>
<?php include("fend.inc");?>
