<?php
/*
	properties_system_advanced.php

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

class properties_system_advanced {
	public $adddivsubmittodataframe;
	public $disableconsolemenu;
	public $disablefm;
	public $disablefirmwarecheck;
	public $disablebeep;
	public $microcode_update;
	public $enabletogglemode;
	public $nonsidisksizevalues;
	public $skipviewmode;
	public $disableextensionmenu;
	public $tune_enable;
	public $zeroconf;
	public $powerd;
	public $pwmode;
	public $pwmax;
	public $pwmin;
	public $motd;
	public $shrinkpageheader;
	public $sysconsaver;
	public $sysconsaverblanktime;
	public $enableserialconsole;

	public function __construct() {
		$this->load();
	}
	public function load() {
		$this->adddivsubmittodataframe = $this->prop_adddivsubmittodataframe();
		$this->disableconsolemenu = $this->prop_disableconsolemenu();
		$this->disablefm = $this->prop_disablefm();
		$this->disablefirmwarecheck = $this->prop_disablefirmwarecheck();
		$this->disablebeep = $this->prop_disablebeep();
		$this->microcode_update = $this->prop_microcode_update();
		$this->enabletogglemode = $this->prop_enabletogglemode();
		$this->nonsidisksizevalues = $this->prop_nonsidisksizevalues();
		$this->skipviewmode = $this->prop_skipviewmode();
		$this->disableextensionmenu = $this->prop_disableextensionmenu();
		$this->tune_enable = $this->prop_tune_enable();
		$this->zeroconf = $this->prop_zeroconf();
		$this->powerd = $this->prop_powerd();
		$this->pwmode = $this->prop_pwmode();
		$this->pwmax = $this->prop_pwmax();
		$this->pwmin = $this->prop_pwmin();
		$this->motd = $this->prop_motd();
		$this->shrinkpageheader = $this->prop_shrinkpageheader();
		$this->sysconsaver = $this->prop_sysconsaver();
		$this->sysconsaverblanktime = $this->prop_sysconsaverblanktime();
		$this->enableserialconsole = $this->prop_enableserialconsole();
		return $this;
	}
	private function prop_adddivsubmittodataframe(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('adddivsubmittodataframe');
		$o->set_name('adddivsubmittodataframe');
		$o->set_title(gtext('Button Location'));
		$o->set_caption(gtext('Display action buttons in the scrollable area instead of the footer area.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_disableconsolemenu(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('disableconsolemenu');
		$o->set_name('disableconsolemenu');
		$o->set_title(gtext('Console Menu'));
		$o->set_caption(gtext('Disable console menu.'));
		$o->set_description(gtext('Changes to this option will take effect after a reboot.'));
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_disablefm(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('disablefm');
		$o->set_name('disablefm');
		$o->set_title(gtext('File Manager'));
		$o->set_caption(gtext('Disable file manager.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_disablefirmwarecheck(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('disablefirmwarecheck');
		$o->set_name('disablefirmwarecheck');
		$o->set_title(gtext('Firmware Check'));
		$o->set_caption(gtext('Disable firmware version check.'));
		$link = '<a href="system_firmware.php">' . gtext('System') . ': ' . gtext('Firmware Update') . '</a>';
		$helpinghand = sprintf(gtext('Do not let the server check for newer firmware versions when the %s page gets loaded.'),$link);		
		$o->set_description($helpinghand);
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_disablebeep(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('disablebeep');
		$o->set_name('disablebeep');
		$o->set_title(gtext('Internal Speaker'));
		$o->set_caption(gtext('Disable speaker beep on startup and shutdown.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_microcode_update(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('microcode_update');
		$o->set_name('microcode_update');
		$o->set_title(gtext('CPU Microcode Update'));
		$o->set_caption(gtext('Enable this option to update the CPU microcode on startup.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_enabletogglemode(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('enabletogglemode');
		$o->set_name('enabletogglemode');
		$o->set_title(gtext('Toggle Mode'));
		$o->set_caption(gtext('Use toggle button instead of enable/disable buttons.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_nonsidisksizevalues(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('nonsidisksizevalues');
		$o->set_name('nonsidisksizevalues');
		$o->set_title(gtext('Binary Prefix'));
		$o->set_caption(gtext('Display disk size values using binary prefixes instead of decimal prefixes.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_skipviewmode(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('skipviewmode');
		$o->set_name('skipviewmode');
		$o->set_title(gtext('Skip View Mode'));
		$o->set_caption(gtext('Enable this option if you want to edit configuration pages directly without the need to switch to edit mode.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_disableextensionmenu(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('disableextensionmenu');
		$o->set_name('disableextensionmenu');
		$o->set_title(gtext('Disable Extension Menu'));
		$o->set_caption(gtext('Disable scanning of folders for existing extension menus.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_tune_enable(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('tune_enable');
		$o->set_name('tune_enable');
		$o->set_title(gtext('Tuning'));
		$o->set_caption(gtext('Enable tuning of some kernel variables.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_zeroconf(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('zeroconf');
		$o->set_name('zeroconf');
		$o->set_title(gtext('Zeroconf/Bonjour'));
		$o->set_caption(gtext('Enable Zeroconf/Bonjour to advertise services of this device.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_powerd(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('powerd');
		$o->set_name('powerd');
		$o->set_title(gtext('Power Daemon'));
		$o->set_caption(gtext('Enable the server power control utility.'));
		$o->set_description(gtext('The powerd utility monitors the server state and sets various power control options accordingly.'));
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_pwmode(): properties_list {
		$o = new properties_list($this);
		$o->set_id('pwmode');
		$o->set_name('pwmode');
		$o->set_title(gtext('Power Mode'));
		$o->set_description(gtext('Controls the power consumption mode.'));
		$o->set_defaultvalue('hiadaptive');
		$options = [
			'maximum' => gtext('Maximum (Highest Performance)'),
			'hiadaptive' => gtext('Hiadaptive (High Performance)'),
			'adaptive' => gtext('Adaptive (Low Power Consumption)'),
			'minimum' => gtext('Minimum (Lowest Performance)')
		];
		$o->set_options($options);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_pwmax(): properties_list {
		$o = new properties_list($this);
		return $o;
/*
		$clocks = @exec("/sbin/sysctl -q -n dev.cpu.0.freq_levels");
		$a_freq = [];
		if(!empty($clocks)):
			$a_tmp = preg_split("/\s/", $clocks);
			foreach ($a_tmp as $val):
				list($freq,$tmp) = preg_split("/\//", $val);
				if(!empty($freq)):
					$a_freq[] = $freq;
				endif;
			endforeach;
		endif;
		html_inputbox2('pwmax',gtext('CPU Maximum Frequency'),$pconfig['pwmax'],sprintf('%s %s',gtext('CPU frequencies:'),join(', ',$a_freq)) . '.<br />' . gtext('An empty field is default.'),false,5);
*/
	}
	private function prop_pwmin(): properties_text {
		$o = new properties_text($this);
		return $o;
//		html_inputbox2('pwmin',gtext('CPU Minimum Frequency'),$pconfig['pwmin'],gtext('An empty field is default.'),false,5);
	}
	private function prop_motd(): properties_text {
		$o = new properties_text($this);
		return $o;
//		html_textarea2('motd',gtext('MOTD'),$pconfig['motd'],gtext('Message of the day.'),false,65,7,false,false);
	}
	private function prop_shrinkpageheader(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('shrinkpageheader');
		$o->set_name('shrinkpageheader');
		$o->set_title(gtext('Shrink Page Header'));
		$o->set_caption(gtext('Enable this option to reduce the height of the page header to a minimum.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_sysconsaver(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('sysconsaver');
		$o->set_name('sysconsaver');
		$o->set_title(gtext('Console Screensaver'));
		$o->set_caption(gtext('Enable console screensaver.'));
		$o->set_description('');
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
	private function prop_sysconsaverblanktime(): properties_text {
		$o = new properties_text($this);
		return $o;
//		html_inputbox2('sysconsaverblanktime',gtext('Blank Time'),$pconfig['sysconsaverblanktime'],gtext('Turn the monitor to standby after N seconds.'),true,5);
	}
	private function prop_enableserialconsole(): properties_bool {
		$o = new properties_bool($this);
		$o->set_id('enableserialconsole');
		$o->set_name('enableserialconsole');
		$o->set_title(gtext('Serial Console'));
		$o->set_caption(gtext('Enable serial console.'));
		$o->set_description(sprintf('<span class="red"><strong>%s</strong></span><br />%s',gtext('The COM port in BIOS has to be enabled before enabling this option.'), gtext('Changes to this option will take effect after a reboot.')));
		$o->set_defaultvalue(false);
		$o->filter_use_default();
		$o->set_editableonadd(true);
		$o->set_editableonmodify(true);
		$o->set_message_error(sprintf('%s: %s',$o->get_title(),gtext('The value is invalid.')));
		return $o;
	}
}
