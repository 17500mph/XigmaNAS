<?php
/*
	list.php

	Part of NAS4Free (http://www.nas4free.org).
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
require_once './_include/permissions.php';
require_once './_include/login.php';
require_once './_include/qxpath.php';

function make_list($_list1,$_list2) { // make list of files
	$list = [];

	if($GLOBALS['srt'] == 'yes'):
		$list1 = $_list1;
		$list2 = $_list2;
	else:
		$list1 = $_list2;
		$list2 = $_list1;
	endif;
	if(is_array($list1)):
		foreach($list1 as $key => $val):
			$list[$key] = $val;
		endforeach;
	endif;
	if(is_array($list2)):
		foreach($list2 as $key => $val):
			$list[$key] = $val;
		endforeach;
	endif;
	return $list;
}
/**
 make table of files in dir
 make tables & place results in reference-variables passed to function
 also 'return' total filesize & total number of items
*/
function make_tables($dir,&$dir_list,&$file_list,&$tot_file_size,&$num_items) {
	$tot_file_size = $num_items = 0;

	// Open directory
	$handle = @opendir(get_abs_dir($dir));
	if($handle === false):
		show_error($dir . ': ' . $GLOBALS['error_msg']['opendir']);
	endif;
	// Read directory
	while(($new_item = readdir($handle)) !== false):
		$abs_new_item = get_abs_item($dir, $new_item);
		if(!get_show_item($dir,$new_item)):
			continue;
		endif;
		$new_file_size = is_link($abs_new_item) ? 0 : @filesize($abs_new_item);
		$tot_file_size += $new_file_size;
		$num_items++;
		if(is_dir($dir.DIRECTORY_SEPARATOR.$new_item)):
			if($GLOBALS['order'] == 'mod'):
				$dir_list[$new_item] = @filemtime($abs_new_item);
			else:
				// order == "size", "type" or "name"
				$dir_list[$new_item] = $new_item;
			endif;
		else:
			if($GLOBALS['order'] == 'size'):
				$file_list[$new_item] = $new_file_size;
			elseif ($GLOBALS['order'] == 'mod'):
				$file_list[$new_item] = @filemtime($abs_new_item);
			elseif ($GLOBALS['order'] == 'type'):
				$file_list[$new_item] = get_mime_type($dir,$new_item,'type');
			else:
				// order == "name"
				$file_list[$new_item] = $new_item;
			endif;
		endif;
	endwhile;
	closedir($handle);
	//	sort directories
	if(is_array($dir_list)):
		if($GLOBALS['order'] == 'mod'):
			if ($GLOBALS['srt'] == 'yes'):
				arsort($dir_list);
			else:
				asort($dir_list);
			endif;
		else:
			// order == "size", "type" or "name"
			if ($GLOBALS['srt'] == 'yes'):
				ksort($dir_list);
			else:
				krsort($dir_list);
			endif;
		endif;
	endif;
	//	sort files
	if(is_array($file_list)):
		if($GLOBALS['order'] == 'mod'):
			if($GLOBALS["srt"] == 'yes'):
				arsort($file_list);
			else:
				asort($file_list);
			endif;
		elseif($GLOBALS['order'] == 'size' || $GLOBALS['order'] == 'type'):
			if($GLOBALS['srt'] == 'yes'):
				asort($file_list);
			else:
				arsort($file_list);
			endif;
		else:
			// order == "name"
			if($GLOBALS['srt'] == 'yes'):
				ksort($file_list);
			else:
				krsort($file_list);
			endif;
		endif;
	endif;
}

/**
  print table of files
 */
