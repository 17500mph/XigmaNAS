<?php
/*
	reboot_sched.php

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

array_make_branch($config,'reboot');
$pconfig['enable'] = isset($config['reboot']['enable']);
$pconfig['minute'] = $config['reboot']['minute'];
$pconfig['hour'] = $config['reboot']['hour'];
$pconfig['day'] = $config['reboot']['day'];
$pconfig['month'] = $config['reboot']['month'];
$pconfig['weekday'] = $config['reboot']['weekday'];
$pconfig['all_mins'] = $config['reboot']['all_mins'];
$pconfig['all_hours'] = $config['reboot']['all_hours'];
$pconfig['all_days'] = $config['reboot']['all_days'];
$pconfig['all_months'] = $config['reboot']['all_months'];
$pconfig['all_weekdays'] = $config['reboot']['all_weekdays'];
if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	// Validate synchronization time
	if(isset($_POST['enable'])):
		do_input_validate_synctime($_POST,$input_errors);
	endif;
	if(empty($input_errors)):
		$config['reboot']['enable'] = isset($_POST['enable']) ? true : false;
		$config['reboot']['minute'] = !empty($_POST['minute']) ? $_POST['minute'] : null;
		$config['reboot']['hour'] = !empty($_POST['hour']) ? $_POST['hour'] : null;
		$config['reboot']['day'] = !empty($_POST['day']) ? $_POST['day'] : null;
		$config['reboot']['month'] = !empty($_POST['month']) ? $_POST['month'] : null;
		$config['reboot']['weekday'] = !empty($_POST['weekday']) ? $_POST['weekday'] : null;
		$config['reboot']['all_mins'] = $_POST['all_mins'];
		$config['reboot']['all_hours'] = $_POST['all_hours'];
		$config['reboot']['all_days'] = $_POST['all_days'];
		$config['reboot']['all_months'] = $_POST['all_months'];
		$config['reboot']['all_weekdays'] = $_POST['all_weekdays'];
		write_config();
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			config_lock();
			$retval |= rc_update_service('cron');
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
	endif;
endif;
$pgtitle = [gtext('System'),gtext('Reboot'),gtext('Scheduled')];
include 'fbegin.inc';?>
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
		<li class="tabinact"><a href="reboot.php"><span><?=gtext('Now');?></span></a></li>
		<li class="tabact"><a href="reboot_sched.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Scheduled');?></span></a></li>
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
			html_titleline_checkbox2('enable',gtext('Scheduled Reboot'),!empty($pconfig['enable']) ? true : false,gtext('Enable'));
?>
		</thead>
		<tbody>
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
		<input name="Submit" type="submit" class="formbtn" value="<?=gtext('Save');?>"/>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
?>
