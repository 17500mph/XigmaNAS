--- endpoints/lib/vboxServiceWrappers.php.orig	2017-08-13 04:21:15.906121000 +0200
+++ endpoints/lib/vboxServiceWrappers.php	2017-08-16 00:58:19.000000000 +0200
@@ -222,7 +222,7 @@
 {
     protected $_handle;
 
-    public function __construct($connection, $handle)
+    public function __construct($connection = null, $handle = null)
     {
         if (is_string($handle))
             $this->_handle = $this->ValueMap[$handle];
@@ -819,7 +819,7 @@
 class IVirtualBox extends VBox_ManagedObject
 {
 
-    public function composeMachineFilename($arg_name, $arg_group, $arg_createFlags, $arg_baseFolder)
+    public function composeMachineFilename($arg_name, $arg_group, $arg_createFlags, $arg_baseFolder = null)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
@@ -909,12 +909,18 @@
         return new IMedium ($this->connection, $response->returnval);
     }
 
-    public function openMedium($arg_location, $arg_deviceType, $arg_accessMode, $arg_forceNewUuid)
+    public function openMedium($arg_location, $arg_deviceType, $arg_accessMode= null, $arg_forceNewUuid=null)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->location = $arg_location;
         $request->deviceType = $arg_deviceType;
+       if( $arg_accessMode == null ) {
+               $arg_accessMode = "ReadWrite";
+       }
+       if( $arg_forceNewUuid == null ) {
+               $arg_forceNewUuid = "No";
+       }
         $request->accessMode = $arg_accessMode;
         $request->forceNewUuid = $arg_forceNewUuid;
         $response = $this->connection->__soapCall('IVirtualBox_openMedium', array((array)$request));
@@ -1897,6 +1903,9 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->position = $arg_position;
+       if( $arg_device == null ) {
+               $arg_device = 0;
+       }
         $request->device = $arg_device;
         $response = $this->connection->__soapCall('IMachine_setBootOrder', array((array)$request));
         return ;
@@ -1978,6 +1987,9 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
+		if( $arg_nonRotational == null ) {
+			$arg_nonRotational = false;
+		}
         $request->nonRotational = $arg_nonRotational;
         $response = $this->connection->__soapCall('IMachine_nonRotationalDevice', array((array)$request));
         return ;
@@ -2002,6 +2014,9 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
+		if( $arg_hotPluggable == null ) {
+			$arg_hotPluggable = false;
+		}
         $request->hotPluggable = $arg_hotPluggable;
         $response = $this->connection->__soapCall('IMachine_setHotPluggableForDevice', array((array)$request));
         return ;
@@ -2623,6 +2638,9 @@
         $request->_this = $this->handle;
         $request->name = $arg_name;
         $request->description = $arg_description;
+		if( $arg_pause == null ) {
+			$arg_pause = false;
+		}
         $request->pause = $arg_pause;
         $response = $this->connection->__soapCall('IMachine_takeSnapshot', array((array)$request));
         return array(new IProgress ($this->connection, $response->returnval), (string)$response->id);
@@ -8869,7 +8887,7 @@
         return ;
     }
 
-    public function getProperties($arg_names)
+    public function getProperties($arg_names=null)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
