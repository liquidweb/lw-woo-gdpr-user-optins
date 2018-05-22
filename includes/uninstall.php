<?php
/**
 * Our uninstall call
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\Uninstall;

/**
 * Delete various options when uninstalling the plugin.
 *
 * @return void
 */
function uninstall() {

	// Delete any saved fields.
	delete_option( LWWOOGDPR_OPTINS_OPTION_NAME );

	// Include our action so that we may add to this later.
	do_action( 'lw_woo_gdpr_optins_uninstall_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_uninstall_hook( LWWOOGDPR_OPTINS_FILE, __NAMESPACE__ . '\uninstall' );

