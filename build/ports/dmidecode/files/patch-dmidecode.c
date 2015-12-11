--- dmidecode.c.orig	2015-09-03 08:03:19.000000000 +0200
+++ dmidecode.c	2015-12-11 12:16:27.000000000 +0200
@@ -4521,16 +4521,30 @@
 		printf("\n");
 	}
 
-	/*
-	 * When we are reading the DMI table from sysfs, we want to print
-	 * the address of the table (done above), but the offset of the
-	 * data in the file is 0.  When reading from /dev/mem, the offset
-	 * in the file is the address.
-	 */
 	if (flags & FLAG_NO_FILE_OFFSET)
-		base = 0;
 
-	if ((buf = mem_chunk(base, len, devmem)) == NULL)
+	{
+		/*
+		 * When reading from sysfs, the file may be shorter than
+		 * announced. For SMBIOS v3 this is expcted, as we only know
+		 * the maximum table size, not the actual table size. For older
+		 * implementations (and for SMBIOS v3 too), this would be the
+		 * result of the kernel truncating the table on parse error.
+		 */
+		size_t size = len;
+		buf = read_file(&size, devmem);
+		if (!(opt.flags & FLAG_QUIET) && num && size != (size_t)len)
+		{
+			printf("Wrong DMI structures length: %u bytes "
+				"announced, only %lu bytes available.\n",
+				len, (unsigned long)size);
+		}
+		len = size;
+	}
+	else
+		buf = mem_chunk(base, len, devmem);
+
+	if (buf == NULL)
 	{
 		fprintf(stderr, "Table is unreachable, sorry."
 #ifndef USE_MMAP
@@ -4748,6 +4762,7 @@
 	int ret = 0;                /* Returned value */
 	int found = 0;
 	off_t fp;
+	size_t size;
 	int efi;
 	u8 *buf;
 
@@ -4817,8 +4832,9 @@
 	 * contain one of several types of entry points, so read enough for
 	 * the largest one, then determine what type it contains.
 	 */
+	size = 0x20;
 	if (!(opt.flags & FLAG_NO_SYSFS)
-	 && (buf = read_file(0x20, SYS_ENTRY_FILE)) != NULL)
+	 && (buf = read_file(&size, SYS_ENTRY_FILE)) != NULL)
 	{
 		if (!(opt.flags & FLAG_QUIET))
 			printf("Getting SMBIOS data from sysfs.\n");
@@ -4864,8 +4880,17 @@
 		goto exit_free;
 	}
 
-	if (smbios_decode(buf, opt.devmem, 0))
-		found++;
+	if (memcmp(buf, "_SM3_", 5) == 0)
+	{
+		if (smbios3_decode(buf, opt.devmem, 0))
+			found++;
+	}
+	else if (memcmp(buf, "_SM_", 4) == 0)
+	{
+		if (smbios_decode(buf, opt.devmem, 0))
+			found++;
+	}
+
 	goto done;
 
 memory_scan:
