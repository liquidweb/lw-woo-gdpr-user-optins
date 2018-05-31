<?php
/**
 * Some layout and template items.
 *
 * @package WooGDPRUserOptIns
 */

namespace LiquidWeb\WooGDPRUserOptIns\Layouts;

use LiquidWeb\WooGDPRUserOptIns\Helpers as Helpers;

/**
 * The individual new entry field.
 *
 * @param  boolean $echo  Whether to echo out the markup or return it.
 *
 * @return HTML
 */
function add_new_entry_block( $echo = false ) {

	// Set an empty.
	$field  = '';

	// Wrap the new in a table.
	$field .= '<table id="lw-woo-gdpr-user-optins-add-new-table" class="lw-woo-gdpr-user-optins-table-wrap lw-woo-gdpr-user-optins-add-new-table-wrap wp-list-table widefat fixed striped">';

		// And a header.
		$field .= '<thead>';

			// Open up the row.
			$field .= '<tr class="lw-woo-gdpr-user-optins-new-fields-header-row">';

				// Add the required checkbox.
				$field .= '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-required lw-woo-gdpr-user-optins-field-header">';
					$field .= '<i title="' . __( 'Required', 'lw-woo-gdpr-user-optins' ) . '" class="dashicons dashicons-warning"></i>';
				$field .= '</th>';

				// Add the title field.
				$field .= '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-title lw-woo-gdpr-user-optins-field-header">';
					$field .= esc_html__( 'Title', 'lw-woo-gdpr-user-optins' );
				$field .= '</th>';

				// Add the label field.
				$field .= '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-label lw-woo-gdpr-user-optins-field-header">';
					$field .= esc_html__( 'Label', 'lw-woo-gdpr-user-optins' );
				$field .= '</th>';

				// Add spacers to align with the button.
				$field .= '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-hook lw-woo-gdpr-user-optins-field-add-new-button lw-woo-gdpr-user-optins-field-header">&nbsp;</th>';
				$field .= '<th class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-trigger lw-woo-gdpr-user-optins-field-header">&nbsp;</th>';

			// Close the row.
			$field .= '</tr>';

		// Close the header.
		$field .= '</thead>';

		// And a body.
		$field .= '<tbody>';

			// Open the row.
			$field .= '<tr class="lw-woo-gdpr-user-optins-new-fields-row">';

				// Add the required checkbox.
				$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-input-checkbox lw-woo-gdpr-user-optins-field-required">';

					$field .= '<input type="checkbox" id="lw-woo-gdpr-user-optin-required-new" class="lw-woo-gdpr-user-optins-input-field" name="lw-woo-gdpr-user-optins-new[required]" value="1">';

				$field .= '</td>';

				// Add the title field.
				$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-input-text lw-woo-gdpr-user-optins-field-title">';

					$field .= '<input type="text" id="lw-woo-gdpr-user-optin-title-new" placeholder="' . __( 'Field Title', 'lw-woo-gdpr-user-optins' ) . '" class="widefat lw-woo-gdpr-user-optins-input-field" name="lw-woo-gdpr-user-optins-new[title]" value="">';

				$field .= '</td>';

				// Add the label field.
				$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-input-text lw-woo-gdpr-user-optins-field-label">';

					$field .= '<input type="text" id="lw-woo-gdpr-user-optin-label-new" placeholder="' . __( 'Field Label', 'lw-woo-gdpr-user-optins' ) . '" class="widefat lw-woo-gdpr-user-optins-input-field" name="lw-woo-gdpr-user-optins-new[label]" value="">';

				$field .= '</td>';

				// Add the button setup itself.
				$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-hook lw-woo-gdpr-user-optins-field-input-button lw-woo-gdpr-user-optins-field-add-new-button">';

					// The button.
					$field .= '<button type="submit" class="button button-secondary button-small lw-woo-gdpr-user-optin-add-new-button" id="lw-woo-gdpr-user-optin-add-new">' . esc_html__( 'Add New Item', 'lw-woo-gdpr-user-optins' ) . '</button>';

					// Include the checkmark that will show and hide when adding a new field.
					$field .= '<span class="lw-woo-gdpr-user-optins-field-new-success lw-woo-gdpr-user-optins-field-hidden hide-if-no-js">';
						$field .= '<i class="dashicons dashicons-yes"></i>';
					$field .= '</span>';

					// Include a nonce.
					$field .= wp_nonce_field( 'lw_woo_gdpr_new_optin_action', 'lw_woo_gdpr_new_optin_nonce', true, false );

				$field .= '</td>';

				// Add a blank table spacer, because tables.
				$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-new-field lw-woo-gdpr-user-optins-field-trigger lw-woo-gdpr-user-optins-field-add-new-button">&nbsp;</td>';

			// Close the row.
			$field .= '</tr>';

		// Close the body.
		$field .= '</tbody>';

	// Close the table.
	$field .= '</table>';

	// Echo if requested.
	if ( ! empty( $echo ) ) {
		echo $field; // WPCS: XSS ok.
	}

	// Return it.
	return $field;
}

