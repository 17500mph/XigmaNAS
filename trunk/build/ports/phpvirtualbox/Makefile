PORTNAME=	phpvirtualbox
DISTVERSION=	5.1-1
PORTREVISION=	1
CATEGORIES=	www

MAINTAINER=	zoon1@nas4free.org
COMMENT=	Web Interface for VirtualBox

LICENSE=	GPLv3

NO_ARCH=	yes
NO_BUILD=	yes
# NAS4Free
#USE_PHP=	json session soap simplexml xml
USES=		dos2unix php

USE_GITHUB=	yes
GH_TAGNAME=	65ebced

DOS2UNIX_REGEX=	.*\.(php|txt|js|css|html)
ETCDIR=		${WWWDIR}

post-patch:
	@${MV} ${WRKSRC}/endpoints/lib/vboxweb-5.0.wsdl ${WRKSRC}/endpoints/lib/vboxweb-5.2.wsdl
	@${MV} ${WRKSRC}/endpoints/lib/vboxwebService-5.0.wsdl ${WRKSRC}/endpoints/lib/vboxwebService-5.2.wsdl

do-install:
	${MKDIR} ${STAGEDIR}${WWWDIR}
	(cd ${WRKSRC}/ && ${COPYTREE_SHARE} "*" ${STAGEDIR}${WWWDIR} \
	    "-not -name *.orig -not -name config.php-example")
	${INSTALL_DATA} ${WRKSRC}/config.php-example \
	    ${STAGEDIR}${WWWDIR}/config.php.sample
# Update translation & install as patching can fail
	${INSTALL_DATA} -v ${FILESDIR}/fr.xml \
	    ${STAGEDIR}${WWWDIR}/languages/fr.xml

# NAS4Free install
	@echo "install for ${NAS4FREE_ROOTFS}"
	@${RM} -f ${STAGEDIR}${WWWDIR}/*.txt
	#@${MKDIR} -p ${NAS4FREE_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${STAGEDIR}${WWWDIR} ${NAS4FREE_ROOTFS}${PREFIX}/www

.include <bsd.port.mk>
