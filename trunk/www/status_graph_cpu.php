<?php
/*
	status_graph_cpu.php

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

$status_cpu = true;
$pgtitle = array(gtext("Status"), gtext("Monitoring"), gtext("CPU Load"));

$status_cpu = true;
$graph_gap = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$graph_width = 397;
$graph_height = 220;

include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
		<ul id="tabnav">
		<?php require("status_graph_tabs.inc");?>
	   </ul>
	</td>
  </tr>
	<td class="tabcont">
	<?=gtext("Graph shows last 120 seconds");?>
	<div align="center" style="min-width:840px;">
		<br />
<?php
session_start();
$cpus = system_get_cpus();
if ($cpus > 1) {
	for ($j = 0; $j < $cpus; $j++) {                                             
		echo '<object id="graph" data="status_graph_cpu2.php?cpu='.$j.'" type="image/svg+xml" width="'.$graph_width.'" height="'.$graph_height.'">';
		echo '<param name="src" value="status_graph_cpu2.php?cpu='.$j.'" />';
		echo 'Your browser does not support this svg object type!<br /> You need to update your browser or use Internet Explorer 9 or higher.<br></br>';
		echo '</object>';
		$test = $j % 2;
		if ($test != 0) { echo '<br /><br /><br />'; }     /* add line breaks after second graph ... */
		else { echo $graph_gap; }                          /* or the gap between two graphs */
	}
}
?>
		<object id="graph" data="status_graph_cpu2.php" type="image/svg+xml" width="<?=$graph_width;?>" height="<?=$graph_height;?>">
		<param name="src" value="status_graph_cpu2.php" />
		</object>

</div>
</td></tr></table>
<?php include("fend.inc");?>
