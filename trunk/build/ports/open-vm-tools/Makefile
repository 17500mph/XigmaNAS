PORTNAME=		nas4free-open-vm-tools
PORTVERSION=	1.0
PORTREVISION=	3
CATEGORIES=		emulators
MAINTAINER=		zoon1@nas4free.org
COMMENT=		Open VMware tools for FreeBSD VMware guests

DISTFILES=
NO_BUILD=		yes
NO_PKG_REGISTER=	yes
ALWAYS_BUILD_DEPENDS=		yes
DEPENDS_TARGET=	clean build

BUILD_DEPENDS=	${NONEXISTENT}:emulators/open-vm-tools-nox11:build

BLDSRC=${PORTSDIR}/emulators/open-vm-tools-nox11/work/open-vm-tools-*
WRKSRC=${WRKDIR}/open-vm-tools-nox11

do-install:
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/libguestlib/.libs/libguestlib.so.0 ${NAS4FREE_ROOTFS}/${PREFIX}/lib
	@${LN} -f -v -s libguestlib.so.0 ${NAS4FREE_ROOTFS}/${PREFIX}/lib/libguestlib.so
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/libvmtools/.libs/libvmtools.so.0 ${NAS4FREE_ROOTFS}/${PREFIX}/lib
	@${LN} -f -v -s libvmtools.so.0 ${NAS4FREE_ROOTFS}/${PREFIX}/lib/libvmtools.so

	@${MKDIR} ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/common
	@${MKDIR} ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/services/plugins/guestInfo/.libs/libguestInfo.so ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/services/plugins/hgfsServer/.libs/libhgfsServer.so ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/common
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/services/plugins/powerOps/.libs/libpowerOps.so ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/services/plugins/timeSync/.libs/libtimeSync.so ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/services/plugins/vix/.libs/libvix.so ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/common
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/services/plugins/vmbackup/.libs/libvmbackup.so ${NAS4FREE_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc

	@${MKDIR} ${NAS4FREE_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers
	@${INSTALL_DATA} -v ${BLDSRC}/open-vm-tools/modules/freebsd/vmblock.ko ${NAS4FREE_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers/vmblock.ko
	@${INSTALL_DATA} -v ${BLDSRC}/open-vm-tools/modules/freebsd/vmmemctl.ko ${NAS4FREE_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers/vmmemctl.ko
	@${INSTALL_DATA} -v ${BLDSRC}/open-vm-tools/modules/freebsd/vmxnet.ko ${NAS4FREE_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers/vmxnet.ko

	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/services/vmtoolsd/.libs/vmtoolsd ${NAS4FREE_ROOTFS}/${PREFIX}/bin
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/checkvm/vmware-checkvm ${NAS4FREE_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/hgfsclient/.libs/vmware-hgfsclient ${NAS4FREE_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/rpctool/vmware-rpctool ${NAS4FREE_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/toolbox/.libs/vmware-toolbox-cmd ${NAS4FREE_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/open-vm-tools/vmblock-fuse/vmware-vmblock-fuse ${NAS4FREE_ROOTFS}/${PREFIX}/bin
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/xferlogs/vmware-xferlogs ${NAS4FREE_ROOTFS}/${PREFIX}/bin

	@${MKDIR} ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/scripts/poweroff-vm-default ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/scripts/poweron-vm-default ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/scripts/resume-vm-default ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/scripts/suspend-vm-default ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/scripts/common/vm-support ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/scripts/common/statechange.subr ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools
	@${MKDIR} ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools/scripts/vmware
	@${INSTALL_SCRIPT} -v ${BLDSRC}/open-vm-tools/scripts/freebsd/network ${NAS4FREE_ROOTFS}/${PREFIX}/share/vmware-tools/scripts/vmware

.include <bsd.port.mk>
.include <../nas4free.mk>