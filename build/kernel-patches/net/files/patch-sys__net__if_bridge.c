--- if_bridge.c.orig	2017-03-10 12:58:17.193306000 +0100
+++ if_bridge.c	2017-03-11 21:21:37.000000000 +0100
@@ -166,7 +166,8 @@
 /*
  * List of capabilities to possibly mask on the member interface.
  */
-#define	BRIDGE_IFCAPS_MASK		(IFCAP_TOE|IFCAP_TSO|IFCAP_TXCSUM)
+#define	BRIDGE_IFCAPS_MASK		(IFCAP_TOE|IFCAP_TSO|IFCAP_TXCSUM|\
+					 IFCAP_TXCSUM_IPV6)
 
 /*
  * List of capabilities to strip
@@ -332,7 +333,7 @@
 #ifdef INET6
 static int	bridge_ip6_checkbasic(struct mbuf **mp);
 #endif /* INET6 */
-static int	bridge_fragment(struct ifnet *, struct mbuf *,
+static int	bridge_fragment(struct ifnet *, struct mbuf **mp,
 		    struct ether_header *, int, struct llc *);
 static void	bridge_linkstate(struct ifnet *ifp);
 static void	bridge_linkcheck(struct bridge_softc *sc);
@@ -908,14 +909,18 @@
 		mask &= bif->bif_savedcaps;
 	}
 
+	BRIDGE_XLOCK(sc);
 	LIST_FOREACH(bif, &sc->sc_iflist, bif_next) {
 		enabled = bif->bif_ifp->if_capenable;
 		enabled &= ~BRIDGE_IFCAPS_STRIP;
 		/* strip off mask bits and enable them again if allowed */
 		enabled &= ~BRIDGE_IFCAPS_MASK;
 		enabled |= mask;
+		BRIDGE_UNLOCK(sc);
 		bridge_set_ifcap(sc, bif, enabled);
+		BRIDGE_LOCK(sc);
 	}
+	BRIDGE_XDROP(sc);
 
 }
 
@@ -926,6 +931,8 @@
 	struct ifreq ifr;
 	int error;
 
+	BRIDGE_UNLOCK_ASSERT(sc);
+
 	bzero(&ifr, sizeof(ifr));
 	ifr.ifr_reqcap = set;
 
@@ -1916,6 +1923,7 @@
 			m->m_flags &= ~M_VLANTAG;
 		}
 
+		M_ASSERTPKTHDR(m); /* We shouldn't transmit mbuf without pkthdr */
 		if ((err = dst_ifp->if_transmit(dst_ifp, m))) {
 			m_freem(m0);
 			if_inc_counter(sc->sc_ifp, IFCOUNTER_OERRORS, 1);
@@ -3233,10 +3241,12 @@
 			break;
 
 		/* check if we need to fragment the packet */
+		/* bridge_fragment generates a mbuf chain of packets */
+		/* that already include eth headers */
 		if (V_pfil_member && ifp != NULL && dir == PFIL_OUT) {
 			i = (*mp)->m_pkthdr.len;
 			if (i > ifp->if_mtu) {
-				error = bridge_fragment(ifp, *mp, &eh2, snap,
+				error = bridge_fragment(ifp, mp, &eh2, snap,
 					    &llc1);
 				return (error);
 			}
@@ -3475,56 +3485,77 @@
 /*
  * bridge_fragment:
  *
- *	Return a fragmented mbuf chain.
+ *	Fragment mbuf chain in multiple packets and prepend ethernet header.
  */
 static int
-bridge_fragment(struct ifnet *ifp, struct mbuf *m, struct ether_header *eh,
+bridge_fragment(struct ifnet *ifp, struct mbuf **mp, struct ether_header *eh,
     int snap, struct llc *llc)
 {
-	struct mbuf *m0;
+	struct mbuf *m = *mp, *nextpkt = NULL, *mprev = NULL, *mcur = NULL;
 	struct ip *ip;
 	int error = -1;
 
 	if (m->m_len < sizeof(struct ip) &&
 	    (m = m_pullup(m, sizeof(struct ip))) == NULL)
-		goto out;
+		goto dropit;
 	ip = mtod(m, struct ip *);
 
 	m->m_pkthdr.csum_flags |= CSUM_IP;
 	error = ip_fragment(ip, &m, ifp->if_mtu, ifp->if_hwassist);
 	if (error)
-		goto out;
+		goto dropit;
 
-	/* walk the chain and re-add the Ethernet header */
-	for (m0 = m; m0; m0 = m0->m_nextpkt) {
-		if (error == 0) {
-			if (snap) {
-				M_PREPEND(m0, sizeof(struct llc), M_NOWAIT);
-				if (m0 == NULL) {
-					error = ENOBUFS;
-					continue;
-				}
-				bcopy(llc, mtod(m0, caddr_t),
-				    sizeof(struct llc));
-			}
-			M_PREPEND(m0, ETHER_HDR_LEN, M_NOWAIT);
-			if (m0 == NULL) {
+	/*
+	 * Walk the chain and re-add the Ethernet header for
+	 * each mbuf packet.
+	 */
+	for (mcur = m; mcur; mcur = mcur->m_nextpkt) {
+		nextpkt = mcur->m_nextpkt;
+		mcur->m_nextpkt = NULL;
+		if (snap) {
+			M_PREPEND(mcur, sizeof(struct llc), M_NOWAIT);
+			if (mcur == NULL) {
 				error = ENOBUFS;
-				continue;
+				if (mprev != NULL)
+					mprev->m_nextpkt = nextpkt;
+				goto dropit;
 			}
-			bcopy(eh, mtod(m0, caddr_t), ETHER_HDR_LEN);
-		} else
-			m_freem(m);
-	}
+			bcopy(llc, mtod(mcur, caddr_t),sizeof(struct llc));
+		}
 
-	if (error == 0)
-		KMOD_IPSTAT_INC(ips_fragmented);
+		M_PREPEND(mcur, ETHER_HDR_LEN, M_NOWAIT);
+		if (mcur == NULL) {
+			error = ENOBUFS;
+			if (mprev != NULL)
+				mprev->m_nextpkt = nextpkt;
+			goto dropit;
+		}
+		bcopy(eh, mtod(mcur, caddr_t), ETHER_HDR_LEN);
 
+		/*
+		 * The previous two M_PREPEND could have inserted one or two
+		 * mbufs in front so we have to update the previous packet's
+		 * m_nextpkt.
+		 */
+		mcur->m_nextpkt = nextpkt;
+		if (mprev != NULL)
+			mprev->m_nextpkt = mcur;
+		else {
+			/* The first mbuf in the original chain needs to be
+			 * updated. */
+			*mp = mcur;
+		}
+		mprev = mcur;
+	}
+
+	KMOD_IPSTAT_INC(ips_fragmented);
 	return (error);
 
-out:
-	if (m != NULL)
-		m_freem(m);
+dropit:
+	for (mcur = *mp; mcur; mcur = m) { /* droping the full packet chain */
+		m = mcur->m_nextpkt;
+		m_freem(mcur);
+	}
 	return (error);
 }
 
