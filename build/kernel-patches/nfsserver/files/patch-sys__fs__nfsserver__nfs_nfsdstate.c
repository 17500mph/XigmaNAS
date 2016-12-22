--- sys/fs/nfsserver/nfs_nfsdstate.c.orig	2016-12-22 18:38:46.337647000 +0100
+++ sys/fs/nfsserver/nfs_nfsdstate.c	2016-12-22 18:51:18.000000000 +0100
@@ -26,7 +26,7 @@
  */
 
 #include <sys/cdefs.h>
-__FBSDID("$FreeBSD: releng/11.0/sys/fs/nfsserver/nfs_nfsdstate.c 298788 2016-04-29 16:07:25Z pfg $");
+__FBSDID("$FreeBSD$");
 
 #ifndef APPLEKEXT
 #include <fs/nfs/nfsport.h>
@@ -37,7 +37,7 @@
 struct nfsv4lock nfsv4rootfs_lock;
 
 extern int newnfs_numnfsd;
-extern struct nfsstats newnfsstats;
+extern struct nfsstatsv1 nfsstatsv1;
 extern int nfsrv_lease;
 extern struct timeval nfsboottime;
 extern u_int32_t newnfs_true, newnfs_false;
@@ -70,6 +70,11 @@
     &nfsrv_v4statelimit, 0,
     "High water limit for NFSv4 opens+locks+delegations");
 
+static int	nfsrv_writedelegifpos = 0;
+SYSCTL_INT(_vfs_nfsd, OID_AUTO, writedelegifpos, CTLFLAG_RW,
+    &nfsrv_writedelegifpos, 0,
+    "Issue a write delegation for read opens if possible");
+
 /*
  * Hash lists for nfs V4.
  */
@@ -80,7 +85,6 @@
 
 static u_int32_t nfsrv_openpluslock = 0, nfsrv_delegatecnt = 0;
 static time_t nfsrvboottime;
-static int nfsrv_writedelegifpos = 1;
 static int nfsrv_returnoldstateid = 0, nfsrv_clients = 0;
 static int nfsrv_clienthighwater = NFSRV_CLIENTHIGHWATER;
 static int nfsrv_nogsscallback = 0;
@@ -273,7 +277,7 @@
 			LIST_INIT(&new_clp->lc_stateid[i]);
 		LIST_INSERT_HEAD(NFSCLIENTHASH(new_clp->lc_clientid), new_clp,
 		    lc_hash);
-		newnfsstats.srvclients++;
+		nfsstatsv1.srvclients++;
 		nfsrv_openpluslock++;
 		nfsrv_clients++;
 		NFSLOCKV4ROOTMUTEX();
@@ -377,7 +381,7 @@
 		}
 		LIST_INSERT_HEAD(NFSCLIENTHASH(new_clp->lc_clientid), new_clp,
 		    lc_hash);
-		newnfsstats.srvclients++;
+		nfsstatsv1.srvclients++;
 		nfsrv_openpluslock++;
 		nfsrv_clients++;
 		NFSLOCKV4ROOTMUTEX();
@@ -441,7 +445,7 @@
 		}
 		LIST_INSERT_HEAD(NFSCLIENTHASH(new_clp->lc_clientid), new_clp,
 		    lc_hash);
-		newnfsstats.srvclients++;
+		nfsstatsv1.srvclients++;
 		nfsrv_openpluslock++;
 		nfsrv_clients++;
 	}
@@ -815,7 +819,7 @@
 
 /*
  * Dump out stats for all clients. Called from nfssvc(2), that is used
- * newnfsstats.
+ * nfsstatsv1.
  */
 APPLESTATIC void
 nfsrv_dumpclients(struct nfsd_dumpclients *dumpp, int maxcnt)
@@ -1219,7 +1223,7 @@
 	free(clp->lc_stateid, M_NFSDCLIENT);
 	free(clp, M_NFSDCLIENT);
 	NFSLOCKSTATE();
-	newnfsstats.srvclients--;
+	nfsstatsv1.srvclients--;
 	nfsrv_openpluslock--;
 	nfsrv_clients--;
 	NFSUNLOCKSTATE();
@@ -1260,7 +1264,7 @@
 	    nfsv4_testlock(&lfp->lf_locallock_lck) == 0)
 		nfsrv_freenfslockfile(lfp);
 	FREE((caddr_t)stp, M_NFSDSTATE);
-	newnfsstats.srvdelegates--;
+	nfsstatsv1.srvdelegates--;
 	nfsrv_openpluslock--;
 	nfsrv_delegatecnt--;
 }
@@ -1286,7 +1290,7 @@
 	if (stp->ls_op)
 		nfsrvd_derefcache(stp->ls_op);
 	FREE((caddr_t)stp, M_NFSDSTATE);
