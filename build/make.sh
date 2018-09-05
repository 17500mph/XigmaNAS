#!/usr/bin/env bash
#
# This script is designed to automate the assembly of XigmaNAS builds.
#
# Part of XigmaNAS (https://www.xigmanas.com).
# Copyright (c) 2018 by XigmaNAS <info@xigmanas.com>.
# All rights reserved.
#
# Debug script
# set -x
#

################################################################################
# Settings
################################################################################

# Global variables
XIGMANAS_ROOTDIR="/usr/local/xigmanas"
XIGMANAS_WORKINGDIR="$XIGMANAS_ROOTDIR/work"
XIGMANAS_ROOTFS="$XIGMANAS_ROOTDIR/rootfs"
XIGMANAS_SVNDIR="$XIGMANAS_ROOTDIR/svn"
XIGMANAS_WORLD=""
XIGMANAS_PRODUCTNAME=$(cat $XIGMANAS_SVNDIR/etc/prd.name)
XIGMANAS_VERSION=$(cat $XIGMANAS_SVNDIR/etc/prd.version)
XIGMANAS_REVISION=$(svn info ${XIGMANAS_SVNDIR} | grep "Revision:" | awk '{print $2}')
if [ -f "${XIGMANAS_SVNDIR}/local.revision" ]; then
	XIGMANAS_REVISION=$(printf $(cat ${XIGMANAS_SVNDIR}/local.revision) ${XIGMANAS_REVISION})
fi
XIGMANAS_ARCH=$(uname -p)
XIGMANAS_KERNCONF="$(echo ${XIGMANAS_PRODUCTNAME} | tr '[:lower:]' '[:upper:]')-${XIGMANAS_ARCH}"
XIGMANAS_BUILD_DOM0=0
if [ -f ${XIGMANAS_ROOTDIR}/build-dom0 ]; then
    XIGMANAS_BUILD_DOM0=1
fi
if [ "amd64" = ${XIGMANAS_ARCH} ]; then
    XIGMANAS_XARCH="x64"
    if [ ${XIGMANAS_BUILD_DOM0} -ne 0 ]; then
	XIGMANAS_XARCH="dom0"
	XIGMANAS_KERNCONF="$(echo ${XIGMANAS_PRODUCTNAME} | tr '[:lower:]' '[:upper:]')-${XIGMANAS_XARCH}"
    fi
elif [ "i386" = ${XIGMANAS_ARCH} ]; then
 echo "->> build script does not support 32-bit builds for the i386 architecture"
exit
elif [ "armv6" = ${XIGMANAS_ARCH} ]; then
    XIGMANAS_ARCH="arm"
    PLATFORM=$(sysctl -n hw.platform)
    if [ "bcm2835" = ${PLATFORM} ]; then
	XIGMANAS_XARCH="rpi"
    elif [ "bcm2836" = ${PLATFORM} ]; then
	XIGMANAS_XARCH="rpi2"
    elif [ "meson8b" = ${PLATFORM} ]; then
	XIGMANAS_XARCH="oc1"
    else
	XIGMANAS_XARCH=$XIGMANAS_ARCH
    fi
    XIGMANAS_KERNCONF="$(echo ${XIGMANAS_PRODUCTNAME} | tr '[:lower:]' '[:upper:]')-${XIGMANAS_XARCH}"
else
    XIGMANAS_XARCH=$XIGMANAS_ARCH
fi
XIGMANAS_OBJDIRPREFIX="/usr/obj/$(echo ${XIGMANAS_PRODUCTNAME} | tr '[:upper:]' '[:lower:]')"
XIGMANAS_BOOTDIR="$XIGMANAS_ROOTDIR/bootloader"
XIGMANAS_TMPDIR="/tmp/xigmanastmp"

export XIGMANAS_ROOTDIR
export XIGMANAS_WORKINGDIR
export XIGMANAS_ROOTFS
export XIGMANAS_SVNDIR
export XIGMANAS_WORLD
export XIGMANAS_PRODUCTNAME
export XIGMANAS_VERSION
export XIGMANAS_ARCH
export XIGMANAS_XARCH
export XIGMANAS_KERNCONF
export XIGMANAS_OBJDIRPREFIX
export XIGMANAS_BOOTDIR
export XIGMANAS_REVISION
export XIGMANAS_TMPDIR
#export XIGMANAS_BUILD_DOM0

XIGMANAS_MK=${XIGMANAS_SVNDIR}/build/ports/xigmanas.mk
rm -rf ${XIGMANAS_MK}
echo "XIGMANAS_ROOTDIR=${XIGMANAS_ROOTDIR}" >> ${XIGMANAS_MK}
echo "XIGMANAS_WORKINGDIR=${XIGMANAS_WORKINGDIR}" >> ${XIGMANAS_MK}
echo "XIGMANAS_ROOTFS=${XIGMANAS_ROOTFS}" >> ${XIGMANAS_MK}
echo "XIGMANAS_SVNDIR=${XIGMANAS_SVNDIR}" >> ${XIGMANAS_MK}
echo "XIGMANAS_WORLD=${XIGMANAS_WORLD}" >> ${XIGMANAS_MK}
echo "XIGMANAS_PRODUCTNAME=${XIGMANAS_PRODUCTNAME}" >> ${XIGMANAS_MK}
echo "XIGMANAS_VERSION=${XIGMANAS_VERSION}" >> ${XIGMANAS_MK}
echo "XIGMANAS_ARCH=${XIGMANAS_ARCH}" >> ${XIGMANAS_MK}
echo "XIGMANAS_XARCH=${XIGMANAS_XARCH}" >> ${XIGMANAS_MK}
echo "XIGMANAS_KERNCONF=${XIGMANAS_KERNCONF}" >> ${XIGMANAS_MK}
echo "XIGMANAS_OBJDIRPREFIX=${XIGMANAS_OBJDIRPREFIX}" >> ${XIGMANAS_MK}
echo "XIGMANAS_BOOTDIR=${XIGMANAS_BOOTDIR}" >> ${XIGMANAS_MK}
echo "XIGMANAS_REVISION=${XIGMANAS_REVISION}" >> ${XIGMANAS_MK}
echo "XIGMANAS_TMPDIR=${XIGMANAS_TMPDIR}" >> ${XIGMANAS_MK}
#echo "XIGMANAS_BUILD_DOM0=${XIGMANAS_BUILD_DOM0}" >> ${XIGMANAS_MK}

# Local variables
XIGMANAS_URL=$(cat $XIGMANAS_SVNDIR/etc/prd.url)
XIGMANAS_SVNURL="https://svn.code.sf.net/p/xigmanas/code/trunk"
XIGMANAS_SVN_SRCTREE="svn://svn.FreeBSD.org/base/releng/11.2"

# Size in MB of the MFS Root filesystem that will include all FreeBSD binary
# and XIGMANAS WEbGUI/Scripts. Keep this file very small! This file is unzipped
# to a RAM disk at XIGMANAS startup.
# The image must fit on 2GB CF/USB.
# Actual size of MDLOCAL is defined in /etc/rc.
XIGMANAS_MFSROOT_SIZE=128
XIGMANAS_MDLOCAL_SIZE=768
XIGMANAS_MDLOCAL_MINI_SIZE=36
# Now image size is less than 500MB (up to 476MiB - alignment)
XIGMANAS_IMG_SIZE=460
if [ "amd64" = ${XIGMANAS_ARCH} ]; then
	XIGMANAS_MFSROOT_SIZE=128
	XIGMANAS_MDLOCAL_SIZE=768
	XIGMANAS_MDLOCAL_MINI_SIZE=38
	XIGMANAS_IMG_SIZE=470
fi
# xz9->673MB/64MB, 8->369MB/32MB, 7->185MB/16MB, 6->93MB/8MB, 5->47MB/4MB
# 4->24MB/2.1MB, 3->12.6MB/1.1MB, 2->4.8MB/576KB, 1->1.4MB/128KB
if [ "arm" = ${XIGMANAS_ARCH} ]; then
	XIGMANAS_COMPLEVEL=3
else
	XIGMANAS_COMPLEVEL=8
fi
XIGMANAS_XMD_SEGLEN=32768
#XIGMANAS_XMD_SEGLEN=65536

# Media geometry, only relevant if bios doesn't understand LBA.
XIGMANAS_IMG_SIZE_SEC=`expr ${XIGMANAS_IMG_SIZE} \* 2048`
XIGMANAS_IMG_SECTS=63
#XIGMANAS_IMG_HEADS=16
XIGMANAS_IMG_HEADS=255
# cylinder alignment
XIGMANAS_IMG_SIZE_SEC=`expr \( $XIGMANAS_IMG_SIZE_SEC / \( $XIGMANAS_IMG_SECTS \* $XIGMANAS_IMG_HEADS \) \) \* \( $XIGMANAS_IMG_SECTS \* $XIGMANAS_IMG_HEADS \)`

# aligned BSD partition on MBR slice
XIGMANAS_IMG_SSTART=$XIGMANAS_IMG_SECTS
XIGMANAS_IMG_SSIZE=`expr $XIGMANAS_IMG_SIZE_SEC - $XIGMANAS_IMG_SSTART`
# aligned by BLKSEC: 8=4KB, 64=32KB, 128=64KB, 2048=1MB
XIGMANAS_IMG_BLKSEC=8
#XIGMANAS_IMG_BLKSEC=64
XIGMANAS_IMG_BLKSIZE=`expr $XIGMANAS_IMG_BLKSEC \* 512`
# PSTART must BLKSEC aligned in the slice.
XIGMANAS_IMG_POFFSET=16
XIGMANAS_IMG_PSTART=`expr \( \( \( $XIGMANAS_IMG_SSTART + $XIGMANAS_IMG_POFFSET + $XIGMANAS_IMG_BLKSEC - 1 \) / $XIGMANAS_IMG_BLKSEC \) \* $XIGMANAS_IMG_BLKSEC \) - $XIGMANAS_IMG_SSTART`
XIGMANAS_IMG_PSIZE0=`expr $XIGMANAS_IMG_SSIZE - $XIGMANAS_IMG_PSTART`
if [ `expr $XIGMANAS_IMG_PSIZE0 % $XIGMANAS_IMG_BLKSEC` -ne 0 ]; then
    XIGMANAS_IMG_PSIZE=`expr $XIGMANAS_IMG_PSIZE0 - \( $XIGMANAS_IMG_PSIZE0 % $XIGMANAS_IMG_BLKSEC \)`
else
    XIGMANAS_IMG_PSIZE=$XIGMANAS_IMG_PSIZE0
fi

# BSD partition only
XIGMANAS_IMG_SSTART=0
XIGMANAS_IMG_SSIZE=$XIGMANAS_IMG_SIZE_SEC
XIGMANAS_IMG_BLKSEC=1
XIGMANAS_IMG_BLKSIZE=512
XIGMANAS_IMG_POFFSET=16
XIGMANAS_IMG_PSTART=$XIGMANAS_IMG_POFFSET
XIGMANAS_IMG_PSIZE=`expr $XIGMANAS_IMG_SSIZE - $XIGMANAS_IMG_PSTART`

# newfs parameters
XIGMANAS_IMGFMT_SECTOR=512
XIGMANAS_IMGFMT_FSIZE=2048
#XIGMANAS_IMGFMT_SECTOR=4096
#XIGMANAS_IMGFMT_FSIZE=4096
XIGMANAS_IMGFMT_BSIZE=`expr $XIGMANAS_IMGFMT_FSIZE \* 8`

#echo "IMAGE=$XIGMANAS_IMG_SIZE_SEC"
#echo "SSTART=$XIGMANAS_IMG_SSTART"
#echo "SSIZE=$XIGMANAS_IMG_SSIZE"
#echo "ALIGN=$XIGMANAS_IMG_BLKSEC"
#echo "PSTART=$XIGMANAS_IMG_PSTART"
#echo "PSIZE0=$XIGMANAS_IMG_PSIZE0"
#echo "PSIZE=$XIGMANAS_IMG_PSIZE"

# Options:
# Support bootmenu
OPT_BOOTMENU=1
# Support bootsplash
OPT_BOOTSPLASH=0
# Support serial console
OPT_SERIALCONSOLE=0

# Dialog command
DIALOG="dialog"

################################################################################
# Functions
################################################################################

