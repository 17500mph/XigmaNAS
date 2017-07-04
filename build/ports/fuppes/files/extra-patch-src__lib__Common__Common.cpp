--- src/lib/Common/Common.cpp.orig	2011-09-22 10:45:44.000000000 +0200
+++ src/lib/Common/Common.cpp	2017-07-02 00:33:28.000000000 +0200
@@ -46,6 +46,7 @@
 #include <algorithm>
 #include <cctype>
 #include <time.h>
+#include <unistd.h>
 
 #ifndef WIN32
 #include <dlfcn.h>
@@ -408,11 +409,11 @@
 	else {*/
     if(p_sEncoding.compare("UTF-8") == 0)
       return p_sValue;
-  
+
 		icv = iconv_open("UTF-8", p_sEncoding.c_str());
 	//}
-	
-  if(icv < 0)  
+
+  if(icv == (iconv_t) -1)
     return p_sValue;  
   
   size_t nInbytes  = p_sValue.length(); 
