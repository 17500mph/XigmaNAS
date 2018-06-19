#!/bin/sh
#
# /etc/install/zfsinstall.sh
# Part of NAS4Free (http://www.nas4free.org).
# Copyright (c) 2012-2018 The NAS4Free Project <info@nas4free.org>.
# All rights reserved.
#
# Debug script
# set -x

# Set environment.
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin
export PATH

# Global variables.
PLATFORM=`uname -m`
CDPATH="/media/cdrom"
SYSBACKUP="/tmp/sysbackup"
INCLUDE="/etc/install/include/boot"
PRDNAME=`cat /etc/prd.name`
APPNAME="RootOnZFS"
ALTROOT="/mnt"
DATASET="/ROOT"
BOOTENV="/default-install"
ZROOT="zroot"

tmpfile=`tmpfile 2>/dev/null` || tmpfile=/tmp/tui$$
trap "rm -f $tmpfile" 0 1 2 5 15

# Mount CD/USB drive.
mount_cdrom()
{
	umount -f ${CDPATH} > /dev/null 2>&1
	LIVECD=`glabel status | grep -q iso9660/${PRDNAME}; echo $?`
	LIVEUSB=`glabel status | grep -q ufs/liveboot; echo $?`
	if [ "${LIVECD}" == 0 ]; then
		# Check if cd-rom is mounted else auto mount cd-rom.
		if [ ! -f "${CDPATH}/version" ]; then
			# Try to auto mount cd-rom.
			mkdir -p ${CDPATH}
			echo "Mounting CD-ROM Drive"
			mount_cd9660 /dev/cd0 ${CDPATH} > /dev/null 2>&1 || mount_cd9660 /dev/cd1 ${CDPATH} > /dev/null 2>&1
		fi
	elif [ "${LIVEUSB}" == 0 ]; then
		# Check if liveusb is mounted else auto mount liveusb.
		if [ ! -f "${CDPATH}/version" ]; then
			# Try to auto mount liveusb.
			mkdir -p ${CDPATH}
			echo "Mounting LiveUSB Drive"
			mount /dev/ufs/liveboot ${CDPATH} > /dev/null 2>&1
		fi
	fi
	# If no cd/usb is mounted ask for manual mount.
	if [ ! -f "${CDPATH}/version" ]; then
		manual_cdmount
	fi
}

umount_cdrom()
{
	echo "Unmount CD/USB Drive"
	umount -f ${CDPATH} > /dev/null 2>&1
	rm -R ${CDPATH}
}

manual_cdmount()
{
	DRIVES=`camcontrol devlist`
	cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "Select the Install Media Source" \
	--form "${DRIVES}" 0 0 0 \
	"Select CD/USB Drive e.g: cd0:" 1 1 "" 1 30 30 30 \
	2>${tmpfile}
	if [ 0 -ne $? ]; then
		exit 0
	fi

	# Try to mount from specified device.
	mkdir -p ${CDPATH}
	echo "Mounting CD/USB Drive"
	DEVICE=`cat ${tmpfile}`
	mount /dev/${DEVICE}s1a ${CDPATH} > /dev/null 2>&1 || mount_cd9660 /dev/${DEVICE} ${CDPATH} > /dev/null 2>&1
	# Check if mounted cd/usb is accessible.
	if [ ! -f "${CDPATH}/version" ]; then
		# Re-try
		mount_cdrom
	fi
}

