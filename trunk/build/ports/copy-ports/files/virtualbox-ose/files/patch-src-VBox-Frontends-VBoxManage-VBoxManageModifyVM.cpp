--- src/VBox/Frontends/VBoxManage/VBoxManageModifyVM.cpp.orig	2015-06-04 14:50:37.278693000 -0400
+++ src/VBox/Frontends/VBoxManage/VBoxManageModifyVM.cpp	2015-06-04 15:07:21.389661000 -0400
@@ -2177,7 +2177,7 @@
                     CHECK_ERROR(audioAdapter, COMSETTER(Enabled)(true));
                 }
 #endif /* RT_OS_WINDOWS */
-#ifdef RT_OS_LINUX
+#if defined(RT_OS_FREEBSD) || defined(RT_OS_LINUX)
 # ifdef VBOX_WITH_ALSA
                 else if (!RTStrICmp(ValueUnion.psz, "alsa"))
                 {
@@ -2192,7 +2192,7 @@
                     CHECK_ERROR(audioAdapter, COMSETTER(Enabled)(true));
                 }
 # endif
-#endif /* !RT_OS_LINUX */
+#endif /* !RT_OS_FREEBSD && !RT_OS_LINUX */
 #ifdef RT_OS_SOLARIS
                 else if (!RTStrICmp(ValueUnion.psz, "solaudio"))
                 {
@@ -2200,20 +2200,6 @@
                     CHECK_ERROR(audioAdapter, COMSETTER(Enabled)(true));
                 }
 #endif /* !RT_OS_SOLARIS */
-#ifdef RT_OS_FREEBSD
-                else if (!RTStrICmp(ValueUnion.psz, "oss"))
-                {
-                    CHECK_ERROR(audioAdapter, COMSETTER(AudioDriver)(AudioDriverType_OSS));
-                    CHECK_ERROR(audioAdapter, COMSETTER(Enabled)(true));
-                }
-# ifdef VBOX_WITH_PULSE
-                else if (!RTStrICmp(ValueUnion.psz, "pulse"))
-                {
-                    CHECK_ERROR(audioAdapter, COMSETTER(AudioDriver)(AudioDriverType_Pulse));
-                    CHECK_ERROR(audioAdapter, COMSETTER(Enabled)(true));
-                }
-# endif
-#endif /* !RT_OS_FREEBSD */
 #ifdef RT_OS_DARWIN
                 else if (!RTStrICmp(ValueUnion.psz, "coreaudio"))
                 {