function print_table($dir,$list) {
	if(!is_array($list)):
		return;
	endif;
	foreach($list as $item => $value):
		// link to dir / file
		$abs_item = get_abs_item($dir,$item);
		$target='';
		if(is_dir($abs_item)):
			$link = make_link('list',get_rel_item($dir,$item),NULL);
		else:
			$link = make_link('download',$dir,$item);
			$target = '_blank';
		endif;
		echo '<tr class="rowdata">';
		echo '<td class="lcelc"><input type="checkbox" name="selitems[]" value="',htmlspecialchars($item),'" onclick="javascript:Toggle(this);"></td>',"\n";
		// Icon + Link
		echo '<td class="lcell" style="white-space: nowrap">';
		if(permissions_grant($dir,$item,'read')):
			echo '<a href="',$link,'">';
		endif;
		echo '<img style="vertical-align:middle" width="16" height="16" src="_img/',get_mime_type($dir,$item,'img'),'" alt="">&nbsp;';
		$s_item = $item;
		if(strlen($s_item)>50):
			$s_item = substr($s_item,0,47) . '...';
		endif;
		echo htmlspecialchars($s_item);
		if(permissions_grant($dir,$item,'read')):
			echo '</a>';
		endif;
		echo '</td>',"\n";
		// Size
		echo '<td class="lcell">',format_bytes(get_file_size($dir,$item),2,false,false),sprintf('%10s','&nbsp;'),'</td>',"\n";
		// Type
		echo '<td class="lcell">',_get_link_info($dir,$item,'type'),'</td>',"\n";
		// Modified
		echo '<td class="lcell">',parse_file_date(get_file_date($dir,$item)),'</td>',"\n";
		// Permissions
		echo '<td class="lcell">';
		if(permissions_grant($dir,NULL,'change')):
			echo '<a href="',make_link('chmod',$dir,$item),'" title="',$GLOBALS['messages']['permlink'],'">';
		endif;
		echo parse_file_type($dir,$item).parse_file_perms(get_file_perms($dir,$item));
		if(permissions_grant($dir,NULL,'change')):
			echo '</a>';
		endif;
		echo '</td>',"\n";
		// Actions
		echo '<td class="lcebl">';
		echo '<table><tbody><tr>';
		// Edit
		if(get_is_editable($dir, $item)):
			_print_link('edit',permissions_grant($dir,$item,'change'),$dir,$item);
		else:
			// Unzip
			if(get_is_unzipable($dir,$item)):
				_print_link('unzip',permissions_grant($dir,$item,'create'),$dir,$item);
			else:
				echo '<td><img style="vertical-align:middle" width="16" height="16" src="',$GLOBALS['baricons']['none'],'" alt=""></td>',"\n";
			endif;
		endif;
		// Download
		if(get_is_file($dir,$item)):
			_print_link('download',permissions_grant($dir,$item,'read'),$dir,$item);
		else:
			echo '<td><img style="vertical-align:middle" width="16" height="16" src="',$GLOBALS['baricons']['none'],'" alt=""></td>',"\n";
		endif;
		echo '</tr></tbody></table>';
		echo '</td>';
		echo '</tr>',"\n";
	endforeach;
}
/**
 MAIN FUNCTION
 */
