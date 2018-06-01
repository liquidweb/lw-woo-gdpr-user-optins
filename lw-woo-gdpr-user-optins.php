<?php
/**
 * Plugin Name: WooCommerce GDPR User Opt-Ins
 * Plugin URI:  https://github.com/liquidweb/lw-woo-gdpr-user-optins
 * Description: A toolset to allow WooCommerce store owners to create and manage user opt-in data.
 * Version:     0.1.0
 * Author:      Liquid Web
 * Author URI:  https://www.liquidweb.com
 * Text Domain: lw-woo-gdpr-user-optins
 * Domain Path: /languages
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 *
 * @package WooGDPRUserOptIns
 */

// Declare our namespace.
namespace LiquidWeb\WooGDPRUserOptIns;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Define our version.
define( __NAMESPACE__ . '\VERS', '0.1.0' );

// Define our file base.
define( __NAMESPACE__ . '\BASE', plugin_basename( __FILE__ ) );

// Plugin Folder URL.
define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );

// Plugin root file.
define( __NAMESPACE__ . '\FILE', __FILE__ );

// Options database name.
define( __NAMESPACE__ . '\OPTION_NAME', 'lw_woo_gdpr_optins_fields' );

// User meta key prefix.
define( __NAMESPACE__ . '\META_PREFIX', 'lw_woo_gdrp_user_optin_' );

// Set our assets directory constant.
define( __NAMESPACE__ . '\ASSETS_URL', URL . 'assets' );

// Set our front menu endpoint constant.
define( __NAMESPACE__ . '\FRONT_VAR', 'privacy-data' );

// Set our tab base slug constant.
define( __NAMESPACE__ . '\TAB_BASE', 'gdpr_user_optins' );

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
require_once __DIR__ . '/includes/admin.php';
require_once __DIR__ . '/includes/settings-tab.php';
require_once __DIR__ . '/includes/ajax-actions.php';
