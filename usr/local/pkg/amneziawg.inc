<?php
/*
 * amneziawg.inc - Core functions for AmneziaWG integration in pfSense
 */
require_once("guiconfig.inc");
require_once("functions.inc");

define('AWG_CONF_DIR', '/var/etc/amneziawg');
define('AWG_BINARY', '/usr/local/bin/amneziawg-go');

/**
 * Save or update a tunnel configuration
 */
function awg_save_tunnel($tunnel) {
    global $config;
    if (!isset($config['installedpackages']['amneziawg']['config'])) {
        $config['installedpackages']['amneziawg']['config'] = [];
    }
    $idx = $tunnel['idx'] ?? null;
    if ($idx !== null && isset($config['installedpackages']['amneziawg']['config'][$idx])) {
        $config['installedpackages']['amneziawg']['config'][$idx] = $tunnel;
    } else {
        $config['installedpackages']['amneziawg']['config'][] = $tunnel;
    }
    write_config("Saved AmneziaWG tunnel configuration");
    awg_generate_configs();
    awg_restart_service();
}

/**
 * Delete a tunnel by index
 */
function awg_delete_tunnel($idx) {
    global $config;
    if (isset($config['installedpackages']['amneziawg']['config'][$idx])) {
        unset($config['installedpackages']['amneziawg']['config'][$idx]);
        $config['installedpackages']['amneziawg']['config'] = array_values($config['installedpackages']['amneziawg']['config']);
        write_config("Deleted AmneziaWG tunnel");
        awg_generate_configs();
        awg_restart_service();
    }
}

/**
 * Generate WireGuard configuration files for all tunnels
 */
function awg_generate_configs() {
    global $config;
    $tunnels = $config['installedpackages']['amneziawg']['config'] ?? [];
    if (!is_dir(AWG_CONF_DIR)) {
        mkdir(AWG_CONF_DIR, 0750, true);
    }
    foreach ($tunnels as $tunnel) {
        $name = $tunnel['name'] ?? 'awg0';
        $conf = "[Interface]\n";
        $conf .= "PrivateKey = " . ($tunnel['privatekey'] ?? '') . "\n";
        $conf .= "Address = " . ($tunnel['address'] ?? '') . "\n";
        if (!empty($tunnel['dns'])) $conf .= "DNS = " . $tunnel['dns'] . "\n";
        if (!empty($tunnel['mtu'])) $conf .= "MTU = " . $tunnel['mtu'] . "\n";
        // AmneziaWG obfuscation parameters
        $conf .= "Jc = " . ($tunnel['jc'] ?? '') . "\n";
        $conf .= "Jmin = " . ($tunnel['jmin'] ?? '') . "\n";
        $conf .= "Jmax = " . ($tunnel['jmax'] ?? '') . "\n";
        $conf .= "S1 = " . ($tunnel['s1'] ?? '') . "\n";
        $conf .= "S2 = " . ($tunnel['s2'] ?? '') . "\n";
        $conf .= "H1 = " . ($tunnel['h1'] ?? '') . "\n";
        $conf .= "H2 = " . ($tunnel['h2'] ?? '') . "\n";
        $conf .= "H3 = " . ($tunnel['h3'] ?? '') . "\n";
        $conf .= "H4 = " . ($tunnel['h4'] ?? '') . "\n";
        $conf .= "Table = off\n";

        // Peers
        $peers = $tunnel['peers']['item'] ?? [];
        foreach ($peers as $peer) {
            $conf .= "\n[Peer]\n";
            $conf .= "PublicKey = " . ($peer['publickey'] ?? '') . "\n";
            if (!empty($peer['endpoint'])) $conf .= "Endpoint = " . $peer['endpoint'] . "\n";
            if (!empty($peer['allowedips'])) $conf .= "AllowedIPs = " . $peer['allowedips'] . "\n";
            if (!empty($peer['persistentkeepalive'])) $conf .= "PersistentKeepalive = " . $peer['persistentkeepalive'] . "\n";
        }

        file_put_contents(AWG_CONF_DIR . "/{$name}.conf", $conf);
    }
}

/**
 * Restart the amneziawg service
 */
function awg_restart_service() {
    mwexec("/usr/local/etc/rc.d/amneziawg restart");
}