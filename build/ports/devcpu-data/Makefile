PORTNAME=	devcpu-data
PORTVERSION=	1.17
PORTREVISION=   0
CATEGORIES=	sysutils
MASTER_SITES=   https://downloadmirror.intel.com/27776/eng/:intel \
		LOCAL/sbruno:amd
PKGNAMEPREFIX=	devcpu-
DISTFILES=	microcode-20180425.tgz:intel \
		amd64-microcode.20180518.tar.xz:amd

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
	${INSTALL_DATA} ${WRKSRC}/microcode_amd.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/microcode_amd_fam15h.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/microcode_amd_fam16h.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/microcode_amd_fam17h.bin ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/mcodes/* ${NAS4FREE_ROOTFS}/${PREFIX}/share/cpucontrol/
.include <bsd.port.mk>
