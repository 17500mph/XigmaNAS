--- endpoints/lib/vboxServiceWrappers.php.orig	2017-08-20 06:17:06.714817000 +0200
+++ endpoints/lib/vboxServiceWrappers.php	2017-08-20 06:26:13.000000000 +0200
@@ -915,8 +915,8 @@
         $request->_this = $this->handle;
         $request->location = $arg_location;
         $request->deviceType = $arg_deviceType;
-        $request->accessMode = $arg_accessMode;
-        $request->forceNewUuid = $arg_forceNewUuid;
+        $request->accessMode = $arg_accessMode === null ? "ReadWrite" : $arg_accessMode;
+        $request->forceNewUuid = $arg_forceNewUuid === null ? false : $arg_forceNewUuid;
         $response = $this->connection->__soapCall('IVirtualBox_openMedium', array((array)$request));
         return new IMedium ($this->connection, $response->returnval);
     }
@@ -1897,7 +1897,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->position = $arg_position;
-        $request->device = $arg_device;
+        $request->device = $arg_device === null ? 0 : $arg_device;
         $response = $this->connection->__soapCall('IMachine_setBootOrder', array((array)$request));
         return ;
     }
@@ -1966,7 +1966,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->temporaryEject = $arg_temporaryEject;
+        $request->temporaryEject = $arg_temporaryEject === null ? 0 : $arg_temporaryEject;
         $response = $this->connection->__soapCall('IMachine_temporaryEjectDevice', array((array)$request));
         return ;
     }
@@ -1978,7 +1978,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->nonRotational = $arg_nonRotational;
+        $request->nonRotational = $arg_nonRotational === null ? 0 : $arg_nonRotational;
         $response = $this->connection->__soapCall('IMachine_nonRotationalDevice', array((array)$request));
         return ;
     }
@@ -2002,7 +2002,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->hotPluggable = $arg_hotPluggable;
+        $request->hotPluggable = $arg_hotPluggable === null ? 0 : $arg_hotPluggable;
         $response = $this->connection->__soapCall('IMachine_setHotPluggableForDevice', array((array)$request));
         return ;
     }
@@ -2623,7 +2623,7 @@
         $request->_this = $this->handle;
         $request->name = $arg_name;
         $request->description = $arg_description;
-        $request->pause = $arg_pause;
+        $request->pause = $arg_pause === null ? false : $arg_pause;
         $response = $this->connection->__soapCall('IMachine_takeSnapshot', array((array)$request));
         return array(new IProgress ($this->connection, $response->returnval), (string)$response->id);
     }
@@ -7889,6 +7889,10 @@
 
     public function changeEncryption($arg_currentPassword, $arg_cipher, $arg_newPassword, $arg_newPasswordId)
     {
+        // No password ID in case of decryption and de-/encryption are both handled here.
+        $isDecryption      = ($arg_cipher == '') && ($arg_newPassword == '');
+        $arg_newPasswordId = $isDecryption ? '' : $arg_newPasswordId;
+
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->currentPassword = $arg_currentPassword;
