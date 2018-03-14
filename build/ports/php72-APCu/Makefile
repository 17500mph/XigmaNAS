PORTNAME=	php72-APCu
PORTREVISION=	1
CATEGORIES=	devel

MAINTAINER=	zoon1@nas4free.org
COMMENT=	APC User Caching

DISTFILES?=
NO_BUILD?=	         yes
NO_PKG_REGISTER?=	 yes
DEPENDS_TARGET=	clean build stage

DEP_STAGEDIR=	${PORTSDIR}/devel/php72-APCu/work-php72/stage
BUILD_DEPENDS=	${NONEXISTENT}:devel/php72-APCu

do-install:
# NAS4Free install
	@${CP} -pv ${FILESDIR}/apcu.ini ${NAS4FREE_ROOTFS}/${PREFIX}/etc/php
	@${MKDIR} ${NAS4FREE_ROOTFS}${PREFIX}/lib/php/extensions/no-debug-non-zts-20170718
	${INSTALL_DATA} ${DEP_STAGEDIR}/usr/local/lib/php/20170718/apcu.so ${NAS4FREE_ROOTFS}/${PREFIX}/lib/php/extensions/no-debug-non-zts-20170718

.include <bsd.port.mk>
