<?php
/*
	properties_zfs_dataset.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2018 The NAS4Free Project <info@nas4free.org>.
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
require_once 'properties.php';
/*
 * To activate a property:
 * - Enable property variable.
 * - Enable init call in method load.
 * - Enable property method.
 */
class properties_zfs_dataset {
	public $aclinherit;
	public $aclmode;
	public $atime;
	public $canmount;
	public $casesensitivity;
	public $checksum;
	public $compression;
	public $copies;
	public $dedup;
//	public $devices;
	public $exec;
	public $jailed;
	public $logbias;
//	public $name;
//	public $nbmand;
	public $normalization;
	public $primarycache;
	public $quota;
	public $readonly;
	public $redundant_metadata;
	public $refquota;
	public $refreservation;
	public $reservation;
	public $secondarycache;
	public $setuid;
//	public $sharesmb;
//	public $sharenfs;
	public $snapdir;
	public $sync;
	public $type;
	public $utf8only;
	public $volmode;
	public $volblocksize;
	public $volsize;
//	public $vscan;
//	public $xattr;

	const REGEXP_SIZE = '/^(0*[1-9][\d]*(\.\d*)?|0*\.0*[1-9]\d*)[kmgtpezy]?[b]?$/i';
	const REGEXP_SIZEORNONE = '/^((0*[1-9]\d*(\.\d*)?|0*\.0*[1-9]\d*)[kmgtpezy]?b?|none)$/i';
	const REGEXP_SIZEORNONEORNOTHING = '/^((0*[1-9][\d]*(\.\d*)?|0*\.0*[1-9]\d*)[kmgtpezy]?b?|none|^$)$/i';	

	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->aclinherit = $this->prop_aclinherit();
		$this->aclmode = $this->prop_aclmode();
		$this->atime = $this->prop_atime();
		$this->canmount = $this->prop_canmount();
		$this->casesensitivity = $this->prop_casesensitivity();
		$this->checksum = $this->prop_checksum();
		$this->compression = $this->prop_compression();
		$this->copies = $this->prop_copies();
		$this->dedup = $this->prop_dedup();
//		$this->devices = $this->prop_devices();
		$this->exec = $this->prop_exec();
		$this->jailed = $this->prop_jailed();
		$this->logbias = $this->prop_logbias();
//		$this->name = $this->prop_name();
//		$this->nbmand = $this->prop_nbmand();
		$this->normalization = $this->prop_normalization();
		$this->primarycache = $this->prop_primarycache();
		$this->quota = $this->prop_quota();
		$this->readonly = $this->prop_readonly();
		$this->redundant_metadata = $this->prop_redundant_metadata();
		$this->refquota = $this->prop_refquota();
		$this->refreservation = $this->prop_refreservation();
		$this->reservation = $this->prop_reservation();
		$this->secondarycache = $this->prop_secondarycache();
		$this->setuid = $this->prop_setuid();
//		$this->sharesmb = $this->prop_sharesmb();
//		$this->sharenfs = $this->prop_sharenfs();
		$this->snapdir = $this->prop_snapdir();
		$this->sync = $this->prop_sync();
		$this->type = $this->prop_type();
		$this->utf8only = $this->prop_utf8only();
		$this->volblocksize = $this->prop_volblocksize();
		$this->volmode = $this->prop_volmode();
		$this->volsize = $this->prop_volsize();
//		$this->vscan = $this->prop_vscan();
//		$this->xattr = $this->prop_xattr();
		return $this;
	}
	private function prop_aclinherit(): properties_list {
		$o = new properties_list($this);
		$o->set_id('aclinherit');
		$o->set_name('aclinherit');
		$o->set_title(gtext('ACL Inherit'));
		$o->set_description(gtext('This attribute determines the behavior of Access Control List inheritance.'));
		$o->set_defaultvalue('restricted');
		$options = [
			'discard' => gtext('Discard - Do not inherit entries'),
			'noallow' => gtext('Noallow - Only inherit deny entries'),
			'restricted' => gtext('Restricted - Inherit all but "write ACL" and "change owner"'),
			'passthrough' => gtext('Passthrough - Inherit all entries'),
//			'secure' => gtext('Same as "Restricted" - kept for compatibility.'),
			'passthrough-x' => gtext('Passthrough-X - Inherit all but "execute" when not specified')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_aclmode(): properties_list {
		$o = new properties_list($this);
		$o->set_id('aclmode');
		$o->set_name('aclmode');
		$o->set_title(gtext('ACL Mode'));
		$o->set_description(gtext('This attribute controls the ACL behavior when a file is created or whenever the mode of a file or a directory is modified.'));
		$o->set_defaultvalue('discard');
		$options = [
			'discard' => gtext('Discard - Discard ACL'),
			'groupmask' => gtext('Groupmask - Mask ACL with mode'),
			'passthrough' => gtext('Passthrough - Do not change ACL'),
			'restricted' => gtext('Restricted')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_atime(): properties_list {
		return $this->prop_onoff('atime',gtext('Access Time'),gtext('Controls whether the access time for files is updated when they are read.'),true,true);
	}
	private function prop_canmount(): properties_list {
		$o = new properties_list($this);
		$o->set_id('canmount');
		$o->set_name('canmount');
		$o->set_title(gtext('Can Mount'));
		$o->set_description(gtext('If this property is set to off, the file system cannot be mounted.'));
		$o->set_defaultvalue('on');
		$options = [
			'on' => gtext('On'),
			'off' => gtext('Off'),
			'noauto' => gtext('Noauto'),
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_casesensitivity(): properties_list {
		$o = new properties_list($this);
		$o->set_id('casesensitivity');
		$o->set_name('casesensitivity');
		$o->set_title(gtext('Case Sensitivity'));
		$o->set_description(gtext('Indicates whether the file name matching algorithm used by the filesystem should be case-sensitive, case-insensitive, or allow a combination of both styles of matching.'));
		$o->set_defaultvalue('sensitive');
		$options = [
			'sensitive' => gtext('Sensitive'),
			'insensitive' => gtext('Insensitive'),
			'mixed' => gtext('Mixed'),
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(false);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_checksum(): properties_list {
		$o = new properties_list($this);
		$o->set_id('checksum');
		$o->set_name('checksum');
		$o->set_title(gtext('Checksum'));
		$o->set_description(gtext('Defines the checksum algorithm.'));
		$o->set_defaultvalue('on');
		$options = [
			'on' => gtext('On'),
			'off' => gtext('Off'),
			'fletcher2' => 'Fletcher 2',
			'fletcher4' => 'Fletcher 4',
			'sha256' => 'SHA-256',
			'noparity' => gtext('No Parity'),
			'sha512' => 'SHA-512',
			'skein' => 'Skein',
//			'edonr' => 'Edon-R',
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_compression(): properties_list {
		$o = new properties_list($this);
		$o->set_id('compression');
		$o->set_name('compression');
		$o->set_title(gtext('Compression'));
		$o->set_description(gtext("Controls the compression algorithm. 'LZ4' is now the recommended compression algorithm. Setting compression to 'On' uses the LZ4 compression algorithm if the feature flag lz4_compress is active, otherwise LZJB is used. You can specify the 'GZIP' level by using the value 'GZIP-N', where N is an integer from 1 (fastest) to 9 (best compression ratio). Currently, 'GZIP' is equivalent to 'GZIP-6'."));
		$o->set_defaultvalue('on');
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
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_copies(): properties_list {
		$o = new properties_list($this);
		$o->set_id('copies');
		$o->set_name('copies');
		$o->set_title(gtext('Copies'));
		$o->set_description(gtext('Controls the number of copies of data stored for this dataset.'));
		$o->set_defaultvalue('1');
		$options = [
			'1' => gtext('1'),
			'2' => gtext('2'),
			'3' => gtext('3')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
/*
	private function prop_devices(): properties_list {
		return $this->prop_onoff('devices',gtext('Devices'),gtext('The devices property is currently not supported on FreeBSD.'),true,true);
	}
 */
	private function prop_dedup(): properties_list {
		$o = new properties_list($this);
		$o->set_id('dedup');
		$o->set_name('dedup');
		$o->set_title(gtext('Dedup Method'));
		$description = '<div>' . gtext('Controls the dedup method.') . '</div>'
			. '<div><b>'
			. '<font color="red">' . gtext('WARNING') . '</font>' . ': '
			. '<a href="https://wiki.nas4free.org/doku.php?id=documentation:setup_and_user_guide:disks_zfs_datasets_dataset" target="_blank">'
			. gtext('See ZFS datasets & deduplication wiki article BEFORE using this feature.')
			. '</a>'
			. '</b></div>';
		$o->set_description($description);
		$o->set_defaultvalue('off');
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
//			'edonr,verify' => gtext('Edon-R, Verify')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_exec(): properties_list {
		return $this->prop_onoff('exec',gtext('Exec'),gtext('Controls whether processes can be executed from within this file system.'),true,true);
	}
	private function prop_jailed(): properties_list {
		return $this->prop_offon('jailed',gtext('Jailed'),gtext('Controls whether the dataset is managed from a jail.'),true,true);
	}
	private function prop_logbias(): properties_list {
		$o = new properties_list($this);
		$o->set_id('logbias');
		$o->set_name('logbias');
		$o->set_title(gtext('Logbias'));
		$o->set_description(gtext('Provide a hint to ZFS about handling of synchronous requests in this dataset.'));
		$o->set_defaultvalue('latency');
		$options = [
			'latency' => gtext('Latency'),
			'throughput' => gtext('Throughput')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
/*
	private function prop_name(): properties {
		$o = new properties($this);
		$o->id('name');
		$o->name('name');
		$o->title(gtext('Name'));
		$o->description(gtext('The name of the dataset.'));
		$o->defaultvalue('');
		$o->editableonadd(true);
		$o->editableonmodify(false);
		$o->ui_filter(FILTER_VALIDATE_REGEXP);
		$regexp = sprintf('/^[a-z\d][a-z\d%1$s]*(?:\/[a-z\d][a-z\d%1$s]*)*$/i',preg_quote('.:-_','/'));
		$o->ui_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->ui_filter_options(['default' => NULL,'regexp' => $regexp]);
		$o->errormessage(sprintf('%s: %s',$o->title(),gtext('The value is invalid.')));
		return $o;
	}
 */
/*
	private function prop_nbmand(): properties_list {
		return $this->prop_offon('nbmand',gtext('NBMAND'),gtext('The nbmand property is currently not supported on FreeBSD.'),true,true);
	}
 */
	private function prop_normalization(): properties_list {
		$o = new properties_list($this);
		$o->set_id('normalization');
		$o->set_name('normalization');
		$o->set_title(gtext('Normalization'));
		$o->set_description(gtext('Indicates whether the file system should perform a unicode normalization of file names whenever two file names are compared, and which normalization algorithm should be used.'));
		$o->set_defaultvalue('none');
		$options = [
			'none' => gtext('None'),
			'formC' => 'formC',
			'formD' => 'formD',
			'formKC' => 'formKC',
			'formKD' => 'formKD',
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(false);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_offon(string $name,string $title,string $description,bool $editableonadd = true,bool $editableonmodify = true): properties_list {
		$o = new properties_list($this);
		$o->set_id($name);
		$o->set_name($name);
		$o->set_title($title);
		$o->set_description($description);
		$o->set_defaultvalue('off');
		$options = [
			'on' => gtext('On'),
			'off' => gtext('Off'),
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd($editableonadd);
		$o->set_editableonmodify($editableonmodify);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_onoff(string $name,string $title,string $description,bool $editableonadd = true,bool $editableonmodify = true): properties_list {
		$o = new properties_list($this);
		$o->set_id($name);
		$o->set_name($name);
		$o->set_title($title);
		$o->set_description($description);
		$o->set_defaultvalue('on');
		$options = [
			'on' => gtext('On'),
			'off' => gtext('Off'),
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd($editableonadd);
		$o->set_editableonmodify($editableonmodify);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_primarycache(): properties_list {
		$o = new properties_list($this);
		$o->set_id('primarycache');
		$o->set_name('primarycache');
		$o->set_title(gtext('Primary Cache'));
		$o->set_description(gtext('Controls what is cached in the primary cache (ARC).'));
		$o->set_defaultvalue('all');
		$options = [
			'all' => gtext('Both user data and metadata will be cached in ARC.'),
			'metadata' => gtext('Only metadata will be cached in ARC.'),
			'none' => gtext('Neither user data nor metadata will be cached in ARC.')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_quota(): properties_text {
		$o = new properties_text($this);
		$o->set_id('quota');
		$o->set_name('quota');
		$o->set_title(gtext('Quota'));
		$o->set_description(gtext('Limits the amount of space a dataset and its descendents can consume.'));
		$o->set_defaultvalue('');
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_filter(FILTER_VALIDATE_REGEXP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'regexp' => $this::REGEXP_SIZEORNONEORNOTHING]);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_readonly(): properties_list {
		return $this->prop_offon('readonly',gtext('Read Only'),gtext('Controls whether this dataset can be modified.'),true,true);
	}
	private function prop_redundant_metadata(): properties_list {
		$o = new properties_list($this);
		$o->set_id('redundant_metadata');
		$o->set_name('redundant_metadata');
		$o->set_title(gtext('Redundant Metadata'));
		$o->set_description(gtext('Controls what types of metadata are stored redundantly.'));
		$o->set_defaultvalue('all');
		$options = [
			'all' => gtext('All'),
			'most' => gtext('Most')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_refquota(): properties_text {
		$o = new properties_text($this);
		$o->set_id('refquota');
		$o->set_name('refquota');
		$o->set_title(gtext('Refquota'));
		$o->set_description(gtext('Limits the amount of space a dataset can consume.'));
		$o->set_defaultvalue('');
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_filter(FILTER_VALIDATE_REGEXP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'regexp' => $this::REGEXP_SIZEORNONEORNOTHING]);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_refreservation(): properties_text {
		$o = new properties_text($this);
		$o->set_id('refreservation');
		$o->set_name('refreservation');
		$o->set_title(gtext('Refreservation'));
		$o->set_description(gtext('The minimum amount of space guaranteed to a dataset, not including its descendents.'));
		$o->set_defaultvalue('');
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_filter(FILTER_VALIDATE_REGEXP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'regexp' => $this::REGEXP_SIZEORNONEORNOTHING]);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_reservation(): properties_text {
		$o = new properties_text($this);
		$o->set_id('reservation');
		$o->set_name('reservation');
		$o->set_title(gtext('Reservation'));
		$o->set_description(gtext('The minimum amount of space guaranteed to a dataset and its descendents.'));
		$o->set_defaultvalue('');
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_filter(FILTER_VALIDATE_REGEXP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'regexp' => $this::REGEXP_SIZEORNONEORNOTHING]);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_secondarycache(): properties_list {
		$o = new properties_list($this);
		$o->set_id('secondarycache');
		$o->set_name('secondarycache');
		$o->set_title(gtext('Secondary Cache'));
		$o->set_description(gtext('Controls what is cached in the secondary cache (L2ARC).'));
		$o->set_defaultvalue('all');
		$options = [
			'all' => gtext('Both user data and metadata will be cached in L2ARC.'),
			'metadata' => gtext('Only metadata will be cached in L2ARC.'),
			'none' => gtext('Neither user data nor metadata will be cached in L2ARC.')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_setuid(): properties_list {
		return $this->prop_onoff('setuid',gtext('Set UID'),gtext('Controls whether the set-UID bit is respected for the file system.'),true,true);
	}
/*
	private function prop_sharesmb(): properties_list {
		return $this->prop_onoff('sharesmb',gtext('Share SMB'),gtext('The sharesmb property currently has no effect on FreeBSD.'),true,true);
	}
 */
/*
	private function prop_sharenfs(): properties_list {
		return $this->prop_onoff('sharenfs',gtext('Share NFS'),gtext('Controls whether the file system is shared via NFS.'),true,true);
	}
 */
	private function prop_snapdir(): properties_list {
		$o = new properties_list($this);
		$options = [
			'hidden' => gtext('Hidden'),
			'visible' => gtext('Visible'),
		];
		$o->set_id('snapdir');
		$o->set_name('snapdir');
		$o->set_title(gtext('Snapdir'));
		$o->set_description(gtext('Controls whether the .zfs directory is hidden or visible in the root of the file system.'));
		$o->set_defaultvalue('hidden');
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_sync(): properties_list {
		$o = new properties_list($this);
		$o->set_id('sync');
		$o->set_name('sync');
		$o->set_title(gtext('Sync'));
		$o->set_description(gtext('Controls the behavior of synchronous requests.'));
		$o->set_defaultvalue('standard');
		$options = [
			'standard' => gtext('Standard'),
			'always' => gtext('Always'),
			'disabled' => gtext('Disabled')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_type(): properties_list {
		$o = new properties_list($this);
		$o->set_id('type');
		$o->set_name('type');
		$o->set_title(gtext('Dataset Type'));
		$o->set_description(gtext('Controls the type of the ZFS dataset.'));
		$o->set_defaultvalue('filesystem');
		$options = [
			'filesystem' => gtext('File System - can be mounted within the standard system namespace and behaves like other file systems.'),
			'volume' => gtext('Volume - A logical volume. Can be exported as a raw or block device.')
//			'snapshot' => gtext('Snapshot - A read-only version of a file system or volume at a given point in time.')
//			'bookmark' => gtext('Bookmark - Creates a bookmark of a given snapshot.')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(false);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_utf8only(): properties_list {
		return $this->prop_offon('utf8only',gtext('UTF-8 Only'),gtext('Indicates whether the file system should reject file names that include characters that are not present in the UTF-8 character code set.'),true,false);
	}
	private function prop_volblocksize(): properties_list {
		$o = new properties_list($this);
		$o->set_id('volblocksize');
		$o->set_name('volblocksize');
		$o->set_title(gtext('Block Size'));
		$o->set_description(gtext('ZFS volume block size. This value can not be changed after creation.'));
		$o->set_defaultvalue('8K');
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
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(false);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_volmode(): properties_list {
		$o = new properties_list($this);
		$o->set_id('volmode');
		$o->set_name('volmode');
		$o->set_title(gtext('Volume Mode'));
		$o->set_description(gtext('Specifies how the volume should be exposed to the OS.'));
		$o->set_defaultvalue('default');
		$options = [
			'default' => gtext('Default'),
			'geom' => 'geom',
			'dev' => 'dev',
			'none' => 'none'
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_volsize(): properties_text {
		$o = new properties_text($this);
		$o->set_id('volsize');
		$o->set_name('volsize');
		$o->set_title(gtext('Volume Size'));
		$o->set_description(gtext('ZFS volume size. You can use human-readable suffixes like K, KB, M, GB.'));
		$o->set_defaultvalue('');
		$o->set_size(20);
		$o->set_maxlength(20);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_filter(FILTER_VALIDATE_REGEXP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'regexp' => $this::REGEXP_SIZE]);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
/*
	private function prop_vscan(): properties_list {
		return $this->prop_offon('vscan',gtext('Vscan'),gtext('The vscan property is currently not supported on FreeBSD.'),true,true);
	}
 */
/*
	private function prop_xattr(): properties_list {
		return $this->prop_offon('xattr',gtext('Xattr'),gtext('The xattr property is currently not supported on FreeBSD.'),true,true);
	}
 */
}
class properties_zfs_dataset_enhanced extends properties_zfs_dataset {
	public $supported_properties = [];
}
class properties_zfs_filesystem extends properties_zfs_dataset_enhanced {
	public function load() {
		parent::load();
		$this->supported_properties = [
			'aclinherit',
			'aclmode',
			'atime',
			'canmount',
			'casesensitivity',
			'checksum',
			'compression',
			'copies',
			'dedup',
			'logbias',
			'normalization',
			'primarycache',
			'quota',
			'readonly',
			'redundant_metadata',
			'refquota',
			'refreservation',
			'reservation',
			'secondarycache',
			'setuid',
			'snapdir',
			'sync',
			'type',
			'utf8only'
		];
	}
}
class properties_zfs_volume extends properties_zfs_dataset_enhanced {
	public function load() {
		parent::load();
		$this->supported_properties = [
			'checksum',
			'compression',
			'dedup',
			'logbias',
			'primarycache',
			'secondarycache',
			'sparse',
			'sync',
			'volblocksize',
			'volmode',
			'volsize'
		];
	}
}