function list_dir($dir) {
	_debug("list_dir: displaying directory $dir");

	if(!get_show_item($dir,NULL)):
		show_error($GLOBALS['error_msg']['accessdir'] . " : '$dir'");
	endif;

	// make file & dir tables, & get total filesize & number of items
	make_tables($dir,$dir_list,$file_list,$tot_file_size,$num_items);

	$s_dir = $dir;
	if (strlen($s_dir) > 50 ):
		$s_dir = '...' . substr($s_dir,-47);
	endif;
	show_header($GLOBALS['messages']['actdir'] . ': ' . _breadcrumbs($dir));
	// Javascript functions:
	include './_include/javascript.php';
	// Sorting of items
	$_img = '&nbsp;<img style="vertical-align:middle" width="10" height="10" src="_img/';
	if($GLOBALS['srt'] == 'yes'):
		$_srt = 'no';
		$_img .= '_arrowup.gif" alt="^">';
	else:
		$_srt = 'yes';
		$_img .= '_arrowdown.gif" alt="v">';
	endif;
	// Toolbar
	echo '<table class="area_data_settings"><tbody><tr>';
	echo '<td><table><tbody><tr>',"\n";
	// PARENT DIR
	echo '<td><a href="',make_link('list',path_up($dir),NULL),'">',
			'<img style="vertical-align:middle" width="16" height="16" src="',$GLOBALS['baricons']['up'],'" alt="',$GLOBALS['messages']['uplink'],'" title="',$GLOBALS['messages']['uplink'],'">',
		'</a></td>',"\n";
	// HOME DIR
	echo '<td><a href="',make_link('list',NULL,NULL),'">',
			'<img style="vertical-align:middle" width="16" height="16" src="',$GLOBALS['baricons']['home'],'" alt="',$GLOBALS['messages']['homelink'],'" title="',$GLOBALS['messages']['homelink'],'">',
		'</a></td>',"\n";
	// RELOAD
	echo '<td><a href="javascript:location.reload();">',
			'<img style="vertical-align:middle" width="16" height="16" src="',$GLOBALS['baricons']['reload'],'" alt="',$GLOBALS['messages']['reloadlink'],'" title="',$GLOBALS['messages']['reloadlink'],'">',
		'</a></td>',"\n";
	// SEARCH
	echo '<td><a href="',make_link('search',$dir,NULL),'">',
			'<img style="vertical-align:middle" width="16" height="16" src="',$GLOBALS['baricons']['search'],'" alt="',$GLOBALS['messages']['searchlink'],'" title="',$GLOBALS['messages']['searchlink'],'">',
		'</a></td>',"\n";
	echo '<td></td>';
	// print the download button
	_print_link('download_selected',permissions_grant($dir, NULL,'read'),$dir,NULL);
	// print the edit buttons
	_print_edit_buttons($dir);
	// ADMIN & LOGOUT
	if(login_is_user_logged_in()):
		echo '<td></td>';
		// ADMIN
		_print_link('admin',permissions_grant(NULL,NULL,'admin') || permissions_grant(NULL,NULL,'password'),$dir,NULL);
		// LOGOUT
		_print_link('logout',true,$dir,NULL);
	endif;
	echo '<td></td>';
	echo '</tr></tbody></table></td>',"\n";
	// Create File / Dir
	if(permissions_grant($dir,NULL,'create')):
		echo '<td style="text-align:right">',"\n";
		echo '<form action="',make_link('mkitem',$dir,NULL),'" method="post">',"\n";
		echo '<table style="width:100%"><tbody><tr><td>',"\n";
		echo '<img style="vertical-align:middle" width="16" height="16" src="',$GLOBALS['baricons']['add'],'" alt="">',"\n";
		echo '<select name="mktype">',"\n";
		echo '<option value="file">',$GLOBALS['mimes']['file'],'</option>',"\n";
		echo '<option value="dir">',$GLOBALS['mimes']['dir'],'</option>',"\n";
		echo '</select>',"\n";
		echo '<input name="mkname" type="text" size="15">',"\n";
		echo '<input type="submit" value="',$GLOBALS['messages']['btncreate'],'">',"\n";
		echo '</td></tr></tbody></table>',"\n";
		echo '</form>',"\n";
		echo '</td>',"\n";
	endif;
	echo "</tr></tbody></table>\n";
	// End Toolbar
	// Begin Table + Form for checkboxes
	echo '<form name="selform" method="post" action="',make_link('post',$dir,NULL),'">',"\n";
	echo '<table class="area_data_selection">';
	echo '<colgroup>';
	echo '<col style="width:5%">'; // checkbox
	echo '<col style="width:35%">'; // name
	echo '<col style="width:10%">'; // size
	echo '<col style="width:15%">'; // type
	echo '<col style="width:15%">'; // modified
	echo '<col style="width:10%">'; // permissions
	echo '<col style="width:10%">'; // toolbox
	echo '</colgroup>',"\n";
	// Table Header
//	echo '<tr><td colspan="7"><HR></td></tr>';
	echo '<thead>';
	echo '<tr>';
	echo '<th class="lhelc">',"\n";
	echo '<input type="checkbox" name="toggleAllC" onclick="javascript:ToggleAll(this);">';
	echo '</th>',"\n";
	$new_srt = ($GLOBALS['order'] == 'name') ? $_srt : 'yes'; 
	echo '<th class="lhell">';
	echo '<a href="',make_link('list',$dir,NULL,'name',$new_srt),'">',$GLOBALS['messages']['nameheader'];
	if($GLOBALS['order'] == 'name'):
		echo $_img;
	endif;
	echo '</a></th>',"\n";
	$new_srt = ($GLOBALS['order'] == 'size') ? $_srt : 'yes'; 
	echo '<th class="lhell">';
	echo '<a href="',make_link('list',$dir,NULL,'size',$new_srt),'">',$GLOBALS['messages']['sizeheader'];
	if($GLOBALS['order'] == 'size'):
		echo $_img;
	endif;
	echo '</a></th>',"\n";
	$new_srt = ($GLOBALS['order'] == 'type') ? $_srt : 'yes'; 
	echo '<th class="lhell">';
	echo '<a href="',make_link('list',$dir,NULL,'type',$new_srt),'">',$GLOBALS['messages']['typeheader'];
	if($GLOBALS['order'] == 'type') echo $_img;
	echo '</a></th>',"\n";
	$new_srt = ($GLOBALS['order'] == 'mod') ? $_srt : 'yes'; 
	echo '<th class="lhell">';
	echo '<a href="',make_link('list',$dir,NULL,'mod',$new_srt),'">',$GLOBALS["messages"]["modifheader"];
	if($GLOBALS['order'] == 'mod'):
		echo $_img;
	endif;
	echo '</a></th>',"\n";
	echo '<th class="lhell">',$GLOBALS['messages']['permheader'],'</th>',"\n";
	echo '<th class="lhebl">',$GLOBALS['messages']['actionheader'],'</th>',"\n";
	echo '</tr>',"\n";
	echo '</thead>';
	// make & print Table using lists
	echo '<tbody>';
	print_table($dir, make_list($dir_list, $file_list));
	echo '</tbody>';
	// print number of items & total filesize
	echo '<tfoot><tr>';
	echo '<th class="lcell"></th>';
	echo '<th class="lcell">',$num_items,' ',$GLOBALS['messages']['miscitems'],' (';
	$free = format_bytes(diskfreespace('/'),2,false,false);
	echo $GLOBALS['messages']['miscfree'],': ',$free,')</th>',"\n";
	echo '<th class="lcell">',format_bytes($tot_file_size,2,false,false),'</th>',"\n";
	echo '<th class="lcell"></th>';
	echo '<th class="lcell"></th>';
	echo '<th class="lcell"></th>';
	echo '<th class="lcebl"></th>';
	echo '</tr></tfoot>';
	echo '</table>',"\n";
	echo '<div id="submit">',"\n";
	echo '<input type="hidden" name="do_action">';
	echo '<input type="hidden" name="first" value="y">',"\n";
	echo '</div>',"\n";
	echo '</form>';
?>
<script type="text/javascript">
//<![CDATA[
	// Uncheck all items (to avoid problems with new items)
	var ml = document.selform;
	var len = ml.elements.length;
	for(var i=0; i<len; ++i) {
		var e = ml.elements[i];
		if(e.name == "selitems[]" && e.checked == true) {
			e.checked=false;
		}
	}
//]]>
</script><?php
}

