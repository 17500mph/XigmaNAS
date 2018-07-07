#!/bin/sh
#
# Part of XigmaNAS (http://www.xigmanas.com).
# Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
# All rights reserved.
#

. /etc/rc.subr
. /etc/email.subr

name="smartdreport"

load_rc_config "${name}"

# Send output of smartctl -a to as message.
_message=`cat`
_report=`/usr/local/sbin/smartctl -a -d ${SMARTD_DEVICETYPE} ${SMARTD_DEVICE}`

# Send email.
send_email "${SMARTD_ADDRESS}" "${SMARTD_SUBJECT}" "${_message} ${_report}"