-	newnfsstats.srvopenowners--;
+	nfsstatsv1.srvopenowners--;
 	nfsrv_openpluslock--;
 }
 
@@ -1336,7 +1340,7 @@
 	if (cansleep != 0)
 		NFSUNLOCKSTATE();
 	FREE((caddr_t)stp, M_NFSDSTATE);
-	newnfsstats.srvopens--;
+	nfsstatsv1.srvopens--;
 	nfsrv_openpluslock--;
 	return (ret);
 }
@@ -1355,7 +1359,7 @@
 	if (stp->ls_op)
 		nfsrvd_derefcache(stp->ls_op);
 	FREE((caddr_t)stp, M_NFSDSTATE);
-	newnfsstats.srvlockowners--;
+	nfsstatsv1.srvlockowners--;
 	nfsrv_openpluslock--;
 }
 
@@ -1430,7 +1434,7 @@
 
 	if (lop->lo_lckfile.le_prev != NULL) {
 		LIST_REMOVE(lop, lo_lckfile);
-		newnfsstats.srvlocks--;
+		nfsstatsv1.srvlocks--;
 		nfsrv_openpluslock--;
 	}
 	LIST_REMOVE(lop, lo_lckowner);
@@ -2200,7 +2204,7 @@
 		LIST_INSERT_HEAD(&stp->ls_open, new_stp, ls_list);
 		*new_lopp = NULL;
 		*new_stpp = NULL;
-		newnfsstats.srvlockowners++;
+		nfsstatsv1.srvlockowners++;
 		nfsrv_openpluslock++;
 	}
 	if (filestruct_locked != 0) {
@@ -2497,6 +2501,8 @@
 	struct nfsclient *clp;
 	int error = 0, haslock = 0, ret, delegate = 1, writedeleg = 1;
 	int readonly = 0, cbret = 1, getfhret = 0;
+	int gotstate = 0, len = 0;
+	u_char *clidp = NULL;
 
 	if ((new_stp->ls_flags & NFSLCK_SHAREBITS) == NFSLCK_READACCESS)
 		readonly = 1;
@@ -2515,6 +2521,7 @@
 		goto out;
 	}
 
+	clidp = malloc(NFSV4_OPAQUELIMIT, M_TEMP, M_WAITOK);
 tryagain:
 	MALLOC(new_lfp, struct nfslockfile *, sizeof (struct nfslockfile),
 	    M_NFSDLOCKFILE, M_WAITOK);
@@ -2849,12 +2856,12 @@
 			LIST_INSERT_HEAD(&new_stp->ls_open, new_open, ls_list);
 			LIST_INSERT_HEAD(&clp->lc_open, new_stp, ls_list);
 			*new_stpp = NULL;
-			newnfsstats.srvopenowners++;
+			nfsstatsv1.srvopenowners++;
 			nfsrv_openpluslock++;
 		    }
 		    openstp = new_open;
 		    new_open = NULL;
-		    newnfsstats.srvopens++;
+		    nfsstatsv1.srvopens++;
 		    nfsrv_openpluslock++;
 		    break;
 		}
@@ -2913,7 +2920,7 @@
 		    NFSRV_V4DELEGLIMIT(nfsrv_delegatecnt) ||
 		    !NFSVNO_DELEGOK(vp))
 		    *rflagsp |= NFSV4OPEN_RECALL;
-		newnfsstats.srvdelegates++;
+		nfsstatsv1.srvdelegates++;
 		nfsrv_openpluslock++;
 		nfsrv_delegatecnt++;
 
@@ -2953,12 +2960,12 @@
 		    LIST_INSERT_HEAD(&new_stp->ls_open, new_open, ls_list);
 		    LIST_INSERT_HEAD(&clp->lc_open, new_stp, ls_list);
 		    *new_stpp = NULL;
-		    newnfsstats.srvopenowners++;
+		    nfsstatsv1.srvopenowners++;
 		    nfsrv_openpluslock++;
 		}
 		openstp = new_open;
 		new_open = NULL;
-		newnfsstats.srvopens++;
+		nfsstatsv1.srvopens++;
 		nfsrv_openpluslock++;
 	    } else {
 		error = NFSERR_RECLAIMCONFLICT;
@@ -3027,7 +3034,7 @@
 			    new_deleg->ls_stateid), new_deleg, ls_hash);
 			LIST_INSERT_HEAD(&clp->lc_deleg, new_deleg, ls_list);
 			new_deleg = NULL;
-			newnfsstats.srvdelegates++;
+			nfsstatsv1.srvdelegates++;
 			nfsrv_openpluslock++;
 			nfsrv_delegatecnt++;
 		    }
@@ -3049,7 +3056,7 @@
 			new_open, ls_hash);
 		    openstp = new_open;
 		    new_open = NULL;
