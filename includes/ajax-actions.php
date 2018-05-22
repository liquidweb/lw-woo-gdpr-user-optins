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
add_action( 'wp_ajax_lw_woo_gdpr_optins_sort', __NAMESPACE__ . '\update_sorted_rows' );
add_action( 'wp_ajax_lw_woo_gdpr_optins_add_new', __NAMESPACE__ . '\add_new_optin_row' );
add_action( 'wp_ajax_lw_woo_gdpr_optins_delete_row', __NAMESPACE__ . '\delete_single_row' );

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
	if ( empty( $_POST['action'] ) || 'lw_woo_gdpr_optins_sort' !== sanitize_text_field( $_POST['action'] ) ) {
		return;
	}

	// Check to see if our sorted data was provided.
	if ( empty( $_POST['sorted'] ) ) {
		send_admin_ajax_error( 'no-field-ids' );
	}

	// Filter my field keys.
	$keys   = array_filter( $_POST['sorted'], 'sanitize_text_field' );

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
	if ( empty( $_POST['action'] ) || 'lw_woo_add_new_optin_row' !== sanitize_text_field( $_POST['action'] ) ) {
		return false;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'lw_woo_gdpr_new_optin_action' ) ) {
		send_admin_ajax_error( 'invalid-nonce' );
	}

	// Check for the title field.
	if ( empty( $_POST['title'] ) ) {
		send_admin_ajax_error( 'missing-title' );
	}

	// Check for the label field.
	if ( empty( $_POST['label'] ) ) {
		send_admin_ajax_error( 'missing-label' );
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
		send_admin_ajax_error( 'bad-merge' );
	}

	// Update our option. // no idea how to use woocommerce_update_options();
	Helpers\update_saved_optin_fields( $update );

	// Now attempt to get the table row.
	$markup = Layouts\table_row( $update[ $id ] );

	// Send an error back if we have no markup.
	if ( ! $markup ) {
		send_admin_ajax_error( 'no-markup' );
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
	if ( empty( $_POST['action'] ) || 'lw_woo_gdpr_optins_delete_row' !== sanitize_text_field( $_POST['action'] ) ) {
		return false;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST['nonce'] ) ) {
		send_admin_ajax_error( 'missing-nonce' );
	}

	// Check to see if our field ID was provided.
	if ( empty( $_POST['field_id'] ) ) {
		send_admin_ajax_error( 'bad-field-id' );
	}

	// Set my field ID and nonce key.
	$field_id   = esc_attr( $_POST['field_id'] );
	$noncekey   = 'lw_woo_gdpr_user_optin_single_' . esc_attr( $_POST['field_id'] );

	// Check to see if our nonce failed.
	if ( ! wp_verify_nonce( $_POST['nonce'], $noncekey ) ) {
		send_admin_ajax_error( 'bad-nonce' );
	}

	// Run the removal.
	$remove = Helpers\update_saved_optin_fields( 0, esc_attr( $field_id ) );

	// Confirm it came back OK.
	if ( ! $remove ) {
		send_admin_ajax_error( 'not-removed' );
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
 * @param  string $errcode  The error code in question.
 *
 * @return void
 */
function send_admin_ajax_error( $errcode = '' ) {

	// Build our return.
	$return = array(
		'errcode' => $errcode,
		'message' => Helpers\notice_text( $errcode ),
	);

	// And handle my JSON return.
	wp_send_json_error( $return );
}
