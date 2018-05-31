<?php
/**
 * Our various Ajax calls.
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\AjaxActions;

use LiquidWeb\WooGDPRUserOptIns\Helpers as Helpers;
use LiquidWeb\WooGDPRUserOptIns\Layouts as Layouts;

/**
 * Start our engines.
 */
add_action( 'wp_ajax_lw_woo_gdpr_save_user_optins', __NAMESPACE__ . '\save_user_optins' );
add_action( 'wp_ajax_lw_woo_gdpr_optins_sort', __NAMESPACE__ . '\update_sorted_rows' );
add_action( 'wp_ajax_lw_woo_gdpr_optins_add_new', __NAMESPACE__ . '\add_new_optin_row' );
add_action( 'wp_ajax_lw_woo_gdpr_optins_delete_row', __NAMESPACE__ . '\delete_single_row' );


/**
 * Update our user opt-in values.
 *
 * @return mixed
 */
function save_user_optins() {

	// Check our various constants.
	if ( ! Helpers\check_ajax_constants() ) {
		return;
	}

	// Check for the specific action.
	if ( empty( $_POST['action'] ) || 'lw_woo_gdpr_save_user_optins' !== sanitize_text_field( $_POST['action'] ) ) { // WPCS: CSRF ok.
		return;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'lw_woo_gdpr_user_optins_change_action' ) ) {
		send_ajax_error_response( 'invalid-nonce', 'account' );
	}

	// Check for the user ID field.
	if ( empty( $_POST['user_id'] ) ) {
		send_ajax_error_response( 'no-user-id', 'account' );
	}

	// Determine if we have opt-in choices.
	$items  = ! empty( $_POST['optins'] ) ? array_filter( (array) $_POST['optins'], 'sanitize_text_field' ) : array();

	// Run our validation checks.
	if ( ! Helpers\confirm_required_fields( $items ) ) {
		send_ajax_error_response( 'missing-required-field', 'account' );
	}

	// Run the update.
	$update = Helpers\update_user_optins( absint( $_POST['user_id'] ), null, $items, false );

	// Make sure it came back OK.
	if ( ! $update ) {
		send_ajax_error_response( 'user-update-failed', 'account' );
	}

	// Get my message text.
	$msgtxt = Helpers\notice_text( 'success-change-opts' );

	// Build our return.
	$return = array(
		'errcode' => null,
		'markup'  => Layouts\optin_status_list( absint( $_POST['user_id'] ) ),
		'message' => $msgtxt,
		'notice'  => Layouts\account_message_markup( $msgtxt, 'success', false ),
	);

	// And handle my JSON return.
	wp_send_json_success( $return );
}

/**
 * Update our stored away with the new sort.
 *
 * @return mixed
 */
function update_sorted_rows() {

	// Only run this on the admin side.
	if ( ! is_admin() ) {
		die();
	}

	// Check our various constants.
	if ( ! Helpers\check_ajax_constants() ) {
		return;
	}

	// Check for the specific action.
	if ( empty( $_POST['action'] ) || 'lw_woo_gdpr_optins_sort' !== sanitize_text_field( $_POST['action'] ) ) { // WPCS: CSRF ok.
		return;
	}

	// Check to see if our sorted data was provided.
	if ( empty( $_POST['sorted'] ) ) { // WPCS: CSRF ok.
		send_ajax_error_response( 'no-field-ids' );
	}

	// Filter my field keys.
	$keys   = array_map( 'sanitize_text_field', $_POST['sorted'] );

	// Set my new array variable.
	$update = array();

	// Loop my field keys to reconstruct the order.
	foreach ( $keys as $key ) {
		$update[ $key ] = Helpers\get_single_optin_data( $key );
	}

	// Run the update itself.
	Helpers\update_saved_optin_fields( $update );

	// Build our return.
	$return = array(
		'errcode' => null,
		'message' => Helpers\notice_text( 'success-sorted' ),
	);

	// And handle my JSON return.
	wp_send_json_success( $return );
}

/**
 * Add a new row from the field.
 *
 * @return mixed
 */
