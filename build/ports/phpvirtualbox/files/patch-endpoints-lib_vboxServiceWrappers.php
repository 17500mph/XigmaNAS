--- endpoints/lib/vboxServiceWrappers.php.orig	2017-03-17 00:49:43.719177000 +0100
+++ endpoints/lib/vboxServiceWrappers.php	2017-03-17 04:40:36.000000000 +0100
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
@@ -909,7 +909,7 @@
         return new IMedium ($this->connection, $response->returnval);
     }
 
-    public function openMedium($arg_location, $arg_deviceType, $arg_accessMode, $arg_forceNewUuid)
+    public function openMedium($arg_location, $arg_deviceType, $arg_accessMode= null, $arg_forceNewUuid=null)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
@@ -8869,7 +8869,7 @@
         return ;
     }
 
-    public function getProperties($arg_names)
+    public function getProperties($arg_names=null)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
