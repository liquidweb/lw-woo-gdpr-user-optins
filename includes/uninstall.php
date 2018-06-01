<?php
/**
 * Our uninstall call
 *
 * @package WooGDPRUserOptIns
 */

// Declare our namespace.
namespace LiquidWeb\WooGDPRUserOptIns\Uninstall;

// Set our aliases.
use LiquidWeb\WooGDPRUserOptIns as Core;

/**
 * Delete various options when uninstalling the plugin.
 *
 * @return void
 */
function uninstall() {

	// Delete any saved fields.
	delete_option( Core\OPTION_NAME );

	// Include our action so that we may add to this later.
	do_action( 'lw_woo_gdpr_optins_uninstall_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_uninstall_hook( Core\FILE, __NAMESPACE__ . '\uninstall' );