// *** HELPER FUNCTIONS

function _print_edit_buttons ($dir) {
	// for the copy button the user must have create and read rights
	_print_link("copy", permissions_grant_all($dir, NULL, array("create", "read")), $dir, NULL);
	_print_link("move", permissions_grant($dir, NULL, "change"), $dir, NULL);
	_print_link("delete", permissions_grant($dir, NULL, "delete"), $dir, NULL);
// NAS4Free info: We disable upload function for security and limited space var/temp
//	_print_link("upload", permissions_grant($dir, NULL, "create") && get_cfg_var("file_uploads"), $dir, NULL);
//	_print_link("archive",
//		permissions_grant_all($dir, NULL, array("create", "read"))
//			&& ($GLOBALS["zip"] || $GLOBALS["tar"] || $GLOBALS["tgz"]),
//		$dir, NULL);
}

/**
  print out an button link in the toolbar.

  if $allow is set, make this button active and work, otherwise print
  an inactive button.
*/
function _print_link ($function, $allow, $dir, $item) {
	// the list of all available button and the coresponding data
	$functions = [
		'copy' => [
			'jfunction' => 'javascript:Copy();',
			'image' => $GLOBALS['baricons']['copy'],
			'imagedisabled' => $GLOBALS['baricons']['notcopy'],
			'message' => $GLOBALS['messages']['copylink']
		],
		'move' => [
			'jfunction' => 'javascript:Move();',
			'image' => $GLOBALS['baricons']['move'],
			'imagedisabled' => $GLOBALS['baricons']['notmove'],
			'message' => $GLOBALS['messages']['movelink']
		],
		'delete' => [
			'jfunction' => 'javascript:Delete();',
			'image' => $GLOBALS['baricons']['delete'],
			'imagedisabled' => $GLOBALS['baricons']['notdelete'],
			'message' => $GLOBALS['messages']['dellink']
		],
		'upload' => [
			'jfunction' => make_link('upload',$dir,NULL),		
			'image' => $GLOBALS['baricons']['upload'],
			'imagedisabled' => $GLOBALS['baricons']['notupload'],
			'message' => $GLOBALS['messages']['uploadlink']
		],
		'archive' => [
			'jfunction' => 'javascript:Archive();',
			'image' => $GLOBALS['baricons']['archive'],
			'message' => $GLOBALS['messages']['comprlink']
		],
		'admin' => [
			'jfunction' => make_link('admin',$dir,NULL),
			'image' => $GLOBALS['baricons']['admin'],
			'message' => $GLOBALS['messages']['adminlink']
		],
		'logout' => [
			'jfunction' => make_link('logout',NULL,NULL),
			'image' => $GLOBALS['baricons']['logout'],
			'imagedisabled' => '_img/_logout_.gif',
			'message' => $GLOBALS['messages']['logoutlink']
		],
		'edit' => [
			'jfunction' => make_link('edit',$dir,$item),
			'image' => $GLOBALS['baricons']['edit'],
			'imagedisabled' => $GLOBALS['baricons']['notedit'],
			'message' => $GLOBALS['messages']['editlink']
		],
		'unzip' => [
			'jfunction' => make_link('unzip',$dir,$item),
			'image' => $GLOBALS['baricons']['unzip'],
			'imagedisabled' => $GLOBALS['baricons']['notunzip'],
			'message' => $GLOBALS['messages']['unziplink']
		],
		'download' => [
			'jfunction' => make_link('download',$dir,$item),
			'image' => $GLOBALS['baricons']['download'],
			'imagedisabled' => $GLOBALS['baricons']['notdownload'],
			'message' => $GLOBALS['messages']['downlink']
		],
		'download_selected' => [
			'jfunction' => 'javascript:DownloadSelected();',
			'image' => $GLOBALS['baricons']['download'],
			'imagedisabled' => $GLOBALS['baricons']['notdownload'],
			'message' => $GLOBALS['messages']['download_selected']
		],
	];

	// determine the function of this button and it's data
	$values = $functions[$function];

	// make an active link if the access is allowed
	if($allow):
		echo '<td>',
				'<a href="',$values['jfunction'],'">',
					'<img',
						' style="vertical-align:middle"',
						' width="16"',
						' height="16" ',
						' src="',$values['image'],'"',
						' alt="',$values['message'],'"',
						' title="',$values['message'],'"',
					'>',
				'</a>',
			'</td>',"\n";
		return;
	endif;
	if(!isset($values['imagedisabled'])):
		return;
	endif;
	// make an inactive link if the access is forbidden
	echo '<td>',
			'<img',
				' style="vertical-align:middle"',
				' width="16"',
				' height="16"',
				' src="',$values['imagedisabled'],'"',
				' alt="',$values['message'],'"',
				' title="',$values["message"],'"',
			'>',
		'</td>',"\n";
}

