--- endpoints/lib/vboxServiceWrappers.php.orig	2017-10-25 23:27:18.691328000 +0200
+++ endpoints/lib/vboxServiceWrappers.php	2017-10-27 20:53:01.000000000 +0200
@@ -1,7 +1,7 @@
 <?php
 
 /*
- * Copyright (C) 2008-2015 Oracle Corporation
+ * Copyright (C) 2008-2016 Oracle Corporation
  *
  * This file is part of a free software library; you can redistribute
  * it and/or modify it under the terms of the GNU Lesser General
@@ -358,7 +358,7 @@
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->isIpv6 = $arg_isIpv6;
+        $request->isIpv6 = (bool)$arg_isIpv6;
         $request->ruleName = $arg_ruleName;
         $request->proto = $arg_proto;
         $request->hostIP = $arg_hostIP;
@@ -373,7 +373,7 @@
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->iSipv6 = $arg_iSipv6;
+        $request->iSipv6 = (bool)$arg_iSipv6;
         $request->ruleName = $arg_ruleName;
         $response = $this->connection->__soapCall('INATNetwork_removePortForwardRule', array((array)$request));
         return ;
@@ -433,11 +433,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabled = $value;
+            $request->enabled = (bool)$value;
         }
         else
         {
-            $request->enabled = $value->handle;
+            $request->enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('INATNetwork_setEnabled', array((array)$request));
     }
@@ -487,11 +487,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->IPv6Enabled = $value;
+            $request->IPv6Enabled = (bool)$value;
         }
         else
         {
-            $request->IPv6Enabled = $value->handle;
+            $request->IPv6Enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('INATNetwork_setIPv6Enabled', array((array)$request));
     }
@@ -533,11 +533,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->advertiseDefaultIPv6RouteEnabled = $value;
+            $request->advertiseDefaultIPv6RouteEnabled = (bool)$value;
         }
         else
         {
-            $request->advertiseDefaultIPv6RouteEnabled = $value->handle;
+            $request->advertiseDefaultIPv6RouteEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('INATNetwork_setAdvertiseDefaultIPv6RouteEnabled', array((array)$request));
     }
@@ -556,11 +556,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->needDhcpServer = $value;
+            $request->needDhcpServer = (bool)$value;
         }
         else
         {
-            $request->needDhcpServer = $value->handle;
+            $request->needDhcpServer = (bool)$value->handle;
         }
         $this->connection->__soapCall('INATNetwork_setNeedDhcpServer', array((array)$request));
     }
@@ -739,11 +739,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabled = $value;
+            $request->enabled = (bool)$value;
         }
         else
         {
-            $request->enabled = $value->handle;
+            $request->enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IDHCPServer_setEnabled', array((array)$request));
     }
@@ -897,6 +897,14 @@
         return new IAppliance ($this->connection, $response->returnval);
     }
 
+    public function createUnattendedInstaller()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IVirtualBox_createUnattendedInstaller', array((array)$request));
+        return new IUnattended ($this->connection, $response->returnval);
+    }
+
     public function createMedium($arg_format, $arg_location, $arg_accessMode, $arg_aDeviceTypeType)
     {
         $request = new stdClass();
@@ -916,7 +924,7 @@
         $request->location = $arg_location;
         $request->deviceType = $arg_deviceType;
         $request->accessMode = $arg_accessMode;
-        $request->forceNewUuid = $arg_forceNewUuid;
+        $request->forceNewUuid = (bool)$arg_forceNewUuid;
         $response = $this->connection->__soapCall('IVirtualBox_openMedium', array((array)$request));
         return new IMedium ($this->connection, $response->returnval);
     }
@@ -936,8 +944,8 @@
         $request->_this = $this->handle;
         $request->name = $arg_name;
         $request->hostPath = $arg_hostPath;
-        $request->writable = $arg_writable;
-        $request->automount = $arg_automount;
+        $request->writable = (bool)$arg_writable;
+        $request->automount = (bool)$arg_automount;
         $response = $this->connection->__soapCall('IVirtualBox_createSharedFolder', array((array)$request));
         return ;
     }
@@ -1337,201 +1345,1033 @@
 /**
  * Generated VBoxWebService Interface Wrapper
  */
+class ICertificate extends VBox_ManagedObject
+{
+
+    public function isCurrentlyExpired()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_isCurrentlyExpired', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function queryInfo($arg_what)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->what = $arg_what;
+        $response = $this->connection->__soapCall('ICertificate_queryInfo', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getVersionNumber()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getVersionNumber', array((array)$request));
+        return new CertificateVersion ($this->connection, $response->returnval);
+    }
+
+    public function getSerialNumber()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getSerialNumber', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getSignatureAlgorithmOID()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getSignatureAlgorithmOID', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getSignatureAlgorithmName()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getSignatureAlgorithmName', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getIssuerName()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getIssuerName', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getSubjectName()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getSubjectName', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getFriendlyName()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getFriendlyName', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getValidityPeriodNotBefore()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getValidityPeriodNotBefore', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getValidityPeriodNotAfter()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getValidityPeriodNotAfter', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getPublicKeyAlgorithmOID()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getPublicKeyAlgorithmOID', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getPublicKeyAlgorithm()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getPublicKeyAlgorithm', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getSubjectPublicKey()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getSubjectPublicKey', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getIssuerUniqueIdentifier()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getIssuerUniqueIdentifier', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getSubjectUniqueIdentifier()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getSubjectUniqueIdentifier', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getCertificateAuthority()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getCertificateAuthority', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function getKeyUsage()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getKeyUsage', array((array)$request));
+        return (float)$response->returnval;
+    }
+
+    public function getExtendedKeyUsage()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getExtendedKeyUsage', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getRawCertData()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getRawCertData', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getSelfSigned()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getSelfSigned', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function getTrusted()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getTrusted', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function getExpired()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('ICertificate_getExpired', array((array)$request));
+        return (bool)$response->returnval;
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class ICertificateCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "ICertificate";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
 class IAppliance extends VBox_ManagedObject
 {
 
-    public function read($arg_file)
+    public function read($arg_file)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->file = $arg_file;
+        $response = $this->connection->__soapCall('IAppliance_read', array((array)$request));
+        return new IProgress ($this->connection, $response->returnval);
+    }
+
+    public function interpret()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_interpret', array((array)$request));
+        return ;
+    }
+
+    public function importMachines($arg_options)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->options = $arg_options;
+        $response = $this->connection->__soapCall('IAppliance_importMachines', array((array)$request));
+        return new IProgress ($this->connection, $response->returnval);
+    }
+
+    public function createVFSExplorer($arg_URI)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->URI = $arg_URI;
+        $response = $this->connection->__soapCall('IAppliance_createVFSExplorer', array((array)$request));
+        return new IVFSExplorer ($this->connection, $response->returnval);
+    }
+
+    public function write($arg_format, $arg_options, $arg_path)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->format = $arg_format;
+        $request->options = $arg_options;
+        $request->path = $arg_path;
+        $response = $this->connection->__soapCall('IAppliance_write', array((array)$request));
+        return new IProgress ($this->connection, $response->returnval);
+    }
+
+    public function getWarnings()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_getWarnings', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getPasswordIds()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_getPasswordIds', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getMediumIdsForPasswordId($arg_passwordId)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->passwordId = $arg_passwordId;
+        $response = $this->connection->__soapCall('IAppliance_getMediumIdsForPasswordId', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function addPasswords($arg_identifiers, $arg_passwords)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->identifiers = $arg_identifiers;
+        $request->passwords = $arg_passwords;
+        $response = $this->connection->__soapCall('IAppliance_addPasswords', array((array)$request));
+        return ;
+    }
+
+    public function getPath()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_getPath', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getDisks()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_getDisks', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getVirtualSystemDescriptions()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_getVirtualSystemDescriptions', array((array)$request));
+        return new IVirtualSystemDescriptionCollection ($this->connection, (array)$response->returnval);
+    }
+
+    public function getMachines()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_getMachines', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getCertificate()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAppliance_getCertificate', array((array)$request));
+        return new ICertificate ($this->connection, $response->returnval);
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IApplianceCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IAppliance";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
+class IVirtualSystemDescription extends VBox_ManagedObject
+{
+
+    public function getDescription()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IVirtualSystemDescription_getDescription', array((array)$request));
+        return array(new VirtualSystemDescriptionTypeCollection ($this->connection, (array)$response->types), (array)$response->refs, (array)$response->OVFValues, (array)$response->VBoxValues, (array)$response->extraConfigValues);
+    }
+
+    public function getDescriptionByType($arg_type)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->type = $arg_type;
+        $response = $this->connection->__soapCall('IVirtualSystemDescription_getDescriptionByType', array((array)$request));
+        return array(new VirtualSystemDescriptionTypeCollection ($this->connection, (array)$response->types), (array)$response->refs, (array)$response->OVFValues, (array)$response->VBoxValues, (array)$response->extraConfigValues);
+    }
+
+    public function getValuesByType($arg_type, $arg_which)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->type = $arg_type;
+        $request->which = $arg_which;
+        $response = $this->connection->__soapCall('IVirtualSystemDescription_getValuesByType', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function setFinalValues($arg_enabled, $arg_VBoxValues, $arg_extraConfigValues)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->enabled = (bool)$arg_enabled;
+        $request->VBoxValues = $arg_VBoxValues;
+        $request->extraConfigValues = $arg_extraConfigValues;
+        $response = $this->connection->__soapCall('IVirtualSystemDescription_setFinalValues', array((array)$request));
+        return ;
+    }
+
+    public function addDescription($arg_type, $arg_VBoxValue, $arg_extraConfigValue)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->type = $arg_type;
+        $request->VBoxValue = $arg_VBoxValue;
+        $request->extraConfigValue = $arg_extraConfigValue;
+        $response = $this->connection->__soapCall('IVirtualSystemDescription_addDescription', array((array)$request));
+        return ;
+    }
+
+    public function getCount()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IVirtualSystemDescription_getCount', array((array)$request));
+        return (float)$response->returnval;
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IVirtualSystemDescriptionCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IVirtualSystemDescription";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
+class IUnattended extends VBox_ManagedObject
+{
+
+    public function detectIsoOS()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_detectIsoOS', array((array)$request));
+        return ;
+    }
+
+    public function prepare()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_prepare', array((array)$request));
+        return ;
+    }
+
+    public function constructMedia()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_constructMedia', array((array)$request));
+        return ;
+    }
+
+    public function reconfigureVM()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_reconfigureVM', array((array)$request));
+        return ;
+    }
+
+    public function done()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_done', array((array)$request));
+        return ;
+    }
+
+    public function getIsoPath()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getIsoPath', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setIsoPath($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->isoPath = $value;
+        }
+        else
+        {
+            $request->isoPath = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setIsoPath', array((array)$request));
+    }
+
+    public function getMachine()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getMachine', array((array)$request));
+        return new IMachine ($this->connection, $response->returnval);
+    }
+
+    public function setMachine($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->machine = $value;
+        }
+        else
+        {
+            $request->machine = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setMachine', array((array)$request));
+    }
+
+    public function getUser()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getUser', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setUser($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->user = $value;
+        }
+        else
+        {
+            $request->user = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setUser', array((array)$request));
+    }
+
+    public function getPassword()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getPassword', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setPassword($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->password = $value;
+        }
+        else
+        {
+            $request->password = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setPassword', array((array)$request));
+    }
+
+    public function getFullUserName()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getFullUserName', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setFullUserName($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->fullUserName = $value;
+        }
+        else
+        {
+            $request->fullUserName = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setFullUserName', array((array)$request));
+    }
+
+    public function getProductKey()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getProductKey', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setProductKey($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->productKey = $value;
+        }
+        else
+        {
+            $request->productKey = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setProductKey', array((array)$request));
+    }
+
+    public function getAdditionsIsoPath()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getAdditionsIsoPath', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setAdditionsIsoPath($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->additionsIsoPath = $value;
+        }
+        else
+        {
+            $request->additionsIsoPath = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setAdditionsIsoPath', array((array)$request));
+    }
+
+    public function getInstallGuestAdditions()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getInstallGuestAdditions', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function setInstallGuestAdditions($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->installGuestAdditions = $value;
+        }
+        else
+        {
+            $request->installGuestAdditions = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setInstallGuestAdditions', array((array)$request));
+    }
+
+    public function getValidationKitIsoPath()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getValidationKitIsoPath', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setValidationKitIsoPath($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->validationKitIsoPath = $value;
+        }
+        else
+        {
+            $request->validationKitIsoPath = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setValidationKitIsoPath', array((array)$request));
+    }
+
+    public function getInstallTestExecService()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getInstallTestExecService', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function setInstallTestExecService($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->installTestExecService = $value;
+        }
+        else
+        {
+            $request->installTestExecService = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setInstallTestExecService', array((array)$request));
+    }
+
+    public function getTimeZone()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getTimeZone', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setTimeZone($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->timeZone = $value;
+        }
+        else
+        {
+            $request->timeZone = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setTimeZone', array((array)$request));
+    }
+
+    public function getLocale()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getLocale', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setLocale($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->locale = $value;
+        }
+        else
+        {
+            $request->locale = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setLocale', array((array)$request));
+    }
+
+    public function getLanguage()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getLanguage', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setLanguage($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->language = $value;
+        }
+        else
+        {
+            $request->language = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setLanguage', array((array)$request));
+    }
+
+    public function getCountry()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getCountry', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setCountry($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->country = $value;
+        }
+        else
+        {
+            $request->country = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setCountry', array((array)$request));
+    }
+
+    public function getProxy()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getProxy', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setProxy($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->proxy = $value;
+        }
+        else
+        {
+            $request->proxy = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setProxy', array((array)$request));
+    }
+
+    public function getPackageSelectionAdjustments()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->file = $arg_file;
-        $response = $this->connection->__soapCall('IAppliance_read', array((array)$request));
-        return new IProgress ($this->connection, $response->returnval);
+        $response = $this->connection->__soapCall('IUnattended_getPackageSelectionAdjustments', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function interpret()
+    public function setPackageSelectionAdjustments($value)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IAppliance_interpret', array((array)$request));
-        return ;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->packageSelectionAdjustments = $value;
+        }
+        else
+        {
+            $request->packageSelectionAdjustments = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setPackageSelectionAdjustments', array((array)$request));
     }
 
