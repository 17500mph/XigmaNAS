--- source3/modules/vfs_shadow_copy2.c.orig	2017-03-07 10:09:03.000000000 +0100
+++ source3/modules/vfs_shadow_copy2.c	2017-03-22 10:39:13.000000000 +0100
@@ -1774,7 +1774,7 @@
 					&smb_fname,
 					false,
 					SEC_DIR_LIST);
-	if (!NT_STATUS_IS_OK(status)) {
+	if (NT_STATUS_EQUAL(status, NT_STATUS_ACCESS_DENIED)) {
 		DEBUG(0,("user does not have list permission "
 			"on snapdir %s\n",
 			smb_fname.base_name));
