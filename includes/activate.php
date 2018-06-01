<?php
/**
 * Our activation call
 *
 * @package WooGDPRUserOptIns
 */

// Declare our namespace.
namespace LiquidWeb\WooGDPRUserOptIns\Activate;

// Set our aliases.
use LiquidWeb\WooGDPRUserOptIns as Core;
use LiquidWeb\WooGDPRUserOptIns\Helpers as Helpers;

/**
 * Our inital setup function when activated.
 *
 * @return void
 */
function activate() {

	// Create our option key with the initial defaults.
	update_option( Core\OPTION_NAME, Helpers\get_default_fields(), 'no' );

	// Include our action so that we may add to this later.
	do_action( 'lw_woo_gdpr_optins_activate_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_activation_hook( Core\FILE, __NAMESPACE__ . '\activate' );
