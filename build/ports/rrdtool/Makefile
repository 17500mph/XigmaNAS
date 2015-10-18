PORTNAME=		rrdtool
PORTVERSION=		1.4.8
PORTREVISION=		9
CATEGORIES=		databases graphics

COMMENT=		Round Robin Database Tools
MAINTAINER=		aoyama@nas4free.org

USES=			perl5 python

DISTFILES=
NO_BUILD=		yes
NO_PKG_REGISTER=	yes
DEPENDS_TARGET=	clean build stage

BUILD_DEPENDS=		${NONEXISTENT}:${PORTSDIR}/databases/rrdtool
DEP_STAGEDIR=		${PORTSDIR}/databases/rrdtool/work/stage
PANGO_MODULES=		${PREFIX}/lib/pango/1.8.0/modules

do-install:
# NAS4Free install
	@echo "install for ${NAS4FREE_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/bin/rrdcached ${NAS4FREE_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/bin/rrdcgi ${NAS4FREE_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/bin/rrdcreate ${NAS4FREE_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/bin/rrdinfo ${NAS4FREE_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/bin/rrdtool ${NAS4FREE_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/bin/rrdupdate ${NAS4FREE_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/lib/librrd.so.4.2.1 ${NAS4FREE_ROOTFS}/usr/local/lib
	@${LN} -fsv /usr/local/lib/librrd.so.4.2.1 ${NAS4FREE_ROOTFS}/usr/local/lib/librrd.so
	@${LN} -fsv /usr/local/lib/librrd.so.4.2.1 ${NAS4FREE_ROOTFS}/usr/local/lib/librrd.so.4
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/lib/librrd_th.so.4.2.1 ${NAS4FREE_ROOTFS}/usr/local/lib
	@${LN} -fsv /usr/local/lib/librrd_th.so.4.2.1 ${NAS4FREE_ROOTFS}/usr/local/lib/librrd_th.so
	@${LN} -fsv /usr/local/lib/librrd_th.so.4.2.1 ${NAS4FREE_ROOTFS}/usr/local/lib/librrd_th.so.4
# bindings
	@${MKDIR} -p ${NAS4FREE_ROOTFS}${SITE_PERL}/${PERL_ARCH}/${PERL_VER}/auto/RRDs
	@${CP} -pv ${DEP_STAGEDIR}${SITE_PERL}/${PERL_ARCH}/${PERL_VER}/auto/RRDs/RRDs.so ${NAS4FREE_ROOTFS}${SITE_PERL}/${PERL_ARCH}/${PERL_VER}/auto/RRDs/
	@${CP} -pv ${DEP_STAGEDIR}${SITE_PERL}/${PERL_ARCH}/${PERL_VER}/RRDs.pm ${NAS4FREE_ROOTFS}${SITE_PERL}/${PERL_ARCH}/${PERL_VER}/
	@${CP} -pv ${DEP_STAGEDIR}${SITE_PERL}/RRDp.pm ${NAS4FREE_ROOTFS}${SITE_PERL}/
	@${MKDIR} -p ${NAS4FREE_ROOTFS}${PYTHON_SITELIBDIR}
	@${CP} -pv ${DEP_STAGEDIR}${PYTHON_SITELIBDIR}/rrdtoolmodule.so ${NAS4FREE_ROOTFS}${PYTHON_SITELIBDIR}/
# pango
	@${MKDIR} -p ${NAS4FREE_ROOTFS}${PANGO_MODULES}
	@${MKDIR} -p ${NAS4FREE_ROOTFS}${PREFIX}/etc/pango
	@${CP} -pv ${PANGO_MODULES}/pango-arabic-lang.so ${NAS4FREE_ROOTFS}${PANGO_MODULES}
	@${CP} -pv ${PANGO_MODULES}/pango-basic-fc.so ${NAS4FREE_ROOTFS}${PANGO_MODULES}
	@${CP} -pv ${PANGO_MODULES}/pango-indic-lang.so ${NAS4FREE_ROOTFS}${PANGO_MODULES}
	@${CP} -pv ${PANGO_MODULES}/../modules.cache ${NAS4FREE_ROOTFS}${PANGO_MODULES}/../
	@${CP} -pv ${PANGO_MODULES}/../modules.cache ${NAS4FREE_ROOTFS}${PREFIX}/etc/pango/pango.modules
# fonts
	@${MKDIR} -p ${NAS4FREE_ROOTFS}${PREFIX}/share/fonts
	@${RM} -rf ${NAS4FREE_ROOTFS}${PREFIX}/share/fonts/dejavu
	@${CP} -Rpv ${PREFIX}/share/fonts/dejavu ${NAS4FREE_ROOTFS}${PREFIX}/share/fonts

.include <bsd.port.mk>
