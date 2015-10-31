--- util.c.orig	2015-09-03 08:03:19.000000000 +0200
+++ util.c	2015-10-14 14:37:09.000000000 +0200
@@ -152,6 +152,7 @@
 	void *p;
 	int fd;
 #ifdef USE_MMAP
+	struct stat statbuf;
 	off_t mmoffset;
 	void *mmp;
 #endif
@@ -165,10 +166,28 @@
 	if ((p = malloc(len)) == NULL)
 	{
 		perror("malloc");
-		return NULL;
+		goto out;
 	}
 
 #ifdef USE_MMAP
+	if (fstat(fd, &statbuf) == -1)
+	{
+		fprintf(stderr, "%s: ", devmem);
+		perror("stat");
+		goto err_free;
+	}
+
+	/*
+	 * mmap() will fail with SIGBUS if trying to map beyond the end of
+	 * the file.
+	 */
+	if (S_ISREG(statbuf.st_mode) && base + (off_t)len > statbuf.st_size)
+	{
+		fprintf(stderr, "mmap: Can't map beyond end of file %s\n",
+			devmem);
+		goto err_free;
+	}
+
 #ifdef _SC_PAGESIZE
 	mmoffset = base % sysconf(_SC_PAGESIZE);
 #else
@@ -199,19 +218,17 @@
 	{
 		fprintf(stderr, "%s: ", devmem);
 		perror("lseek");
-		free(p);
-		return NULL;
+		goto err_free;
 	}
 
-	if (myread(fd, p, len, devmem) == -1)
-	{
-		free(p);
-		return NULL;
-	}
+	if (myread(fd, p, len, devmem) == 0)
+		goto out;
+
+err_free:
+	free(p);
+	p = NULL;
 
-#ifdef USE_MMAP
 out:
-#endif
 	if (close(fd) == -1)
 		perror(devmem);
 
