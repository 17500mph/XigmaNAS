PORTNAME=		zoneinfo
DISTVERSION=	2018e
CATEGORIES=		misc
COMMENT=		Updated timezone definitions
MAINTAINER=		zoon01@xigmanas.com

MASTER_SITES=   	ftp://ftp.iana.org/tz/releases/ \
					ftp://munnari.oz.au/pub/
DISTNAME=		tzdata${DISTVERSION}

WRKSRC=	${WRKDIR}
PREFIX=	/usr

TZFILES=	africa antarctica asia australasia etcetera europe \
		factory northamerica southamerica systemv
POSIXRULES=	America/New_York

.include <bsd.port.pre.mk>

# The following country codes can be missed from /usr/share/misc/iso3166 and
# shouldn't appear in the zoneinfo file.
MISSING=	ax bl bq cw gg im je me mf rs ss sx

.ifndef NOMISSING
.for M in ${MISSING}
HAS_${M:tu}!=	${GREP} -c ^${M:tu} /usr/share/misc/iso3166 || ${TRUE}
.if ${HAS_${M:tu}} == "0"
EXTRA_PATCHES+=	${FILESDIR}/patchremove-${M}
.endif
.endfor
.endif

do-build:
	umask 022; \
	cd ${WRKSRC}; \
	zic -d ${WRKSRC}/zoneinfo -p ${POSIXRULES} -m ${NOBINMODE} \
		${LEAPFILE} -y ${.OBJDIR}/yearistype ${TZFILES}

test-patches:
.for M in ${MISSING}
	make clean
	NOMISSING=1 EXTRA_PATCHES=${FILESDIR}/patchremove-${M} make patch
.endfor

do-install:
# Compress zoneinfo data, exclude some useless files.
	@${ECHO_CMD} "Factory" > ${WRKSRC}/zoneinfo.exlude
	@${ECHO_CMD} "posixrules" >> ${WRKSRC}/zoneinfo.exlude
	@${ECHO_CMD} "zone.tab" >> ${WRKSRC}/zoneinfo.exlude

	@${MKDIR} -p ${XIGMANAS_ROOTFS}/usr/share
	${TAR} -c -v -f - -X ${WRKSRC}/zoneinfo.exlude -C ${WRKSRC}/zoneinfo/ . | ${XZ_CMD} -cv > ${XIGMANAS_ROOTFS}/usr/share/zoneinfo.txz

.include <bsd.port.post.mk>
