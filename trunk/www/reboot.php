<?php
/*
	reboot.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
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

$pgtitle = array(gtext("System"), gtext("Reboot"), gtext("Now"));

if ($_POST) {
	if ($_POST['Submit'] !== gtext("No")) {
		$rebootmsg = gtext("The system is rebooting now. This may take one minute.");
	} else {
		header("Location: index.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="reboot.php" title="<?=gettext("Reload page");?>"><span><?=gtext("Now");?></span></a></li>
        <li class="tabinact"><a href="reboot_sched.php"><span><?=gtext("Scheduled");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<?php if (!empty($rebootmsg)): echo print_info_box($rebootmsg); sleep(1); system_reboot(); else:?>
			<form action="reboot.php" method="post" onsubmit="spinner()">
			  <strong><?=gtext("Are you sure you want to reboot the system?");?></strong>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Yes");?>" />
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("No");?>" />
				</div>
				<?php include("formend.inc");?>
			</form>
			<?php endif;?>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