-    public function importMachines($arg_options)
+    public function getHostname()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->options = $arg_options;
-        $response = $this->connection->__soapCall('IAppliance_importMachines', array((array)$request));
-        return new IProgress ($this->connection, $response->returnval);
+        $response = $this->connection->__soapCall('IUnattended_getHostname', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function createVFSExplorer($arg_URI)
+    public function setHostname($value)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->URI = $arg_URI;
-        $response = $this->connection->__soapCall('IAppliance_createVFSExplorer', array((array)$request));
-        return new IVFSExplorer ($this->connection, $response->returnval);
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->hostname = $value;
+        }
+        else
+        {
+            $request->hostname = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setHostname', array((array)$request));
     }
 
-    public function write($arg_format, $arg_options, $arg_path)
+    public function getAuxiliaryBasePath()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->format = $arg_format;
-        $request->options = $arg_options;
-        $request->path = $arg_path;
-        $response = $this->connection->__soapCall('IAppliance_write', array((array)$request));
-        return new IProgress ($this->connection, $response->returnval);
+        $response = $this->connection->__soapCall('IUnattended_getAuxiliaryBasePath', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function getWarnings()
+    public function setAuxiliaryBasePath($value)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IAppliance_getWarnings', array((array)$request));
-        return (array)$response->returnval;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->auxiliaryBasePath = $value;
+        }
+        else
+        {
+            $request->auxiliaryBasePath = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setAuxiliaryBasePath', array((array)$request));
     }
 
-    public function getPasswordIds()
+    public function getImageIndex()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IAppliance_getPasswordIds', array((array)$request));
-        return (array)$response->returnval;
+        $response = $this->connection->__soapCall('IUnattended_getImageIndex', array((array)$request));
+        return (float)$response->returnval;
     }
 
-    public function getMediumIdsForPasswordId($arg_passwordId)
+    public function setImageIndex($value)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->passwordId = $arg_passwordId;
-        $response = $this->connection->__soapCall('IAppliance_getMediumIdsForPasswordId', array((array)$request));
-        return (array)$response->returnval;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->imageIndex = $value;
+        }
+        else
+        {
+            $request->imageIndex = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setImageIndex', array((array)$request));
     }
 
-    public function addPasswords($arg_identifiers, $arg_passwords)
+    public function getScriptTemplatePath()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->identifiers = $arg_identifiers;
-        $request->passwords = $arg_passwords;
-        $response = $this->connection->__soapCall('IAppliance_addPasswords', array((array)$request));
-        return ;
+        $response = $this->connection->__soapCall('IUnattended_getScriptTemplatePath', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function getPath()
+    public function setScriptTemplatePath($value)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IAppliance_getPath', array((array)$request));
-        return (string)$response->returnval;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->scriptTemplatePath = $value;
+        }
+        else
+        {
+            $request->scriptTemplatePath = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setScriptTemplatePath', array((array)$request));
     }
 
-    public function getDisks()
+    public function getPostInstallScriptTemplatePath()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IAppliance_getDisks', array((array)$request));
-        return (array)$response->returnval;
+        $response = $this->connection->__soapCall('IUnattended_getPostInstallScriptTemplatePath', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function getVirtualSystemDescriptions()
+    public function setPostInstallScriptTemplatePath($value)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IAppliance_getVirtualSystemDescriptions', array((array)$request));
-        return new IVirtualSystemDescriptionCollection ($this->connection, (array)$response->returnval);
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->postInstallScriptTemplatePath = $value;
+        }
+        else
+        {
+            $request->postInstallScriptTemplatePath = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setPostInstallScriptTemplatePath', array((array)$request));
     }
 
-    public function getMachines()
+    public function getPostInstallCommand()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IAppliance_getMachines', array((array)$request));
-        return (array)$response->returnval;
+        $response = $this->connection->__soapCall('IUnattended_getPostInstallCommand', array((array)$request));
+        return (string)$response->returnval;
     }
-}
 
-/**
- * Generated VBoxWebService Managed Object Collection
- */
-class IApplianceCollection extends VBox_ManagedObjectCollection
-{
-    protected $_interfaceName = "IAppliance";
-}
+    public function setPostInstallCommand($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->postInstallCommand = $value;
+        }
+        else
+        {
+            $request->postInstallCommand = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setPostInstallCommand', array((array)$request));
+    }
 
-/**
- * Generated VBoxWebService Interface Wrapper
- */
-class IVirtualSystemDescription extends VBox_ManagedObject
-{
+    public function getExtraInstallKernelParameters()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUnattended_getExtraInstallKernelParameters', array((array)$request));
+        return (string)$response->returnval;
+    }
 
-    public function getDescription()
+    public function setExtraInstallKernelParameters($value)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IVirtualSystemDescription_getDescription', array((array)$request));
-        return array(new VirtualSystemDescriptionTypeCollection ($this->connection, (array)$response->types), (array)$response->refs, (array)$response->OVFValues, (array)$response->VBoxValues, (array)$response->extraConfigValues);
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->extraInstallKernelParameters = $value;
+        }
+        else
+        {
+            $request->extraInstallKernelParameters = $value->handle;
+        }
+        $this->connection->__soapCall('IUnattended_setExtraInstallKernelParameters', array((array)$request));
     }
 
-    public function getDescriptionByType($arg_type)
+    public function getDetectedOSTypeId()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->type = $arg_type;
-        $response = $this->connection->__soapCall('IVirtualSystemDescription_getDescriptionByType', array((array)$request));
-        return array(new VirtualSystemDescriptionTypeCollection ($this->connection, (array)$response->types), (array)$response->refs, (array)$response->OVFValues, (array)$response->VBoxValues, (array)$response->extraConfigValues);
+        $response = $this->connection->__soapCall('IUnattended_getDetectedOSTypeId', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function getValuesByType($arg_type, $arg_which)
+    public function getDetectedOSVersion()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->type = $arg_type;
-        $request->which = $arg_which;
-        $response = $this->connection->__soapCall('IVirtualSystemDescription_getValuesByType', array((array)$request));
-        return (array)$response->returnval;
+        $response = $this->connection->__soapCall('IUnattended_getDetectedOSVersion', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function setFinalValues($arg_enabled, $arg_VBoxValues, $arg_extraConfigValues)
+    public function getDetectedOSFlavor()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->enabled = $arg_enabled;
-        $request->VBoxValues = $arg_VBoxValues;
-        $request->extraConfigValues = $arg_extraConfigValues;
-        $response = $this->connection->__soapCall('IVirtualSystemDescription_setFinalValues', array((array)$request));
-        return ;
+        $response = $this->connection->__soapCall('IUnattended_getDetectedOSFlavor', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function addDescription($arg_type, $arg_VBoxValue, $arg_extraConfigValue)
+    public function getDetectedOSLanguages()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->type = $arg_type;
-        $request->VBoxValue = $arg_VBoxValue;
-        $request->extraConfigValue = $arg_extraConfigValue;
-        $response = $this->connection->__soapCall('IVirtualSystemDescription_addDescription', array((array)$request));
-        return ;
+        $response = $this->connection->__soapCall('IUnattended_getDetectedOSLanguages', array((array)$request));
+        return (string)$response->returnval;
     }
 
-    public function getCount()
+    public function getDetectedOSHints()
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $response = $this->connection->__soapCall('IVirtualSystemDescription_getCount', array((array)$request));
-        return (float)$response->returnval;
+        $response = $this->connection->__soapCall('IUnattended_getDetectedOSHints', array((array)$request));
+        return (string)$response->returnval;
     }
 }
 
 /**
  * Generated VBoxWebService Managed Object Collection
  */
