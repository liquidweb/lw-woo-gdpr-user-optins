<?php
/**
 * Our helper functions to use across the plugin.
 *
 * @package WooGDPRUserOptIns
 */

// Declare our namespace.
namespace LiquidWeb\WooGDPRUserOptIns\Helpers;

// Set our aliases.
use LiquidWeb\WooGDPRUserOptIns as Core;

/**
 * Check an code and (usually an error) return the appropriate text.
 *
 * @param  string $code  The code provided.
 *
 * @return string
 */
function notice_text( $code = '' ) {

	// Return if we don't have an error code.
	if ( empty( $code ) ) {
		return __( 'There was an error with your request.', 'lw-woo-gdpr-user-optins' );
	}

	// Handle my different error codes.
	switch ( esc_attr( strtolower( $code ) ) ) {

		case 'success-delete' :
			return __( 'The opt-in field has been successfully deleted.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'success-sorted' :
			return __( 'Your field sort order has been saved.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'success-added' :
			return __( 'Your new field has been added.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'success-change-opts' :
			return __( 'Your opt-in selections have been updated.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'success-general' :
		case 'success' :
			return __( 'Success! Your request has been processed.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'update-error' :
			return __( 'Your settings could not be updated.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'missing-nonce' :
			return __( 'The required nonce was missing.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'bad-nonce' :
			return __( 'The required nonce was invalid.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'invalid-nonce' :
			return __( 'The required nonce was missing or invalid.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'missing-title' :
			return __( 'The title field is required.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'missing-label' :
			return __( 'The label field is required.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'no-field-ids' :
			return __( 'The field IDs required for sorting could not be determined.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'bad-merge' :
			return __( 'The new field could not be added correctly.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'no-markup' :
			return __( 'The new field display could not be added.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'not-removed' :
			return __( 'There was an error removing the field.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'no-user-id' :
			return __( 'The required user ID was not provided.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'no-user' :
			return __( 'The current user could not be determined.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'user-update-failed' :
			return __( 'Your opt-in selections could not be updated.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'missing-required-field' :
			return __( 'Please review all the required fields.', 'lw-woo-gdpr-user-optins' );
			break;

		case 'unknown' :
		case 'unknown-error' :
			return __( 'There was an unknown error with your request.', 'lw-woo-gdpr-user-optins' );
			break;

		// End all case breaks.
	}

	// Set a default text.
	$msgtxt = __( 'There was an error with your request.', 'lw-woo-gdpr-user-optins' );

	// Return it with a filter.
	return apply_filters( 'lw_woo_gdpr_optins_default_fields', $msgtxt, $code );
}

/**
 * Return our base link, with function fallbacks.
 *
 * @return string
 */
function get_settings_tab_link() {
	return ! function_exists( 'menu_page_url' ) ? admin_url( 'admin.php?page=wc-settings&tab=' . Core\TAB_BASE ) : add_query_arg( array( 'tab' => Core\TAB_BASE ), menu_page_url( 'wc-settings', false ) );
}

/**
 * Get our "My Account" page to use in the plugin.
 *
 * @param  array $args  Any query args to add to the base URL.
 *
 * @return string
 */
function get_account_tab_link( $args = array() ) {

	// Set my base link.
	$page   = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

	// Add our link.
	$link   = rtrim( $page, '/' ) . '/' . Core\FRONT_VAR;

	// Return the link with or without args.
	return ! empty( $args ) ? add_query_arg( $args, $link ) : $link;
}

/**
 * Our default values, which will seed the settings tab.
 *
 * @return array
 */
function get_default_fields() {

	// Set an array of what we know we need.
	$fields = array(

		// Our general contact list item.
		'general-contact' => array(
			'type'      => 'checkbox',
			'id'        => 'general-contact',
			'action'    => 'lw_woo_gdpr_optin_general_contact',
			'title'     => __( 'General Contact', 'lw-woo-gdpr-user-optins' ),
			'label'     => __( 'You may contact me regarding my order or account.', 'lw-woo-gdpr-user-optins' ),
			'required'  => false,
		),

		// Our mailing list item.
		'mailing-list' => array(
			'type'      => 'checkbox',
			'id'        => 'mailing-list',
			'action'    => 'lw_woo_gdpr_optin_mailing_list',
			'title'     => __( 'Mailing List', 'lw-woo-gdpr-user-optins' ),
			'label'     => __( 'You may include me on your mailing list.', 'lw-woo-gdpr-user-optins' ),
			'required'  => false,
		),
	);

	// Do our check for the "terms and conditions" setting.
	$tc = get_option( 'woocommerce_terms_page_id', 0 );

	// Include one if we haven't turned it on.
	if ( empty( $tc ) ) {

		// Add our terms and conditions. But people should have their own.
		$fields['terms-conditions'] = array(
			'type'      => 'checkbox',
			'id'        => 'terms-conditions',
			'action'    => 'lw_woo_gdpr_optin_terms_conditions',
			'title'     => __( 'Terms and Conditions', 'lw-woo-gdpr-user-optins' ),
			'label'     => __( 'I have read and understand the terms and conditions.', 'lw-woo-gdpr-user-optins' ),
			'required'  => true,
		);
	}

	// Set the fields with a filter.
	$fields = apply_filters( 'lw_woo_gdpr_optins_default_fields', $fields );

	// Bail if we have no fields.
	return ! empty( $fields ) ? $fields : false;
}

/**
 * Get our saved fields, with the defaults as a backup.
 *
 * @return array
 */
function get_current_optin_fields() {

	// Fetch the option row.
	$fields = get_option( Core\OPTION_NAME, array() );

	// Return the fields, or the defaults.
	return ! empty( $fields ) ? $fields : get_default_fields();
}

/**
 * Get our required saved fields.
 *
 * @return array
 */
function get_required_optin_fields() {

	// Fetch my existing fields.
	$fields = get_current_optin_fields();

	// Bail without my fields.
	if ( empty( $fields ) ) {
		return;
	}

	// Now loop my fields.
	foreach ( $fields as $key => $field ) {

		// If it isn't a required field, unset it.
		if ( ! maybe_field_required( $key, $fields ) ) {
			unset( $fields[ $key ] );
		}
	}

	// Return what's left.
	return ! empty( $fields ) ? $fields : array();
}

/**
 * Take an array key for the fields and return that data.
 *
 * @param  string $key  The key inside the data array we want.
 *
 * @return mixed        Array if field is found, false otherwise.
 */
function get_single_optin_data( $key = '' ) {

	// Fetch all the fields.
	$fields = get_current_optin_fields();

	// Bail if no fields exist.
	if ( empty( $fields ) ) {
		return false;
	}

	// Return that field if it exists, or false.
	return isset( $fields[ $key ] ) ? $fields[ $key ] : false;
}

/**
 * Take a new opt-in field and create our full data array.
 *
 * @param  array   $args   The args saved by the user.
 * @param  boolean $merge  Whether to merge the existing fields.
 *
 * @return array         The new data array.
 */
function format_new_optin_field( $args = array(), $merge = false ) {

	// Check the required title and label.
	if ( empty( $args['title'] ) || empty( $args['label'] ) ) {
		return false; // @@todo some error checking
	}

	// Sanitize the args in one fell swoop.
	$filter = array_map( 'sanitize_text_field', $args );

	// Parse out the rest.
	$id     = sanitize_title_with_dashes( $filter['title'], '', 'save' );
	$action = make_action_key( $id );
	$req    = ! empty( $filter['required'] ) ? true : false;

	// Build the constructed data array.
	$setup  = array(
		'id'        => $id,
		'action'    => $action,
		'title'     => esc_attr( $filter['title'] ),
		'label'     => esc_attr( $filter['label'] ),
		'required'  => $req,
	);

	// If we didn't request the merge, return our setup.
	if ( empty( $merge ) ) {
		return $setup;
	}

	// Set up the new item.
	$update = array( $id => $setup );

	// Pull our saved items.
	$saved  = get_option( Core\OPTION_NAME, array() );

	// And return the merged array.
	return wp_parse_args( $update, $saved );
}

/**
 * Take the existing opt-in fields and create our full data array.
 *
 * @param  array $current  The existing args saved by the user.
 *
 * @return array $data     The new data array.
 */
function format_current_optin_fields( $current = array() ) {

	// Make sure we have stuff.
	if ( empty( $current ) ) {
		return false; // @@todo some error checking
	}

	// Set my empty.
	$data   = array();

	// Loop my existing items and reconstruct the array.
	foreach ( $current as $id => $args ) {

		// Sanitize the args in one fell swoop.
		$filter = array_map( 'sanitize_text_field', $args );

		// Make sure we have an action.
		$req    = ! empty( $filter['required'] ) ? true : false;
		$action = ! empty( $filter['action'] ) ? esc_attr( $filter['action'] ) : make_action_key( $id );

		// Build the data array.
		$setup  = array(
			'id'        => $id,
			'action'    => $action,
			'title'     => esc_attr( $filter['title'] ),
			'label'     => esc_attr( $filter['label'] ),
			'required'  => $req,
		);

		// And add it to our data.
		$data[ $id ] = $setup;
	}

	// Return the whole thing, or cleaned up.
	return ! empty( $data ) ? $data : false;
}

/**
 * Manage saving the fields created.
 *
 * @param  array  $fields  The field data we are going to save.
 * @param  string $remove  A field item to remove.
 *
 * @return boolean
 */
function update_saved_optin_fields( $fields = array(), $remove = '' ) {

	// Make sure we have either fields or add or remove.
	if ( empty( $fields ) && empty( $remove ) ) {
		return false;
	}

	// Make sure we have fields to begin with.
	$fields = ! empty( $fields ) ? $fields : get_option( Core\OPTION_NAME, array() );

	// Check for the remove first.
	if ( ! empty( $remove ) ) {
		unset( $fields[ $remove ] );
	}

	// Unslash the bastards.
	$fields = array_map( 'wp_unslash', $fields );

	// And update our data.
	update_option( Core\OPTION_NAME, $fields );

	// Return that we've done it.
	return true;
}

/**
 * Manage saving the opt-in choices for a user.
 *
 * @param  integer $user_id   The user we are going to look up if no customer object is there.
 * @param  object  $customer  The customer object.
 * @param  array   $data      The field data to use in updating.
 * @param  boolean $use_keys  Whether or not to use the array keys from the data.
 *
 * @return boolean
 */
function update_user_optins( $user_id = 0, $customer, $data = array(), $use_keys = true ) {

	// Make sure we have everything required.
	if ( empty( $user_id ) && empty( $customer ) ) {
		return false;
	}

	// Get my fields.
	$fields = get_current_optin_fields();

	// Bail without my fields.
	if ( empty( $fields ) ) {
		return false;
	}

	// Now loop my fields.
	foreach ( $fields as $key => $field ) {

		// Set the meta key using the field name.
		$meta_key   = make_user_meta_key( $key );

		// Check our dataset.
		$dataset    = ! empty( $use_keys ) ? array_keys( $data ) : $data;

		// Set the value from the posted data, or null if it's missing.
		$meta_value = ! empty( $data ) && in_array( $key, $dataset ) ? 1 : 0;

		// And add it to the customer object if we have it, or just the meta.
		if ( ! empty( $customer ) && is_object( $customer ) ) {
			$customer->update_meta_data( $meta_key, $meta_value );
		} else {
			update_user_meta( $user_id, $meta_key, $meta_value );
		}

		// Run an action for each individual opt-in.
		if ( ! empty( $field['action'] ) ) {

			// Sanitize the action name.
			$action = sanitize_text_field( $field['action'] );

			// And do the action.
			do_action( $action, $field, $user_id );
		}
	}

	// And just be done.
	return true;
}

/**
 * Take a string and make it all cleaned up for using in an action.
 *
 * @param  string $key     What the original key is to begin building.
 * @param  string $prefix  What to begin the name with. Optional.
 * @param  string $suffix  What to end the name with. Optional.
 *
 * @return string          The name used in the action.
 */
function make_action_key( $key = '', $prefix = 'lw_woo_gdpr_optin_', $suffix = '' ) {

	// Bail if we don't have a key to check.
	if ( empty( $key ) ) {
		return;
	}

	// Run the main cleanup.
	$clean  = sanitize_key( $key );

	// Now swap the dashes for underscores.
	$strip  = str_replace( array( '-', ' ' ), '_', $clean );

	// Return it.
	return esc_attr( $prefix ) . esc_attr( $strip ) . esc_attr( $suffix );
}

/**
 * Take a string and make it all cleaned up for using in a meta key.
 *
 * @param  string $key  What the original key is to begin building.
 *
 * @return string       The name used in the meta key.
 */
function make_user_meta_key( $key = '' ) {

	// Bail if we don't have a key to check.
	if ( empty( $key ) ) {
		return;
	}

	// Run the main cleanup.
	$clean  = sanitize_key( $key );

	// Now swap the dashes for underscores.
	$strip  = str_replace( array( '-', ' ' ), '_', $clean );

	// Return the key name with our prefix as a constant.
	return Core\META_PREFIX . esc_attr( $strip );
}

/**
 * Handle our redirect within the admin settings page.
 *
 * @param  array $args  The query args to include in the redirect.
 *
 * @return void
 */
function admin_page_redirect( $args = array() ) {

	// Don't redirect if we didn't pass any args.
	if ( ! is_admin() || empty( $args ) ) {
		return;
	}

	// Now set my redirect link.
	$link   = add_query_arg( $args, get_settings_tab_link() );

	// Do the redirect.
	wp_redirect( $link );
	exit;
}

/**
 * Handle our redirect within the admin settings page.
 *
 * @param  array $args  The query args to include in the redirect.
 *
 * @return void
 */
function account_page_redirect( $args = array() ) {

	// Don't redirect if we didn't pass any args.
	if ( is_admin() || empty( $args ) ) {
		return;
	}

	// Do the redirect.
	wp_redirect( get_account_tab_link( $args ) );
	exit;
}

/**
 * Adjust the "My Account" menu to make sure login is at the bottom.
 *
 * @param  array $items  Our current array of items.
 *
 * @return array $items  The modified array.
 */
function adjust_account_tab_order( $items = array() ) {

	// If we don't have the logout link, just return what we have.
	if ( ! isset( $items['customer-logout'] ) ) {
		return $items;
	}

	// Set our logout link.
	$logout = $items['customer-logout'];

	// Remove the logout.
	unset( $items['customer-logout'] );

	// Now add it back in.
	$items['customer-logout'] = $logout;

	// And return the set.
	return $items;
}

/**
 * Check if we are on the account privacy data page.
 *
 * @param  boolean $in_query  Whether to check inside the actual query.
 *
 * @return boolean
 */
function maybe_account_endpoint_page( $in_query = false ) {

	// Bail if we aren't on the right general place.
	if ( is_admin() || ! is_account_page() ) {
		return false;
	}

	// Bail if we aren't on the right general place.
	if ( $in_query && ! in_the_loop() || $in_query && ! is_main_query() ) {
		return false;
	}

	// Call the global query object.
	global $wp_query;

	// Return if we are on our specific var or not.
	return isset( $wp_query->query_vars[ Core\FRONT_VAR ] ) ? true : false;
}

/**
 * Check if a specific field is required.
 *
 * @param  string $key     The array key from the dataset.
 * @param  array  $fields  Our array of fields (if already available).
 *
 * @return boolean
 */
function maybe_field_required( $key = '', $fields = array() ) {

	// Make sure we have everything required.
	if ( empty( $key ) ) {
		return false;
	}

	// Get my fields.
	$fields = ! empty( $fields ) ? $fields : get_current_optin_fields();

	// Bail without my fields.
	if ( empty( $fields ) || ! isset( $fields[ $key ] ) ) {
		return false;
	}

	// Set my single field.
	$field  = $fields[ $key ];

	// Now check the specific field array data.
	return ! empty( $field['required'] ) ? true : false;
}

/**
 * Check if we are on the admin settings tab.
 *
 * @param  string $hook  Optional hook sent from some actions.
 *
 * @return boolean
 */
function maybe_admin_settings_tab( $hook = '' ) {

	// Can't be the admin tab if we aren't admin.
	if ( ! is_admin() ) {
		return false;
	}

	// Check the hook if we passed one.
	if ( ! empty( $hook ) && 'woocommerce_page_wc-settings' !== esc_attr( $hook ) ) {
		return false;
	}

	// Check the tab portion and return true if it matches.
	if ( ! empty( $_GET['tab'] ) && Core\TAB_BASE === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) {
		return true;
	}

	// Nothing left to check, so go false.
	return false;
}

/**
 * Check our various constants on an Ajax call.
 *
 * @return boolean
 */
function check_ajax_constants() {

	// Check for a REST API request.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}

	// Check for running an autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	// Check for running a cron, unless we've skipped that.
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return false;
	}

	// We hit none of the checks, so proceed.
	return true;
}

/**
 * Confirm the user has selected all that are required.
 *
 * @param  array $items  The items the user has selected.
 *
 * @return boolean
 */
function confirm_required_fields( $items = array() ) {

	// Fetch my required fields.
	$fields = get_required_optin_fields();

	// If no required fields exist, we're OK.
	if ( empty( $fields ) ) {
		return true;
	}

	// Set my initial flag.
	$valid  = true;

	// Now loop my fields.
	foreach ( $fields as $key => $field ) {

		// If we have the required opt-in, skip it.
		if ( ! in_array( $key, $items ) ) {

			// Set my valid to false.
			$valid  = false;

			// And be done.
			break;
		}
	}

	// Return the result.
	return $valid;
}
