PORTNAME=	beadm
PORTVERSION=	1.2.9
PORTREVISION=	0
CATEGORIES=	sysutils
MASTER_SITES=	\
		LOCAL/bdrewery/${PORTNAME} \
		http://mirror.shatow.net/freebsd/${PORTNAME}/ \
		GH

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Solaris-like utility to manage Boot Environments on ZFS

USE_GITHUB=	yes
GH_ACCOUNT=	vermaden
GH_TAGNAME=	2665f52786a12aadbe8984731b8a64d1249692f4

NO_BUILD=	YES

CONFLICTS_INSTALL=	beadm-devel-*

do-install:
# XigmaNAS install
	${INSTALL_SCRIPT} -v ${WRKSRC}/beadm ${XIGMANAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>