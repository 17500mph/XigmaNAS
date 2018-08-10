<?php
/*
	properties_services_ctl_lun.php

	Part of XigmaNAS (https://www.xigmanas.com).
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
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS, either expressed or implied.
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
			set_title(gettext('LUN Name'));
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
			set_title(gettext('Backend'));
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
			set_title(gettext('Block Size'));
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
			set_title(gettext('CTL LUN'));
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
			set_title(gettext('Device ID'));
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
			set_title(gettext('Device Type'));
		return $property;
	}
	protected $x_passthrough_address;
	public function get_passthrough_address() {
		return $this->x_passthrough_address ?? $this->init_passthrough_address();
	}
	public function init_passthrough_address() {
		$property = $this->x_passthrough_address = new property_text($this);
		$property->
			set_name('passthrough_address')->
			set_title(gettext('Passthrough Address'));
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
			set_title(gettext('Path'));
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
			set_title(gettext('Serial Number'));
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
			set_title(gettext('Size'));
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
			set_title(gettext('LUN Vendor'));
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
			set_title(gettext('LUN Product'));
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
			set_title(gettext('LUN Revision'));
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
			set_title(gettext('LUN SCSI Name'));
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
			set_title(gettext('LUN EUI-64	identifier'));
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
			set_title(gettext('LUN NAA Identifier'));
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
			set_title(gettext('UUID'));
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
			set_title(gettext('HA Role'));
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
			set_title(gettext('Insecure TPC'));
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
			set_title(gettext('Read Cache'));
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
			set_title(gettext('Read Only'));
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
			set_title(gettext('Removable'));
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
			set_title(gettext('Reordering'));
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
			set_title(gettext('Serialize Sequence'));
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
			set_title(gettext('Physical Block Size'));
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
			set_title(gettext('Physical Block Offset'));
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
			set_title(gettext('UNMAP Block Size'));
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
			set_title(gettext('UNMAP Block Offset'));
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
			set_title(gettext('RPM'));
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
			set_title(gettext('Form Factor'));
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
			set_title(gettext('Provisioning Type'));
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
			set_title(gettext('UNMAP'));
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
			set_title(gettext('UNMAP Maximum LBA'));
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
			set_title(gettext('UNMAP Maximum Descriptors'));
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
			set_title(gettext('Write Same Maximum LBA'));
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
			set_title(gettext('Avail Threshold'));
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
			set_title(gettext('Used Threshold'));
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
			set_title(gettext('Pool Avail Threshold'));
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
			set_title(gettext('Pool Used Threshold'));
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
			set_title(gettext('Write Cache'));
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
			set_title(gettext('File'));
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
			set_title(gettext('Threads'));
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
			set_title(gettext('Capacity'));
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
			set_title(gettext('Additional Parameter'));
		return $property;
	}
}
class ctl_lun_edit_properties extends ctl_lun_properties {
	public function init_name() {
		$property = parent::init_name();
		$description = gettext('Name of the LUN.');
		$placeholder = gettext('LUN Name');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_backend() {
		$property = parent::init_backend();
		$description = gettext('The CTL backend to use for a given LUN.');
		$options = [
			'' => gettext('Default'),
			'block' => gettext('Block'),
//			'passthrough' => gettext('Passthrough'),
			'ramdisk' => gettext('RAM Disk')
		];
		$property->
			set_id('backend')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_blocksize() {
		$property = $property = parent::init_blocksize();
		$description = gettext('The blocksize visible to the initiator.');
		$options = [
			'' => gettext('Default'),
			'512' => gettext('512 Bytes'),
			'2048' => gettext('2 KiB'),
			'4096' => gettext('4 KiB'),
			'8192' => gettext('8 KiB'),
			'16384' => gettext('16 KiB'),
			'32738' => gettext('32 KiB'),
			'65536' => gettext('64 KiB')
		];
		$property->
			set_id('blocksize')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_ctl_lun() {
		$property = parent::init_ctl_lun();
		$description = gettext('Global numeric identifier to use for a given LUN inside CTL.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_device_id() {
		//	The device-id shall be 48 characters width,
		//	Compatibility istgt: 16 bytes "iSCSI Disk     " + 32 bytes aligned serial number
		$property = parent::init_device_id();
		$description = gettext('The SCSI Device Identification string presented to the initiator.');
		$placeholder = gettext('Device ID');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_device_type() {
		$property = parent::init_device_type();
		$description = gettext('Specify the SCSI device type to use when creating the LUN.');
		$options = [
			'' => gettext('Undefined'),
			'disk' => gettext('Disk'),
			'direct' => gettext('Direct'),
			'processor' => gettext('Processor'),
			'cd' => gettext('CD'),
			'cdrom' => gettext('CD-ROM'),
			'dvd' => gettext('DVD'),
			'dvdrom' => gettext('DVD-ROM'),
/*
			'0' => gettext('0'),
			'1' => gettext('1'),
			'2' => gettext('2'),
			'3' => gettext('3'),
			'4' => gettext('4'),
			'5' => gettext('5'),
			'6' => gettext('6'),
			'7' => gettext('7'),
			'8' => gettext('8'),
			'9' => gettext('9'),
			'10' => gettext('10'),
			'11' => gettext('11'),
			'12' => gettext('12'),
			'13' => gettext('13'),
			'14' => gettext('14'),
			'15' => gettext('15'),
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_passthrough_address() {
		$property = parent::init_passthrough_address();
		$description = gettext('Enter passthrough device address bus:path:lun');
		$regexp = '/^(?:|[0-9]+:[0-9]+:[0-9]+)$/';
		$property->
			set_id('passthrough_address')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(20)->
			set_maxlength(18)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_path() {
		$property = parent::init_path();
		$description = gettext('The path to the file, device node, or zfs volume used to back the LUN.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_serial() {
		$property = parent::init_serial();
		$description = gettext('The SCSI serial number presented to the initiator.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_size() {
		$property = parent::init_size();
		$description = gettext('The size of the LUN.');
		$regexp = '/^(?:|0|[1-9][0-9]{0,16}[kmgtpezy]b?)$/i';
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_vendor() {
		$property = parent::init_opt_vendor();
		$description = gettext('Specifies LUN vendor string up to 8 chars.');
		$placeholder = 'FreeBSD';
		$regexp = '/^.{0,8}$/';
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_product() {
		$property = parent::init_opt_product();
		$description = gettext('Specifies LUN product string up to 16 chars.');
		$placeholder = 'iSCSI Disk';
		$regexp = '/^.{0,16}$/';
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_revision() {
		$property = parent::init_opt_revision();
		$description = gettext('Specifies LUN revision string up to 4 chars.');
		$placeholder = '0123';
		$regexp = '/^.{0,4}$/';
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_scsiname() {
		$property = parent::init_opt_scsiname();
		$description = gettext('Specifies LUN SCSI name string.');
		$placeholder = gettext('SCSI Name');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_eui() {
		$property = parent::init_opt_eui();
		$description = gettext('Specifies LUN EUI-64 identifier.');
		$placeholder = gettext('EUI-64');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_naa() {
		$property = parent::init_opt_naa();
		$description = gettext('Specifies LUN NAA identifier.');
		$placeholder = gettext('NAA Identifier');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_uuid() {
		$property = parent::init_opt_uuid();
		$description = gettext('Specifies LUN locally assigned RFC 4122 UUID.');
		$placeholder = gettext('UUID');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_ha_role() {
		$property = parent::init_opt_ha_role();
		$description = gettext('Setting to "primary" or "secondary" overrides default role of the node in HA cluster.');
		$options = [
			'' => gettext('Default'),
			'primary' => gettext('Primary'),
			'secondary' => gettext('Secondary')
		];
		$property->
			set_id('opt_ha_role')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_insecure_tpc() {
		$property = parent::init_opt_insecure_tpc();
		$description = gettext('Setting to "on" allows EXTENDED COPY command sent to this LUN access other LUNs on this host, not accessible otherwise. This allows to offload copying between different iSCSI targets residing on the same host in trusted environments.');
		$options = [
			'' => gettext('Off'),
			'on' => gettext('On')
		];
		$property->
			set_id('opt_insecure_tpc')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_readcache() {
		$property = parent::init_opt_readcache();
		$description = gettext('Set to "off", disables read caching for the LUN, if supported by the backend.');
		$options = [
			'' => gettext('On'),
			'off' => gettext('Off')
		];
		$property->
			set_id('opt_readcache')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_readonly() {
		$property = parent::init_opt_readonly();
		$description = gettext('Set to "on", blocks all media write operations to the LUN reporting it as write protected.');
		$options = [
			'' => gettext('Off'),
			'on' => gettext('On')
		];
		$property->
			set_id('opt_readonly')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_removable() {
		$property = parent::init_opt_removable();
		$description = gettext('Set to "on" makes LUN removable.');
		$options = [
			'' => gettext('Off'),
			'on' => gettext('On')
		];
		$property->
			set_id('opt_removable')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_reordering() {
		$property = parent::init_opt_reordering();
		$description = gettext('Set to "unrestricted", allows target to process commands with SIMPLE task attribute in arbitrary order.');
		$options = [
			'' => gettext('Restricted'),
			'unrestricted' => gettext('Unrestricted')
		];
		$property->
			set_id('opt_reordering')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_serseq() {
		$property = parent::init_opt_serseq();
		$description = '';
		$options = [
			'' => gettext('Default'),
			'off' => gettext('Allow consecutive read/writes to be issued in parallel'),
			'on' => gettext('Serialize consecutive reads/writes'),
			'read' => gettext('Serialize consecutive reads')
		];
		$property->
			set_id('opt_serseq')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pblocksize() {
		$property = parent::init_opt_pblocksize();
		$description = gettext('Specify physical block size of the device.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pblockoffset() {
		$property = parent::init_opt_pblockoffset();
		$description = gettext('Specify physical block offset of the device.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_ublocksize() {
		$property = parent::init_opt_ublocksize();
		$description = gettext('Specify UNMAP block size of the device.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_ublockoffset() {
		$property = parent::init_opt_ublockoffset();
		$description = gettext('Specify UNMAP block offset of the device.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_rpm() {
		$property = parent::init_opt_rpm();
		$description = gettext('Specifies medium rotation rate of the device.');
		//	0: not reported
		//	1: non-rotating (SSD)
		//	>1024: value in	revolutions per minute
		//	99999: max rpm
		$regexp = '/^(?:|[01]|102[5-9]|10[3-9][0-9]|1[1-9][0-9]{2}|[2-9][0-9]{3}|[1-9][0-9]{4})$/';
		$property->
			set_id('opt_rpm')->
			set_defaultvalue('')->
			set_description($description)->
			set_size(7)->
			set_maxlength(5)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_formfactor() {
		$property = parent::init_opt_formfactor();
		$description = gettext('Specifies nominal form factor of the device.');
		$options = [
			'' => gettext('Undefined'),
			'0' => gettext('Not Reported'),
			'1' => gettext('5.25"'),
			'2' => gettext('3.5"'),
			'3' => gettext('2.5"'),
			'4' => gettext('1.8"'),
			'5' => gettext('less then 1.8".')
		];
		$property->
			set_id('opt_formfactor')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_provisioning_type() {
		$property = parent::init_opt_provisioning_type();
		$description = gettext('When UNMAP support is enabled, this option specifies provisioning type.');
		$options = [
			'' => gettext('Default'),
			'resource' => gettext('Resource Provisioning'),
			'thin' => gettext('Thin Provisioning'),
			'unknown' => gettext('Unknown')
		];
		$property->
			set_id('opt_provisioning_type')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_unmap() {
		$property = parent::init_opt_unmap();
		$description = gettext('Setting to "on" or "off" controls UNMAP support for the logical unit. Default value is "on" if supported by the backend.');
		$options = [
			'' => gettext('Default'),
			'on' => gettext('On'),
			'off' => gettext('Off')
		];
		$property->
			set_id('opt_unmap')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_unmap_max_lba() {
		$property = parent::init_opt_unmap_max_lba();
		$description = gettext('Specify maximum allowed number of LBAs per UNMAP command to report in Block Limits VPD page.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_unmap_max_descr() {
		$property = parent::init_opt_unmap_max_descr();
		$description = gettext('Specify maximum allowed number of block descriptors per UNMAP command to report in Block Limits VPD page.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_write_same_max_lba() {
		$property = parent::init_opt_write_same_max_lba();
		$description = gettext('Specify maximum allowed number of LBAs per WRITE SAME command to report in Block Limits VPD page.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_avail_threshold() {
		$property = parent::init_opt_avail_threshold();
		$description = gettext('Set per-LUN/per-pool thin provisioning soft threshold.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_used_threshold() {
		$property = parent::init_opt_used_threshold();
		$description = gettext('Set per-LUN/per-pool thin provisioning soft threshold.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pool_avail_threshold() {
		$property = parent::init_opt_pool_avail_threshold();
		$description = gettext('Set per-LUN/per-pool thin provisioning soft threshold.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_pool_used_threshold() {
		$property = parent::init_opt_pool_used_threshold();
		$description = gettext('Set per-LUN/per-pool thin provisioning soft threshold.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_writecache() {
		$property = parent::init_opt_writecache();
		$description = gettext('Set to "off", disables write caching for the LUN, if supported by the backend.');
		$options = [
			'' => gettext('On'),
			'off' => gettext('Off')
		];
		$property->
			set_id('opt_writecache')->
			set_description($description)->
			set_options($options)->
			set_defaultvalue('')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_file() {
		$property = parent::init_opt_file();
		$description = gettext('Specifies file or device name to use for backing store.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_num_threads() {
		$property = parent::init_opt_num_threads();
		$description = gettext('Specifies number of backend threads to use for this LUN.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_opt_capacity() {
		$property = parent::init_opt_capacity();
		$description = gettext('Specifies capacity of backing store (maximum RAM for data). The default value is zero, that disables backing store completely, making all writes go to nowhere, while all reads return zeroes.');
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
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_auxparam() {
		$property = parent::init_auxparam();
		$description = gettext('These parameter will be added to this lun.');
		$placeholder = gettext('Enter additional parameter');
		$property->
			set_id('auxparam')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
}
