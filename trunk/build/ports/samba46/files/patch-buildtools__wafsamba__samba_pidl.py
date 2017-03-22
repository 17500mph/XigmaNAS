--- buildtools/wafsamba/samba_pidl.py.orig	2013-01-27 23:15:21 UTC
+++ buildtools/wafsamba/samba_pidl.py	2013-01-27 23:15:50 UTC
@@ -88,7 +88,7 @@
     # prime the list of nodes we are dependent on with the cached pidl sources
     t.allnodes = pidl_src_nodes
 
-    t.env.PIDL = os.path.join(bld.srcnode.abspath(), 'pidl/pidl')
+    t.env.PIDL = os.path.join('%%LOCALBASE%%', 'bin/pidl')
     t.env.OPTIONS = TO_LIST(options)
     t.env.OUTPUTDIR = bld.bldnode.name + '/' + bld.path.find_dir(output_dir).bldpath(t.env)
 
