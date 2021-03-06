<?php
/*
	extra.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
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
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS, either expressed or implied.
*/
require_once "_include/session.php";
require_once "_include/qxpath.php";
require_once "_include/str.php";

//------------------------------------------------------------------------------
// THESE ARE NUMEROUS HELPER FUNCTIONS FOR THE OTHER INCLUDE FILES
//------------------------------------------------------------------------------
function make_link($_action,$_dir,$_item = NULL,$_order = NULL,$_srt = NULL,$_lang = NULL) {
// make link to next page
	$a_query = [];
	if($_action == '' || $_action == NULL):
		$_action = 'list';
	endif;
	$a_query['action'] = $_action;
	if($_dir == ''):
		$_dir = NULL; 
	endif;
	$a_query['dir'] = $_dir;
	if($_item == ''):
		$_item = NULL;
	endif;
	$a_query['item'] = $_item;
	$a_query['order'] = $_order ?? $GLOBALS['order'] ?? NULL;
	$a_query['srt'] = $_srt ?? $GLOBALS['srt'] ?? NULL;
	$a_query['lang'] = $_lang ?? $GLOBALS['lang'] ?? NULL;
	$link = sprintf('%s?%s',$GLOBALS['script_name'],http_build_query($a_query,'','&',PHP_QUERY_RFC3986));
	return $link;
}

function get_abs_dir($path)
{
    return path_f($path);
}
// get absolute file+path
function get_abs_item($dir, $item) {
	return get_abs_dir($dir).DIRECTORY_SEPARATOR.$item;
}
/**
  get file relative from home
 */
function get_rel_item($dir, $item)
{
    return $dir == "" ? $item : "$dir/$item";
}

/**
  can this file be edited?
  */
function get_is_file($dir, $item)
{
    $filename = get_abs_item($dir, $item);
	return @is_file($filename);
}

// is this a directory?
function get_is_dir($dir, $item) {
	return @is_dir(get_abs_item($dir,$item));
}
// parsed file type (d / l / -)
function parse_file_type($dir,$item) {
	$abs_item = get_abs_item($dir, $item);
	if(@is_dir($abs_item)) return "d";
	if(@is_link($abs_item)) return "l";
	return "-";
}
// file permissions
function get_file_perms($dir,$item) {
	return @decoct(@fileperms(get_abs_item($dir,$item)) & 0777);
}
// parsed file permisions
function parse_file_perms($mode) {
	if(strlen($mode)<3) return "---------";
	$parsed_mode="";
	for($i=0;$i<3;$i++) {
		// read
		if(($mode{$i} & 04)) $parsed_mode .= "r";
		else $parsed_mode .= "-";
		// write
		if(($mode{$i} & 02)) $parsed_mode .= "w";
		else $parsed_mode .= "-";
		// execute
		if(($mode{$i} & 01)) $parsed_mode .= "x";
		else $parsed_mode .= "-";
	}
	return $parsed_mode;
}

/**
  file size
  */
function get_file_size($dir, $item)
{
	return @filesize(get_abs_item($dir, $item));
}
// parsed file size
/*
 *	replaced by format_bytes
 */
/*
function parse_file_size($size) {
	if($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 . " GiB";
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 . " MiB";
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 . " KiB";
	} else $size = $size . " Bytes";
	if($size==0) $size="-";

	return $size;
}
		*/
// file date
function get_file_date($dir, $item) {
	return @filemtime(get_abs_item($dir, $item));
}
// parsed file date
function parse_file_date($date) {
	return @date($GLOBALS["date_fmt"],$date);
}
// is this file an image?
function get_is_image($dir, $item) {
	if(!get_is_file($dir, $item)) {
		return false;
	}
	return preg_match('/'.$GLOBALS['images_ext'].'/i', $item);
}
// is this file editable?
function get_is_editable($dir, $item) {
	if(!get_is_file($dir, $item)) {
		return false;
	}
	foreach($GLOBALS["editable_ext"] as $pat) {
		if (preg_match('/'.$pat.'/i',$item)) {
			return true;
		}
	}
	return false;
}
// is this file editable?
function get_is_unzipable($dir, $item) {
	if(!get_is_file($dir, $item)) {
		return false;
	}
	foreach($GLOBALS["unzipable_ext"] as $pat) {
		if (preg_match('/'.$pat.'/i',$item)) {
			return true;
		}
	}
	return false;
}

function _get_used_mime_info ($item)
{
    foreach ($GLOBALS["used_mime_types"] as $mime)
    {
        list($desc, $img, $ext, $type) = $mime;
		if (preg_match('/'.$ext.'/i',$item)) {
            return array($mime, $img, $type);
		}
    }

    return array(NULL, NULL, NULL);
}

