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

class properties_system_advanced extends co_property_container {
	protected $x_adddivsubmittodataframe;
	protected $x_disableconsolemenu;
	protected $x_disablefm;
	protected $x_disablefirmwarecheck;
	protected $x_disablebeep;
	protected $x_microcode_update;
	protected $x_enabletogglemode;
	protected $x_nonsidisksizevalues;
	protected $x_skipviewmode;
	protected $x_disableextensionmenu;
	protected $x_tune_enable;
	protected $x_zeroconf;
	protected $x_powerd;
	protected $x_pwmode;
	protected $x_pwmax;
	protected $x_pwmin;
	protected $x_motd;
	protected $x_shrinkpageheader;
	protected $x_sysconsaver;
	protected $x_sysconsaverblanktime;
	protected $x_enableserialconsole;

	public function get_adddivsubmittodataframe() {
		return $this->x_adddivsubmittodataframe ?? $this->init_adddivsubmittodataframe();
	}
	public function init_adddivsubmittodataframe(): properties_bool {
		$this->x_adddivsubmittodataframe = new properties_bool($this);
		$this->x_adddivsubmittodataframe->
			set_id('adddivsubmittodataframe')->
			set_name('adddivsubmittodataframe')->
			set_title(gtext('Button Location'))->
			set_caption(gtext('Display action buttons in the scrollable area instead of the footer area.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_adddivsubmittodataframe->
			set_message_error(sprintf('%s: %s',$this->x_adddivsubmittodataframe->get_title(),gtext('The value is invalid.')));
		return $this->x_adddivsubmittodataframe;
	}
	public function get_disableconsolemenu() {
		return $this->x_disableconsolemenu ?? $this->init_disableconsolemenu();
	}
	public function init_disableconsolemenu() {
		$this->x_disableconsolemenu = new properties_bool($this);
		$this->x_disableconsolemenu->
			set_id('disableconsolemenu')->
			set_name('disableconsolemenu')->
			set_title(gtext('Console Menu'))->
			set_caption(gtext('Disable console menu.'))->
			set_description(gtext('Changes to this option will take effect after a reboot.'))->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_disableconsolemenu->
			set_message_error(sprintf('%s: %s',$this->x_disableconsolemenu->get_title(),gtext('The value is invalid.')));
		return $this->x_disableconsolemenu;
	}
	public function get_disablefm() {
		return $this->x_disablefm ?? $this->init_disablefm();
	}
	public function init_disablefm() {
		$this->x_disablefm = new properties_bool($this);
		$this->x_disablefm->
			set_id('disablefm')->
			set_name('disablefm')->
			set_title(gtext('File Manager'))->
			set_caption(gtext('Disable file manager.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_disablefm->
			set_message_error(sprintf('%s: %s',$this->x_disablefm->get_title(),gtext('The value is invalid.')));
		return $this->x_disablefm;
	}
	public function get_disablefirmwarecheck() {
		return $this->x_disablefirmwarecheck ?? $this->init_disablefirmwarecheck();
	}
	public function init_disablefirmwarecheck() {
		$link = '<a href="system_firmware.php">' . gtext('System') . ': ' . gtext('Firmware Update') . '</a>';
		$description = sprintf(gtext('Do not let the server check for newer firmware versions when the %s page gets loaded.'),$link);		
		$this->x_disablefirmwarecheck = new properties_bool($this);
		$this->x_disablefirmwarecheck->
			set_id('disablefirmwarecheck')->
			set_name('disablefirmwarecheck')->
			set_title(gtext('Firmware Check'))->
			set_caption(gtext('Disable firmware version check.'))->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_disablefirmwarecheck->
			set_message_error(sprintf('%s: %s',$this->x_disablefirmwarecheck->get_title(),gtext('The value is invalid.')));
		return $this->x_disablefirmwarecheck;
	}
	public function get_disablebeep() {
		return $this->x_disablebeep ?? $this->init_disablebeep();
	}
	public function init_disablebeep() {
		$this->x_disablebeep = new properties_bool($this);
		$this->x_disablebeep->
			set_id('disablebeep')->
			set_name('disablebeep')->
			set_title(gtext('Internal Speaker'))->
			set_caption(gtext('Disable speaker beep on startup and shutdown.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_disablebeep->
			set_message_error(sprintf('%s: %s',$this->x_disablebeep->get_title(),gtext('The value is invalid.')));
		return $this->x_disablebeep;
	}
	public function get_microcode_update() {
		return $this->x_microcode_update ?? $this->init_microcode_update();
	}
	public function init_microcode_update() {
		$this->x_microcode_update = new properties_bool($this);
		$this->x_microcode_update->
			set_id('microcode_update')->
			set_name('microcode_update')->
			set_title(gtext('CPU Microcode Update'))->
			set_caption(gtext('Enable this option to update the CPU microcode on startup.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_microcode_update->
			set_message_error(sprintf('%s: %s',$this->x_microcode_update->get_title(),gtext('The value is invalid.')));
		return $this->x_microcode_update;
	}
	public function get_enabletogglemode() {
		return $this->x_enabletogglemode ?? $this->init_enabletogglemode();
	}
	public function init_enabletogglemode() {
		$this->x_enabletogglemode = new properties_bool($this);
		$this->x_enabletogglemode->
			set_id('enabletogglemode')->
			set_name('enabletogglemode')->
			set_title(gtext('Toggle Mode'))->
			set_caption(gtext('Use toggle button instead of enable/disable buttons.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_enabletogglemode->
			set_message_error(sprintf('%s: %s',$this->x_enabletogglemode->get_title(),gtext('The value is invalid.')));
		return $this->x_enabletogglemode;
	}
	public function get_nonsidisksizevalues() {
		return $this->x_nonsidisksizevalues ?? $this->init_nonsidisksizevalues();
	}
	public function init_nonsidisksizevalues() {
		$this->x_nonsidisksizevalues = new properties_bool($this);
		$this->x_nonsidisksizevalues->
			set_id('nonsidisksizevalues')->
			set_name('nonsidisksizevalues')->
			set_title(gtext('Binary Prefix'))->
			set_caption(gtext('Display disk size values using binary prefixes instead of decimal prefixes.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_nonsidisksizevalues->
			set_message_error(sprintf('%s: %s',$this->x_nonsidisksizevalues->get_title(),gtext('The value is invalid.')));
		return $this->x_nonsidisksizevalues;
	}
	public function get_skipviewmode() {
		return $this->x_skipviewmode ?? $this->init_skipviewmode();
	}
	public function init_skipviewmode() {
		$this->x_skipviewmode = new properties_bool($this);
		$this->x_skipviewmode->
			set_id('skipviewmode')->
			set_name('skipviewmode')->
			set_title(gtext('Skip View Mode'))->
			set_caption(gtext('Enable this option if you want to edit configuration pages directly without the need to switch to edit mode.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_skipviewmode->
			set_message_error(sprintf('%s: %s',$this->x_skipviewmode->get_title(),gtext('The value is invalid.')));
		return $this->x_skipviewmode;
	}
	public function get_disableextensionmenu() {
		return $this->x_disableextensionmenu ?? $this->init_disableextensionmenu();
	}
	public function init_disableextensionmenu() {
		$this->x_disableextensionmenu = new properties_bool($this);
		$this->x_disableextensionmenu->
			set_id('disableextensionmenu')->
			set_name('disableextensionmenu')->
			set_title(gtext('Disable Extension Menu'))->
			set_caption(gtext('Disable scanning of folders for existing extension menus.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_disableextensionmenu->
			set_message_error(sprintf('%s: %s',$this->x_disableextensionmenu->get_title(),gtext('The value is invalid.')));
		return $this->x_disableextensionmenu;
	}
	public function get_tune_enable() {
		return $this->x_tune_enable ?? $this->init_tune_enable();
	}
	public function init_tune_enable() {
		$this->x_tune_enable = new properties_bool($this);
		$this->x_tune_enable->
			set_id('tune_enable')->
			set_name('tune_enable')->
			set_title(gtext('Tuning'))->
			set_caption(gtext('Enable tuning of some kernel variables.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_tune_enable->
			set_message_error(sprintf('%s: %s',$this->x_tune_enable->get_title(),gtext('The value is invalid.')));
		return $this->x_tune_enable;
	}
	public function get_zeroconf() {
		return $this->x_zeroconf ?? $this->init_zeroconf();
	}
	public function init_zeroconf() {
		$this->x_zeroconf = new properties_bool($this);
		$this->x_zeroconf->set_id('zeroconf')->
			set_name('zeroconf')->
			set_title(gtext('Zeroconf/Bonjour'))->
			set_caption(gtext('Enable Zeroconf/Bonjour to advertise services of this device.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_zeroconf->
			set_message_error(sprintf('%s: %s',$this->x_zeroconf->get_title(),gtext('The value is invalid.')));
		return $this->x_zeroconf;
	}
	public function get_powerd() {
		return $this->x_powerd ?? $this->init_powerd();
	}
	public function init_powerd() {
		$this->x_powerd = new properties_bool($this);
		$this->x_powerd->set_id('powerd')->
			set_name('powerd')->
			set_title(gtext('Power Daemon'))->
			set_caption(gtext('Enable the server power control utility.'))->
			set_description(gtext('The powerd utility monitors the server state and sets various power control options accordingly.'))->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_powerd->
			set_message_error(sprintf('%s: %s',$this->x_powerd->get_title(),gtext('The value is invalid.')));
		return $this->x_powerd;
	}
	public function get_pwmode() {
		return $this->x_pwmode ?? $this->init_pwmode();
	}
	public function init_pwmode() {
		$options = [
			'maximum' => gtext('Maximum (Highest Performance)'),
			'hiadaptive' => gtext('Hiadaptive (High Performance)'),
			'adaptive' => gtext('Adaptive (Low Power Consumption)'),
			'minimum' => gtext('Minimum (Lowest Performance)')
		];
		$this->x_pwmode = new properties_list($this);
		$this->x_pwmode->set_id('pwmode')->
			set_name('pwmode')->
			set_title(gtext('Power Mode'))->
			set_description(gtext('Controls the power consumption mode.'))->
			set_defaultvalue('hiadaptive')->
			set_options($options)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_pwmode->
			set_message_error(sprintf('%s: %s',$this->x_pwmode->get_title(),gtext('The value is invalid.')));
		return $this->x_pwmode;
	}
	public function get_pwmax() {
		return $this->x_pwmax ?? $this->init_pwmax();
	}
	public function init_pwmax() {
		$this->x_pwmax = new properties_list($this);
		return $this->x_pwmax;
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
	public function get_pwmin() {
		return $this->x_pwmin ?? $this->init_pwmin();
	}
	public function init_pwmin() {
		$this->x_pwmin = new properties_text($this);
		return $this->x_pwmin;
//		html_inputbox2('pwmin',gtext('CPU Minimum Frequency'),$pconfig['pwmin'],gtext('An empty field is default.'),false,5);
	}
	public function get_motd() {
		return $this->x_motd ?? $this->init_motd();
	}
	public function init_motd() {
		$this->x_motd = new properties_text($this);
		return $this->x_motd;
//		html_textarea2('motd',gtext('MOTD'),$pconfig['motd'],gtext('Message of the day.'),false,65,7,false,false);
	}
	public function get_shrinkpageheader() {
		return $this->x_shrinkpageheader ?? $this->init_shrinkpageheader();
	}
	public function init_shrinkpageheader() {
		$this->x_shrinkpageheader = new properties_bool($this);
		$this->x_shrinkpageheader->
			set_id('shrinkpageheader')->
			set_name('shrinkpageheader')->
			set_title(gtext('Shrink Page Header'))->
			set_caption(gtext('Enable this option to reduce the height of the page header to a minimum.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_shrinkpageheader->
			set_message_error(sprintf('%s: %s',$this->x_shrinkpageheader->get_title(),gtext('The value is invalid.')));
		return $this->x_shrinkpageheader;
	}
	public function get_sysconsaver() {
		return $this->x_sysconsaver ?? $this->init_sysconsaver();
	}
	public function init_sysconsaver() {
		$this->x_sysconsaver = new properties_bool($this);
		$this->x_sysconsaver->
			set_id('sysconsaver')->
			set_name('sysconsaver')->
			set_title(gtext('Console Screensaver'))->
			set_caption(gtext('Enable console screensaver.'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_sysconsaver->
			set_message_error(sprintf('%s: %s',$this->x_sysconsaver->get_title(),gtext('The value is invalid.')));
		return $this->x_sysconsaver;
	}
	public function get_sysconsaverblanktime() {
		return $this->x_sysconsaverblanktime ?? $this->init_sysconsaverblanktime();
	}
	public function init_sysconsaverblanktime() {
		$this->x_sysconsaverblanktime = new properties_text($this);
		return $this->x_sysconsaverblanktime;
//		html_inputbox2('sysconsaverblanktime',gtext('Blank Time'),$pconfig['sysconsaverblanktime'],gtext('Turn the monitor to standby after N seconds.'),true,5);
	}
	public function get_enableserialconsole() {
		return $this->x_enableserialconsole ?? $this->init_enableserialconsole();
	}
	public function init_enableserialconsole() {
		$this->x_enableserialconsole = new properties_bool($this);
		$this->x_enableserialconsole->
			set_id('enableserialconsole')->
			set_name('enableserialconsole')->
			set_title(gtext('Serial Console'))->
			set_caption(gtext('Enable serial console.'))->
			set_description(sprintf('<span class="red"><strong>%s</strong></span><br />%s',gtext('The COM port in BIOS has to be enabled before enabling this option.'), gtext('Changes to this option will take effect after a reboot.')))->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true);
		$this->x_enableserialconsole->
			set_message_error(sprintf('%s: %s',$this->x_enableserialconsole->get_title(),gtext('The value is invalid.')));
		return $this->x_enableserialconsole;
	}
}
