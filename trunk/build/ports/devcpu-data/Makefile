PORTNAME=	devcpu-data
PORTVERSION=	1.16
PORTREVISION=   1
CATEGORIES=	sysutils
MASTER_SITES=   https://downloadmirror.intel.com/27591/eng/:intel \
		http://www.amd64.org/microcode/:amd
PKGNAMEPREFIX=	devcpu-
DISTFILES=	microcode-20180312.tgz:intel \
		amd64-microcode_3.20171205.1.tar.xz:amd

MAINTAINER=	zoon1@nas4free.org
COMMENT=	Intel and AMD CPUs microcode updates

LICENSE=	EULA
LICENSE_NAME=	Intel Software License Agreement
LICENSE_FILE=	${FILESDIR}/LICENSE
LICENSE_PERMS=	dist-mirror no-dist-sell pkg-mirror no-pkg-sell auto-accept

ONLY_FOR_ARCHS=	amd64 i386
NO_ARCH=	yes
NO_WRKSUBDIR=	yes

DATADIR=	${PREFIX}/share/cpucontrol

USE_RC_SUBR=	microcode_update

post-extract:
	${CP} -p ${FILESDIR}/Makefile ${FILESDIR}/ucode-split.c ${WRKSRC}

do-install:
# NAS4Free install
	@echo "install for ${NAS4FREE_ROOTFS}"
	@${MKDIR} -v ${NAS4FREE_ROOTFS}/usr/local/share/cpucontrol
	@${INSTALL_SCRIPT} -v ${FILESDIR}/microcode_update.in ${NAS4FREE_ROOTFS}/etc/rc.d/microcode_update
# mcodes
	${INSTALL_DATA} ${WRKSRC}/amd64-microcode-3.20171205.1/microcode_amd.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/amd64-microcode-3.20171205.1/microcode_amd_fam15h.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/amd64-microcode-3.20171205.1/microcode_amd_fam16h.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/amd64-microcode-3.20171205.1/microcode_amd_fam17h.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/mcodes/* ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
.include <bsd.port.mk>
