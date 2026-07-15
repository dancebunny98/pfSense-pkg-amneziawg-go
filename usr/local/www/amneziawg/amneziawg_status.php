<?php
/*
 * amneziawg_status.php - Show AmneziaWG status/version
 */
require_once("guiconfig.inc");
$pgtitle = array("Status", "AmneziaWG");
include("head.inc");
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">AmneziaWG Status</h2>
        </div>
        <div class="panel-body">
            <pre><?=htmlspecialchars(shell_exec("/usr/local/bin/amneziawg-go --version 2>&1"))?></pre>
        </div>
    </div>
<?php include("foot.inc"); ?>