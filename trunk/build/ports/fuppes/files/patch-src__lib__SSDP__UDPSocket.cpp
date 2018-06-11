--- src/lib/SSDP/UDPSocket.cpp.orig	2011-08-15 10:25:54.000000000 +0200
+++ src/lib/SSDP/UDPSocket.cpp	2018-06-11 23:06:35.000000000 +0200
@@ -132,8 +132,8 @@
 	memset(&(m_LocalEndpoint.sin_zero), '\0', 8); // fill the rest of the structure with zero
 	
 	/* Bind socket */
-	ret = bind(m_Socket, (struct sockaddr*)&m_LocalEndpoint, sizeof(m_LocalEndpoint)); 
+	auto ret_bind = bind(m_Socket, (struct sockaddr*)&m_LocalEndpoint, sizeof(m_LocalEndpoint)); 
   if(ret == -1) {
     throw fuppes::Exception(__FILE__, __LINE__, "failed to bind udp socket %s", p_sIPAddress.c_str());
   }
 	
