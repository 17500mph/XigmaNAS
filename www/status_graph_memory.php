<?php
/*
	status_graph_memory.php

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

$pgtitle = array(gtext("Status"), gtext("Monitoring"), gtext("Memory Usage"));

$rrd_memory = true;
$refresh = !empty($config['rrdgraphs']['refresh_time']) ? $config['rrdgraphs']['refresh_time'] : 300;
mwexec("/usr/local/share/rrdgraphs/bin/rrd-graph.sh memory", true);

include("fbegin.inc");?>
<meta http-equiv="refresh" content="<?=$refresh?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td class="tabnavtbl">
        <ul id="tabnav">
<?php require("status_graph_tabs.inc");?>
        </ul>
    </td>
</tr>
<td class="tabcont">
<form name="form2" action="status_graph_memory.php" method="get">
<?=sprintf(gtext("Graph updates every %d seconds"), $refresh);?>.&nbsp;<?=gtext("Selected graph:");?>&nbsp;&nbsp;&nbsp;
<select name="if" class="formfld" onchange="submit()">
    <?php
        $curif = "memory";
        if (isset($_GET['if']) && $_GET['if']) $curif = $_GET['if'];
        $ifnum = $curif;
        $ifdescrs = array('memory' => gtext("Standard"), 'memory-detailed' => gtext("Detailed"));
        foreach ($ifdescrs as $ifn => $ifd) {
        	echo "<option value=\"$ifn\"";
        	if ($ifn == $curif) echo " selected=\"selected\"";
        	echo ">" . htmlspecialchars($ifd) . "</option>\n";
        }
    ?>
</select>
</form>
<div align="center" style="min-width:840px;">
    <br>
    <img src="/images/rrd/rrd-<?=$ifnum;?>_daily.png?rand=<?=time()?>" alt="RRDGraphs Daily Memory Graph <?=$ifnum;?>" width="graph_width" height="graph_height">
    <br><br>
    <img src="/images/rrd/rrd-<?=$ifnum;?>_weekly.png?rand=<?=time()?>" alt="RRDGraphs Weekly Memory Graph" width="graph_width" height="graph_height">
    <br><br>
    <img src="/images/rrd/rrd-<?=$ifnum;?>_monthly.png?rand=<?=time()?>" alt="RRDGraphs Monthly Memory Graph" width="graph_width" height="graph_height">
    <br><br>
    <img src="/images/rrd/rrd-<?=$ifnum;?>_yearly.png?rand=<?=time()?>" alt="RRDGraphs Yearly Memory Graph" width="graph_width" height="graph_height">
</div>
</td></tr></table>
<?php include("fend.inc");?>