# Update source tree and ports collection.
update_sources() {
	tempfile=$XIGMANAS_WORKINGDIR/tmp$$

	# Choose what to do.
	$DIALOG --title "$XIGMANAS_PRODUCTNAME - Update Sources" --checklist "Please select what to update." 12 60 5 \
		"svnco" "Fetch source tree" OFF \
		"svnup" "Update source tree" OFF \
		"freebsd-update" "Fetch and install binary updates" OFF \
		"portsnap" "Update ports collection" OFF \
		"portupgrade" "Upgrade ports on host" OFF 2> $tempfile
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi

	choices=`cat $tempfile`
	rm $tempfile

	for choice in $(echo $choices | tr -d '"'); do
		case $choice in
			freebsd-update)
				freebsd-update fetch install;;
			portsnap)
				portsnap fetch update;;
			svnco)
				rm -rf /usr/src; svn co ${XIGMANAS_SVN_SRCTREE} /usr/src;;
			svnup)
				svn up /usr/src;;
			portupgrade)
				portupgrade -aFP;;
  	esac
  done

	return $?
}

# Build world. Copying required files defined in 'build/xigmanas.files'.
build_world() {
	# Make a pseudo 'chroot' to XIGMANAS root.
  cd $XIGMANAS_ROOTFS

	echo
	echo "Building World:"

	[ -f $XIGMANAS_WORKINGDIR/xigmanas.files ] && rm -f $XIGMANAS_WORKINGDIR/xigmanas.files
	cp $XIGMANAS_SVNDIR/build/xigmanas.files $XIGMANAS_WORKINGDIR

	# Add custom binaries
	if [ -f $XIGMANAS_WORKINGDIR/xigmanas.custfiles ]; then
		cat $XIGMANAS_WORKINGDIR/xigmanas.custfiles >> $XIGMANAS_WORKINGDIR/xigmanas.files
	fi

	for i in $(cat $XIGMANAS_WORKINGDIR/xigmanas.files | grep -v "^#"); do
		file=$(echo "$i" | cut -d ":" -f 1)

		# Deal with directories
		dir=$(dirname $file)
		if [ ! -d ${XIGMANAS_WORLD}/$dir ]; then
			echo "skip: $file ($dir)"
			continue;
		fi
		if [ ! -d $dir ]; then
		  mkdir -pv $dir
		fi
		#if [ "$(echo $file | grep '*')" == "" -a ! -f ${XIGMANAS_WORLD}/$file ]; then
		#	echo "skip: $file ($dir)"
		#	continue;
		#fi

		# Copy files from world.
		cp -Rpv ${XIGMANAS_WORLD}/$file $(echo $file | rev | cut -d "/" -f 2- | rev)

		# Deal with links
		if [ $(echo "$i" | grep -c ":") -gt 0 ]; then
			for j in $(echo $i | cut -d ":" -f 2- | sed "s/:/ /g"); do
				ln -sv /$file $j
			done
		fi
	done

	# iconv files
	(cd ${XIGMANAS_WORLD}/; find -x usr/lib/i18n | cpio -pdv ${XIGMANAS_ROOTFS})
	(cd ${XIGMANAS_WORLD}/; find -x usr/share/i18n | cpio -pdv ${XIGMANAS_ROOTFS})

	# Cleanup
	chflags -R noschg $XIGMANAS_TMPDIR
	chflags -R noschg $XIGMANAS_ROOTFS
	[ -d $XIGMANAS_TMPDIR ] && rm -f $XIGMANAS_WORKINGDIR/xigmanas.files
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz

	return 0
}

# Create rootfs
create_rootfs() {
	$XIGMANAS_SVNDIR/build/xigmanas-create-rootfs.sh -f $XIGMANAS_ROOTFS

	# Configuring platform variable
	echo ${XIGMANAS_VERSION} > ${XIGMANAS_ROOTFS}/etc/prd.version

	# Config file: config.xml
	cd $XIGMANAS_ROOTFS/conf.default/
	cp -v $XIGMANAS_SVNDIR/conf/config.xml .

	# Compress zoneinfo data, exclude some useless files.
	mkdir $XIGMANAS_TMPDIR
	echo "Factory" > $XIGMANAS_TMPDIR/zoneinfo.exlude
	echo "posixrules" >> $XIGMANAS_TMPDIR/zoneinfo.exlude
	echo "zone.tab" >> $XIGMANAS_TMPDIR/zoneinfo.exlude
	tar -c -v -f - -X $XIGMANAS_TMPDIR/zoneinfo.exlude -C /usr/share/zoneinfo/ . | xz -cv > $XIGMANAS_ROOTFS/usr/share/zoneinfo.txz
	rm $XIGMANAS_TMPDIR/zoneinfo.exlude

	return 0
}

