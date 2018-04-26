<?php
/*
	properties_services_ctl_lun.php

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

class ctl_lun_properties extends co_property_container_param {
	protected $x_name;
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$property = $this->x_name = new property_text($this);
		$property->
			set_name('name')->
			set_title(gtext('LUN Name'));
		return $property;
	}
	protected $x_backend;
	public function get_backend() {
		return $this->x_backend ?? $this->init_backend();
	}
	public function init_backend() {
		$property = $this->x_backend = new property_list($this);
		$property->
			set_name('backend')->
			set_title(gtext('Backend'));
		return $property;
	}
	protected $x_blocksize;
	public function get_blocksize() {
		return $this->x_blocksize ?? $this->init_blocksize();
	}
	public function init_blocksize() {
		$property = $this->x_blocksize = new property_list($this);
		$property->
			set_name('blocksize')->
			set_title(gtext('Block Size'));
		return $property;
	}
	protected $x_ctl_lun;
	public function get_ctl_lun() {
		return $this->x_ctl_lun ?? $this->init_ctl_lun();
	}
	public function init_ctl_lun() {
		$property = $this->x_ctl_lun = new property_text($this);
		$property->
			set_name('ctl_lun')->
			set_title(gtext('CTL LUN'));
		return $property;
	}
	protected $x_device_id;
	public function get_device_id() {
		return $this->x_device_id ?? $this->init_device_id();
	}
	public function init_device_id() {
		$property = $this->x_device_id = new property_text($this);
		$property->
			set_name('device_id')->
			set_title(gtext('Device ID'));
		return $property;
	}
	protected $x_device_type;
	public function get_device_type() {
		return $this->x_device_type ?? $this->init_device_type();
	}
	public function init_device_type() {
		$property = $this->x_device_type = new property_list($this);
		$property->
			set_name('device_type')->
			set_title(gtext('Device Type'));
		return $property;
	}
	protected $x_path;
	public function get_path() {
		return $this->x_path ?? $this->init_path();
	}
	public function init_path() {
		$property = $this->x_path = new property_text($this);
		$property->
			set_name('path')->
			set_title(gtext('Path'));
		return $property;
	}
	protected $x_serial;
	public function get_serial() {
		return $this->x_serial ?? $this->init_serial();
	}
	public function init_serial() {
		$property = $this->x_ = new property_text($this);
		$property->
			set_name('serial')->
			set_title(gtext('Serial Number'));
		return $property;
	}
	protected $x_size;
	public function get_size() {
		return $this->x_size ?? $this->init_size();
	}
	public function init_size() {
		$property = $this->x_size = new property_text($this);
		$property->
			set_name('size')->
			set_title(gtext('Size'));
		return $property;
	}
	protected $x_opt_vendor;
	public function get_opt_vendor() {
		return $this->x_opt_vendor ?? $this->init_opt_vendor();
	}
	public function init_opt_vendor() {
		$property = $this->x_opt_vendor = new property_text($this);
		$property->
			set_name('opt_vendor')->
			set_title(gtext('LUN Vendor'));
		return $property;
	}
	protected $x_opt_product;
	public function get_opt_product() {
		return $this->x_opt_product ?? $this->init_opt_product();
	}
	public function init_opt_product() {
		$property = $this->x_opt_product = new property_text($this);
		$property->
			set_name('opt_product')->
			set_title(gtext('LUN Product'));
		return $property;
	}
	protected $x_opt_revision;
	public function get_opt_revision() {
		return $this->x_opt_revision ?? $this->init_opt_revision();
	}
	public function init_opt_revision() {
		$property = $this->x_opt_revision = new property_text($this);
		$property->
			set_name('opt_revision')->
			set_title(gtext('LUN Revision'));
		return $property;
	}
	protected $x_opt_scsiname;
	public function get_opt_scsiname() {
		return $this->x_opt_scsiname ?? $this->init_opt_scsiname();
	}
	public function init_opt_scsiname() {
		$property = $this->x_opt_scsiname = new property_text($this);
		$property->
			set_name('opt_scsiname')->
			set_title(gtext('LUN SCSI Name'));
		return $property;
	}
	protected $x_opt_eui;
	public function get_opt_eui() {
		return $this->x_opt_eui ?? $this->init_opt_eui();
	}
	public function init_opt_eui() {
		$property = $this->x_opt_eui = new property_text($this);
		$property->
			set_name('opt_eui')->
			set_title(gtext('LUN EUI-64	identifier'));
		return $property;
	}
	protected $x_opt_naa;
	public function get_opt_naa() {
		return $this->x_opt_naa ?? $this->init_opt_naa();
	}
	public function init_opt_naa() {
		$property = $this->x_opt_naa = new property_text($this);
		$property->
			set_name('opt_naa')->
			set_title(gtext('LUN NAA Identifier'));
		return $property;
	}
	protected $x_opt_uuid;
	public function get_opt_uuid() {
		return $this->x_opt_uuid ?? $this->init_opt_uuid();
	}
	public function init_opt_uuid() {
		$property = $this->x_opt_uuid = new property_text($this);
		$property->
			set_name('opt_uuid')->
			set_title(gtext('UUID'));
		return $property;
	}
	protected $x_opt_ha_role;
	public function get_opt_ha_role() {
		return $this->x_opt_ha_role ?? $this->init_opt_ha_role();
	}
	public function init_opt_ha_role() {
		$property = $this->x_opt_ha_role = new property_list($this);
		$property->
			set_name('opt_ha_role')->
			set_title(gtext('HA Role'));
		return $property;
	}
	protected $x_opt_insecure_tpc;
	public function get_opt_insecure_tpc() {
		return $this->x_opt_insecure_tpc ?? $this->init_opt_insecure_tpc();
	}
	public function init_opt_insecure_tpc() {
		$property = $this->x_opt_insecure_tpc = new property_list($this);
		$property->
			set_name('opt_insecure_tpc')->
			set_title(gtext('Insecure TPC'));
		return $property;
	}
	protected $x_opt_readcache;
	public function get_opt_readcache() {
		return $this->x_opt_readcache ?? $this->init_opt_readcache();
	}
	public function init_opt_readcache() {
		$property = $this->x_opt_readcache = new property_list($this);
		$property->
			set_name('opt_readcache')->
			set_title(gtext('Read Cache'));
		return $property;
	}
	protected $x_opt_readonly;
	public function get_opt_readonly() {
		return $this->x_opt_readonly ?? $this->init_opt_readonly();
	}
	public function init_opt_readonly() {
		$property = $this->x_opt_readonly = new property_list($this);
		$property->
			set_name('opt_readonly')->
			set_title(gtext('Read Only'));
		return $property;
	}
	protected $x_opt_removable;
	public function get_opt_removable() {
		return $this->x_opt_removable ?? $this->init_opt_removable();
	}
	public function init_opt_removable() {
		$property = $this->x_opt_removable = new property_list($this);
		$property->
			set_name('opt_removable')->
			set_title(gtext('Removable'));
		return $property;
	}
	protected $x_opt_reordering;
	public function get_opt_reordering() {
		return $this->x_opt_reordering ?? $this->init_opt_reordering();
	}
	public function init_opt_reordering() {
		$property = $this->x_opt_reordering = new property_list($this);
		$property->
			set_name('opt_reordering')->
			set_title(gtext('Reordering'));
		return $property;
	}
	protected $x_opt_serseq;
	public function get_opt_serseq() {
		return $this->x_opt_serseq ?? $this->init_opt_serseq();
	}
	public function init_opt_serseq() {
		$property = $this->x_opt_serseq = new property_list($this);
		$property->
			set_name('opt_serseq')->
			set_title(gtext('Serialize Sequence'));
		return $property;
	}
	protected $x_opt_pblocksize;
	public function get_opt_pblocksize() {
		return $this->x_opt_pblocksize ?? $this->init_opt_pblocksize();
	}
	public function init_opt_pblocksize() {
		$property = $this->x_opt_pblocksize = new property_text($this);
		$property->
			set_name('opt_pblocksize')->
			set_title(gtext('Physical Block Size'));
		return $property;
	}
	protected $x_opt_pblockoffset;
	public function get_opt_pblockoffset() {
		return $this->x_opt_pblockoffset ?? $this->init_opt_pblockoffset();
	}
	public function init_opt_pblockoffset() {
		$property = $this->x_opt_pblockoffset = new property_text($this);
		$property->
			set_name('opt_pblockoffset')->
			set_title(gtext('Physical Block Offset'));
		return $property;
	}
	protected $x_opt_ublocksize;
	public function get_opt_ublocksize() {
		return $this->x_opt_ublocksize ?? $this->init_opt_ublocksize();
	}
	public function init_opt_ublocksize() {
		$property = $this->x_opt_ublocksize = new property_text($this);
		$property->
			set_name('opt_ublocksize')->
			set_title(gtext('UNMAP Block Size'));
		return $property;
	}
	protected $x_opt_ublockoffset;
	public function get_opt_ublockoffset() {
		return $this->x_opt_ublockoffset ?? $this->init_opt_ublockoffset();
	}
	public function init_opt_ublockoffset() {
		$property = $this->x_opt_ublockoffset = new property_text($this);
		$property->
			set_name('opt_ublockoffset')->
			set_title(gtext('UNMAP Block Offset'));
		return $property;
	}
	protected $x_opt_rpm;
	public function get_opt_rpm() {
		return $this->x_opt_rpm ?? $this->init_opt_rpm();
	}
	public function init_opt_rpm() {
		$property = $this->x_opt_rpm = new property_text($this);
		$property->
			set_name('opt_rpm')->
			set_title(gtext('RPM'));
		return $property;
	}
	protected $x_opt_formfactor;
	public function get_opt_formfactor() {
		return $this->x_opt_formfactor ?? $this->init_opt_formfactor();
	}
	public function init_opt_formfactor() {
		$property = $this->x_opt_formfactor = new property_list($this);
		$property->
			set_name('opt_formfactor')->
			set_title(gtext('Form Factor'));
		return $property;
	}
	protected $x_opt_provisioning_type;
	public function get_opt_provisioning_type() {
		return $this->x_opt_provisioning_type ?? $this->init_opt_provisioning_type();
	}
	public function init_opt_provisioning_type() {
		$property = $this->x_opt_provisioning_type = new property_list($this);
		$property->
			set_name('opt_provisioning_type')->
			set_title(gtext('Provisioning Type'));
		return $property;
	}
	protected $x_opt_unmap;
	public function get_opt_unmap() {
		return $this->x_opt_unmap ?? $this->init_opt_unmap();
	}
	public function init_opt_unmap() {
		$property = $this->x_opt_unmap = new property_list($this);
		$property->
			set_name('opt_unmap')->
			set_title(gtext('UNMAP'));
		return $property;
	}
	protected $x_opt_unmap_max_lba;
	public function get_opt_unmap_max_lba() {
		return $this->x_opt_unmap_max_lba ?? $this->init_opt_unmap_max_lba();
	}
	public function init_opt_unmap_max_lba() {
		$property = $this->x_opt_unmap_max_lba = new property_text($this);
		$property->
			set_name('opt_unmap_max_lba')->
			set_title(gtext('UNMAP Maximum LBA'));
		return $property;
	}
	protected $x_opt_unmap_max_descr;
	public function get_opt_unmap_max_descr() {
		return $this->x_opt_unmap_max_descr ?? $this->init_opt_unmap_max_descr();
	}
	public function init_opt_unmap_max_descr() {
		$property = $this->x_opt_unmap_max_descr = new property_text($this);
		$property->
			set_name('opt_unmap_max_descr')->
			set_title(gtext('UNMAP Maximum Descriptors'));
		return $property;
	}
	protected $x_opt_write_same_max_lba;
	public function get_opt_write_same_max_lba() {
		return $this->x_opt_write_same_max_lba ?? $this->init_opt_write_same_max_lba();
	}
	public function init_opt_write_same_max_lba() {
		$property = $this->x_opt_write_same_max_lba = new property_text($this);
		$property->
			set_name('opt_write_same_max_lba')->
			set_title(gtext('Write Same Maximum LBA'));
		return $property;
	}
	protected $x_opt_avail_threshold;
	public function get_opt_avail_threshold() {
		return $this->x_opt_avail_threshold ?? $this->init_opt_avail_threshold();
	}
	public function init_opt_avail_threshold() {
		$property = $this->x_opt_avail_threshold = new property_text($this);
		$property->
			set_name('opt_avail_threshold')->
			set_title(gtext('Avail Threshold'));
		return $property;
	}
	protected $x_opt_used_threshold;
	public function get_opt_used_threshold() {
		return $this->x_opt_used_threshold ?? $this->init_opt_used_threshold();
	}
	public function init_opt_used_threshold() {
		$property = $this->x_opt_used_threshold = new property_text($this);
		$property->
			set_name('opt_used_threshold')->
			set_title(gtext('Used Threshold'));
		return $property;
	}
	protected $x_opt_pool_avail_threshold;
	public function get_opt_pool_avail_threshold() {
		return $this->x_opt_pool_avail_threshold ?? $this->init_opt_pool_avail_threshold();
	}
	public function init_opt_pool_avail_threshold() {
		$property = $this->x_opt_pool_avail_threshold = new property_text($this);
		$property->
			set_name('opt_pool_avail_threshold')->
			set_title(gtext('Pool Avail Threshold'));
		return $property;
	}
	protected $x_opt_pool_used_threshold;
	public function get_opt_pool_used_threshold() {
		return $this->x_opt_pool_used_threshold ?? $this->init_opt_pool_used_threshold();
	}
	public function init_opt_pool_used_threshold() {
		$property = $this->x_opt_pool_used_threshold = new property_text($this);
		$property->
			set_name('opt_pool_used_threshold')->
			set_title(gtext('Pool Used Threshold'));
		return $property;
	}
	protected $x_opt_writecache;
	public function get_opt_writecache() {
		return $this->x_opt_writecache ?? $this->init_opt_writecache();
	}
	public function init_opt_writecache() {
		$property = $this->x_opt_writecache = new property_list($this);
		$property->
			set_name('opt_writecache')->
			set_title(gtext('Write Cache'));
		return $property;
	}
	protected $x_opt_file;
	public function get_opt_file() {
		return $this->x_opt_file ?? $this->init_opt_file();
	}
	public function init_opt_file() {
		$property = $this->x_opt_file = new property_text($this);
		$property->
			set_name('opt_file')->
			set_title(gtext('File'));
		return $property;
	}
	protected $x_opt_num_threads;
	public function get_opt_num_threads() {
		return $this->x_opt_num_threads ?? $this->init_opt_num_threads();
	}
	public function init_opt_num_threads() {
		$property = $this->x_opt_num_threads = new property_text($this);
		$property->
			set_name('opt_num_threads')->
			set_title(gtext('Threads'));
		return $property;
	}
	protected $x_opt_capacity;
	public function get_opt_capacity() {
		return $this->x_opt_capacity ?? $this->init_opt_capacity();
	}
	public function init_opt_capacity() {
		$property = $this->x_opt_capacity = new property_text($this);
		$property->
			set_name('opt_capacity')->
			set_title(gtext('Capacity'));
		return $property;
	}
	protected $x_auxparam;
	public function get_auxparam() {
		return $this->x_auxparam ?? $this->init_auxparam();
	}
	public function init_auxparam() {
		$property = $this->x_auxparam = new property_textarea($this);
		$property->
			set_name('auxparam')->
			set_title(gtext('Additional Parameter'));
		return $property;
	}
}
class ctl_lun_edit_properties extends ctl_lun_properties {
	public function init_name() {
		$property = parent::init_name();
		$description = gtext('Name of the LUN.');
		$placeholder = gtext('LUN Name');
		$regexp = '/^\S{1,223}$/';
		$property->
			set_id('name')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(223)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_backend() {
		$property = parent::init_backend();
		$description = gtext('The CTL backend to use for a given LUN.');
		$options = [
			'' => gtext('Default'),
			'block' => gtext('Block'),
			'ramdisk' => gtext('RAM Disk')
		];
		$property->
			set_id('backend')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_blocksize() {
		$property = $property = parent::init_blocksize();
		$description = gtext('The blocksize visible to the initiator.');
		$options = [
			'' => gtext('Default'),
			'512' => gtext('512'),
			'2048' => gtext('2048'),
			'4096' => gtext('4096'),
			'8192' => gtext('8192')
		];
		$property->
			set_id('blocksize')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_ctl_lun() {
		$property = parent::init_ctl_lun();
		$description = gtext('Global numeric identifier to use for a given LUN inside CTL.');
		$regexp = '/^(?:|[0-9]|[1-9][0-9]{1,2}|10[01][0-9]|102[0-3])$/';
		$property->
			set_id('ctl_lun')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(5)->
			set_maxlength(4)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_device_id() {
		//	The device-id shall be 48 characters width,
		//	Compatibility istgt: 16 bytes "iSCSI Disk     " + 32 bytes aligned serial number
		$property = parent::init_device_id();
		$description = gtext('The SCSI Device Identification string presented to the initiator.');
		$placeholder = gtext('Device ID');
		$regexp = '/^\S{0,48}$/';
		$property->
			set_id('device_id')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(48)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_device_type() {
		$property = parent::init_device_type();
		$description = gtext('Specify the SCSI device type to use when creating the LUN.');
		$options = [
			'' => gtext('Undefined'),
			'disk' => gtext('Disk'),
			'direct' => gtext('Direct'),
			'processor' => gtext('Processor'),
			'cd' => gtext('CD'),
			'cdrom' => gtext('CD-ROM'),
			'dvd' => gtext('DVD'),
			'dvdrom' => gtext('DVD-ROM'),
/*
			'0' => gtext('0'),
			'1' => gtext('1'),
			'2' => gtext('2'),
			'3' => gtext('3'),
			'4' => gtext('4'),
			'5' => gtext('5'),
			'6' => gtext('6'),
			'7' => gtext('7'),
			'8' => gtext('8'),
			'9' => gtext('9'),
			'10' => gtext('10'),
			'11' => gtext('11'),
			'12' => gtext('12'),
			'13' => gtext('13'),
			'14' => gtext('14'),
			'15' => gtext('15'),
 */
		];
		$property->
			set_id('device_type')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_path() {
		$property = parent::init_path();
		$description = gtext('The path to the file, device node, or zfs volume used to back the LUN.');
		$regexp = '/^(?:|.{1,223})$/';
		$property->
			set_id('path')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(223)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_serial() {
		$property = parent::init_serial();
		$description = gtext('The SCSI serial number presented to the initiator.');
		$regexp = '/^(?:|.{1,40})$/';
		$property->
			set_id('serial')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(40)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_size() {
		$property = parent::init_size();
		$description = gtext('The LUN size, in bytes.');
		$regexp = '/^(?:|0|[1-9][0-9]{0,16})$/';
		$property->
			set_id('size')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(40)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_vendor() {
		$property = parent::init_opt_vendor();
		$description = gtext('Specifies LUN vendor string up to 8 chars.');
		$placeholder = 'FreeBSD';
		$regexp = '/^\S{0,8}$/';
		$property->
			set_id('opt_vendor')->
			set_description($description)->
			set_placeholder($placeholder)->
//			set_defaultvalue('FreeBSD')->
			set_defaultvalue('')->
			set_size(10)->
			set_maxlength(8)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_product() {
		$property = parent::init_opt_product();
		$description = gtext('Specifies LUN product string up to 16 chars.');
		$placeholder = 'iSCSI Disk';
		$regexp = '/^\S{0,16}$/';
		$property->
			set_id('opt_product')->
			set_description($description)->
			set_placeholder($placeholder)->
//			set_defaultvalue('iSCSI Disk')->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_revision() {
		$property = parent::init_opt_revision();
		$description = gtext('Specifies LUN revision string up to 4 chars.');
		$placeholder = '0123';
		$regexp = '/^\S{0,4}$/';
		$property->
			set_id('opt_revision')->
			set_description($description)->
			set_placeholder($placeholder)->
//			set_defaultvalue('0123')->
			set_defaultvalue('')->
			set_size(6)->
			set_maxlength(4)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_scsiname() {
		$property = parent::init_opt_scsiname();
		$description = gtext('Specifies LUN SCSI name string.');
		$placeholder = gtext('SCSI Name');
		$regexp = '/^\S{0,223}$/';
		$property->
			set_id('opt_scsiname')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(223)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_eui() {
		$property = parent::init_opt_eui();
		$description = gtext('Specifies LUN EUI-64 identifier.');
		$placeholder = gtext('EUI-64');
		$regexp = '/^(?:|[0-9a-f]{16})$/i';
		$property->
			set_id('opt_eui')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_naa() {
		$property = parent::init_opt_naa();
		$description = gtext('Specifies LUN NAA identifier.');
		$placeholder = gtext('NAA Identifier');
		$regexp = '/^(?:|[0-9a-f]{32})$/i';
		$property->
			set_id('opt_naa')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(34)->
			set_maxlength(32)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_uuid() {
		$property = parent::init_opt_uuid();
		$description = gtext('Specifies LUN locally assigned RFC 4122 UUID.');
		$placeholder = gtext('UUID');
		$regexp = '/^(?:|[0-9a-f]{4}(?:[0-9a-f]{4}-){4}[0-9a-f]{12})$/i';
		$property->
			set_id('opt_uuid')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(45)->
			set_maxlength(36)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_editableonadd(false)->
			set_editableonmodify(false)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_ha_role() {
		$property = parent::init_opt_ha_role();
		$description = gtext('Setting to "primary" or "secondary" overrides default role of the node in HA cluster.');
		$options = [
			'' => gtext('Default'),
			'primary' => gtext('Primary'),
			'secondary' => gtext('Seconfary')
		];
		$property->
			set_id('opt_ha_role')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_insecure_tpc() {
		$property = parent::init_opt_insecure_tpc();
		$description = gtext('Setting to "on" allows EXTENDED COPY command sent to this LUN access other LUNs on this host, not accessible otherwise. This allows to offload copying between different iSCSI targets residing on the same host in trusted environments.');
		$options = [
			'' => gtext('Off'),
			'on' => gtext('On')
		];
		$property->
			set_id('opt_insecure_tpc')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_readcache() {
		$property = parent::init_opt_readcache();
		$description = gtext('Set to "off", disables read caching for the LUN, if supported by the backend.');
		$options = [
			'' => gtext('On'),
			'off' => gtext('Off')
		];
		$property->
			set_id('opt_readcache')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_readonly() {
		$property = parent::init_opt_readonly();
		$description = gtext('Set to "on", blocks all media write operations to the LUN reporting it as write protected.');
		$options = [
			'' => gtext('Off'),
			'on' => gtext('On')
		];
		$property->
			set_id('opt_readonly')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_removable() {
		$property = parent::init_opt_removable();
		$description = gtext('Set to "on" makes LUN removable.');
		$options = [
			'' => gtext('Off'),
			'on' => gtext('On')
		];
		$property->
			set_id('opt_removable')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_reordering() {
		$property = parent::init_opt_reordering();
		$description = gtext('Set to "unrestricted", allows target to process commands with SIMPLE task attribute in arbitrary order.');
		$options = [
			'' => gtext('Restricted'),
			'unrestricted' => gtext('Unrestricted')
		];
		$property->
			set_id('opt_reordering')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_serseq() {
		$property = parent::init_opt_serseq();
		$description = '';
		$options = [
			'' => gtext('Default'),
			'off' => gtext('Allow consecutive read/writes to be issued in parallel'),
			'on' => gtext('Serialize consecutive reads/writes'),
			'read' => gtext('Serialize consecutive reads')
		];
		$property->
			set_id('opt_serseq')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pblocksize() {
		$property = parent::init_opt_pblocksize();
		$description = gtext('Specify physical block size of the device.');
		$regexp = '/^(?:|[1-9][0-9]{0,8}[kmgtpezy]b?)$/i';
		$property->
			set_id('opt_pblocksize')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pblockoffset() {
		$property = parent::init_opt_pblockoffset();
		$description = gtext('Specify physical block offset of the device.');
		$regexp = '/^(?:|0|[1-9][0-9]{0,8}[kmgtpezy]b?)$/i';
		$property->
			set_id('opt_pblockoffset')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_ublocksize() {
		$property = parent::init_opt_ublocksize();
		$description = gtext('Specify UNMAP block size of the device.');
		$regexp = '/^(?:|[1-9][0-9]{0,8}[kmgtpezy]b?)$/i';
		$property->
			set_id('opt_ublocksize')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_ublockoffset() {
		$property = parent::init_opt_ublockoffset();
		$description = gtext('Specify UNMAP block offset of the device.');
		$regexp = '/^(?:|0|[1-9][0-9]{0,8}[kmgtpezy]b?)$/i';
		$property->
			set_id('opt_ublockoffset')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_rpm() {
		$property = parent::init_opt_rpm();
		$description = gtext('Specifies medium rotation rate of the device.');
		//	0: not reported
		//	1: non-rotating (SSD)
		//	>1024: value in	revolutions per minute
		//	99999: max rpm
		$regexp = '/^(?:|[01]|102[5-9]|10[3-9][0-9]|1[1-9][0-9]{2}|[2-9][0-9]{3}|[1-9][0-9]{4})$/';
		$property->
			set_id('opt_rpm')->
			set_defaultvalue('0')->
			set_description($description)->
			set_size(7)->
			set_maxlength(5)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_formfactor() {
		$property = parent::init_opt_formfactor();
		$description = gtext('Specifies nominal form factor of the device.');
		$options = [
			'' => gtext('Undefined'),
			'0' => gtext('Not Reported'),
			'1' => gtext('5.25"'),
			'2' => gtext('3.5"'),
			'3' => gtext('2.5"'),
			'4' => gtext('1.8"'),
			'5' => gtext('less then 1.8".')
		];
		$property->
			set_id('opt_formfactor')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_provisioning_type() {
		$property = parent::init_opt_provisioning_type();
		$description = gtext('When UNMAP support is enabled, this option specifies provisioning type.');
		$options = [
			'' => gtext('Default'),
			'resource' => gtext('Resource Provisioning'),
			'thin' => gtext('Thin Provisioning'),
			'unknown' => gtext('Unknown')
		];
		$property->
			set_id('opt_provisioning_type')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_unmap() {
		$property = parent::init_opt_unmap();
		$description = gtext('Setting to "on" or "off" controls UNMAP support for the logical unit. Default value is "on" if supported by the backend.');
		$options = [
			'' => gtext('Default'),
			'on' => gtext('On'),
			'off' => gtext('Off')
		];
		$property->
			set_id('opt_unmap')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_unmap_max_lba() {
		$property = parent::init_opt_unmap_max_lba();
		$description = gtext('Specify maximum allowed number of LBAs per UNMAP command to report in Block Limits VPD page.');
		$regexp = '/^(?:|0|[1-9][0-9]*)$/';
		$property->
			set_id('opt_unmap_max_lba')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_unmap_max_descr() {
		$property = parent::init_opt_unmap_max_descr();
		$description = gtext('Specify maximum allowed number of block descriptors per UNMAP command to report in Block Limits VPD page.');
		$regexp = '/^(?:|0|[1-9][0-9]*)$/';
		$property->
			set_id('opt_unmap_max_descr')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_write_same_max_lba() {
		$property = parent::init_opt_write_same_max_lba();
		$description = gtext('Specify maximum allowed number of LBAs per WRITE SAME command to report in Block Limits VPD page.');
		$regexp = '/^(?:|0|[1-9][0-9]*)$/';
		$property->
			set_id('opt_write_same_max_lba')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_avail_threshold() {
		$property = parent::init_opt_avail_threshold();
		$description = gtext('Set per-LUN/per-pool thin provisioning soft threshold.');
		$regexp = '/^(?:|0|[1-9][0-9]*)$/';
		$property->
			set_id('opt_avail_threshold')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_used_threshold() {
		$property = parent::init_opt_used_threshold();
		$description = gtext('Set per-LUN/per-pool thin provisioning soft threshold.');
		$regexp = '/^(?:|0|[1-9][0-9]*)$/';
		$property->
			set_id('opt_used_threshold')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pool_avail_threshold() {
		$property = parent::init_opt_pool_avail_threshold();
		$description = gtext('Set per-LUN/per-pool thin provisioning soft threshold.');
		$regexp = '/^(?:|0|[1-9][0-9]*)$/';
		$property->
			set_id('opt_pool_avail_threshold')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(18)->
			set_maxlength(16)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pool_used_threshold() {
		$property = parent::init_opt_pool_used_threshold();
		$description = gtext('Set per-LUN/per-pool thin provisioning soft threshold.');
		$regexp = '/^(?:|0|[1-9][0-9]{0,8})$/';
		$property->
			set_id('opt_pool_used_threshold')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(12)->
			set_maxlength(10)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_writecache() {
		$property = parent::init_opt_writecache();
		$description = gtext('Set to "off", disables write caching for the LUN, if supported by the backend.');
		$options = [
			'' => gtext('On'),
			'off' => gtext('Off')
		];
		$property->
			set_id('opt_writecache')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_file() {
		$property = parent::init_opt_file();
		$description = gtext('Specifies file or device name to use for backing store.');
		$regexp = '/^\S{0,223}$/';
		$property->
			set_id('opt_file')->
			set_description($description)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_num_threads() {
		$property = parent::init_opt_num_threads();
		$description = gtext('Specifies number of backend threads to use for this LUN.');
		$regexp = '/^(?:|0|[1-9][0-9]{0,8})$/';
		$property->
			set_id('opt_num_threads')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(12)->
			set_maxlength(10)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_opt_capacity() {
		$property = parent::init_opt_capacity();
		$description = gtext('Specifies capacity of backing store (maximum RAM for data). The default value is zero, that disables backing store completely, making all writes go to nowhere, while all reads return zeroes.');
		$regexp = '/^(?:|0|[1-9][0-9]{0,8}[kmgtpezy]b?)$/i';
		$property->
			set_id('opt_capacity')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(14)->
			set_maxlength(12)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_auxparam() {
		$property = parent::init_auxparam();
		$description = gtext('These parameter will be added to this lun.');
		$placeholder = gtext('Enter additional parameter');
		$property->
			set_id('auxparam')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}
