<?php
/*
	properties_disks_zfs_settings.php

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
require_once 'properties.php';

class properties_disks_zfs_settings extends co_property_container {
	protected $x_showusedavail;
	protected $x_capacity_warning;
	protected $x_capacity_critical;
	
	public function get_showusedavail() {
		return $this->x_showusedavail ?? $this->init_showusedavail();
	}
	public function init_showusedavail() {
		$property = $this->x_showusedavail = new property_bool($this);
		$property->
			set_name('showusedavail')->
			set_title(gtext('Show Used/Avail'));
		$caption = gtext('Display Used/Avail information from the filesystem instead of the Alloc/Free information from the pool.');
		$description = gtext('Used/Avail lists storage information after all redundancy is taken into account but is impacted by compression, deduplication and quotas. Alloc/Free lists the raw storage information of a pool.');
		$property->
			set_id('showusedavail')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_capacity_warning() {
		return $this->x_capacity_warning ?? $this->init_capacity_warning();
	}
	public function test_capacity_warning($value = '') {
		if(is_string($value) && preg_match('/^(8\d)?$/',$value)):
			return $value;
		endif;
		return NULL;
	}
	public function init_capacity_warning() {
		$property = $this->x_capacity_warning = new property_int($this);
		$property->
			set_name('capacity_warning')->
			set_title(gtext('Capacity Warning Threshold'));
		$caption = gtext('Set the warning threshold to a value between 80 and 89.');
		$description =
			gtext('An alert email is sent when the capacity of a pool exceeds the warning threshold.') .
			' ' .
			gtext('A cron job must be setup to schedule script /etc/capacitycheck.zfs.');
		$property->
			set_id('capacity_warning')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue('')->
			set_size(3)->
			set_maxlength(4)->
			set_placeholder('80')->
			set_filter(FILTER_CALLBACK)->
			set_filter_options([$this,'test_capacity_warning'])->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Must be a number between 80 and 89.')));
		return $property;
	}
	public function get_capacity_critical() {
		return $this->x_capacity_critical ?? $this->init_capacity_critical();
	}
	public function test_capacity_critical($value = '') {
		if(is_string($value) && preg_match('/^(9[0-5])?$/',$value)):
			return $value;
		endif;
		return NULL;
	}
	public function init_capacity_critical() {
		$property = $this->x_capacity_critical = new property_int($this);
		$property->
			set_name('capacity_critical')->
			set_title(gtext('Capacity Critical Threshold'));
		$caption = gtext('Set the critical threshold to a value between 90 and 95.');
		$description =
			gtext('An alert email is sent when the capacity of a pool exceeds the critical threshold.') .
			' ' .
			gtext('A cron job must be setup to schedule script /etc/capacitycheck.zfs.');
		$property->
			set_id('capacity_critical')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue('')->
			set_size(3)->
			set_maxlength(4)->
			set_placeholder('90')->
			set_filter(FILTER_CALLBACK)->
			set_filter_options([$this,'test_capacity_critical'])->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Must be a number between 90 and 95.')));
		return $property;
	}
}
