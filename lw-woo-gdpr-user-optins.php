<?php
/**
 * Plugin Name: WooCommerce GDPR User Opt-Ins
 * Plugin URI:  https://github.com/liquidweb/lw-woo-gdpr-user-optins
 * Description: A toolset to allow WooCommerce store owners to create and manage user opt-in data.
 * Version:     0.0.1
 * Author:      Liquid Web
 * Author URI:  https://www.liquidweb.com
 * Text Domain: lw-woo-gdpr-user-optins
 * Domain Path: /languages
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Define our version.
define( 'LWWOOGDPR_OPTINS_VERS', '0.0.1' );

// Handle the constants being set.
set_constants();

// Go and load our files.
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/activate.php';
require_once __DIR__ . '/includes/deactivate.php';
require_once __DIR__ . '/includes/uninstall.php';

// Load the files with ongoing functionality.
require_once __DIR__ . '/includes/query-mods.php';
require_once __DIR__ . '/includes/layouts.php';
require_once __DIR__ . '/includes/account.php';
require_once __DIR__ . '/includes/checkout.php';
require_once __DIR__ . '/includes/settings-tab.php';
require_once __DIR__ . '/includes/ajax-actions.php';


/**
 * Define all the constants used in the plugin.
 */
function set_constants() {

	// Plugin Folder URL.
	if ( ! defined( 'LWWOOGDPR_OPTINS_URL' ) ) {
		define( 'LWWOOGDPR_OPTINS_URL', plugin_dir_url( __FILE__ ) );
	}

	// Plugin root file.
	if ( ! defined( 'LWWOOGDPR_OPTINS_FILE' ) ) {
		define( 'LWWOOGDPR_OPTINS_FILE', __FILE__ );
	}

	// Options database name.
	if ( ! defined( 'LWWOOGDPR_OPTINS_OPTION_NAME' ) ) {
		define( 'LWWOOGDPR_OPTINS_OPTION_NAME', 'lw_woo_gdpr_optins_fields' );
	}

	// User meta key prefix.
	if ( ! defined( 'LWWOOGDPR_OPTINS_META_PREFIX' ) ) {
		define( 'LWWOOGDPR_OPTINS_META_PREFIX', 'lw_woo_gdrp_user_optin_' );
	}

	// Set our assets directory constant.
	if ( ! defined( 'LWWOOGDPR_OPTINS_ASSETS_URL' ) ) {
		define( 'LWWOOGDPR_OPTINS_ASSETS_URL', LWWOOGDPR_OPTINS_URL . 'assets' );
	}

	// Set our front menu endpoint constant.
	if ( ! defined( 'LWWOOGDPR_OPTINS_FRONT_VAR' ) ) {
		define( 'LWWOOGDPR_OPTINS_FRONT_VAR', 'privacy-data' );
	}

	// Set our tab base slug constant.
	if ( ! defined( 'LWWOOGDPR_OPTINS_TAB_BASE' ) ) {
		define( 'LWWOOGDPR_OPTINS_TAB_BASE', 'gdpr_user_optins' );
	}
}
