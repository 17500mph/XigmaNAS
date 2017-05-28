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
/*
 * Enable property variable, init call in method load and property method to activate a property
 */
class properties_zfs_dataset {
//	public $aclinherit;
//	public $aclmode;
//	public $atime;
//	public $canmount;
//	public $casesensitivity;
//	public $checksum;
	public $compression;
//	public $copies;
	public $dedup;
//	public $devices;
//	public $exec;
//	public $jailed;
//	public $logbias;
//	public $nbmand;
//	public $normalization;
	public $primarycache;
//	public $readonly;
//	public $redundant_metadata;
	public $secondarycache;
//	public $setuid;
//	public $sharesmb;
//	public $sharenfs;	
//	public $snapdir;
	public $sync;
	public $type;
//	public $utf8only;
	public $volmode;
	public $volblocksize;
	public $volsize;
//	public $vscan;
//	public $xattr;
	
	public function __construct() {
		$this->load();
	}
	public function load() {
//		$this->aclinherit = prop_aclinherit();
//		$this->aclmode = prop_aclmode();
//		$this->atime = prop_atime();
//		$this->canmount = prop_canmount();
//		$this->casesensitivity = prop_casesensitivity();
//		$this->checksum = prop_checksum();
		$this->compression = prop_compression();
//		$this->copies = prop_copies();
		$this->dedup = prop_dedup();
//		$this->devices = prop_devices();
//		$this->exec = prop_exec();
//		$this->jailed = prop_jailed();
//		$this->logbias = prop_logbias();
//		$this->nmband = prop_nbmand();
//		$this->normalization = prop_normalization();
		$this->primarycache = prop_primarycache();
//		$this->readonly = prop_readonly();
//		$this->redundant_metadata = prop_redundant_metadata();
		$this->secondarycache = prop_secondarycache();
//		$this->setuid = prop_setuid();
//		$this->sharesmb = prop_sharesmb();
//		$this->sharenfs = prop_sharenfs();
//		$this->snapdir = prop_snapdir();
		$this->sync = prop_sync();
		$this->type = prop_type();
//		$this->utf8only = prop_utf8only();
		$this->volblocksize = prop_volblocksize();
		$this->volmode = prop_volmode();
		$this->volsize = prop_volsize();
//		$this->nmband = prop_nmband();
//		$this->xattr = prop_xattr();
		return $this;
	}
}
/*
function prop_aclinherit(): properties_list {
	$o = new properties_list();
	$o->name('aclinherit');
	$o->title(gtext('ACL Inherit'));
	$o->description(gtext('This attribute determines the behavior of Access Control List inheritance.'));
	$o->type('list');
	$o->defaultvalue('restricted');
	$options = [
		'discard' => gtext('Discard - Do not inherit entries'),
		'noallow' => gtext('Noallow - Only inherit deny entries'),
		'restricted' => gtext('Restricted - Inherit all but "write ACL" and "change owner"'),
		'passthrough' => gtext('Passthrough - Inherit all entries'),
//		'secure' => gtext('Same as "Restricted" - kept for compatibility.'),
		'passthrough-x' => gtext('Passthrough-X - Inherit all but "execute" when not specified')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
/*
function prop_aclmode(): properties_list {
	$o = new properties_list();
	$o->name('aclmode');
	$o->title(gtext('ACL Mode'));
	$o->description(gtext('This attribute controls the ACL behavior when a file is created or whenever the mode of a file or a directory is modified.'));
	$o->type('list');
	$o->defaultvalue('discard');
	$options = [
		'discard' => gtext('Discard - Discard ACL'),
		'groupmask' => gtext('Groupmask - Mask ACL with mode'),
		'passthrough' => gtext('Passthrough - Do not change ACL'),
		'restricted' => gtext('Restricted')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
/*
function prop_atime(): properties_list {
	return prop_onoff('atime',gtext('Access Time'),gtext('Controls whether the access time for files is updated when they are read.'));
}
 */
