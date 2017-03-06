<?php
/*
	fbegin.inc

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
header('Content-Type: text/html; charset=' . system_get_language_codeset());
  // XML declarations
/*
  some browser might be broken.
  echo '<?xml version="1.0" encoding="'.system_get_language_codeset().'"?>';
  echo "\n";
*/
echo '<!DOCTYPE html>',"\n"
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=system_get_language_code();?>" lang="<?=system_get_language_code();?>">
<head>
	<meta charset="<?=system_get_language_codeset();?>"/>
	<title><?=genhtmltitle($pgtitle ?? []);?></title>
	<?php if (isset($pgrefresh) && $pgrefresh):?>
	<meta http-equiv="refresh" content="<?=$pgrefresh;?>" />
	<?php endif;?>
	<link href="/css/gui.css" rel="stylesheet" type="text/css" />
	<link href="/css/navbar.css" rel="stylesheet" type="text/css" />
	<link href="/css/tabs.css" rel="stylesheet" type="text/css" />	
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/gui.js"></script>
	<script type="text/javascript" src="/js/spinner.js"></script>
	<script type="text/javascript" src="/js/spin.min.js"></script>
<?php
	if(isset($pglocalheader) && !empty($pglocalheader)):
		if(is_array($pglocalheader)):
			foreach($pglocalheader as $pglocalheaderv):
		 		echo $pglocalheaderv,"\n";
			endforeach;
		else:
			echo $pglocalheader,"\n";
		endif;
	endif;
?>
</head>
<body id="main">
<div id="spinner_main"></div>
<div id="spinner_overlay" style="display: none; background-color: white; position: fixed; left:0; top:0; height:100%; width:100%; opacity: 0.25;"></div>
<header id="g4h">
<?php
	if(!$_SESSION['g']['shrinkpageheader']):
?>
	<div id="header">
		<div id="headerrlogo">
			<div class="hostname">
				<span><?=system_get_hostname();?>&nbsp;</span>
			</div>
		</div>
		<div id="headerlogo">
			<a title="www.<?=get_product_url();?>" href="https://www.<?=get_product_url();?>" target="_blank"><img src="/images/header_logo.png" alt="logo" /></a>
		</div>
	</div>
<?php
	endif; 
?>
	<div id="headernavbar">
		<ul id="navbarmenu">
<?php
			display_headermenu();
?>
		</ul>
		<div style="clear:both"></div>
	</div>
	<div id="gapheader"></div>
</header>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
<?php // init spinner on tabnav.?>
	$("#tabnav").on('click', function() { spinner(); });
<?php // and his mate.?>
	$("#tabnav2").on('click', function() { spinner(); });
});
//]]>
</script>
<main id="g4m">
<div id="pagecontent">
<?php
if(!isset($pgtitle_omit) || !$pgtitle_omit):
?>
	<p class="pgtitle"><?=gentitle($pgtitle);?></p>
<?php
endif;
?>