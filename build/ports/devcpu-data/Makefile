PORTNAME=	devcpu-data
PORTVERSION=	1.11
CATEGORIES=	sysutils
MASTER_SITES=   https://downloadmirror.intel.com/26925/eng/:intel \
		http://www.amd64.org/microcode/:amd
PKGNAMEPREFIX=	devcpu-
DISTFILES=      microcode-20170707.tgz:intel amd-ucode-2014-12-01.tar.bz2:amd 

MAINTAINER=	zoon1@nas4free.org
COMMENT=	Intel and AMD CPUs microcode updates

LICENSE=	EULA
LICENSE_NAME=	Intel Software License Agreement
LICENSE_FILE=	${FILESDIR}/LICENSE
LICENSE_PERMS=	dist-mirror no-dist-sell pkg-mirror no-pkg-sell auto-accept

NO_ARCH=	yes
NO_WRKSUBDIR=	yes

ONLY_FOR_ARCHS=	i386 amd64

DATADIR=	${PREFIX}/share/cpucontrol

USE_RC_SUBR=	microcode_update

post-extract:
	${CP} -p ${FILESDIR}/Makefile ${FILESDIR}/ucode-tool.c ${WRKSRC}

do-install:
# NAS4Free install
	@echo "install for ${NAS4FREE_ROOTFS}"
# mcodes
	@${MKDIR} -v ${NAS4FREE_ROOTFS}/usr/local/share/cpucontrol
	@${INSTALL_DATA} -v ${WRKSRC}/mcodes/* ${NAS4FREE_ROOTFS}/usr/local/share/cpucontrol/
	@${INSTALL_SCRIPT} -v ${FILESDIR}/microcode_update.in ${NAS4FREE_ROOTFS}/etc/rc.d/microcode_update
.include <bsd.port.mk>