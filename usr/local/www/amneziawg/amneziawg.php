<?php
/*
 * amneziawg.php - Tunnel list page
 */
require_once("guiconfig.inc");
require_once("/usr/local/pkg/amneziawg.inc");

$pgtitle = array("Services", "AmneziaWG");
include("head.inc");

$tunnels = $config['installedpackages']['amneziawg']['config'] ?? [];
?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">AmneziaWG Tunnels</h2>
        </div>
        <div class="panel-body">
            <a href="amneziawg_edit.php" class="btn btn-success">
                <i class="fa fa-plus"></i> Add Tunnel
            </a>
            <br/><br/>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Peers</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tunnels as $idx => $tunnel): ?>
                    <tr>
                        <td><?=htmlspecialchars($tunnel['name'] ?? '')?></td>
                        <td><?=htmlspecialchars($tunnel['address'] ?? '')?></td>
                        <td><?=count($tunnel['peers']['item'] ?? [])?></td>
                        <td>
                            <a class="btn btn-xs btn-primary" href="amneziawg_edit.php?idx=<?=$idx?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a class="btn btn-xs btn-danger" href="amneziawg_edit.php?act=del&idx=<?=$idx?>" onclick="return confirm('Delete this tunnel?')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include("foot.inc"); ?>