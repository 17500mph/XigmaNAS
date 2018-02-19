PORTNAME=	phpvirtualbox
DISTVERSION=	5.2-0
PORTREVISION=	0
CATEGORIES=	www

MAINTAINER=	zoon1@nas4free.org
COMMENT=	Web Interface for VirtualBox

LICENSE=	GPLv3
NO_ARCH=	yes
NO_BUILD=	yes
USES=		dos2unix php
# NAS4Free
#USE_PHP=	json session soap simplexml xml

USE_GITHUB=	yes

DOS2UNIX_REGEX=	.*\.(php|txt|js|css|html)
ETCDIR=		${WWWDIR}

post-patch:
	@${REINPLACE_CMD} -e 's#/usr/share/phpvirtualbox#${WWWDIR}#g' ${WRKSRC}/phpvirtualbox.conf

do-install:
	${MKDIR} ${STAGEDIR}${WWWDIR}
	(cd ${WRKSRC}/ && ${COPYTREE_SHARE} "*" ${STAGEDIR}${WWWDIR} \
	    "-not -name *.orig -not -name config.php-example")
	${INSTALL_DATA} ${WRKSRC}/config.php-example \
	    ${STAGEDIR}${WWWDIR}/config.php.sample

# NAS4Free install
	@echo "install for ${NAS4FREE_ROOTFS}"
	@${RM} -f ${STAGEDIR}${WWWDIR}/*.txt
	#@${MKDIR} -p ${NAS4FREE_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${STAGEDIR}${WWWDIR} ${NAS4FREE_ROOTFS}${PREFIX}/www

.include <bsd.port.mk>