-class IVirtualSystemDescriptionCollection extends VBox_ManagedObjectCollection
+class IUnattendedCollection extends VBox_ManagedObjectCollection
 {
-    protected $_interfaceName = "IVirtualSystemDescription";
+    protected $_interfaceName = "IUnattended";
 }
 
 /**
@@ -1554,11 +2394,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->logoFadeIn = $value;
+            $request->logoFadeIn = (bool)$value;
         }
         else
         {
-            $request->logoFadeIn = $value->handle;
+            $request->logoFadeIn = (bool)$value->handle;
         }
         $this->connection->__soapCall('IBIOSSettings_setLogoFadeIn', array((array)$request));
     }
@@ -1577,11 +2417,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->logoFadeOut = $value;
+            $request->logoFadeOut = (bool)$value;
         }
         else
         {
-            $request->logoFadeOut = $value->handle;
+            $request->logoFadeOut = (bool)$value->handle;
         }
         $this->connection->__soapCall('IBIOSSettings_setLogoFadeOut', array((array)$request));
     }
@@ -1669,11 +2509,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->ACPIEnabled = $value;
+            $request->ACPIEnabled = (bool)$value;
         }
         else
         {
-            $request->ACPIEnabled = $value->handle;
+            $request->ACPIEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IBIOSSettings_setACPIEnabled', array((array)$request));
     }
@@ -1692,15 +2532,38 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->IOAPICEnabled = $value;
+            $request->IOAPICEnabled = (bool)$value;
         }
         else
         {
-            $request->IOAPICEnabled = $value->handle;
+            $request->IOAPICEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IBIOSSettings_setIOAPICEnabled', array((array)$request));
     }
 
+    public function getAPICMode()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IBIOSSettings_getAPICMode', array((array)$request));
+        return new APICMode ($this->connection, $response->returnval);
+    }
+
+    public function setAPICMode($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->APICMode = $value;
+        }
+        else
+        {
+            $request->APICMode = $value->handle;
+        }
+        $this->connection->__soapCall('IBIOSSettings_setAPICMode', array((array)$request));
+    }
+
     public function getTimeOffset()
     {
         $request = new stdClass();
@@ -1738,11 +2601,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->PXEDebugEnabled = $value;
+            $request->PXEDebugEnabled = (bool)$value;
         }
         else
         {
-            $request->PXEDebugEnabled = $value->handle;
+            $request->PXEDebugEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IBIOSSettings_setPXEDebugEnabled', array((array)$request));
     }
@@ -1954,7 +2817,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->passthrough = $arg_passthrough;
+        $request->passthrough = (bool)$arg_passthrough;
         $response = $this->connection->__soapCall('IMachine_passthroughDevice', array((array)$request));
         return ;
     }
@@ -1966,7 +2829,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->temporaryEject = $arg_temporaryEject;
+        $request->temporaryEject = (bool)$arg_temporaryEject;
         $response = $this->connection->__soapCall('IMachine_temporaryEjectDevice', array((array)$request));
         return ;
     }
@@ -1978,7 +2841,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->nonRotational = $arg_nonRotational;
+        $request->nonRotational = (bool)$arg_nonRotational;
         $response = $this->connection->__soapCall('IMachine_nonRotationalDevice', array((array)$request));
         return ;
     }
@@ -1990,7 +2853,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->discard = $arg_discard;
+        $request->discard = (bool)$arg_discard;
         $response = $this->connection->__soapCall('IMachine_setAutoDiscardForDevice', array((array)$request));
         return ;
     }
@@ -2002,7 +2865,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->hotPluggable = $arg_hotPluggable;
+        $request->hotPluggable = (bool)$arg_hotPluggable;
         $response = $this->connection->__soapCall('IMachine_setHotPluggableForDevice', array((array)$request));
         return ;
     }
@@ -2037,7 +2900,7 @@
         $request->name = $arg_name;
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
-        $request->force = $arg_force;
+        $request->force = (bool)$arg_force;
         $response = $this->connection->__soapCall('IMachine_unmountMedium', array((array)$request));
         return ;
     }
@@ -2050,7 +2913,7 @@
         $request->controllerPort = $arg_controllerPort;
         $request->device = $arg_device;
         $request->medium = $arg_medium;
-        $request->force = $arg_force;
+        $request->force = (bool)$arg_force;
         $response = $this->connection->__soapCall('IMachine_mountMedium', array((array)$request));
         return ;
     }
@@ -2092,7 +2955,7 @@
         $request->_this = $this->handle;
         $request->hostAddress = $arg_hostAddress;
         $request->desiredGuestAddress = $arg_desiredGuestAddress;
-        $request->tryToUnbind = $arg_tryToUnbind;
+        $request->tryToUnbind = (bool)$arg_tryToUnbind;
         $response = $this->connection->__soapCall('IMachine_attachHostPCIDevice', array((array)$request));
         return ;
     }
@@ -2158,7 +3021,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->name = $arg_name;
-        $request->bootable = $arg_bootable;
+        $request->bootable = (bool)$arg_bootable;
         $response = $this->connection->__soapCall('IMachine_setStorageControllerBootable', array((array)$request));
         return ;
     }
@@ -2259,25 +3122,36 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->property = $arg_property;
-        $request->value = $arg_value;
+        $request->value = (bool)$arg_value;
         $response = $this->connection->__soapCall('IMachine_setCPUProperty', array((array)$request));
         return ;
     }
 
-    public function getCPUIDLeaf($arg_id)
+    public function getCPUIDLeafByOrdinal($arg_ordinal)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->id = $arg_id;
+        $request->ordinal = $arg_ordinal;
+        $response = $this->connection->__soapCall('IMachine_getCPUIDLeafByOrdinal', array((array)$request));
+        return array((float)$response->idx, (float)$response->idxSub, (float)$response->valEax, (float)$response->valEbx, (float)$response->valEcx, (float)$response->valEdx);
+    }
+
+    public function getCPUIDLeaf($arg_idx, $arg_idxSub)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->idx = $arg_idx;
+        $request->idxSub = $arg_idxSub;
         $response = $this->connection->__soapCall('IMachine_getCPUIDLeaf', array((array)$request));
         return array((float)$response->valEax, (float)$response->valEbx, (float)$response->valEcx, (float)$response->valEdx);
     }
 
-    public function setCPUIDLeaf($arg_id, $arg_valEax, $arg_valEbx, $arg_valEcx, $arg_valEdx)
+    public function setCPUIDLeaf($arg_idx, $arg_idxSub, $arg_valEax, $arg_valEbx, $arg_valEcx, $arg_valEdx)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->id = $arg_id;
+        $request->idx = $arg_idx;
+        $request->idxSub = $arg_idxSub;
         $request->valEax = $arg_valEax;
         $request->valEbx = $arg_valEbx;
         $request->valEcx = $arg_valEcx;
@@ -2286,11 +3160,12 @@
         return ;
     }
 
-    public function removeCPUIDLeaf($arg_id)
+    public function removeCPUIDLeaf($arg_idx, $arg_idxSub)
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->id = $arg_id;
+        $request->idx = $arg_idx;
+        $request->idxSub = $arg_idxSub;
         $response = $this->connection->__soapCall('IMachine_removeCPUIDLeaf', array((array)$request));
         return ;
     }
@@ -2317,7 +3192,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->property = $arg_property;
-        $request->value = $arg_value;
+        $request->value = (bool)$arg_value;
         $response = $this->connection->__soapCall('IMachine_setHWVirtExProperty', array((array)$request));
         return ;
     }
@@ -2390,8 +3265,8 @@
         $request->_this = $this->handle;
         $request->name = $arg_name;
         $request->hostPath = $arg_hostPath;
-        $request->writable = $arg_writable;
-        $request->automount = $arg_automount;
+        $request->writable = (bool)$arg_writable;
+        $request->automount = (bool)$arg_automount;
         $response = $this->connection->__soapCall('IMachine_createSharedFolder', array((array)$request));
         return ;
     }
@@ -2612,7 +3487,7 @@
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->fRemoveFile = $arg_fRemoveFile;
+        $request->fRemoveFile = (bool)$arg_fRemoveFile;
         $response = $this->connection->__soapCall('IMachine_discardSavedState', array((array)$request));
         return ;
     }
@@ -2623,7 +3498,7 @@
         $request->_this = $this->handle;
         $request->name = $arg_name;
         $request->description = $arg_description;
-        $request->pause = $arg_pause;
+        $request->pause = (bool)$arg_pause;
         $response = $this->connection->__soapCall('IMachine_takeSnapshot', array((array)$request));
         return array(new IProgress ($this->connection, $response->returnval), (string)$response->id);
     }
@@ -2904,11 +3779,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->CPUHotPlugEnabled = $value;
+            $request->CPUHotPlugEnabled = (bool)$value;
         }
         else
         {
-            $request->CPUHotPlugEnabled = $value->handle;
+            $request->CPUHotPlugEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setCPUHotPlugEnabled', array((array)$request));
     }
@@ -3019,11 +3894,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->pageFusionEnabled = $value;
+            $request->pageFusionEnabled = (bool)$value;
         }
         else
         {
-            $request->pageFusionEnabled = $value->handle;
+            $request->pageFusionEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setPageFusionEnabled', array((array)$request));
     }
@@ -3088,11 +3963,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->accelerate3DEnabled = $value;
+            $request->accelerate3DEnabled = (bool)$value;
         }
         else
         {
-            $request->accelerate3DEnabled = $value->handle;
+            $request->accelerate3DEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setAccelerate3DEnabled', array((array)$request));
     }
@@ -3111,11 +3986,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->accelerate2DVideoEnabled = $value;
+            $request->accelerate2DVideoEnabled = (bool)$value;
         }
         else
         {
-            $request->accelerate2DVideoEnabled = $value->handle;
+            $request->accelerate2DVideoEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setAccelerate2DVideoEnabled', array((array)$request));
     }
@@ -3157,11 +4032,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->videoCaptureEnabled = $value;
+            $request->videoCaptureEnabled = (bool)$value;
         }
         else
         {
-            $request->videoCaptureEnabled = $value->handle;
+            $request->videoCaptureEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setVideoCaptureEnabled', array((array)$request));
     }
@@ -3464,11 +4339,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->HPETEnabled = $value;
+            $request->HPETEnabled = (bool)$value;
         }
         else
         {
-            $request->HPETEnabled = $value->handle;
+            $request->HPETEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setHPETEnabled', array((array)$request));
     }
@@ -3541,11 +4416,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->emulatedUSBCardReaderEnabled = $value;
+            $request->emulatedUSBCardReaderEnabled = (bool)$value;
         }
         else
         {
-            $request->emulatedUSBCardReaderEnabled = $value->handle;
+            $request->emulatedUSBCardReaderEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setEmulatedUSBCardReaderEnabled', array((array)$request));
     }
@@ -3598,6 +4473,14 @@
         return (string)$response->returnval;
     }
 
+    public function getSettingsAuxFilePath()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IMachine_getSettingsAuxFilePath', array((array)$request));
+        return (string)$response->returnval;
+    }
+
     public function getSettingsModified()
     {
         $request = new stdClass();
@@ -3754,11 +4637,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->teleporterEnabled = $value;
+            $request->teleporterEnabled = (bool)$value;
         }
         else
         {
-            $request->teleporterEnabled = $value->handle;
+            $request->teleporterEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setTeleporterEnabled', array((array)$request));
     }
@@ -3984,11 +4867,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->RTCUseUTC = $value;
+            $request->RTCUseUTC = (bool)$value;
         }
         else
         {
-            $request->RTCUseUTC = $value->handle;
+            $request->RTCUseUTC = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setRTCUseUTC', array((array)$request));
     }
@@ -4007,11 +4890,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->IOCacheEnabled = $value;
+            $request->IOCacheEnabled = (bool)$value;
         }
         else
         {
-            $request->IOCacheEnabled = $value->handle;
+            $request->IOCacheEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setIOCacheEnabled', array((array)$request));
     }
@@ -4069,11 +4952,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->tracingEnabled = $value;
+            $request->tracingEnabled = (bool)$value;
         }
         else
         {
-            $request->tracingEnabled = $value->handle;
+            $request->tracingEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setTracingEnabled', array((array)$request));
     }
@@ -4115,11 +4998,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->allowTracingToAccessVM = $value;
+            $request->allowTracingToAccessVM = (bool)$value;
         }
         else
         {
-            $request->allowTracingToAccessVM = $value->handle;
+            $request->allowTracingToAccessVM = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setAllowTracingToAccessVM', array((array)$request));
     }
@@ -4138,11 +5021,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->autostartEnabled = $value;
+            $request->autostartEnabled = (bool)$value;
         }
         else
         {
-            $request->autostartEnabled = $value->handle;
+            $request->autostartEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachine_setAutostartEnabled', array((array)$request));
     }
@@ -4246,6 +5129,52 @@
         }
         $this->connection->__soapCall('IMachine_setVMProcessPriority', array((array)$request));
     }
+
+    public function getParavirtDebug()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IMachine_getParavirtDebug', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setParavirtDebug($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->paravirtDebug = $value;
+        }
+        else
+        {
+            $request->paravirtDebug = $value->handle;
+        }
+        $this->connection->__soapCall('IMachine_setParavirtDebug', array((array)$request));
+    }
+
+    public function getCPUProfile()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IMachine_getCPUProfile', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function setCPUProfile($value)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        if (is_null($value) || is_scalar($value))
+        {
+            $request->CPUProfile = $value;
+        }
+        else
+        {
+            $request->CPUProfile = $value->handle;
+        }
+        $this->connection->__soapCall('IMachine_setCPUProfile', array((array)$request));
+    }
 }
 
 /**
@@ -4436,8 +5365,8 @@
         $request->_this = $this->handle;
         $request->name = $arg_name;
         $request->hostPath = $arg_hostPath;
-        $request->writable = $arg_writable;
-        $request->automount = $arg_automount;
+        $request->writable = (bool)$arg_writable;
+        $request->automount = (bool)$arg_automount;
         $response = $this->connection->__soapCall('IConsole_createSharedFolder', array((array)$request));
         return ;
     }
@@ -4469,7 +5398,7 @@
         $request->_this = $this->handle;
         $request->id = $arg_id;
         $request->password = $arg_password;
-        $request->clearOnSuspend = $arg_clearOnSuspend;
+        $request->clearOnSuspend = (bool)$arg_clearOnSuspend;
         $response = $this->connection->__soapCall('IConsole_addDiskEncryptionPassword', array((array)$request));
         return ;
     }
@@ -4480,7 +5409,7 @@
         $request->_this = $this->handle;
         $request->ids = $arg_ids;
         $request->passwords = $arg_passwords;
-        $request->clearOnSuspend = $arg_clearOnSuspend;
+        $request->clearOnSuspend = (bool)$arg_clearOnSuspend;
         $response = $this->connection->__soapCall('IConsole_addDiskEncryptionPasswords', array((array)$request));
         return ;
     }
@@ -4620,11 +5549,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->useHostClipboard = $value;
+            $request->useHostClipboard = (bool)$value;
         }
         else
         {
-            $request->useHostClipboard = $value->handle;
+            $request->useHostClipboard = (bool)$value->handle;
         }
         $this->connection->__soapCall('IConsole_setUseHostClipboard', array((array)$request));
     }
@@ -4799,6 +5728,14 @@
         $response = $this->connection->__soapCall('IHostNetworkInterface_getInterfaceType', array((array)$request));
         return new HostNetworkInterfaceType ($this->connection, $response->returnval);
     }
+
+    public function getWireless()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IHostNetworkInterface_getWireless', array((array)$request));
+        return (bool)$response->returnval;
+    }
 }
 
 /**
@@ -5008,6 +5945,28 @@
         return (string)$response->returnval;
     }
 
+    public function addUSBDeviceSource($arg_backend, $arg_id, $arg_address, $arg_propertyNames, $arg_propertyValues)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->backend = $arg_backend;
+        $request->id = $arg_id;
+        $request->address = $arg_address;
+        $request->propertyNames = $arg_propertyNames;
+        $request->propertyValues = $arg_propertyValues;
+        $response = $this->connection->__soapCall('IHost_addUSBDeviceSource', array((array)$request));
+        return ;
+    }
+
+    public function removeUSBDeviceSource($arg_id)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->id = $arg_id;
+        $response = $this->connection->__soapCall('IHost_removeUSBDeviceSource', array((array)$request));
+        return ;
+    }
+
     public function getDVDDrives()
     {
         $request = new stdClass();
@@ -5378,11 +6337,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->exclusiveHwVirt = $value;
+            $request->exclusiveHwVirt = (bool)$value;
         }
         else
         {
-            $request->exclusiveHwVirt = $value->handle;
+            $request->exclusiveHwVirt = (bool)$value->handle;
         }
         $this->connection->__soapCall('ISystemProperties_setExclusiveHwVirt', array((array)$request));
     }
@@ -6038,7 +6997,7 @@
         $request->templateName = $arg_templateName;
         $request->mode = $arg_mode;
         $request->path = $arg_path;
-        $request->secure = $arg_secure;
+        $request->secure = (bool)$arg_secure;
         $response = $this->connection->__soapCall('IGuestSession_directoryCreateTemp', array((array)$request));
         return (string)$response->returnval;
     }
@@ -6048,7 +7007,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->path = $arg_path;
-        $request->followSymlinks = $arg_followSymlinks;
+        $request->followSymlinks = (bool)$arg_followSymlinks;
         $response = $this->connection->__soapCall('IGuestSession_directoryExists', array((array)$request));
         return (bool)$response->returnval;
     }
@@ -6160,7 +7119,7 @@
         $request->templateName = $arg_templateName;
         $request->mode = $arg_mode;
         $request->path = $arg_path;
-        $request->secure = $arg_secure;
+        $request->secure = (bool)$arg_secure;
         $response = $this->connection->__soapCall('IGuestSession_fileCreateTemp', array((array)$request));
         return new IGuestFile ($this->connection, $response->returnval);
     }
@@ -6170,7 +7129,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->path = $arg_path;
-        $request->followSymlinks = $arg_followSymlinks;
+        $request->followSymlinks = (bool)$arg_followSymlinks;
         $response = $this->connection->__soapCall('IGuestSession_fileExists', array((array)$request));
         return (bool)$response->returnval;
     }
@@ -6206,7 +7165,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->path = $arg_path;
-        $request->followSymlinks = $arg_followSymlinks;
+        $request->followSymlinks = (bool)$arg_followSymlinks;
         $response = $this->connection->__soapCall('IGuestSession_fileQuerySize', array((array)$request));
         return (float)$response->returnval;
     }
@@ -6216,7 +7175,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->path = $arg_path;
-        $request->followSymlinks = $arg_followSymlinks;
+        $request->followSymlinks = (bool)$arg_followSymlinks;
         $response = $this->connection->__soapCall('IGuestSession_fsObjExists', array((array)$request));
         return (bool)$response->returnval;
     }
@@ -6226,7 +7185,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->path = $arg_path;
-        $request->followSymlinks = $arg_followSymlinks;
+        $request->followSymlinks = (bool)$arg_followSymlinks;
         $response = $this->connection->__soapCall('IGuestSession_fsObjQueryInfo', array((array)$request));
         return new IGuestFsObjInfo ($this->connection, $response->returnval);
     }
@@ -6267,7 +7226,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->path = $arg_path;
-        $request->followSymlinks = $arg_followSymlinks;
+        $request->followSymlinks = (bool)$arg_followSymlinks;
         $request->acl = $arg_acl;
         $request->mode = $arg_mode;
         $response = $this->connection->__soapCall('IGuestSession_fsObjSetACL', array((array)$request));
@@ -7204,7 +8163,7 @@
         $request->userName = $arg_userName;
         $request->password = $arg_password;
         $request->domain = $arg_domain;
-        $request->allowInteractiveLogon = $arg_allowInteractiveLogon;
+        $request->allowInteractiveLogon = (bool)$arg_allowInteractiveLogon;
         $response = $this->connection->__soapCall('IGuest_setCredentials', array((array)$request));
         return ;
     }
@@ -7571,6 +8530,14 @@
         }
         $this->connection->__soapCall('IProgress_setTimeout', array((array)$request));
     }
+
+    public function getEventSource()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IProgress_getEventSource', array((array)$request));
+        return new IEventSource ($this->connection, $response->returnval);
+    }
 }
 
 /**
@@ -7708,9 +8675,9 @@
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->setImageId = $arg_setImageId;
+        $request->setImageId = (bool)$arg_setImageId;
         $request->imageId = $arg_imageId;
-        $request->setParentId = $arg_setParentId;
+        $request->setParentId = (bool)$arg_setParentId;
         $request->parentId = $arg_parentId;
         $response = $this->connection->__soapCall('IMedium_setIds', array((array)$request));
         return ;
@@ -7889,7 +8856,11 @@
 
     public function changeEncryption($arg_currentPassword, $arg_cipher, $arg_newPassword, $arg_newPasswordId)
     {
-        $request = new stdClass();
+        // No password ID in case of decryption and de-/encryption are both handled here.
+        $isDecryption      = ($arg_cipher == '') && ($arg_newPassword == '');
+        $arg_newPasswordId = $isDecryption ? '' : $arg_newPasswordId;
+ 
+       $request = new stdClass();
         $request->_this = $this->handle;
         $request->currentPassword = $arg_currentPassword;
         $request->cipher = $arg_cipher;
@@ -8104,11 +9075,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->autoReset = $value;
+            $request->autoReset = (bool)$value;
         }
         else
         {
-            $request->autoReset = $value->handle;
+            $request->autoReset = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMedium_setAutoReset', array((array)$request));
     }
@@ -8663,11 +9634,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->visible = $value;
+            $request->visible = (bool)$value;
         }
         else
         {
-            $request->visible = $value->handle;
+            $request->visible = (bool)$value->handle;
         }
         $this->connection->__soapCall('IFramebufferOverlay_setVisible', array((array)$request));
     }
@@ -8707,6 +9678,101 @@
 /**
  * Generated VBoxWebService Interface Wrapper
  */
