--- src/VBox/Additions/common/pam/pam_vbox.cpp.orig	2015-04-14 15:38:08.000000000 -0400
+++ src/VBox/Additions/common/pam/pam_vbox.cpp	2015-04-28 18:50:50.923476000 -0400
@@ -104,7 +104,7 @@
     openlog("pam_vbox", LOG_PID, LOG_AUTHPRIV);
     syslog(LOG_ERR, "%s", pszBuf);
     closelog();
-#elif defined(RT_OS_SOLARIS)
+#elif defined(RT_OS_FREEBSD) || defined(RT_OS_SOLARIS)
     syslog(LOG_ERR, "pam_vbox: %s\n", pszBuf);
 #endif
 }
@@ -179,7 +179,7 @@
 
     pam_message msg;
     msg.msg_style = iStyle;
-#ifdef RT_OS_SOLARIS
+#if defined(RT_OS_FREEBSD) || defined(RT_OS_SOLARIS)
     msg.msg = (char*)pszText;
 #else
     msg.msg = pszText;
