<?php
/*
 * vpn_awg_tunnels_import.php
 *
 * part of pfSense (https://www.pfsense.org)
 * Cloned/extended from pfSense-pkg-WireGuard for AmneziaWG.
 *
 * Lets the user paste a full AmneziaWG (or plain WireGuard) .conf and have
 * it parsed straight into a saved Tunnel + Peer(s), reusing the same
 * awgconfig parser and validators as the normal GUI forms.
 */

##|+PRIV
##|*IDENT=page-vpn-amneziawg
##|*NAME=VPN: AmneziaWG: Import
##|*DESCR=Allow access to the 'VPN: AmneziaWG' page.
##|*MATCH=vpn_awg_tunnels_import.php*
##|-PRIV

// pfSense includes
require_once('functions.inc');
require_once('guiconfig.inc');

// AmneziaWG includes
require_once('amneziawg/includes/awg.inc');
require_once('amneziawg/includes/awg_guiconfig.inc');

global $wgg;

// Initialize $wgg state
awg_globals();

$pconfig = array();
$import_success = false;

if ($_POST && isset($_POST['act']) && $_POST['act'] == 'import') {
	$pconfig = $_POST;

	$res = awg_do_tunnel_import_post($_POST);

	$input_errors = $res['input_errors'];

	if (empty($input_errors)) {
		$import_success = true;
		$imported_tun_name = $res['tun_name'];
		$imported_peer_count = $res['peer_count'];
	}
}

$shortcut_section = 'amneziawg';

$pgtitle = array(gettext('VPN'), gettext('AmneziaWG'), gettext('Tunnels'), gettext('Import'));
$pglinks = array('', '/awg/vpn_awg_tunnels.php', '/awg/vpn_awg_tunnels.php', '@self');

$tab_array = array();
$tab_array[] = array(gettext('Tunnels'), true, '/awg/vpn_awg_tunnels.php');
$tab_array[] = array(gettext('Peers'), false, '/awg/vpn_awg_peers.php');
$tab_array[] = array(gettext('Settings'), false, '/awg/vpn_awg_settings.php');
$tab_array[] = array(gettext('Status'), false, '/awg/status_amneziawg.php');

$service_hook = 'amneziawgd';
include('head.inc');

awg_print_service_warning();

awg_print_config_apply_box();

if (!empty($input_errors)) {
	print_input_errors($input_errors);
}

if ($import_success) {
	print_info_box(
		sprintf(
			gettext('Imported tunnel "%s" with %d peer(s). Review it under Tunnels, then apply changes.'),
			htmlspecialchars($imported_tun_name),
			$imported_peer_count
		),
		'success',
		null
	);
}

display_top_tabs($tab_array);

$form = new Form(false);

$section = new Form_Section(gettext('Import Configuration'));

$section->addInput(new Form_StaticText(
	'Hint',
	gettext('Paste a full AmneziaWG (or plain WireGuard-compatible) configuration file below. Both the [Interface] section (PrivateKey, Address, MTU, Jc/Jmin/Jmax/S1/S2/H1-H4/I1-I5) and any [Peer] section(s) (PublicKey, PresharedKey, Endpoint, AllowedIPs, PersistentKeepalive) will be parsed and saved as a new Tunnel and its Peer(s). Nothing is written until you click Import, and nothing is saved at all if any field fails validation.')
));

$section->addInput(new Form_Input(
	'descr',
	gettext('Tunnel Description'),
	'text',
	$pconfig['descr'] ?? '',
	['placeholder' => 'e.g. NL exit node']
))->setHelp(gettext('Optional. Defaults to "Imported tunnel" if left blank.'));

$section->addInput(new Form_Input(
	'peer_descr',
	gettext('Peer Description'),
	'text',
	$pconfig['peer_descr'] ?? '',
	['placeholder' => 'e.g. tribukvy NL']
))->setHelp(gettext('Optional. Applied to every [Peer] section found (defaults to "Imported peer" if left blank).'));

$section->addInput(new Form_Textarea(
	'config',
	gettext('Configuration'),
	$pconfig['config'] ?? ''
))->setHelp(gettext('Paste the full contents of a .conf file here, including [Interface] and [Peer] sections.'))
  ->setAttribute('rows', 22)
  ->setAttribute('style', 'font-family: monospace;');

$form->add($section);

$form->addGlobal(new Form_Input(
	'act',
	'',
	'hidden',
	'import'
));

print($form);

?>

<nav class="action-buttons">
	<button type="submit" id="importform" name="importform" class="btn btn-primary btn-sm" value="import" title="<?=gettext('Import Configuration')?>">
		<i class="fa-solid fa-file-import icon-embed-btn"></i>
		<?=gettext('Import')?>
	</button>
</nav>

<script type="text/javascript">
//<![CDATA[
events.push(function() {
	$('#importform').click(function(event) {
		$(form).submit();
	});
});
//]]>
</script>

<?php
include('amneziawg/includes/awg_foot.inc');
include('foot.inc');
?>