function add_new_optin_row() {

	// Only run this on the admin side.
	if ( ! is_admin() ) {
		die();
	}

	// Check our various constants.
	if ( ! Helpers\check_ajax_constants() ) {
		return;
	}

	// Check for the specific action.
	if ( empty( $_POST['action'] ) || 'lw_woo_gdpr_optins_add_new' !== sanitize_text_field( $_POST['action'] ) ) { // WPCS: CSRF ok.
		return;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'lw_woo_gdpr_new_optin_action' ) ) {
		send_ajax_error_response( 'invalid-nonce' );
	}

	// Check for the title field.
	if ( empty( $_POST['title'] ) ) {
		send_ajax_error_response( 'missing-title' );
	}

	// Check for the label field.
	if ( empty( $_POST['label'] ) ) {
		send_ajax_error_response( 'missing-label' );
	}

	// Set my ID, since we use it a few places.
	$id = sanitize_title_with_dashes( $_POST['title'], '', 'save' );

	// Create the data array needed to make the field.
	$setup  = array(
		'id'        => $id,
		'title'     => sanitize_text_field( $_POST['title'] ),
		'label'     => sanitize_text_field( $_POST['label'] ),
		'required'  => 'true' === sanitize_text_field( $_POST['required'] ) ? 1 : 0,
	);

	// Grab my field based on the setup.
	$update = Helpers\format_new_optin_field( $setup, true );

	// Make sure it came back OK.
	if ( ! $update ) {
		send_ajax_error_response( 'bad-merge' );
	}

	// Update our option.
	Helpers\update_saved_optin_fields( $update );

	// Now attempt to get the table row.
	$markup = Layouts\table_row( $update[ $id ] );

	// Send an error back if we have no markup.
	if ( ! $markup ) {
		send_ajax_error_response( 'no-markup' );
	}

	// Build our return.
	$return = array(
		'errcode' => null,
		'markup'  => $markup,
		'message' => Helpers\notice_text( 'success-added' ),
	);

	// And handle my JSON return.
	wp_send_json_success( $return );
}

/**
 * Handle deleting a single row from the data array.
 *
 * @return mixed
 */
function delete_single_row() {

	// Only run this on the admin side.
	if ( ! is_admin() ) {
		die();
	}

	// Check our various constants.
	if ( ! Helpers\check_ajax_constants() ) {
		return;
	}

	// Check for the specific action.
	if ( empty( $_POST['action'] ) || 'lw_woo_gdpr_optins_delete_row' !== sanitize_text_field( $_POST['action'] ) ) { // WPCS: CSRF ok.
		return;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST['nonce'] ) ) {
		send_ajax_error_response( 'missing-nonce' );
	}

	// Check to see if our field ID was provided.
	if ( empty( $_POST['field_id'] ) ) {
		send_ajax_error_response( 'bad-field-id' );
	}

	// Set my field ID and nonce key.
	$field_id   = esc_attr( $_POST['field_id'] ); // WPCS: CSRF ok.
	$noncekey   = 'lw_woo_gdpr_user_optin_single_' . esc_attr( $_POST['field_id'] ); // WPCS: CSRF ok.

	// Check to see if our nonce failed.
	if ( ! wp_verify_nonce( $_POST['nonce'], $noncekey ) ) {
		send_ajax_error_response( 'bad-nonce' );
	}

	// Run the removal.
	$remove = Helpers\update_saved_optin_fields( 0, esc_attr( $field_id ) );

	// Confirm it came back OK.
	if ( ! $remove ) {
		send_ajax_error_response( 'not-removed' );
	}

	// Build our return.
	$return = array(
		'errcode' => null,
		'message' => Helpers\notice_text( 'success-delete' ),
	);

	// And handle my JSON return.
	wp_send_json_success( $return );
}

/**
 * Build and process our Ajax error handler.
 *
 * @param  string $errcode   The error code in question.
 * @param  string $location  Where the notice is being displayed.
 *
 * @return array
 */
function send_ajax_error_response( $errcode = '', $location = 'admin' ) {

	// Get my message text.
	$msgtxt = Helpers\notice_text( $errcode );

	// Get my notice markup.
	$notice = 'account' !== sanitize_text_field( $location ) ?  Layouts\admin_message_markup( $msgtxt, 'error', true, true ) : Layouts\account_message_markup( $msgtxt );

	// Build our return.
	$return = array(
		'errcode' => $errcode,
		'message' => $msgtxt,
		'notice'  => $notice,
	);

	// And handle my JSON return.
	wp_send_json_error( $return );
}