/**
 * The individual table rows.
 *
 * @param  array   $args   The field args I passed.
 * @param  boolean $echo   Whether to echo the field or return it.
 *
 * @return HTML
 */
function table_row( $args = array(), $echo = false ) {

	// Bail without the args.
	if ( empty( $args ) || empty( $args['id'] ) ) {
		return false;
	}

	// Create my name field and confirm the action name.
	$name   = 'lw-woo-gdpr-user-optins-current[' . esc_attr( $args['id'] ) . ']';
	$check  = ! empty( $args['required'] ) ? true : false;
	$action = ! empty( $args['action'] ) ? $args['action'] : Helpers\make_action_key( $args['id'] );

	// Set our delete link.
	$d_nonc = wp_create_nonce( 'lw_woo_gdpr_user_optin_single_' . esc_attr( $args['id'] ) );
	$d_args = array( 'lw-woo-gdpr-user-optin-single-delete' => 1, 'field-id' => esc_attr( $args['id'] ), 'nonce' => $d_nonc );
	$delete = add_query_arg( $d_args, Helpers\get_settings_tab_link() );

	// Set the data attributes for the Ajax call.
	$d_atrb = ' data-field-id="' . esc_attr( $args['id'] ) . '" data-nonce="' . $d_nonc . '"';

	// Set an empty.
	$field  = '';

	// Set up the single div.
	$field .= '<tr data-key="' . esc_attr( $args['id'] ) . '" id="lw-woo-gdpr-user-optins-single-' . esc_attr( $args['id'] ) . '-row" class="lw-woo-gdpr-user-optins-single-row">';

		// Output the required checkbox.
		$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-input-checkbox lw-woo-gdpr-user-optins-field-required lw-woo-gdpr-user-optins-field-single">';

			$field .= '<input type="checkbox" class="lw-woo-gdpr-user-optins-input-field" name="' . esc_attr( $name ) . '[required]" value="1" ' . checked( $check, 1, false ) . '>';

		$field .= '</td>';

		// Output the title field.
		$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-input-text lw-woo-gdpr-user-optins-field-title lw-woo-gdpr-user-optins-field-single">';

			$field .= '<input type="text" class="widefat lw-woo-gdpr-user-optins-input-field" name="' . esc_attr( $name ) . '[title]" value="' . esc_attr( $args['title'] ) . '">';

		$field .= '</td>';

		// Output the label field.
		$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-input-text lw-woo-gdpr-user-optins-field-label lw-woo-gdpr-user-optins-field-single">';

			$field .= '<input type="text" class="widefat lw-woo-gdpr-user-optins-input-field" name="' . esc_attr( $name ) . '[label]" value="' . esc_attr( $args['label'] ) . '">';

		$field .= '</td>';

		// Output the hook name field.
		$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-input-text  lw-woo-gdpr-user-optins-field-hook lw-woo-gdpr-user-optins-field-single">';

			$field .= '<input type="text" class="widefat code lw-woo-gdpr-user-optins-input-field" readonly="readonly" value="' . esc_attr( $action ) . '">';

			$field .= '<input type="hidden" name="' . esc_attr( $name ) . '[action]" value="' . esc_attr( $action ) . '">';

		$field .= '</td>';

		// Output the trigger field.
		$field .= '<td class="lw-woo-gdpr-user-optins-field lw-woo-gdpr-user-optins-list-field lw-woo-gdpr-user-optins-field-input-icons lw-woo-gdpr-user-optins-field-trigger lw-woo-gdpr-user-optins-field-single">';

			// Handle the trash trigger.
			$field .= '<a class="lw-woo-gdpr-user-optins-field-trigger-item lw-woo-gdpr-user-optins-field-trigger-trash" href="' . esc_url( $delete ) . '" ' . $d_atrb . ' >';
				$field .= '<i class="lw-woo-gdpr-user-optins-field-trigger-icon dashicons dashicons-trash"></i>';
			$field .= '</a>';

			// Handle the sort trigger.
			$field .= '<span class="lw-woo-gdpr-user-optins-field-trigger-item lw-woo-gdpr-user-optins-field-trigger-sort hide-if-no-js">';
				$field .= '<i class="lw-woo-gdpr-user-optins-field-trigger-icon dashicons dashicons-sort"></i>';
			$field .= '</span>';

		$field .= '</td>';

	// Close the single div.
	$field .= '</tr>';

	// Echo it if requested.
	if ( ! empty( $echo ) ) {
		echo $field; // WPCS: XSS ok.
	}

	// Just return it.
	return $field;
}

