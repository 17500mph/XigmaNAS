--- minissdp.c.orig	2015-09-10 21:24:09.000000000 +0200
+++ minissdp.c	2017-05-23 17:58:54.000000000 +0200
@@ -60,7 +60,7 @@
 AddMulticastMembership(int s, struct lan_addr_s *iface)
 {
 	int ret;
-#ifdef HAVE_STRUCT_IP_MREQN
+#if defined(HAVE_STRUCT_IP_MREQN) && !defined(__FreeBSD__)
 	struct ip_mreqn imr;	/* Ip multicast membership */
 	/* setting up imr structure */
 	memset(&imr, '\0', sizeof(imr));
@@ -113,11 +113,18 @@
 	memset(&sockname, 0, sizeof(struct sockaddr_in));
 	sockname.sin_family = AF_INET;
 	sockname.sin_port = htons(SSDP_PORT);
+#ifdef __linux__
 	/* NOTE: Binding a socket to a UDP multicast address means, that we just want
 	 * to receive datagramms send to this multicast address.
 	 * To specify the local nics we want to use we have to use setsockopt,
 	 * see AddMulticastMembership(...). */
 	sockname.sin_addr.s_addr = inet_addr(SSDP_MCAST_ADDR);
+#else
+	/* NOTE: Binding to SSDP_MCAST_ADDR on Darwin & *BSD causes NOTIFY replies are
+	 * sent from SSDP_MCAST_ADDR what forces some clients to ignore subsequent
+	 * unsolicited NOTIFY packets from the real interface address. */
+	sockname.sin_addr.s_addr = htonl(INADDR_ANY);
+#endif
 
 	if (bind(s, (struct sockaddr *)&sockname, sizeof(struct sockaddr_in)) < 0)
 	{
@@ -165,13 +172,6 @@
 	}
 
 	setsockopt(s, IPPROTO_IP, IP_MULTICAST_TTL, &ttl, sizeof(ttl));
-	
-	if (setsockopt(s, SOL_SOCKET, SO_BROADCAST, &bcast, sizeof(bcast)) < 0)
-	{
-		DPRINTF(E_ERROR, L_SSDP, "setsockopt(udp_notify, SO_BROADCAST): %s\n", strerror(errno));
-		close(s);
-		return -1;
-	}
 
 	memset(&sockname, 0, sizeof(struct sockaddr_in));
 	sockname.sin_family = AF_INET;