function _get_link_info($dir, $item) {
	$type = get_mime_type($dir, $item, "type");
	if(is_array($type)):
		$type = $type[0];
	endif;

	if(! file_exists(get_abs_item($dir, $item))):
		return '<span style="background:red;">'.$type.'</span>';
	endif;
	return $type;
}

/*
 * The breadcrumbs function will take the user's current path and build a breadcrumb.
 * 
 * 	A breadcrums is a list of links for each directory in the current path.
 * 
 * 	@param
 * $curdir is a string containing what will usually be the users
 * current directory.  %displayseparator is optional and contains a
 * string that will be displayed betweenach crumb.
 * 
 *  Typical syntax:
 * 
 * echo breadcrumbs($dir, ">>");
 * show_header($GLOBALS["messages"]["actdir"].":".breadcrumbs($dir));
 */
function _breadcrumbs($curdir, $displayseparator = ' &raquo; ') {
	//Get localized name for the Home directory
	$homedir = $GLOBALS["messages"]["homelink"];
	// Initialize first crumb and set it to the home directory.
	$breadcrumbs[] = "<a href=\"".make_link("list", "", NULL)."\">$homedir</a>";
	// Take the current directory and split the string into an array at each '/'.
	$patharray = explode('/', $curdir);
	// Find out the index for the last value in our path array
	$lastx = array_keys($patharray);
	$last = end($lastx);
	// Build the rest of the breadcrumbs
	$crumbdir = "";
	foreach ($patharray AS $x => $crumb) {
		// Add a new directory to the directory list so the link has the
		// correct path to the current crumb.
		$crumbdir = $crumbdir . $crumb;
		if($x != $last):
			// If we are not on the last index, then create a link using $crumb
			// as the text.
			$breadcrumbs[] = "<a href=\"".make_link("list", $crumbdir, NULL)."\">".htmlspecialchars($crumb)."</a>";
			// Add a separator between our crumbs.
			$crumbdir = $crumbdir . DIRECTORY_SEPARATOR;
		else:
			// Don't create a link for the final crumb.  Just display the crumb name.
			$breadcrumbs[] = htmlspecialchars($crumb);
		endif;
	}
	// Build temporary array into one string.
	return implode($displayseparator, $breadcrumbs);
}
?>