/**
 * Display the possible opt-in statuses.
 *
 * @param  integer $user_id  The user ID we are dealing with.
 * @param  boolean $echo     Whether to echo the field or return it.
 *
 * @return HTML
 */
function optin_status_display_form( $user_id = 0, $echo = false ) {

	// Fetch my list of optins.
	$user_list  = optin_status_list( $user_id );

	// Bail without my list.
	if ( ! $user_list ) {
		return;
	}

	// Set an empty.
	$build  = '';

	// Add some title stuff.
	$build .= '<h3 class="lw-woo-gdpr-section-title">' . esc_html__( 'Your Data Opt-Ins', 'lw-woo-gdpr-user-optins' ) . '</h3>';

	// Describe what to do.
	$build .= '<p class="lw-woo-gdpr-section-subtitle">' . esc_html__( 'Below are the choices you have opted into. You can review and update them at any time.', 'lw-woo-gdpr-user-optins' ) . '</p>';

	// Set the form.
	$build .= '<form class="lw-woo-gdpr-user-optins-change-form" action="" method="post">';

		// Display our list of items.
		$build .= $user_list;

		// Open the paragraph for the submit button.
		$build .= '<p class="lw-woo-gdpr-user-optins-change-submit">';

			// Handle the nonce.
			$build .= wp_nonce_field( 'lw_woo_gdpr_user_optins_change_action', 'lw_woo_gdpr_user_optins_change_nonce', false, false );

			// The button / action combo.
			$build .= '<input class="woocommerce-Button button lw-woo-gdpr-user-optins-submit-button" name="lw_woo_gdpr_user_optins_change" value="' . __( 'Update Your Opt-Ins', 'lw-woo-gdpr-user-optins' ) . '" type="submit">';
			$build .= '<input name="action" value="lw_woo_gdpr_user_optins_change" type="hidden">';
			$build .= '<input id="lw_woo_gdpr_user_optins_change_user_id" name="lw_woo_gdpr_user_optins_change_user_id" value="' . absint( $user_id ) . '" type="hidden">';

		// Close the paragraph.
		$build .= '</p>';

	// Close the form.
	$build .= '</form>';

	// Echo if requested.
	if ( ! empty( $echo ) ) {
		echo $build; // WPCS: XSS ok.
	}

	// Return our build.
	return $build;
}

