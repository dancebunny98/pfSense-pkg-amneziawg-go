<?php
/*
 * amneziawg_peers.php - Peer management (placeholder)
 */
require_once("guiconfig.inc");
$pgtitle = array("Services", "AmneziaWG", "Peers");
include("head.inc");
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">Peers</h2>
        </div>
        <div class="panel-body">
            <div class="alert alert-info">
                Peers are currently managed as part of the tunnel configuration.
                Add or edit peers in the <a href="amneziawg_edit.php">tunnel editor</a>.
            </div>
        </div>
    </div>
<?php include("foot.inc"); ?>