+class IGuestScreenInfo extends VBox_ManagedObject
+{
+
+    public function getScreenId()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getScreenId', array((array)$request));
+        return (float)$response->returnval;
+    }
+
+    public function getGuestMonitorStatus()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getGuestMonitorStatus', array((array)$request));
+        return new GuestMonitorStatus ($this->connection, $response->returnval);
+    }
+
+    public function getPrimary()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getPrimary', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function getOrigin()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getOrigin', array((array)$request));
+        return (bool)$response->returnval;
+    }
+
+    public function getOriginX()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getOriginX', array((array)$request));
+        return (int)$response->returnval;
+    }
+
+    public function getOriginY()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getOriginY', array((array)$request));
+        return (int)$response->returnval;
+    }
+
+    public function getWidth()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getWidth', array((array)$request));
+        return (float)$response->returnval;
+    }
+
+    public function getHeight()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getHeight', array((array)$request));
+        return (float)$response->returnval;
+    }
+
+    public function getBitsPerPixel()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getBitsPerPixel', array((array)$request));
+        return (float)$response->returnval;
+    }
+
+    public function getExtendedInfo()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IGuestScreenInfo_getExtendedInfo', array((array)$request));
+        return (string)$response->returnval;
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IGuestScreenInfoCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IGuestScreenInfo";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
 class IDisplay extends VBox_ManagedObject
 {
 
@@ -8753,8 +9819,8 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->display = $arg_display;
-        $request->enabled = $arg_enabled;
-        $request->changeOrigin = $arg_changeOrigin;
+        $request->enabled = (bool)$arg_enabled;
+        $request->changeOrigin = (bool)$arg_changeOrigin;
         $request->originX = $arg_originX;
         $request->originY = $arg_originY;
         $request->width = $arg_width;
@@ -8768,7 +9834,7 @@
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->enabled = $arg_enabled;
+        $request->enabled = (bool)$arg_enabled;
         $response = $this->connection->__soapCall('IDisplay_setSeamlessMode', array((array)$request));
         return ;
     }
