--- src/mod_deflate.c.orig	2017-11-11 17:30:25.000000000 +0100
+++ src/mod_deflate.c	2017-12-05 20:37:30.000000000 +0100
@@ -841,6 +841,7 @@
 			free(start);
 			return -1;
 		}
+		abs_offset = 0;
 	}
 
 #ifdef USE_MMAP