# Clean any existing metadata on selected disks.
cleandisk_init()
{
	sysctl kern.geom.debugflags=0x10
	sleep 1

	# Load geom_mirror kernel module.
	if ! kldstat | grep -q geom_mirror; then
		kldload /boot/kernel/geom_mirror.ko
		# Destroy existing geom swap mirror.
		if gmirror status | grep -q gswap; then
			gmirror forget gswap
			gmirror destroy gswap
		fi
	fi

	DISKS=${DEVICE_LIST}
	NUM="0"
	WIPESECTORCOUNT=16384
	for DISK in ${DISKS}
	do
		echo "Cleaning disk ${DISK}"
		gmirror clear ${DISK} > /dev/null 2>&1
		zpool labelclear -f /dev/gpt/sysdisk${NUM} > /dev/null 2>&1
		zpool labelclear -f /dev/${DISK} > /dev/null 2>&1
		gpart destroy -F ${DISK} > /dev/null 2>&1

		diskinfo ${DISK} | while read DISK sectorsize size sectors other
			do
				if [ ${WIPESECTORCOUNT} -gt ${sectors} ]; then
					sectorstowipe=${sectors}
				else
					sectorstowipe=${WIPESECTORCOUNT}
				fi
				# Delete MBR, GPT Primary, ZFS(L0L1)/other partition table.
				/bin/dd if=/dev/zero of=${DISK} bs=${sectorsize} count=${sectorstowipe} > /dev/null 2>&1
				# Delete GEOM metadata, GPT Secondary(L2L3).
				/bin/dd if=/dev/zero of=${DISK} bs=${sectorsize} oseek=`expr ${sectors} - ${sectorstowipe}` count=${sectorstowipe} > /dev/null 2>&1
			done
			NUM=`expr $NUM + 1`
	done
}

# Create GPT/Partition on disk.
gptpart_init()
{
	DISKS=${DEVICE_LIST}
	tmplist=/tmp/swap$$
	cat /dev/null > ${tmpfile}
	NUM="0"
	for DISK in ${DISKS}
	do
		echo "Creating GPT/Partition on ${DISK}"
		gpart create -s gpt ${DISK} > /dev/null

		# Create boot partition.
		if [ "${BOOT_MODE}" == 2 ]; then
			gpart add -a 4k -s 200M -t efi -l efiboot${NUM} ${DISK} > /dev/null
		fi
		gpart add -a 4k -s 512K -t freebsd-boot -l sysboot${NUM} ${DISK} > /dev/null

		if [ ! -z "${SWAP}" ]; then
			# Add swap partition to selected drives.
			gpart add -a 4m -s ${SWAP} -t freebsd-swap -l swap${NUM} ${DISK} > /dev/null
			# Generate explicit swap device list.
			echo "/dev/gpt/swap${NUM}" >> ${tmplist}
		fi
		gpart add -a 4m ${ZROOT_SIZE} -t freebsd-zfs -l sysdisk${NUM} ${DISK} > /dev/null
		# Generate the glabel device list.
		echo "/dev/gpt/sysdisk${NUM}" >> ${tmpfile}
		NUM=`expr $NUM + 1`
	done

	export GLABEL_DEVLIST=`cat ${tmpfile}`
	if [ ! -z "${SWAP}" ]; then
		export SWAP_DEVLIST=`cat ${tmplist}`
		rm -f ${tmplist}
	fi
}

