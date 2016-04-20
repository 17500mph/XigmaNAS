--- src/VBox/Devices/build/VBoxDD.cpp.orig	2015-06-01 13:54:34.000000000 -0400
+++ src/VBox/Devices/build/VBoxDD.cpp	2015-06-04 14:59:08.925853000 -0400
@@ -281,7 +281,7 @@
     if (RT_FAILURE(rc))
         return rc;
 #endif
-#if defined(RT_OS_LINUX)
+#if defined(RT_OS_LINUX) || defined(RT_OS_FREEBSD)
 # ifdef VBOX_WITH_PULSE
     rc = pCallbacks->pfnRegister(pCallbacks, &g_DrvHostPulseAudio);
     if (RT_FAILURE(rc))
@@ -295,12 +295,7 @@
     rc = pCallbacks->pfnRegister(pCallbacks, &g_DrvHostOSSAudio);
     if (RT_FAILURE(rc))
         return rc;
-#endif /* RT_OS_LINUX */
-#if defined(RT_OS_FREEBSD)
-    rc = pCallbacks->pfnRegister(pCallbacks, &g_DrvHostOSSAudio);
-    if (RT_FAILURE(rc))
-        return rc;
-#endif
+#endif /* RT_OS_LINUX || RT_OS_FREEBSD */
 #if defined(RT_OS_DARWIN)
     rc = pCallbacks->pfnRegister(pCallbacks, &g_DrvHostCoreAudio);
     if (RT_FAILURE(rc))
