--- src/VBox/Frontends/VirtualBox/src/settings/machine/UIMachineSettingsAudio.cpp.orig	2015-06-01 13:55:19.000000000 -0400
+++ src/VBox/Frontends/VirtualBox/src/settings/machine/UIMachineSettingsAudio.cpp	2015-06-04 14:55:06.593769000 -0400
@@ -161,17 +161,14 @@
 
 #if defined Q_OS_LINUX || defined Q_OS_FREEBSD
     m_pComboAudioDriver->setItemText(++iIndex, gpConverter->toString(KAudioDriverType_OSS));
+# ifdef VBOX_WITH_ALSA
+    m_pComboAudioDriver->setItemText(++iIndex, gpConverter->toString(KAudioDriverType_ALSA));
+# endif /* VBOX_WITH_ALSA */
 # ifdef VBOX_WITH_PULSE
     m_pComboAudioDriver->setItemText(++iIndex, gpConverter->toString(KAudioDriverType_Pulse));
 # endif /* VBOX_WITH_PULSE */
 #endif /* Q_OS_LINUX | Q_OS_FREEBSD */
 
-#ifdef Q_OS_LINUX
-# ifdef VBOX_WITH_ALSA
-    m_pComboAudioDriver->setItemText(++iIndex, gpConverter->toString(KAudioDriverType_ALSA));
-# endif /* VBOX_WITH_ALSA */
-#endif /* Q_OS_LINUX */
-
 #ifdef Q_OS_MACX
     m_pComboAudioDriver->setItemText(++iIndex, gpConverter->toString(KAudioDriverType_CoreAudio));
 #endif /* Q_OS_MACX */
@@ -229,17 +226,14 @@
 
 #if defined Q_OS_LINUX || defined Q_OS_FREEBSD
     m_pComboAudioDriver->insertItem(++iIndex, "", KAudioDriverType_OSS);
+# ifdef VBOX_WITH_ALSA
+    m_pComboAudioDriver->insertItem(++iIndex, "", KAudioDriverType_ALSA);
+# endif /* VBOX_WITH_ALSA */
 # ifdef VBOX_WITH_PULSE
     m_pComboAudioDriver->insertItem(++iIndex, "", KAudioDriverType_Pulse);
 # endif /* VBOX_WITH_PULSE */
 #endif /* Q_OS_LINUX | Q_OS_FREEBSD */
 
-#ifdef Q_OS_LINUX
-# ifdef VBOX_WITH_ALSA
-    m_pComboAudioDriver->insertItem(++iIndex, "", KAudioDriverType_ALSA);
-# endif /* VBOX_WITH_ALSA */
-#endif /* Q_OS_LINUX */
-
 #ifdef Q_OS_MACX
     m_pComboAudioDriver->insertItem(++iIndex, "", KAudioDriverType_CoreAudio);
 #endif /* Q_OS_MACX */
