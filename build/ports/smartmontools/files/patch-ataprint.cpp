--- ataprint.cpp.orig	2017-10-29 16:13:58.000000000 +0100
+++ ataprint.cpp	2017-11-11 17:39:02.000000000 +0100
@@ -40,7 +40,7 @@
 #include "utility.h"
 #include "knowndrives.h"
 
-const char * ataprint_cpp_cvsid = "$Id: ataprint.cpp 4573 2017-10-29 15:13:58Z chrfranke $"
+const char * ataprint_cpp_cvsid = "$Id: ataprint.cpp 4604 2017-11-08 06:30:44Z chrfranke $"
                                   ATAPRINT_H_CVSID;
 
 
@@ -3687,7 +3687,7 @@
     bool use_gplog = true;
     unsigned nsectors = 0;
     if (gplogdir) 
-      nsectors = GetNumLogSectors(gplogdir, 0x04, false);
+      nsectors = GetNumLogSectors(gplogdir, 0x04, true);
     else if (smartlogdir){ // for systems without ATA_READ_LOG_EXT
       nsectors = GetNumLogSectors(smartlogdir, 0x04, false);
       use_gplog = false;
