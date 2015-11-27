PORTNAME=	syncthing
PORTVERSION=	0.12.4
DISTVERSIONPREFIX=	v
CATEGORIES=	net

MAINTAINER=	aoyama@nas4free.org
COMMENT=	Encrypted file sync tool

BUILD_DEPENDS=	${LOCALBASE}/bin/go:${PORTSDIR}/lang/go

USE_GITHUB=	yes
USE_RC_SUBR=	syncthing
# NAS4Free
#PLIST_FILES=	bin/syncthing
PORTDOCS=	*

USERS=		syncthing
GROUPS=		syncthing

STRIP=		# stripping can break go binaries

# NAS4Free
NO_PKG_REGISTER=	yes

post-patch:
	@cd ${WRKSRC} ; ${MKDIR} src/github.com/${PORTNAME}/${PORTNAME} ; \
		${MV} .gitattributes .gitignore .mailmap AUTHORS CONDUCT.md \
		CONTRIBUTING.md Godeps LICENSE NICKS README.md assets build.go \
		build.sh cmd etc gui lib man protocol script test \
		src/github.com/${PORTNAME}/${PORTNAME}

do-build:
	# timestamp here refers to source, not build time
	@cd ${WRKSRC}/src/github.com/${PORTNAME}/${PORTNAME} ; \
		${SETENV} GOPATH=${WRKSRC} go run build.go -no-upgrade=true -version v${PORTVERSION}

do-install:
# NAS4Free install
	@echo "install for ${NAS4FREE_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/github.com/${PORTNAME}/${PORTNAME}/bin/syncthing \
		${NAS4FREE_ROOTFS}${PREFIX}/bin/
	@${INSTALL_SCRIPT} -v ${WRKDIR}/syncthing ${NAS4FREE_ROOTFS}/etc/rc.d

.include <bsd.port.mk>
