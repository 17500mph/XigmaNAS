<?php
/*
	properties_disks_zfs_volume.php

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

class properties_disks_zfs_volume extends co_property_container {
	protected $x_uuid;
	protected $x_enabled;
	protected $x_protected;
	protected $x_toolbox;

	protected $x_available;
	protected $x_checksum;
	protected $x_compression;
	protected $x_copies;
	protected $x_dedup;
	protected $x_description;
	protected $x_logbias;
	protected $x_name;
	protected $x_pool;
	protected $x_primarycache;
	protected $x_readonly;
	protected $x_redundant_metadata;
	protected $x_refreservation;
	protected $x_reservation;
	protected $x_secondarycache;
	protected $x_sparse;
	protected $x_used;
	protected $x_volblocksize;
	protected $x_volmode;
	protected $x_volsize;

	public function get_uuid() {
		return $this->x_uuid ?? $this->init_uuid();
	}
	public function init_uuid() {
		$this->x_uuid = new property_uuid($this);
		return $this->x_uuid;
	}
		public function get_enabled() {
		return $this->x_enabled ?? $this->init_enabled();
	}
	public function init_enabled() {
		$this->x_enabled = new properties_bool($this);
		$this->x_enabled->
			set_title(gtext('Enabled'))->
			set_name('enabled');
		return $this->x_enabled;
	}
	public function get_protected() {
		return $this->x_protected ?? $this->init_protected();
	}
	public function init_protected() {
		$this->x_protected = new properties_bool($this);
		$this->x_protected->
			set_title(gtext('Protected'))->
			set_name('protected');
		return $this->x_protected;
	}
	public function get_toolbox() {
		return $this->x_toolbox ?? $this->init_toolbox();
	}
	public function init_toolbox() {
		$property = $this->x_toolbox = new property_toolbox($this);
		return $property;
	}
	public function get_available() {
		return $this->x_available ?? $this->init_available();
	}
	public function init_available() {
		$this->x_available = new properties_text($this);
		$this->x_available->
			set_title(gtext('Available'))->
			set_name('avail');
		return $this->x_available;
	}
	public function get_checksum() {
		return $this->x_checksum ?? $this->init_checksum();
	}
	public function init_checksum() {
		$this->x_checksum = new properties_list($this);
		$this->x_checksum->
			set_title(gtext('Checksum'))->
			set_name('checksum');
		return $this->x_checksum;
	}
	public function get_compression() {
		return $this->x_compression ?? $this->init_compression();
	}
	public function init_compression() {
		$this->x_compression = new properties_list($this);
		$this->x_compression->
			set_title(gtext('Compression'))->
			set_name('compression');
		return $this->x_compression;
	}
	public function get_copies() {
		return $this->x_copies ?? $this->init_copies();
	}
	public function init_copies() {
		$this->x_copies = new properties_list($this);
		$this->x_copies->
			set_title(gtext('Copies'))->
			set_name('copies');
		return $this->x_copies;
	}
	public function get_dedup() {
		return $this->x_dedup ?? $this->init_dedup();
	}
	public function init_dedup() {
		$this->x_dedup = new properties_list($this);
		$this->x_dedup->
			set_title(gtext('Dedup Method'))->
			set_name('dedup');
		return $this->x_dedup;
	}
	public function get_description() {
		return $this->x_description ?? $this->init_description();
	}
	public function init_description() {
		$this->x_description = new properties_text($this);
		$this->x_description->
			set_title(gtext('Description'))->
			set_name('desc');
		return $this->x_description;
	}
	public function get_logbias() {
		return $this->x_logbias ?? $this->init_logbias();
	}
	public function init_logbias() {
		$this->x_logbias = new properties_list($this);
		$this->x_logbias->
			set_title(gtext('Logbias'))->
			set_name('logbias');
		return $this->x_logbias;
	}
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$this->x_name = new properties_text($this);
		$this->x_name->
			set_title(gtext('Name'))->
			set_name('name');
		return $this->x_name;
	}
	public function get_pool() {
		return $this->x_pool ?? $this->init_pool();
	}
	public function init_pool() {
		$this->x_pool = new properties_text($this);
		$this->x_pool->
			set_title(gtext('Pool'))->
			set_name('pool');
		return $this->x_pool;
	}
	public function get_primarycache() {
		return $this->x_primarycache ?? $this->init_primarycache();
	}
	public function init_primarycache() {
		$this->x_primarycache = new properties_list($this);
		$this->x_primarycache->
			set_title(gtext('Primary Cache'))->
			set_name('primarycache');
		return $this->x_primarycache;
	}
	public function get_readonly() {
		return $this->x_readonly ?? $this->init_readonly();
	}
	public function init_readonly() {
		$this->x_readonly = new properties_list($this);
		$this->x_readonly->
			set_title(gtext('Read Only'))->
			set_name('readonly');
		return $this->x_readonly;
	}
	public function get_redundant_metadata() {
		return $this->x_redundant_metadata ?? $this->init_redundant_metadata();
	}
	public function init_redundant_metadata() {
		$this->x_redundant_metadata = new properties_list($this);
		$this->x_redundant_metadata->
			set_title(gtext('Redundant Metadata'))->
			set_name('redundant_metadata');
		return $this->x_redundant_metadata;
	}
	public function get_refreservation() {
		return $this->x_refreservation ?? $this->init_refreservation();
	}
	public function init_refreservation() {
		$this->x_refreservation = new properties_text($this);
		$this->x_refreservation->
			set_title(gtext('Refreservation'))->
			set_name('refreservation');
		return $this->x_refreservation;
	}
	public function get_reservation() {
		return $this->x_reservation ?? $this->init_reservation();
	}
	public function init_reservation() {
		$this->x_reservation = new properties_text($this);
		$this->x_reservation->
			set_title(gtext('Reservation'))->
			set_name('reservation');
		return $this->x_reservation;
	}
	public function get_secondarycache() {
		return $this->x_secondarycache ?? $this->init_secondarycache();
	}
	public function init_secondarycache() {
		$this->x_secondarycache = new properties_list($this);
		$this->x_secondarycache->
			set_title(gtext('Secondary Cache'))->
			set_name('secondarycache');
		return $this->x_secondarycache;
	}
	public function get_sparse() {
		return $this->x_sparse ?? $this->init_sparse();
	}
	public function init_sparse() {
		$this->x_sparse = new properties_bool();
		$this->x_sparse->
			set_title(gtext('Sparse'))->
			set_name('sparse');
		return $this->x_sparse;
	}
	public function get_sync() {
		return $this->x_sync ?? $this->init_sync();
	}
	public function init_sync() {
		$this->x_sync = new properties_list($this);
		$this->x_sync->
			set_title(gtext('Sync'))->
			set_name('sync');
		return $this->x_sync;
	}
	public function get_type() {
		return $this->x_type ?? $this->init_type();
	}
	public function init_type() {
		$this->x_type = new properties_list($this);
		$this->x_type->
			set_title(gtext('Dataset Type'))->
			set_name('type');
		return $this->x_type;
	}
	public function get_used() {
		return $this->x_used ?? $this->init_used();
	}
	public function init_used() {
		$this->x_used = new properties_text($this);
		$this->x_used->
			set_title(gtext('Used Space'))->
			set_name('used');
		return $this->x_used;
	}
	public function get_volblocksize() {
		return $this->x_volblocksize ?? $this->init_volblocksize();
	}
	public function init_volblocksize() {
		$this->x_volblocksize = new properties_text($this);
		$this->x_volblocksize->
			set_title(gtext('Block Size'))->
			set_name('volblocksize');
		return $this->x_volblocksize;
	}
	public function set_volmode() {
		$this->x_volmode = new properties_list($this);
		$this->x_volmode->
			set_title(gtext('Volume Mode'))->
			set_name('volmode');
		return $this->x_volmode;
	}

	public function get_volsize() {
		return $this->x_volsize ?? $this->init_volsize();
	}
	public function init_volsize() {
		$this->x_volsize = new properties_text($this);
		$this->x_volsize->
			set_title(gtext('Size'))->
			set_name('volsize');
		return $this->x_volsize;
	}
}
