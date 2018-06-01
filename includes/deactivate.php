<?php
/**
 * Our deactivation call
 *
 * @package WooGDPRUserOptIns
 */

// Declare our namespace.
namespace LiquidWeb\WooGDPRUserOptIns\Deactivate;

// Set our aliases.
use LiquidWeb\WooGDPRUserOptIns as Core;

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
register_deactivation_hook( Core\FILE, __NAMESPACE__ . '\deactivate' );