@@ -8830,10 +9896,37 @@
     {
         $request = new stdClass();
         $request->_this = $this->handle;
-        $request->fUnscaledHiDPI = $arg_fUnscaledHiDPI;
+        $request->fUnscaledHiDPI = (bool)$arg_fUnscaledHiDPI;
         $response = $this->connection->__soapCall('IDisplay_notifyHiDPIOutputPolicyChange', array((array)$request));
         return ;
     }
+
+    public function setScreenLayout($arg_screenLayoutMode, $arg_guestScreenInfo)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->screenLayoutMode = $arg_screenLayoutMode;
+        $request->guestScreenInfo = $arg_guestScreenInfo;
+        $response = $this->connection->__soapCall('IDisplay_setScreenLayout', array((array)$request));
+        return ;
+    }
+
+    public function detachScreens($arg_screenIds)
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $request->screenIds = $arg_screenIds;
+        $response = $this->connection->__soapCall('IDisplay_detachScreens', array((array)$request));
+        return ;
+    }
+
+    public function getGuestScreenLayout()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IDisplay_getGuestScreenLayout', array((array)$request));
+        return new IGuestScreenInfoCollection ($this->connection, (array)$response->returnval);
+    }
 }
 
 /**
@@ -8923,11 +10016,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabled = $value;
+            $request->enabled = (bool)$value;
         }
         else
         {
-            $request->enabled = $value->handle;
+            $request->enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('INetworkAdapter_setEnabled', array((array)$request));
     }
@@ -9107,11 +10200,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->cableConnected = $value;
+            $request->cableConnected = (bool)$value;
         }
         else
         {
-            $request->cableConnected = $value->handle;
+            $request->cableConnected = (bool)$value->handle;
         }
         $this->connection->__soapCall('INetworkAdapter_setCableConnected', array((array)$request));
     }
@@ -9176,11 +10269,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->traceEnabled = $value;
+            $request->traceEnabled = (bool)$value;
         }
         else
         {
-            $request->traceEnabled = $value->handle;
+            $request->traceEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('INetworkAdapter_setTraceEnabled', array((array)$request));
     }
@@ -9299,11 +10392,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabled = $value;
+            $request->enabled = (bool)$value;
         }
         else
         {
-            $request->enabled = $value->handle;
+            $request->enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('ISerialPort_setEnabled', array((array)$request));
     }
@@ -9391,11 +10484,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->server = $value;
+            $request->server = (bool)$value;
         }
         else
         {
-            $request->server = $value->handle;
+            $request->server = (bool)$value->handle;
         }
         $this->connection->__soapCall('ISerialPort_setServer', array((array)$request));
     }
@@ -9460,11 +10553,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabled = $value;
+            $request->enabled = (bool)$value;
         }
         else
         {
-            $request->enabled = $value->handle;
+            $request->enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IParallelPort_setEnabled', array((array)$request));
     }
@@ -9770,7 +10863,7 @@
         $request = new stdClass();
         $request->_this = $this->handle;
         $request->pattern = $arg_pattern;
-        $request->withDescriptions = $arg_withDescriptions;
+        $request->withDescriptions = (bool)$arg_withDescriptions;
         $response = $this->connection->__soapCall('IMachineDebugger_getStats', array((array)$request));
         return (string)$response->returnval;
     }
@@ -9789,11 +10882,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->singleStep = $value;
+            $request->singleStep = (bool)$value;
         }
         else
         {
-            $request->singleStep = $value->handle;
+            $request->singleStep = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachineDebugger_setSingleStep', array((array)$request));
     }
@@ -9812,11 +10905,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->recompileUser = $value;
+            $request->recompileUser = (bool)$value;
         }
         else
         {
-            $request->recompileUser = $value->handle;
+            $request->recompileUser = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachineDebugger_setRecompileUser', array((array)$request));
     }
@@ -9835,11 +10928,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->recompileSupervisor = $value;
+            $request->recompileSupervisor = (bool)$value;
         }
         else
         {
-            $request->recompileSupervisor = $value->handle;
+            $request->recompileSupervisor = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachineDebugger_setRecompileSupervisor', array((array)$request));
     }
@@ -9858,11 +10951,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->executeAllInIEM = $value;
+            $request->executeAllInIEM = (bool)$value;
         }
         else
         {
-            $request->executeAllInIEM = $value->handle;
+            $request->executeAllInIEM = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachineDebugger_setExecuteAllInIEM', array((array)$request));
     }
@@ -9881,11 +10974,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->PATMEnabled = $value;
+            $request->PATMEnabled = (bool)$value;
         }
         else
         {
-            $request->PATMEnabled = $value->handle;
+            $request->PATMEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachineDebugger_setPATMEnabled', array((array)$request));
     }
@@ -9904,11 +10997,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->CSAMEnabled = $value;
+            $request->CSAMEnabled = (bool)$value;
         }
         else
         {
-            $request->CSAMEnabled = $value->handle;
+            $request->CSAMEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachineDebugger_setCSAMEnabled', array((array)$request));
     }
@@ -9927,11 +11020,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->logEnabled = $value;
+            $request->logEnabled = (bool)$value;
         }
         else
         {
-            $request->logEnabled = $value->handle;
+            $request->logEnabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IMachineDebugger_setLogEnabled', array((array)$request));
     }
@@ -10070,6 +11163,14 @@
         $response = $this->connection->__soapCall('IMachineDebugger_getVM', array((array)$request));
         return (float)$response->returnval;
     }
+
+    public function getUptime()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IMachineDebugger_getUptime', array((array)$request));
+        return (float)$response->returnval;
+    }
 }
 
 /**
@@ -10309,6 +11410,22 @@
         $response = $this->connection->__soapCall('IUSBDevice_getRemote', array((array)$request));
         return (bool)$response->returnval;
     }
+
+    public function getDeviceInfo()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUSBDevice_getDeviceInfo', array((array)$request));
+        return (array)$response->returnval;
+    }
+
+    public function getBackend()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUSBDevice_getBackend', array((array)$request));
+        return (string)$response->returnval;
+    }
 }
 
 /**
@@ -10362,11 +11479,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->active = $value;
+            $request->active = (bool)$value;
         }
         else
         {
-            $request->active = $value->handle;
+            $request->active = (bool)$value->handle;
         }
         $this->connection->__soapCall('IUSBDeviceFilter_setActive', array((array)$request));
     }
@@ -10651,6 +11768,37 @@
 /**
  * Generated VBoxWebService Interface Wrapper
  */
+class IUSBProxyBackend extends VBox_ManagedObject
+{
+
+    public function getName()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUSBProxyBackend_getName', array((array)$request));
+        return (string)$response->returnval;
+    }
+
+    public function getType()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IUSBProxyBackend_getType', array((array)$request));
+        return (string)$response->returnval;
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IUSBProxyBackendCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IUSBProxyBackend";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
 class IAudioAdapter extends VBox_ManagedObject
 {
 
@@ -10687,11 +11835,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabled = $value;
+            $request->enabled = (bool)$value;
         }
         else
         {
-            $request->enabled = $value->handle;
+            $request->enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IAudioAdapter_setEnabled', array((array)$request));
     }
@@ -10710,11 +11858,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabledIn = $value;
+            $request->enabledIn = (bool)$value;
         }
         else
         {
-            $request->enabledIn = $value->handle;
+            $request->enabledIn = (bool)$value->handle;
         }
         $this->connection->__soapCall('IAudioAdapter_setEnabledIn', array((array)$request));
     }
@@ -10867,11 +12015,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->enabled = $value;
+            $request->enabled = (bool)$value;
         }
         else
         {
-            $request->enabled = $value->handle;
+            $request->enabled = (bool)$value->handle;
         }
         $this->connection->__soapCall('IVRDEServer_setEnabled', array((array)$request));
     }
@@ -10936,11 +12084,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->allowMultiConnection = $value;
+            $request->allowMultiConnection = (bool)$value;
         }
         else
         {
-            $request->allowMultiConnection = $value->handle;
+            $request->allowMultiConnection = (bool)$value->handle;
         }
         $this->connection->__soapCall('IVRDEServer_setAllowMultiConnection', array((array)$request));
     }