/**
 * Get the actual list markup of the statuses.
 *
 * @param  array   $fields   The field data we have to render.
 * @param  integer $user_id  The user ID that is being viewed.
 * @param  boolean $echo     Whether to echo or return it.
 *
 * @return HTML
 */
function optin_status_list( $user_id = 0, $echo = false ) {

	// Fetch my existing fields.
	$fields = Helpers\get_current_optin_fields();

	// Bail without fields or a user ID.
	if ( empty( $fields ) || empty( $user_id ) ) {
		return false;
	}

	// Set our empty.
	$build  = '';

	// Wrap it all in a list.
	$build .= '<ul class="lw-woo-gdpr-user-optins-items-list-wrap">';

	// Loop my fields to display.
	foreach ( $fields as $key => $field ) {

		// Check the status.
		$mkey   = Helpers\make_user_meta_key( $key );
		$status = get_user_meta( $user_id, $mkey, true );
		$check  = ! empty( $status ) ? true : false;

		// Set the label text accordingly.
		$label  = ! empty( $status ) ? sprintf( __( 'You have opted in to %s', 'lw-woo-gdpr-user-optins' ), esc_attr( $field['title'] ) ) : sprintf( __( 'You have not opted in to %s', 'lw-woo-gdpr-user-optins' ), esc_attr( $field['title'] ) );

		// Set new field args.
		$new_field_args = array(
			'name'      => 'lw_woo_gdpr_user_optins_items[' . esc_attr( $field['id'] ) . ']',
			'label'     => wp_kses_post( $label ),
			'checked'   => $check,
		);

		// Merge my new args.
		$setup  = wp_parse_args( $new_field_args, $field );

		// Open up our list item.
		$build .= '<li class="lw-woo-gdpr-user-optins-items-list-item">';

			// Include the actual checkbox.
			$build .= single_checkbox_field( $setup );

			// Add the little text for the required.
			$build .= ! empty( $field['required'] ) ? ' <span class="lw-woo-gdpr-user-optins-required-text">(' . __( 'required', 'lw-woo-gdpr-user-optins' ) . ')</span>' : '';

		// Close the list item.
		$build .= '</li>';
	}

	// Close out the list.
	$build .= '</ul>';

	// Echo if requested.
	if ( ! empty( $echo ) ) {
		echo $build; // WPCS: XSS ok.
	}

	// Return our build.
	return $build;
}

/**
 * A checkbox input.
 *
 * @param  array   $args   The field args I passed.
 * @param  boolean $echo   Whether to echo the field or return it.
 *
 * @return  HTML
 */
function single_checkbox_field( $args = array(), $echo = false ) {

	// Remove our type.
	unset( $args['type'] );

	// Set my default args.
	$base   = array(
		'id'        => microtime(),
		'required'  => 0,
		'checked'   => false,
	);

	// Parse my args.
	$args   = wp_parse_args( $args, $base );

	// Make sure I have a value to enter.
	$value  = ! empty( $args['value'] ) ? $args['value'] : 1;

	// Set my field name if none was passed.
	$name   = ! empty( $args['name'] ) ? $args['name'] : 'gdpr-user-optins[' . esc_attr( $args['id'] ) . ']';

	// Add a required check for the markup.
	$reqrd  = ! empty( $args['required'] ) ? 'required="required"' : '';

	// Set an empty.
	$field  = '';

	// Start the label setup.
	$field .= '<label class="woocommerce-form__label woocommerce-form-' . esc_attr( $args['id'] ) . '__label woocommerce-form__label-for-checkbox lw-woo-gdpr-user-optins-checkbox-label checkbox">';

		// Set the input box.
		$field .= '<input class="woocommerce-form__input woocommerce-form-' . esc_attr( $args['id'] ) . '__input-checkbox woocommerce-form__input-checkbox input-checkbox" name="' . esc_attr( $name ) . '" id="' . esc_attr( $args['id'] ) . '" type="checkbox" value="' . esc_attr( $value ) . '" ' . checked( $args['checked'], $value, false ) . ' ' . $reqrd . '>';

		// Add the label text if present.
		$field .= ! empty( $args['label'] ) ? '<span>' . esc_html( $args['label'] ) . '</span>' : '';

		// Add the required flag if present.
		$field .= ! empty( $args['required'] ) ? ' <span class="required">*</span>' : '';

	// And close the tag.
	$field .= '</label>';

	// Echo it if requested.
	if ( ! empty( $echo ) ) {
		echo $field; // WPCS: XSS ok.
	}

	// Just return it.
	return $field;
}

