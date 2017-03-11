install:
	# ieee8023ad_lacp.c (Rev. 313038)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/net < ${.CURDIR}/files/patch-sys__net__ieee8023ad_lacp.c

	# if_bridge.c (Rev. 313050)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/net < ${.CURDIR}/files/patch-sys__net__if_bridge.c

	# if_bridgevar.h (Rev. 313050)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/net < ${.CURDIR}/files/patch-sys__net__if_bridgevar.h

	# if_lagg.c (Rev. 313108)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/net < ${.CURDIR}/files/patch-sys__net__if_lagg.c

	# rtsock.c (Rev. 309177)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/net < ${.CURDIR}/files/patch-sys__net__rtsock.c
