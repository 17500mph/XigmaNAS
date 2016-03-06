--- lib/auth/Builtin.php.orig	2016-03-02 22:29:29.326909000 +0100
+++ lib/auth/Builtin.php	2016-03-06 19:38:24.000000000 +0100
@@ -170,7 +170,8 @@
 		global $_SESSION;
 		
 		// Must be an admin
-		if(!$_SESSION['admin']) break;
+		if(!$_SESSION['admin'])
+			return;
 
 		// Use main / auth server
 		$vbox = new vboxconnector(true);
@@ -178,7 +179,7 @@
 		
 		// See if it exists
 		if(!$skipExistCheck && $vbox->vbox->getExtraData('phpvb/users/'.$vboxRequest['u'].'/pass'))
-			break;
+			return;
 		
 		if($vboxRequest['p'])
 			$vbox->vbox->setExtraData('phpvb/users/'.$vboxRequest['u'].'/pass', hash('sha512', $vboxRequest['p']));
