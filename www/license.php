<?php
/*
	license.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE NAS4Free PROJECT ``AS IS'' AND ANY
	EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
	IN NO EVENT SHALL THE NAS4Free PROJECT OR ITS CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
	THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
// Configure page permission
$pgperm['allowuser'] = TRUE;

require_once 'auth.inc';
require_once 'guiconfig.inc';

$pgtitle = [gtext('Help'), gtext('License & Credits')];
?>
<?php
include 'fbegin.inc';
?>
<table id="area_data"><tbody><tr><td id="area_data_frame">
				
	<table class="area_data_selection">
		<colgroup>
			<col style="width:100%">
		</colgroup>
		<thead>
<?php 
			html_titleline2(gtext('License'));
?>
		</thead>
		<tbody class="donothighlight">
			<tr>
				<td class="lcebl">
<p>
<strong>
NAS4Free is Copyright &copy; 2012-2017 The NAS4Free Project (<a href="mailto:info@nas4free.org">info@nas4free.org</a>).<br />
All rights reserved.
</strong>
</p>
<p>
The compilation of software, code and documentation known as NAS4Free is distributed under the following terms:
</p>
<p>
Redistribution and use in source and binary forms, with or without<br />
modification, are permitted provided that the following conditions are met:
</p>
<p>
1. Redistributions of source code must retain the above copyright notice,<br />
   this list of conditions and the following disclaimer.<br />
</p>
<p>
2. Redistributions in binary form must reproduce the above copyright<br />
   notice, this list of conditions and the following disclaimer in the<br />
   documentation and/or other materials provided with the distribution.<br />
</p>
<p>
THIS SOFTWARE IS PROVIDED BY THE NAS4Free PROJECT "AS IS" AND ANY<br />
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED<br />
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.<br />
IN NO EVENT SHALL THE NAS4Free PROJECT OR ITS CONTRIBUTORS BE LIABLE FOR<br />
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES<br /> 
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;<br />
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON<br />
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT<br />
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF<br />
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
</p>
<p>
The views and conclusions contained in the software and documentation are those of the authors and should<br /> 
not be interpreted as representing official policies, either expressed or implied, of the NAS4Free Project.
</p>
				</td>
			</tr>
		</tbody>
	</table>
<?php
	$grid = [];
	$grid[] = ['Daisuke Aoyama','aoyama@nas4free.org','Developer'];
	$grid[] = ['Michael Schneider','ms49434@nas4free.org','Developer'];
	$grid[] = ['Michael Zoon','zoon1@nas4free.org','Developer & Project Lead'];
	$grid[] = ['José Rivera','joserprg@gmail.com','Contributor'];
	$grid[] = ['Andreas Schmidhuber','a.schmidhuber@gmail.com','Contributor'];
	$grid[] = ['Tony Cat','tony1@nas4free.org','User guide and Live support on irc #nas4free (tony1)'];
	$grid[] = ['Rhett Hillary','siftu@nas4free.org','User guide and Live support on irc #nas4free (siftu)'];
	$grid[] = ['Vicente Soriano Navarro','victek@gmail.com','Catalan translator of the WebGUI'];
	$grid[] = ['Alex Lin','linuxant@gmail.com','Chinese translator of the WebGUI'];
	$grid[] = ['Zhu Yan','tianmotrue@163.com','Chinese translator of the WebGUI'];
	$grid[] = ['Pavel Borecki','pavel.borecki@gmail.com','Czech translator of the WebGUI'];
	$grid[] = ['Carsten Vinkler','carsten@indysign.dk','Danish translator of the WebGUI'];
	$grid[] = ['Christophe Lherieau','skimpax@gmail.com','French translator of the WebGUI'];
	$grid[] = ['Edouard Richard','richard.edouard84@gmail.com','French translator of the WebGUI'];
	$grid[] = ['Dominik Plaszewski','domme555@gmx.net','German translator of the WebGUI'];
	$grid[] = ['Chris Kanatas','ckanatas@gmail.com','Greek translator of the WebGUI'];
	$grid[] = ['Petros Kyladitis','petros.kyladitis@gmail.com','Greek translator of the WebGUI'];
	$grid[] = ['Kiss-Kálmán Dániel','kisskalmandaniel@gmail.com','Hungarian translator of the WebGUI'];
	$grid[] = ['Christian Sulmoni','csulmoni@gmail.com','Italian translator of the WebGUI and QuiXplorer'];
	$grid[] = ['Frederico Tavares','frederico-tavares@sapo.pt','Portuguese translator of the WebGUI'];
	$grid[] = ['Laurentiu Bubuianu','laurfb@yahoo.com','Romanian translator of the WebGUI'];
	$grid[] = ['Raul Fernandez Garcia','raulfg3@gmail.com','Spanish translator of the WebGUI'];
	$grid[] = ['Mucahid Zeyrek','mucahid.zeyrek@dhl.com','Turkish translator of the WebGUI'];
	$grid[] = ['Volker Theile','votdev@gmx.de','Developer 2006-2009'];
	$grid[] = ['Samuel Tunis','killermist@gmail.com','User guide and Live support on irc (killermist)'];
?>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:20%">
			<col style="width:25%">
			<col style="width:55%">
		</colgroup>
		<thead>
<?php
			html_separator2();
			html_titleline2(gtext('Credits'),3);
?>
			<tr>
				<th colspan="3" class="lhebl"><?=gtext('The following persons have contributed to NAS4Free:');?></th>
			</tr>
		</thead>
		<tbody class="donothighlight">
<?php
			foreach($grid as $row):
				echo '<tr>',
					'<td class="lcell">',$row[0],'</td>',
					'<td class="lcell"><a href="mailto:',$row[1],'">',$row[1],'</a></td>',
					'<td class="lcebl"><em><font color="#666666">',$row[2],'</font></em></td>',
					'</tr>',PHP_EOL;
			endforeach;
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
			html_titleline2(gtext('Software Used'),2);
			
?>
			<tr>
				<th colspan="2" class="lhebl">
					<?=gtext('NAS4Free is based upon/includes various free software packages, listed below.');?>
					<br />
					<?=gtext('The NAS4Free Project would like to thank the authors of this software for their efforts.');?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="celltag">ataidle</td>
				<td class="celldata">
					Sets the idle timer on ATA (IDE) hard drives (<a href="http://bluestop.org/ataidle/" target="_blank">http://bluestop.org/ataidle</a>)
					<br />
					Copyright © 2004-2005 Bruce Cran (<a href="mailto:bruce@cran.org.uk">bruce@cran.org.uk</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Apple Bonjour</td>
				<td class="celldata">
					Bonjour, known as zero-configuration networking, using multicast Domain Name System (mDNS) records (<a href="http://developer.apple.com/networking/bonjour" target="_blank">http://developer.apple.com/networking/bonjour</a>)
					<br />
					Copyright © Apple Public Source License. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">cdialog</td>
				<td class="celldata">
					Display simple dialog boxes from shell scripts (<a href="http://invisible-island.net/dialog/" target="_blank">http://invisible-island.net/dialog</a>)
					<br />
					Copyright © 2000-2006, 2007 Thomas E. Dickey. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">e2fsprogs</td>
				<td class="celldata">
					e2fsprogs (<a href="http://e2fsprogs.sourceforge.net" target="_blank">http://e2fsprogs.sourceforge.net</a>)
					<br />
					Copyright © 2007 Theodore Ts'o. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">ext4fuse</td>
				<td class="celldata">
					EXT4 implementation for FUSE (<a href="https://github.com/gerard/ext4fuse" target="_blank">https://github.com/gerard/ext4fuse</a>)
					<br />
					Copyright © 1989, 1991 Free Software Foundation. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">FreeBSD</td>
				<td class="celldata">
					The FreeBSD Project (<a href="http://www.freebsd.org" target="_blank">http://www.freebsd.org</a>)
					<br />
					Copyright © 1995-2017 The FreeBSD Project. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Freenas 7</td>
				<td class="celldata">
					Freenas 7 (<a href="https://github.com/freenas/freenas7" target="_blank">https://github.com/freenas/freenas7</a>)
					<br />
					Copyright © 2005-2011 by Olivier Cochard (olivier@freenas.org). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">FUPPES</td>
				<td class="celldata">
					Free UPnP Entertainment Service (<a href="http://fuppes.ulrich-voelkel.de" target="_blank">http://fuppes.ulrich-voelkel.de</a>)
					<br />
					Copyright © 2005 - 2011 Ulrich Völkel (<a href="mailto:mail@ulrich-voelkel.de">mail@ulrich-voelkel.de</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Fuse</td>
				<td class="celldata">
					Filesystem in Userspace (<a href="https://github.com/libfuse/libfuse" target="_blank">https://github.com/libfuse/libfuse</a>)
					<br />
					Copyright &copy; GNU General Public License. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">GEOM RAID5</td>
				<td class="celldata">
					GEOM RAID5 module (<a href="http://www.wgboome.org/geom_raid5-html" target="_blank">http://www.wgboome.org/geom_raid5-html</a> & (<a href="http://lev.serebryakov.spb.ru/download/graid5/" target="_blank">http://lev.serebryakov.spb.ru/download/graid5</a>)
					<br />
					Copyright &copy; 2006-2010 Originally written by Arne Woerner (<a href="mailto:graid5@wgboome.org">graid5@wgboome.org</a>).
					<br />
					Copyright &copy; 2010-2014 Now maintained by Lev Serebryakov (<a href="mailto:lev@FreeBSD.org">lev@FreeBSD.org</a>).
				</td>
			</tr>
			<tr>
				<td class="celltag">host</td>
				<td class="celldata">
					An utility to query DNS servers.
					<br />
					Copyright &copy; Rewritten by Eric Wassenaar, Nikhef-H, (<a href="mailto:e07@nikhef.nl">e07@nikhef.nl</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">inadyn-mt</td>
				<td class="celldata">
					Simple Dynamic DNS client (<a href="http://sourceforge.net/projects/inadyn-mt" target="_blank">http://sourceforge.net/projects/inadyn-mt</a>)
					<br />
					Inadyn Copyright &copy; 2003-2004 Narcis Ilisei. All Rights Reserved.
					<br />
					Inadyn-mt Copyright &copy; 2007 Bryan Hoover (<a href="mailto:bhoover@wecs.com">bhoover@wecs.com</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">iperf3</td>
				<td class="celldata">
					A tool to measure TCP and UDP bandwidth. (<a href="http://software.es.net/iperf/" target="_blank">http://software.es.net/iperf</a>)
					<br />
					Copyright &copy; 2014-2017 ESnet. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">ipmitool</td>
				<td class="celldata">
					IPMItool provides a simple command-line interface to v1.5 & v2.0 IPMI-enabled devices. (<a href="http://sourceforge.net/projects/ipmitool/" target="_blank">http://sourceforge.net/projects/ipmitool</a>)
					<br />
					Copyright &copy; 2003 Sun Microsystems. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">iSCSI initiator</td>
				<td class="celldata">
					iSCSI initiator (<a href="ftp://ftp.cs.huji.ac.il/users/danny/freebsd" target="_blank">ftp://ftp.cs.huji.ac.il/users/danny/freebsd</a>)
					<br />
					Copyright &copy; 2005-2011 Daniel Braniss (<a href="mailto:danny@cs.huji.ac.il">danny@cs.huji.ac.il</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">istgt</td>
				<td class="celldata">
					iSCSI target for FreeBSD (<a href="http://shell.peach.ne.jp/aoyama" target="_blank">http://shell.peach.ne.jp/aoyama</a>)
					<br />
					Copyright &copy; 2008-2016 Daisuke Aoyama (<a href="mailto:aoyama@peach.ne.jp">aoyama@peach.ne.jp</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">jQuery</td>
				<td class="celldata">
					A fast, small, and feature-rich JavaScript library (<a href="http://jquery.com" target="_blank">http://jquery.com</a>).
					<br />
					Copyright &copy; 2017 jQuery Foundation. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">LCDproc</td>
				<td class="celldata">
					A client/server suite for LCD devices (<a href="http://lcdproc.org" target="_blank">http://lcdproc.org</a>).
					<br />
					Copyright &copy; 1998-2006 William Ferrell, Selene Scriven and many other contributors. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Lighttpd</td>
				<td class="celldata">
					A lighty fast webserver (<a href="http://www.lighttpd.net" target="_blank">http://www.lighttpd.net</a>).
					<br />
					Copyright &copy; 2004 Jan Kneschke (<a href="mailto:jan@kneschke.de">jan@kneschke.de</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">MiniDLNA</td>
				<td class="celldata">
					Media server software, with the aim of being fully compliant with DLNA/UPnP-AV clients. (<a href="https://sourceforge.net/projects/minidlna/" target="_blank">https://sourceforge.net/projects/minidlna</a>)
					<br />
					Copyright &copy; 2008-2015  Justin Maggard. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">m0n0wall</td>
				<td class="celldata">
					m0n0wall (<a href="http://m0n0.ch/wall/index.php" target="_blank">http://m0n0.ch/wall/index.php</a>)
					<br />
					Copyright &copy; 2002-2006 by Manuel Kasper. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">msmtp</td>
				<td class="celldata">
					A SMTP client with a sendmail compatible interface (<a href="http://msmtp.sourceforge.net" target="_blank">http://msmtp.sourceforge.net</a>)
					<br />
					Copyright &copy; 2008 Martin Lambers and others. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">mt-daapd</td>
				<td class="celldata">
					Multithread daapd Apple iTunes server (<a href="http://www.fireflymediaserver.org" target="_blank">http://www.fireflymediaserver.org</a>)
					<br />
					Copyright &copy; 2003 Ron Pedde (<a href="mailto:ron@pedde.com">ron@pedde.com</a>). All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Netatalk</td>
				<td class="celldata">
					Netatalk is a freely-available Open Source AFP fileserver (<a href="http://netatalk.sourceforge.net" target="_blank">http://netatalk.sourceforge.net</a>)
					<br />
					Copyright &copy; 1990,1996 Regents of The University of Michigan. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">noVNC</td>
				<td class="celldata">
					noVNC (<a href="http://kanaka.github.io/noVNC/" target="_blank">http://kanaka.github.io/noVNC/</a>)
					<br />
					Copyright &copy; 2011-2017 Joel Martin (<a href="mailto:github@martintribe.org">github@martintribe.org</a>) Inc. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">NTFS-3G</td>
				<td class="celldata">
					NTFS-3G is a NTFS driver (<a href="http://www.tuxera.com/community/open-source-ntfs-3g/" target="_blank">http://www.tuxera.com/community/open-source-ntfs-3g</a>)
					<br />
					Copyright &copy; 2008-2016 Tuxera Inc. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Open Virtual Machine Tools</td>
				<td class="celldata">
					Open Virtual Machine Tools - Virtualization utilities and drivers (<a href="http://sourceforge.net/projects/open-vm-tools/" target="_blank">http://sourceforge.net/projects/open-vm-tools</a>)
					<br />
					Copyright &copy; 2007-2015 VMware Inc. All rights reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">OpenSSH</td>
				<td class="celldata">
					OpenSSH (<a href="http://www.openssh.com" target="_blank">http://www.openssh.com</a>)
					<br />
					Copyright &copy; 1999-2009 OpenBSD. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">pfSense</td>
				<td class="celldata">
					pfSense (<a href="http://www.pfsense.com" target="_blank">http://www.pfsense.com</a>).
					<br />
					Copyright &copy; 2004, 2005, 2006 Scott Ullrich. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">PHP</td>
				<td class="celldata">
					A server-side scripting language (<a href="http://www.php.net" target="_blank">http://www.php.net</a>).
					<br />
					Copyright &copy; 1999-2017 The PHP Group. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">phpVirtualBox</td>
				<td class="celldata">
					phpVirtualBox (<a href="http://sourceforge.net/projects/phpvirtualbox/" target="_blank">http://sourceforge.net/projects/phpvirtualbox</a>).
					<br />
					Copyright &copy; 2011-2016 Ian Moore, Inc. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">ProFTPD</td>
				<td class="celldata">
					A Highly configurable FTP server (<a href="http://www.proftpd.org" target="_blank">http://www.proftpd.org</a>).
					<br />
					Copyright &copy; 1999, 2000-2017 The ProFTPD Project. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Python</td>
				<td class="celldata">
					A programming language (<a href="http://www.python.org" target="_blank">http://www.python.org</a>).
					<br />
					Copyright &copy; 2001-2017 Python Software Foundation. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">QuiXplorer</td>
				<td class="celldata">
					A Web-based file-management browser (<a href="https://github.com/realtimeprojects/quixplorer" target="_blank">https://github.com/realtimeprojects/quixplorer</a>).
					<br />
					Copyright &copy; Felix C. Stegerman. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Rsync</td>
				<td class="celldata">
					Utility that provides fast incremental file transfer. (<a href="http://www.samba.org/rsync" target="_blank">http://www.samba.org/rsync</a>).
					<br />
					Copyright &copy; 2007 Free Software Foundation. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Samba</td>
				<td class="celldata">
					Suite providing secure, stable and fast file services for all clients using the SMB/CIFS protocol (<a href="http://www.samba.org" target="_blank">http://www.samba.org</a>).
					<br />
					Copyright &copy; 2007 Free Software Foundation. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">sipcalc</td>
				<td class="celldata">
					sipcalc (<a href="http://www.routemeister.net/projects/sipcalc/" target="_blank">http://www.routemeister.net/projects/sipcalc</a>).
					<br />
					Copyright &copy; 2003 Simon Ekstrand. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">smartmontools</td>
				<td class="celldata">
					Utility programs (smartctl, smartd) to control/monitor storage systems (<a href="http://sourceforge.net/projects/smartmontools/" target="_blank">http://sourceforge.net/projects/smartmontools</a>).
					<br />
					Copyright &copy; 2002-2016 Bruce Allen, Christian Franke. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Spinner.js</td>
				<td class="celldata">
					A spinning activity indicator (<a href="https://github.com/fgnass/spin.js" target="_blank">https://github.com/fgnass/spin.js</a>).
					<br />
					Copyright &copy; 2011-2015 Felix Gnass. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">sudo</td>
				<td class="celldata">
					A tool to allow a sysadmin to give limited root privileges. (<a href="http://www.sudo.ws" target="_blank">http://www.sudo.ws</a>).
					<br />
					Copyright &copy; 1994-1996, 1998-2017 Todd C. Miller. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Syncthing</td>
				<td class="celldata">
					Syncthing replaces proprietary sync and cloud services with something open, trustworthy and decentralized. (<a href="https://syncthing.net" target="_blank">https://syncthing.net</a>).
					<br />
					Copyright &copy; Syncthing Development Team. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">syslogd</td>
				<td class="celldata">
					Circular log support for FreeBSD syslogd.
					<br />
					Copyright &copy; 2001 Jeff Wheelhouse (<a href="mailto:jdw@wheelhouse.org">jdw@wheelhouse.org</a>).
				</td>
			</tr>
			<tr>
				<td class="celltag">tablesorter</td>
				<td class="celldata">
					jQuery plugin for turning a standard HTML table into a sortable table (<a href="https://github.com/Mottie/tablesorter" target="_blank">https://github.com/Mottie/tablesorter</a>).
					<br />
					A Github fork of Rob Garrison (<a href="mailto:wowmotty@gmail.com">wowmotty@gmail.com</a>).
					<br />
					Copyright &copy; 2007 Christian Bach (<a href="mailto:christian@tablesorter.com">christian@tablesorter.com</a>).
				</td>
			</tr>
			<tr>
				<td class="celltag">TFTPD-HPA</td>
				<td class="celldata">
					TFTPD-HPA (Trivial File Transfer Protocol Server) (<a href="http://www.kernel.org/pub/software/network/tftp" target="_blank">http://www.kernel.org/pub/software/network/tftp</a>).
					<br />
					Copyright &copy; 1999, 2000-2009 The tftp-hpa series is maintained by H. Peter Anvin. <hpa@zytor.com>All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">tmux</td>
				<td class="celldata">
					A terminal multiplexer. (<a href="http://tmux.github.io/" target="_blank">http://tmux.github.io</a>).
					<br />
					Copyright &copy; 2010 Nicholas Marriott. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">Transmission</td>
				<td class="celldata">
					A fast, easy, and free multi-platform BitTorrent client (<a href="http://www.transmissionbt.com" target="_blank">http://www.transmissionbt.com</a>).
					<br />
					Copyright &copy; 2008-2016 Transmission Project. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">VirtualBox</td>
				<td class="celldata">
					Open Source Edition (OSE) & (Guest Additions) (<a href="http://www.virtualbox.org" target="_blank">http://www.virtualbox.org</a>).
					<br />
					Copyright &copy; 2010-2017, Oracle and/or its affiliates. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">VMXNET3</td>
				<td class="celldata">
					A NIC driver for FreeBSD (<a href="http://www.vmware.com" target="_blank">http://www.vmware.com</a>).
					<br />
					Copyright &copy; 2010 VMware, Inc. All Rights Reserved.
				</td>
			</tr>
			<tr>
				<td class="celltag">XMLStarlet</td>
				<td class="celldata">
					Command Line XML Toolkit (<a href="http://xmlstar.sourceforge.net" target="_blank">http://xmlstar.sourceforge.net</a>).
					<br />
					Copyright &copy; 2002 Mikhail Grushinskiy. All Rights Reserved.
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" class="celldata">
					<p>Some of the software used for NAS4Free are under the <a href="third-party_licenses/gpl-license.txt">GNU General Public License</a> (<a href="third-party_licenses/gpl-license.txt">GPLv2</a>, <a href="third-party_licenses/gpl3-license.txt">GPLv3</a>), <a href="third-party_licenses/lgpl-license.txt">GNU Lesser General Public License (LGPL)</a>, <a href="third-party_licenses/mpl2-license.txt">Mozilla Public License Version 2.0 (MPLv2)</a>, <a href="third-party_licenses/apple-license.txt">Apple Public Source License</a> and <a href="third-party_licenses/php-license.txt">PHP License</a>.</p>
				</td>
			</tr>
		</tfoot>
	</table>
</td></tr></tbody></table>
<?php include 'fend.inc';?>
