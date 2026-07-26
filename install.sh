#!/bin/sh
#
# install.sh - installs the AmneziaWG web GUI package files onto pfSense.
#
# Run this from bash on pfSense, from the directory this script lives in
# (i.e. the extracted archive root, containing usr/ and etc/ subfolders):
#
#   bash
#   cd /root/pfSense-pkg-AmneziaWG
#   sh install.sh
#
# This script ONLY installs the web GUI (PHP pages, package XML, priv,
# shortcuts, widget). It does NOT install amneziawg-go / amnezia-tools /
# amnezia-kmod - install those .pkg files with `pkg add` first (see
# README.md, section "1. Install the AmneziaWG packages").

set -e

if [ "$(id -u)" != "0" ]; then
	echo "Must be run as root." >&2
	exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if [ ! -d "${SCRIPT_DIR}/usr/local/pkg/amneziawg" ]; then
	echo "Can't find usr/local/pkg/amneziawg next to this script - run it from the extracted archive root." >&2
	exit 1
fi

echo "==> Checking prerequisites (amneziawg-go / amnezia-tools)"
if ! pkg info amneziawg-go >/dev/null 2>&1; then
	echo "WARNING: amneziawg-go is not installed via pkg. Install it first (see README.md)." >&2
fi
if ! pkg info amnezia-tools >/dev/null 2>&1; then
	echo "WARNING: amnezia-tools is not installed via pkg. Install it first (see README.md)." >&2
fi

echo "==> Copying package files"
mkdir -p /usr/local/pkg/amneziawg /usr/local/www/awg
cp -f "${SCRIPT_DIR}/usr/local/pkg/amneziawg.xml" /usr/local/pkg/amneziawg.xml
cp -rf "${SCRIPT_DIR}/usr/local/pkg/amneziawg/"* /usr/local/pkg/amneziawg/
cp -rf "${SCRIPT_DIR}/usr/local/www/awg/"* /usr/local/www/awg/
cp -f "${SCRIPT_DIR}/usr/local/www/shortcuts/pkg_amneziawg.inc" /usr/local/www/shortcuts/pkg_amneziawg.inc
cp -f "${SCRIPT_DIR}/usr/local/www/widgets/include/amneziawg.inc" /usr/local/www/widgets/include/amneziawg.inc
cp -f "${SCRIPT_DIR}/usr/local/www/widgets/widgets/amneziawg.widget.php" /usr/local/www/widgets/widgets/amneziawg.widget.php
cp -f "${SCRIPT_DIR}/etc/inc/priv/amneziawg.priv.inc" /etc/inc/priv/amneziawg.priv.inc

echo "==> Verifying PHP syntax"
for f in $(find /usr/local/www/awg /usr/local/pkg/amneziawg -name "*.php" -o -name "*.inc"); do
	if ! php -l "$f" >/dev/null; then
		echo "SYNTAX ERROR in $f - aborting before touching the running config." >&2
		php -l "$f"
		exit 1
	fi
done
echo "    All files OK."

echo "==> Creating config directory"
mkdir -p /usr/local/etc/amneziawg
chmod 700 /usr/local/etc/amneziawg

echo "==> Running package installer (registers service, ifgroup, unbound ACL, earlyshellcmd, php_awg)"
php -r "
require_once('config.inc');
require_once('/usr/local/pkg/amneziawg/includes/awg_globals.inc');
require_once('/usr/local/pkg/amneziawg/includes/awg_install.inc');
awg_install();
echo \"    awg_install() completed.\n\";
"

echo "==> Restarting webConfigurator"
/etc/rc.restart_webgui

echo ""
echo "Done. Open the pfSense GUI (hard-refresh with Ctrl+F5) and check:"
echo "  VPN -> AmneziaWG"
echo "  Status -> AmneziaWG"
