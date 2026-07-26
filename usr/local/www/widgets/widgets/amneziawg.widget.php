<?php
/*
 * amneziawg.widget.php
 *
 * Dashboard widget for AmneziaWG. Cloned from widgets/widgets/wireguard.widget.php.
 *
 * NOTE: best-effort reconstruction, see amneziawg.inc for details. This
 * widget is purely cosmetic (Dashboard summary) - the package works fully
 * without it via VPN > AmneziaWG in the main menu.
 */

require_once('amneziawg/includes/awg.inc');

global $wgg;

awg_globals();

$tunnels = config_get_path('installedpackages/amneziawg/tunnels/item', []);

?>

<div class="table-responsive">
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th><?=gettext('Tunnel')?></th>
				<th><?=gettext('Status')?></th>
				<th><?=gettext('Last Handshake')?></th>
			</tr>
		</thead>
		<tbody>
<?php if (empty($tunnels)): ?>
			<tr>
				<td colspan="3"><?=gettext('No AmneziaWG tunnels have been configured.')?></td>
			</tr>
<?php else: foreach ($tunnels as $tunnel): ?>
			<tr>
				<td><?=htmlspecialchars($tunnel['name'])?> (<?=htmlspecialchars($tunnel['descr'])?>)</td>
				<td><?=($tunnel['enabled'] == 'yes') ? gettext('Enabled') : gettext('Disabled')?></td>
				<td><?=gettext('See Status > AmneziaWG for details')?></td>
			</tr>
<?php endforeach; endif; ?>
		</tbody>
	</table>
</div>
