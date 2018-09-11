PORTNAME=		syncthing
PORTVERSION=	0.14.50
PORTREVISION=	0
DISTVERSIONPREFIX= v
CATEGORIES=	net

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Encrypted file sync tool

BUILD_DEPENDS=	${LOCALBASE}/bin/go:lang/go

USE_GITHUB=	yes
GH_SUBDIR=      src/github.com/${PORTNAME}/${PORTNAME}
USE_RC_SUBR=	syncthing
# XigmaNAS
#PLIST_FILES=	bin/syncthing
PORTDOCS=	*

USERS=		syncthing
GROUPS=		syncthing

STRIP=		# stripping can break go binaries

# XigmaNAS
NO_PKG_REGISTER= yes

.include <bsd.port.pre.mk>

do-build:
	@cd ${WRKSRC}/src/github.com/${PORTNAME}/${PORTNAME} ; \
		${SETENV} go run build.go -no-upgrade=true -version v${PORTVERSION}

do-install:
# XigmaNAS install
	@echo "install for ${XIGMANAS_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/github.com/${PORTNAME}/${PORTNAME}/bin/syncthing \
		${XIGMANAS_ROOTFS}${PREFIX}/bin/
	@${INSTALL_SCRIPT} -v ${WRKDIR}/syncthing ${XIGMANAS_ROOTFS}/etc/rc.d

.include <bsd.port.post.mk>