/*
function prop_canmount(): properties_list {
	$o = new properties_list();
	$o->name('canmount');
	$o->title(gtext('Can Mount'));
	$o->description(gtext(''));
	$o->type('list');
	$o->defaultvalue('on');
	$options = [
		'on' => gtext('On'),
		'off' => gtext('Off'),
		'noauto' => gtext('Noauto'),
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
/*
function prop_casesensitivity(): properties_list {
	$o = new properties_list();
	$o->name('casesensitivity');
	$o->title(gtext('Casesensitivity'));
	$o->description(gtext(''));
	$o->type('list');
	$o->defaultvalue('sensitive');
	$options = [
		'sensitive' => gtext('Sensitive'),
		'insensitive' => gtext('Insensitive'),
		'mixed' => gtext('Mixed'),
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(false);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
/*
function prop_checksum(): properties_list {
	$o = new properties_list();
	$o->name('checksum');
	$o->title(gtext('Checksum'));
	$o->description(gtext('Defines the checksum algorithm.'));
	$o->type('list');
	$o->defaultvalue('on');
	$options = [
		'on' => gtext('On'),
		'off' => gtext('Off'),
		'fletcher2' => 'fletcher2',
		'fletcher4' => 'fletcher4',
		'sha256' => 'sha256',
		'noparity' => 'noparity',
		'sha512' => 'sha512',
		'skein' => 'skein',
//		'edonr' => 'Edon-R',
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
function prop_compression(): properties_list {
	$o = new properties_list();
	$o->name('compression');
	$o->title(gtext('Compression'));
	$o->description(gtext("Controls the compression algorithm. 'LZ4' is now the recommended compression algorithm. Setting compression to 'On' uses the LZ4 compression algorithm if the feature flag lz4_compress is active, otherwise LZJB is used. You can specify the 'GZIP' level by using the value 'GZIP-N', where N is an integer from 1 (fastest) to 9 (best compression ratio). Currently, 'GZIP' is equivalent to 'GZIP-6'."));
	$o->type('list');
	$o->defaultvalue('on');
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
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
/*
function prop_copies(): properties_list {
	$o = new properties_list();
	$o->name('copies');
	$o->title(gtext('Copies'));
	$o->description(gtext(''));
	$o->type('list');
	$o->defaultvalue('1');
	$options = [
		'1' => gtext('1'),
		'2' => gtext('2'),
		'3' => getxt('3')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
/*
function prop_devices(): properties_list {
	return prop_onoff('devices',gtext('Devices'),gtext('The devices property is currently not supported on FreeBSD.'));
}
 */
/*
function prop_exec(): properties_list {
	return prop_onoff('exec',gtext('Exec'),gtext('Controls whether processes can be executed from within this file system.'));
}
 */
/*
function prop_logbias(): properties_list {
	$o = new properties_list();
	$o->name('logbias');
	$o->title(gtext('Logbias'));
	$o->description(gtext('throughoutput'));
	$o->type('list');
	$o->defaultvalue('1');
	$options = [
		'latency' => gtext('Latency'),
		'throughoutput' => gtext('Throughoutput')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
function prop_dedup(): properties_list {
	$o = new properties_list();
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
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
/*
function prop_jailed(): properties_list {
	return prop_offon('jailed',gtext('Jailed'),gtext('Controls whether the dataset is managed from a jail.'));
}
 */
/*
function prop_nbmand(): properties_list {
	return prop_offon('nbmand',gtext('NBMAND'),gtext('The nbmand property is currently not supported on FreeBSD.'));
}
 */
