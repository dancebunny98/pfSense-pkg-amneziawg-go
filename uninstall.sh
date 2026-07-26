#!/bin/sh
#
# uninstall.sh - cleanly removes the AmneziaWG web GUI package from pfSense.
#
# Run this from bash on pfSense:
#
#   bash
#   sh /root/pfSense-pkg-AmneziaWG/uninstall.sh
#
# By default this KEEPS your tunnel/peer configuration in config.xml
# (controlled by the "Keep Configuration" option on the AmneziaWG Settings
# page) so you can reinstall later without losing anything. Pass
# --purge-config to also wipe all AmneziaWG configuration from config.xml.
#
# This does NOT remove amneziawg-go / amnezia-tools / amnezia-kmod (those
# are separate FreeBSD packages, remove them yourself with `pkg delete` if
# you no longer need them - other things might depend on them).

set -e

if [ "$(id -u)" != "0" ]; then
	echo "Must be run as root." >&2
	exit 1
fi

PURGE_CONFIG=0
if [ "$1" = "--purge-config" ]; then
	PURGE_CONFIG=1
fi

echo "==> Running package deinstaller (stops service, tears down tunnels, removes ifgroup/ACL/earlyshellcmd)"
php -r "
require_once('config.inc');
require_once('/usr/local/pkg/amneziawg/includes/awg_globals.inc');
require_once('/usr/local/pkg/amneziawg/includes/awg_install.inc');
awg_deinstall();
echo \"    awg_deinstall() completed.\n\";
" || echo "    (deinstaller reported an error, continuing with file cleanup anyway)"

if [ "$PURGE_CONFIG" = "1" ]; then
	echo "==> --purge-config given: wiping all AmneziaWG config.xml data"
	php -r "
	require_once('config.inc');
	config_del_path('installedpackages/amneziawg');
	write_config('Purged AmneziaWG configuration (uninstall.sh --purge-config)', true);
	echo \"    Removed installedpackages/amneziawg from config.xml.\n\";
	"
else
	echo "==> Keeping AmneziaWG configuration in config.xml (run with --purge-config to remove it too)"
fi

echo "==> Removing package files"
rm -rf /usr/local/pkg/amneziawg
rm -f  /usr/local/pkg/amneziawg.xml
rm -rf /usr/local/www/awg
rm -f  /usr/local/www/shortcuts/pkg_amneziawg.inc
rm -f  /usr/local/www/widgets/include/amneziawg.inc
rm -f  /usr/local/www/widgets/widgets/amneziawg.widget.php
rm -f  /etc/inc/priv/amneziawg.priv.inc
rm -f  /usr/local/bin/php_awg
rm -f  /usr/local/etc/rc.d/amneziawgd

echo "==> Restarting webConfigurator"
/etc/rc.restart_webgui

echo ""
echo "AmneziaWG web GUI removed."
if [ "$PURGE_CONFIG" = "0" ]; then
	echo "Your tunnel/peer configuration is still in config.xml under <installedpackages><amneziawg>."
	echo "Reinstall the package later and it will pick this data back up automatically."
	echo "To wipe it too, re-run: sh uninstall.sh --purge-config"
fi
echo ""
echo "Note: amneziawg-go / amnezia-tools / amnezia-kmod FreeBSD packages were NOT removed."
echo "Remove them yourself if no longer needed: pkg delete amneziawg-go amnezia-tools amnezia-kmod"
