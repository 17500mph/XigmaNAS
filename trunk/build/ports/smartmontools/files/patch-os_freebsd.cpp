--- os_freebsd.cpp.orig	2017-04-24 18:34:16.000000000 +0100
+++ os_freebsd.cpp	2017-11-11 18:08:49.000000000 +0100
@@ -1447,6 +1447,8 @@
   virtual smart_device * get_custom_smart_device(const char * name, const char * type);
 
   virtual std::string get_valid_custom_dev_types_str();
+private:
+  bool get_nvme_devlist(smart_device_list & devlist, const char * type);
 };
 
 
@@ -1716,6 +1718,12 @@
     return false;
   }
 
+#ifdef WITH_NVME_DEVICESCAN // TODO: Remove when NVMe support is no longer EXPERIMENTAL
+  bool scan_nvme = !type || !strcmp(type, "nvme");
+#else
+  bool scan_nvme = type &&  !strcmp(type, "nvme");
+#endif
+
   // Make namelists
   char * * atanames = 0; int numata = 0;
   if (!type || !strcmp(type, "ata")) {
@@ -1758,9 +1766,32 @@
         devlist.push_back(scsidev);
     }
   }
+
+  if (scan_nvme)
+    get_nvme_devlist(devlist, type);
   return true;
 }
 
+bool freebsd_smart_interface::get_nvme_devlist(smart_device_list & devlist,
+    const char * type)
+{
+  char ctrlpath[64];
+  nvme_device * nvmedev;
+
+  for (int ctrlr = 0;; ctrlr++) {
+    sprintf(ctrlpath, "%s%d", NVME_CTRLR_PREFIX, ctrlr);
+    int fd = ::open(ctrlpath, O_RDWR);
+    if (fd < 0)
+       break;
+    ::close(fd);
+    nvmedev = get_nvme_device(ctrlpath, type, 0);
+    if (nvmedev)
+        devlist.push_back(nvmedev);
+    else
+        break;
+ }
+  return true;
+}
 
 #if (FREEBSDVER < 800000) // without this build fail on FreeBSD 8
 static char done[USB_MAX_DEVICES];
@@ -2004,6 +2035,8 @@
   // form /dev/nvme* or nvme*
   if(!strncmp("/dev/nvme", test_name, strlen("/dev/nvme")))
     return new freebsd_nvme_device(this, name, "", 0 /* use default nsid */);
+  if(!strncmp("/dev/nvd", test_name, strlen("/dev/nvd")))
+    set_err(EINVAL, "To monitor NVMe disks use /dev/nvme* device names");
 
   // device type unknown
   return 0;
