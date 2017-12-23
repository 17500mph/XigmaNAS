--- endpoints/lib/vboxconnector.php.orig	2017-12-23 13:44:18.021205000 +0100
+++ endpoints/lib/vboxconnector.php	2017-12-23 13:42:28.000000000 +0100
@@ -131,8 +131,8 @@
 		if(@$this->settings->warnDefault) {
 			throw new Exception("No configuration found. Rename the file <b>config.php-example</b> in phpVirtualBox's folder to ".
 					"<b>config.php</b> and edit as needed.<p>For more detailed instructions, please see the installation wiki on ".
-					"phpVirtualBox's web site. <p><a href='http://sourceforge.net/p/phpvirtualbox/wiki/Home/' target=_blank>".
-					"http://sourceforge.net/p/phpvirtualbox/wiki/Home/</a>.</p>",
+					"phpVirtualBox's web site. <p><a href='https://github.com/phpvirtualbox/phpvirtualbox/wiki' target=_blank>".
+					"https://github.com/phpvirtualbox/phpvirtualbox/wiki</a>.</p>",
 						(vboxconnector::PHPVB_ERRNO_FATAL + vboxconnector::PHPVB_ERRNO_HTML));
 		}
 
@@ -1126,7 +1126,7 @@
 			// Try to register medium.
 			foreach($checks as $iso) {
 				try {
-					$gem = $this->vbox->openMedium($iso,'DVD','ReadOnly');
+					$gem = $this->vbox->openMedium($iso,'DVD','ReadOnly',false);
 					break;
 				} catch (Exception $e) {
 					// Ignore
@@ -1358,7 +1358,7 @@
 			$src = $nsrc->machine;
 		}
 		/* @var $m IMachine */
-		$m = $this->vbox->createMachine($this->vbox->composeMachineFilename($args['name'],null,null),$args['name'],null,null,null,false);
+		$m = $this->vbox->createMachine($this->vbox->composeMachineFilename($args['name'],null,null,null),$args['name'],null,null,null,false);
 		$sfpath = $m->settingsFilePath;
 
 		/* @var $cm CloneMode */
@@ -1522,7 +1522,7 @@
 									$md->releaseRemote();
 								}
 							} else {
-								$med = $this->vbox->openMedium($ma['medium']['location'],$ma['type']);
+								$med = $this->vbox->openMedium($ma['medium']['location'],$ma['type'],'ReadWrite',false);
 							}
 						} else {
 							$med = null;
@@ -1591,9 +1591,9 @@
 			if($state != 'Saved') {
 
 				// Network properties
-				$eprops = $n->getProperties();
+				$eprops = $n->getProperties(null);
 				$eprops = array_combine($eprops[1],$eprops[0]);
-				$iprops = array_map(create_function('$a','$b=explode("=",$a); return array($b[0]=>$b[1]);'),preg_split('/[\r|\n]+/',$args['networkAdapters'][$i]['properties']));
+				$iprops = array_map(function ($a) { $b=explode("=",$a); return array($b[0]=>$b[1]); },preg_split('/[\r|\n]+/',$args['networkAdapters'][$i]['properties']));
 				$inprops = array();
 				foreach($iprops as $a) {
 					foreach($a as $k=>$v)
@@ -1942,7 +1942,7 @@
 			if($args['bootOrder'][$i]) {
 				$m->setBootOrder(($i + 1),$args['bootOrder'][$i]);
 			} else {
-				$m->setBootOrder(($i + 1),null);
+				$m->setBootOrder(($i + 1),'Null');
 			}
 		}
 
@@ -2028,7 +2028,7 @@
 						}
 					} else {
 						/* @var $med IMedium */
-						$med = $this->vbox->openMedium($ma['medium']['location'],$ma['type']);
+						$med = $this->vbox->openMedium($ma['medium']['location'],$ma['type'],'ReadWrite',false);
 					}
 				} else {
 					$med = null;
@@ -2111,9 +2111,9 @@
 			*/
 
 			// Network properties
-			$eprops = $n->getProperties();
+			$eprops = $n->getProperties(null);
 			$eprops = array_combine($eprops[1],$eprops[0]);
-			$iprops = array_map(create_function('$a','$b=explode("=",$a); return array($b[0]=>$b[1]);'),preg_split('/[\r|\n]+/',$args['networkAdapters'][$i]['properties']));
+			$iprops = array_map(function ($a) { $b=explode("=",$a); return array($b[0]=>$b[1]); } ,preg_split('/[\r|\n]+/',$args['networkAdapters'][$i]['properties']));
 			$inprops = array();
 			foreach($iprops as $a) {
 				foreach($a as $k=>$v)
@@ -2519,7 +2519,7 @@
 	 */
 	public function remote_vboxGetEnumerationMap($args) {
 
-		$c = new $args['class'];
+		$c = new $args['class'](null,null);
 		return (@isset($args['ValueMap']) ? $c->ValueMap : $c->NameMap);
 	}
 
@@ -3390,8 +3390,10 @@
 		/*
 		 * Processors
 		 */
-		for($i = 0; $i < $host->processorCount; $i++) {
-			$response['cpus'][$i] = $host->getProcessorDescription($i);
+		// TODO https://github.com/phpvirtualbox/phpvirtualbox/issues/53
+		$response['cpus'][0] = $host->getProcessorDescription(0);
+		for($i = 1; $i < $host->processorCount; $i++) {
+			$response['cpus'][$i] = $response['cpus'][0];
 		}
 
 		/*
@@ -3697,7 +3699,7 @@
 			$hds = array();
 			$delete = $machine->unregister('DetachAllReturnHardDisksOnly');
 			foreach($delete as $hd) {
-				$hds[] = $this->vbox->openMedium($hd->location,'HardDisk')->handle;
+				$hds[] = $this->vbox->openMedium($hd->location,'HardDisk','ReadWrite',false)->handle;
 			}
 
 			/* @var $progress IProgress */
@@ -3772,7 +3774,7 @@
 			$args['name'] = $_SESSION['user'] . '_' . $args['name'];
 
 		/* Check if file exists */
-		$filename = $this->vbox->composeMachineFilename($args['name'],($this->settings->phpVboxGroups ? '' : $args['group']),$this->vbox->systemProperties->defaultMachineFolder);
+		$filename = $this->vbox->composeMachineFilename($args['name'],($this->settings->phpVboxGroups ? '' : $args['group']),$this->vbox->systemProperties->defaultMachineFolder,null);
 
 		if($this->remote_fileExists(array('file'=>$filename))) {
 			return array('exists' => $filename);
@@ -3874,7 +3876,7 @@
 
 				$sc->releaseRemote();
 
-				$m = $this->vbox->openMedium($args['disk'],'HardDisk');
+				$m = $this->vbox->openMedium($args['disk'],'HardDisk','ReadWrite',false);
 
 				$this->session->machine->attachDevice(trans($HDbusType,'UIMachineSettingsStorage'),0,0,'HardDisk',$m->handle);
 
@@ -3941,8 +3943,8 @@
 			if($at == 'NAT') $nd = $n->NATEngine; /* @var $nd INATEngine */
 			else $nd = null;
 
-			$props = $n->getProperties();
-			$props = implode("\n",array_map(create_function('$a,$b','return "$a=$b";'),$props[1],$props[0]));
+			$props = $n->getProperties(null);
+			$props = implode("\n",array_map(function ($a,$b) { return "$a=$b"; },$props[1],$props[0]));
 
 			$adapters[] = array(
 				'adapterType' => (string)$n->adapterType,
@@ -4381,7 +4383,7 @@
 	        }
 
     	    try {
-    	        $this->session->console->addDiskEncryptionPassword($creds['id'], $creds['password'], (bool)@$args['clearOnSuspend']);
+    	        $this->session->console->addDiskEncryptionPassword($creds['id'], $creds['password'], (bool)$creds['clearOnSuspend']);
     	        $response['accepted'][] = $creds['id'];
     		} catch (Exception $e) {
     		    $response['failed'][] = $creds['id'];
@@ -4498,7 +4500,7 @@
 		}
 
 		// sort by port then device
-		usort($return,create_function('$a,$b', 'if($a["port"] == $b["port"]) { if($a["device"] < $b["device"]) { return -1; } if($a["device"] > $b["device"]) { return 1; } return 0; } if($a["port"] < $b["port"]) { return -1; } return 1;'));
+		usort($return,function ($a,$b) { if($a["port"] == $b["port"]) { if($a["device"] < $b["device"]) { return -1; } if($a["device"] > $b["device"]) { return 1; } return 0; } if($a["port"] < $b["port"]) { return -1; } return 1; }); 
 
 		return $return;
 	}
@@ -4690,7 +4692,7 @@
 			$machine->lockMachine($this->session->handle, ((string)$machine->sessionState == 'Unlocked' ? 'Write' : 'Shared'));
 
 			/* @var $progress IProgress */
-			list($progress, $snapshotId) = $this->session->machine->takeSnapshot($args['name'], $args['description']);
+			list($progress, $snapshotId) = $this->session->machine->takeSnapshot($args['name'], $args['description'],null);
 
 			// Does an exception exist?
 			try {
@@ -4853,7 +4855,7 @@
 	    // Connect to vboxwebsrv
 	    $this->connect();
 
-	    $m = $this->vbox->openMedium($args['medium'],'HardDisk');
+	    $m = $this->vbox->openMedium($args['medium'],'HardDisk','ReadWrite',false);
 
 	    $retval = $m->checkEncryptionPassword($args['password']);
 
@@ -4874,7 +4876,7 @@
 	    // Connect to vboxwebsrv
 	    $this->connect();
 
-	    $m = $this->vbox->openMedium($args['medium'], 'HardDisk', 'ReadWrite');
+	    $m = $this->vbox->openMedium($args['medium'], 'HardDisk', 'ReadWrite', false);
 
 	    /* @var $progress IProgress */
 	    $progress = $m->changeEncryption($args['old_password'],
@@ -4915,7 +4917,7 @@
 		// Connect to vboxwebsrv
 		$this->connect();
 
-		$m = $this->vbox->openMedium($args['medium'], 'HardDisk');
+		$m = $this->vbox->openMedium($args['medium'], 'HardDisk', 'ReadWrite', false);
 
 		/* @var $progress IProgress */
 		$progress = $m->resize($args['bytes']);
@@ -4953,7 +4955,7 @@
 		$mid = $target->id;
 
 		/* @var $src IMedium */
-		$src = $this->vbox->openMedium($args['src'], 'HardDisk');
+		$src = $this->vbox->openMedium($args['src'], 'HardDisk', 'ReadWrite', false);
 
 		$type = array(($args['type'] == 'fixed' ? 'Fixed' : 'Standard'));
 		if($args['split']) $type[] = 'VmdkSplit2G';
@@ -4991,7 +4993,7 @@
 		$this->connect();
 
 		/* @var $m IMedium */
-		$m = $this->vbox->openMedium($args['medium'], 'HardDisk');
+		$m = $this->vbox->openMedium($args['medium'], 'HardDisk', 'ReadWrite', false);
 		$m->type = $args['type'];
 		$m->releaseRemote();
 
@@ -5074,7 +5076,7 @@
 		// Connect to vboxwebsrv
 		$this->connect();
 
-		return $this->vbox->composeMachineFilename($args['name'],($this->settings->phpVboxGroups ? '' : $args['group']),$this->vbox->systemProperties->defaultMachineFolder);
+		return $this->vbox->composeMachineFilename($args['name'],($this->settings->phpVboxGroups ? '' : $args['group']),$this->vbox->systemProperties->defaultMachineFolder,null);
 
 	}
 
@@ -5129,7 +5131,7 @@
 		$this->connect();
 
 		/* @var $m IMedium */
-		$m = $this->vbox->openMedium($args['medium'],$args['type']);
+		$m = $this->vbox->openMedium($args['medium'],$args['type'],'ReadWrite',false);
 		$mediumid = $m->id;
 
 		// connected to...
@@ -5211,7 +5213,7 @@
 		if(!$args['type']) $args['type'] = 'HardDisk';
 
 		/* @var $m IMedium */
-		$m = $this->vbox->openMedium($args['medium'],$args['type']);
+		$m = $this->vbox->openMedium($args['medium'],$args['type'],'ReadWrite',false);
 
 		if($args['delete'] && @$this->settings->deleteOnRemove && (string)$m->deviceType == 'HardDisk') {
 
@@ -5380,7 +5382,7 @@
 			// Normal medium
 			} else {
 				/* @var $med IMedium */
-				$med = $this->vbox->openMedium($args['medium']['location'],$args['medium']['deviceType']);
+				$med = $this->vbox->openMedium($args['medium']['location'],$args['medium']['deviceType'],'ReadWrite',false);
 			}
 		}
 
@@ -5445,7 +5447,7 @@
 		}
 
 		// For $fixed value
-		$mvenum = new MediumVariant();
+		$mvenum = new MediumVariant(null,null);
 		$variant = 0;
 
 		foreach($m->variant as $mv) {
