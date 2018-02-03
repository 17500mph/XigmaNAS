<?php
/*
	system_email.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'email.inc';

$sphere_scriptname = basename(__FILE__);
array_make_branch($config,'system','email');
$pconfig['from'] = $config['system']['email']['from'];
$pconfig['server'] = $config['system']['email']['server'];
$pconfig['port'] = $config['system']['email']['port'];
$pconfig['auth'] = isset($config['system']['email']['auth']);
$pconfig['authmethod'] = $config['system']['email']['authmethod'];
$pconfig['starttls'] = isset($config['system']['email']['starttls']);
$pconfig['tls_certcheck'] = $config['system']['email']['tls_certcheck'];
$pconfig['tls_use_default_trust_file'] = isset($config['system']['email']['tls_use_default_trust_file']);
$pconfig['tls_trust_file'] = $config['system']['email']['tls_trust_file'] ?? '';
$pconfig['tls_crl_file'] = $config['system']['email']['tls_crl_file'] ?? '';
$pconfig['tls_fingerprint'] = $config['system']['email']['tls_fingerprint'] ?? '';
$pconfig['security'] = $config['system']['email']['security'];
$pconfig['username'] = $config['system']['email']['username'];
$pconfig['password'] = $config['system']['email']['password'];
$pconfig['passwordconf'] = $pconfig['password'];
$pconfig['sendto'] = isset($config['system']['email']['sendto']) ? $config['system']['email']['sendto'] : (isset($config['system']['email']['from']) ? $config['system']['email']['from'] : '');

if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	$reqdfields = [];
	$reqdfieldsn = [];
	$reqdfieldst = [];
	// Input validation.
	if(isset($_POST['auth'])):
		$reqdfields = ['username','password'];
		$reqdfieldsn = [gtext('Username'),gtext('Password')];
		$reqdfieldst = ['string','string'];
	endif;
	$reqdfields = ['from','sendto','server','port'];
	$reqdfieldsn = [gtext('From Email Address'),
		gtext('To Email Address'),
		gtext('SMTP Server'),
		gtext('Port')];
	$reqdfieldst = ['string','string','string','string'];
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
	// Check for a password mismatch.
	if(isset($_POST['auth']) && ($_POST['password'] !== $_POST['passwordconf'])) :
		$input_errors[] = gtext('The passwords do not match.');
	endif;
	if(empty($input_errors)):
		$config['system']['email']['from'] = $_POST['from'];
		$config['system']['email']['sendto'] = $_POST['sendto'];
		$config['system']['email']['server'] = $_POST['server'];
		$config['system']['email']['port'] = $_POST['port'];
		$config['system']['email']['auth'] = isset($_POST['auth']) ? true : false;
		$config['system']['email']['authmethod'] = $_POST['authmethod'];
		$config['system']['email']['security'] = $_POST['security'];
		$config['system']['email']['starttls'] = isset($_POST['starttls']) ? true : false;
		$config['system']['email']['tls_certcheck'] = $_POST['tls_certcheck'];
		$config['system']['email']['tls_use_default_trust_file'] = isset($_POST['tls_use_default_trust_file']) ? true : false;
		$config['system']['email']['tls_trust_file'] = $_POST['tls_trust_file'] ?? '';
		$config['system']['email']['tls_crl_file'] = $_POST['tls_crl_file'] ?? '';
		$config['system']['email']['tls_fingerprint'] = $_POST['tls_fingerprint'] ?? '';
		$config['system']['email']['username'] = $_POST['username'];
		$config['system']['email']['password'] = $_POST['password'];
		write_config();
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)):
			config_lock();
			$retval |= rc_exec_service('msmtp');
			config_unlock();
		endif;
		// Send test email.
		if(isset($_POST['submit'])):
			if($_POST['submit'] == 'sendtestemail'):
				$subject = sprintf(gtext('Test email from host: %s'),system_get_hostname());
				$message = gtext('This email has been sent to validate your email configuration.');
				$retval = @email_send($config['system']['email']['sendto'],$subject,$message,$error);
				if(0 == $retval):
					$savemsg = gtext('Test email successfully sent.');
					write_log(sprintf('Test email successfully sent to: %s.',$config['system']['email']['sendto']));
				else:
					$failmsg = gtext('Failed to send test email.')
						. ' '
						. '<a href="' . 'diag_log.php' . '">'
						. gtext('Please check the log files')
						. '</a>.';
					write_log(sprintf('Failed to send test email to: %s.',$config['system']['email']['sendto']));
				endif;
			elseif($_POST['submit'] == 'save'):
				$savemsg = get_std_save_message($retval);
			endif;
		endif;
	endif;
endif;
$l_security = [
	'none' => gtext('Off'),
	'tls' => gtext('On')
];
$l_tls_certcheck = [
	'tls_certcheck off' => gtext('Off'),
	'tls_certcheck on' => gtext('On')
];
$l_authmethod = [
	'plain' => gtext('Plain-text'),
	'scram-sha-1' => 'SCRAM-SHA-1',
	'cram-md5' => 'CRAM-MD5',
	'digest-md5' => 'Digest-MD5',
//	'gssapi' => 'GSSAPI',
	'external' => 'External',
	'login' => gtext('Login'),
	'ntlm' => 'NTLM',
	'on' => gtext('Best available')
];
$pgtitle = [gtext('System'),gtext('Advanced'),gtext('Email Setup')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init spinner onsubmit()
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
	auth_change();
});
function auth_change() {
	switch (document.iform.auth.checked) {
		case false:
			showElementById('username_tr','hide');
			showElementById('password_tr','hide');
			showElementById('authmethod_tr','hide');
			break;
		case true:
			showElementById('username_tr','show');
			showElementById('password_tr','show');
			showElementById('authmethod_tr','show');
		break;
	}
}
//]]>
</script>
<?php
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('system_advanced.php',gtext('Advanced'))->
			ins_tabnav_record('system_email.php',gtext('Email'),gtext('Reload page'),true)->
			ins_tabnav_record('system_email_reports.php',gtext('Email Reports'))->
			ins_tabnav_record('system_monitoring.php',gtext('Monitoring'))->
			ins_tabnav_record('system_swap.php',gtext('Swap'))->
			ins_tabnav_record('system_rc.php',gtext('Command Scripts'))->
			ins_tabnav_record('system_cron.php',gtext('Cron'))->
			ins_tabnav_record('system_loaderconf.php',gtext('loader.conf'))->
			ins_tabnav_record('system_rcconf.php',gtext('rc.conf'))->
			ins_tabnav_record('system_sysctl.php',gtext('sysctl.conf'))->
			ins_tabnav_record('system_syslogconf.php',gtext('syslog.conf'));
$document->render();
?>
<form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
	if(!empty($failmsg)):
		print_error_box($failmsg);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('System Email Settings'));
?>
		</thead>
		<tbody>
<?php
			html_inputbox2('from',gtext('From Email Address'),$pconfig['from'],gtext('From email address for sending system messages.'),true,62);
			html_inputbox2('sendto',gtext('To Email Address'),$pconfig['sendto'],gtext('Destination email address. Separate email addresses by semi-colon.'),true,62);
			html_inputbox2('server',gtext('SMTP Server'),$pconfig['server'],gtext('Outgoing SMTP mail server address.'),true,62);
			html_inputbox2('port',gtext('Port'),$pconfig['port'],gtext('The default SMTP mail server port, e.g. 25 or 587.'),true,5);
?>
		</tbody>
	</table>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_separator2();
			html_titleline2(gtext('SMTP Authentication'));
?>
		</thead>
		<tbody>
<?php
			html_checkbox2('auth',gtext('Authentication'),!empty($pconfig['auth']) ? true : false,gtext('Enable SMTP authentication.'),'',false,false,'auth_change()');
			html_inputbox2('username',gtext('Username'),$pconfig['username'],'',true,40);
			html_passwordconfbox2('password','passwordconf',gtext('Password'),$pconfig['password'],$pconfig['passwordconf'],'',true);
			html_combobox2('authmethod',gtext('Authentication Method'),$pconfig['authmethod'],$l_authmethod,'',true);
?>
		</tbody>
	</table>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_separator2();
			html_titleline2(gtext('Transport Layer Security (TLS)'));
?>
		</thead>
		<tbody>
<?php
			html_radiobox2('security',gtext('Use TLS'),$pconfig['security'],$l_security,gtext('Enable SSL/TLS for secured connections. You also need to configure the TLS trust file. For some servers you may need to disable STARTTLS.'),false);
			html_checkbox2('starttls',gtext('Enable STARTTLS'),!empty($pconfig['starttls']),gtext('Enable STARTTLS.'),'',false);
			html_radiobox2('tls_certcheck',gtext('TLS Server Certificate Check'),$pconfig['tls_certcheck'],$l_tls_certcheck,gtext('Enable or disable checks of the server certificate.'),false);
			html_checkbox2('tls_use_default_trust_file',gtext('Use Default Trust File'),!empty($pconfig['tls_use_default_trust_file']),gtext('Use default TLS trust file /usr/local/etc/ssl/cert.pem.'),'',false);
			html_filechooser2('tls_trust_file',gtext('TLS Trust File'),$pconfig['tls_trust_file'],gtext('The file must be in PEM format containing one or more certificates of trusted Certification Authorities (CAs).'),$g['media_path'],false,60);
			html_inputbox2('tls_fingerprint',gtext('TLS Fingerprint'),$pconfig['tls_fingerprint'],gtext('Set the fingerprint of a single certificate to accept for TLS.'),false,60);
			html_filechooser2('tls_crl_file',gtext('TLS CRL File'),$pconfig['tls_crl_file'],gtext('Certificate revocation list (CRL) file for TLS, to check for revoked certificates.'),$g['media_path'],false,60);
?>
		</tbody>
	</table>
	<div id="submit">
<?php
		echo html_button('save',gtext('Save'));
		echo html_button('sendtestemail',gtext('Send Test Email'));
?>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
?>
