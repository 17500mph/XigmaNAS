<?php
/*
	header.php

	Part of NAS4Free (https://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of Quixplorer (http://quixplorer.sourceforge.net).
	Authors: quix@free.fr, ck@realtime-projects.com.
	The Initial Developer of the Original Code is The QuiX project.

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
/* NAS4FREE CODE */
require_once 'guiconfig.inc';
require_once 'session.inc';

Session::start();
// Check if session is valid
if (!Session::isLogin()) {
	header('Location: /login.php');
	exit;
}
/* QUIXPLORER CODE */
// header for html-page
function show_header($title, $additional_header_content = null) {
	global $g;
	global $config;
    global $site_name;
	
	$pgtitle = [gtext('Tools'), gtext('File Manager')];

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-Type: text/html; charset=".$GLOBALS["charset"]);
/* NAS4FREE & QUIXPLORER CODE*/
	// Html & Page Headers
	echo '<!DOCTYPE html>',"\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="',system_get_language_code(),'" lang="',system_get_language_code(),'" dir="',$GLOBALS['text_dir'],'">',"\n";
	echo '<head>',"\n";
	echo '<meta charset="',$GLOBALS["charset"],'">',"\n";
	echo '<meta name="format-detection" content="telephone=no"/>',"\n";
	echo '<title>',genhtmltitle($pgtitle ?? []),'</title>',"\n";
	echo '<link href="./_style/style.css" rel="stylesheet" type="text/css">',"\n";
	echo '<link href="../css/gui.css" rel="stylesheet" type="text/css">',"\n";
	echo '<link href="../css/navbar.css" rel="stylesheet" type="text/css">',"\n";
	echo '<link href="../css/tabs.css" rel="stylesheet" type="text/css">',"\n";	
	echo '<script type="text/javascript" src="../js/jquery.min.js"></script>',"\n";
	echo '<script type="text/javascript" src="../js/gui.js"></script>',"\n";
	echo '<script type="text/javascript" src="../js/spinner.js"></script>',"\n";
	echo '<script type="text/javascript" src="../js/spin.min.js"></script>',"\n";
	if(isset($pglocalheader) && !empty($pglocalheader)):
		if(is_array($pglocalheader)):
			foreach($pglocalheader as $pglocalheaderv):
		 		echo $pglocalheaderv,"\n";
			endforeach;
		else:
			echo $pglocalheader,"\n";
		endif;
	endif;
	echo '</head>',"\n";
	// NAS4Free Header
	echo '<body id="main">',"\n";
	echo '<div id="spinner_main"></div>',"\n";
	echo '<div id="spinner_overlay" style="display: none; background-color: white; position: fixed; left:0; top:0; height:100%; width:100%; opacity: 0.25;"></div>',"\n";
	echo '<header id="g4h">',"\n";
	if(!$_SESSION['g']['shrinkpageheader']):
		echo '<div id="header">',"\n";
		echo '<div id="headerlogo">',"\n";
		echo '<a title="www.',get_product_url(),'" href="https://www.',get_product_url(),'" target="_blank"><img src="../images/header_logo.png" alt="logo"/></a>',"\n";
		echo '</div>',"\n";
		echo '<div id="headerrlogo">',"\n";
		echo '<div class="hostname">',"\n";
		echo '<span>',system_get_hostname(),'&nbsp;</span>',"\n";
		echo '</div>',"\n";
		echo '</div>',"\n";
		echo '</div>',"\n";
	endif;
	display_headermenu();
	echo '<div id="gapheader"></div>', "\n";
	echo "</header>\n";
	echo '<main id="g4m">', "\n";
	echo '<div id="pagecontent">';
	// QuiXplorer Header
	if(!isset($pgtitle_omit) || !$pgtitle_omit):
		echo '<p class="pgtitle">',gentitle($pgtitle),'</p>',"\n";
	endif;
	echo '<table id="area_data"><tbody><tr><td id="area_data_frame">',"\n",
			'<table class="area_data_settings"><tbody><tr>',"\n",
				'<td class="lhetop" style="text-align:left">';
					if($GLOBALS['require_login'] && isset($GLOBALS['__SESSION']['s_user'])):
						echo '[',$GLOBALS['__SESSION']['s_user'],'] ';
					endif;
					echo $title;
	echo		'</td>',"\n",
				'<td class="lhetop" style="text-align:right">Powered by QuiXplorer</td>',"\n",
			'</tr></tbody></table>',"\n";
}
?>