@@ -10959,11 +12107,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->reuseSingleConnection = $value;
+            $request->reuseSingleConnection = (bool)$value;
         }
         else
         {
-            $request->reuseSingleConnection = $value->handle;
+            $request->reuseSingleConnection = (bool)$value->handle;
         }
         $this->connection->__soapCall('IVRDEServer_setReuseSingleConnection', array((array)$request));
     }
@@ -11253,11 +12401,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->useHostIOCache = $value;
+            $request->useHostIOCache = (bool)$value;
         }
         else
         {
-            $request->useHostIOCache = $value->handle;
+            $request->useHostIOCache = (bool)$value->handle;
         }
         $this->connection->__soapCall('IStorageController_setUseHostIOCache', array((array)$request));
     }
@@ -11709,11 +12857,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->DNSPassDomain = $value;
+            $request->DNSPassDomain = (bool)$value;
         }
         else
         {
-            $request->DNSPassDomain = $value->handle;
+            $request->DNSPassDomain = (bool)$value->handle;
         }
         $this->connection->__soapCall('INATEngine_setDNSPassDomain', array((array)$request));
     }
@@ -11732,11 +12880,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->DNSProxy = $value;
+            $request->DNSProxy = (bool)$value;
         }
         else
         {
-            $request->DNSProxy = $value->handle;
+            $request->DNSProxy = (bool)$value->handle;
         }
         $this->connection->__soapCall('INATEngine_setDNSProxy', array((array)$request));
     }
@@ -11755,11 +12903,11 @@
         $request->_this = $this->handle;
         if (is_null($value) || is_scalar($value))
         {
-            $request->DNSUseHostResolver = $value;
+            $request->DNSUseHostResolver = (bool)$value;
         }
         else
         {
-            $request->DNSUseHostResolver = $value->handle;
+            $request->DNSUseHostResolver = (bool)$value->handle;
         }
         $this->connection->__soapCall('INATEngine_setDNSUseHostResolver', array((array)$request));
     }
@@ -11932,7 +13080,7 @@
         $request->_this = $this->handle;
         $request->listener = $arg_listener;
         $request->interesting = $arg_interesting;
-        $request->active = $arg_active;
+        $request->active = (bool)$arg_active;
         $response = $this->connection->__soapCall('IEventSource_registerListener', array((array)$request));
         return ;
     }
@@ -12656,6 +13804,29 @@
 /**
  * Generated VBoxWebService Interface Wrapper
  */
