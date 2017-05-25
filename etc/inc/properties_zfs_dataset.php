<?php

/*
  properties_zfs_dataset.php

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
require 'properties.php';

class zfs_dataset_properties {
	public $aclinherit;
	public $aclmode;
	public $compression;
	public $dedup;
	public $primarycache;
	public $secondarycache;
	public $sync;
	public $type;
	public $volmode;
	public $volblocksize;
	
	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->aclinherit = prop_aclinherit();
		$this->aclmode = prop_aclmode();
		$this->compression = prop_compression();
		$this->dedup = prop_dedup();
		$this->primarycache = prop_primarycache();
		$this->secondarycache = prop_secondarycache();
		$this->sync = prop_sync();
		$this->type = prop_type();
		$this->volblocksize = prop_volblocksize();
		$this->volmode = prop_volmode();
		return $this;
	}
}
function prop_aclinherit(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'discard' => gtext('Discard - Do not inherit entries'),
		'noallow' => gtext('Noallow - Only inherit deny entries'),
		'restricted' => gtext('Restricted - Inherit all but "write ACL" and "change owner"'),
		'passthrough' => gtext('Passthrough - Inherit all entries'),
		'passthrough-x' => gtext('Passthrough-X - Inherit all but "execute" when not specified')
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('aclinherit');
	$o->title(gtext('ACL Inherit'));
	$o->description(gtext('This attribute determines the behavior of Access Control List inheritance.'));
	$o->type('list');
	$o->defaultvalue('restricted');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_aclmode(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'discard' => gtext('Discard - Discard ACL'),
		'groupmask' => gtext('Groupmask - Mask ACL with mode'),
		'passthrough' => gtext('Passthrough - Do not change ACL'),
		'restricted' => gtext('Restricted')
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('aclmode');
	$o->title(gtext('ACL Mode'));
	$o->description(gtext('This attribute controls the ACL behavior when a file is created or whenever the mode of a file or a directory is modified.'));
	$o->type('list');
	$o->defaultvalue('discard');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_compression(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'on' => gtext('On'),
		'off' => gtext('Off'),
		'lz4' => 'lz4',
		'lzjb' => 'lzjb',
		'gzip' => 'gzip',
		'gzip-1' => 'gzip-1',
		'gzip-2' => 'gzip-2',
		'gzip-3' => 'gzip-3',
		'gzip-4' => 'gzip-4',
		'gzip-5' => 'gzip-5',
		'gzip-6' => 'gzip-6',
		'gzip-7' => 'gzip-7',
		'gzip-8' => 'gzip-8',
		'gzip-9' => 'gzip-9',
		'zle' => 'zle'
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('compression');
	$o->title(gtext('Compression'));
	$o->description(gtext("Controls the compression algorithm used for this volume. 'LZ4' is now the recommended compression algorithm. Setting compression to 'On' uses the LZ4 compression algorithm if the feature flag lz4_compress is active, otherwise LZJB is used. You can specify the 'GZIP' level by using the value 'GZIP-N', where N is an integer from 1 (fastest) to 9 (best compression ratio). Currently, 'GZIP' is equivalent to 'GZIP-6'."));
	$o->type('list');
	$o->defaultvalue('on');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_dedup(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'on' => gtext('On'),
		'off' => gtext('Off'),
		'verify' => gtext('Verify'),
		'sha256' => 'SHA-256',
		'sha256,verify' => gtext('SHA-256, Verify'),
		'sha512' => 'SHA-512',
		'sha512,verify' => gtext('SHA-512, Verify'),
		'skein' => 'Skein',
		'skein,verify' => gtext('Skein, Verify'),
//		'edonr,verify' => gtext('Edon-R, Verify')
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('dedup');
	$o->title(gtext('Dedup Method'));
	$description = '<div>' . gtext('Controls the dedup method.') . '</div>'
		. '<div><b>'
		. '<font color="red">' . gtext('WARNING') . '</font>' . ': '
		. '<a href="https://wiki.nas4free.org/doku.php?id=documentation:setup_and_user_guide:disks_zfs_datasets_dataset" target="_blank">'
		. gtext('See ZFS datasets & deduplication wiki article BEFORE using this feature.')
		. '</a>'
		. '</b></div>';
	$o->description($description);
	$o->type('list');
	$o->defaultvalue('off');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_primarycache(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'all' => gtext('Both user data and metadata will be cached in ARC.'),
		'metadata' => gtext('Only metadata will be cached in ARC.'),
		'none' => gtext('Neither user data nor metadata will be cached in ARC.')
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('primarycache');
	$o->title(gtext('Primary Cache'));
	$o->description(gtext('Controls what is cached in the primary cache (ARC).'));
	$o->type('list');
	$o->defaultvalue('all');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_secondarycache(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'all' => gtext('Both user data and metadata will be cached in L2ARC.'),
		'metadata' => gtext('Only metadata will be cached in L2ARC.'),
		'none' => gtext('Neither user data nor metadata will be cached in L2ARC.')
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions = [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('secondarycache');
	$o->title(gtext('Secondary Cache'));
	$o->description(gtext('Controls what is cached in the secondary cache (L2ARC).'));
	$o->type('list');
	$o->defaultvalue('all');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_sync(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'standard' => gtext('Standard'),
		'always' => gtext('Always'),
		'disabled' => gtext('Disabled')
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions = [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('sync');
	$o->title(gtext('Sync'));
	$o->description(gtext('Controls the behavior of synchronous requests.'));
	$o->type('list');
	$o->defaultvalue('standard');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_type(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'filesystem' => gtext('File System - can be mounted within the standard system namespace and behaves like other file systems.'),
		'volume' => gtext('Volume - A logical volume. Can be exported as a raw or block device.')
//		'snapshot' => gtext('Snapshot - A read-only version of a file system or volume at a given point in time.')
//		'bookmark' => gtext('Bookmark - Creates a bookmark of a given snapshot.')
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('type');
	$o->title(gtext('Dataset Type'));
	$o->description(gtext(''));
	$o->type('list');
	$o->defaultvalue('filesystem');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(false);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_volblocksize(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'' => gtext('Default'),
		'512B' => '512B',
		'1K' => '1K',
		'2K' => '2K',
		'4K' => '4K',
		'8K' => '8K',
		'16K' => '16K',
		'32K' => '32K',
		'64K' => '64K',
		'128K' => '128K'
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('volblocksize');
	$o->title(gtext('Block Size'));
	$o->description(gtext('ZFS volume block size. This value can not be changed after creation.'));
	$o->type('list');
	$o->defaultvalue('');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(false);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_volmode(properties_list $o = NULL): properties_list {
	if(is_null($o)):
		$o = new properties_list();
	endif;
	$options = [
		'default' => gtext('Default'),
		'geom' => 'geom',
		'dev' => 'dev',
		'none' => 'none'
	];
	$filter = FILTER_VALIDATE_REGEXP;
	$filteroptions =  [
		'flags' => FILTER_REQUIRE_SCALAR,
		'options' => [
			'default' => NULL,
			'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))
		]
	];
	$o->name('volmode');
	$o->title(gtext('Volume Mode'));
	$o->description(gtext('Specifies how the volume should be exposed to the OS.'));
	$o->type('list');
	$o->defaultvalue('default');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter($filter);
	$o->filteroptions($filteroptions);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
