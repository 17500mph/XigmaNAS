#!/bin/sh -e
#
# Part of XigmaNAS (http://www.xigmanas.com).
# Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
# All rights reserved.
#

# Set environment.
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:${PATH}
export PATH

# Set global variables.
PLATFORM=$(uname -m)
PRDNAME=$(cat /etc/prd.name)
CDPATH="/tmp/cdrom"
SYSBACKUP="/tmp/sysbackup"
INCLUDE="/etc/install/include/boot"
APPNAME="RootOnZFS"
ALTROOT="/mnt"
DATASET="/ROOT"
BOOTENV="/default-install"
ZROOT="zroot"

tmpfile=/tmp/zfsinstall.$$
trap "rm -f ${tmpfile}" 0 1 2 3 5 6 9 15

# Mount CD/USB drive.
mount_cdrom()
{
	# Unmount for stale/interrupted previous attempts.
	umount_cdrom

	# Search for LiveMedia label information.
	if glabel status | grep -q iso9660/${PRDNAME}; then
		LIVECD=0
	elif glabel status | grep -q ufs/liveboot; then
		LIVEUSB=0
	fi

	if [ "${LIVECD}" = 0 ]; then
		# Check if cd-rom is mounted else auto mount cd-rom.
		if [ ! -f "${CDPATH}/version" ]; then
			# Try to auto mount cd-rom.
			mkdir -p ${CDPATH}
			echo "Mounting CD-ROM Drive"
			mount_cd9660 /dev/cd0 ${CDPATH} > /dev/null 2>&1 || mount_cd9660 /dev/cd1 ${CDPATH} > /dev/null 2>&1
		fi
	elif [ "${LIVEUSB}" = 0 ]; then
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
	if [ -d "${CDPATH}" ]; then
		if [ -f "${CDPATH}/version" ]; then
			echo "Unmount CD/USB Drive"
			umount -f ${CDPATH} > /dev/null 2>&1
			rm -R ${CDPATH}
		fi
	fi
}

manual_cdmount()
{
	DRIVES=$(camcontrol devlist)
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
	DEVICE=$(cat ${tmpfile} | tr -d' ')
	mount /dev/${DEVICE}s1a ${CDPATH} > /dev/null 2>&1 || mount_cd9660 /dev/${DEVICE} ${CDPATH} > /dev/null 2>&1
	# Check if mounted cd/usb is accessible.
	if [ ! -f "${CDPATH}/version" ]; then
		# Re-try loop.
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
		# Ignore errors here since they are expected.
		# We need this section to try dealing with problematic disk metadata.
		set +e
		gmirror clear ${DISK} > /dev/null 2>&1
		zpool labelclear -f /dev/gpt/sysdisk${NUM} > /dev/null 2>&1
		zpool labelclear -f /dev/${DISK} > /dev/null 2>&1
		gpart destroy -F ${DISK} > /dev/null 2>&1
		set -e

		diskinfo ${DISK} | while read DISK sectorsize size sectors other
			do
				if [ ${WIPESECTORCOUNT} -gt ${sectors} ]; then
					WIPESECTORS=${sectors}
				else
					WIPESECTORS=${WIPESECTORCOUNT}
				fi
				# Delete MBR, GPT Primary, ZFS(L0L1)/other partition table.
				dd if=/dev/zero of=${DISK} bs=${sectorsize} count=${WIPESECTORS} > /dev/null 2>&1
				# Delete GEOM metadata, GPT Secondary(L2L3).
				dd if=/dev/zero of=${DISK} bs=${sectorsize} oseek=$(expr ${sectors} - ${WIPESECTORS}) count=${WIPESECTORS} > /dev/null 2>&1
			done
			NUM=$(expr $NUM + 1)
	done
}

