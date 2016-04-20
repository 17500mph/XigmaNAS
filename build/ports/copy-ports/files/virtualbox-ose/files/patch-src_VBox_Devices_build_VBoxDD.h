--- src/VBox/Devices/build/VBoxDD.h.orig	2015-06-01 13:54:34.000000000 -0400
+++ src/VBox/Devices/build/VBoxDD.h	2015-06-04 15:33:33.261856000 -0400
@@ -118,7 +118,7 @@
 #if defined(RT_OS_WINDOWS)
 extern const PDMDRVREG g_DrvHostDSound;
 #endif
-#if defined(RT_OS_LINUX)
+#if defined(RT_OS_LINUX) || defined(RT_OS_FREEBSD)
 extern const PDMDRVREG g_DrvHostPulseAudio;
 extern const PDMDRVREG g_DrvHostALSAAudio;
 extern const PDMDRVREG g_DrvHostOSSAudio;
@@ -130,9 +130,6 @@
 extern const PDMDRVREG g_DrvHostOSSAudio;
 extern const PDMDRVREG g_DrvHostSolAudio;
 #endif
-#if defined(RT_OS_FREEBSD)
-extern const PDMDRVREG g_DrvHostOSSAudio;
-#endif
 extern const PDMDRVREG g_DrvACPI;
 extern const PDMDRVREG g_DrvAcpiCpu;
 extern const PDMDRVREG g_DrvVUSBRootHub;
