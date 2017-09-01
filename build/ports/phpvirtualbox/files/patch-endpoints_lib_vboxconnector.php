--- /usr/local/nas4free/svn/build/ports/phpvirtualbox/work/phpvirtualbox-65ebced/endpoints/lib/vboxconnector.php.orig	2017-08-31 00:23:08.867384000 +0200
+++ /usr/local/nas4free/svn/build/ports/phpvirtualbox/work/phpvirtualbox-65ebced/endpoints/lib/vboxconnector.php	2017-09-01 20:15:19.000000000 +0200
@@ -1126,7 +1126,7 @@
 			// Try to register medium.
 			foreach($checks as $iso) {
 				try {
-					$gem = $this->vbox->openMedium($iso,'DVD','ReadOnly');
+					$gem = $this->vbox->openMedium($iso,'DVD','ReadOnly',null);
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
+								$med = $this->vbox->openMedium($ma['medium']['location'],$ma['type'],null,null);
 							}
 						} else {
 							$med = null;
@@ -1591,7 +1591,7 @@
 			if($state != 'Saved') {
 
 				// Network properties
-				$eprops = $n->getProperties();
+				$eprops = $n->getProperties(null);
 				$eprops = array_combine($eprops[1],$eprops[0]);
 				$iprops = array_map(create_function('$a','$b=explode("=",$a); return array($b[0]=>$b[1]);'),preg_split('/[\r|\n]+/',$args['networkAdapters'][$i]['properties']));
 				$inprops = array();
@@ -2028,7 +2028,7 @@
 						}
 					} else {
 						/* @var $med IMedium */
-						$med = $this->vbox->openMedium($ma['medium']['location'],$ma['type']);
+						$med = $this->vbox->openMedium($ma['medium']['location'],$ma['type'],null,null);
 					}
 				} else {
 					$med = null;
@@ -2111,7 +2111,7 @@
 			*/
 
 			// Network properties
-			$eprops = $n->getProperties();
+			$eprops = $n->getProperties(null);
 			$eprops = array_combine($eprops[1],$eprops[0]);
 			$iprops = array_map(create_function('$a','$b=explode("=",$a); return array($b[0]=>$b[1]);'),preg_split('/[\r|\n]+/',$args['networkAdapters'][$i]['properties']));
 			$inprops = array();
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
+				$hds[] = $this->vbox->openMedium($hd->location,'HardDisk',null,null)->handle;
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
+				$m = $this->vbox->openMedium($args['disk'],'HardDisk',null,null);
 
 				$this->session->machine->attachDevice(trans($HDbusType,'UIMachineSettingsStorage'),0,0,'HardDisk',$m->handle);
 
@@ -3941,7 +3943,7 @@
 			if($at == 'NAT') $nd = $n->NATEngine; /* @var $nd INATEngine */
 			else $nd = null;
 
-			$props = $n->getProperties();
+			$props = $n->getProperties(null);
 			$props = implode("\n",array_map(create_function('$a,$b','return "$a=$b";'),$props[1],$props[0]));
 
 			$adapters[] = array(
@@ -4381,7 +4383,7 @@
 	        }
 
     	    try {
-    	        $this->session->console->addDiskEncryptionPassword($creds['id'], $creds['password'], (bool)@$args['clearOnSuspend']);
+    	        $this->session->console->addDiskEncryptionPassword($creds['id'], $creds['password'], (bool)$creds['clearOnSuspend']);
     	        $response['accepted'][] = $creds['id'];
     		} catch (Exception $e) {
     		    $response['failed'][] = $creds['id'];
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
+	    $m = $this->vbox->openMedium($args['medium'],'HardDisk',null,null);
 
 	    $retval = $m->checkEncryptionPassword($args['password']);
 
@@ -4874,7 +4876,7 @@
 	    // Connect to vboxwebsrv
 	    $this->connect();
 
-	    $m = $this->vbox->openMedium($args['medium'], 'HardDisk', 'ReadWrite');
+	    $m = $this->vbox->openMedium($args['medium'], 'HardDisk', 'ReadWrite',null);
 
 	    /* @var $progress IProgress */
 	    $progress = $m->changeEncryption($args['old_password'],
@@ -4915,7 +4917,7 @@
 		// Connect to vboxwebsrv
 		$this->connect();
 
-		$m = $this->vbox->openMedium($args['medium'], 'HardDisk');
+		$m = $this->vbox->openMedium($args['medium'], 'HardDisk',null,null);
 
 		/* @var $progress IProgress */
 		$progress = $m->resize($args['bytes']);
@@ -4953,7 +4955,7 @@
 		$mid = $target->id;
 
 		/* @var $src IMedium */
-		$src = $this->vbox->openMedium($args['src'], 'HardDisk');
+		$src = $this->vbox->openMedium($args['src'], 'HardDisk',null,null);
 
 		$type = array(($args['type'] == 'fixed' ? 'Fixed' : 'Standard'));
 		if($args['split']) $type[] = 'VmdkSplit2G';
@@ -4991,7 +4993,7 @@
 		$this->connect();
 
 		/* @var $m IMedium */
-		$m = $this->vbox->openMedium($args['medium'], 'HardDisk');
+		$m = $this->vbox->openMedium($args['medium'], 'HardDisk',null,null);
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
+		$m = $this->vbox->openMedium($args['medium'],$args['type'],null,null);
 		$mediumid = $m->id;
 
 		// connected to...
@@ -5211,7 +5213,7 @@
 		if(!$args['type']) $args['type'] = 'HardDisk';
 
 		/* @var $m IMedium */
-		$m = $this->vbox->openMedium($args['medium'],$args['type']);
+		$m = $this->vbox->openMedium($args['medium'],$args['type'],null,null);
 
 		if($args['delete'] && @$this->settings->deleteOnRemove && (string)$m->deviceType == 'HardDisk') {
 
@@ -5380,7 +5382,7 @@
 			// Normal medium
 			} else {
 				/* @var $med IMedium */
-				$med = $this->vbox->openMedium($args['medium']['location'],$args['medium']['deviceType']);
+				$med = $this->vbox->openMedium($args['medium']['location'],$args['medium']['deviceType'],null,null);
 			}
 		}
 
@@ -5445,7 +5447,7 @@
 		}
 
 		// For $fixed value
-		$mvenum = new MediumVariant();
+		$mvenum = new MediumVariant(null,null);
 		$variant = 0;
 
 		foreach($m->variant as $mv) {
