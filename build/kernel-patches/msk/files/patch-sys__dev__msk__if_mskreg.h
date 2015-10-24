Index: sys/dev/msk/if_mskreg.h
===================================================================
--- sys/dev/msk/if_mskreg.h	(revision 288826)
+++ sys/dev/msk/if_mskreg.h	(working copy)
@@ -2175,13 +2175,8 @@
 #define MSK_ADDR_LO(x)	((uint64_t) (x) & 0xffffffffUL)
 #define MSK_ADDR_HI(x)	((uint64_t) (x) >> 32)
 
-/*
- * At first I guessed 8 bytes, the size of a single descriptor, would be
- * required alignment constraints. But, it seems that Yukon II have 4096
- * bytes boundary alignment constraints.
- */
-#define MSK_RING_ALIGN	4096
-#define	MSK_STAT_ALIGN	4096
+#define	MSK_RING_ALIGN	32768
+#define	MSK_STAT_ALIGN	32768
 
 /* Rx descriptor data structure */
 struct msk_rx_desc {
