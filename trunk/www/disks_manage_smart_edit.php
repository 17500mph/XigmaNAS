<?php
/*
	disks_manage_smart_edit.php

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

$sphere_scriptname = basename(__FILE__);
if(isset($_GET['uuid'])):
	$uuid = $_GET['uuid'];
endif;
if(isset($_POST['uuid'])):
	$uuid = $_POST['uuid'];
endif;
$a_selftest = &array_make_branch($config,'smartd','selftest');
// Get list of all configured physical disks.
$a_disk = get_conf_physical_disks_list();
if(isset($uuid) && (false !== ($cnid = array_search_ex($uuid,$a_selftest,'uuid')))):
	$pconfig['uuid'] = $a_selftest[$cnid]['uuid'];
	$pconfig['devicespecialfile'] = $a_selftest[$cnid]['devicespecialfile'];
	$pconfig['type'] = $a_selftest[$cnid]['type'];
	$pconfig['hour'] = $a_selftest[$cnid]['hour'];
	$pconfig['day'] = $a_selftest[$cnid]['day'];
	$pconfig['month'] = $a_selftest[$cnid]['month'];
	$pconfig['weekday'] = $a_selftest[$cnid]['weekday'];
	$pconfig['all_hours'] = $a_selftest[$cnid]['all_hours'];
	$pconfig['all_days'] = $a_selftest[$cnid]['all_days'];
	$pconfig['all_months'] = $a_selftest[$cnid]['all_months'];
	$pconfig['all_weekdays'] = $a_selftest[$cnid]['all_weekdays'];
	$pconfig['desc'] = $a_selftest[$cnid]['desc'];
else:
	$pconfig['uuid'] = uuid();
	$pconfig['type'] = 'S';
	$pconfig['desc'] = '';
	$pconfig['all_hours'] = 1;
	$pconfig['all_days'] = 1;
	$pconfig['all_months'] = 1;
	$pconfig['all_weekdays'] = 1;
endif;
if($_POST):
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;
	if(isset($_POST['Cancel']) && $_POST['Cancel']):
		header("Location: disks_manage_smart.php");
		exit;
	endif;
	$reqdfields = ['disk','type'];
	$reqdfieldsn = [gtext('Disk'),gtext('Type')];
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	$_POST['all_mins'] = true; // cheat on minutes
	do_input_validate_synctime($_POST,$input_errors);
	$_POST['all_mins'] = false;
	if(empty($input_errors)):
		$selftest = [];
		$selftest['uuid'] = $_POST['uuid'];
		$selftest['devicespecialfile'] = $_POST['disk'];
		$selftest['type'] = $_POST['type'];
		$selftest['hour'] = !empty($_POST['hour']) ? $_POST['hour'] : null;
		$selftest['day'] = !empty($_POST['day']) ? $_POST['day'] : null;
		$selftest['month'] = !empty($_POST['month']) ? $_POST['month'] : null;
		$selftest['weekday'] = !empty($_POST['weekday']) ? $_POST['weekday'] : null;
		$selftest['all_hours'] = $_POST['all_hours'];
		$selftest['all_days'] = $_POST['all_days'];
		$selftest['all_months'] = $_POST['all_months'];
		$selftest['all_weekdays'] = $_POST['all_weekdays'];
		$selftest['desc'] = $_POST['desc'];
		if(isset($uuid) && (false !== $cnid)):
			$a_selftest[$cnid] = $selftest;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		else:
			$a_selftest[] = $selftest;
			$mode = UPDATENOTIFY_MODE_NEW;
		endif;
		updatenotify_set('smartssd',$mode,$selftest['uuid']);
		write_config();
		header('Location: disks_manage_smart.php');
		exit;
	endif;
endif;
$pgtitle = [gtext('Disks'),gtext('Management'),gtext('S.M.A.R.T.'),gtext('Scheduled Self-Test'),isset($uuid) ? gtext('Edit') : gtext('Add')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init spinner onsubmit()
	$("#iform").submit(function() { spinner(); });
}); 
function set_selected(name) {
	document.getElementsByName(name)[1].checked = true;
}
function enable_change(enable_change) {
	document.iform.disk.disabled = !enable_change;
}
//]]>
</script>
<table id="area_navigator">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="disks_manage.php"><span><?=gtext('HDD Management');?></span></a></li>
		<li class="tabinact"><a href="disks_init.php"><span><?=gtext('HDD Format');?></span></a></li>
		<li class="tabact"><a href="disks_manage_smart.php" title="<?=gtext('Reload page');?>"><span><?=gtext('S.M.A.R.T.');?></span></a></li>
		<li class="tabinact"><a href="disks_manage_iscsi.php"><span><?=gtext('iSCSI Initiator');?></span></a></li>
	</ul></td></tr>
</table>
<form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
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
			html_titleline2(gtext('Scheduled Self-Test Settings'));
?>
		</thead>
		<tbody>
			<tr>
				<td class="celltagreq"><?=gtext('Disk');?></td>
				<td class="celldatareq">
					<select name="disk" class="formfld" id="disk">
						<option value=""><?=gtext('Must choose one');?></option>
<?php
						foreach ($a_disk as $diskv):
							if(0 == strcmp($diskv['size'],'NA')):
								continue;
							endif;
							if(1 == disks_exists($diskv['devicespecialfile'])):
								continue;
							endif;
							if(!isset($diskv['smart'])):
								continue;
							endif;
?>
							<option value="<?=$diskv['devicespecialfile'];?>" <?php if($diskv['devicespecialfile'] === $pconfig['devicespecialfile']) echo "selected=\"selected\"";?>>
<?php
								$diskinfo = disks_get_diskinfo($diskv['devicespecialfile']);
								$helpinghand = format_bytes($diskinfo['mediasize_bytes'],2,true,!isset($config['system']['nonsidisksizevalues']));
								echo htmlspecialchars(sprintf('%s: %s (%s)',$diskv['name'],$helpinghand,$diskv['desc']));
?>
							</option>
<?php
						endforeach;
?>
					</select><br />
					<span class="vexpl"><?=gtext('Select a disk that is enabled for S.M.A.R.T. monitoring.');?></span>
				</td>
			</tr>
			<tr>
				<td class="celltagreq"><?=gtext('Type');?></td>
				<td class="celldatareq">
					<select name="type" class="formfld" id="type">
<?php
						$types = [gtext('Short Self-Test'),gtext('Long Self-Test'),gtext('Conveyance Self-Test'),gtext('Offline Immediate Test')];
						$vals = ['S','L','C','O'];
?>
<?php
						$j = 0;
						for ($j = 0; $j < count($vals); $j++):
?>
							<option value="<?=$vals[$j];?>" <?php if($vals[$j] == $pconfig['type']) echo "selected=\"selected\"";?>><?=htmlspecialchars($types[$j]);?></option>
<?php
						endfor;
?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="celltagreq"><?=gtext('Time');?></td>
				<td class="celldatareq">
					<table class="area_data_selection">
						<colgroup>
							<col style="width:30%">
							<col style="width:35%">
							<col style="width:20%">
							<col style="width:20%">
						</colgroup>
						<thead>
							<tr>
								<th class="lhell"><?=gtext('Hours');?></th>
								<th class="lhell"><?=gtext('Days');?></th>
								<th class="lhell"><?=gtext('Months');?></th>
								<th class="lhebl"><?=gtext('Week days');?></th>
							</tr>
						</thead>
						<tbody class="donothighlight"><tr>
							<td class="lcell">
								<div><input type="radio" name="all_hours" id="all_hours1" value="1" <?php if(1 == $pconfig['all_hours']) echo 'checked="checked"';?>/><?=gtext('All');?></div>
								<div><input type="radio" name="all_hours" id="all_hours2" value="0" <?php if(1 != $pconfig['all_hours']) echo 'checked="checked"';?>/><?=gtext('Selected');?> ..</div>
								<div><table><tbody><tr>
<?php
									$val_min = $i = 0;
									$val_count = 24;
									$val_max = $val_min + $val_count - 1;
									$val_break = 6;
									$i_outer_max = ceil($val_count / $val_break) - 1;
									$i_inner_max = $val_min + $val_break - 1;
									for($i_outer = 0;$i_outer <= $i_outer_max;$i_outer++):
										echo '<td valign="top">',"\n";
										echo '<select multiple="multiple" size="',$val_break,'" name="hour[]" id="hours1" onchange="set_selected(\'all_hours\')">',"\n";
										for($i_inner = $val_min;$i_inner <= $i_inner_max;$i_inner++):
											if($i <= $val_max):
												echo '<option value="',$i,'"';
												if(is_array($pconfig['hour']) && in_array("$i",$pconfig['hour'])):
													echo ' selected';
												endif;
												echo '>',$i,'</option>',"\n";
											else:
												break;
											endif;
											$i++;
										endfor;
										echo '</select>',"\n";
										echo '</td>',"\n";
									endfor;
?>
								</tr></tbody></table></div>
							</td>
							<td class="lcell">
								<div><input type="radio" name="all_days" id="all_days1" value="1" <?php if(1 == $pconfig['all_days']) echo 'checked="checked"';?>/><?=gtext('All');?></div>
								<div><input type="radio" name="all_days" id="all_days2" value="0" <?php if(1 != $pconfig['all_days']) echo 'checked="checked"';?>/><?=gtext('Selected');?> ..</div>
								<div><table><tbody><tr>
<?php
									$val_min = $i = 1;
									$val_count = 31;
									$val_max = $val_min + $val_count - 1;
									$val_break = 7;
									$i_outer_max = ceil($val_count / $val_break) - 1;
									$i_inner_max = $val_min + $val_break - 1;
									for($i_outer = 0;$i_outer <= $i_outer_max;$i_outer++):
										echo '<td valign="top">',"\n";
										echo '<select multiple="multiple" size="',$val_break,'" name="day[]" id="days1" onchange="set_selected(\'all_days\')">',"\n";
										for($i_inner = $val_min;$i_inner <= $i_inner_max;$i_inner++):
											if($i <= $val_max):
												echo '<option value="',$i,'"';
												if(is_array($pconfig['day']) && in_array("$i",$pconfig['day'])):
													echo ' selected';
												endif;
												echo '>',$i,'</option>',"\n";
											else:
												break;
											endif;
											$i++;
										endfor;
										echo '</select>',"\n";
										echo '</td>',"\n";
									endfor;
?>
								</tr></tbody></table></div>
							</td>
							<td class="lcell">
								<div><input type="radio" name="all_months" id="all_months1" value="1" <?php if(1 == $pconfig['all_months']) echo 'checked="checked"';?>/><?=gtext('All');?></div>
								<div><input type="radio" name="all_months" id="all_months2" value="0" <?php if(1 != $pconfig['all_months']) echo 'checked="checked"';?>/><?=gtext('Selected');?> ..</div>
								<div><table><tbody><tr>
									<td valign="top">
										<select multiple="multiple" size="12" name="month[]" id="months" onchange="set_selected('all_months')">
<?php
											foreach ($g_months as $key => $val):
												echo '<option value="',$key,'"';
												if(isset($pconfig['month']) && in_array((string)$key,$pconfig['month'])):
													echo ' selected="selected"';
												endif;
												echo '>',$val,'</option>',"\n";
											endforeach;
?>
										</select>
									</td>
								</tr></tbody></table></div>
							</td>
							<td class="lcebl">
								<div><input type="radio" name="all_weekdays" id="all_weekdays1" value="1" <?php if(1 == $pconfig['all_weekdays']) echo 'checked="checked"';?>/><?=gtext('All');?></div>
								<div><input type="radio" name="all_weekdays" id="all_weekdays2" value="0" <?php if(1 != $pconfig['all_weekdays']) echo 'checked="checked"';?>/><?=gtext('Selected');?> ..</div>
								<div><table><tbody><tr>
									<td valign="top">
										<select multiple="multiple" size="7" name="weekday[]" id="weekdays" onchange="set_selected('all_weekdays')">
<?php
											foreach($g_weekdays as $key => $val):
												echo '<option value="',$key,'"';
												if(isset($pconfig['weekday']) && in_array((string)$key,$pconfig['weekday'])):
													echo ' selected="selected"';
												endif;
												echo '>',$val,'</option>',"\n";
											endforeach;
?>
										</select>
									</td>
								</tr></tbody></table></div>
							</td>

						</tr></tbody>
					</table>
					<span class="vexpl"><?=gtext('Note: Ctrl-click (or command-click on the Mac) to select and de-select minutes, hours, days and months.');?></span>
				</td>
			</tr>
			<tr>
				<td class="celltag"><?=gtext('Description');?></td>
				<td class="celldata">
					<input name="desc" type="text" class="formfld" id="desc" size="40" value="<?=htmlspecialchars($pconfig['desc']);?>" />
				</td>
			</tr>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gtext('Save') : gtext('Add')?>" onclick="enable_change(true)" />
		<input name="Cancel" type="submit" class="formbtn" value="<?=gtext('Cancel');?>" />
		<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
if(isset($uuid) && (FALSE !== $cnid)):
?>
<script type="text/javascript">
<!-- Disable controls that should not be modified anymore in edit mode. -->
enable_change(false);
</script>
<?php
endif;
?>
<?php
include 'fend.inc';
?>
