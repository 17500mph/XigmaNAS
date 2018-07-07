<?php
/*
	properties_services_samba_share_edit.php

	Part of XigmaNAS (http://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
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
	either expressed or implied, of the XigmaNAS Project.
*/
require_once 'properties_services_samba_share.php';

class properties_services_samba_share_edit extends properties_services_samba_share {
	public function init_afpcompat() {
		$property = parent::init_afpcompat();
		$caption = gtext('Enable');
		$property->
			set_id('afpcompat')->
			set_caption($caption)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_auxparam() {
		$property = parent::init_auxparam();
		$description = 
			sprintf(gtext('These parameters are added to [Share] section of %s.'),'smb4.conf') .
			' ' .
			sprintf('<a href="%s" target="_blank">%s</a>.','http://us1.samba.org/samba/docs/man/manpages-3/smb.conf.5.html',gtext('Please check the documentation'));
		$property->
			set_id('auxparam')->
			set_description($description)->
			set_defaultvalue('')->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_shadowformat() {
		$property = parent::init_shadowformat();
		$default_shadowformat = "auto-%Y%m%d-%H%M%S";
		$description = sprintf(gtext('The custom format of the snapshot for shadow copy service can be specified. The default format is %s used for ZFS auto snapshot.'),$default_shadowformat);
		$property->
			set_id('shadowformat')->
			set_description($description)->
			set_size(60)->
			set_maxlength(1024)->
			set_defaultvalue($default_shadowformat)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_vfs_fruit_encoding() {
		$property = parent::init_vfs_fruit_encoding();
		$description = gtext('Controls how the set of illegal NTFS ASCII character are stored in the filesystem, commonly used by OS X clients.');
		$options = [
			'native' => gtext('Native - store characters with their native ASCII value.'),
			'private' => gtext('Private (default) - store characters as encoded by the OS X client: mapped to the Unicode private range.')
		];
		$property->
			set_id('vfs_fruit_encoding')->
			set_options($options)->
			set_description($description)->
			set_defaultvalue('native')->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_vfs_fruit_locking() {
		$property = parent::init_vfs_fruit_locking();
		$description = '';
		$options = [
			'netatalk' => gtext('Netatalk - use cross protocol locking with Netatalk.'),
			'none' => gtext('None (default) - no cross protocol locking.')
		];
		$property->
			set_id('vfs_fruit_locking')->
			set_options($options)->
			set_description($description)->
			set_defaultvalue('netatalk')->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_vfs_fruit_metadata() {
		$property = parent::init_vfs_fruit_metadata();
		$description = gtext('Controls where the OS X metadata stream is stored.');
		$options = [
			'netatalk' => gtext('Netatalk (default) - use Netatalk compatible xattr.'),
			'stream' => gtext('Stream - pass the stream on to the next module in the VFS stack.')
		];
		$property->
			set_id('vfs_fruit_metadata')->
			set_options($options)->
			set_description($description)->
			set_defaultvalue('netatalk')->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_vfs_fruit_resource() {
		$property = parent::init_vfs_fruit_resource();
		$description = gtext('Controls where the OS X resource fork is stored.');
		$options = [
			'file' => gtext('File (default) - use a ._ AppleDouble file compatible with OS X and Netatalk.'),
			'xattr' => gtext('Extended Attributes - use a xattr.'),
			'stream' => gtext('Stream (experimental) - pass the stream on to the next module in the VFS stack.')
		];
		$property->
			set_id('vfs_fruit_resource')->
			set_options($options)->
			set_description($description)->
			set_defaultvalue('file')->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_vfs_fruit_time_machine() {
		$property = parent::init_vfs_fruit_time_machine();
		$description = gtext('Controls if Time Machine support via the FULLSYNC volume capability is advertised to clients.');
		$options = [
			'yes' => gtext('Yes - Enables Time Machine support for this share.'),
			'no' => gtext('No (default) - Disables advertising Time Machine support.')
		];
		$property->
			set_id('vfs_fruit_time_machine')->
			set_options($options)->
			set_description($description)->
			set_defaultvalue('no')->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_zfsacl() {
		$property = parent::init_zfsacl();
		$caption = gtext('Enable ZFS ACL.');
		$description = gtext('This will provide ZFS ACL support. (ZFS only).');
		$property->
			set_id('zfsacl')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_inheritacls() {
		$property = parent::init_inheritacls();
		$caption = gtext('Enable ACL inheritance.');
		$description = '';
		$property->
			set_id('inheritacls')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_storealternatedatastreams() {
		$property = parent::init_storealternatedatastreams();
		$caption = gtext('Store alternate data streams in Extended Attributes.');
		$description = '';
		$property->
			set_id('storealternatedatastreams')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_storentfsacls() {
		$property = parent::init_storentfsacls();
		$caption = gtext('Store NTFS ACLs in Extended Attributes.');
		$description = gtext('This will provide NTFS ACLs without ZFS ACL support such as UFS.');
		$property->
			set_id('storentfsacls')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_hostsallow() {
		$property = parent::init_hostsallow();
		$description = gtext('This option is a comma, space, or tab delimited set of hosts which are permitted to access this share. You can specify the hosts by name or IP number. Leave this field empty to use default settings.');
		$property->
			set_id('hostsallow')->
			set_size(60)->
			set_maxlength(1024)->
			set_description($description)->
			set_defaultvalue('')->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => '/(^$|\S)/'])->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_hostsdeny() {
		$property = parent::init_hostsdeny();
		$description = gtext('This option is a comma, space, or tab delimited set of host which are NOT permitted to access this share. Where the lists conflict, the allow list takes precedence. In the event that it is necessary to deny all by default, use the keyword ALL (or the netmask 0.0.0.0/0) and then explicitly specify to the hosts allow parameter those hosts that should be permitted access. Leave this field empty to use default settings.');
		$property->
			set_id('hostsdeny')->
			set_size(60)->
			set_maxlength(1024)->
			set_description($description)->
			set_defaultvalue('')->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => '/(^$|\S)/'])->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_shadowcopy() {
		$property = parent::init_shadowcopy();
		$caption = gtext('Enable shadow copy.');
		$description = gtext('This will provide shadow copy created by auto snapshot. (ZFS only).');
		$property->
			set_id('shadowcopy')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_readonly() {
		$property = parent::init_readonly();
		$caption = gtext('Set read only.');
		$description = gtext('If this parameter is set, then users may not create or modify files in the share.');
		$property->
			set_id('readonly')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_browseable() {
		$property = parent::init_browseable();
		$caption = gtext('Set browseable.');
		$description = gtext('This controls whether this share is seen in the list of available shares in a net view and in the browse list.');
		$property->
			set_id('browseable')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_guest() {
		$property = parent::init_guest();
		$caption = gtext('Enable guest access.');
		$description = gtext('This controls whether this share is accessible by guest account.');
		$property->
			set_id('guest')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_inheritpermissions() {
		$property = parent::init_inheritpermissions();
		$caption = gtext('Enable permission inheritance.');
		$description = gtext('The permissions on new files and directories are normally governed by create mask and directory mask but the inherit permissions parameter overrides this. This can be particularly useful on systems with many users to allow a single share to be used flexibly by each user.');
		$property->
			set_id('inheritpermissions')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_recyclebin() {
		$property = parent::init_recyclebin();
		$caption = gtext('Enable recycle bin.');
		$description = gtext('This will create a recycle bin on the share.');
		$property->
			set_id('recyclebin')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_hidedotfiles() {
		$property = parent::init_hidedotfiles();
		$caption = gtext('This parameter controls whether files starting with a dot appear as hidden files.');
		$description = '';
		$property->
			set_id('hidedotfiles')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_name() {
		$property = parent::init_name();
		$description = '';
		$placeholder = gtext('Enter the name of the share');
		$property->
			set_id('name')->
			set_size(60)->
			set_maxlength(1024)->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Must not be empty.')));
		return $property;
	}
	public function init_comment() {
		$property = parent::init_comment();
		$description = '';
		$placeholder = gtext('Enter comment for the share');
		$property->
			set_id('comment')->
			set_size(60)->
			set_maxlength(1024)->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Must not be empty.')));
		return $property;
	}
	public function init_path() {
		$property = parent::init_path();
		$description = gtext('Path to be shared.');
		$placeholder = gtext('Enter the location of the share');
		$property->
			set_id('path')->
			set_size(60)->
			set_maxlength(1024)->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Must not be empty.')));
		return $property;
	}
}
