<?php
/*
 * amneziawg_edit.php - Add/Edit tunnel with all AmneziaWG parameters
 */
require_once("guiconfig.inc");
require_once("/usr/local/pkg/amneziawg.inc");

$pgtitle = array("Services", "AmneziaWG", "Edit");
include("head.inc");

$idx = $_GET['idx'] ?? null;
$tunnel = [];

// Load existing tunnel if editing
if ($idx !== null && isset($config['installedpackages']['amneziawg']['config'][$idx])) {
    $tunnel = $config['installedpackages']['amneziawg']['config'][$idx];
}

// Handle save
if ($_POST) {
    $tunnel = $_POST;
    $tunnel['idx'] = $idx;
    awg_save_tunnel($tunnel);
    header("Location: amneziawg.php");
    exit;
}

// Handle delete
if ($_GET['act'] == 'del' && $idx !== null) {
    awg_delete_tunnel($idx);
    header("Location: amneziawg.php");
    exit;
}
?>

    <form method="post" class="form-horizontal">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title">Tunnel Settings</h2>
            </div>
            <div class="panel-body">
                <!-- Basic fields -->
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="name" value="<?=htmlspecialchars($tunnel['name'] ?? '')?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Private Key</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="privatekey" value="<?=htmlspecialchars($tunnel['privatekey'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Address</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="address" value="<?=htmlspecialchars($tunnel['address'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">DNS</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="dns" value="<?=htmlspecialchars($tunnel['dns'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">MTU</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="mtu" value="<?=htmlspecialchars($tunnel['mtu'] ?? '')?>">
                    </div>
                </div>

                <hr/>
                <h4>AmneziaWG Obfuscation Parameters</h4>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Jc</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="jc" placeholder="e.g., 5" value="<?=htmlspecialchars($tunnel['jc'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Jmin</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="jmin" value="<?=htmlspecialchars($tunnel['jmin'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Jmax</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="jmax" value="<?=htmlspecialchars($tunnel['jmax'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">S1</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="s1" value="<?=htmlspecialchars($tunnel['s1'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">S2</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="s2" value="<?=htmlspecialchars($tunnel['s2'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">H1</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="h1" value="<?=htmlspecialchars($tunnel['h1'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">H2</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="h2" value="<?=htmlspecialchars($tunnel['h2'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">H3</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="h3" value="<?=htmlspecialchars($tunnel['h3'] ?? '')?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">H4</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="h4" value="<?=htmlspecialchars($tunnel['h4'] ?? '')?>">
                    </div>
                </div>
                <!-- Peers (placeholder - для полноценного редактирования пиров можно расширить) -->
                <hr/>
                <h4>Peers</h4>
                <div class="alert alert-info">
                    Peer management will be added in a future update. For now, use the configuration file directly.
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" class="btn btn-primary" value="Save">
                <a href="amneziawg.php" class="btn btn-default">Cancel</a>
            </div>
        </div>
    </form>
<?php include("foot.inc"); ?>