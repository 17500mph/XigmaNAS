--- rtsock.c.orig	2017-03-10 12:58:17.206424000 +0100
+++ rtsock.c	2017-03-11 21:23:14.000000000 +0100
@@ -1566,8 +1566,8 @@
 }
 
 static int
-sysctl_iflist_ifml(struct ifnet *ifp, struct rt_addrinfo *info,
-    struct walkarg *w, int len)
+sysctl_iflist_ifml(struct ifnet *ifp, const struct if_data *src_ifd,
+    struct rt_addrinfo *info, struct walkarg *w, int len)
 {
 	struct if_msghdrl *ifm;
 	struct if_data *ifd;
@@ -1598,14 +1598,14 @@
 		ifd = &ifm->ifm_data;
 	}
 
-	if_data_copy(ifp, ifd);
+	memcpy(ifd, src_ifd, sizeof(*ifd));
 
 	return (SYSCTL_OUT(w->w_req, (caddr_t)ifm, len));
 }
 
 static int
-sysctl_iflist_ifm(struct ifnet *ifp, struct rt_addrinfo *info,
-    struct walkarg *w, int len)
+sysctl_iflist_ifm(struct ifnet *ifp, const struct if_data *src_ifd,
+    struct rt_addrinfo *info, struct walkarg *w, int len)
 {
 	struct if_msghdr *ifm;
 	struct if_data *ifd;
@@ -1630,7 +1630,7 @@
 		ifd = &ifm->ifm_data;
 	}
 
-	if_data_copy(ifp, ifd);
+	memcpy(ifd, src_ifd, sizeof(*ifd));
 
 	return (SYSCTL_OUT(w->w_req, (caddr_t)ifm, len));
 }
@@ -1705,15 +1705,18 @@
 {
 	struct ifnet *ifp;
 	struct ifaddr *ifa;
+	struct if_data ifd;
 	struct rt_addrinfo info;
 	int len, error = 0;
 	struct sockaddr_storage ss;
 
 	bzero((caddr_t)&info, sizeof(info));
+	bzero(&ifd, sizeof(ifd));
 	IFNET_RLOCK_NOSLEEP();
 	TAILQ_FOREACH(ifp, &V_ifnet, if_link) {
 		if (w->w_arg && w->w_arg != ifp->if_index)
 			continue;
+		if_data_copy(ifp, &ifd);
 		IF_ADDR_RLOCK(ifp);
 		ifa = ifp->if_addr;
 		info.rti_info[RTAX_IFP] = ifa->ifa_addr;
@@ -1723,9 +1726,11 @@
 		info.rti_info[RTAX_IFP] = NULL;
 		if (w->w_req && w->w_tmem) {
 			if (w->w_op == NET_RT_IFLISTL)
-				error = sysctl_iflist_ifml(ifp, &info, w, len);
+				error = sysctl_iflist_ifml(ifp, &ifd, &info, w,
+				    len);
 			else
-				error = sysctl_iflist_ifm(ifp, &info, w, len);
+				error = sysctl_iflist_ifm(ifp, &ifd, &info, w,
+				    len);
 			if (error)
 				goto done;
 		}
@@ -1768,13 +1773,15 @@
 static int
 sysctl_ifmalist(int af, struct walkarg *w)
 {
-	struct ifnet *ifp;
-	struct ifmultiaddr *ifma;
-	struct	rt_addrinfo info;
-	int	len, error = 0;
+	struct rt_addrinfo info;
 	struct ifaddr *ifa;
+	struct ifmultiaddr *ifma;
+	struct ifnet *ifp;
+	int error, len;
 
+	error = 0;
 	bzero((caddr_t)&info, sizeof(info));
+
 	IFNET_RLOCK_NOSLEEP();
 	TAILQ_FOREACH(ifp, &V_ifnet, if_link) {
 		if (w->w_arg && w->w_arg != ifp->if_index)
@@ -1794,7 +1801,7 @@
 			    ifma->ifma_lladdr : NULL;
 			error = rtsock_msg_buffer(RTM_NEWMADDR, &info, w, &len);
 			if (error != 0)
-				goto done;
+				break;
 			if (w->w_req && w->w_tmem) {
 				struct ifma_msghdr *ifmam;
 
@@ -1803,15 +1810,14 @@
 				ifmam->ifmam_flags = 0;
 				ifmam->ifmam_addrs = info.rti_addrs;
 				error = SYSCTL_OUT(w->w_req, w->w_tmem, len);
-				if (error) {
-					IF_ADDR_RUNLOCK(ifp);
-					goto done;
-				}
+				if (error != 0)
+					break;
 			}
 		}
 		IF_ADDR_RUNLOCK(ifp);
+		if (error != 0)
+			break;
 	}
-done:
 	IFNET_RUNLOCK_NOSLEEP();
 	return (error);
 }
@@ -1941,3 +1947,4 @@
 };
 
 VNET_DOMAIN_SET(route);
+