# Create GPT/Partition on disk.
gptpart_init()
{
	DISKS=${DEVICE_LIST}
	swapdevlist=/tmp/swapdevlist.$$
	cat /dev/null > ${tmpfile}
	NUM="0"
	for DISK in ${DISKS}
	do
		echo "Creating GPT/Partition on ${DISK}"
		gpart create -s gpt ${DISK} > /dev/null

		# Create boot partition.
		if [ "${BOOT_MODE}" = 2 ]; then
			gpart add -a 4k -s 200M -t efi -l efiboot${NUM} ${DISK} > /dev/null
		fi
		gpart add -a 4k -s 512K -t freebsd-boot -l sysboot${NUM} ${DISK} > /dev/null

		if [ ! -z "${SWAP}" ]; then
			# Add swap partition to selected drives.
			gpart add -a 4m -s ${SWAP} -t freebsd-swap -l swap${NUM} ${DISK} > /dev/null
			# Generate explicit swap device list.
			echo "/dev/gpt/swap${NUM}" >> ${swapdevlist}
		fi
		gpart add -a 4m ${ZROOT_SIZE} -t freebsd-zfs -l sysdisk${NUM} ${DISK} > /dev/null
		# Generate the glabel device list.
		echo "/dev/gpt/sysdisk${NUM}" >> ${tmpfile}
		NUM=$(expr $NUM + 1)
	done

	GLABEL_DEVLIST=$(cat ${tmpfile})
	if [ ! -z "${SWAP}" ]; then
		SWAP_DEVLIST=$(cat ${swapdevlist})
		rm -f ${swapdevlist}
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
	if [ "${STRIPE}" = 0 ]; then
		RAID=""
	elif [ "${MIRROR}" = 0 ]; then
		RAID="mirror"
	elif [ "${RAIDZ1}" = 0 ]; then
		RAID="raidz1"
	elif [ "${RAIDZ2}" = 0 ]; then
		RAID="raidz2"
	elif [ "${RAIDZ3}" = 0 ]; then
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
		echo "Error: A problem has occurred while creating ${ZROOT} pool."
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
		if [ "${BOOT_MODE}" = 2 ]; then
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
		if [ "${SWAPMODE}" = 1 ]; then
			echo "Creating/adding swap Mirror..."
			if ! kldstat | grep -q geom_mirror; then
				kldload /boot/kernel/geom_mirror.ko
			fi
			gmirror label -b prefer swap ${SWAP_DEVLIST}
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
	if zpool status | grep -q ${ZROOT}; then
		zpool export ${ZROOT}
	fi

	# Final message.
	if [ $? -eq 0 ]; then
		cdialog --msgbox "$PRDNAME $APPNAME Successfully Installed!" 6 60
		exit 0
	else
		echo "Error: A problem has occurred during the installation."
		exit 1
	fi
}

