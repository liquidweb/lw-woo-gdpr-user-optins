<?php
/**
 * Our functions related to the checkout.
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\Checkout;

use LiquidWeb\WooGDPRUserOptIns\Helpers as Helpers;
use LiquidWeb\WooGDPRUserOptIns\Layouts as Layouts;

/**
 * Start our engines.
 */
add_action( 'woocommerce_review_order_before_submit', __NAMESPACE__ . '\display_checkout_fields' );
add_filter( 'woocommerce_checkout_posted_data', __NAMESPACE__ . '\merge_optin_post_data' );
add_action( 'woocommerce_after_checkout_validation', __NAMESPACE__ . '\validate_optin_fields', 10, 2 );
add_action( 'woocommerce_checkout_update_customer', __NAMESPACE__ . '\update_customer_optins', 10, 2 );


/**
 * Add our new opt-in boxes to the checkout.
 *
 * @return HTML
 */
function display_checkout_fields() {

	// Fetch my existing fields.
	$fields = Helpers\get_current_optin_fields();

	// Bail without my fields.
	if ( empty( $fields ) ) {
		return;
	}

	// Set an empty.
	$build  = '';

	// Loop my fields and set up each one.
	foreach ( $fields as $key => $field ) {

		// Wrap each one in a paragraph.
		$build .= '<p class="form-row lw-woo-gdpr-' . esc_attr( $key ) . '-field">';

			// Handle our field output.
			$build .= Layouts\single_checkbox_field( $field );

		// Close the single paragraph.
		$build .= '</p>';
	}

	// And echo it out.
	echo $build;
}

/**
 * Merge in our posted field data.
 *
 * @param  array  $data  The post data that comes by default.
 *
 * @return array  $data  The possibly modified posted data.
 */
function merge_optin_post_data( $data ) {

	// Bail if we have no posted data.
	if ( empty( $_POST['gdpr-user-optins'] ) ) {
		return $data;
	}

	// Clean each entry.
	$items = array_filter( $_POST['gdpr-user-optins'], 'sanitize_text_field' );

	// Loop the posted opt in values.
	foreach ( $items as $key => $value ) {
		$data[ $key ] = $value;
	}

	// And return the modified array.
	return $data;
}

/**
 * Validate the opt-in fields.
 *
 * @param  array  $data    The post data that comes by default.
 * @param  object $errors  The existing error object.
 *
 * @return mixed
 */
function validate_optin_fields( $data, $errors ) {

	// Fetch my existing fields.
	$fields = Helpers\get_current_optin_fields();

	// Bail without my fields.
	if ( empty( $fields ) ) {
		return;
	}

	// Now loop my fields.
	foreach ( $fields as $key => $field ) {

		// If it isn't a required field, skip it.
		if ( empty( $field['required'] ) ) {
			continue;
		}

		// If we have the required opt-in, skip it.
		if ( in_array( $key, array_keys( $data ) ) ) {
			continue;
		}

		// Make sure I have a title.
		$title  = ! empty( $field['title'] ) ? $field['title'] : __( 'Opt In Field', 'lw-woo-gdpr-user-optins' );

		// Set our error key and message.
		$error_code = 'missing-' . esc_attr( $key );
		$error_text = sprintf( __( 'You did not agree to %s', 'lw-woo-gdpr-user-optins' ), esc_attr( $title ) );

		// And add my error.
		$errors->add( $error_code, $error_text );
	}

	// And just be done.
	return;
}

/**
 * Validate the opt-in fields.
 *
 * @param  object $customer  The WooCommerce customer object.
 * @param  array  $data      The post data from the order.
 *
 * @return void
 */
function update_customer_optins( $customer, $data ) {

	// Bail without data or customer info.
	if ( empty( $customer ) || ! is_object( $customer ) || empty( $data ) || ! is_array( $data ) ) {
		return;
	}

	// Run our updates.
	Helpers\update_user_optins( 0, $customer, $data );

	// And just be done.
	return;
}
