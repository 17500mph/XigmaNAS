--- sys/vm/swap_pager.c.orig	2017-07-02 00:36:55.329851000 +0200
+++ sys/vm/swap_pager.c	2017-07-02 00:52:05.000000000 +0200
@@ -837,7 +837,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
