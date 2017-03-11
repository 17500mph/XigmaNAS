--- if_lagg.c.orig	2017-03-10 12:58:17.000000000 +0100
+++ if_lagg.c	2017-03-11 22:18:18.000000000 +0100
@@ -1022,7 +1022,7 @@
 	return (error);
 
 fallback:
-	if (lp->lp_ioctl != NULL)
+	if (lp != NULL && lp->lp_ioctl != NULL)
 		return ((*lp->lp_ioctl)(ifp, cmd, data));
 
 	return (EINVAL);
