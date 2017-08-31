install:
	# mps patches.
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps.c.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps_config.c.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps_pci.c.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps_sas.c.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps_sas_lsi.c.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps_table.c.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps_table.h.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mps_user.c.diff
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mpsvar.h.diff
