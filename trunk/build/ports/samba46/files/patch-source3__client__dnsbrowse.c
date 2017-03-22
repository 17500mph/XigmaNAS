--- source3/client/dnsbrowse.c.orig	2015-07-21 09:47:49 UTC
+++ source3/client/dnsbrowse.c	2015-12-07 02:08:01 UTC
@@ -91,7 +91,7 @@
 		}
 	}
 
-	TALLOC_FREE(fdset);
+	TALLOC_FREE(ctx);
 	DNSServiceRefDeallocate(mdns_conn_sdref);
 }
 
