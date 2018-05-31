<?php
/**
 * General admin functionality.
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\Admin;

use LiquidWeb\WooGDPRUserOptIns as Core;
use LiquidWeb\WooGDPRUserOptIns\Helpers as Helpers;
use LiquidWeb\WooGDPRUserOptIns\Layouts as Layouts;

/**
 * Start our engines.
 */
add_action( 'admin_notices', __NAMESPACE__ . '\display_admin_notices' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\load_settings_assets' );
add_filter( 'admin_body_class', __NAMESPACE__ . '\load_admin_body_class' );
add_filter( 'plugin_action_links', __NAMESPACE__ . '\quick_link', 10, 2 );

/**
 * Set up the admin notices.
 *
 * @return mixed
 */
function display_admin_notices() {

	// Confirm we are on the settings tab.
	if ( ! Helpers\maybe_admin_settings_tab() ) {
		return;
	}

	// Now check to make sure we have our key.
	if ( empty( $_GET['lw-woo-gdpr-action'] ) ) {
		return;
	}

	// Handle the success notice first.
	if ( ! empty( $_GET['success'] ) ) {

		// Determine my error text.
		$msg_code   = ! empty( $_GET['message'] ) ? sanitize_text_field( $_GET['message'] ) : 'success';
		$msg_text   = Helpers\notice_text( $msg_code );

		// Output the message along with the dismissable.
		echo Layouts\admin_message_markup( $msg_txt, 'success' ); // WPCS: XSS ok.

		// And be done.
		return;
	}

	// Figure out my error code.
	$error_code = ! empty( $_GET['errcode'] ) ? esc_attr( $_GET['errcode'] ) : 'unknown';

	// Determine my error text.
	$error_text = Helpers\notice_text( $error_code );

	// Output the message along with the dismissable.
	echo Layouts\admin_message_markup( $error_text ); // WPCS: XSS ok.
}

/**
 * Load our admin side JS and CSS.
 *
 * @param $hook  Admin page hook we are current on.
 *
 * @return void
 */
function load_settings_assets( $hook ) {

	// Confirm we are on the settings tab.
	if ( ! Helpers\maybe_admin_settings_tab( $hook ) ) {
		return;
	}

	// Set my handle.
	$handle = 'lw-woo-gdpr-user-optins-admin';

	// Set a file suffix structure based on whether or not we want a minified version.
	$file   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'lw-woo-gdpr-user-optins-admin' : 'lw-woo-gdpr-user-optins-admin.min';

	// Set a version for whether or not we're debugging.
	$vers   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : Core\VERS;

	// Load our CSS file.
	wp_enqueue_style( $handle, Core\ASSETS_URL . '/css/' . $file . '.css', false, $vers, 'all' );

	// And our JS.
	wp_enqueue_script( $handle, Core\ASSETS_URL . '/js/' . $file . '.js', array( 'jquery' ), $vers, true );
}

/**
 * Include a custom body class on our admin tab.
 *
 * @param  string $classes  The current string of body classes.
 *
 * @return string $classes  The potentially modified string of body classes.
 */
function load_admin_body_class( $classes ) {

	// Confirm we are on the settings tab.
	if ( Helpers\maybe_admin_settings_tab() ) {
		$classes .= ' lw-woo-gdpr-user-optins-admin-tab';
	}

	// And send back the string.
	return $classes;
}

/**
 * Add our "settings" links to the plugins page.
 *
 * @param  array  $links  The existing array of links.
 * @param  string $file   The file we are actually loading from.
 *
 * @return array  $links  The updated array of links.
 */
function quick_link( $links, $file ) {

	// Bail without caps.
	if ( ! current_user_can( 'manage_options' ) ) {
		return $links;
	}

	// Set the static var.
	static $this_plugin;

	// Check the base if we aren't paired up.
	if ( ! $this_plugin ) {
		$this_plugin = Core\BASE;
	}

	// Check to make sure we are on the correct plugin.
	if ( $file != $this_plugin ) {
		return $links;
	}

	// Fetch our settings link.
	$link   = Helpers\get_settings_tab_link();

	// Now create the link markup.
	$setup  = '<a href="' . esc_url( $link ) . ' ">' . esc_html__( 'Settings', 'lw-woo-gdpr-user-optins' ) . '</a>';

	// Add it to the array.
	array_unshift( $links, $setup );

	// Return the resulting array.
	return $links;
}
