PORTNAME=       noVNC
PORTVERSION=    20171120
PORTREVISION=   2
CATEGORIES=     net

MAINTAINER=     zoon1@nas4free.org
COMMENT=        HTML5 VNC Client

LICENSE=	MPL20

USE_GITHUB=	yes
GH_ACCOUNT=	novnc
GH_TAGNAME=	ddcb60c

NO_BUILD=		yes
NO_PKG_REGISTER=	yes

WWWDIR=${PREFIX}/www/novnc

do-install:
# NAS4Free install
	@echo "install for ${NAS4FREE_ROOTFS}"
	@${MKDIR} -p ${NAS4FREE_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${WRKSRC}/app ${NAS4FREE_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${WRKSRC}/core ${NAS4FREE_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${WRKSRC}/vendor ${NAS4FREE_ROOTFS}${WWWDIR}
	@${CP} -pv ${WRKSRC}/vnc.html ${NAS4FREE_ROOTFS}${WWWDIR}
	@${CP} -pv ${WRKSRC}/vnc_lite.html ${NAS4FREE_ROOTFS}${WWWDIR}

.include <bsd.port.mk>
