<?php
/**
 * The functionality tied to the settings tab.
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\SettingsTab;

use LiquidWeb\WooGDPRUserOptIns\Helpers as Helpers;
use LiquidWeb\WooGDPRUserOptIns\Layouts as Layouts;

/**
 * Start our engines.
 */
add_action( 'admin_notices', __NAMESPACE__ . '\display_admin_notices' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\load_settings_assets' );
add_filter( 'woocommerce_settings_tabs_array', __NAMESPACE__ . '\add_settings_tab', 50 );
add_action( 'woocommerce_update_options_gdpr_user_optins', __NAMESPACE__ . '\update_settings' );
add_action( 'admin_init', __NAMESPACE__ . '\remove_single_field' );
add_action( 'woocommerce_settings_tabs_gdpr_user_optins', __NAMESPACE__ . '\settings_tab' );
add_action( 'woocommerce_admin_field_repeating_setup', __NAMESPACE__ . '\output_repeating_setup', 10, 1 );
add_action( 'woocommerce_admin_field_repeating_group', __NAMESPACE__ . '\output_repeating_group', 10, 1 );


/**
 * Set up the admin notices.
 *
 * @return mixed
 */
function display_admin_notices() {

	// Now check to make sure we have our key.
	if ( empty( $_GET['lw-woo-gdpr-action'] ) ) {
		return;
	}

	// Handle the success notice first.
	if ( ! empty( $_GET['success'] ) ) {

		// Determine my error text.
		$msg_code   = ! empty( $_GET['message'] ) ? esc_attr( $_GET['message'] ) : 'success';
		$msg_text   = Helpers\notice_text( $msg_code );

		// Output the message along with the dismissable.
		echo '<div class="notice notice-success is-dismissible lw-woo-gdpr-user-optins-admin-message">';
			echo '<p><strong>' . wp_kses_post( $msg_text ) . '</strong></p>';
		echo '</div>';

		// And be done.
		return;
	}

	// Figure out my error code.
	$error_code = ! empty( $_GET['errcode'] ) ? esc_attr( $_GET['errcode'] ) : 'unknown';

	// Determine my error text.
	$error_text = Helpers\notice_text( $error_code );

	// Output the message along with the dismissable.
	echo '<div class="notice notice-error is-dismissible lw-woo-gdpr-user-optins-admin-message">';
		echo '<p><strong>' . wp_kses_post( $error_text ) . '</strong></p>';
	echo '</div>';

	// And be done.
	return;
}

/**
 * Load our admin side JS and CSS.
 *
 * @param $hook  Admin page hook we are current on.
 *
 * @return void
 */
function load_settings_assets( $hook ) {

	// Check my hook before moving forward.
	if ( 'woocommerce_page_wc-settings' !== esc_attr( $hook ) ) {
		return;
	}

	// Check the tab portion.
	if ( empty( $_GET['tab'] ) || LWWOOGDPR_OPTINS_TAB_BASE !== esc_attr( $_GET['tab'] ) ) {
		return;
	}

	// Set a file suffix structure based on whether or not we want a minified version.
	$file   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'lw-woo-gdpr-user-optins-admin' : 'lw-woo-gdpr-user-optins-admin.min';

	// Set a version for whether or not we're debugging.
	$vers   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : LWWOOGDPR_OPTINS_VERS;

	// Load our CSS file.
	wp_enqueue_style( 'lw-woo-gdpr-user-optins-admin', LWWOOGDPR_OPTINS_ASSETS_URL . '/css/' . $file . '.css', false, $vers, 'all' );
}

/**
 * Add a new settings tab to the WooCommerce settings tabs array.
 *
 * @param  array $tabs  The current array of WooCommerce setting tabs.
 *
 * @return array $tabs  The modified array of WooCommerce setting tabs.
 */
function add_settings_tab( $tabs ) {

	// Confirm we don't already have the tab.
	if ( ! isset( $tabs[ LWWOOGDPR_OPTINS_TAB_BASE ] ) ) {
		$tabs[ LWWOOGDPR_OPTINS_TAB_BASE ] = __( 'GDPR Opt-Ins', 'lw-woo-gdpr-user-optins' );
	}

	// And return the entire array.
	return $tabs;
}

/**
 * Uses the WooCommerce options API to save settings
 *
 * @see  woocommerce_update_options() function.
 *
 * @uses woocommerce_update_options()
 * @uses self::get_settings()
 */
function update_settings() {

	// Make sure we have at least one of our key sets.
	if ( empty( $_POST['lw-woo-gdpr-user-optins-new'] ) && empty( $_POST['lw-woo-gdpr-user-optins-current'] ) ) {
		return;
	}

	// Set my empty data array.
	$fields = array();

	// Check for the new field, and if it's there format the data.
	if ( ! empty( $_POST['lw-woo-gdpr-user-optins-new'] ) ) {

		// Handle formatting the new fields.
		$format = Helpers\format_new_optin_field( $_POST['lw-woo-gdpr-user-optins-new'] );

		// Add the item to our data array.
		$fields = ! empty( $format ) ? wp_parse_args( array( $format['id'] => $format ), $fields ) : $fields;
	}

	// Check for the existing fields, and if it's there format the data.
	if ( ! empty( $_POST['lw-woo-gdpr-user-optins-current'] ) ) {

		// Handle formatting the new fields.
		$format = Helpers\format_current_optin_fields( $_POST['lw-woo-gdpr-user-optins-current'] );

		// Add the item to our data array.
		$fields = ! empty( $format ) ? wp_parse_args( $fields, $format ) : $fields;
	}

	// Run the update process.
	Helpers\update_saved_optin_fields( $fields );

	// And be done.
	return;
}

/**
 * Remove a single opt-in if the user selects it.
 *
 * @return void
 */
function remove_single_field() {

	// Check the page portion.
	if ( empty( $_GET['page'] ) || 'wc-settings' !== esc_attr( $_GET['page'] ) ) {
		return;
	}

	// Check the tab portion.
	if ( empty( $_GET['tab'] ) || LWWOOGDPR_OPTINS_TAB_BASE !== esc_attr( $_GET['tab'] ) ) {
		return;
	}

	// Check for the delete flag being passed.
	if ( empty( $_GET['lw-woo-gdpr-user-optin-single-delete'] ) || empty( $_GET['field-id'] ) ) {
		return;
	}

	// Set my field.
	$field  = sanitize_text_field( $_GET['field-id'] );

	// Set up the name of the nonce key.
	$nonce  = 'lw_woo_optin_single_' . esc_attr( $field );

	// The nonce check. ALWAYS A NONCE CHECK.
	if ( empty( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], $nonce ) ) {
		return;
	}

	// Run the removal.
	Helpers\update_saved_optin_fields( 0, esc_attr( $field ) );

	// Set up our redirect args.
	$setup  = array( 'success' => 1, 'lw-woo-gdpr-action' => 1, 'message' => 'success-delete' );

	// Now redirect.
	Helpers\admin_page_redirect( $setup );
}

