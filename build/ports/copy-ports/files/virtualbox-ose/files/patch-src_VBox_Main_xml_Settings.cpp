--- src/VBox/Main/xml/Settings.cpp.orig	2015-06-01 13:55:44.000000000 -0400
+++ src/VBox/Main/xml/Settings.cpp	2015-06-04 15:01:05.473090000 -0400
@@ -5434,22 +5434,17 @@
 #ifdef RT_OS_SOLARIS
         case AudioDriverType_SolAudio:
 #endif
-#ifdef RT_OS_LINUX
+#if defined (RT_OS_LINUX) || defined (RT_OS_FREEBSD)
 # ifdef VBOX_WITH_ALSA
         case AudioDriverType_ALSA:
 # endif
 # ifdef VBOX_WITH_PULSE
         case AudioDriverType_Pulse:
 # endif
-#endif /* RT_OS_LINUX */
+#endif /* RT_OS_LINUX || RT_OS_FREEBSD */
 #if defined (RT_OS_LINUX) || defined (RT_OS_FREEBSD) || defined(VBOX_WITH_SOLARIS_OSS)
         case AudioDriverType_OSS:
 #endif
-#ifdef RT_OS_FREEBSD
-# ifdef VBOX_WITH_PULSE
-        case AudioDriverType_Pulse:
-# endif
-#endif
 #ifdef RT_OS_DARWIN
         case AudioDriverType_CoreAudio:
 #endif
