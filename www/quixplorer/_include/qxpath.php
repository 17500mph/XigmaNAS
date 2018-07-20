<?php
/*
	qxpath.php

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

/**
  @returns the FullPath out of an RelativePath

  A FullPath is the full path including the home directory and a subdirectory
  below of it.

  If the home directory is set to '/var/www/data' in the conf.php ('home_dir'),
  and you provide a RelativePath of 'first_subdirectory', the function returns
  '/var/www/data/first_subdirectory'.

  This path is intended for internal use and not for presentation to the
  user, since he should only see relative pathes.
 
 */
function path_f ($path = '')
{
    global $home_dir;
    $abs_dir = $home_dir;
    switch ($path)
    {
        case '.':
        case '': return realpath($abs_dir);
    }
    
    return realpath(realpath($home_dir) . "/$path");
}

function path_r ($path)
{
    global $home_dir;
    $base = realpath($home_dir);
    $ret = preg_replace("#^$base#", "", $path);
    return $ret;
}

function path_up ($path)
{
    $ret = dirname($path);
    // make sure that we stop at the root directory
    // and convert the "." to an empty string
    return $ret == "." ?  "" : $ret;
}

?>
