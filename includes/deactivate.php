<?php
/**
 * Our deactivation call
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\Deactivate;

/**
 * Delete various options when deactivating the plugin.
 *
 * @return void
 */
function deactivate() {

	// Include our action so that we may add to this later.
	do_action( 'lw_woo_gdpr_optins_deactivate_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_deactivation_hook( LWWOOGDPR_OPTINS_FILE, __NAMESPACE__ . '\deactivate' );

