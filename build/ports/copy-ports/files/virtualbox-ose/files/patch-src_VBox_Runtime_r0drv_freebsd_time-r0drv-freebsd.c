--- src/VBox/Runtime/r0drv/freebsd/time-r0drv-freebsd.c.orig	2015-04-14 15:41:35.000000000 -0400
+++ src/VBox/Runtime/r0drv/freebsd/time-r0drv-freebsd.c	2015-04-23 19:10:05.312328000 -0400
@@ -40,16 +40,23 @@
 
 RTDECL(uint64_t) RTTimeNanoTS(void)
 {
-    struct timespec tsp;
-    nanouptime(&tsp);
-    return tsp.tv_sec * RT_NS_1SEC_64
-         + tsp.tv_nsec;
+    struct bintime bt;
+    uint64_t ns;
+    binuptime(&bt);
+    ns = RT_NS_1SEC_64 * bt.sec;
+    ns += (RT_NS_1SEC_64 * (uint32_t)(bt.frac >> 32)) >> 32;
+    return ns;
 }
 
 
 RTDECL(uint64_t) RTTimeMilliTS(void)
 {
-    return RTTimeNanoTS() / RT_NS_1MS;
+    struct bintime bt;
+    uint64_t ms;
+    binuptime(&bt);
+    ms = RT_MS_1SEC_64 * bt.sec;
+    ms += (RT_MS_1SEC_64 * (uint32_t)(bt.frac >> 32)) >> 32;
+    return ms;
 }
 
 
@@ -67,8 +74,7 @@
 
 RTDECL(PRTTIMESPEC) RTTimeNow(PRTTIMESPEC pTime)
 {
-    struct timespec tsp;
-    nanotime(&tsp);
-    return RTTimeSpecSetTimespec(pTime, &tsp);
+    struct timespec ts;
+    nanotime(&ts);
+    return RTTimeSpecSetTimespec(pTime, &ts);
 }
-