+class IAudioAdapterChangedEvent extends IEvent
+{
+
+    public function getAudioAdapter()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IAudioAdapterChangedEvent_getAudioAdapter', array((array)$request));
+        return new IAudioAdapter ($this->connection, $response->returnval);
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IAudioAdapterChangedEventCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IAudioAdapterChangedEvent";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
 class ISerialPortChangedEvent extends IEvent
 {
 
@@ -14417,6 +15588,75 @@
 }
 
 /**
+ * Generated VBoxWebService Interface Wrapper
+ */
+class IProgressEvent extends IEvent
+{
+
+    public function getProgressId()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IProgressEvent_getProgressId', array((array)$request));
+        return (string)$response->returnval;
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IProgressEventCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IProgressEvent";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
+class IProgressPercentageChangedEvent extends IProgressEvent
+{
+
+    public function getPercent()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IProgressPercentageChangedEvent_getPercent', array((array)$request));
+        return (int)$response->returnval;
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IProgressPercentageChangedEventCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IProgressPercentageChangedEvent";
+}
+
+/**
+ * Generated VBoxWebService Interface Wrapper
+ */
+class IProgressTaskCompletedEvent extends IProgressEvent
+{
+
+    public function getMidlDoesNotLikeEmptyInterfaces()
+    {
+        $request = new stdClass();
+        $request->_this = $this->handle;
+        $response = $this->connection->__soapCall('IProgressTaskCompletedEvent_getMidlDoesNotLikeEmptyInterfaces', array((array)$request));
+        return (bool)$response->returnval;
+    }
+}
+
+/**
+ * Generated VBoxWebService Managed Object Collection
+ */
+class IProgressTaskCompletedEventCollection extends VBox_ManagedObjectCollection
+{
+    protected $_interfaceName = "IProgressTaskCompletedEvent";
+}
+
+/**
  * Generated VBoxWebService Struct
  */
 class IPCIDeviceAttachment extends VBox_Struct
@@ -14605,7 +15845,9 @@
     protected $recommendedAudioCodec;
     protected $recommendedFloppy;
     protected $recommendedUSB;
+    protected $recommendedUSB3;
     protected $recommendedTFReset;
+    protected $recommendedX2APIC;
 
     public function __construct($connection, $values)
     {
@@ -14638,7 +15880,9 @@
         $this->recommendedAudioCodec = $values->recommendedAudioCodec;
         $this->recommendedFloppy = $values->recommendedFloppy;
         $this->recommendedUSB = $values->recommendedUSB;
+        $this->recommendedUSB3 = $values->recommendedUSB3;
         $this->recommendedTFReset = $values->recommendedTFReset;
+        $this->recommendedX2APIC = $values->recommendedX2APIC;
     }
 
     public function getFamilyId()
@@ -14753,10 +15997,18 @@
     {
         return (bool)$this->recommendedUSB;
     }
+    public function getRecommendedUSB3()
+    {
+        return (bool)$this->recommendedUSB3;
+    }
     public function getRecommendedTFReset()
     {
         return (bool)$this->recommendedTFReset;
     }
+    public function getRecommendedX2APIC()
+    {
+        return (bool)$this->recommendedX2APIC;
+    }
 }
 
 /**
@@ -14973,8 +16225,8 @@
  */
 class SettingsVersion extends VBox_Enum
 {
-    public $NameMap = array(0 => 'Null', 1 => 'v1_0', 2 => 'v1_1', 3 => 'v1_2', 4 => 'v1_3pre', 5 => 'v1_3', 6 => 'v1_4', 7 => 'v1_5', 8 => 'v1_6', 9 => 'v1_7', 10 => 'v1_8', 11 => 'v1_9', 12 => 'v1_10', 13 => 'v1_11', 14 => 'v1_12', 15 => 'v1_13', 16 => 'v1_14', 17 => 'v1_15', 99999 => 'Future');
-    public $ValueMap = array('Null' => 0, 'v1_0' => 1, 'v1_1' => 2, 'v1_2' => 3, 'v1_3pre' => 4, 'v1_3' => 5, 'v1_4' => 6, 'v1_5' => 7, 'v1_6' => 8, 'v1_7' => 9, 'v1_8' => 10, 'v1_9' => 11, 'v1_10' => 12, 'v1_11' => 13, 'v1_12' => 14, 'v1_13' => 15, 'v1_14' => 16, 'v1_15' => 17, 'Future' => 99999);
+    public $NameMap = array(0 => 'Null', 1 => 'v1_0', 2 => 'v1_1', 3 => 'v1_2', 4 => 'v1_3pre', 5 => 'v1_3', 6 => 'v1_4', 7 => 'v1_5', 8 => 'v1_6', 9 => 'v1_7', 10 => 'v1_8', 11 => 'v1_9', 12 => 'v1_10', 13 => 'v1_11', 14 => 'v1_12', 15 => 'v1_13', 16 => 'v1_14', 17 => 'v1_15', 18 => 'v1_16', 19 => 'v1_17', 99999 => 'Future');
+    public $ValueMap = array('Null' => 0, 'v1_0' => 1, 'v1_1' => 2, 'v1_2' => 3, 'v1_3pre' => 4, 'v1_3' => 5, 'v1_4' => 6, 'v1_5' => 7, 'v1_6' => 8, 'v1_7' => 9, 'v1_8' => 10, 'v1_9' => 11, 'v1_10' => 12, 'v1_11' => 13, 'v1_12' => 14, 'v1_13' => 15, 'v1_14' => 16, 'v1_15' => 17, 'v1_16' => 18, 'v1_17' => 19, 'Future' => 99999);
 }
 
 /**
@@ -15041,8 +16293,8 @@
  */
 class CPUPropertyType extends VBox_Enum
 {
-    public $NameMap = array(0 => 'Null', 1 => 'PAE', 2 => 'LongMode', 3 => 'TripleFaultReset');
-    public $ValueMap = array('Null' => 0, 'PAE' => 1, 'LongMode' => 2, 'TripleFaultReset' => 3);
+    public $NameMap = array(0 => 'Null', 1 => 'PAE', 2 => 'LongMode', 3 => 'TripleFaultReset', 4 => 'APIC', 5 => 'X2APIC');
+    public $ValueMap = array('Null' => 0, 'PAE' => 1, 'LongMode' => 2, 'TripleFaultReset' => 3, 'APIC' => 4, 'X2APIC' => 5);
 }
 
 /**
@@ -15243,6 +16495,23 @@
 /**
  * Generated VBoxWebService ENUM
  */
+class APICMode extends VBox_Enum
+{
+    public $NameMap = array(0 => 'Disabled', 1 => 'APIC', 2 => 'X2APIC');
+    public $ValueMap = array('Disabled' => 0, 'APIC' => 1, 'X2APIC' => 2);
+}
+
+/**
+ * Generated VBoxWebService Enum Collection
+ */
+class APICModeCollection extends VBox_EnumCollection
+{
+    protected $_interfaceName = "APICMode";
+}
+
+/**
+ * Generated VBoxWebService ENUM
+ */
 class ProcessorFeature extends VBox_Enum
 {
     public $NameMap = array(0 => 'HWVirtEx', 1 => 'PAE', 2 => 'LongMode', 3 => 'NestedPaging');
@@ -15413,6 +16682,23 @@
 /**
  * Generated VBoxWebService ENUM
  */
+class CertificateVersion extends VBox_Enum
+{
+    public $NameMap = array(1 => 'V1', 2 => 'V2', 3 => 'V3', 99 => 'Unknown');
+    public $ValueMap = array('V1' => 1, 'V2' => 2, 'V3' => 3, 'Unknown' => 99);
+}
+
+/**
+ * Generated VBoxWebService Enum Collection
+ */
+class CertificateVersionCollection extends VBox_EnumCollection
+{
+    protected $_interfaceName = "CertificateVersion";
+}
+
+/**
+ * Generated VBoxWebService ENUM
+ */
 class VirtualSystemDescriptionType extends VBox_Enum
 {
     public $NameMap = array(1 => 'Ignore', 2 => 'OS', 3 => 'Name', 4 => 'Product', 5 => 'Vendor', 6 => 'Version', 7 => 'ProductUrl', 8 => 'VendorUrl', 9 => 'Description', 10 => 'License', 11 => 'Miscellaneous', 12 => 'CPU', 13 => 'Memory', 14 => 'HardDiskControllerIDE', 15 => 'HardDiskControllerSATA', 16 => 'HardDiskControllerSCSI', 17 => 'HardDiskControllerSAS', 18 => 'HardDiskImage', 19 => 'Floppy', 20 => 'CDROM', 21 => 'NetworkAdapter', 22 => 'USBController', 23 => 'SoundCard', 24 => 'SettingsFile');
@@ -15585,8 +16871,8 @@
  */
 class AdditionsFacilityType extends VBox_Enum
 {
-    public $NameMap = array(0 => 'None', 20 => 'VBoxGuestDriver', 90 => 'AutoLogon', 100 => 'VBoxService', 101 => 'VBoxTrayClient', 1000 => 'Seamless', 1100 => 'Graphics', 2147483646 => 'All');
-    public $ValueMap = array('None' => 0, 'VBoxGuestDriver' => 20, 'AutoLogon' => 90, 'VBoxService' => 100, 'VBoxTrayClient' => 101, 'Seamless' => 1000, 'Graphics' => 1100, 'All' => 2147483646);
+    public $NameMap = array(0 => 'None', 20 => 'VBoxGuestDriver', 90 => 'AutoLogon', 100 => 'VBoxService', 101 => 'VBoxTrayClient', 1000 => 'Seamless', 1100 => 'Graphics', 1101 => 'MonitorAttach', 2147483646 => 'All');
+    public $ValueMap = array('None' => 0, 'VBoxGuestDriver' => 20, 'AutoLogon' => 90, 'VBoxService' => 100, 'VBoxTrayClient' => 101, 'Seamless' => 1000, 'Graphics' => 1100, 'MonitorAttach' => 1101, 'All' => 2147483646);
 }
 
 /**
@@ -15925,8 +17211,8 @@
  */
 class ProcessCreateFlag extends VBox_Enum
 {
-    public $NameMap = array(0 => 'None', 1 => 'WaitForProcessStartOnly', 2 => 'IgnoreOrphanedProcesses', 4 => 'Hidden', 8 => 'NoProfile', 16 => 'WaitForStdOut', 32 => 'WaitForStdErr', 64 => 'ExpandArguments', 128 => 'UnquotedArguments');
-    public $ValueMap = array('None' => 0, 'WaitForProcessStartOnly' => 1, 'IgnoreOrphanedProcesses' => 2, 'Hidden' => 4, 'NoProfile' => 8, 'WaitForStdOut' => 16, 'WaitForStdErr' => 32, 'ExpandArguments' => 64, 'UnquotedArguments' => 128);
+    public $NameMap = array(0 => 'None', 1 => 'WaitForProcessStartOnly', 2 => 'IgnoreOrphanedProcesses', 4 => 'Hidden', 8 => 'Profile', 16 => 'WaitForStdOut', 32 => 'WaitForStdErr', 64 => 'ExpandArguments', 128 => 'UnquotedArguments');
+    public $ValueMap = array('None' => 0, 'WaitForProcessStartOnly' => 1, 'IgnoreOrphanedProcesses' => 2, 'Hidden' => 4, 'Profile' => 8, 'WaitForStdOut' => 16, 'WaitForStdErr' => 32, 'ExpandArguments' => 64, 'UnquotedArguments' => 128);
 }
 
 /**
@@ -16265,8 +17551,8 @@
  */
 class MediumFormatCapabilities extends VBox_Enum
 {
-    public $NameMap = array(0x01 => 'Uuid', 0x02 => 'CreateFixed', 0x04 => 'CreateDynamic', 0x08 => 'CreateSplit2G', 0x10 => 'Differencing', 0x20 => 'Asynchronous', 0x40 => 'File', 0x80 => 'Properties', 0x100 => 'TcpNetworking', 0x200 => 'VFS', 0x3FF => 'CapabilityMask');
-    public $ValueMap = array('Uuid' => 0x01, 'CreateFixed' => 0x02, 'CreateDynamic' => 0x04, 'CreateSplit2G' => 0x08, 'Differencing' => 0x10, 'Asynchronous' => 0x20, 'File' => 0x40, 'Properties' => 0x80, 'TcpNetworking' => 0x100, 'VFS' => 0x200, 'CapabilityMask' => 0x3FF);
+    public $NameMap = array(0x01 => 'Uuid', 0x02 => 'CreateFixed', 0x04 => 'CreateDynamic', 0x08 => 'CreateSplit2G', 0x10 => 'Differencing', 0x20 => 'Asynchronous', 0x40 => 'File', 0x80 => 'Properties', 0x100 => 'TcpNetworking', 0x200 => 'VFS', 0x400 => 'Discard', 0x800 => 'Preferred', 0xFFF => 'CapabilityMask');
+    public $ValueMap = array('Uuid' => 0x01, 'CreateFixed' => 0x02, 'CreateDynamic' => 0x04, 'CreateSplit2G' => 0x08, 'Differencing' => 0x10, 'Asynchronous' => 0x20, 'File' => 0x40, 'Properties' => 0x80, 'TcpNetworking' => 0x100, 'VFS' => 0x200, 'Discard' => 0x400, 'Preferred' => 0x800, 'CapabilityMask' => 0xFFF);
 }
 
 /**
@@ -16350,8 +17636,8 @@
  */
 class GuestMonitorStatus extends VBox_Enum
 {
-    public $NameMap = array(0 => 'Disabled', 1 => 'Enabled');
-    public $ValueMap = array('Disabled' => 0, 'Enabled' => 1);
+    public $NameMap = array(0 => 'Disabled', 1 => 'Enabled', 2 => 'Blank');
+    public $ValueMap = array('Disabled' => 0, 'Enabled' => 1, 'Blank' => 2);
 }
 
 /**
@@ -16365,6 +17651,23 @@
 /**
  * Generated VBoxWebService ENUM
  */
+class ScreenLayoutMode extends VBox_Enum
+{
+    public $NameMap = array(0 => 'Apply', 1 => 'Reset', 2 => 'Attach');
+    public $ValueMap = array('Apply' => 0, 'Reset' => 1, 'Attach' => 2);
+}
+
+/**
+ * Generated VBoxWebService Enum Collection
+ */
+class ScreenLayoutModeCollection extends VBox_EnumCollection
+{
+    protected $_interfaceName = "ScreenLayoutMode";
+}
+
+/**
+ * Generated VBoxWebService ENUM
+ */
 class NetworkAttachmentType extends VBox_Enum
 {
     public $NameMap = array(0 => 'Null', 1 => 'NAT', 2 => 'Bridged', 3 => 'Internal', 4 => 'HostOnly', 5 => 'Generic', 6 => 'NATNetwork');
@@ -16588,8 +17891,8 @@
  */
 class StorageBus extends VBox_Enum
 {
-    public $NameMap = array(0 => 'Null', 1 => 'IDE', 2 => 'SATA', 3 => 'SCSI', 4 => 'Floppy', 5 => 'SAS', 6 => 'USB');
-    public $ValueMap = array('Null' => 0, 'IDE' => 1, 'SATA' => 2, 'SCSI' => 3, 'Floppy' => 4, 'SAS' => 5, 'USB' => 6);
+    public $NameMap = array(0 => 'Null', 1 => 'IDE', 2 => 'SATA', 3 => 'SCSI', 4 => 'Floppy', 5 => 'SAS', 6 => 'USB', 7 => 'PCIe');
+    public $ValueMap = array('Null' => 0, 'IDE' => 1, 'SATA' => 2, 'SCSI' => 3, 'Floppy' => 4, 'SAS' => 5, 'USB' => 6, 'PCIe' => 7);
 }
 
 /**
@@ -16605,8 +17908,8 @@
  */
 class StorageControllerType extends VBox_Enum
 {
-    public $NameMap = array(0 => 'Null', 1 => 'LsiLogic', 2 => 'BusLogic', 3 => 'IntelAhci', 4 => 'PIIX3', 5 => 'PIIX4', 6 => 'ICH6', 7 => 'I82078', 8 => 'LsiLogicSas', 9 => 'USB');
-    public $ValueMap = array('Null' => 0, 'LsiLogic' => 1, 'BusLogic' => 2, 'IntelAhci' => 3, 'PIIX3' => 4, 'PIIX4' => 5, 'ICH6' => 6, 'I82078' => 7, 'LsiLogicSas' => 8, 'USB' => 9);
+    public $NameMap = array(0 => 'Null', 1 => 'LsiLogic', 2 => 'BusLogic', 3 => 'IntelAhci', 4 => 'PIIX3', 5 => 'PIIX4', 6 => 'ICH6', 7 => 'I82078', 8 => 'LsiLogicSas', 9 => 'USB', 10 => 'NVMe');
+    public $ValueMap = array('Null' => 0, 'LsiLogic' => 1, 'BusLogic' => 2, 'IntelAhci' => 3, 'PIIX3' => 4, 'PIIX4' => 5, 'ICH6' => 6, 'I82078' => 7, 'LsiLogicSas' => 8, 'USB' => 9, 'NVMe' => 10);
 }
 
 /**
@@ -16690,8 +17993,8 @@
  */
 class VBoxEventType extends VBox_Enum
 {
-    public $NameMap = array(0 => 'Invalid', 1 => 'Any', 2 => 'Vetoable', 3 => 'MachineEvent', 4 => 'SnapshotEvent', 5 => 'InputEvent', 31 => 'LastWildcard', 32 => 'OnMachineStateChanged', 33 => 'OnMachineDataChanged', 34 => 'OnExtraDataChanged', 35 => 'OnExtraDataCanChange', 36 => 'OnMediumRegistered', 37 => 'OnMachineRegistered', 38 => 'OnSessionStateChanged', 39 => 'OnSnapshotTaken', 40 => 'OnSnapshotDeleted', 41 => 'OnSnapshotChanged', 42 => 'OnGuestPropertyChanged', 43 => 'OnMousePointerShapeChanged', 44 => 'OnMouseCapabilityChanged', 45 => 'OnKeyboardLedsChanged', 46 => 'OnStateChanged', 47 => 'OnAdditionsStateChanged', 48 => 'OnNetworkAdapterChanged', 49 => 'OnSerialPortChanged', 50 => 'OnParallelPortChanged', 51 => 'OnStorageControllerChanged', 52 => 'OnMediumChanged', 53 => 'OnVRDEServerChanged', 54 => 'OnUSBControllerChanged', 55 => 'OnUSBDeviceStateChanged', 56 => 'OnSharedFolderChanged', 57 => 'OnRuntimeError', 58 => 'OnCanShowWindow', 59 => 'OnShowWindow', 60 => 'OnCPUChanged', 61 => 'OnVRDEServerInfoChanged', 62 => 'OnEventSourceChanged', 63 => 'OnCPUExecutionCapChanged', 64 => 'OnGuestKeyboard', 65 => 'OnGuestMouse', 66 => 'OnNATRedirect', 67 => 'OnHostPCIDevicePlug', 68 => 'OnVBoxSVCAvailabilityChanged', 69 => 'OnBandwidthGroupChanged', 70 => 'OnGuestMonitorChanged', 71 => 'OnStorageDeviceChanged', 72 => 'OnClipboardModeChanged', 73 => 'OnDnDModeChanged', 74 => 'OnNATNetworkChanged', 75 => 'OnNATNetworkStartStop', 76 => 'OnNATNetworkAlter', 77 => 'OnNATNetworkCreationDeletion', 78 => 'OnNATNetworkSetting', 79 => 'OnNATNetworkPortForward', 80 => 'OnGuestSessionStateChanged', 81 => 'OnGuestSessionRegistered', 82 => 'OnGuestProcessRegistered', 83 => 'OnGuestProcessStateChanged', 84 => 'OnGuestProcessInputNotify', 85 => 'OnGuestProcessOutput', 86 => 'OnGuestFileRegistered', 87 => 'OnGuestFileStateChanged', 88 => 'OnGuestFileOffsetChanged', 89 => 'OnGuestFileRead', 90 => 'OnGuestFileWrite', 91 => 'OnVideoCaptureChanged', 92 => 'OnGuestUserStateChanged', 93 => 'OnGuestMultiTouch', 94 => 'OnHostNameResolutionConfigurationChange', 95 => 'OnSnapshotRestored', 96 => 'OnMediumConfigChanged', 97 => 'Last');
-    public $ValueMap = array('Invalid' => 0, 'Any' => 1, 'Vetoable' => 2, 'MachineEvent' => 3, 'SnapshotEvent' => 4, 'InputEvent' => 5, 'LastWildcard' => 31, 'OnMachineStateChanged' => 32, 'OnMachineDataChanged' => 33, 'OnExtraDataChanged' => 34, 'OnExtraDataCanChange' => 35, 'OnMediumRegistered' => 36, 'OnMachineRegistered' => 37, 'OnSessionStateChanged' => 38, 'OnSnapshotTaken' => 39, 'OnSnapshotDeleted' => 40, 'OnSnapshotChanged' => 41, 'OnGuestPropertyChanged' => 42, 'OnMousePointerShapeChanged' => 43, 'OnMouseCapabilityChanged' => 44, 'OnKeyboardLedsChanged' => 45, 'OnStateChanged' => 46, 'OnAdditionsStateChanged' => 47, 'OnNetworkAdapterChanged' => 48, 'OnSerialPortChanged' => 49, 'OnParallelPortChanged' => 50, 'OnStorageControllerChanged' => 51, 'OnMediumChanged' => 52, 'OnVRDEServerChanged' => 53, 'OnUSBControllerChanged' => 54, 'OnUSBDeviceStateChanged' => 55, 'OnSharedFolderChanged' => 56, 'OnRuntimeError' => 57, 'OnCanShowWindow' => 58, 'OnShowWindow' => 59, 'OnCPUChanged' => 60, 'OnVRDEServerInfoChanged' => 61, 'OnEventSourceChanged' => 62, 'OnCPUExecutionCapChanged' => 63, 'OnGuestKeyboard' => 64, 'OnGuestMouse' => 65, 'OnNATRedirect' => 66, 'OnHostPCIDevicePlug' => 67, 'OnVBoxSVCAvailabilityChanged' => 68, 'OnBandwidthGroupChanged' => 69, 'OnGuestMonitorChanged' => 70, 'OnStorageDeviceChanged' => 71, 'OnClipboardModeChanged' => 72, 'OnDnDModeChanged' => 73, 'OnNATNetworkChanged' => 74, 'OnNATNetworkStartStop' => 75, 'OnNATNetworkAlter' => 76, 'OnNATNetworkCreationDeletion' => 77, 'OnNATNetworkSetting' => 78, 'OnNATNetworkPortForward' => 79, 'OnGuestSessionStateChanged' => 80, 'OnGuestSessionRegistered' => 81, 'OnGuestProcessRegistered' => 82, 'OnGuestProcessStateChanged' => 83, 'OnGuestProcessInputNotify' => 84, 'OnGuestProcessOutput' => 85, 'OnGuestFileRegistered' => 86, 'OnGuestFileStateChanged' => 87, 'OnGuestFileOffsetChanged' => 88, 'OnGuestFileRead' => 89, 'OnGuestFileWrite' => 90, 'OnVideoCaptureChanged' => 91, 'OnGuestUserStateChanged' => 92, 'OnGuestMultiTouch' => 93, 'OnHostNameResolutionConfigurationChange' => 94, 'OnSnapshotRestored' => 95, 'OnMediumConfigChanged' => 96, 'Last' => 97);
+    public $NameMap = array(0 => 'Invalid', 1 => 'Any', 2 => 'Vetoable', 3 => 'MachineEvent', 4 => 'SnapshotEvent', 5 => 'InputEvent', 31 => 'LastWildcard', 32 => 'OnMachineStateChanged', 33 => 'OnMachineDataChanged', 34 => 'OnExtraDataChanged', 35 => 'OnExtraDataCanChange', 36 => 'OnMediumRegistered', 37 => 'OnMachineRegistered', 38 => 'OnSessionStateChanged', 39 => 'OnSnapshotTaken', 40 => 'OnSnapshotDeleted', 41 => 'OnSnapshotChanged', 42 => 'OnGuestPropertyChanged', 43 => 'OnMousePointerShapeChanged', 44 => 'OnMouseCapabilityChanged', 45 => 'OnKeyboardLedsChanged', 46 => 'OnStateChanged', 47 => 'OnAdditionsStateChanged', 48 => 'OnNetworkAdapterChanged', 49 => 'OnSerialPortChanged', 50 => 'OnParallelPortChanged', 51 => 'OnStorageControllerChanged', 52 => 'OnMediumChanged', 53 => 'OnVRDEServerChanged', 54 => 'OnUSBControllerChanged', 55 => 'OnUSBDeviceStateChanged', 56 => 'OnSharedFolderChanged', 57 => 'OnRuntimeError', 58 => 'OnCanShowWindow', 59 => 'OnShowWindow', 60 => 'OnCPUChanged', 61 => 'OnVRDEServerInfoChanged', 62 => 'OnEventSourceChanged', 63 => 'OnCPUExecutionCapChanged', 64 => 'OnGuestKeyboard', 65 => 'OnGuestMouse', 66 => 'OnNATRedirect', 67 => 'OnHostPCIDevicePlug', 68 => 'OnVBoxSVCAvailabilityChanged', 69 => 'OnBandwidthGroupChanged', 70 => 'OnGuestMonitorChanged', 71 => 'OnStorageDeviceChanged', 72 => 'OnClipboardModeChanged', 73 => 'OnDnDModeChanged', 74 => 'OnNATNetworkChanged', 75 => 'OnNATNetworkStartStop', 76 => 'OnNATNetworkAlter', 77 => 'OnNATNetworkCreationDeletion', 78 => 'OnNATNetworkSetting', 79 => 'OnNATNetworkPortForward', 80 => 'OnGuestSessionStateChanged', 81 => 'OnGuestSessionRegistered', 82 => 'OnGuestProcessRegistered', 83 => 'OnGuestProcessStateChanged', 84 => 'OnGuestProcessInputNotify', 85 => 'OnGuestProcessOutput', 86 => 'OnGuestFileRegistered', 87 => 'OnGuestFileStateChanged', 88 => 'OnGuestFileOffsetChanged', 89 => 'OnGuestFileRead', 90 => 'OnGuestFileWrite', 91 => 'OnVideoCaptureChanged', 92 => 'OnGuestUserStateChanged', 93 => 'OnGuestMultiTouch', 94 => 'OnHostNameResolutionConfigurationChange', 95 => 'OnSnapshotRestored', 96 => 'OnMediumConfigChanged', 97 => 'OnAudioAdapterChanged', 98 => 'OnProgressPercentageChanged', 99 => 'OnProgressTaskCompleted', 100 => 'Last');
+    public $ValueMap = array('Invalid' => 0, 'Any' => 1, 'Vetoable' => 2, 'MachineEvent' => 3, 'SnapshotEvent' => 4, 'InputEvent' => 5, 'LastWildcard' => 31, 'OnMachineStateChanged' => 32, 'OnMachineDataChanged' => 33, 'OnExtraDataChanged' => 34, 'OnExtraDataCanChange' => 35, 'OnMediumRegistered' => 36, 'OnMachineRegistered' => 37, 'OnSessionStateChanged' => 38, 'OnSnapshotTaken' => 39, 'OnSnapshotDeleted' => 40, 'OnSnapshotChanged' => 41, 'OnGuestPropertyChanged' => 42, 'OnMousePointerShapeChanged' => 43, 'OnMouseCapabilityChanged' => 44, 'OnKeyboardLedsChanged' => 45, 'OnStateChanged' => 46, 'OnAdditionsStateChanged' => 47, 'OnNetworkAdapterChanged' => 48, 'OnSerialPortChanged' => 49, 'OnParallelPortChanged' => 50, 'OnStorageControllerChanged' => 51, 'OnMediumChanged' => 52, 'OnVRDEServerChanged' => 53, 'OnUSBControllerChanged' => 54, 'OnUSBDeviceStateChanged' => 55, 'OnSharedFolderChanged' => 56, 'OnRuntimeError' => 57, 'OnCanShowWindow' => 58, 'OnShowWindow' => 59, 'OnCPUChanged' => 60, 'OnVRDEServerInfoChanged' => 61, 'OnEventSourceChanged' => 62, 'OnCPUExecutionCapChanged' => 63, 'OnGuestKeyboard' => 64, 'OnGuestMouse' => 65, 'OnNATRedirect' => 66, 'OnHostPCIDevicePlug' => 67, 'OnVBoxSVCAvailabilityChanged' => 68, 'OnBandwidthGroupChanged' => 69, 'OnGuestMonitorChanged' => 70, 'OnStorageDeviceChanged' => 71, 'OnClipboardModeChanged' => 72, 'OnDnDModeChanged' => 73, 'OnNATNetworkChanged' => 74, 'OnNATNetworkStartStop' => 75, 'OnNATNetworkAlter' => 76, 'OnNATNetworkCreationDeletion' => 77, 'OnNATNetworkSetting' => 78, 'OnNATNetworkPortForward' => 79, 'OnGuestSessionStateChanged' => 80, 'OnGuestSessionRegistered' => 81, 'OnGuestProcessRegistered' => 82, 'OnGuestProcessStateChanged' => 83, 'OnGuestProcessInputNotify' => 84, 'OnGuestProcessOutput' => 85, 'OnGuestFileRegistered' => 86, 'OnGuestFileStateChanged' => 87, 'OnGuestFileOffsetChanged' => 88, 'OnGuestFileRead' => 89, 'OnGuestFileWrite' => 90, 'OnVideoCaptureChanged' => 91, 'OnGuestUserStateChanged' => 92, 'OnGuestMultiTouch' => 93, 'OnHostNameResolutionConfigurationChange' => 94, 'OnSnapshotRestored' => 95, 'OnMediumConfigChanged' => 96, 'OnAudioAdapterChanged' => 97, 'OnProgressPercentageChanged' => 98, 'OnProgressTaskCompleted' => 99, 'Last' => 100);
 }
 
 /**
