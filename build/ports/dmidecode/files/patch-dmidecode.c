--- dmidecode.c.orig	2017-05-23 15:34:14.000000000 +0200
+++ dmidecode.c	2018-08-10 14:28:00.000000000 +0200
@@ -64,6 +64,11 @@
 #include <stdlib.h>
 #include <unistd.h>
 
+#ifdef __FreeBSD__
+#include <errno.h>
+#include <kenv.h>
+#endif
+
 #include "version.h"
 #include "config.h"
 #include "types.h"
@@ -4506,7 +4511,7 @@
 				case 0x02:
 					printf("\tFirmware Revision: %u.%u\n",
 						DWORD(data + 0x0A) >> 16,
-						DWORD(data + 0x0A) && 0xFF);
+						DWORD(data + 0x0A) & 0xFFFF);
 					/*
 					 * We skip the next 4 bytes, as their
 					 * format is not standardized and their
@@ -4638,6 +4643,7 @@
 			}
 			break;
 		}
+		i++;
 
 		/* In quiet mode, stop decoding at end of table marker */
 		if ((opt.flags & FLAG_QUIET) && h.type == 127)
@@ -4648,6 +4654,22 @@
 			printf("Handle 0x%04X, DMI type %d, %d bytes\n",
 				h.handle, h.type, h.length);
 
+		/* Look for the next handle */
+		next = data + h.length;
+		while ((unsigned long)(next - buf + 1) < len
+			&& (next[0] != 0 || next[1] != 0))
+			next++;
+		next += 2;
+
+		/* Make sure the whole structure fits in the table */
+		if ((unsigned long)(next - buf) > len)
+		{
+			if (display && !(opt.flags & FLAG_QUIET))
+				printf("\t<TRUNCATED>\n\n");
+			data = next;
+			break;
+		}
+
 		/* assign vendor for vendor-specific decodes later */
 		if (h.type == 1 && h.length >= 5)
 			dmi_set_vendor(dmi_string(&h, data[0x04]));
@@ -4655,34 +4677,21 @@
 		/* Fixup a common mistake */
 		if (h.type == 34)
 			dmi_fixup_type_34(&h, display);
-
-		/* look for the next handle */
-		next = data + h.length;
-		while ((unsigned long)(next - buf + 1) < len
-		    && (next[0] != 0 || next[1] != 0))
-			next++;
-		next += 2;
 		if (display)
 		{
-			if ((unsigned long)(next - buf) <= len)
+			if (opt.flags & FLAG_DUMP)
 			{
-				if (opt.flags & FLAG_DUMP)
-				{
-					dmi_dump(&h, "\t");
-					printf("\n");
-				}
-				else
-					dmi_decode(&h, ver);
+				dmi_dump(&h, "\t");
+				printf("\n");
 			}
-			else if (!(opt.flags & FLAG_QUIET))
-				printf("\t<TRUNCATED>\n\n");
+			else
+				dmi_decode(&h, ver);
 		}
 		else if (opt.string != NULL
 		      && opt.string->type == h.type)
 			dmi_table_string(&h, data, ver);
 
 		data = next;
-		i++;
 
 		/* SMBIOS v3 requires stopping at this marker */
 		if (h.type == 127 && (flags & FLAG_STOP_AT_EOT))
@@ -4812,6 +4821,15 @@
 	u32 ver;
 	u64 offset;
 
+	/* Don't let checksum run beyond the buffer */
+	if (buf[0x06] > 0x20)
+	{
+		fprintf(stderr,
+			"Entry point length too large (%u bytes, expected %u).\n",
+			(unsigned int)buf[0x06], 0x18U);
+		return 0;
+	}
+
 	if (!checksum(buf, buf[0x06]))
 		return 0;
 
@@ -4850,6 +4868,15 @@
 {
 	u16 ver;
 
+	/* Don't let checksum run beyond the buffer */
+	if (buf[0x05] > 0x20)
+	{
+		fprintf(stderr,
+			"Entry point length too large (%u bytes, expected %u).\n",
+			(unsigned int)buf[0x05], 0x1FU);
+		return 0;
+	}
+
 	if (!checksum(buf, buf[0x05])
 	 || memcmp(buf + 0x10, "_DMI_", 5) != 0
 	 || !checksum(buf + 0x10, 0x0F))
@@ -4934,13 +4961,19 @@
 #define EFI_NO_SMBIOS   (-2)
 static int address_from_efi(off_t *address)
 {
+#if defined(__linux__)
 	FILE *efi_systab;
 	const char *filename;
 	char linebuf[64];
+#elif defined(__FreeBSD__)
+	char addrstr[KENV_MVALLEN + 1];
+#endif
+	const char *eptype;
 	int ret;
 
 	*address = 0; /* Prevent compiler warning */
 
+#if defined(__linux__)
 	/*
 	 * Linux up to 2.6.6: /proc/efi/systab
 	 * Linux 2.6.7 and up: /sys/firmware/efi/systab
@@ -4960,9 +4993,7 @@
 		 || strcmp(linebuf, "SMBIOS") == 0)
 		{
 			*address = strtoull(addrp, NULL, 0);
-			if (!(opt.flags & FLAG_QUIET))
-				printf("# %s entry point at 0x%08llx\n",
-				       linebuf, (unsigned long long)*address);
+			eptype = linebuf;
 			ret = 0;
 			break;
 		}
@@ -4972,6 +5003,31 @@
 
 	if (ret == EFI_NO_SMBIOS)
 		fprintf(stderr, "%s: SMBIOS entry point missing\n", filename);
+#elif defined(__FreeBSD__)
+	/*
+	 * On FreeBSD, SMBIOS anchor base address in UEFI mode is exposed
+	 * via kernel environment:
+	 * https://svnweb.freebsd.org/base?view=revision&revision=307326
+	 */
+	ret = kenv(KENV_GET, "hint.smbios.0.mem", addrstr, sizeof(addrstr));
+	if (ret == -1)
+	{
+		if (errno != ENOENT)
+			perror("kenv");
+		return EFI_NOT_FOUND;
+	}
+
+	*address = strtoull(addrstr, NULL, 0);
+	eptype = "SMBIOS";
+	ret = 0;
+#else
+	ret = EFI_NOT_FOUND;
+#endif
+
+	if (ret == 0 && !(opt.flags & FLAG_QUIET))
+		printf("# %s entry point at 0x%08llx\n",
+		       eptype, (unsigned long long)*address);
+
 	return ret;
 }
 