function get_mime_type ($dir, $item, $query)
{
	switch (filetype(get_abs_item($dir, $item))) {
		case "dir":
			$mime_type	= $GLOBALS["super_mimes"]["dir"][0];
			$image		= $GLOBALS["super_mimes"]["dir"][1];
			break;
		case "link":
			$mime_type	= $GLOBALS["super_mimes"]["link"][0];
			$image		= $GLOBALS["super_mimes"]["link"][1];
			break;
		default:
			list($mime_type, $image, $type) = _get_used_mime_info($item);
			if ($mime_type != NULL) {
				_debug("found mime type $mime_type");  
				break;
			}
		if ((function_exists("is_executable") && @is_executable(get_abs_item($dir,$item))) || preg_match('/'.$GLOBALS["super_mimes"]["exe"][2].'/i',$item)) {
				$mime_type	= $GLOBALS["super_mimes"]["exe"][0];
				$image		= $GLOBALS["super_mimes"]["exe"][1];
			} else {
				// unknown file
				_debug("unknown file type ");
				$mime_type	= $GLOBALS["super_mimes"]["file"][0];
				$image		= $GLOBALS["super_mimes"]["file"][1];
			}
	}
	switch ($query) {
		case "img":	return $image;
		case "ext":	return $type;
		default:	return $mime_type;
	}
}

/**
    Check if user is allowed to access $file in $directory
 */
function get_show_item ($directory, $file)
{
    // no relative paths are allowed in directories
    if ( preg_match( "/\.\./", $directory ) )
        return false;

    if ( isset($file) )
    {
        // file name must not contain any path separators
        if ( preg_match( "/[\/\\\\]/", $file ) )
            return false;

        // dont display own and parent directory
        if ( $file == "." || $file == ".." )
            return false;

        // determine full path to the file
        $full_path = get_abs_item( $directory, $file );
        _debug("full_path: $full_path");
        if ( ! str_startswith( $full_path, path_f() ) )
            return false;
    }

    // check if user is allowed to acces shidden files
    global $show_hidden;
    if ( ! $show_hidden )
    {
        if ( $file[0] == '.' )
            return false;

        // no part of the path may be hidden
        $directory_parts = explode( "/", $directory );
        foreach ( $directory_parts as $directory_part )
        {
            if ( $directory_part[0] == '.' )
                return false;
        }
    }

    if (matches_noaccess_pattern($file))
        return false;

    return true;
}

// copy dir
function copy_dir($source,$dest) {
	$ok = true;

	if ( !@mkdir($dest,0777) )
        return false;
	if ( ($handle = @opendir( $source ) ) === false)
        show_error($source."xx:".basename($source)."xx : ".$GLOBALS["error_msg"]["opendir"]);

	while(($file=readdir($handle))!==false) {
		if(($file==".." || $file==".")) continue;

		$new_source = $source."/".$file;
		$new_dest = $dest."/".$file;
		if(@is_dir($new_source)) {
			$ok=copy_dir($new_source,$new_dest);
		} else {
			$ok=@copy($new_source,$new_dest);
		}
	}
	closedir($handle);
	return $ok;
}

/**
    remove file / dir
 */
function remove ( $item )
{
	$ok = true;
	if(@is_link($item) || @is_file($item)) $ok=@unlink($item);
	elseif(@is_dir($item))
    {
		if(($handle=@opendir($item))===false)
            show_error($item.":".basename($item).": ".$GLOBALS["error_msg"]["opendir"]);

		while(($file=readdir($handle))!==false) {
			if(($file==".." || $file==".")) continue;

			$new_item = $item."/".$file;
			if(!@file_exists($new_item)) show_error(basename($item).": ".$GLOBALS["error_msg"]["readdir"]);
			//if(!get_show_item($item, $new_item)) continue;

			if(@is_dir($new_item)) {
				$ok=remove($new_item);
			} else {
				$ok=@unlink($new_item);
			}
		}

		closedir($handle);
		$ok=@rmdir($item);
	}
	return $ok;
}
// get php max_upload_file_size
function get_max_file_size() {
	$max = get_cfg_var("upload_max_filesize");
	if (preg_match('/G$/i',$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1073741824);
	} elseif(preg_match('/M$/i',$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1048576);
	} elseif(preg_match('/K$/i',$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1024);
	}

	return $max;
}
// dir deeper than home?
function down_home($abs_dir) {
	$real_home = @realpath($GLOBALS["home_dir"]);
	$real_dir = @realpath($abs_dir);

	if($real_home===false || $real_dir===false) {
		if(preg_match('/\\.\\./i',$abs_dir)) {
			return false;
		}
	} else if(strcmp($real_home,@substr($real_dir,0,strlen($real_home)))) {
		return false;
	}
	return true;
}

function id_browser() {
	$browser=$GLOBALS['__SERVER']['HTTP_USER_AGENT'];

	if(preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', $browser)) {
		return 'OPERA';
	} else if(preg_match('/MSIE ([0-9].[0-9]{1,2})/', $browser)) {
		return 'IE';
	} else if(preg_match('#OmniWeb/([0-9].[0-9]{1,2})#', $browser)) {
		return 'OMNIWEB';
	} else if(preg_match('#(Konqueror/)(.*)#', $browser)) {
		return 'KONQUEROR';
	} else if(preg_match('#Mozilla/([0-9].[0-9]{1,2})#', $browser)) {
		return 'MOZILLA';
	} else {
		return 'OTHER';
	}
}

?>