/*
function prop_normalization(): properties_list {
	$o = new properties_list();
	$o->name('normalization');
	$o->title(gtext('Normalization'));
	$description = gtext('Indicates whether the file system should perform a unicode normalization of file names whenever two file names are compared, and which normalization algorithm should	be used.');
	$o->description($description);
	$o->type('list');
	$o->defaultvalue('none');
	$options = [
		'none' => gtext('None'),
		'formC' => 'formC',
		'formD' => 'formD',
		'formKC' => 'formKC',
		'formKD' => 'formKD',
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
*/
function prop_offon(string $name,string $title,string $description,bool $editableonadd = true,bool $editableonmodify = true): properties_list {
	$o = new properties_list();
	$o->name($name);
	$o->title($title);
	$o->description($description);
	$o->type('list');
	$o->defaultvalue('off');
	$options = [
		'on' => gtext('On'),
		'off' => gtext('Off'),
	];
	$o->options($options);
	$o->editableonadd($editableonadd);
	$o->editableonmodify($editableonmodify);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_onoff(string $name,string $title,string $description,bool $editableonadd = true,bool $editableonmodify = true): properties_list {
	$o = new properties_list();
	$o->name($name);
	$o->title($title);
	$o->description($description);
	$o->type('list');
	$o->defaultvalue('on');
	$options = [
		'on' => gtext('On'),
		'off' => gtext('Off'),
	];
	$o->options($options);
	$o->editableonadd($editableonadd);
	$o->editableonmodify($editableonmodify);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_primarycache(): properties_list {
	$o = new properties_list();
	$o->name('primarycache');
	$o->title(gtext('Primary Cache'));
	$o->description(gtext('Controls what is cached in the primary cache (ARC).'));
	$o->type('list');
	$o->defaultvalue('all');
	$options = [
		'all' => gtext('Both user data and metadata will be cached in ARC.'),
		'metadata' => gtext('Only metadata will be cached in ARC.'),
		'none' => gtext('Neither user data nor metadata will be cached in ARC.')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
/*
function prop_readonly(): properties_list {
	return prop_offon('readonly',gtext('Read Only'),gtext('Controls whether this dataset can be modified.'));
}
 */
/*
function prop_redundant_metadata(): properties_list {
	$o = new properties_list();
	$o->name('redundant_metadata');
	$o->title(gtext('Redundant Metadata'));
	$o->description(gtext('Controls what types of metadata are stored redundantly.'));
	$o->type('list');
	$o->defaultvalue('all');
	$options = [
		'all' => gtext('All'),
		'most' => gtext('Most')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
function prop_secondarycache(): properties_list {
	$o = new properties_list();
	$o->name('secondarycache');
	$o->title(gtext('Secondary Cache'));
	$o->description(gtext('Controls what is cached in the secondary cache (L2ARC).'));
	$o->type('list');
	$o->defaultvalue('all');
	$options = [
		'all' => gtext('Both user data and metadata will be cached in L2ARC.'),
		'metadata' => gtext('Only metadata will be cached in L2ARC.'),
		'none' => gtext('Neither user data nor metadata will be cached in L2ARC.')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
/*
function prop_setuid(): properties_list {
	return prop_onoff('setuid',gtext('Set UID'),gtext('Controls whether the set-UID bit is respected for the file system.'));
}
 */
/*
function prop_sharesmb(): properties_list {
	return prop_onoff('sharesmb',gtext('Share SMB'),gtext('The sharesmb property currently has no effect on FreeBSD.'));
}
 */
/*
function prop_sharenfs(): properties_list {
	return prop_onoff('sharenfs',gtext('Share NFS'),gtext('Controls whether the file system is shared via NFS.'));
}
 */
/*
function prop_snapdir(): properties_list {
	$o = new properties_list();
	$options = [
		'hidden' => gtext('Hidden'),
		'visible' => gtext('Visible'),
	];
	$o->name('snapdir');
	$o->title(gtext('Snapdir'));
	$o->description(gtext(''));
	$o->type('list');
	$o->defaultvalue('hidden');
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
 */
function prop_sync(): properties_list {
	$o = new properties_list();
	$o->name('sync');
	$o->title(gtext('Sync'));
	$o->description(gtext('Controls the behavior of synchronous requests.'));
	$o->type('list');
	$o->defaultvalue('standard');
	$options = [
		'standard' => gtext('Standard'),
		'always' => gtext('Always'),
		'disabled' => gtext('Disabled')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_type(): properties_list {
	$o = new properties_list();
	$o->name('type');
	$o->title(gtext('Dataset Type'));
	$o->description(gtext(''));
	$o->type('list');
	$o->defaultvalue('filesystem');
	$options = [
		'filesystem' => gtext('File System - can be mounted within the standard system namespace and behaves like other file systems.'),
		'volume' => gtext('Volume - A logical volume. Can be exported as a raw or block device.')
//		'snapshot' => gtext('Snapshot - A read-only version of a file system or volume at a given point in time.')
//		'bookmark' => gtext('Bookmark - Creates a bookmark of a given snapshot.')
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(false);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
/*
function prop_utf8only(): properties_list {
	return prop_offon('utf8only',gtext('UTF-8 Only'),gtext('Indicates whether the file system should reject file names that include characters that are not present in the UTF-8 character code set.'),true,false);
}
 */
function prop_volblocksize(): properties_list {
	$o = new properties_list();
	$o->name('volblocksize');
	$o->title(gtext('Block Size'));
	$o->description(gtext('ZFS volume block size. This value can not be changed after creation.'));
	$o->type('list');
	$o->defaultvalue('8K');
	$options = [
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
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(false);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_volmode(): properties_list {
	$o = new properties_list();
	$o->name('volmode');
	$o->title(gtext('Volume Mode'));
	$o->description(gtext('Specifies how the volume should be exposed to the OS.'));
	$o->type('list');
	$o->defaultvalue('default');
	$options = [
		'default' => gtext('Default'),
		'geom' => 'geom',
		'dev' => 'dev',
		'none' => 'none'
	];
	$o->options($options);
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => sprintf('/^(%s)$/',implode('|',array_keys($options)))]]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
function prop_volsize(): properties_base {
	$o = new properties_base();
	$o->name('volsize');
	$o->title(gtext('Volume Size'));
	$o->description(gtext('ZFS volume size. You can use human-readable suffixes like K, KB, M, GB.'));
	$o->type('string');
	$o->defaultvalue('');
	$o->editableonadd(true);
	$o->editableonmodify(true);
	$o->filter(FILTER_VALIDATE_REGEXP);
	$o->filteroptions(['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => '/^[1-9][\d]*[kmgtpezy]?[b]?$/i']]);
	$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
	return $o;
}
/*
function prop_xattr(): properties_list {
	return prop_offon('xattr',gtext('Xattr'),gtext('The xattr property is currently not supported on FreeBSD.'));
}
 */
/*
function prop_vscan(): properties_list {
	return prop_offon('vscan',gtext('Vscan'),gtext('The xattr property is currently not supported on FreeBSD.'));
}
 */