# Install RootOnZFS.
zroot_init()
{
	# Begin installation.
	printf '\033[1;37;44m RootOnZFS Working... \033[0m\033[1;37m\033[0m\n'

	# Mount cd-rom.
	mount_cdrom

	# Check for existing zroot pool.
	zpool_check

	# Get rid of any metadata on selected disk.
	cleandisk_init

	# Create GPT/Partition on disk.
	gptpart_init

	# Set the raid type.
	if [ "${STRIPE}" == 0 ]; then
		RAID=""
	elif [ "${MIRROR}" == 0 ]; then
		RAID="mirror"
	elif [ "${RAIDZ1}" == 0 ]; then
		RAID="raidz1"
	elif [ "${RAIDZ2}" == 0 ]; then
		RAID="raidz2"
	elif [ "${RAIDZ3}" == 0 ]; then
		RAID="raidz3"
	fi

	# Create bootable zroot pool with boot environments support.
	echo "Creating bootable ${ZROOT} pool"
	zpool create -o altroot=${ALTROOT} -O compress=lz4 -O atime=off -m none -f ${ZROOT} ${RAID} ${GLABEL_DEVLIST}

	# Create ZFS filesystem hierarchy.
	zfs create -o mountpoint=none ${ZROOT}${DATASET}
	zfs create -o mountpoint=/ ${ZROOT}${DATASET}${BOOTENV}
	zfs create -o mountpoint=/tmp -o exec=on -o setuid=off ${ZROOT}/tmp
	zfs create -o mountpoint=/var ${ZROOT}/var
	zfs set mountpoint=/${ZROOT} ${ZROOT}
	zpool set bootfs=${ZROOT}${DATASET}${BOOTENV} ${ZROOT}
	zfs set canmount=noauto ${ZROOT}${DATASET}${BOOTENV}
	if [ $? -eq 1 ]; then
		echo "An error has occurred while creating ${ZROOT} pool."
		exit 1
	fi

	# Install system files.
	install_sys_files

	# Set the zpool.cache file.
	zpool set cachefile=${ALTROOT}/boot/zfs/zpool.cache ${ZROOT}

	# Write bootcode.
	echo "Writing bootcode..."
	DISKS=${DEVICE_LIST}
	for DISK in ${DISKS}
	do
		if [ "${BOOT_MODE}" == 2 ]; then
			gpart bootcode -p /boot/boot1.efifat -i 1 ${DISK}
			gpart bootcode -b /boot/pmbr -p /boot/gptzfsboot -i 2 ${DISK}
		else
			gpart bootcode -b /boot/pmbr -p /boot/gptzfsboot -i 1 ${DISK}
		fi
	done

	sysctl kern.geom.debugflags=0
	sleep 1

	# Add required ZFS mount points to fstab(legacy mountpoints only).
	#echo "${ZROOT}/tmp /tmp zfs rw,noatime 0 0" >> ${ALTROOT}/etc/fstab
	#echo "${ZROOT}/var /var zfs rw,noatime 0 0" >> ${ALTROOT}/etc/fstab

	# Creating/adding the fixed swap devices.
	if [ ! -z "${SWAP}" ]; then
		if [ "${SWAPMODE}" == 1 ]; then
			echo "Creating/adding swap Mirror..."
			if ! kldstat | grep -q geom_mirror; then
				kldload /boot/kernel/geom_mirror.ko
			fi
			gmirror label -b prefer gswap ${SWAP_DEVLIST}
			# Add swap mirror to fstab.
			echo "/dev/mirror/gswap none swap sw 0 0" >> ${ALTROOT}/etc/fstab
		else
			# Add swap device to fstab.
			echo "Adding swap devices to fstab..."
			for swapdev in ${SWAP_DEVLIST}; do
				echo "${swapdev} none swap sw 0 0" >> ${ALTROOT}/etc/fstab
			done
		fi
	fi

	# Unmount cd-rom.
	umount_cdrom

	# Creates system default snapshot after install.
	create_default_snapshot

	# Flush disk cache and wait 1 second.
	sync; sleep 1
	zpool export ${ZROOT}
	#rm -Rf ${ALTROOT}

	# Final message.
	if [ $? -eq 0 ]; then
		cdialog --msgbox "$PRDNAME $APPNAME Successfully Installed!" 6 60
	else
		echo "An error has occurred during the installation."
		exit 1
	fi
	exit 0
}

