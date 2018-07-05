PORTNAME=		locale
PORTVERSION=	1.0
CATEGORIES=		lang
COMMENT=		Language packages for NAS4Free
MAINTAINER=		zoon1@nas4free.org

DISTFILES?=

.include <../nas4free.mk>

do-build:
# Translate *.po files to *.mo.
	@for i in $$(ls ${XIGMANAS_SVNDIR}/locale/*.po); do \
		filename=$$(basename $$i); \
		language=$${filename%*.po}; \
		language=$${language#*-}; \
		filename=$$(echo ${XIGMANAS_PRODUCTNAME} | tr '[A-Z]' '[a-z]'); \
		${MKDIR} -v ${WRKSRC}/$$language/LC_MESSAGES; \
		msgfmt -v --output-file="${WRKSRC}/$$language/LC_MESSAGES/$$filename.mo" $$i; \
	done

do-install:
	@${CP} -vp -r ${WRKSRC}/* ${XIGMANAS_ROOTFS}/usr/local/share/locale

.include <bsd.port.mk>