install_sys_files()
{
	echo "Installing system files on ${ZROOT}..."

	# Install system files and discard unwanted folders.
	EXCLUDEDIRS="--exclude .snap/ --exclude resources/ --exclude zinstall.sh/ --exclude mnt/ --exclude dev/ --exclude var/ --exclude tmp/ --exclude cf/"
	tar ${EXCLUDEDIRS} -c -f - -C / . | tar -xpf - -C ${ALTROOT}

	# Copy files from live media source.
	copy_media_files

	# Install configuration file.
	mkdir -p ${ALTROOT}/cf/conf
	cp /conf.default/config.xml ${ALTROOT}/cf/conf

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

	if [ "${PLATFORM}" = "amd64" ]; then
		echo 'mlx4en_load="YES"' >> ${ALTROOT}/boot/loader.conf
	fi

	if [ "${SWAPMODE}" = 1 ]; then
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
	if [ "${PLATFORM}" = "amd64" ]; then
		echo "x64-full" > ${ALTROOT}/etc/platform
	elif [ "${PLATFORM}" = "i386" ]; then
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

copy_media_files()
{
	# Copy files from live media source.
	mkdir -p ${ALTROOT}/dev
	mkdir -p ${ALTROOT}/mnt
	mkdir -p ${ALTROOT}/tmp
	mkdir -p ${ALTROOT}/var
	chmod 1777 ${ALTROOT}/tmp
	mkdir -p ${ALTROOT}/boot/defaults
	cp -r ${CDPATH}/boot/* ${ALTROOT}/boot/
	cp -r ${CDPATH}/boot/defaults/* ${ALTROOT}/boot/defaults/
	cp -r ${CDPATH}/boot/kernel/* ${ALTROOT}/boot/kernel/

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
	gzip -d -f ${ALTROOT}/boot/kernel/kernel.gz

	# Decompress modules (legacy versions).
	cd ${ALTROOT}/boot/kernel
	for FILE in *.gz
	do
		if [ -f "${FILE}" ]; then
			gzip -d -f ${FILE}
		fi
	done
	cd
}

create_default_snapshot()
{
	echo "Creating system default snapshot..."
	zfs snapshot ${ZROOT}${DATASET}${BOOTENV}@factory-defaults
	echo "Done!"
	sleep 1
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
		if zpool status | grep -q ${ZROOT}; then
			zpool export -f ${ZROOT} > /dev/null 2>&1
		fi
	fi
}

install_yesno()
{
	DISKS=$(echo ${disklist})
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

	SWAP=$(cat ${tmpfile} | tr -d '"')
	
	if [ ! -z "${SWAP}" ]; then
		if [ "${choice}" = 2 ]; then
			if [ "${NWAY_MIRROR}" != 0 ]; then
				cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "System Swap mode selection" \
				--radiolist "Select system Swap mode, (default mirrored)." 10 50 4 \
				1 "Mirrored System Swap" on \
				2 "Multiple System Swap" off \
				2>${tmpfile}
				if [ 0 -ne $? ]; then
					exit 0;
				fi
				SWAPMODE=$(cat ${tmpfile})
			fi
		fi
	fi
}

menu_zrootsize()
{
	cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "Customize zroot pool partition" \
	--radiolist "Would you like to customize the ${ZROOT} partition size?" 10 60 4 \
	1 "Yes, I want to customize." off \
	2 "No, leave the defaults." on \
	2>${tmpfile}
	if [ 0 -ne $? ]; then
		exit 0
	fi

	CUSTOM_ROOT=$(cat ${tmpfile})
	if [ "${CUSTOM_ROOT}" = 1 ]; then
		cdialog --backtitle "$PRDNAME $APPNAME Installer" --title "Enter a desired zroot pool size" \
		--form "\nPlease enter a valid zroot pool size in GB, for example 10G, or leave empty to use all remaining disk space (default empty)." 0 0 0 \
		"Enter zroot pool size:" 1 1 "" 1 25 25 25 \
		2>${tmpfile}
		if [ 0 -ne $? ]; then
			exit 0
		fi

		rootsize=$(cat ${tmpfile})
		if [ ! -z "${rootsize}" ]; then
			ZROOT_SIZE="-s ${rootsize}"
		fi
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

	BOOT_MODE=$(cat ${tmpfile})
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

	VAL=$(echo ${VAL} | tr ' ' '\n'| grep -v '^cd' | sort_disklist)
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
		description=$(camcontrol identify ${media} | grep 'model' | awk '{print $3, $4, $5}')
	if [ -z "${description}" ] ; then
		# Re-try with "camcontrol inquiry" instead.
		description=$(camcontrol inquiry ${media} | grep -E '<*>' | cut -d '<' -f2 | cut -d '>' -f1)
		if [ -z "${description}" ] ; then
			description="Disk Drive"
		fi
	fi
		cap=$(diskinfo ${media} | awk '{
			capacity = $3;
			if (capacity >= 1099511627776) {
				printf("%.1f TiB", capacity / 1099511627776.0);
			} else if (capacity >= 1073741824) {
				printf("%.1f GiB", capacity / 1073741824.0);
			} else if (capacity >= 1048576) {
				printf("%.1f MiB", capacity / 1048576.0);
			} else {
				printf("%d Bytes", capacity);
			}}')
		VAL="${description} -- ${cap}"
	fi
}

menu_install()
{
	tmplist=/tmp/zfsinstall.$$
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
		eval "cdialog --title 'Choose destination drive(s)' --msgbox 'No drives available' 5 60" \
		2>${tmpfile}
		exit 1
	fi

	eval "cdialog --backtitle '$PRDNAME $APPNAME Installer'   --title 'Choose destination drive' \
		--checklist 'Select (one) or (more) drives where $PRDNAME should be installed, use arrow keys to navigate to the drive(s) for installation then select a drive with the spacebar.' \
		${menuheight} 60 ${items} ${list}" 2>${tmpfile}
	if [ 0 -ne $? ]; then
		exit 0
	fi

	if [ ! -z "${tmpfile}" ]; then
		#disklist=$(eval "echo $(cat ${tmpfile})")
		disklist=$(echo $(cat ${tmpfile}))
	fi

	count="1"
	for item in ${disklist}
	do
		DEV_COUNT=$(echo $count)
		count=$(expr $count + 1)
	done

	# Check for empty user input.
	if [ -z "${disklist}" ]; then
		if [ "${choice}" = 1 ]; then
			cdialog --msgbox "Notice: You need to select at least one disk!" 6 55; exit 1
		elif [ "${choice}" = 2 ]; then
			cdialog --msgbox "Notice: You need to select at least two disks!" 6 55; exit 1
		elif [ "${choice}" = 3 ]; then
			cdialog --msgbox "Notice: You need to select at least two disks!" 6 55; exit 1
		elif [ "${choice}" = 4 ]; then
			cdialog --msgbox "Notice: You need to select at least three disks!" 6 55; exit 1
		elif [ "${choice}" = 5 ]; then
			cdialog --msgbox "Notice: You need to select at least four disks!" 6 55; exit 1
		fi
	fi

	# Check for absolute minimum disks selection.
	if [ -n "${disklist}" ]; then
		if [ "${choice}" = 2 ]; then
			if [ "${DEV_COUNT}" -le 1 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of two disks!" 6 60; exit 1
			else
				if [ "${DEV_COUNT}" -ge 3 ]; then
					NWAY_MIRROR="0"
				fi
			fi
		elif [ "${choice}" = 3 ]; then
			if [ "${DEV_COUNT}" -le 1 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of two disks!" 6 60; exit 1
			fi
		elif [ "${choice}" = 4 ]; then
			if [ "${DEV_COUNT}" -le 2 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of three disks!" 6 60; exit 1
			fi
		elif [ "${choice}" = 5 ]; then
			if [ "${DEV_COUNT}" -le 3 ]; then
				cdialog --msgbox "Notice: You need to select a minimum of four disks!" 6 60; exit 1
			fi
		fi
	fi
	cat /dev/null > ${tmplist}
	for disk in ${disklist}; do
		echo "/dev/${disk}" >> ${tmplist}
	done

	DEVICE_LIST=$(cat ${tmplist})
	rm -f ${tmplist}
}

menu_main()
{
	while :
		do
			cdialog --backtitle "$PRDNAME $APPNAME Installer" --clear --title "$PRDNAME Installer Options" --cancel-label "Exit" --menu "" 11 50 10 \
			"1" "Install $PRDNAME $APPNAME on Stripe" \
			"2" "Install $PRDNAME $APPNAME on Mirror" \
			"3" "Install $PRDNAME $APPNAME on RAIDZ1" \
			"4" "Install $PRDNAME $APPNAME on RAIDZ2" \
			"5" "Install $PRDNAME $APPNAME on RAIDZ3" \
			2>${tmpfile}
			if [ 0 -ne $? ]; then
				exit 0
			fi
			choice=$(cat "${tmpfile}")
			case "${choice}" in
				1) STRIPE="0" && menu_zroot_create ;;
				2) MIRROR="0" && menu_zroot_create ;;
				3) RAIDZ1="0" && menu_zroot_create ;;
				4) RAIDZ2="0" && menu_zroot_create ;;
				5) RAIDZ3="0" && menu_zroot_create ;;
			esac
		done
}
menu_main
