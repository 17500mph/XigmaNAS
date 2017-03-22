--- source4/rpc_server/lsa/dcesrv_lsa.c.org	2017-03-17 14:50:39 UTC
+++ source4/rpc_server/lsa/dcesrv_lsa.c	2017-03-17 14:52:54 UTC
@@ -1,6 +1,6 @@
 /* need access mask/acl implementation */
 
-/* 
+/*
    Unix SMB/CIFS implementation.
 
    endpoint server for the lsarpc pipe
@@ -45,7 +45,7 @@
 
 static NTSTATUS lsarpc__op_init_server(struct dcesrv_context *dce_ctx,
 				       const struct dcesrv_endpoint_server *ep_server);
-static const struct dcesrv_interface dcesrv_lsarpc_interface;
+const struct dcesrv_interface dcesrv_lsarpc_interface;
 
 #define DCESRV_INTERFACE_LSARPC_INIT_SERVER	\
        dcesrv_interface_lsarpc_init_server
@@ -4718,8 +4718,8 @@
 }
 
 
-/* 
-  dssetup_DsRoleGetDcOperationResults 
+/*
+  dssetup_DsRoleGetDcOperationResults
 */
 static WERROR dcesrv_dssetup_DsRoleGetDcOperationResults(struct dcesrv_call_state *dce_call, TALLOC_CTX *mem_ctx,
 					    struct dssetup_DsRoleGetDcOperationResults *r)
@@ -4728,8 +4728,8 @@
 }
 
 
-/* 
-  dssetup_DsRoleCancel 
+/*
+  dssetup_DsRoleCancel
 */
 static WERROR dcesrv_dssetup_DsRoleCancel(struct dcesrv_call_state *dce_call, TALLOC_CTX *mem_ctx,
 			     struct dssetup_DsRoleCancel *r)
