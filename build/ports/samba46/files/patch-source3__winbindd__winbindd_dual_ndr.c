--- source3/winbindd/winbindd_dual_ndr.c.org	2017-03-17 15:25:55 UTC
+++ source3/winbindd/winbindd_dual_ndr.c	2017-03-17 15:26:41 UTC
@@ -304,7 +304,7 @@
 	int num_fns;
 	bool ret;

-	fns = winbind_get_pipe_fns(&num_fns);
+	winbind_get_pipe_fns(&fns, &num_fns);

 	if (state->request->data.ndrcmd >= num_fns) {
 		return WINBINDD_ERROR;
