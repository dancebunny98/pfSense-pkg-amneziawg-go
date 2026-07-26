<?php
/*
 * pkg_amneziawg.inc
 *
 * Shortcut definitions for AmneziaWG (cloned from pkg_wireguard.inc).
 * Consumed by guiconfig.inc / head.inc wherever a page sets
 * $shortcut_section = 'amneziawg';
 *
 * This file was not present in the source tree that was cloned from the
 * live pfSense install (only its filename was discovered via `find`), so
 * it is reconstructed here following the same $shortcuts[] array shape
 * used by every other pfSense package shortcut file.
 */

$shortcuts['amneziawg'] = array();
$shortcuts['amneziawg']['main'] = "/awg/vpn_awg_tunnels.php";
$shortcuts['amneziawg']['log'] = "";
$shortcuts['amneziawg']['status'] = "/awg/status_amneziawg.php";

?>
