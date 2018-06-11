--- sys/vm/swap_pager.c.orig	2018-06-11 01:21:24.378069000 +0200
+++ sys/vm/swap_pager.c	2018-06-11 02:07:55.000000000 +0200
@@ -860,7 +860,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* Happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
