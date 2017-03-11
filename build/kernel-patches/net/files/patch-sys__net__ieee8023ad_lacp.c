--- ieee8023ad_lacp.c.orig	2017-03-10 12:58:18.009215000 +0100
+++ ieee8023ad_lacp.c	2017-03-11 21:33:28.000000000 +0100
@@ -526,9 +526,6 @@
 	struct ifmultiaddr *rifma = NULL;
 	int error;
 
-	boolean_t active = TRUE; /* XXX should be configurable */
-	boolean_t fast = FALSE; /* Configurable via ioctl */ 
-
 	link_init_sdl(ifp, (struct sockaddr *)&sdl, IFT_ETHER);
 	sdl.sdl_alen = ETHER_ADDR_LEN;
 
@@ -557,9 +554,7 @@
 
 	lacp_fill_actorinfo(lp, &lp->lp_actor);
 	lacp_fill_markerinfo(lp, &lp->lp_marker);
-	lp->lp_state =
-	    (active ? LACP_STATE_ACTIVITY : 0) |
-	    (fast ? LACP_STATE_TIMEOUT : 0);
+	lp->lp_state = LACP_STATE_ACTIVITY;
 	lp->lp_aggregator = NULL;
 	lacp_sm_rx_set_expired(lp);
 	LACP_UNLOCK(lsc);
