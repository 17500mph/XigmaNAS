PORTNAME=	devcpu-data
PORTVERSION=	1.19
PORTREVISION=   0
CATEGORIES=	sysutils
MASTER_SITES=   https://downloadmirror.intel.com/28039/eng/:intel \
		LOCAL/sbruno:amd
PKGNAMEPREFIX=	devcpu-
DISTFILES=	microcode-20180807.tgz:intel \
		amd64-microcode.20180518.tar.xz:amd

MAINTAINER=	zoon01@xigmanas.com
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
# XigmaNAS install
	@echo "install for ${XIGMANAS_ROOTFS}"
	@${MKDIR} -v ${XIGMANAS_ROOTFS}/usr/local/share/cpucontrol
	@${INSTALL_SCRIPT} -v ${FILESDIR}/microcode_update.in ${XIGMANAS_ROOTFS}/etc/rc.d/microcode_update
# mcodes
	${INSTALL_DATA} ${WRKSRC}/microcode_amd.bin ${XIGMANAS_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/microcode_amd_fam15h.bin ${XIGMANAS_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/microcode_amd_fam16h.bin ${XIGMANAS_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/microcode_amd_fam17h.bin ${XIGMANAS_ROOTFS}/${PREFIX}/share/cpucontrol/
	${INSTALL_DATA} ${WRKSRC}/mcodes/* ${XIGMANAS_ROOTFS}/${PREFIX}/share/cpucontrol/
.include <bsd.port.mk>