/**
 * Build the markup for an admin notice.
 *
 * @param  string  $message      The actual message to display.
 * @param  string  $type         Which type of message it is.
 * @param  boolean $dismiss      Whether it should be dismissable.
 * @param  boolean $show_button  Show the dismiss button (for Ajax calls).
 * @param  boolean $echo         Whether to echo out the markup or return it.
 *
 * @return HTML
 */
function admin_message_markup( $message = '', $type = 'error', $dismiss = true, $show_button = false, $echo = false ) {

	// Bail without the required message text.
	if ( empty( $message ) ) {
		return;
	}

	// Set my base class.
	$class  = 'notice notice-' . esc_attr( $type ) . ' lw-woo-gdpr-user-optins-admin-message';

	// Add the dismiss class.
	if ( $dismiss ) {
		$class .= ' is-dismissible';
	}

	// Set an empty.
	$field  = '';

	// Start the notice markup.
	$field .= '<div class="' . esc_attr( $class ) . '">';

		// Display the actual message.
		$field .= '<p><strong>' . wp_kses_post( $message ) . '</strong></p>';

		// Show the button if we set dismiss and button variables.
		$field .= $dismiss && $show_button ? '<button type="button" class="notice-dismiss">' . screen_reader_text() . '</button>' : '';

	// And close the div.
	$field .= '</div>';

	// Echo it if requested.
	if ( ! empty( $echo ) ) {
		echo $field; // WPCS: XSS ok.
	}

	// Just return it.
	return $field;
}

/**
 * Build the markup for an account page notice.
 *
 * @param  string  $message      The actual message to display.
 * @param  string  $type         Which type of message it is.
 * @param  boolean $echo         Whether to echo out the markup or return it.
 *
 * @return HTML
 */
function account_message_markup( $message = '', $type = 'error', $wrap = false, $echo = false ) {

	// Bail without the required message text.
	if ( empty( $message ) ) {
		return;
	}

	// Get my dismiss link.
	$dslink = Helpers\get_account_tab_link();

	// Set an empty.
	$field  = '';

	// Start the notice markup.
	$field .= '<div class="lw-woo-gdpr-user-optins-notice lw-woo-gdpr-user-optins-notice-' . esc_attr( $type ) . '">';

		// Display the actual message.
		$field .= '<p>' . wp_kses_post( $message ) . '</p>';

		// And our dismissal button.
		$field .= '<a class="lw-woo-gdpr-user-optins-notice-dismiss" href="' . esc_url( $dslink ) . '">';
			$field .= screen_reader_text() . '<i class="dashicons dashicons-no"></i>';
		$field .= '</a>';

	// And close the div.
	$field .= '</div>';

	// Add the optional wrapper.
	$build  = ! $wrap ? $field : '<div class="lw-woo-gdpr-user-optins-account-notices">' . $field . '</div>';

	// Echo it if requested.
	if ( ! empty( $echo ) ) {
		echo $build; // WPCS: XSS ok.
	}

	// Just return it.
	return $build;
}

/**
 * Set the markup for the screen reader text on dismiss.
 *
 * @return HTML
 */
function screen_reader_text() {
	return '<span class="screen-reader-text">' . esc_html__( 'Dismiss this notice.', 'lw-woo-gdpr-user-optins' ) . '</span>';
}
