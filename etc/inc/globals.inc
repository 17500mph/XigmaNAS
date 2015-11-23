<?php
/*
	globals.inc

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2015 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of freenas (http://www.freenas.org).
	Copyright (c) 2005-2011 by Olivier Cochard (olivier@freenas.org).
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

$g = array(
	"varrun_path" => "/var/run",
	"varetc_path" => "/var/etc",
	"vardb_path" => "/var/db",
	"varlog_path" => "/var/log",
	"etc_path" => "/etc",
	"tmp_path" => "/tmp",
	"conf_path" => "/conf",
	"ftmp_path" => "/var/tmp/ftmp",
	"media_path" => "/mnt",
	"conf_default_path" => "/conf.default",
	"cf_path" => "/cf",
	"cf_conf_path" => "/cf/conf",
	"www_path" => "/usr/local/www",
	"xml_rootobj" => "nas4free",
	"debug" => false,
	"latest_config" => "2.3",
	"wireless_regex" => "/^(ndis|wlan|an|ath|bwi|bwn|ipw|iwi|iwn|malo|mwl|ral|wi|wpi|rsu|rum|run|uath|upgt|ural|urtw|urtwn|usie|zyd)/",
	"default_passwd" => "nas4free",
	"default_ip" => "192.168.1.250"
);

$g_install = array(
	"default_swap" => "1024",
	"part1min_embedded" => "466",	/*  500MB= 476MiB ->  470MB - alignment */
	"part1size_embedded" => "800",	/* 2000MB=1907MiB ->  768MB + SWAP1024MB */
	"part1size_full" => "2600"	/* 4000MB=3814MiB -> 2560MB + SWAP1024MB */
);

/* Available languages/locales */
$g_languages = array (
	"nas4free-sq"    => array("codeset" => "UTF-8", "desc" => gettext("Albanian")),
	"nas4free-bg"    => array("codeset" => "UTF-8", "desc" => gettext("Bulgarian")),
	"nas4free-cs"    => array("codeset" => "UTF-8", "desc" => gettext("Czech")),
	"nas4free-zh_CN" => array("codeset" => "UTF-8", "desc" => gettext("Chinese (Simplified)")),
	"nas4free-zh_TW" => array("codeset" => "UTF-8", "desc" => gettext("Chinese (Traditional)")),
	"nas4free-nl"    => array("codeset" => "UTF-8", "desc" => gettext("Dutch")),
	"nas4free-da"    => array("codeset" => "UTF-8", "desc" => gettext("Danish")),
	"nas4free-en_US" => array("codeset" => "UTF-8", "desc" => gettext("English")),
	"nas4free-fi"    => array("codeset" => "UTF-8", "desc" => gettext("Finnish")),
	"nas4free-fr"    => array("codeset" => "UTF-8", "desc" => gettext("French")),
	"nas4free-de"    => array("codeset" => "UTF-8", "desc" => gettext("German")),
	"nas4free-el"    => array("codeset" => "UTF-8", "desc" => gettext("Greek")),
	"nas4free-hu"    => array("codeset" => "UTF-8", "desc" => gettext("Hungarian")),
	"nas4free-it"    => array("codeset" => "UTF-8", "desc" => gettext("Italian")),
	"nas4free-ja"    => array("codeset" => "UTF-8", "desc" => gettext("Japanese")),
	"nas4free-ko"    => array("codeset" => "UTF-8", "desc" => gettext("Korean")),
	"nas4free-lv"    => array("codeset" => "UTF-8", "desc" => gettext("Latvian")),
	"nas4free-nb"    => array("codeset" => "UTF-8", "desc" => gettext("Norwegian (Bokmal)")),
	"nas4free-pl"    => array("codeset" => "UTF-8", "desc" => gettext("Polish")),
	"nas4free-pt_BR" => array("codeset" => "UTF-8", "desc" => gettext("Portuguese (Brazil)")),
	"nas4free-pt"    => array("codeset" => "UTF-8", "desc" => gettext("Portuguese (Portugal)")),
	"nas4free-ro"    => array("codeset" => "UTF-8", "desc" => gettext("Romanian")),
	"nas4free-ru"    => array("codeset" => "UTF-8", "desc" => gettext("Russian")),
	"nas4free-sl"    => array("codeset" => "UTF-8", "desc" => gettext("Slovenian")),
	"nas4free-es"    => array("codeset" => "UTF-8", "desc" => gettext("Spanish")),
	"nas4free-sv"    => array("codeset" => "UTF-8", "desc" => gettext("Swedish")),
	"nas4free-sk"    => array("codeset" => "UTF-8", "desc" => gettext("Slovak")),
	"nas4free-tr"    => array("codeset" => "UTF-8", "desc" => gettext("Turkish")),
	"nas4free-uk"    => array("codeset" => "UTF-8", "desc" => gettext("Ukrainian"))
);

/* Supported filesystems */
$g_filesystems = array(
	"" => "Unformated",
	"ufs" => "UFS with Soft Updates",
	"ufs_no_su" => "UFS",
	"ufsgpt" => "UFS (GPT and Soft Updates)",
	"ufsgpt_no_su" => "UFS (EFI/GPT)",
	"msdos" => "FAT32",
	"cd9660" => "CD/DVD",
	"ntfs" => "NTFS",
	"ext2" => "EXT2",
	"exfat" => "exFAT",
	"softraid" => "Software RAID",
	"geli" => "Encrypted",
	"zfs" => "ZFS storage pool device"
);

$g_file_upload_error = array(
	UPLOAD_ERR_OK => gettext("There is no error, the file uploaded with success."),
	UPLOAD_ERR_INI_SIZE => gettext("The uploaded file exceeds the upload_max_filesize directive in php.ini."),
	UPLOAD_ERR_FORM_SIZE => gettext("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form."),
	UPLOAD_ERR_PARTIAL => gettext("The uploaded file was only partially uploaded."),
	UPLOAD_ERR_NO_FILE => gettext("No file was uploaded."),
	UPLOAD_ERR_NO_TMP_DIR => gettext("Missing a temporary folder."),
	UPLOAD_ERR_CANT_WRITE => gettext("Failed to write file to disk."),
	UPLOAD_ERR_EXTENSION => gettext("File upload stopped by extension.")
);
?>
