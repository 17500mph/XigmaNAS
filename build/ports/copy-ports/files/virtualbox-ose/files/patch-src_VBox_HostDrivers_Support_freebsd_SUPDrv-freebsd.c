--- src/VBox/HostDrivers/Support/freebsd/SUPDrv-freebsd.c.orig	2015-11-10 17:06:12 UTC
+++ src/VBox/HostDrivers/Support/freebsd/SUPDrv-freebsd.c
@@ -541,8 +541,7 @@ bool VBOXCALL  supdrvOSGetForcedAsyncTsc
 
 bool VBOXCALL  supdrvOSAreCpusOfflinedOnSuspend(void)
 {
-    /** @todo verify this. */
-    return false;
+    return true;
 }
 
 
