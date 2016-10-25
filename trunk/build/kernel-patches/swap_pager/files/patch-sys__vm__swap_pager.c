--- sys/vm/swap_pager.c.orig	2016-10-07 08:57:18.045998000 +0200
+++ sys/vm/swap_pager.c	2016-10-07 09:23:13.000000000 +0200
@@ -847,7 +847,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