# Actions before building kernel (e.g. install special/additional kernel patches).
pre_build_kernel() {
	tempfile=$XIGMANAS_WORKINGDIR/tmp$$
	patches=$XIGMANAS_WORKINGDIR/patches$$

	# Create list of available packages.
	echo "#! /bin/sh
$DIALOG --title \"$XIGMANAS_PRODUCTNAME - Kernel Patches\" \\
--checklist \"Select the patches you want to add. Make sure you have clean/origin kernel sources (via suvbersion) to apply patches successful.\" 22 88 14 \\" > $tempfile

	for s in $XIGMANAS_SVNDIR/build/kernel-patches/*; do
		[ ! -d "$s" ] && continue
		package=`basename $s`
		desc=`cat $s/pkg-descr`
		state=`cat $s/pkg-state`
		echo "\"$package\" \"$desc\" $state \\" >> $tempfile
	done

	# Display list of available kernel patches.
	sh $tempfile 2> $patches
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi
	rm $tempfile

	echo "Remove old patched files..."
	for file in $(find /usr/src -name "*.orig"); do
		rm -rv ${file}
	done

	for patch in $(cat $patches | tr -d '"'); do
    echo
		echo "--------------------------------------------------------------"
		echo ">>> Adding kernel patch: ${patch}"
		echo "--------------------------------------------------------------"
		cd $XIGMANAS_SVNDIR/build/kernel-patches/$patch
		make install
		[ 0 != $? ] && return 1 # successful?
	done
	rm $patches
}

# Build/Install the kernel.
build_kernel() {
	tempfile=$XIGMANAS_WORKINGDIR/tmp$$

	# Make sure kernel directory exists.
	[ ! -d "${XIGMANAS_ROOTFS}/boot/kernel" ] && mkdir -p ${XIGMANAS_ROOTFS}/boot/kernel

	# Choose what to do.
	$DIALOG --title "$XIGMANAS_PRODUCTNAME - Build/Install Kernel" --checklist "Please select whether you want to build or install the kernel." 10 75 3 \
		"prebuild" "Apply kernel patches" OFF \
		"build" "Build kernel" OFF \
		"install" "Install kernel + modules" ON 2> $tempfile
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi

	choices=`cat $tempfile`
	rm $tempfile

	for choice in $(echo $choices | tr -d '"'); do
		case $choice in
			prebuild)
				# Apply kernel patches.
				pre_build_kernel;
				[ 0 != $? ] && return 1;; # successful?
			build)
				# Copy kernel configuration.
				cd /sys/${XIGMANAS_ARCH}/conf;
				cp -f $XIGMANAS_SVNDIR/build/kernel-config/${XIGMANAS_KERNCONF} .;
				# Clean object directory.
				rm -f -r ${XIGMANAS_OBJDIRPREFIX};
				# Compiling and compressing the kernel.
				cd /usr/src;
				env MAKEOBJDIRPREFIX=${XIGMANAS_OBJDIRPREFIX} make -j4 buildkernel KERNCONF=${XIGMANAS_KERNCONF};
				gzip -9cnv ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/kernel > ${XIGMANAS_WORKINGDIR}/kernel.gz;;
			install)
				# Installing the modules.
				echo "--------------------------------------------------------------";
				echo ">>> Install Kernel Modules";
				echo "--------------------------------------------------------------";

				[ -f ${XIGMANAS_WORKINGDIR}/modules.files ] && rm -f ${XIGMANAS_WORKINGDIR}/modules.files;
				cp ${XIGMANAS_SVNDIR}/build/kernel-config/modules.files ${XIGMANAS_WORKINGDIR};

				modulesdir=${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules;
				for module in $(cat ${XIGMANAS_WORKINGDIR}/modules.files | grep -v "^#"); do
					install -v -o root -g wheel -m 555 ${modulesdir}/${module} ${XIGMANAS_ROOTFS}/boot/kernel
				done
				;;
  	esac
  done

	return 0
}

# Adding the libraries
add_libs() {
	echo
	echo "Adding required libs:"

	# Identify required libs.
	[ -f /tmp/lib.list ] && rm -f /tmp/lib.list
	dirs=(${XIGMANAS_ROOTFS}/bin ${XIGMANAS_ROOTFS}/sbin ${XIGMANAS_ROOTFS}/usr/bin ${XIGMANAS_ROOTFS}/usr/sbin ${XIGMANAS_ROOTFS}/usr/local/bin ${XIGMANAS_ROOTFS}/usr/local/sbin ${XIGMANAS_ROOTFS}/usr/lib ${XIGMANAS_ROOTFS}/usr/local/lib ${XIGMANAS_ROOTFS}/usr/libexec ${XIGMANAS_ROOTFS}/usr/local/libexec)
	for i in ${dirs[@]}; do
		for file in $(find -L ${i} -type f -print); do
			ldd -f "%p\n" ${file} 2> /dev/null >> /tmp/lib.list
		done
	done

	# Copy identified libs.
	for i in $(sort -u /tmp/lib.list); do
		if [ -e "${XIGMANAS_WORLD}${i}" ]; then
			DESTDIR=${XIGMANAS_ROOTFS}$(echo $i | rev | cut -d '/' -f 2- | rev)
			if [ ! -d ${DESTDIR} ]; then
			    DESTDIR=${XIGMANAS_ROOTFS}/usr/local/lib
			fi
			FILE=`basename ${i}`
			if [ -L "${DESTDIR}/${FILE}" ]; then
				# do not remove symbolic link
				echo "link: ${i}"
			else
				install -c -s -v ${XIGMANAS_WORLD}${i} ${DESTDIR}
			fi
		fi
	done

	# for compatibility
	# install -c -s -v ${XIGMANAS_WORLD}/lib/libreadline.* ${XIGMANAS_ROOTFS}/lib
	install -c -s -v ${XIGMANAS_WORLD}/usr/lib/libblacklist.so.* ${XIGMANAS_ROOTFS}/usr/lib
	install -c -s -v ${XIGMANAS_WORLD}/usr/lib/libgssapi_krb5.so.* ${XIGMANAS_ROOTFS}/usr/lib
	install -c -s -v ${XIGMANAS_WORLD}/usr/lib/libgssapi_ntlm.so.* ${XIGMANAS_ROOTFS}/usr/lib
	install -c -s -v ${XIGMANAS_WORLD}/usr/lib/libgssapi_spnego.so.* ${XIGMANAS_ROOTFS}/usr/lib

	# Cleanup.
	rm -f /tmp/lib.list

  return 0
}

# Creating mdlocal-mini
create_mdlocal_mini() {
	echo "--------------------------------------------------------------"
	echo ">>> Generating MDLOCAL mini"
	echo "--------------------------------------------------------------"

	cd $XIGMANAS_WORKINGDIR

	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.files ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.files
	cp $XIGMANAS_SVNDIR/build/xigmanas-mdlocal-mini.files $XIGMANAS_WORKINGDIR/mdlocal-mini.files

	# Make mfsroot to have the size of the XIGMANAS_MFSROOT_SIZE variable
	#dd if=/dev/zero of=$XIGMANAS_WORKINGDIR/mdlocal-mini bs=1k count=$(expr ${XIGMANAS_MDLOCAL_MINI_SIZE} \* 1024)
	dd if=/dev/zero of=$XIGMANAS_WORKINGDIR/mdlocal-mini bs=1k seek=$(expr ${XIGMANAS_MDLOCAL_MINI_SIZE} \* 1024) count=0
	# Configure this file as a memory disk
	md=`mdconfig -a -t vnode -f $XIGMANAS_WORKINGDIR/mdlocal-mini`
	# Format memory disk using UFS
	newfs -S $XIGMANAS_IMGFMT_SECTOR -b $XIGMANAS_IMGFMT_BSIZE -f $XIGMANAS_IMGFMT_FSIZE -O2 -o space -m 0 -U -t /dev/${md}
	# Umount memory disk (if already used)
	umount $XIGMANAS_TMPDIR >/dev/null 2>&1
	# Mount memory disk
	mkdir -p ${XIGMANAS_TMPDIR}/usr/local
	mount /dev/${md} ${XIGMANAS_TMPDIR}/usr/local

	# Create tree
	cd $XIGMANAS_ROOTFS/usr/local
	find . -type d | cpio -pmd ${XIGMANAS_TMPDIR}/usr/local

	# Copy selected files
	cd $XIGMANAS_TMPDIR
	for i in $(cat $XIGMANAS_WORKINGDIR/mdlocal-mini.files | grep -v "^#"); do
		d=`dirname $i`
		b=`basename $i`
		echo "cp $XIGMANAS_ROOTFS/$d/$b  ->  $XIGMANAS_TMPDIR/$d/$b"
		cp $XIGMANAS_ROOTFS/$d/$b $XIGMANAS_TMPDIR/$d/$b
		# Copy required libraries
		for j in $(ldd $XIGMANAS_ROOTFS/$d/$b | cut -w -f 4 | grep /usr/local | sed -e '/:/d' -e 's/^\///'); do
			d=`dirname $j`
			b=`basename $j`
			if [ ! -e $XIGMANAS_TMPDIR/$d/$b ]; then
				echo "cp $XIGMANAS_ROOTFS/$d/$b  ->  $XIGMANAS_TMPDIR/$d/$b"
				cp $XIGMANAS_ROOTFS/$d/$b $XIGMANAS_TMPDIR/$d/$b
			fi
		done
	done

	# Identify required libs.
	[ -f /tmp/lib.list ] && rm -f /tmp/lib.list
	dirs=(${XIGMANAS_TMPDIR}/usr/local/bin ${XIGMANAS_TMPDIR}/usr/local/sbin ${XIGMANAS_TMPDIR}/usr/local/lib ${XIGMANAS_TMPDIR}/usr/local/libexec)
	for i in ${dirs[@]}; do
		for file in $(find -L ${i} -type f -print); do
			ldd -f "%p\n" ${file} 2> /dev/null >> /tmp/lib.list
		done
	done

	# Copy identified libs.
	for i in $(sort -u /tmp/lib.list); do
		if [ -e "${XIGMANAS_WORLD}${i}" ]; then
			d=`dirname $i`
			b=`basename $i`
			if [ "$d" = "/lib" -o "$d" = "/usr/lib" ]; then
				# skip lib in mfsroot
				[ -e ${XIGMANAS_ROOTFS}${i} ] && continue
			fi
			DESTDIR=${XIGMANAS_TMPDIR}$(echo $i | rev | cut -d '/' -f 2- | rev)
			if [ ! -d ${DESTDIR} ]; then
			    DESTDIR=${XIGMANAS_TMPDIR}/usr/local/lib
			fi
			install -c -s -v ${XIGMANAS_WORLD}${i} ${DESTDIR}
		fi
	done

	# Cleanup.
	rm -f /tmp/lib.list

	# Umount memory disk
	umount $XIGMANAS_TMPDIR/usr/local
	# Detach memory disk
	mdconfig -d -u ${md}

	xz -${XIGMANAS_COMPLEVEL}v $XIGMANAS_WORKINGDIR/mdlocal-mini

	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.files ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.files

	return 0
}

# Creating msfroot
create_mfsroot() {
	echo "--------------------------------------------------------------"
	echo ">>> Generating MFSROOT Filesystem"
	echo "--------------------------------------------------------------"

	cd $XIGMANAS_WORKINGDIR

	[ -f $XIGMANAS_WORKINGDIR/mfsroot ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	[ -d $XIGMANAS_SVNDIR ] && use_svn ;

	# Make mfsroot to have the size of the XIGMANAS_MFSROOT_SIZE variable
	#dd if=/dev/zero of=$XIGMANAS_WORKINGDIR/mfsroot bs=1k count=$(expr ${XIGMANAS_MFSROOT_SIZE} \* 1024)
	#dd if=/dev/zero of=$XIGMANAS_WORKINGDIR/mdlocal bs=1k count=$(expr ${XIGMANAS_MDLOCAL_SIZE} \* 1024)
	dd if=/dev/zero of=$XIGMANAS_WORKINGDIR/mfsroot bs=1k seek=$(expr ${XIGMANAS_MFSROOT_SIZE} \* 1024) count=0
	dd if=/dev/zero of=$XIGMANAS_WORKINGDIR/mdlocal bs=1k seek=$(expr ${XIGMANAS_MDLOCAL_SIZE} \* 1024) count=0
	# Configure this file as a memory disk
	md=`mdconfig -a -t vnode -f $XIGMANAS_WORKINGDIR/mfsroot`
	md2=`mdconfig -a -t vnode -f $XIGMANAS_WORKINGDIR/mdlocal`
	# Format memory disk using UFS
	newfs -S $XIGMANAS_IMGFMT_SECTOR -b $XIGMANAS_IMGFMT_BSIZE -f $XIGMANAS_IMGFMT_FSIZE -O2 -o space -m 0 /dev/${md}
	newfs -S $XIGMANAS_IMGFMT_SECTOR -b $XIGMANAS_IMGFMT_BSIZE -f $XIGMANAS_IMGFMT_FSIZE -O2 -o space -m 0 -U -t /dev/${md2}
	# Umount memory disk (if already used)
	umount $XIGMANAS_TMPDIR >/dev/null 2>&1
	# Mount memory disk
	mount /dev/${md} ${XIGMANAS_TMPDIR}
	mkdir -p ${XIGMANAS_TMPDIR}/usr/local
	mount /dev/${md2} ${XIGMANAS_TMPDIR}/usr/local
	cd $XIGMANAS_TMPDIR
	tar -cf - -C $XIGMANAS_ROOTFS ./ | tar -xvpf -

	cd $XIGMANAS_WORKINGDIR
	# Umount memory disk
	umount $XIGMANAS_TMPDIR/usr/local
	umount $XIGMANAS_TMPDIR
	# Detach memory disk
	mdconfig -d -u ${md2}
	mdconfig -d -u ${md}

	#mkuzip -s ${XIGMANAS_XMD_SEGLEN} $XIGMANAS_WORKINGDIR/mfsroot
	#chmod 644 $XIGMANAS_WORKINGDIR/mfsroot.uzip
	gzip -9kfnv $XIGMANAS_WORKINGDIR/mfsroot
	if [ "arm" = ${XIGMANAS_ARCH} ]; then
		mkuzip -s ${XIGMANAS_XMD_SEGLEN} $XIGMANAS_WORKINGDIR/mdlocal
	fi
	xz -${XIGMANAS_COMPLEVEL}kv $XIGMANAS_WORKINGDIR/mdlocal

	create_mdlocal_mini;

	return 0
}

update_mfsroot() {
	echo "--------------------------------------------------------------"
	echo ">>> Generating MFSROOT Filesystem (use existing image)"
	echo "--------------------------------------------------------------"

	# Check if mfsroot exists.
	if [ ! -f $XIGMANAS_WORKINGDIR/mfsroot ]; then
		echo "==> Error: $XIGMANAS_WORKINGDIR/mfsroot does not exist."
		return 1
	fi

	# Cleanup.
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	#[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	#[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip

	cd $XIGMANAS_WORKINGDIR
	#mkuzip -s ${XIGMANAS_XMD_SEGLEN} $XIGMANAS_WORKINGDIR/mfsroot
	#chmod 644 $XIGMANAS_WORKINGDIR/mfsroot.uzip
	gzip -9kfnv $XIGMANAS_WORKINGDIR/mfsroot
	#xz -8kv $XIGMANAS_WORKINGDIR/mdlocal

	return 0
}

copy_kmod() {
	local kmodlist
	echo "Copy kmod to $XIGMANAS_TMPDIR/boot/kernel"
	kmodlist=`(cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules; find . -name '*.ko' | sed -e 's/\.\///')`
	for f in $kmodlist; do
		if grep -q "^${f}" $XIGMANAS_SVNDIR/build/xigmanas.kmod.exclude > /dev/null; then
			echo "skip: $f"
			continue;
		fi
		b=`basename ${f}`
		#(cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules; install -v -o root -g wheel -m 555 ${f} $XIGMANAS_TMPDIR/boot/kernel/${b}; gzip -9 $XIGMANAS_TMPDIR/boot/kernel/${b})
		(cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules; install -v -o root -g wheel -m 555 ${f} $XIGMANAS_TMPDIR/boot/kernel/${b})
	done
	return 0;
}

create_image() {
	echo "--------------------------------------------------------------"
	echo ">>> Generating ${XIGMANAS_PRODUCTNAME} IMG File (to be rawrite on CF/USB/HD/SSD)"
	echo "--------------------------------------------------------------"

	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$XIGMANAS_ROOTFS" ]; then
		echo "==> Error: ${XIGMANAS_ROOTFS} does not exist."
		return 1
	fi

	# Cleanup.
	[ -f ${XIGMANAS_WORKINGDIR}/image.bin ] && rm -f ${XIGMANAS_WORKINGDIR}/image.bin
	[ -f ${XIGMANAS_WORKINGDIR}/image.bin.xz ] && rm -f ${XIGMANAS_WORKINGDIR}/image.bin.xz

	# Set platform information.
	PLATFORM="${XIGMANAS_XARCH}-embedded"
	echo $PLATFORM > ${XIGMANAS_ROOTFS}/etc/platform

	# Set build time.
	date > ${XIGMANAS_ROOTFS}/etc/prd.version.buildtime
	date "+%s" > ${XIGMANAS_ROOTFS}/etc/prd.version.buildtimestamp

	# Set revision.
	echo ${XIGMANAS_REVISION} > ${XIGMANAS_ROOTFS}/etc/prd.revision

	IMGFILENAME="${XIGMANAS_PRODUCTNAME}-${PLATFORM}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.img"

	echo "===> Generating tempory $XIGMANAS_TMPDIR folder"
	mkdir $XIGMANAS_TMPDIR
	create_mfsroot;

	echo "===> Creating Empty IMG File"
	#dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/image.bin bs=${XIGMANAS_IMG_SECTS}b count=`expr ${XIGMANAS_IMG_SIZE_SEC} / ${XIGMANAS_IMG_SECTS} + 64`
	dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/image.bin bs=512 seek=`expr ${XIGMANAS_IMG_SIZE_SEC}` count=0
	echo "===> Use IMG as a memory disk"
	md=`mdconfig -a -t vnode -f ${XIGMANAS_WORKINGDIR}/image.bin -x ${XIGMANAS_IMG_SECTS} -y ${XIGMANAS_IMG_HEADS}`
	diskinfo -v ${md}

	IMGSIZEM=450

	# create 1MB aligned MBR image
	echo "===> Creating MBR partition on this memory disk"
	gpart create -s mbr ${md}
	gpart add -t freebsd ${md}
	gpart set -a active -i 1 ${md}
	gpart bootcode -b ${XIGMANAS_BOOTDIR}/mbr ${md}

	echo "===> Creating BSD partition on this memory disk"
	gpart create -s bsd ${md}s1
	gpart bootcode -b ${XIGMANAS_BOOTDIR}/boot ${md}s1
	gpart add -a 1m -s ${IMGSIZEM}m -t freebsd-ufs ${md}s1
	mdp=${md}s1a

	echo "===> Formatting this memory disk using UFS"
	newfs -S $XIGMANAS_IMGFMT_SECTOR -b $XIGMANAS_IMGFMT_BSIZE -f $XIGMANAS_IMGFMT_FSIZE -O2 -U -o space -m 0 -L "embboot" /dev/${mdp}
	echo "===> Mount this virtual disk on $XIGMANAS_TMPDIR"
	mount /dev/${mdp} $XIGMANAS_TMPDIR
	echo "===> Copying previously generated MFSROOT file to memory disk"
	cp $XIGMANAS_WORKINGDIR/mfsroot.gz $XIGMANAS_TMPDIR
	#cp $XIGMANAS_WORKINGDIR/mfsroot.uzip $XIGMANAS_TMPDIR
	cp $XIGMANAS_WORKINGDIR/mdlocal.xz $XIGMANAS_TMPDIR
	#cp $XIGMANAS_WORKINGDIR/mdlocal.uzip $XIGMANAS_TMPDIR
	echo "${XIGMANAS_PRODUCTNAME}-${PLATFORM}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}" > $XIGMANAS_TMPDIR/version

	echo "===> Copying Bootloader File(s) to memory disk"
	mkdir -p $XIGMANAS_TMPDIR/boot
	mkdir -p $XIGMANAS_TMPDIR/boot/kernel $XIGMANAS_TMPDIR/boot/defaults $XIGMANAS_TMPDIR/boot/zfs
	mkdir -p $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_ROOTFS/conf.default/config.xml $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_BOOTDIR/kernel/kernel.gz $XIGMANAS_TMPDIR/boot/kernel
	cp $XIGMANAS_BOOTDIR/kernel/*.ko $XIGMANAS_TMPDIR/boot/kernel
	cp $XIGMANAS_BOOTDIR/boot $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.conf $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.rc $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/support.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/defaults/loader.conf $XIGMANAS_TMPDIR/boot/defaults/
	cp $XIGMANAS_BOOTDIR/device.hints $XIGMANAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $XIGMANAS_SVNDIR/boot/menu.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_SVNDIR/boot/loader.efi $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/beastie.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menu.rc $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menusets.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/brand.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/check-password.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/color.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/delay.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/menu-commands.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/shortcuts.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/version.4th $XIGMANAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $XIGMANAS_SVNDIR/boot/splash.bmp $XIGMANAS_TMPDIR/boot
		install -v -o root -g wheel -m 555 ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	if [ "amd64" != ${XIGMANAS_ARCH} ]; then
		cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 apm/apm.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	# iSCSI driver
	install -v -o root -g wheel -m 555 ${XIGMANAS_ROOTFS}/boot/kernel/isboot.ko $XIGMANAS_TMPDIR/boot/kernel
	# preload kernel drivers
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 opensolaris/opensolaris.ko $XIGMANAS_TMPDIR/boot/kernel
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 zfs/zfs.ko $XIGMANAS_TMPDIR/boot/kernel
	# copy kernel modules
	copy_kmod

	# Mellanox ConnectX EN
	if [ "amd64" == ${XIGMANAS_ARCH} ]; then
		echo 'mlx4en_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	fi

	# Xen
	if [ "dom0" == ${XIGMANAS_XARCH} ]; then
		install -v -o root -g wheel -m 555 ${XIGMANAS_BOOTDIR}/xen ${XIGMANAS_TMPDIR}/boot
		install -v -o root -g wheel -m 644 ${XIGMANAS_BOOTDIR}/xen.4th ${XIGMANAS_TMPDIR}/boot
		kldxref -R ${XIGMANAS_TMPDIR}/boot
	fi

	echo "===> Unmount memory disk"
	umount $XIGMANAS_TMPDIR
	echo "===> Detach memory disk"
	mdconfig -d -u ${md}
	echo "===> Compress the IMG file"
	xz -${XIGMANAS_COMPLEVEL}v $XIGMANAS_WORKINGDIR/image.bin
	cp $XIGMANAS_WORKINGDIR/image.bin.xz $XIGMANAS_ROOTDIR/${IMGFILENAME}.xz

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	#[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/image.bin ] && rm -f $XIGMANAS_WORKINGDIR/image.bin

	return 0
}

create_iso () {
	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$XIGMANAS_ROOTFS" ]; then
		echo "==> Error: ${XIGMANAS_ROOTFS} does not exist!."
		return 1
	fi

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz

	if [ ! $TINY_ISO ]; then
		LABEL="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-LiveCD-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}"
		VOLUMEID="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-LiveCD-${XIGMANAS_VERSION}"
		echo "ISO: Generating the $XIGMANAS_PRODUCTNAME Image file:"
		create_image;
	else
		LABEL="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-LiveCD-Tin-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}"
		VOLUMEID="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-LiveCD-Tin-${XIGMANAS_VERSION}"
	fi

	# Set Platform Informations.
	PLATFORM="${XIGMANAS_XARCH}-liveCD"
	echo $PLATFORM > ${XIGMANAS_ROOTFS}/etc/platform

	# Set Revision.
	echo ${XIGMANAS_REVISION} > ${XIGMANAS_ROOTFS}/etc/prd.revision

	echo "ISO: Generating temporary folder '$XIGMANAS_TMPDIR'"
	mkdir $XIGMANAS_TMPDIR
	if [ $TINY_ISO ]; then
		# Not call create_image if TINY_ISO
		create_mfsroot;
	elif [ -z "$FORCE_MFSROOT" -o "$FORCE_MFSROOT" != "0" ]; then
		# Mount mfsroot/mdlocal created by create_image
		md=`mdconfig -a -t vnode -f $XIGMANAS_WORKINGDIR/mfsroot`
		mount /dev/${md} ${XIGMANAS_TMPDIR}
		# Update mfsroot/mdlocal
		echo $PLATFORM > ${XIGMANAS_TMPDIR}/etc/platform
		# Umount and update mfsroot/mdlocal
		umount $XIGMANAS_TMPDIR
		mdconfig -d -u ${md}
		update_mfsroot;
	else
		create_mfsroot;
	fi

	echo "ISO: Copying previously generated MFSROOT file to $XIGMANAS_TMPDIR"
	cp $XIGMANAS_WORKINGDIR/mfsroot.gz $XIGMANAS_TMPDIR
	#cp $XIGMANAS_WORKINGDIR/mfsroot.uzip $XIGMANAS_TMPDIR
	cp $XIGMANAS_WORKINGDIR/mdlocal.xz $XIGMANAS_TMPDIR
	cp $XIGMANAS_WORKINGDIR/mdlocal-mini.xz $XIGMANAS_TMPDIR
	echo "${LABEL}" > $XIGMANAS_TMPDIR/version

	echo "ISO: Copying Bootloader file(s) to $XIGMANAS_TMPDIR"
	mkdir -p $XIGMANAS_TMPDIR/boot
	mkdir -p $XIGMANAS_TMPDIR/boot/kernel $XIGMANAS_TMPDIR/boot/defaults $XIGMANAS_TMPDIR/boot/zfs
	cp $XIGMANAS_BOOTDIR/kernel/kernel.gz $XIGMANAS_TMPDIR/boot/kernel
	cp $XIGMANAS_BOOTDIR/kernel/*.ko $XIGMANAS_TMPDIR/boot/kernel
	cp $XIGMANAS_BOOTDIR/cdboot $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.conf $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.rc $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/support.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/defaults/loader.conf $XIGMANAS_TMPDIR/boot/defaults/
	cp $XIGMANAS_BOOTDIR/device.hints $XIGMANAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $XIGMANAS_SVNDIR/boot/menu.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_SVNDIR/boot/loader.efi $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/beastie.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menu.rc $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menusets.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/brand.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/check-password.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/color.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/delay.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/menu-commands.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/shortcuts.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/version.4th $XIGMANAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $XIGMANAS_SVNDIR/boot/splash.bmp $XIGMANAS_TMPDIR/boot
		install -v -o root -g wheel -m 555 ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	if [ "amd64" != ${XIGMANAS_ARCH} ]; then
		cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 apm/apm.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	# iSCSI driver
	install -v -o root -g wheel -m 555 ${XIGMANAS_ROOTFS}/boot/kernel/isboot.ko $XIGMANAS_TMPDIR/boot/kernel
	# preload kernel drivers
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 opensolaris/opensolaris.ko $XIGMANAS_TMPDIR/boot/kernel
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 zfs/zfs.ko $XIGMANAS_TMPDIR/boot/kernel
	# copy kernel modules
	copy_kmod

	# Mellanox ConnectX EN
	if [ "amd64" == ${XIGMANAS_ARCH} ]; then
		echo 'mlx4en_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	fi

	# Xen
	if [ "dom0" == ${XIGMANAS_XARCH} ]; then
		install -v -o root -g wheel -m 555 ${XIGMANAS_BOOTDIR}/xen ${XIGMANAS_TMPDIR}/boot
		install -v -o root -g wheel -m 644 ${XIGMANAS_BOOTDIR}/xen.4th ${XIGMANAS_TMPDIR}/boot
		kldxref -R ${XIGMANAS_TMPDIR}/boot
	fi

	if [ ! $TINY_ISO ]; then
		echo "ISO: Copying IMG file to $XIGMANAS_TMPDIR"
		cp ${XIGMANAS_WORKINGDIR}/image.bin.xz ${XIGMANAS_TMPDIR}/${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-embedded.xz
	fi

	echo "ISO: Generating ISO File"
	mkisofs -b "boot/cdboot" -no-emul-boot -r -J -A "${XIGMANAS_PRODUCTNAME} CD-ROM image" -publisher "${XIGMANAS_URL}" -V "${VOLUMEID}" -o "${XIGMANAS_ROOTDIR}/${LABEL}.iso" ${XIGMANAS_TMPDIR}
	[ 0 != $? ] && return 1 # successful?

	echo "Generating SHA512 CHECKSUM File"
	XIGMANAS_CHECKSUMFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.SHA512-CHECKSUM"
	cd ${XIGMANAS_ROOTDIR} && sha512 *.img.gz *.xz *.iso > ${XIGMANAS_ROOTDIR}/${XIGMANAS_CHECKSUMFILENAME}

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f $XIGMANAS_WORKINGDIR/mfsroot ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz
	[ -f $XIGMANAS_WORKINGDIR/image.bin.xz ] && rm -f $XIGMANAS_WORKINGDIR/image.bin.xz

	return 0
}

create_iso_tiny() {
	TINY_ISO=1
	create_iso;
	unset TINY_ISO
	return 0
}

create_embedded() {
	create_image;

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f $XIGMANAS_WORKINGDIR/mfsroot ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz
	[ -f $XIGMANAS_WORKINGDIR/image.bin.xz ] && rm -f $XIGMANAS_WORKINGDIR/image.bin.xz
}

create_usb () {
	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$XIGMANAS_ROOTFS" ]; then
		echo "==> Error: ${XIGMANAS_ROOTFS} does not exist!."
		return 1
	fi

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f ${XIGMANAS_WORKINGDIR}/image.bin ] && rm -f ${XIGMANAS_WORKINGDIR}/image.bin
	[ -f ${XIGMANAS_WORKINGDIR}/image.bin.xz ] && rm -f ${XIGMANAS_WORKINGDIR}/image.bin.xz
	[ -f ${XIGMANAS_WORKINGDIR}/mfsroot.gz ] && rm -f ${XIGMANAS_WORKINGDIR}/mfsroot.gz
	[ -f ${XIGMANAS_WORKINGDIR}/mfsroot.uzip ] && rm -f ${XIGMANAS_WORKINGDIR}/mfsroot.uzip
	[ -f ${XIGMANAS_WORKINGDIR}/mdlocal.xz ] && rm -f ${XIGMANAS_WORKINGDIR}/mdlocal.xz
	[ -f ${XIGMANAS_WORKINGDIR}/mdlocal.uzip ] && rm -f ${XIGMANAS_WORKINGDIR}/mdlocal.uzip
	[ -f ${XIGMANAS_WORKINGDIR}/mdlocal-mini.xz ] && rm -f ${XIGMANAS_WORKINGDIR}/mdlocal-mini.xz
	[ -f ${XIGMANAS_WORKINGDIR}/usb-image.bin ] && rm -f ${XIGMANAS_WORKINGDIR}/usb-image.bin
	[ -f ${XIGMANAS_WORKINGDIR}/usb-image.bin.gz ] && rm -f ${XIGMANAS_WORKINGDIR}/usb-image.bin.gz

	echo "USB: Generating the $XIGMANAS_PRODUCTNAME Image file:"
	create_image;

	# Set Platform Informations.
	PLATFORM="${XIGMANAS_XARCH}-liveUSB"
	echo $PLATFORM > ${XIGMANAS_ROOTFS}/etc/platform

	# Set Revision.
	echo ${XIGMANAS_REVISION} > ${XIGMANAS_ROOTFS}/etc/prd.revision

	IMGFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-LiveUSB-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.img"

	echo "USB: Generating temporary folder '$XIGMANAS_TMPDIR'"
	mkdir $XIGMANAS_TMPDIR
	if [ -z "$FORCE_MFSROOT" -o "$FORCE_MFSROOT" != "0" ]; then
		# Mount mfsroot/mdlocal created by create_image
		md=`mdconfig -a -t vnode -f $XIGMANAS_WORKINGDIR/mfsroot`
		mount /dev/${md} ${XIGMANAS_TMPDIR}
		# Update mfsroot/mdlocal
		echo $PLATFORM > ${XIGMANAS_TMPDIR}/etc/platform
		# Umount and update mfsroot/mdlocal
		umount $XIGMANAS_TMPDIR
		mdconfig -d -u ${md}
		update_mfsroot;
	else
		create_mfsroot;
	fi

	# for 1GB USB stick
	IMGSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/image.bin.xz)
	MFSSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mfsroot.gz)
	#MFS2SIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mfsroot.uzip)
	MDLSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mdlocal.xz)
	MDLSIZE2=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mdlocal-mini.xz)
	#IMGSIZEM=$(expr \( $IMGSIZE + $MFSSIZE + $MFS2SIZE + $MDLSIZE + $MDLSIZE2 - 1 + 1024 \* 1024 \) / 1024 / 1024)
	IMGSIZEM=$(expr \( $IMGSIZE + $MFSSIZE + $MDLSIZE + $MDLSIZE2 - 1 + 1024 \* 1024 \) / 1024 / 1024)
	USBROOTM=480
	USBSWAPM=512
	USBDATAM=12
	#USB_SECTS=64
	#USB_HEADS=32
	USB_SECTS=63
	USB_HEADS=255

	# 4MB alignment
	#USBSYSSIZEM=$(expr $USBROOTM + $IMGSIZEM + 4)
	USBSYSSIZEM=$(expr $USBROOTM + 4)
	USBSWPSIZEM=$(expr $USBSWAPM + 4)
	USBDATSIZEM=$(expr $USBDATAM + 4)
	USBIMGSIZEM=$(expr $USBSYSSIZEM + $USBSWPSIZEM + $USBDATSIZEM + 1)

	# 4MB aligned USB stick
	echo "USB: Creating Empty IMG File"
	#dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/usb-image.bin bs=1m count=${USBIMGSIZEM}
	dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/usb-image.bin bs=1m seek=${USBIMGSIZEM} count=0
	echo "USB: Use IMG as a memory disk"
	md=`mdconfig -a -t vnode -f ${XIGMANAS_WORKINGDIR}/usb-image.bin -x ${USB_SECTS} -y ${USB_HEADS}`
	diskinfo -v ${md}

	echo "USB: Creating BSD partition on this memory disk"
	#gpart create -s bsd ${md}
	#gpart bootcode -b ${XIGMANAS_BOOTDIR}/boot ${md}
	#gpart add -s ${USBSYSSIZEM}m -t freebsd-ufs ${md}
	#gpart add -s ${USBSWAPM}m -t freebsd-swap ${md}
	#gpart add -s ${USBDATSIZEM}m -t freebsd-ufs ${md}
	#mdp=${md}a

	#gpart create -s mbr ${md}
	#gpart add -i 4 -t freebsd ${md}
	#gpart set -a active -i 4 ${md}
	#gpart bootcode -b ${XIGMANAS_BOOTDIR}/mbr ${md}
	#mdp=${md}s4
	#gpart create -s bsd ${mdp}
	#gpart bootcode -b ${XIGMANAS_BOOTDIR}/boot ${mdp}
	#gpart add -a 1m -s ${USBSYSSIZEM}m -t freebsd-ufs ${mdp}
	#gpart add -a 1m -s ${USBSWAPM}m -t freebsd-swap ${mdp}
	#gpart add -a 1m -s ${USBDATSIZEM}m -t freebsd-ufs ${mdp}
	#mdp=${mdp}a

	gpart create -s mbr ${md}
	gpart add -s ${USBSYSSIZEM}m -t freebsd ${md}
	gpart add -s ${USBSWPSIZEM}m -t freebsd ${md}
	gpart add -s ${USBDATSIZEM}m -t freebsd ${md}
	gpart set -a active -i 1 ${md}
	gpart bootcode -b ${XIGMANAS_BOOTDIR}/mbr ${md}

	# s1 (UFS/SYSTEM)
	gpart create -s bsd ${md}s1
	gpart bootcode -b ${XIGMANAS_BOOTDIR}/boot ${md}s1
	gpart add -a 4m -s ${USBROOTM}m -t freebsd-ufs ${md}s1
	# s2 (SWAP)
	gpart create -s bsd ${md}s2
	gpart add -i2 -a 4m -s ${USBSWAPM}m -t freebsd-swap ${md}s2
	# s3 (UFS/DATA) dummy
	gpart create -s bsd ${md}s3
	gpart add -a 4m -s ${USBDATAM}m -t freebsd-ufs ${md}s3
	# SYSTEM partition
	mdp=${md}s1a

	echo "USB: Formatting this memory disk using UFS"
	#newfs -S 512 -b 32768 -f 4096 -O2 -U -j -o time -m 8 -L "liveboot" /dev/${mdp}
	#newfs -S $XIGMANAS_IMGFMT_SECTOR -b $XIGMANAS_IMGFMT_BSIZE -f $XIGMANAS_IMGFMT_FSIZE -O2 -U -o space -m 0 -L "liveboot" /dev/${mdp}
	newfs -S 4096 -b 32768 -f 4096 -O2 -U -j -o space -m 0 -L "liveboot" /dev/${mdp}

	echo "USB: Mount this virtual disk on $XIGMANAS_TMPDIR"
	mount /dev/${mdp} $XIGMANAS_TMPDIR

	#echo "USB: Creating swap file on the memory disk"
	#dd if=/dev/zero of=$XIGMANAS_TMPDIR/swap.dat bs=1m seek=${USBSWAPM} count=0

	echo "USB: Copying previously generated MFSROOT file to memory disk"
	cp $XIGMANAS_WORKINGDIR/mfsroot.gz $XIGMANAS_TMPDIR
	#cp $XIGMANAS_WORKINGDIR/mfsroot.uzip $XIGMANAS_TMPDIR
	cp $XIGMANAS_WORKINGDIR/mdlocal.xz $XIGMANAS_TMPDIR
	cp $XIGMANAS_WORKINGDIR/mdlocal-mini.xz $XIGMANAS_TMPDIR
	echo "${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-LiveUSB-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}" > $XIGMANAS_TMPDIR/version

	echo "USB: Copying Bootloader File(s) to memory disk"
	mkdir -p $XIGMANAS_TMPDIR/boot
	mkdir -p $XIGMANAS_TMPDIR/boot/kernel $XIGMANAS_TMPDIR/boot/defaults $XIGMANAS_TMPDIR/boot/zfs
	mkdir -p $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_ROOTFS/conf.default/config.xml $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_BOOTDIR/kernel/kernel.gz $XIGMANAS_TMPDIR/boot/kernel
	cp $XIGMANAS_BOOTDIR/kernel/*.ko $XIGMANAS_TMPDIR/boot/kernel
	cp $XIGMANAS_BOOTDIR/boot $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.conf $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.rc $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/support.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/defaults/loader.conf $XIGMANAS_TMPDIR/boot/defaults/
	cp $XIGMANAS_BOOTDIR/device.hints $XIGMANAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $XIGMANAS_SVNDIR/boot/menu.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_SVNDIR/boot/loader.efi $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/beastie.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menu.rc $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menusets.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/brand.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/check-password.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/color.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/delay.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/menu-commands.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/shortcuts.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/version.4th $XIGMANAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $XIGMANAS_SVNDIR/boot/splash.bmp $XIGMANAS_TMPDIR/boot
		install -v -o root -g wheel -m 555 ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	if [ "amd64" != ${XIGMANAS_ARCH} ]; then
		cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 apm/apm.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	# iSCSI driver
	install -v -o root -g wheel -m 555 ${XIGMANAS_ROOTFS}/boot/kernel/isboot.ko $XIGMANAS_TMPDIR/boot/kernel
	# preload kernel drivers
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 opensolaris/opensolaris.ko $XIGMANAS_TMPDIR/boot/kernel
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 zfs/zfs.ko $XIGMANAS_TMPDIR/boot/kernel
	# copy kernel modules
	copy_kmod

	# Mellanox ConnectX EN
	if [ "amd64" == ${XIGMANAS_ARCH} ]; then
		echo 'mlx4en_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	fi

	# Xen
	if [ "dom0" == ${XIGMANAS_XARCH} ]; then
		install -v -o root -g wheel -m 555 ${XIGMANAS_BOOTDIR}/xen ${XIGMANAS_TMPDIR}/boot
		install -v -o root -g wheel -m 644 ${XIGMANAS_BOOTDIR}/xen.4th ${XIGMANAS_TMPDIR}/boot
		kldxref -R ${XIGMANAS_TMPDIR}/boot
	fi

	echo "USB: Copying IMG file to $XIGMANAS_TMPDIR"
	cp ${XIGMANAS_WORKINGDIR}/image.bin.xz ${XIGMANAS_TMPDIR}/${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-embedded.xz

	echo "USB: Unmount memory disk"
	umount $XIGMANAS_TMPDIR
	echo "USB: Detach memory disk"
	mdconfig -d -u ${md}
	cp $XIGMANAS_WORKINGDIR/usb-image.bin $XIGMANAS_ROOTDIR/$IMGFILENAME
	echo "Compress LiveUSB.img to LiveUSB.img.gz"
	gzip -9n $XIGMANAS_ROOTDIR/$IMGFILENAME

	echo "Generating SHA512 CHECKSUM File"
	XIGMANAS_CHECKSUMFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.SHA512-CHECKSUM"
	cd ${XIGMANAS_ROOTDIR} && sha512 *.img.gz *.xz > ${XIGMANAS_ROOTDIR}/${XIGMANAS_CHECKSUMFILENAME}

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f $XIGMANAS_WORKINGDIR/mfsroot ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz
	[ -f $XIGMANAS_WORKINGDIR/image.bin.xz ] && rm -f $XIGMANAS_WORKINGDIR/image.bin.xz
	[ -f $XIGMANAS_WORKINGDIR/usb-image.bin ] && rm -f $XIGMANAS_WORKINGDIR/usb-image.bin

	return 0
}

create_full() {
	[ -d $XIGMANAS_SVNDIR ] && use_svn ;

	echo "FULL: Generating $XIGMANAS_PRODUCTNAME tgz update file"

	# Set platform information.
	PLATFORM="${XIGMANAS_XARCH}-full"
	echo $PLATFORM > ${XIGMANAS_ROOTFS}/etc/platform

	# Set Revision.
	echo ${XIGMANAS_REVISION} > ${XIGMANAS_ROOTFS}/etc/prd.revision

	FULLFILENAME="${XIGMANAS_PRODUCTNAME}-${PLATFORM}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.tgz"

	echo "FULL: Generating tempory $XIGMANAS_TMPDIR folder"
	#Clean TMP dir:
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	mkdir $XIGMANAS_TMPDIR

	#Copying all XIGMANAS rootfilesystem (including symlink) on this folder
	cd $XIGMANAS_TMPDIR
	tar -cf - -C $XIGMANAS_ROOTFS ./ | tar -xvpf -
	#tar -cf - -C $XIGMANAS_ROOTFS ./ | tar -xvpf - -C $XIGMANAS_TMPDIR
	echo "${XIGMANAS_PRODUCTNAME}-${PLATFORM}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}" > $XIGMANAS_TMPDIR/version

	echo "Copying bootloader file(s) to root filesystem"
	mkdir -p $XIGMANAS_TMPDIR/boot/kernel $XIGMANAS_TMPDIR/boot/defaults $XIGMANAS_TMPDIR/boot/zfs
	#mkdir $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_ROOTFS/conf.default/config.xml $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_BOOTDIR/kernel/kernel.gz $XIGMANAS_TMPDIR/boot/kernel
	gunzip $XIGMANAS_TMPDIR/boot/kernel/kernel.gz
	cp $XIGMANAS_BOOTDIR/boot $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.rc $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/support.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/defaults/loader.conf $XIGMANAS_TMPDIR/boot/defaults/
	cp $XIGMANAS_BOOTDIR/device.hints $XIGMANAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $XIGMANAS_SVNDIR/boot/menu.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_SVNDIR/boot/loader.efi $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/beastie.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menu.rc $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/boot/menusets.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/brand.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/check-password.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/color.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/delay.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/menu-commands.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/shortcuts.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/version.4th $XIGMANAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $XIGMANAS_SVNDIR/boot/splash.bmp $XIGMANAS_TMPDIR/boot
		cp ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	if [ "amd64" != ${XIGMANAS_ARCH} ]; then
		cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && cp apm/apm.ko $XIGMANAS_TMPDIR/boot/kernel
	fi
	# iSCSI driver
	install -v -o root -g wheel -m 555 ${XIGMANAS_ROOTFS}/boot/kernel/isboot.ko $XIGMANAS_TMPDIR/boot/kernel
	# preload kernel drivers
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 opensolaris/opensolaris.ko $XIGMANAS_TMPDIR/boot/kernel
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 zfs/zfs.ko $XIGMANAS_TMPDIR/boot/kernel
	# copy kernel modules
	copy_kmod

	#Generate a loader.conf for full mode:
	echo 'kernel="kernel"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'bootfile="kernel"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'kernel_options=""' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'hw.est.msr_info="0"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'hw.hptrr.attach_generic="0"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'hw.msk.msi_disable="1"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'kern.maxfiles="6289573"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'kern.cam.boot_delay="12000"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'kern.geom.label.disk_ident.enable="0"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'kern.geom.label.gptid.enable="0"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'hint.acpi_throttle.0.disabled="0"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'hint.p4tcc.0.disabled="0"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	#echo 'splash_bmp_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	#echo 'bitmap_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	#echo 'bitmap_name="/boot/splash.bmp"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'autoboot_delay="3"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'isboot_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	echo 'zfs_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf

	# Mellanox ConnectX EN
	if [ "amd64" == ${XIGMANAS_ARCH} ]; then
		echo 'mlx4en_load="YES"' >> $XIGMANAS_TMPDIR/boot/loader.conf
	fi

	# Xen
	if [ "dom0" == ${XIGMANAS_XARCH} ]; then
		install -v -o root -g wheel -m 555 ${XIGMANAS_BOOTDIR}/xen ${XIGMANAS_TMPDIR}/boot
		install -v -o root -g wheel -m 644 ${XIGMANAS_BOOTDIR}/xen.4th ${XIGMANAS_TMPDIR}/boot
		kldxref -R ${XIGMANAS_TMPDIR}/boot
	fi

	#Check that there is no /etc/fstab file! This file can be generated only during install, and must be kept
	[ -f $XIGMANAS_TMPDIR/etc/fstab ] && rm -f $XIGMANAS_TMPDIR/etc/fstab

	#Check that there is no /etc/cfdevice file! This file can be generated only during install, and must be kept
	[ -f $XIGMANAS_TMPDIR/etc/cfdevice ] && rm -f $XIGMANAS_TMPDIR/etc/cfdevice

	echo "FULL: tgz the directory"
	cd $XIGMANAS_ROOTDIR
	tar cvfz $FULLFILENAME -C $XIGMANAS_TMPDIR ./

	# Cleanup.
	echo "Cleaning temp .o file(s)"
	if [ -f $XIGMANAS_TMPDIR/usr/lib/librt.so.1 ]; then
		chflags -R noschg $XIGMANAS_TMPDIR/usr/lib/*
	fi
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR

	echo "Generating SHA512 CHECKSUM File"
	XIGMANAS_CHECKSUMFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.SHA512-CHECKSUM"
	cd ${XIGMANAS_ROOTDIR} && sha512 *.img.gz *.xz *.iso *.tgz > ${XIGMANAS_ROOTDIR}/${XIGMANAS_CHECKSUMFILENAME}

	return 0
}

custom_rpi() {
	# RPI settings
	echo "kern.hz=100" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.pmap.sp_enabled=0" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.bcm2835.sdhci.hs=1" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.bcm2835.cpufreq.verbose=1" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.bcm2835.cpufreq.lowest_freq=400" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "vfs.zfs.arc_max=160m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.kmem_size=350m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.kmem_size_max=450m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "if_axe_load=YES" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#if_axge_load=YES" >>$XIGMANAS_TMPDIR/boot/loader.conf
}

custom_rpi2() {
	# RPI2 settings
	echo "kern.hz=100" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.pmap.sp_enabled=0" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.bcm2835.sdhci.hs=1" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.bcm2835.cpufreq.verbose=1" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#hw.bcm2835.cpufreq.lowest_freq=600" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#hw.bcm2835.cpufreq.highest_freq=900" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "vfs.zfs.arc_max=280m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.kmem_size=450m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.kmem_size_max=500m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "if_axe_load=YES" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#if_axge_load=YES" >>$XIGMANAS_TMPDIR/boot/loader.conf
}

custom_oc1() {
	# OC1 settings
	echo "kern.hz=100" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.pmap.sp_enabled=0" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#hw.m8b.sdhc.hs=1" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.m8b.sdhc.uhs=2" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.m8b.sdhc.hs200=1" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.m8b.sdhc.max_freq=200000000" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.m8b.cpufreq.verbose=1" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.m8b.cpufreq.lowest_freq=816" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "hw.m8b.cpufreq.highest_freq=1608" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "vfs.zfs.arc_max=550m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.kmem_size=750m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#vm.kmem_size_max=800m" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#if_axe_load=YES" >>$XIGMANAS_TMPDIR/boot/loader.conf
	echo "#if_axge_load=YES" >>$XIGMANAS_TMPDIR/boot/loader.conf
}

create_arm_image() {
	custom_cmd="$1"

	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$XIGMANAS_ROOTFS" ]; then
		echo "==> Error: ${XIGMANAS_ROOTFS} does not exist!."
		return 1
	fi

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f ${XIGMANAS_WORKINGDIR}/image.bin ] && rm -f ${XIGMANAS_WORKINGDIR}/image.bin
	[ -f ${XIGMANAS_WORKINGDIR}/image.bin.xz ] && rm -f ${XIGMANAS_WORKINGDIR}/image.bin.xz
	[ -f ${XIGMANAS_WORKINGDIR}/mfsroot.gz ] && rm -f ${XIGMANAS_WORKINGDIR}/mfsroot.gz
	[ -f ${XIGMANAS_WORKINGDIR}/mfsroot.uzip ] && rm -f ${XIGMANAS_WORKINGDIR}/mfsroot.uzip
	[ -f ${XIGMANAS_WORKINGDIR}/mdlocal.xz ] && rm -f ${XIGMANAS_WORKINGDIR}/mdlocal.xz
	[ -f ${XIGMANAS_WORKINGDIR}/mdlocal.uzip ] && rm -f ${XIGMANAS_WORKINGDIR}/mdlocal.uzip
	[ -f ${XIGMANAS_WORKINGDIR}/mdlocal-mini.xz ] && rm -f ${XIGMANAS_WORKINGDIR}/mdlocal-mini.xz

	# Set Platform Informations.
	PLATFORM="${XIGMANAS_XARCH}-embedded"
	echo $PLATFORM > ${XIGMANAS_ROOTFS}/etc/platform

	# Set build time.
	date > ${XIGMANAS_ROOTFS}/etc/prd.version.buildtime
	date "+%s" > ${XIGMANAS_ROOTFS}/etc/prd.version.buildtimestamp

	# Set Revision.
	echo ${XIGMANAS_REVISION} > ${XIGMANAS_ROOTFS}/etc/prd.revision

	IMGFILENAME="${XIGMANAS_PRODUCTNAME}-${PLATFORM}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.img"
	IMGSIZEM=320

	echo "ARM: Generating temporary folder '$XIGMANAS_TMPDIR'"
	mkdir $XIGMANAS_TMPDIR
	create_mfsroot;

	echo "ARM: Creating Empty IMG File"
	dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/image.bin bs=1m seek=${IMGSIZEM} count=0
	echo "ARM: Use IMG as a memory disk"
	md=`mdconfig -a -t vnode -f ${XIGMANAS_WORKINGDIR}/image.bin`
	diskinfo -v ${md}

	echo "ARM: Formatting this memory disk using UFS"
	newfs -S 4096 -b 32768 -f 4096 -O2 -U -j -o space -m 0 -L "embboot" /dev/${md}

	echo "ARM: Mount this virtual disk on $XIGMANAS_TMPDIR"
	mount /dev/${md} $XIGMANAS_TMPDIR

	echo "ARM: Copying previously generated MFSROOT file to memory disk"
	#cp $XIGMANAS_WORKINGDIR/mfsroot.gz $XIGMANAS_TMPDIR
	cp $XIGMANAS_WORKINGDIR/mfsroot.uzip $XIGMANAS_TMPDIR
	#cp $XIGMANAS_WORKINGDIR/mdlocal.xz $XIGMANAS_TMPDIR
	cp $XIGMANAS_WORKINGDIR/mdlocal.uzip $XIGMANAS_TMPDIR
	echo "${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-embedded-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}" > $XIGMANAS_TMPDIR/version

	echo "ARM: Copying Bootloader File(s) to memory disk"
	mkdir -p $XIGMANAS_TMPDIR/boot
	mkdir -p $XIGMANAS_TMPDIR/boot/kernel $XIGMANAS_TMPDIR/boot/defaults $XIGMANAS_TMPDIR/boot/zfs
	mkdir -p $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_ROOTFS/conf.default/config.xml $XIGMANAS_TMPDIR/conf
	cp $XIGMANAS_BOOTDIR/kernel/kernel.gz $XIGMANAS_TMPDIR/boot/kernel
	# ARM use uncompressed kernel
	#gunzip $XIGMANAS_TMPDIR/mfsroot.gz 
	gunzip $XIGMANAS_TMPDIR/boot/kernel/kernel.gz
	cp $XIGMANAS_BOOTDIR/kernel/*.ko $XIGMANAS_TMPDIR/boot/kernel
	#cp $XIGMANAS_BOOTDIR/boot $XIGMANAS_TMPDIR/boot
	#cp $XIGMANAS_BOOTDIR/loader $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.conf $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.rc $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/loader.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/support.4th $XIGMANAS_TMPDIR/boot
	cp $XIGMANAS_BOOTDIR/defaults/loader.conf $XIGMANAS_TMPDIR/boot/defaults/
	#cp $XIGMANAS_BOOTDIR/device.hints $XIGMANAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $XIGMANAS_SVNDIR/boot/menu.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		#cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/brand.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/check-password.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/color.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/delay.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/frames.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/menu-commands.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/screen.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/shortcuts.4th $XIGMANAS_TMPDIR/boot
		cp $XIGMANAS_BOOTDIR/version.4th $XIGMANAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $XIGMANAS_SVNDIR/boot/splash.bmp $XIGMANAS_TMPDIR/boot
		install -v -o root -g wheel -m 555 ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $XIGMANAS_TMPDIR/boot/kernel
	fi

	# iSCSI driver
	#install -v -o root -g wheel -m 555 ${XIGMANAS_ROOTFS}/boot/kernel/isboot.ko $XIGMANAS_TMPDIR/boot/kernel
	# preload kernel drivers
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 opensolaris/opensolaris.ko $XIGMANAS_TMPDIR/boot/kernel
	cd ${XIGMANAS_OBJDIRPREFIX}/usr/src/sys/${XIGMANAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 zfs/zfs.ko $XIGMANAS_TMPDIR/boot/kernel
	# copy kernel modules
	copy_kmod

	# copy boot-update
	if [ -f ${XIGMANAS_WORKINGDIR}/boot-update.tar.xz ]; then
		cp -p ${XIGMANAS_WORKINGDIR}/boot-update.tar.xz ${XIGMANAS_TMPDIR}
	fi

	# Platform customize
	if [ -n "$custom_cmd" ]; then
		eval "$custom_cmd"
	fi

	echo "ARM: Unmount memory disk"
	umount $XIGMANAS_TMPDIR
	echo "ARM: Detach memory disk"
	mdconfig -d -u ${md}
	echo "ARM: Compress the IMG file"
	xz -${XIGMANAS_COMPLEVEL}v $XIGMANAS_WORKINGDIR/image.bin
	cp $XIGMANAS_WORKINGDIR/image.bin.xz $XIGMANAS_ROOTDIR/${IMGFILENAME}.xz

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	#[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	#[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	#[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	#[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	#[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz
	#[ -f $XIGMANAS_WORKINGDIR/image.bin.xz ] && rm -f $XIGMANAS_WORKINGDIR/image.bin.xz

	return 0
}

create_rpisd() {
	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$XIGMANAS_ROOTFS" ]; then
		echo "==> Error: ${XIGMANAS_ROOTFS} does not exist!."
		return 1
	fi

	# Prepare boot files
	RPI_BOOTFILES=${XIGMANAS_SVNDIR}/build/arm/boot-rpi.tar.xz
	RPI_BOOTDIR=${XIGMANAS_WORKINGDIR}/boot
	rm -rf ${RPI_BOOTDIR} ${XIGMANAS_WORKINGDIR}/boot-update.tar.xz
	tar -C ${XIGMANAS_WORKINGDIR} -Jxvf ${RPI_BOOTFILES}

	# Create boot-update
	tar -C ${RPI_BOOTDIR} -Jcvf ${XIGMANAS_WORKINGDIR}/boot-update.tar.xz \
	    bootversion bootcode.bin config.txt fixup.dat fixup_cd.dat fixup_x.dat \
	    rpi.dtb start.elf start_cd.elf start_x.elf uboot.img uenv.txt ubldr

	# Create embedded image
	create_arm_image custom_rpi;

	[ -f ${XIGMANAS_WORKINGDIR}/sd-image.bin ] && rm -f ${XIGMANAS_WORKINGDIR}/sd-image.bin
	[ -f ${XIGMANAS_WORKINGDIR}/sd-image.bin.gz ] && rm -f ${XIGMANAS_WORKINGDIR}/sd-image.bin.gz
	mkdir -p ${XIGMANAS_TMPDIR}
	mkdir -p ${XIGMANAS_TMPDIR}/usr/local

	IMGFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-SD-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.img"
	FIRMWARENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-embedded-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.img"

	# for 1GB SD card
	IMGSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/image.bin.xz)
	MFSSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mfsroot.uzip)
	MDLSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mdlocal.xz)
	IMGSIZEM=$(expr \( $IMGSIZE + $MFSSIZE + $MDLSIZE - 1 + 1024 \* 1024 \) / 1024 / 1024)
	SDROOTM=320
	SDSWAPM=512
	SDDATAM=12

	SDFATSIZEM=19
	# 4MB alignment
	#SDSYSSIZEM=$(expr $SDROOTM + $IMGSIZEM + 4)
	SDSYSSIZEM=$(expr $SDROOTM + 4)
	SDIMGSIZEM=$(expr $SDFATSIZEM + 4 + $SDSYSSIZEM + $SDSWAPM + 4)
	SDSWPSIZEM=$(expr $SDSWAPM + 4)
	SDDATSIZEM=$(expr $SDDATAM + 4)

	#SDIMGSIZE=1802240
	SDIMGSIZE=$(expr 8192 \* 20 \* 11)

	# 4MB aligned SD card
	echo "RPISD: Creating Empty IMG File"
	#dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/sd-image.bin bs=1m seek=${SDIMGSIZEM} count=0
	dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/sd-image.bin bs=512 seek=${SDIMGSIZE} count=0
	echo "RPISD: Use IMG as a memory disk"
	md=`mdconfig -a -t vnode -f ${XIGMANAS_WORKINGDIR}/sd-image.bin`
	diskinfo -v ${md}

	echo "RPISD: Creating BSD partition on this memory disk"
	gpart create -s mbr ${md}
	gpart add -b 63 -s ${SDFATSIZEM}m -t '!12' ${md}
	gpart add -s ${SDSWPSIZEM}m -t freebsd ${md}
	gpart add -s ${SDSYSSIZEM}m -t freebsd ${md}
	gpart add -s ${SDDATSIZEM}m -t freebsd ${md}
	gpart set -a active -i 1 ${md}

	# mmcsd0s1 (FAT16)
	newfs_msdos -L "BOOT" -F 16 ${md}s1
	mount -t msdosfs /dev/${md}s1 ${XIGMANAS_TMPDIR}

	# Install boot files
	for f in bootcode.bin config.txt fixup.dat fixup_cd.dat fixup_x.dat rpi.dtb \
	    start.elf start_cd.elf start_x.elf uboot.img uenv.txt; do
		cp -p ${RPI_BOOTDIR}/$f ${XIGMANAS_TMPDIR}
	done

	# Install bootversion/ubldr
	cp -p ${RPI_BOOTDIR}/bootversion ${XIGMANAS_TMPDIR}
	cp -p ${RPI_BOOTDIR}/ubldr ${XIGMANAS_TMPDIR}

	sync
	cd ${XIGMANAS_WORKINGDIR}
	umount ${XIGMANAS_TMPDIR}
	rm -rf ${RPI_BOOTDIR}

	# mmcsd0s2 (SWAP)
	gpart create -s bsd ${md}s2
	gpart add -i2 -a 4m -s ${SDSWAPM}m -t freebsd-swap ${md}s2

	# mmcsd0s3 (UFS/SYSTEM)
	gpart create -s bsd ${md}s3
	gpart add -a 4m -s ${SDROOTM}m -t freebsd-ufs ${md}s3

	# mmcsd0s4 (UFS/DATA)
	gpart create -s bsd ${md}s4
	gpart add -a 4m -s ${SDDATAM}m -t freebsd-ufs ${md}s4

	# SYSTEM partition
	mdp=${md}s3a

	#echo "RPISD: Formatting this memory disk using UFS"
	#newfs -S 4096 -b 32768 -f 4096 -O2 -U -j -o space -m 0 -L "embboot" /dev/${mdp}
	echo "RPISD: Installing embedded image"
	xz -dcv ${XIGMANAS_ROOTDIR}/${FIRMWARENAME}.xz | dd of=/dev/${mdp} bs=1m status=none

	echo "RPISD: Mount this virtual disk on $XIGMANAS_TMPDIR"
	mount /dev/${mdp} $XIGMANAS_TMPDIR

	# Enable auto resize
	touch ${XIGMANAS_TMPDIR}/req_resize

	echo "RPISD: Unmount memory disk"
	umount $XIGMANAS_TMPDIR
	echo "RPISD: Detach memory disk"
	mdconfig -d -u ${md}
	echo "RPISD: Copy SD image"
	cp $XIGMANAS_WORKINGDIR/sd-image.bin $XIGMANAS_ROOTDIR/${IMGFILENAME}

	echo "Generating SHA512 CHECKSUM File"
	XIGMANAS_CHECKSUMFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.SHA512-CHECKSUM"
	cd ${XIGMANAS_ROOTDIR} && sha512 *.img *.xz *.iso > ${XIGMANAS_ROOTDIR}/${XIGMANAS_CHECKSUMFILENAME}

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f $XIGMANAS_WORKINGDIR/mfsroot ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz
	[ -f $XIGMANAS_WORKINGDIR/image.bin.xz ] && rm -f $XIGMANAS_WORKINGDIR/image.bin.xz
	[ -f $XIGMANAS_WORKINGDIR/sd-image.bin ] && rm -f $XIGMANAS_WORKINGDIR/sd-image.bin

	return 0
}

create_rpi2sd() {
	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$XIGMANAS_ROOTFS" ]; then
		echo "==> Error: ${XIGMANAS_ROOTFS} does not exist!."
		return 1
	fi

	# Prepare boot files
	RPI_BOOTFILES=${XIGMANAS_SVNDIR}/build/arm/boot-rpi2.tar.xz
	RPI_BOOTDIR=${XIGMANAS_WORKINGDIR}/boot
	rm -rf ${RPI_BOOTDIR} ${XIGMANAS_WORKINGDIR}/boot-update.tar.xz
	tar -C ${XIGMANAS_WORKINGDIR} -Jxvf ${RPI_BOOTFILES}

	# Create boot-update
	tar -C ${RPI_BOOTDIR} -Jcvf ${XIGMANAS_WORKINGDIR}/boot-update.tar.xz \
	    bootversion bootcode.bin config.txt fixup.dat fixup_cd.dat fixup_x.dat \
	    rpi2.dtb start.elf start_cd.elf start_x.elf u-boot.bin ubldr uboot.env

	# Create embedded image
	create_arm_image custom_rpi2;

	[ -f ${XIGMANAS_WORKINGDIR}/sd-image.bin ] && rm -f ${XIGMANAS_WORKINGDIR}/sd-image.bin
	[ -f ${XIGMANAS_WORKINGDIR}/sd-image.bin.gz ] && rm -f ${XIGMANAS_WORKINGDIR}/sd-image.bin.gz
	mkdir -p ${XIGMANAS_TMPDIR}
	mkdir -p ${XIGMANAS_TMPDIR}/usr/local

	IMGFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-SD-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.img"
	FIRMWARENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-embedded-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.img"

	# for 2GB SD card
	IMGSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/image.bin.xz)
	MFSSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mfsroot.uzip)
	MDLSIZE=$(stat -f "%z" ${XIGMANAS_WORKINGDIR}/mdlocal.xz)
	IMGSIZEM=$(expr \( $IMGSIZE + $MFSSIZE + $MDLSIZE - 1 + 1024 \* 1024 \) / 1024 / 1024)
	SDROOTM=320
	SDSWAPM=1024
	SDDATAM=12

	SDFATSIZEM=19
	# 4MB alignment
	#SDSYSSIZEM=$(expr $SDROOTM + $IMGSIZEM + 4)
	SDSYSSIZEM=$(expr $SDROOTM + 4)
	SDIMGSIZEM=$(expr $SDFATSIZEM + 4 + $SDSYSSIZEM + $SDSWAPM + 4)
	SDSWPSIZEM=$(expr $SDSWAPM + 4)
	SDDATSIZEM=$(expr $SDDATAM + 4)

	#SDIMGSIZE=3768320
	SDIMGSIZE=$(expr 8192 \* 20 \* 18)

	# 4MB aligned SD card
	echo "RPISD: Creating Empty IMG File"
	dd if=/dev/zero of=${XIGMANAS_WORKINGDIR}/sd-image.bin bs=512 seek=${SDIMGSIZE} count=0
	echo "RPISD: Use IMG as a memory disk"
	md=`mdconfig -a -t vnode -f ${XIGMANAS_WORKINGDIR}/sd-image.bin`
	diskinfo -v ${md}

	echo "RPISD: Creating BSD partition on this memory disk"
	gpart create -s mbr ${md}
	gpart add -b 63 -s ${SDFATSIZEM}m -t '!12' ${md}
	gpart add -s ${SDSWPSIZEM}m -t freebsd ${md}
	gpart add -s ${SDSYSSIZEM}m -t freebsd ${md}
	gpart add -s ${SDDATSIZEM}m -t freebsd ${md}
	gpart set -a active -i 1 ${md}

	# mmcsd0s1 (FAT16)
	newfs_msdos -L "BOOT" -F 16 ${md}s1
	mount -t msdosfs /dev/${md}s1 ${XIGMANAS_TMPDIR}

	# Install boot files
	for f in bootcode.bin config.txt fixup.dat fixup_cd.dat fixup_x.dat rpi2.dtb \
	    start.elf start_cd.elf start_x.elf u-boot.bin uboot.env; do
		cp -p ${RPI_BOOTDIR}/$f ${XIGMANAS_TMPDIR}
	done

	# Install bootversion/ubldr
	cp -p ${RPI_BOOTDIR}/bootversion ${XIGMANAS_TMPDIR}
	cp -p ${RPI_BOOTDIR}/ubldr ${XIGMANAS_TMPDIR}

	sync
	cd ${XIGMANAS_WORKINGDIR}
	umount ${XIGMANAS_TMPDIR}
	rm -rf ${RPI_BOOTDIR}

	# mmcsd0s2 (SWAP)
	gpart create -s bsd ${md}s2
	gpart add -i2 -a 4m -s ${SDSWAPM}m -t freebsd-swap ${md}s2

	# mmcsd0s3 (UFS/SYSTEM)
	gpart create -s bsd ${md}s3
	gpart add -a 4m -s ${SDROOTM}m -t freebsd-ufs ${md}s3

	# mmcsd0s4 (UFS/DATA)
	gpart create -s bsd ${md}s4
	gpart add -a 4m -s ${SDDATAM}m -t freebsd-ufs ${md}s4

	# SYSTEM partition
	mdp=${md}s3a

	#echo "RPISD: Formatting this memory disk using UFS"
	#newfs -S 4096 -b 32768 -f 4096 -O2 -U -j -o space -m 0 -L "embboot" /dev/${mdp}
	echo "RPISD: Installing embedded image"
	xz -dcv ${XIGMANAS_ROOTDIR}/${FIRMWARENAME}.xz | dd of=/dev/${mdp} bs=1m status=none

	echo "RPISD: Mount this virtual disk on $XIGMANAS_TMPDIR"
	mount /dev/${mdp} $XIGMANAS_TMPDIR

	# Enable auto resize
	touch ${XIGMANAS_TMPDIR}/req_resize

	echo "RPISD: Unmount memory disk"
	umount $XIGMANAS_TMPDIR
	echo "RPISD: Detach memory disk"
	mdconfig -d -u ${md}
	echo "RPISD: Copy SD image"
	cp $XIGMANAS_WORKINGDIR/sd-image.bin $XIGMANAS_ROOTDIR/${IMGFILENAME}

	echo "Generating SHA512 CHECKSUM File"
	XIGMANAS_CHECKSUMFILENAME="${XIGMANAS_PRODUCTNAME}-${XIGMANAS_XARCH}-${XIGMANAS_VERSION}.${XIGMANAS_REVISION}.SHA512-CHECKSUM"
	cd ${XIGMANAS_ROOTDIR} && sha512 *.img *.xz *.iso > ${XIGMANAS_ROOTDIR}/${XIGMANAS_CHECKSUMFILENAME}

	# Cleanup.
	[ -d $XIGMANAS_TMPDIR ] && rm -rf $XIGMANAS_TMPDIR
	[ -f $XIGMANAS_WORKINGDIR/mfsroot ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.gz ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.gz
	[ -f $XIGMANAS_WORKINGDIR/mfsroot.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mfsroot.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.xz
	[ -f $XIGMANAS_WORKINGDIR/mdlocal.uzip ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal.uzip
	[ -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz ] && rm -f $XIGMANAS_WORKINGDIR/mdlocal-mini.xz
	[ -f $XIGMANAS_WORKINGDIR/image.bin.xz ] && rm -f $XIGMANAS_WORKINGDIR/image.bin.xz
	[ -f $XIGMANAS_WORKINGDIR/sd-image.bin ] && rm -f $XIGMANAS_WORKINGDIR/sd-image.bin

	return 0
}

# Update Subversion Sources.
update_svn() {
	# Update sources from repository.
	cd $XIGMANAS_ROOTDIR
	svn co $XIGMANAS_SVNURL svn

	# Update Revision Number.
	XIGMANAS_REVISION=$(svn info ${XIGMANAS_SVNDIR} | grep Revision | awk '{print $2}')

	return 0
}

use_svn() {
	echo "===> Replacing old code with SVN code"

	cd ${XIGMANAS_SVNDIR}/build && cp -pv CHANGES ${XIGMANAS_ROOTFS}/usr/local/www
	cd ${XIGMANAS_SVNDIR}/build/scripts && cp -pv carp-hast-switch ${XIGMANAS_ROOTFS}/usr/local/sbin
	cd ${XIGMANAS_SVNDIR}/build/scripts && cp -pv hastswitch ${XIGMANAS_ROOTFS}/usr/local/sbin
	cd ${XIGMANAS_SVNDIR}/root && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${XIGMANAS_ROOTFS}/root
	cd ${XIGMANAS_SVNDIR}/etc && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${XIGMANAS_ROOTFS}/etc
	cd ${XIGMANAS_SVNDIR}/www && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${XIGMANAS_ROOTFS}/usr/local/www
	cd ${XIGMANAS_SVNDIR}/conf && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${XIGMANAS_ROOTFS}/conf.default

	# adjust for dom0
	if [ "dom0" = ${XIGMANAS_XARCH} ]; then
		sed -i '' -e "/^xc0/ s/off/on /" ${XIGMANAS_ROOTFS}/etc/ttys
	fi

	return 0
}

build_system() {
  while true; do
echo -n '
-----------------------------
Compile XIGMANAS from Scratch
-----------------------------

	Menu Options:

1 - Update FreeBSD Source Tree and Ports Collections.
2 - Create Filesystem Structure.
3 - Build/Install the Kernel.
4 - Build World.
5 - Copy Files/Ports to their locations.
6 - Build Ports.
7 - Build Bootloader.
8 - Add Necessary Libraries.
9 - Modify File Permissions.
* - Exit.

Press # '
		read choice
		case $choice in
			1)	update_sources;;
			2)	create_rootfs;;
			3)	build_kernel;;
			4)	build_world;;
			5)	copy_files;;
			6)	build_ports;;
			7)	opt="-f";
					if [ 0 != $OPT_BOOTMENU ]; then
						opt="$opt -m"
					fi;
					if [ 0 != $OPT_BOOTSPLASH ]; then
						opt="$opt -b"
					fi;
					if [ 0 != $OPT_SERIALCONSOLE ]; then
						opt="$opt -s"
					fi;
					$XIGMANAS_SVNDIR/build/xigmanas-create-bootdir.sh $opt $XIGMANAS_BOOTDIR;;
			8)	add_libs;;
			9)	$XIGMANAS_SVNDIR/build/xigmanas-modify-permissions.sh $XIGMANAS_ROOTFS;;
			*)	main; return $?;;
		esac
		[ 0 == $? ] && echo "=> Successfully done <=" || echo "=> Failed!"
		sleep 1
  done
}
# Copy files/ports. Copying required files from 'distfiles & copy-ports'.
copy_files() {
			# Copy required sources to FreeBSD distfiles directory.
			echo;
			echo "-------------------------------------------------------------------";
			echo ">>> Copy needed sources to distfiles directory usr/ports/distfiles.";
			echo "-------------------------------------------------------------------";
			echo "===> Start copy sources"
			cp -f ${XIGMANAS_SVNDIR}/build/ports/distfiles/amd64-microcode_3.20171205.1.tar.xz /usr/ports/distfiles
			echo "===> Copy amd64-microcode_3.20171205.1.tar.xz done!"
			cp -f ${XIGMANAS_SVNDIR}/build/ports/distfiles/CLI_freebsd-from_the_10.2.2.1_9.5.5.1_codesets.zip /usr/ports/distfiles
			echo "===> Copy CLI_freebsd-from_the_10.2.2.1_9.5.5.1_codesets.zip done!"
			cp -f ${XIGMANAS_SVNDIR}/build/ports/distfiles/istgt-20180521.tar.gz /usr/ports/distfiles
			echo "===> Copy istgt-20180521.tar.gz done!"
			cp -f ${XIGMANAS_SVNDIR}/build/ports/distfiles/fuppes-0.692.tar.gz /usr/ports/distfiles
			echo "===> Copy fuppes-0.692.tar.gz done!"
			# Copy required ports to FreeBSD ports directory.
			echo;
			echo "----------------------------------------------------------";
			echo ">>> Copy new files to ports directory FreeBSD usr/ports/*.";
			echo "----------------------------------------------------------";
			echo "===> Copy new port php72-APCu to ports/devel/"
			cp -Rpv ${XIGMANAS_SVNDIR}/build/ports/copy-ports/files/php72-APCu /usr/ports/devel
			echo "===> Copy new files to /usr/ports/devel/php72-APCu done!"
	return 0
}
build_ports() {
	tempfile=$XIGMANAS_WORKINGDIR/tmp$$
	ports=$XIGMANAS_WORKINGDIR/ports$$

	# Choose what to do.
	$DIALOG --title "$XIGMANAS_PRODUCTNAME - Build/Install Ports" --menu "Please select whether you want to build or install ports." 11 45 3 \
		"build" "Build ports" \
		"rebuild" "Re-build ports (dev only)" \
		"install" "Install ports" 2> $tempfile
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi

	choice=`cat $tempfile`
	rm $tempfile

	# Create list of available ports.
	echo "#! /bin/sh
$DIALOG --title \"$XIGMANAS_PRODUCTNAME - Ports\" \\
--checklist \"Select the ports you want to process.\" 21 130 14 \\" > $tempfile

	for s in $XIGMANAS_SVNDIR/build/ports/*; do
		[ ! -d "$s" ] && continue
		port=`basename $s`
		state=`cat $s/pkg-state`
		if [ "arm" = ${XIGMANAS_ARCH} ]; then
			for forceoff in arcconf isboot grub2-bhyve open-vm-tools tw_cli vbox vbox-additions vmxnet3 icu; do
				if [ "$port" = "$forceoff" ]; then
					state="OFF"; break;
				fi
			done
		elif [ "i386" = ${XIGMANAS_ARCH} ]; then
			for forceoff in grub2-bhyve novnc open-vm-tools phpvirtualbox vbox vbox-additions xmd; do
				if [ "$port" = "$forceoff" ]; then
					state="OFF"; break;
				fi
			done
		elif [ "amd64" = ${XIGMANAS_ARCH} ]; then
			for forceoff in xmd; do
				if [ "$port" = "$forceoff" ]; then
					state="OFF"; break;
				fi
			done
		elif [ "dom0" = ${XIGMANAS_XARCH} ]; then
			for forceoff in firefly fuppes grub2-bhyve inadyn-mt minidlna netatalk3 open-vm-tools phpvirtualbox samba42 transmission vbox vbox-additions; do
				if [ "$port" = "$forceoff" ]; then
					state="OFF"; break;
				fi
			done
		fi
		case ${choice} in
			rebuild)
				t=`echo $s/work/.build_done.*`
				if [ -e "$t" ]; then
					state="OFF"
				fi
				;;
		esac
		case ${state} in
			[hH][iI][dD][eE])
				;;
			*)
				desc=`cat $s/pkg-descr`;
				echo "\"$port\" \"$desc\" $state \\" >> $tempfile;
				;;
		esac
	done

	# Display list of available ports.
	sh $tempfile 2> $ports
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		rm $ports
		return 1
	fi
	rm $tempfile

	case ${choice} in
		build|rebuild)
			# Set ports options
			echo;
			echo "--------------------------------------------------------------";
			echo ">>> Set Ports Options.";
			echo "--------------------------------------------------------------";
			cd ${XIGMANAS_SVNDIR}/build/ports/options && make
			# Clean ports.
			echo;
			echo "--------------------------------------------------------------";
			echo ">>> Cleaning Ports.";
			echo "--------------------------------------------------------------";
			for port in $(cat ${ports} | tr -d '"'); do
				cd ${XIGMANAS_SVNDIR}/build/ports/${port};
				make clean;
			done;
			if [ "i386" = ${XIGMANAS_ARCH} ]; then
				# workaround patch
				cp ${XIGMANAS_SVNDIR}/build/ports/vbox/files/extra-patch-src-VBox-Devices-Graphics-DevVGA.h /usr/ports/emulators/virtualbox-ose/files/patch-src-VBox-Devices-Graphics-DevVGA.h
			fi
			# Build ports.
			for port in $(cat $ports | tr -d '"'); do
				echo;
				echo "--------------------------------------------------------------";
				echo ">>> Building Port: ${port}";
				echo "--------------------------------------------------------------";
				cd ${XIGMANAS_SVNDIR}/build/ports/${port};
				make build;
				[ 0 != $? ] && return 1; # successful?
			done;
			;;
		install)
			if [ -f /var/db/pkg/local.sqlite ]; then
				cp -p /var/db/pkg/local.sqlite $XIGMANAS_WORKINGDIR/pkg
			fi
			for port in $(cat ${ports} | tr -d '"'); do
				echo;
				echo "--------------------------------------------------------------";
				echo ">>> Installing Port: ${port}";
				echo "--------------------------------------------------------------";
				cd ${XIGMANAS_SVNDIR}/build/ports/${port};
				# Delete cookie first, otherwise Makefile will skip this step.
				rm -f ./work/.install_done.* ./work/.stage_done.*;
				env PKG_DBDIR=$XIGMANAS_WORKINGDIR/pkg FORCE_PKG_REGISTER=1 make install;
				[ 0 != $? ] && return 1; # successful?
			done;
			;;
	esac
	rm ${ports}

  return 0
}

main() {
	# Ensure we are in $XIGMANAS_WORKINGDIR
	[ ! -d "$XIGMANAS_WORKINGDIR" ] && mkdir $XIGMANAS_WORKINGDIR
	[ ! -d "$XIGMANAS_WORKINGDIR/pkg" ] && mkdir $XIGMANAS_WORKINGDIR/pkg
	cd $XIGMANAS_WORKINGDIR

	echo -n "
--------------------------
${XIGMANAS_PRODUCTNAME} Build Environment
--------------------------

1  - Update XigmaNAS Source Files to CURRENT.
2  - XigmaNAS Compile Menu.
10 - Create 'Embedded.img.xz' File. (Firmware Update)
11 - Create 'LiveUSB.img.gz' File. (Rawrite to USB Key)
12 - Create 'LiveCD' (ISO) File.
13 - Create 'LiveCD-Tin' (ISO) without 'Embedded' File.
14 - Create 'Full' (TGZ) Update File."
	if [ "arm" = ${XIGMANAS_ARCH} ]; then
		echo -n "
20 - Create 'RPI SD (IMG) File.
21 - Create 'RPI2 SD (IMG) File."
	fi
	echo -n "
15 - Create 'xigmanas.pot' file from Source files.
*  - Exit.

Press # "
	read choice
	case $choice in
		1)	update_svn;;
		2)	build_system;;
		10)	create_embedded;;
		11)	create_usb;;
		12)	create_iso;;
		13)	create_iso_tiny;;
		14)	create_full;;
		15)	$XIGMANAS_SVNDIR/build/xigmanas-create-pot.sh;;
		20)	if [ "arm" = ${XIGMANAS_ARCH} ]; then create_rpisd; fi;;
		21)	if [ "arm" = ${XIGMANAS_ARCH} ]; then create_rpi2sd; fi;;
		*)	exit 0;;
	esac

	[ 0 == $? ] && echo "=> Successfully done <=" || echo "=> Failed! <="
	sleep 1

	return 0
}

while true; do
	main
done
exit 0
