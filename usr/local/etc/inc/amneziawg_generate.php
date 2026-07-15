<?php
/*
 * amneziawg_generate.php - Generates configs and starts/stops the service
 * Called by the rc.d script.
 */
require_once("/usr/local/pkg/amneziawg.inc");

$action = $argv[1] ?? 'start';
if ($action === 'start') {
    awg_generate_configs();
    $tunnels = $config['installedpackages']['amneziawg']['config'] ?? [];
    foreach ($tunnels as $tunnel) {
        $name = $tunnel['name'] ?? 'awg0';
        $conf = AWG_CONF_DIR . "/{$name}.conf";
        if (file_exists($conf)) {
            // Run in background like other pfSense services
            mwexec_bg(AWG_BINARY . " --config " . escapeshellarg($conf));
        }
    }
} elseif ($action === 'stop') {
    mwexec("pkill -f " . escapeshellarg(AWG_BINARY));
}