/**
 * Uses the WooCommerce admin fields API to output settings.
 *
 * @see  woocommerce_admin_fields() function.
 *
 * @uses woocommerce_admin_fields()
 * @uses self::get_settings()
 */
function settings_tab() {
	woocommerce_admin_fields( get_settings() );
}

/**
 * Create the array of opt-ins we are going to display.
 *
 * @return array $settings  The array of settings data.
 */
function get_settings() {

	// Set up our array, including default Woo items.
	$setup  = array(

		// Include the header portion.
		'header'   => array(
			'name' => __( 'Opt-In Fields', 'lw-woo-gdpr-user-optins' ),
			'type' => 'repeating_setup',
			'text' => __( 'Below is each checkbox the user will need to opt-in to.', 'lw-woo-gdpr-user-optins' ),
		),

		// Now our opt-in fields, which is just one repeating field.
		'optins'   => array(
			'type' => 'repeating_group',
		),

		// Include my section end.
		'section_end' => array( 'type' => 'sectionend', 'id' => 'lw_woo_gdpr_section_end' ),
	);

	// Return our set of fields with a filter.
	return apply_filters( 'lw_woo_gdpr_optins_settings_array', $setup );
}

/**
 * Output our custom repeating field.
 *
 * @param  array $args  The field args we set up.
 *
 * @return HTML
 */
function output_repeating_setup( $args ) {

	// Handle the name output.
	if ( ! empty( $args['name'] ) ) {
		echo '<h2>' . esc_html( $args['title'] ) . '</h2>';
	}

	// Handle the text output.
	if ( ! empty( $args['text'] ) ) {
		echo wp_kses_post( wpautop( wptexturize( $args['text'] ) ) );
	}

	// Echo out the block.
	echo Layouts\add_new_entry_block();
}

/**
 * Output our custom repeating field.
 *
 * @param  array $args  The field args we set up.
 *
 * @return HTML
 */
function output_repeating_group( $args ) {

	// Fetch my existing fields.
	$fields = Helpers\get_current_optin_fields();

	// Bail if we don't have any fields.
	if ( ! $fields ) {
		return;
	}

	// Wrap my entire table.
	echo '<table id="lw-woo-gdpr-user-optins-list-table" class="lw-woo-gdpr-user-optins-table-wrap lw-woo-gdpr-user-optins-list-table-wrap wp-list-table widefat fixed striped">';

	// Set up the table header.
	echo '<thead>';
		echo '<tr>';

			echo '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-required lw-woo-gdpr-user-optins-field-header" scope="col">';
				echo '<i title="' . __( 'Required', 'lw-woo-gdpr-user-optins' ) . '" class="dashicons dashicons-warning"></i>';
				echo '<span class="screen-reader-text">' . __( 'Required', 'lw-woo-gdpr-user-optins' ) . '</span>';
			echo '</th>';

			echo '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-title lw-woo-gdpr-user-optins-field-header" scope="col">' . __( 'Title', 'lw-woo-gdpr-user-optins' ) . '</th>';

			echo '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-label lw-woo-gdpr-user-optins-field-header" scope="col">' . __( 'Label', 'lw-woo-gdpr-user-optins' ) . '</th>';

			echo '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-hook lw-woo-gdpr-user-optins-field-header" scope="col">' . __( 'Hook', 'lw-woo-gdpr-user-optins' ) . '</th>';

			echo '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-trigger lw-woo-gdpr-user-optins-field-header" scope="col">&nbsp;</th>';

		echo '</tr>';
	echo '</thead>';

	// Set the table body.
	echo '<tbody>';

	// Loop my fields and make a block of each one.
	foreach ( $fields as $field ) {
		echo Layouts\table_row( $field );
	}

	// Close the table body.
	echo '</tbody>';

	// Close my table.
	echo '</table>';
}

