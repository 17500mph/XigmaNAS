PORTNAME=		libvncserver
PORTVERSION=	0.9.11
PORTREVISION=	1
DISTVERSIONPREFIX=	LibVNCServer-
CATEGORIES=	net devel

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Provide an easy API to a custom vnc server

LICENSE=	GPLv2

LIB_DEPENDS=	libpng.so:graphics/png

USE_GITHUB=	yes
GH_ACCOUNT=	LibVNC


USES=		autoreconf jpeg libtool pathfix pkgconfig
USE_LDCONFIG=	yes
GNU_CONFIGURE=	yes
NO_PKG_REGISTER=yes
CPPFLAGS+=	-I${LOCALBASE}/include
LDFLAGS+=	-L${LOCALBASE}/lib
INSTALL_TARGET=	install-strip

OPTIONS_DEFINE=		GCRYPT IPV6 VAAPI
OPTIONS_SINGLE=		SSL
OPTIONS_SINGLE_SSL=	GNUTLS OPENSSL
OPTIONS_DEFAULT=	OPENSSL
GCRYPT_DESC=		libgcrypt support

GNUTLS_IMPLIES=		GCRYPT
GNUTLS_LIB_DEPENDS=	libgnutls.so:security/gnutls
GNUTLS_CONFIGURE_WITH=	gnutls

OPENSSL_CONFIGURE_WITH=	crypto ssl
OPENSSL_USE=		OPENSSL=yes

GCRYPT_LIB_DEPENDS=	libgcrypt.so:security/libgcrypt \
			libgpg-error.so:security/libgpg-error
GCRYPT_CONFIGURE_WITH=	gcrypt

IPV6_CONFIGURE_WITH=	ipv6

VAAPI_LIB_DEPENDS=	libva.so:multimedia/libva
VAAPI_CONFIGURE_WITH=	libva

post-install:
	${INSTALL_DATA} ${WRKSRC}/rfb/default8x16.h ${STAGEDIR}${PREFIX}/include/rfb

# XigmaNAS install
	@echo "install for ${XIGMANAS_ROOTFS}"
	@${INSTALL_DATA} -v ${STAGEDIR}${PREFIX}/lib/libvncserver.so.1 ${XIGMANAS_ROOTFS}/usr/local/lib
# XXX
	@${INSTALL_DATA} -v ${STAGEDIR}${PREFIX}/lib/libvncserver.so.1 /usr/local/lib

.include <bsd.port.mk>
