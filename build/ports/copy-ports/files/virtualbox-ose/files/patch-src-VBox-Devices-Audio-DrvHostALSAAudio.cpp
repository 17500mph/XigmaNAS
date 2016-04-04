--- src/VBox/Devices/Audio/DrvHostALSAAudio.cpp.orig	2015-09-08 07:04:47 UTC
+++ src/VBox/Devices/Audio/DrvHostALSAAudio.cpp
@@ -986,6 +986,7 @@ static DECLCALLBACK(int) drvHostALSAAudi
                             continue;
                         }
 
+#if EPIPE != ESTRPIPE
                         case -ESTRPIPE:
                         {
                             /* Stream was suspended and waiting for a recovery. */
@@ -999,6 +1000,7 @@ static DECLCALLBACK(int) drvHostALSAAudi
                             LogFlowFunc(("Resumed suspended output stream\n"));
                             continue;
                         }
+#endif
 
                         default:
                             LogFlowFunc(("Failed to write %RI32 output frames, rc=%Rrc\n",
