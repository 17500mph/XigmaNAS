--- dmidecode.c.orig	2017-05-23 15:34:14.000000000 +0200
+++ dmidecode.c	2017-12-05 18:40:52.000000000 +0100
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
@@ -4934,13 +4939,19 @@
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
@@ -4960,9 +4971,7 @@
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
@@ -4972,6 +4981,31 @@
 
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
 
