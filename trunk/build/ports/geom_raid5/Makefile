# http://lev.serebryakov.spb.ru/download/graid5/
PORTNAME=		geom_raid5
PORTVERSION=	1.3
PORTREVISION?=  1
CATEGORIES=		sysutils
COMMENT=		GEOM RAID5 filesystem
MAINTAINER=		zoon1@nas4free.org

DISTFILES?=
NO_BUILD?=	yes
NO_PKG_REGISTER?=	yes
#ALWAYS_BUILD_DEPENDS=	yes
DEPENDS_TARGET=	clean build

BUILD_DEPENDS=	${NONEXISTENT}:${PORTSDIR}/sysutils/graid5

.include <../nas4free.mk>
.if ${XIGMANAS_XARCH} == "rpi2" || ${XIGMANAS_XARCH} == "oc1"
export CONF_CFLAGS=-march=armv7a -mtune=cortex-a7 -mcpu=cortex-a7
.elif ${XIGMANAS_XARCH} == "rpi"
export CONF_CFLAGS=-march=armv6z -mtune=arm1176jzf-s -mcpu=arm1176jzf-s -DCPU_ARM1176
.endif

do-install:
	# Install kernel module.
	@${INSTALL_KLD} -v ${PORTSDIR}/sysutils/graid5/work/graid5-1.3.20140711.62/sys/geom_raid5.ko ${XIGMANAS_ROOTFS}/boot/kernel

	# Install application.
	@${LN} -f -v -s /sbin/geom ${XIGMANAS_ROOTFS}/sbin/graid5
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/graid5/work/graid5-1.3.20140711.62/lib/geom_raid5.so ${XIGMANAS_ROOTFS}/lib/geom


.include <bsd.port.mk>
