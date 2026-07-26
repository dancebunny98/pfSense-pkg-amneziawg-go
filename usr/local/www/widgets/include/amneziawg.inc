<?php
/*
 * amneziawg.inc (widget include)
 *
 * Registers the AmneziaWG dashboard widget with pfSense's widget system.
 * Cloned from widgets/include/wireguard.inc.
 *
 * NOTE: this file is a best-effort reconstruction (only its filename was
 * confirmed to exist on the live system, its content was not). Dashboard
 * widgets are cosmetic - if this does not register cleanly, the rest of
 * the AmneziaWG package (menu, tunnels, peers, settings, status) is fully
 * independent and unaffected. Verify against /usr/local/www/widgets/include/
 * on your own system and adjust field names if pfSense complains.
 */

$amneziawg_widgetkey = "amneziawg";

$amneziawg_widgetconfig = array();
$amneziawg_widgetconfig['widgetkey'] = $amneziawg_widgetkey;
$amneziawg_widgetconfig['name'] = gettext("AmneziaWG");
$amneziawg_widgetconfig['configurable'] = false;

?>
