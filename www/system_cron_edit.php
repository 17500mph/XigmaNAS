<?php
/*
	system_cron_edit.php

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
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = 'Location: system_cron.php';
$sphere_notifier = 'cronjob';
$sphere_array = [];
$sphere_record = [];
$prerequisites_ok = true;

if (isset($_GET['uuid'])):
	$uuid = $_GET['uuid'];
endif;
if(isset($_POST['uuid'])):
	$uuid = $_POST['uuid'];
endif;

$a_cronjob = &array_make_branch($config,'cron','job');
if(isset($uuid) && (false !== ($cnid = array_search_ex($uuid,$a_cronjob,'uuid')))):
	$pconfig['enable'] = isset($a_cronjob[$cnid]['enable']);
	$pconfig['uuid'] = $a_cronjob[$cnid]['uuid'];
	$pconfig['desc'] = $a_cronjob[$cnid]['desc'];
	$pconfig['minute'] = $a_cronjob[$cnid]['minute'];
	$pconfig['hour'] = $a_cronjob[$cnid]['hour'];
	$pconfig['day'] = $a_cronjob[$cnid]['day'];
	$pconfig['month'] = $a_cronjob[$cnid]['month'];
	$pconfig['weekday'] = $a_cronjob[$cnid]['weekday'];
	$pconfig['all_mins'] = $a_cronjob[$cnid]['all_mins'];
	$pconfig['all_hours'] = $a_cronjob[$cnid]['all_hours'];
	$pconfig['all_days'] = $a_cronjob[$cnid]['all_days'];
	$pconfig['all_months'] = $a_cronjob[$cnid]['all_months'];
	$pconfig['all_weekdays'] = $a_cronjob[$cnid]['all_weekdays'];
	$pconfig['who'] = $a_cronjob[$cnid]['who'];
	$pconfig['command'] = $a_cronjob[$cnid]['command'];
else:
	$pconfig['enable'] = true;
	$pconfig['uuid'] = uuid();
	$pconfig['desc'] = '';
	$pconfig['all_mins'] = 1;
	$pconfig['all_hours'] = 1;
	$pconfig['all_days'] = 1;
	$pconfig['all_months'] = 1;
	$pconfig['all_weekdays'] = 1;
	$pconfig['who'] = 'root';
	$pconfig['command'] = '';
endif;
if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	if(isset($_POST['Cancel']) && $_POST['Cancel']):
		header($sphere_header_parent);
		exit;
	endif;
	// Input validation.
	$reqdfields = ['desc','who','command'];
	$reqdfieldsn = [gtext('Description'),gtext('Who'),gtext('Command')];
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	if(gtext('Run Now') !== $_POST['Submit']):
		// Validate synchronization time
		do_input_validate_synctime($_POST,$input_errors);
	endif;
	if(empty($input_errors)):
		$cronjob = [];
		$cronjob['enable'] = isset($_POST['enable']) ? true : false;
		$cronjob['uuid'] = $_POST['uuid'];
		$cronjob['desc'] = $_POST['desc'];
		$cronjob['minute'] = !empty($_POST['minute']) ? $_POST['minute'] : null;
		$cronjob['hour'] = !empty($_POST['hour']) ? $_POST['hour'] : null;
		$cronjob['day'] = !empty($_POST['day']) ? $_POST['day'] : null;
		$cronjob['month'] = !empty($_POST['month']) ? $_POST['month'] : null;
		$cronjob['weekday'] = !empty($_POST['weekday']) ? $_POST['weekday'] : null;
		$cronjob['all_mins'] = $_POST['all_mins'];
		$cronjob['all_hours'] = $_POST['all_hours'];
		$cronjob['all_days'] = $_POST['all_days'];
		$cronjob['all_months'] = $_POST['all_months'];
		$cronjob['all_weekdays'] = $_POST['all_weekdays'];
		$cronjob['who'] = $_POST['who'];
		$cronjob['command'] = $_POST['command'];
		if(stristr($_POST['Submit'],gtext('Run Now'))):
			if($_POST['who'] != 'root'):
				mwexec2(escapeshellcmd("sudo -u {$_POST['who']} {$_POST['command']}"),$output,$retval);
			else:
				mwexec2(escapeshellcmd($_POST['command']),$output,$retval);
			endif;
			if(0 == $retval):
				$execmsg = gtext('The cron job has been executed successfully.');
				write_log("The cron job '{$_POST['command']}' has been executed successfully.");
			else:
				$execfailmsg = gtext('Failed to execute cron job.');
				write_log("Failed to execute cron job '{$_POST['command']}'.");
			endif;
		else:
			if(isset($uuid) && (false !== $cnid)):
				$a_cronjob[$cnid] = $cronjob;
				$mode = UPDATENOTIFY_MODE_MODIFIED;
			else:
				$a_cronjob[] = $cronjob;
				$mode = UPDATENOTIFY_MODE_NEW;
			endif;
			updatenotify_set($sphere_notifier,$mode,$cronjob['uuid']);
			write_config();
			header($sphere_header_parent);
			exit;
		endif;
	endif;
endif;
$pgtitle = [gtext('System'),gtext('Advanced'),gtext('Cron'),isset($uuid) ? gtext('Edit') : gtext('Add')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
<?php // Init spinner.?>
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
});
function set_selected(name) {
	document.getElementsByName(name)[1].checked = true;
}
//]]>
</script>
<table id="area_navigator">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="system_advanced.php"><span><?=gtext("Advanced");?></span></a></li>
		<li class="tabinact"><a href="system_email.php"><span><?=gtext("Email");?></span></a></li>
		<li class="tabinact"><a href="system_monitoring.php"><span><?=gtext("Monitoring");?></span></a></li>
		<li class="tabinact"><a href="system_email_reports.php"><span><?=gtext("Email Reports");?></span></a></li>
		<li class="tabinact"><a href="system_swap.php"><span><?=gtext("Swap");?></span></a></li>
		<li class="tabinact"><a href="system_rc.php"><span><?=gtext("Command Scripts");?></span></a></li>
		<li class="tabact"><a href="system_cron.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Cron");?></span></a></li>
		<li class="tabinact"><a href="system_loaderconf.php"><span><?=gtext("loader.conf");?></span></a></li>
		<li class="tabinact"><a href="system_rcconf.php"><span><?=gtext("rc.conf");?></span></a></li>
		<li class="tabinact"><a href="system_sysctl.php"><span><?=gtext("sysctl.conf");?></span></a></li>
	</ul></td></tr>
</table>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform">
<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
	if(!empty($execmsg)):
		print_info_box($execmsg);
	endif;
	if(!empty($execfailmsg)):
		print_error_box($execfailmsg);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline_checkbox2('enable',gtext('Cron job'),$pconfig['enable'] ? true : false,gtext('Enable'));
?>
		</thead>
		<tbody>
<?php
			html_inputbox2('command',gtext('Command'),$pconfig['command'],gtext('Specifies the command to be run.'),true,60);
			$a_user = [];
			foreach(system_get_user_list() as $userk => $userv):
				$a_user[$userk] = htmlspecialchars($userk);
			endforeach;
			html_combobox2('who',gtext('Who'),$pconfig['who'],$a_user,'',true);
			html_inputbox2('desc',gtext('Description'),$pconfig['desc'],gtext('You may enter a description here for your reference.'),true,40);
?>
			<tr>
				<td class="celltagreq"><?=gtext('Schedule Time');?></td>
				<td class="celldatareq">
					<table class="area_data_selection">
						<colgroup>
							<col style="width:20%">
							<col style="width:20%">
							<col style="width:24%">
							<col style="width:18%">
							<col style="width:18%">
						</colgroup>
						<thead>
							<tr>
								<th class="lhell"><?=gtext('Minutes');?></th>
								<th class="lhell"><?=gtext('Hours');?></th>
								<th class="lhell"><?=gtext('Days');?></th>
								<th class="lhell"><?=gtext('Months');?></th>
								<th class="lhebl"><?=gtext('Weekdays');?></th>
							</tr>
						</thead>
						<tbody class="donothighlight"><tr>
							<td class="lcell" style="vertical-align:top">
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_mins" id="all_mins1" value="1" <?php if(1 == $pconfig['all_mins']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('All');?></span>
									</label>
								</div>
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_mins" id="all_mins2" value="0" <?php if(1 != $pconfig['all_mins']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('Selected');?> ..</span>
									</label>
								</div>
								<div><table><tbody class="donothighlight"><tr>
<?php
									$val_min = $key = 0;
									$val_count = 60;
									$val_max = $val_min + $val_count - 1;
									$val_break = 15;
									$i_outer_max = ceil($val_count / $val_break) - 1;
									$i_inner_max = $val_min + $val_break - 1;
									for($i_outer = 0;$i_outer <= $i_outer_max;$i_outer++):
										echo '<td class="lcefl">',"\n";
										for($i_inner = $val_min;$i_inner <= $i_inner_max;$i_inner++):
											if($key <= $val_max):
												echo '<div class="cblo"><label>';
												echo '<input type="checkbox" class="cblo" name="minute[]" onchange="set_selected(\'all_mins\')" value="',$key,'"';
												if(isset($pconfig['minute']) && is_array($pconfig['minute']) && in_array((string)$key,$pconfig['minute'])):
													echo ' checked="checked"';
												endif;
												echo '/><span class="cblo">',sprintf('%02d',$key),'</span>';
												echo '</label></div>',"\n";
											else:
												break;
											endif;
											$key++;
										endfor;
										echo '</td>',"\n";
									endfor;
?>
								</tr></tbody></table></div>
							</td>
							<td class="lcell" style="vertical-align:top">
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_hours" id="all_hours1" value="1" <?php if(1 == $pconfig['all_hours']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('All');?></span>
									</label>
								</div>
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_hours" id="all_hours2" value="0" <?php if(1 != $pconfig['all_hours']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('Selected');?> ..</span>
									</label>
								</div>
								<div><table><tbody class="donothighlight"><tr>
<?php
									$val_min = $key = 0;
									$val_count = 24;
									$val_max = $val_min + $val_count - 1;
									$val_break = 6;
									$i_outer_max = ceil($val_count / $val_break) - 1;
									$i_inner_max = $val_min + $val_break - 1;
									for($i_outer = 0;$i_outer <= $i_outer_max;$i_outer++):
										echo '<td class="lcefl">',"\n";
										for($i_inner = $val_min;$i_inner <= $i_inner_max;$i_inner++):
											if($key <= $val_max):
												echo '<div class="cblo"><label>';
												echo '<input type="checkbox" class="cblo" name="hour[]" onchange="set_selected(\'all_hours\')" value="',$key,'"';
												if(isset($pconfig['hour']) && is_array($pconfig['hour']) && in_array((string)$key,$pconfig['hour'])):
													echo ' checked="checked"';
												endif;
												echo '/><span class="cblo">',sprintf('%02d',$key),'</span>';
												echo '</label></div>',"\n";
											else:
												break;
											endif;
											$key++;
										endfor;
										echo '</td>',"\n";
									endfor;
?>
								</tr></tbody></table></div>
							</td>
							<td class="lcell" style="vertical-align:top">
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_days" id="all_days1" value="1" <?php if(1 == $pconfig['all_days']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('All');?></span>
									</label>
								</div>
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_days" id="all_days2" value="0" <?php if(1 != $pconfig['all_days']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('Selected');?> ..</span>
									</label>
								</div>
								<div><table><tbody class="donothighlight"><tr>
<?php
									$val_min = $key = 1;
									$val_count = 31;
									$val_max = $val_min + $val_count - 1;
									$val_break = 7;
									$i_outer_max = ceil($val_count / $val_break) - 1;
									$i_inner_max = $val_min + $val_break - 1;
									for($i_outer = 0;$i_outer <= $i_outer_max;$i_outer++):
										echo '<td class="lcefl">',"\n";
										for($i_inner = $val_min;$i_inner <= $i_inner_max;$i_inner++):
											if($key <= $val_max):
												echo '<div class="cblo"><label>';
												echo '<input type="checkbox" class="cblo" name="day[]" onchange="set_selected(\'all_days\')" value="',$key,'"';
												if(isset($pconfig['day']) && is_array($pconfig['day']) && in_array((string)$key,$pconfig['day'])):
													echo ' checked="checked"';
												endif;
												echo '/><span class="cblo">',sprintf('%02d',$key),'</span>';
												echo '</label></div>',"\n";
											else:
												break;
											endif;
											$key++;
										endfor;
										echo '</td>',"\n";
									endfor;
?>
								</tr></tbody></table></div>
							</td>
							<td class="lcell" style="vertical-align:top">
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_months" id="all_months1" value="1" <?php if(1 == $pconfig['all_months']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('All');?></span>
									</label>
								</div>
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_months" id="all_months2" value="0" <?php if(1 != $pconfig['all_months']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('Selected');?> ..</span>
									</label>
								</div>
								<div><table><tbody class="donothighlight"><tr>
<?php
									echo '<td class="lcefl">',"\n";
									foreach ($g_months as $key => $val):
										echo '<div class="cblo"><label>';
										echo '<input type="checkbox" class="cblo" name="month[]" onchange="set_selected(\'all_months\')" value="',$key,'"';
										if(is_array($pconfig['month']) && in_array((string)$key,$pconfig['month'])):
											echo ' checked="checked"';
										endif;
										echo '/><span class="cblo">',$val,'</span>';
										echo '</label></div>',"\n";
									endforeach;
									echo '</td>',"\n";
?>
								</tr></tbody></table></div>
							</td>
							<td class="lcebl" style="vertical-align:top">
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_weekdays" id="all_weekdays1" value="1" <?php if(1 == $pconfig['all_weekdays']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('All');?></span>
									</label>
								</div>
								<div class="rblo">
									<label>
										<input type="radio" class="rblo" name="all_weekdays" id="all_weekdays2" value="0" <?php if(1 != $pconfig['all_weekdays']) echo 'checked="checked"';?>/>
										<span class="rblo"><?=gtext('Selected');?> ..</span>
									</label>
								</div>
								<div><table><tbody class="donothighlight"><tr>
<?php
									echo '<td class="lcefl">',"\n";
									foreach($g_weekdays as $key => $val):
										echo '<div class="cblo"><label>';
										echo '<input type="checkbox" class="cblo" name="weekday[]" onchange="set_selected(\'all_weekdays\')" value="',$key,'"';
										if(isset($pconfig['weekday']) && is_array($pconfig['weekday'])):
											if(in_array((string)$key,$pconfig['weekday'])):
												echo ' checked="checked"';
											endif;
											if(7 == $key): // Compatibility for non-ISO day of week 0 for Sunday
												if(in_array('0',$pconfig['weekday'])):
													echo ' checked="checked"';
												endif;
											endif;
										endif;
										echo '/><span class="cblo">',$val,'</span>';
										echo '</label></div>',"\n";
									endforeach;
									echo '</td>',"\n";
?>
								</tr></tbody></table></div>
							</td>
						</tr></tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (false !== $cnid)) ? gtext('Save') : gtext('Add')?>"/>
		<input name="Submit" id="runnow" type="submit" class="formbtn" value="<?=gtext('Run Now');?>"/>
		<input name="Cancel" type="submit" class="formbtn" value="<?=gtext('Cancel');?>"/>
		<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>"/>
	</div>
<?php
	include 'formend.inc';
?>
</form></td></tr></tbody></table>
<?php
include 'fend.inc';
?>
