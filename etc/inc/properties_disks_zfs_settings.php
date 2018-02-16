<?php
/*
	properties_disks_zfs_settings.php

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

class properties_disks_zfs_settings {
	public $showusedavail;
	public $capacity_warning;
	public $capacity_critical;
	
	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->showusedavail = $this->prop_showusedavail();
		$this->capacity_warning = $this->prop_capacity_warning();
		$this->capacity_critical = $this->prop_capacity_critical();
		return $this;
	}
	private function prop_showusedavail(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('showusedavail');
		$o->set_name('showusedavail');
		$o->set_title(gtext('Show Used/Avail'));
		$o->set_caption(gtext('Display Used/Avail information from the filesystem instead of the Alloc/Free information from the pool.'));
		$o->set_description('Used/Avail lists storage information after all redundancy is taken into account but is impacted by compression, deduplication and quotas. Alloc/Free lists the raw storage information of a pool.');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_capacity_warning(): properties_int {
		$o = new properties_int($this);
		$o->set_id('capacity_warning');
		$o->set_name('capacity_warning');
		$o->set_title(gtext('Capacity Warning Threshold'));
		$o->set_caption(gtext('Set the warning threshold to a value between 80 and 89.'));
		$o->set_description(gtext('An alert email is sent when the capacity of a pool exceeds the warning threshold.') . ' ' . gtext('A cron job must be setup to schedule script /etc/capacitycheck.zfs.'));
		$o->set_defaultvalue('');
		$o->set_size(3);
		$o->set_maxlength(4);
		$o->set_min(80)->set_max(89);
		$o->set_placeholder('80');
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('Must be a number between 80 and 89.')));
		return $o;
	}
	private function prop_capacity_critical(): properties_int {
		$o = new properties_int($this);
		$o->set_id('capacity_critical');
		$o->set_name('capacity_critical');
		$o->set_title(gtext('Capacity Critical Threshold'));
		$o->set_caption(gtext('Set the critical threshold to a value between 90 and 95.'));
		$o->set_description(gtext('An alert email is sent when the capacity of a pool exceeds the critical threshold.') . ' ' . gtext('A cron job must be setup to schedule script /etc/capacitycheck.zfs.'));
		$o->set_defaultvalue('');
		$o->set_size(3);
		$o->set_maxlength(4);
		$o->set_min(90)->set_max(95);
		$o->set_placeholder('90');
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('Must be a number between 90 and 95.')));
		return $o;
	}
}
