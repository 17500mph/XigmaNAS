--- if_bridgevar.h.orig	2017-03-10 12:58:18.026317000 +0100
+++ if_bridgevar.h	2017-03-11 21:16:49.000000000 +0100
@@ -280,6 +280,7 @@
 #define BRIDGE_LOCK(_sc)		mtx_lock(&(_sc)->sc_mtx)
 #define BRIDGE_UNLOCK(_sc)		mtx_unlock(&(_sc)->sc_mtx)
 #define BRIDGE_LOCK_ASSERT(_sc)		mtx_assert(&(_sc)->sc_mtx, MA_OWNED)
+#define BRIDGE_UNLOCK_ASSERT(_sc)       mtx_assert(&(_sc)->sc_mtx, MA_NOTOWNED)
 #define	BRIDGE_LOCK2REF(_sc, _err)	do {	\
 	mtx_assert(&(_sc)->sc_mtx, MA_OWNED);	\
 	if ((_sc)->sc_iflist_xcnt > 0)		\