-		    newnfsstats.srvopens++;
+		    nfsstatsv1.srvopens++;
 		    nfsrv_openpluslock++;
 
 		    /*
@@ -3094,7 +3101,7 @@
 			    new_deleg->ls_stateid), new_deleg, ls_hash);
 			LIST_INSERT_HEAD(&clp->lc_deleg, new_deleg, ls_list);
 			new_deleg = NULL;
-			newnfsstats.srvdelegates++;
+			nfsstatsv1.srvdelegates++;
 			nfsrv_openpluslock++;
 			nfsrv_delegatecnt++;
 		    }
@@ -3173,10 +3180,20 @@
 				LIST_INSERT_HEAD(&clp->lc_deleg, new_deleg,
 				    ls_list);
 				new_deleg = NULL;
-				newnfsstats.srvdelegates++;
+				nfsstatsv1.srvdelegates++;
 				nfsrv_openpluslock++;
 				nfsrv_delegatecnt++;
 			}
+			/*
+			 * Since NFSv4.1 never does an OpenConfirm, the first
+			 * open state will be acquired here.
+			 */
+			if (!(clp->lc_flags & LCL_STAMPEDSTABLE)) {
+				clp->lc_flags |= LCL_STAMPEDSTABLE;
+				len = clp->lc_idlen;
+				NFSBCOPY(clp->lc_id, clidp, len);
+				gotstate = 1;
+			}
 		} else {
 			*rflagsp |= NFSV4OPEN_RESULTCONFIRM;
 			new_stp->ls_flags = NFSLCK_NEEDSCONFIRM;
@@ -3191,9 +3208,9 @@
 		openstp = new_open;
 		new_open = NULL;
 		*new_stpp = NULL;
-		newnfsstats.srvopens++;
+		nfsstatsv1.srvopens++;
 		nfsrv_openpluslock++;
-		newnfsstats.srvopenowners++;
+		nfsstatsv1.srvopenowners++;
 		nfsrv_openpluslock++;
 	}
 	if (!error) {
@@ -3213,7 +3230,17 @@
 	if (new_deleg)
 		FREE((caddr_t)new_deleg, M_NFSDSTATE);
 
+	/*
+	 * If the NFSv4.1 client just acquired its first open, write a timestamp
+	 * to the stable storage file.
+	 */
+	if (gotstate != 0) {
+		nfsrv_writestable(clidp, len, NFSNST_NEWSTATE, p);
+		nfsrv_backupstable();
+	}
+
 out:
+	free(clidp, M_TEMP);
 	NFSEXITCODE2(error, nd);
 	return (error);
 }
@@ -3230,7 +3257,7 @@
 	struct nfslockfile *lfp;
 	u_int32_t bits;
 	int error = 0, gotstate = 0, len = 0;
-	u_char client[NFSV4_OPAQUELIMIT];
+	u_char *clidp = NULL;
 
 	/*
 	 * Check for restart conditions (client and server).
@@ -3240,6 +3267,7 @@
 	if (error)
 		goto out;
 
+	clidp = malloc(NFSV4_OPAQUELIMIT, M_TEMP, M_WAITOK);
 	NFSLOCKSTATE();
 	/*
 	 * Get the open structure via clientid and stateid.
@@ -3318,7 +3346,7 @@
 		if (!(clp->lc_flags & LCL_STAMPEDSTABLE)) {
 			clp->lc_flags |= LCL_STAMPEDSTABLE;
 			len = clp->lc_idlen;
-			NFSBCOPY(clp->lc_id, client, len);
+			NFSBCOPY(clp->lc_id, clidp, len);
 			gotstate = 1;
 		}
 		NFSUNLOCKSTATE();
@@ -3365,11 +3393,12 @@
 	 * to the stable storage file.
 	 */
 	if (gotstate != 0) {
-		nfsrv_writestable(client, len, NFSNST_NEWSTATE, p);
+		nfsrv_writestable(clidp, len, NFSNST_NEWSTATE, p);
 		nfsrv_backupstable();
 	}
 
 out:
+	free(clidp, M_TEMP);
 	NFSEXITCODE2(error, nd);
 	return (error);
 }
@@ -3645,7 +3674,7 @@
 	else
 		LIST_INSERT_AFTER(insert_lop, new_lop, lo_lckowner);
 	if (stp != NULL) {
-		newnfsstats.srvlocks++;
+		nfsstatsv1.srvlocks++;
 		nfsrv_openpluslock++;
 	}
 }
@@ -3843,7 +3872,7 @@
  * just set lc_program to 0 to indicate no callbacks are possible.
  * (For cases where the address can't be parsed or is 0.0.0.0.0.0, set
  *  the address to the client's transport address. This won't be used
- *  for callbacks, but can be printed out by newnfsstats for info.)
+ *  for callbacks, but can be printed out by nfsstats for info.)
  * Return error if the xdr can't be parsed, 0 otherwise.
  */
 APPLESTATIC int
