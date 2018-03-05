<?php
/*
	properties_services_samba_share.php

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

class properties_services_samba_share extends co_property_container {
	protected $x_afpcompat;
	protected $x_auxparam;
	protected $x_inheritacls;
	protected $x_shadowformat;
	protected $x_storealternatedatastreams;
	protected $x_storentfsacls;
	protected $x_vfs_fruit_encoding;
	protected $x_vfs_fruit_locking;
	protected $x_vfs_fruit_metadata;
	protected $x_vfs_fruit_resource;
	protected $x_vfs_fruit_time_machine;
	protected $x_zfsacl;
	protected $x_hostsallow;
	protected $x_hostsdeny;
	protected $x_shadowcopy;
	protected $x_readonly;
	protected $x_browseable;
	protected $x_guest;
	protected $x_inheritpermissions;
	protected $x_recyclebin;
	protected $x_hidedotfiles;
	protected $x_name;
	protected $x_comment;
	protected $x_path;
	protected $x_uuid;

	public function get_afpcompat() {
		return $this->x_afpcompat ?? $this->init_afpcompat();
	}
	public function init_afpcompat() {
		$this->x_afpcompat = new properties_bool($this);
		$this->x_afpcompat->
			set_title(gtext('Enable AFP'))->
			set_name('afpcompat');
		return $this->x_afpcompat;
	}
	public function get_auxparam() {
		return $this->x_auxparam ?? $this->init_auxparam();
	}
	public function init_auxparam() {
		$this->x_auxparam = new properties_textarea($this);
		$this->x_auxparam->
			set_title(gtext('Additional Parameter'))->
			set_name('auxparam');
		return $this->x_auxparam;
	}
	public function get_shadowformat() {
		return $this->x_shadowformat ?? $this->init_shadowformat();
	}
	public function init_shadowformat() {
		$this->x_shadowformat = new properties_text($this);
		$this->x_shadowformat->
			set_title(gtext('Shadow Copy Format'))->
			set_name('shadowformat');
		return $this->x_shadowformat;
	}
	public function get_vfs_fruit_encoding() {
		return $this->x_vfs_fruit_encoding ?? $this->init_vfs_fruit_encoding();
	}
	public function init_vfs_fruit_encoding() {
		$this->x_vfs_fruit_encoding = new properties_list($this);
		$this->x_vfs_fruit_encoding->
			set_title(gtext('VFS fruit:encoding'))->
			set_name('vfs_fruit_encoding');
		return $this->x_vfs_fruit_encoding;
	}
	public function get_vfs_fruit_locking() {
		return $this->x_vfs_fruit_locking ?? $this->init_vfs_fruit_locking();
	}
	public function init_vfs_fruit_locking() {
		$this->x_vfs_fruit_locking = new properties_list($this);
		$this->x_vfs_fruit_locking->
			set_title(gtext('VFS fruit:locking'))->
			set_name('vfs_fruit_locking');
		return $this->x_vfs_fruit_locking;
	}
	public function get_vfs_fruit_metadata() {
		return $this->x_vfs_fruit_metadata ?? $this->init_vfs_fruit_metadata();
	}
	public function init_vfs_fruit_metadata() {
		$this->x_vfs_fruit_metadata = new properties_list($this);
		$this->x_vfs_fruit_metadata->
			set_title(gtext('VFS fruit:metadata'))->
			set_name('vfs_fruit_metadata');
		return $this->x_vfs_fruit_metadata;
	}
	public function get_vfs_fruit_resource() {
		return $this->x_vfs_fruit_resource ?? $this->init_vfs_fruit_resource();
	}
	public function init_vfs_fruit_resource() {
		$this->x_vfs_fruit_resource = new properties_list($this);
		$this->x_vfs_fruit_resource->
			set_title(gtext('VFS fruit:resource'))->
			set_name('vfs_fruit_resource');
		return $this->x_vfs_fruit_resource;
	}
	public function get_vfs_fruit_time_machine() {
		return $this->x_vfs_fruit_time_machine ?? $this->init_vfs_fruit_time_machine();
	}
	public function init_vfs_fruit_time_machine() {
		$this->x_vfs_fruit_time_machine = new properties_list($this);
		$this->x_vfs_fruit_time_machine->
			set_title(gtext('VFS fruit:time machine'))->
			set_name('vfs_fruit_time_machine');
		return $this->x_vfs_fruit_time_machine;
	}
	public function get_zfsacl() {
		return $this->x_zfsacl ?? $this->init_zfsacl();
	}
	public function init_zfsacl() {
		$this->x_zfsacl = new properties_bool($this);
		$this->x_zfsacl->
			set_title(gtext('ZFS ACL'))->
			set_name('zfsacl');
		return $this->x_zfsacl;
	}
	public function get_inheritacls() {
		return $this->x_inheritacls ?? $this->init_inheritacls();
	}
	public function init_inheritacls() {
		$this->x_inheritacls = new properties_bool($this);
		$this->x_inheritacls->
			set_title(gtext('Inherit ACL'))->
			set_name('inheritacls');
		return $this->x_inheritacls;
	}
	public function get_storealternatedatastreams() {
		return $this->x_storealternatedatastreams ?? $this->init_storealternatedatastreams();
	}
	public function init_storealternatedatastreams() {
		$this->x_storealternatedatastreams = new properties_bool($this);
		$this->x_storealternatedatastreams->
			set_title(gtext('Alternate Data Streams'))->
			set_name('storealternatedatastreams');
		return $this->x_storealternatedatastreams;
	}
	public function get_storentfsacls() {
		return $this->x_storentfsacls ?? $this->init_storentfsacls();
	}
	public function init_storentfsacls() {
		$this->x_storentfsacls = new properties_bool($this);
		$this->x_storentfsacls->
			set_title(gtext('NTFS ACLs'))->
			set_name('storentfsacls');
		return $this->x_storentfsacls;
	}
	public function get_hostsallow() {
		return $this->x_hostsallow ?? $this->init_hostsallow();
	}
	public function init_hostsallow() {
		$this->x_hostsallow = new properties_text($this);
		$this->x_hostsallow->
			set_title(gtext('Hosts Allow'))->
			set_name('hostsallow');
		return $this->x_hostsallow;
	}
	public function get_hostsdeny() {
		return $this->x_hostsdeny ?? $this->init_hostsdeny();
	}
	public function init_hostsdeny() {
		$this->x_hostsdeny = new properties_text($this);
		$this->x_hostsdeny->
			set_title(gtext('Hosts Deny'))->
			set_name('hostsdeny');
		return $this->x_hostsdeny;
	}
	public function get_shadowcopy() {
		return $this->x_shadowcopy ?? $this->init_shadowcopy();
	}
	public function init_shadowcopy() {
		$this->x_shadowcopy = new properties_bool($this);
		$this->x_shadowcopy->
			set_title(gtext('Shadow Copy'))->
			set_name('shadowcopy');
		return $this->x_shadowcopy;
	}
	public function get_readonly() {
		return $this->x_readonly ?? $this->init_readonly();
	}
	public function init_readonly() {
		$this->x_readonly = new properties_bool($this);
		$this->x_readonly->
			set_title(gtext('Read Only'))->
			set_name('readonly');
		return $this->x_readonly;
	}
	public function get_browseable() {
		return $this->x_browseable ?? $this->init_browseable();
	}
	public function init_browseable() {
		$this->x_browseable = new properties_bool($this);
		$this->x_browseable->
			set_title(gtext('Browseable'))->
			set_name('browseable');
		return $this->x_browseable;
	}
	public function get_guest() {
		return $this->x_guest ?? $this->init_guest();
	}
	public function init_guest() {
		$this->x_guest = new properties_bool($this);
		$this->x_guest->
			set_title(gtext('Guest'))->
			set_name('guest');
		return $this->x_guest;
	}
	public function get_inheritpermissions() {
		return $this->x_inheritpermissions ?? $this->init_inheritpermissions();
	}
	public function init_inheritpermissions() {
		$this->x_inheritpermissions = new properties_bool($this);
		$this->x_inheritpermissions->
			set_title(gtext('Inherit Permissions'))->
			set_name('inheritpermissions');
		return $this->x_inheritpermissions;
	}
	public function get_recyclebin() {
		return $this->x_recyclebin ?? $this->init_recyclebin();
	}
	public function init_recyclebin() {
		$this->x_recyclebin = new properties_bool($this);
		$this->x_recyclebin->
			set_title(gtext('Recycle Bin'))->
			set_name('recyclebin');
		return $this->x_recyclebin;
	}
	public function get_hidedotfiles() {
		return $this->x_hidedotfiles ?? $this->init_hidedotfiles();
	}
	public function init_hidedotfiles() {
		$this->x_hidedotfiles = new properties_bool($this);
		$this->x_hidedotfiles->
			set_title(gtext('Hide Dot Files'))->
			set_name('hidedotfiles');
		return $this->x_hidedotfiles;
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
	public function get_comment() {
		return $this->x_comment ?? $this->init_comment();
	}
	public function init_comment() {
		$this->x_comment = new properties_text($this);
		$this->x_comment->
			set_title(gtext('Comment'))->
			set_name('comment');
		return $this->x_comment;
	}
	public function get_path() {
		return $this->x_path ?? $this->init_path();
	}
	public function init_path() {
		$this->x_path = new properties_text($this);
		$this->x_path->
			set_title(gtext('Path'))->
			set_name('path');
		return $this->x_path;
	}
	public function get_uuid() {
		return $this->x_uuid ?? $this->init_uuid();
	}
	public function init_uuid() {
		$this->x_uuid = new property_uuid($this);
		return $this->x_uuid;
	}
}
