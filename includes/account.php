<?php
/**
 * Our functions related to the "my account" page.
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\AccountPage;

use LiquidWeb\WooGDPRUserOptIns\Helpers as Helpers;
use LiquidWeb\WooGDPRUserOptIns\Layouts as Layouts;

/**
 * Start our engines.
 */
add_action( 'init', __NAMESPACE__ . '\check_user_optin_changes' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\load_endpoint_assets' );
add_filter( 'the_title', __NAMESPACE__ . '\add_endpoint_title' );
add_action( 'woocommerce_before_account_navigation', __NAMESPACE__ . '\add_endpoint_notices', 15 );
add_filter( 'woocommerce_account_menu_items', __NAMESPACE__ . '\add_endpoint_menu_item' );
add_action( 'woocommerce_account_privacy-data_endpoint', __NAMESPACE__ . '\add_endpoint_content' );


/**
 * Look for our users changing their opt-in statuses.
 *
 * @return void
 */
function check_user_optin_changes() {

	// Make sure we have the action we want.
	if ( empty( $_POST['action'] ) || 'lw_woo_gdpr_user_optins_change' !== sanitize_text_field( $_POST['action'] ) ) {
		return;
	}

	// The nonce check. ALWAYS A NONCE CHECK.
	if ( ! isset( $_POST['lw_woo_gdpr_user_optins_change_nonce'] ) || ! wp_verify_nonce( $_POST['lw_woo_gdpr_user_optins_change_nonce'], 'lw_woo_gdpr_user_optins_change_action' ) ) {
		return;
	}

	// Make sure we have a user of some kind.
	if ( empty( $_POST['lw_woo_gdpr_user_optins_change_user_id'] ) ) {

		// Set up our redirect args.
		$setup  = array( 'success' => 0, 'lw-woo-gdpr-action' => 1, 'errcode' => 'no-user' );

		// Redirect with our error code.
		Helpers\account_page_redirect( $setup );
	}

	// Set my user ID.
	$user_id    = absint( $_POST['lw_woo_gdpr_user_optins_change_user_id'] );

	// Filter my field args getting passed.
	$field_args = empty( $_POST['lw_woo_gdpr_user_optins_items'] ) ? array() : array_filter( $_POST['lw_woo_gdpr_user_optins_items'], 'sanitize_text_field' );

	// Attempt to update the settings.
	if ( ! Helpers\update_user_optins( $user_id, null, $field_args ) ) {

		// Set up our redirect args.
		$setup  = array( 'success' => 0, 'lw-woo-gdpr-action' => 1, 'errcode' => 'update-error' );

		// Redirect with our error code.
		Helpers\account_page_redirect( $setup );
	}

	// Set up our redirect args.
	$setup  = array( 'success' => 1, 'lw-woo-gdpr-action' => 1, 'message' => 'success-change-opts' );

	// Redirect with our error code.
	Helpers\account_page_redirect( $setup );
}

/**
 * Load our front-end side JS and CSS.
 *
 * @return void
 */
function load_endpoint_assets() {

	// Bail if we aren't on the right general place.
	if ( ! Helpers\maybe_account_endpoint_page() ) {
		return;
	}

	// Set my handle.
	$handle = 'lw-woo-gdpr-user-optins-front';

	// Set a file suffix structure based on whether or not we want a minified version.
	$file   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'lw-woo-gdpr-user-optins-front' : 'lw-woo-gdpr-user-optins-front.min';

	// Set a version for whether or not we're debugging.
	$vers   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : LWWOOGDPR_OPTINS_VERS;

	// Load our CSS file.
	wp_enqueue_style( $handle, LWWOOGDPR_OPTINS_ASSETS_URL . '/css/' . $file . '.css', false, $vers, 'all' );

	// And our JS.
	wp_enqueue_script( $handle, LWWOOGDPR_OPTINS_ASSETS_URL . '/js/' . $file . '.js', array( 'jquery' ), $vers, true );
	wp_localize_script( $handle, 'frontWooUserGDPR',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

/**
 * Add the notices above the "my account" area.
 *
 * @return HTML
 */
function add_endpoint_notices() {

	// Bail if we aren't on the right general place.
	if ( ! Helpers\maybe_account_endpoint_page() ) {
		return;
	}

	// Bail without our result flag.
	if ( empty( $_GET['lw-woo-gdpr-action'] ) ) {

		// Echo out the blank placeholder for Ajax calls.
		echo '<div class="lw-woo-gdpr-user-optins-account-notices"></div>';

		// And just be done.
		return;
	}

	// Figure out the text.
	$msg_text   = ! empty( $_GET['message'] ) ? sanitize_text_field( $_GET['message'] ) : Helpers\notice_text( 'unknown' );

	// Determine the message type.
	$msg_type   = empty( $_GET['success'] ) ? 'error' : 'success';

	// Output the message.
	Layouts\account_message_markup( $msg_text, $msg_type, true );
}

/**
 * Set a title for the individual endpoint we just made.
 *
 * @param  string $title  The existing page title.
 *
 * @return string
 */
function add_endpoint_title( $title ) {

	// Bail if we aren't on the page.
	if ( ! Helpers\maybe_account_endpoint_page( true ) ) {
		return $title;
	}

	// Set our new page title.
	$title = apply_filters( 'lw_woo_gdpr_user_optins_endpoint_title', __( 'My Opt-In Statuses', 'lw-woo-gdpr-user-optins' ) );

	// Remove the filter so we don't loop endlessly.
	remove_filter( 'the_title', __NAMESPACE__ . '\add_endpoint_title' );

	// Return the title.
	return $title;
}

/**
 * Merge in our new enpoint into the existing "My Account" menu.
 *
 * @param  array $items  The existing menu items.
 *
 * @return array
 */
function add_endpoint_menu_item( $items ) {

	// Set up our menu item title.
	$title  = apply_filters( 'lw_woo_gdpr_user_optins_endpoint_menu_title', __( 'Opt-Ins', 'lw-woo-gdpr-user-optins' ) );

	// Add it to the array.
	$items  = wp_parse_args( array( LWWOOGDPR_OPTINS_FRONT_VAR => esc_attr( $title ) ), $items );

	// Return our tabs.
	return Helpers\adjust_account_tab_order( $items );
}

/**
 * Add the content for our endpoint to display.
 *
 * @return HTML
 */
function add_endpoint_content() {
	Layouts\optin_status_display_form( get_current_user_id(), true );
}

