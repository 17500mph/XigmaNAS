<?php
/*
  properties_diag_log_settings.php

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
require_once 'properties.php';

class properties_diag_log_settings {
	public $reverse;
	public $nentries;
	public $resolve;
	public $disablecomp;
	public $disablesecure;
	public $system;
	public $ftp;
	public $rsyncd;
	public $sshd;
	public $smartd;
	public $daemon;
	public $ipaddr;
	public $enable;
	
	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->reverse = $this->prop_reverse();
		$this->nentries = $this->prop_nentries();
		$this->resolve = $this->prop_resolve();
		$this->disablecomp = $this->prop_disablecomp();
		$this->disablesecure = $this->prop_disablesecure();
		$this->system = $this->prop_system();
		$this->ftp = $this->prop_ftp();
		$this->rsyncd = $this->prop_rsyncd();
		$this->sshd = $this->prop_sshd();
		$this->smartd = $this->prop_smartd();
		$this->daemon = $this->prop_daemon();
		$this->ipaddr = $this->prop_ipaddr();
		$this->enable = $this->prop_enable();
		return $this;
	}
	private function prop_reverse(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('reverse');
		$o->set_name('reverse');
		$o->set_title(gtext('Log Order'));
		$o->set_caption(gtext('Show log entries in reverse order (newest entries on top).'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_nentries(): properties {
		$o = new properties($this);
		$o->set_id('nentries');
		$o->set_name('nentries');
		$o->set_title(gtext('Show Log Entries'));
		$o->set_description('');
		$o->set_defaultvalue(50);
		$o->set_filter(FILTER_VALIDATE_INT);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL,'min_range' => 5,'max_range' => 1000]);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The number of log entries to show must be between 5 and 1000.')));
		return $o;
	}
	private function prop_resolve(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('resolve');
		$o->set_name('resolve');
		$o->set_title(gtext('Resolve IP'));
		$o->set_caption(gtext('Resolve IP addresses to hostnames.'));
		$helpinghand = [
			gtext('Hint'),
			[': ',true],
			[gtext('If this option is checked, IP addresses in the server logs are resolved to their hostnames where possible.'),true],
			[gtext('Warning'),'red'],
			[': ',true],
			[gtext('This can cause a huge delay in loading the log page!'),true]
		];
		$o->set_description($helpinghand);
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_disablecomp(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('disablecomp');
		$o->set_name('disablecomp');
		$o->set_title(gtext('Compression'));
		$o->set_caption(gtext('Disable the compression of repeating lines.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_disablesecure(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('disablesecure');
		$o->set_name('disablesecure');
		$o->set_title(gtext('Remote Syslog Messages'));
		$o->set_caption(gtext('Accept remote syslog messages.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_system(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('system');
		$o->set_name('system');
		$o->set_title(gtext('System Events'));
		$o->set_caption(gtext('Send system event messages.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_ftp(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('ftp');
		$o->set_name('ftp');
		$o->set_title(gtext('FTP Events'));
		$o->set_caption(gtext('Send FTP event messages.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_rsyncd(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('rsyncd');
		$o->set_name('rsyncd');
		$o->set_title(gtext('RSYNC Events'));
		$o->set_caption(gtext('Send RSYNC event messages.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_sshd(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('sshd');
		$o->set_name('sshd');
		$o->set_title(gtext('SSH Events'));
		$o->set_caption(gtext('Send SSH event messages.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_smartd(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('smartd');
		$o->set_name('smartd');
		$o->set_title(gtext('S.M.A.R.T. Events'));
		$o->set_caption(gtext('Send S.M.A.R.T. event messages.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_daemon(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('daemon');
		$o->set_name('daemon');
		$o->set_title(gtext('Daemon Events'));
		$o->set_caption(gtext('Send daemon event messages.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_ipaddr(): properties {
		$o = new properties($this);
		$o->set_id('ipaddr');
		$o->set_name('ipaddr');
		$o->set_title(gtext('IP Address'));
		$o->set_description(gtext('IP address of the remote syslog server.'));
		$o->set_defaultvalue('');
		$o->set_filter(FILTER_VALIDATE_IP);
		$o->set_filter_flags(FILTER_REQUIRE_SCALAR);
		$o->set_filter_options(['default' => NULL]);
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('This is not a valid IP Address.')));
		return $o;
	}
	private function prop_enable(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('enable');
		$o->set_name('enable');
		$o->set_title(gtext('Remote Syslog Server'));
		$o->set_caption(gtext('Enable'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
}
