--- src/VBox/Frontends/VBoxManage/VBoxManageHelp.cpp.orig	2015-06-01 13:55:01.000000000 -0400
+++ src/VBox/Frontends/VBoxManage/VBoxManageHelp.cpp	2015-06-04 15:03:32.105882000 -0400
@@ -298,7 +298,7 @@
 #endif
                         );
         }
-        if (fLinux)
+        if (fLinux || fFreeBSD)
         {
             RTStrmPrintf(pStrm, "|oss"
 #ifdef VBOX_WITH_ALSA
@@ -309,20 +309,6 @@
 #endif
                         );
         }
-        if (fFreeBSD)
-        {
-            /* Get the line break sorted when dumping all option variants. */
-            if (fDumpOpts)
-            {
-                RTStrmPrintf(pStrm, "|\n"
-                     "                                     oss");
-            }
-            else
-                RTStrmPrintf(pStrm, "|oss");
-#ifdef VBOX_WITH_PULSE
-            RTStrmPrintf(pStrm, "|pulse");
-#endif
-        }
         if (fDarwin)
         {
             RTStrmPrintf(pStrm, "|coreaudio");
