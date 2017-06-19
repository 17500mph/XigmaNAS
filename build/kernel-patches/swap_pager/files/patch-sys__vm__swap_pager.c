--- sys/vm/swap_pager.c.orig	2017-06-19 03:40:54.137695000 +0200
+++ sys/vm/swap_pager.c	2017-06-19 04:27:13.000000000 +0200
@@ -847,7 +847,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