install_sys_files()
{
	echo "Installing system files on ${ZROOT}..."

	# Install system files and discard unwanted folders.
	EXCLUDEDIRS="--exclude .snap/ --exclude resources/ --exclude zinstall.sh/ --exclude media/ --exclude mnt/ --exclude dev/ --exclude var/ --exclude tmp/ --exclude cf/"
	/usr/bin/tar ${EXCLUDEDIRS} -c -f - -C / . | tar -xpf - -C ${ALTROOT}

	# Copy files from live media source.
	/bin/mkdir -p ${ALTROOT}/dev
	/bin/mkdir -p ${ALTROOT}/mnt
	/bin/mkdir -p ${ALTROOT}/tmp
	/bin/mkdir -p ${ALTROOT}/var
	/bin/chmod 1777 ${ALTROOT}/tmp
	/bin/mkdir -p ${ALTROOT}/boot/defaults
	/bin/cp -r ${CDPATH}/boot/* ${ALTROOT}/boot
	/bin/cp -r ${CDPATH}/boot/defaults/* ${ALTROOT}/boot/defaults
	/bin/cp -r ${CDPATH}/boot/kernel/* ${ALTROOT}/boot/kernel

	# Copy our boot loader menu files to /boot.
	if [ -f "${INCLUDE}/menu.4th" ]; then
		chmod 444 ${INCLUDE}/menu.4th
		cp -pf ${INCLUDE}/menu.4th ${ALTROOT}/boot
	fi

	# Generate/update our loader.rc
	if [ -f "${INCLUDE}/menu.4th" ]; then
	cat << EOF > ${ALTROOT}/boot/loader.rc
\ Loader.rc
include /boot/loader.4th
start
initialize
check-password
include /boot/beastie.4th
beastie-start
EOF
	fi
	chmod 444 ${ALTROOT}/boot/loader.rc

	# Decompress kernel.
	/usr/bin/gzip -d -f ${ALTROOT}/boot/kernel/kernel.gz

	# Decompress modules (legacy versions).
	cd ${ALTROOT}/boot/kernel
	for FILE in *.gz
	do
		if [ -f "${FILE}" ]; then
			/usr/bin/gzip -d -f ${FILE}
		fi
	done
	cd

	# Install configuration file.
	/bin/mkdir -p ${ALTROOT}/cf/conf
	/bin/cp /conf.default/config.xml ${ALTROOT}/cf/conf

	# Generate new loader.conf file.
	cat << EOF > ${ALTROOT}/boot/loader.conf
kernel="kernel"
bootfile="kernel"
kernel_options=""
hw.est.msr_info="0"
hw.hptrr.attach_generic="0"
hw.msk.msi_disable="1"
kern.maxfiles="6289573"
kern.cam.boot_delay="12000"
kern.cam.ada.legacy_aliases="0"
kern.geom.label.disk_ident.enable="0"
kern.geom.label.gptid.enable="0"
hint.acpi_throttle.0.disabled="0"
hint.p4tcc.0.disabled="0"
autoboot_delay="3"
isboot_load="YES"
vfs.root.mountfrom="zfs:${ZROOT}${DATASET}${BOOTENV}"
zfs_load="YES"
EOF

	if [ "${PLATFORM}" == "amd64" ]; then
		echo 'mlx4en_load="YES"' >> ${ALTROOT}/boot/loader.conf
	fi

	if [ "${SWAPMODE}" == 1 ]; then
		if [ ! -z "${SWAP}" ]; then
			echo 'geom_mirror_load="YES"' >> ${ALTROOT}/boot/loader.conf
		fi
	fi

	# Generate new rc.conf file.
	#cat << EOF > ${ALTROOT}/etc/rc.conf

#EOF

	# Clear default router and netwait ip on new installs.
	sysrc -f ${ALTROOT}/etc/rc.conf defaultrouter="" > /dev/null 2>&1
	sysrc -f ${ALTROOT}/etc/rc.conf netwait_ip="" > /dev/null 2>&1

	# Set the release type.
	if [ "${PLATFORM}" == "amd64" ]; then
		echo "x64-full" > ${ALTROOT}/etc/platform
	elif [ "${PLATFORM}" == "i386" ]; then
		echo "x86-full" > ${ALTROOT}/etc/platform
	fi

	# Generate /etc/fstab.
	cat << EOF > ${ALTROOT}/etc/fstab
# Device    Mountpoint    FStype    Options    Dump    Pass#
#
EOF

	# Generate /etc/swapdevice.
	if [ ! -z "${SWAP}" ]; then
		cat << EOF > ${ALTROOT}/etc/swapdevice
swapinfo
EOF
	fi

	# Generating the /etc/cfdevice (this file is linked in /var/etc at bootup)
	# This file is used by the firmware and mount check and is normally
	# generated with 'liveCD' and 'embedded' during startup, but need to be
	# created during install of 'full'.
	if [ ! -z "${disklist}" ]; then
		cat << EOF > ${ALTROOT}/etc/cfdevice
${disklist}
EOF
	fi

	echo "Done!"
}

create_default_snapshot()
{
	echo "Creating system default snapshot..."
	zfs snapshot ${ZROOT}${DATASET}${BOOTENV}@factory-defaults
	echo "Done!"
	sleep 1
}

create_upgrade_snapshot()
{
	echo "Creating system upgrade snapshot..."
	DATE=`date +%Y-%m-%d-%H%M%S`
	zfs snapshot ${ZROOT}${DATASET}${BOOTENV}@upgrade-${DATE}
	echo "Done!"
	sleep 1
}

upgrade_sys_files()
{
	echo "Upgrading system files on ${ZROOT}..."

	# Remove chflags for protected files before upgrade to prevent errors
	# chflags will be restored after upgrade completion by default.
	if [ -f ${ALTROOT}/usr/lib/librt.so.1 ]; then
		chflags 0 ${ALTROOT}/usr/lib/librt.so.1
	fi

	# Install system files and discard unwanted folders.
	EXCLUDEDIRS="--exclude .snap/ --exclude resources/ --exclude zinstall.sh/ --exclude ${ZROOT}/ --exclude mnt/ --exclude dev/ --exclude var/ --exclude tmp/ --exclude cf/"
	/usr/bin/tar ${EXCLUDEDIRS} -c -f - -C / . | tar -xpf - -C ${ALTROOT}

	# Copy files from live media source.
	/bin/mkdir -p ${ALTROOT}/dev
	/bin/mkdir -p ${ALTROOT}/mnt
	/bin/mkdir -p ${ALTROOT}/tmp
	/bin/mkdir -p ${ALTROOT}/var
	/bin/chmod 1777 ${ALTROOT}/tmp
	/bin/mkdir -p ${ALTROOT}/boot/defaults
	/bin/cp -r ${CDPATH}/boot/* ${ALTROOT}/boot
	/bin/cp -r ${CDPATH}/boot/defaults/* ${ALTROOT}/boot/defaults
	/bin/cp -r ${CDPATH}/boot/kernel/* ${ALTROOT}/boot/kernel

	# Copy our boot loader menu files to /boot.
	if [ -f "${INCLUDE}/menu.4th" ]; then
		chmod 444 ${INCLUDE}/menu.4th
		cp -pf ${INCLUDE}/menu.4th ${ALTROOT}/boot
	fi

	# Generate/update our loader.rc
	if [ -f "${INCLUDE}/menu.4th" ]; then
	cat << EOF > ${ALTROOT}/boot/loader.rc
\ Loader.rc
include /boot/loader.4th
start
initialize
check-password
include /boot/beastie.4th
beastie-start
EOF
	fi
	chmod 444 ${ALTROOT}/boot/loader.rc

	# Decompress kernel.
	/usr/bin/gzip -d -f ${ALTROOT}/boot/kernel/kernel.gz

	# Decompress modules (legacy versions).
	cd ${ALTROOT}/boot/kernel
	for FILE in *.gz
	do
		if [ -f "${FILE}" ]; then
			/usr/bin/gzip -d -f ${FILE}
		fi
	done
	cd
	echo "Done!"
}

backup_sys_files()
{
	# Backup system configuration.
	echo "Backup system configuration..."
	cp -p ${ALTROOT}/boot/loader.conf ${SYSBACKUP}

	if [ -f "/${ALTROOT}/boot.config" ]; then
		cp -p ${ALTROOT}/boot.config ${SYSBACKUP}
	fi
	if [ -f "/${ALTROOT}/boot/loader.conf.local" ]; then
		cp -p ${ALTROOT}/boot/loader.conf.local ${SYSBACKUP}
	fi
	if [ -f "/${ALTROOT}/boot/zfs/zpool.cache" ]; then
		cp -p ${ALTROOT}/boot/zfs/zpool.cache ${SYSBACKUP}
	fi

	#cp -p /${ALTROOT}/etc/platform ${SYSBACKUP}
	cp -p ${ALTROOT}/etc/fstab ${SYSBACKUP}
	cp -p ${ALTROOT}/etc/cfdevice ${SYSBACKUP}
}

restore_sys_files()
{
	# Restore previous backup files to upgraded system.
	echo "Restore system configuration..."
	cp -pf ${SYSBACKUP}/loader.conf ${ALTROOT}/boot

	if [ -f "${SYSBACKUP}/boot.config" ]; then
		cp -pf ${SYSBACKUP}/boot.config ${ALTROOT}
	else
		rm -f ${ALTROOT}/boot.config
	fi
	if [ -f "${SYSBACKUP}/loader.conf.local" ]; then
		cp -pf ${SYSBACKUP}/loader.conf.local ${ALTROOT}/boot
	fi
	if [ -f "${SYSBACKUP}/zpool.cache" ]; then
		cp -pf ${SYSBACKUP}/zpool.cache ${ALTROOT}/boot/zfs
	fi

	#cp -pf ${SYSBACKUP}/platform /etc
	cp -pf ${SYSBACKUP}/fstab ${ALTROOT}/etc
	cp -pf ${SYSBACKUP}/cfdevice ${ALTROOT}/etc
}

# Legacy upgrade on default install.
upgrade_system()
{
	# Import current zroot pool to be upgrade, otherwise exit.
	echo "Trying to import ${ZROOT} pool..."

	if [ ! -f "${ALTROOT}/etc/prd.name" ]; then
		zpool import -R ${ALTROOT} ${ZROOT} > /dev/null 2>&1 || zpool import -f -R ${ALTROOT} ${ZROOT} > /dev/null 2>&1
		if [ $? -eq 1 ]; then
			echo "Unable to detect/import ${ZROOT} pool."
			exit 1
		fi
	fi

	# Check if os exists on specified zroot pool, otherwise exit.
	if [ -f "${ALTROOT}/etc/prd.name" ]; then
		PRD=`cat ${ALTROOT}/etc/prd.name`
		if [ "${PRD}" != "${PRDNAME}" ]; then
			echo "${PRDNAME} product not detected."
			zpool export ${ZROOT}
			exit 1
		fi
	else
		echo "${PRDNAME} installation not found."
		zpool export ${ZROOT}
		exit 1
	fi

	# System upgrade confirmation.
	upgrade_yesno

	printf '\033[1;37;44m RootOnZFS Working... \033[0m\033[1;37m\033[0m\n'

	# Mount cd-rom.
	mount_cdrom

	# Create config backup directory.
	mkdir -p ${SYSBACKUP}

	# Backup system configuration.
	backup_sys_files

	# Start upgrade script to remove obsolete files. This should be done
	# before system is updated because it may happen that some files
	# may be reintroduced in the system.
	echo "Removing obsolete files..."
	/etc/install/upgrade.sh clean ${ALTROOT}

	# Upgrade system files.
	upgrade_sys_files
  
	# Restore previous backup files to upgraded system.
	restore_sys_files

	# Set the release type.
	if [ "${PLATFORM}" == "amd64" ]; then
		echo "x64-full" > ${ALTROOT}/etc/platform
	elif [ "${PLATFORM}" == "i386" ]; then
		echo "x86-full" > ${ALTROOT}/etc/platform
	fi

	# Cleanup system backup files.
	rm -Rf ${SYSBACKUP}

	# Unmount cd-rom.
	umount_cdrom

	# Create system upgrade snapshot after install.
	create_upgrade_snapshot

	# Flush disk cache and wait 1 second..
	sync; sleep 1
	zpool export ${ZROOT}

	# Final message.
	if [ $? -eq 0 ]; then
		cdialog --msgbox "$PRDNAME $APPNAME System Successfully Upgraded!" 6 62
	else
		echo "An error has occurred during the installation."
	fi
	exit 0
}

zpool_check()
{
	# Check if a zroot pool already exist and/or mounted.
	echo "Check for existing ${ZROOT} pool..."
	if zpool import | grep -qw ${ZROOT} || zpool status | grep -qw ${ZROOT}; then
		printf '\033[1;37;43m WARNING \033[0m\033[1;37m A pool called '${ZROOT}' already exist.\033[0m\n'
		while true
			do
				read -p "Do you wish to proceed with the installation anyway? [y/N]:" yn
				case ${yn} in
				[Yy]) break;;
				[Nn]) exit 0;;
				esac
			done
		echo "Proceeding..."
		# Export existing zroot pool.
		zpool export -f ${ZROOT} > /dev/null 2>&1
	fi
}

upgrade_yesno()
{
	cdialog --title "Proceed with $PRDNAME $APPNAME Upgrade" \
	--backtitle "$PRDNAME $APPNAME Installer" \
	--yesno "${PRDNAME} has been detected and will be upgraded on <${ZROOT}> pool, do you really want to continue?" 6 70
	if [ 0 -ne $? ]; then
		zpool export ${ZROOT}
		exit 0
	fi
}

menu_reboot()
{
	echo "System Rebooting..."
	shutdown -r now
	while [ 1 ]; do exit; done
}

install_yesno()
{
	DISKS=`echo ${disklist}`
	cdialog --title "Proceed with $PRDNAME $APPNAME Install" \
	--backtitle "$PRDNAME $APPNAME Installer" \
	--yesno "Continuing with the installation will destroy all data on <${DISKS}> device(s), do you really want to continue?" 0 0
	if [ 0 -ne $? ]; then
		exit 0
	fi
}

menu_swap()
{
	cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "Enter a desired Swap size" \
	--form "\nPlease enter a valid swap size, default size is 2G, leave empty for none." 0 0 0 \
	"Enter swap size:" 1 1 "2G" 1 25 25 25 \
	2>${tmpfile}
	if [ 0 -ne $? ]; then
		exit 0
	fi

	export SWAP=`cat ${tmpfile} | tr -d '"'`
	
	if [ ! -z "${SWAP}" ]; then
		if [ "${choice}" == 2 ]; then
			if [ "${NWAY_MIRROR}" != 0 ]; then
				cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "System Swap mode selection" \
				--radiolist "Select system Swap mode, (default mirrored)." 10 50 4 \
				1 "Mirrored System Swap" on \
				2 "Multiple System Swap" off \
				2>${tmpfile}
				if [ 0 -ne $? ]; then
					exit 0;
				fi
				export SWAPMODE=`cat ${tmpfile}`
			fi
		fi
	fi
}

menu_zrootsize()
{
	cdialog --title "Customize zroot pool partition size?" \
	--backtitle "$PRDNAME $APPNAME Installer" \
	--default-button no --yesno "Would you like to customize the ${ZROOT} partition size?" 5 60
	choice=$?
	case "${choice}" in
		0) true;;
		1) return 0;;
		255) exit 0;;
	esac

	cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "Enter a desired zroot pool size" \
	--form "\nPlease enter a valid zroot pool size in GB, for example 10G, or leave empty to use all remaining disk space (default empty)." 0 0 0 \
	"Enter zroot pool size:" 1 1 "" 1 25 25 25 \
	2>${tmpfile}
	if [ 0 -ne $? ]; then
		exit 0
	fi

	rootsize=`cat ${tmpfile}`

	if [ ! -z "${rootsize}" ]; then
		export ZROOT_SIZE="-s ${rootsize}"
	fi
}

menu_bootmode()
{
	cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "Boot mode selection menu" \
	--radiolist "Select system boot mode, default is GPT BIOS." 10 50 4 \
	1 "GPT BIOS System Boot" on \
	2 "GPT BIOS+UEFI System Boot" off \
	2>${tmpfile}
	if [ 0 -ne $? ]; then
		exit 0
	fi

	bootmode=`cat ${tmpfile}`

	export BOOT_MODE=${bootmode}
}

menu_zroot_create()
{
	menu_install
	menu_swap
	menu_zrootsize
	menu_bootmode
	install_yesno
	zroot_init
}

sort_disklist()
{
	sed 's/\([^0-9]*\)/\1 /' | sort +0 -1 +1n | tr -d ' '
}

get_disklist()
{
	local disklist

	for disklist in $(sysctl -n kern.disks)
	do
		VAL="${VAL} ${disklist}"
	done

	VAL=`echo ${VAL} | tr ' ' '\n'| grep -v '^cd' | sort_disklist`
	export VAL
}

get_media_desc()
{
	local media
	local description
	local cap

	media=$1
	VAL=""
	if [ -n "${media}" ]; then
		# Try to get model information for each detected device.
		description=`camcontrol identify ${media} | grep 'model' | awk '{print $3, $4, $5}'`
	if [ -z "${description}" ] ; then
		# Re-try with "camcontrol inquiry" instead.
		description=`camcontrol inquiry ${media} | grep -E '<*>' | cut -d '<' -f2 | cut -d '>' -f1`
		if [ -z "${description}" ] ; then
			description="Disk Drive"
		fi
	fi
		cap=`diskinfo ${media} | awk '{
			capacity = $3;
			if (capacity >= 1099511627776) {
				printf("%.1f TiB", capacity / 1099511627776.0);
			} else if (capacity >= 1073741824) {
				printf("%.1f GiB", capacity / 1073741824.0);
			} else if (capacity >= 1048576) {
				printf("%.1f MiB", capacity / 1048576.0);
			} else {
				printf("%d Bytes", capacity);
		}}'`
		VAL="${description} -- ${cap}"
	fi
	export VAL
}

menu_install()
{
	tmplist=/tmp/tui$$
	get_disklist
	disklist="${VAL}"
	list=""
	items=0

	for disklist in ${disklist}
		do
			get_media_desc "${disklist}"
			desc="${VAL}"
			list="${list} ${disklist} '${desc}' off"
			items=$((${items} + 1))
		done

	if [ "${items}" -ge 10 ]; then
		items=10
		menuheight=20
	else
		menuheight=10
		menuheight=$((${menuheight} + ${items}))
	fi

	if [ "${items}" -eq 0 ]; then
		eval "cdialog --title 'Choose destination drive(s)' --msgbox 'No drives available' 5 60" 2>${tmpfile}
		exit 1
	fi

	eval "cdialog --backtitle '$PRDNAME $APPNAME Installer'   --title 'Choose destination drive' \
		--checklist 'Select (one) or (more) drives where $PRDNAME should be installed, use arrow keys to navigate to the drive(s) for installation then select a drive with the spacebar.' \
		${menuheight} 60 ${items} ${list}" 2>${tmpfile}
	if [ 0 -ne $? ]; then
		exit 0
	fi

	if [ ! -z "${tmpfile}" ]; then
		export disklist=$(eval "echo `cat ${tmpfile}`")
	fi

	count="1"
	for item in ${disklist}
	do
		DEV_COUNT=`echo $count`
		count=`expr $count + 1`
	done

	# Check for empty user input.
	if [ -z "${disklist}" ]; then
		if [ "${choice}" == 1 ]; then
			cdialog --msgbox "Notice: You need to select at least one disk!" 6 55; exit 1
		elif [ "${choice}" == 2 ]; then
			cdialog --msgbox "Notice: You need to select at least two disks!" 6 55; exit 1
		elif [ "${choice}" == 3 ]; then
			cdialog --msgbox "Notice: You need to select at least two disks!" 6 55; exit 1
		elif [ "${choice}" == 4 ]; then
			cdialog --msgbox "Notice: You need to select at least three disks!" 6 55; exit 1
		elif [ "${choice}" == 5 ]; then
			cdialog --msgbox "Notice: You need to select at least four disks!" 6 55; exit 1
		fi
	fi

	# Check for absolute minimum disks selection.
	if [ -n "${disklist}" ]; then
		if [ "${choice}" == 2 ]; then
			if [ "${DEV_COUNT}" -le 1 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of two disks!" 6 60; exit 1
			else
				if [ "${DEV_COUNT}" -ge 3 ]; then
					export NWAY_MIRROR="0"
				fi
			fi
		elif [ "${choice}" == 3 ]; then
			if [ "${DEV_COUNT}" -le 1 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of two disks!" 6 60; exit 1
			fi
		elif [ "${choice}" == 4 ]; then
			if [ "${DEV_COUNT}" -le 2 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of three disks!" 6 60; exit 1
			fi
		elif [ "${choice}" == 5 ]; then
			if [ "${DEV_COUNT}" -le 3 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of four disks!" 6 60; exit 1
			fi
		fi
	fi
	cat /dev/null > ${tmplist}
	for disk in $disklist; do
		echo "/dev/$disk" >> ${tmplist}
	done

	export DEVICE_LIST=`cat ${tmplist}`
	rm -f ${tmplist}
}

menu_main()
{
	while :
		do
			cdialog --backtitle "$PRDNAME $APPNAME Installer" --clear --title "$PRDNAME Installer Options" --cancel-label "Exit" --menu "" 12 50 10 \
			"1" "Install $PRDNAME $APPNAME on Stripe" \
			"2" "Install $PRDNAME $APPNAME on Mirror" \
			"3" "Install $PRDNAME $APPNAME on RAIDZ1" \
			"4" "Install $PRDNAME $APPNAME on RAIDZ2" \
			"5" "Install $PRDNAME $APPNAME on RAIDZ3" \
			"6" "Upgrade $PRDNAME $APPNAME System" \
			2>${tmpfile}
			if [ 0 -ne $? ]; then
				exit 0
			fi
			choice=`cat "${tmpfile}"`
			case "${choice}" in
				1) export STRIPE="0" && menu_zroot_create ;;
				2) export MIRROR="0" && menu_zroot_create ;;
				3) export RAIDZ1="0" && menu_zroot_create ;;
				4) export RAIDZ2="0" && menu_zroot_create ;;
				5) export RAIDZ3="0" && menu_zroot_create ;;
				6) upgrade_system ;;
			esac
		done
}
menu_main