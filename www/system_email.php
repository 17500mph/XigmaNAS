<?php
/*
	system_email.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of freenas (http://www.freenas.org).
	Copyright (c) 2005-2011 by Olivier Cochard <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");
require("email.inc");

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Email"));

if (!isset($config['system']['email']) || !is_array($config['system']['email']))
	$config['system']['email'] = array();

$pconfig['server'] = $config['system']['email']['server'];
$pconfig['port'] = $config['system']['email']['port'];
$pconfig['auth'] = isset($config['system']['email']['auth']);
$pconfig['authmethod'] = $config['system']['email']['authmethod'];
$pconfig['starttls'] = isset($config['system']['email']['starttls']);
$pconfig['security'] = $config['system']['email']['security'];
$pconfig['username'] = $config['system']['email']['username'];
$pconfig['password'] = $config['system']['email']['password'];
$pconfig['passwordconf'] = $pconfig['password'];
$pconfig['from'] = $config['system']['email']['from'];
$pconfig['sendto'] = isset($config['system']['email']['sendto']) ? $config['system']['email']['sendto'] : (isset($config['system']['email']['from']) ? $config['system']['email']['from'] : '');

//assure that POST Submit and Input Button use the same string
$sendtestemailbuttonvalue = gettext('Send Test Email');

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	$reqdfields = array();
	$reqdfieldsn = array();
	$reqdfieldst = array();

	if (isset($_POST['auth'])) {
		$reqdfields = array_merge($reqdfields, array("username", "password"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Username"), gettext("Password")));
		$reqdfieldst = array_merge($reqdfieldst, array("string","string"));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	// Check for a password mismatch.
	if (isset($_POST['auth']) && ($_POST['password'] !== $_POST['passwordconf'])) {
		$input_errors[] = gettext("The passwords do not match.");
	}

	if (empty($input_errors)) {
		$config['system']['email']['server'] = $_POST['server'];
		$config['system']['email']['port'] = $_POST['port'];
		$config['system']['email']['auth'] = isset($_POST['auth']) ? true : false;
		$config['system']['email']['authmethod'] = $_POST['authmethod'];
		$config['system']['email']['security'] = $_POST['security'];
		$config['system']['email']['starttls'] = isset($_POST['starttls']) ? true : false;
		$config['system']['email']['username'] = $_POST['username'];
		$config['system']['email']['password'] = $_POST['password'];
		$config['system']['email']['from'] = $_POST['from'];
		$config['system']['email']['sendto'] = $_POST['sendto'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_exec_service("msmtp");
			config_unlock();
		}

		// Send test email.
		if (stristr($_POST['Submit'], $sendtestemailbuttonvalue)) {
			$subject = sprintf(gettext("Test email from host: %s"), system_get_hostname());
			$message = gettext("This email has been sent to validate your email configuration.");

			$retval = @email_send($config['system']['email']['sendto'], $subject, $message, $error);
			if (0 == $retval) {
				$savemsg = gettext("Test email successfully sent.");
				write_log(sprintf(gettext("Test email successfully sent to: %s."), $config['system']['email']['sendto']));
			} else {
				$failmsg = sprintf(gettext("Failed to send test email. Please check the <a href='%s'>log</a> files."), "diag_log.php");
				write_log(sprintf(gettext("Failed to send test email to: %s."), $config['system']['email']['sendto']));
			}
		} else {
			$savemsg = get_std_save_message($retval);
		}
	}
}
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!--
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
function enable_change(enable_change) {
	document.iform.starttls.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><span><?=gettext("Advanced");?></span></a></li>
      	<li class="tabact"><a href="system_email.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Email");?></span></a></li>
      	<li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
      	<li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command Scripts");?></span></a></li>
        <li class="tabinact"><a href="system_cron.php"><span><?=gettext("Cron");?></span></a></li>
		<li class="tabinact"><a href="system_loaderconf.php"><span><?=gettext("loader.conf");?></span></a></li>
        <li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
        <li class="tabinact"><a href="system_sysctl.php"><span><?=gettext("sysctl.conf");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
    	<form action="system_email.php" method="post" name="iform" id="iform">
				<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<?php if (!empty($failmsg)) print_error_box($failmsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
			    		<?php html_titleline(gettext("Email"));?>
					<?php html_inputbox("from", gettext("From Email Address"), $pconfig['from'], gettext("From email address for sending system messages."), true, 62);?>
					<?php html_inputbox("sendto", gettext("To Email Address"), $pconfig['sendto'], gettext("Destination email address. Separate email addresses by semi-colon."), true, 62);?>
					<?php html_inputbox("server", gettext("SMTP Server"), $pconfig['server'], gettext("Outgoing SMTP mail server address."), true, 62);?>
					<?php html_inputbox("port", gettext("Port"), $pconfig['port'], gettext("The default SMTP mail server port, e.g. 25 or 587."), true, 5);?>
					<?php html_combobox("security", gettext("Security"), $pconfig['security'], array("none" => gettext("None"), "ssl" => "SSL", "tls" => "TLS"), "", true);?>
					<?php html_checkbox("starttls", gettext("TLS mode"), !empty($pconfig['starttls']) ? true : false, gettext("Enable STARTTLS encryption. This doesn't mean you have to use TLS, you can use SSL."), gettext("This is a way to take an existing insecure connection, and upgrade it to a secure connection using SSL/TLS."), false);?>
					<?php html_checkbox("auth", gettext("Authentication"), !empty($pconfig['auth']) ? true : false, gettext("Enable SMTP authentication."), "", false, "auth_change()");?>
					<?php html_inputbox("username", gettext("Username"), $pconfig['username'], "", true, 40);?>
					<?php html_passwordconfbox("password", "passwordconf", gettext("Password"), $pconfig['password'], $pconfig['passwordconf'], "", true);?>
					<?php html_combobox("authmethod", gettext("Authentication method"), $pconfig['authmethod'], array("plain" => gettext("Plain-text"), "cram-md5" => "Cram-MD5", "digest-md5" => "Digest-MD5", "gssapi" => "GSSAPI", "external" => "External", "login" => gettext("Login"), "ntlm" => "NTLM", "on" => gettext("Best available")), "", true);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" />
					<input name="Submit" id="sendnow" type="submit" class="formbtn" value="<?=$sendtestemailbuttonvalue;?>" />
			  </div>
			  <?php include("formend.inc");?>
			</form>
		</td>
  </tr>
</table>
<script type="text/javascript">
<!--
auth_change();
//-->
</script>
<?php include("fend.inc");?>
