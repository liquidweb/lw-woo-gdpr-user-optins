
/**
 * Clear the new field inputs.
 */
function clearNewFieldInputs( showIcon ) {

	// Map out all the fields items and clear them out.
	jQuery( '.lw-woo-gdpr-user-optins-new-fields-row' ).map( function() {

		// Handle the text input fields.
		jQuery( this ).find( 'input[type=text]' ).val( '' );

		// And my checkbox.
		jQuery( this ).find( 'input[type=checkbox]' ).prop( 'checked', false );
	});

	// Set the focus to the title field.
	jQuery( '.lw-woo-gdpr-user-optins-new-fields-row #lw-woo-gdpr-user-optin-title-new' ).focus();

	// Add the success icon.
	if ( showIcon ) {

		// Set the icon.
		var iconCheck = jQuery( 'tr.lw-woo-gdpr-user-optins-new-fields-row span.lw-woo-gdpr-user-optins-field-new-success' );

		// Remove the class.
		jQuery( iconCheck ).removeClass( 'lw-woo-gdpr-user-optins-field-hidden' );

		// Then hide it again.
		hideAgain = setTimeout( function() {
			jQuery( iconCheck ).addClass( 'lw-woo-gdpr-user-optins-field-hidden' );
		}, 3000 );
	}
}

/**
 * Now let's get started.
 */
jQuery(document).ready( function($) {

	/**
	 * Quick helper to check for an existance of an element.
	 */
	$.fn.divExists = function(callback) {

		// Slice some args.
		var args = [].slice.call( arguments, 1 );

		// Check for length.
		if ( this.length ) {
			callback.call( this, args );
		}
		// Return it.
		return this;
	};

	/**
	 * Set some vars for later
	 */
	var tabBody = 'body.lw-woo-gdpr-user-optins-admin-tab';
	var saveForm = 'form#mainform';
	var saveSubmit = false;

	var sortTable = 'table.lw-woo-gdpr-user-optins-list-table-wrap';
	var sortBody = 'table.lw-woo-gdpr-user-optins-list-table-wrap tbody';

	/**
	 * Set up the sortable table rows.
	 */
	$( sortTable ).divExists( function() {

		// Make our table sortable.
		$( sortBody ).sortable({
			handle: '.lw-woo-gdpr-user-optins-field-trigger-icon',
			update: function( event, ui ) {

				// Build the data structure for the call with the updated sort order.
				var data = {
					action: 'lw_woo_gdpr_optins_sort',
					sorted: $( sortBody ).sortable( 'toArray', { attribute: 'data-key' } )
				};

				// Send the post request, we don't actually care about the response.
				jQuery.post( ajaxurl, data );
			},
		});
	});

	// Don't even think about running this anywhere else.
	$( tabBody ).divExists( function() {

		/**
		 * Set the button variable to handle the two submits.
		 */
		$( saveForm ).on( 'click', 'button', function() {
			saveSubmit = $( this ).hasClass( 'lw-woo-gdpr-user-optin-add-new-button' ) ? true : false;
		});

		/**
		 * Add a new item into the table.
		 */
		$( saveForm ).submit( function( event ) {

			// Bail on the actual save button.
			if ( saveSubmit !== true ) {
				return;
			}

			// Stop the actual submit.
			event.preventDefault();

			// We call this a sledgehammer because Woo doesn't register
			// the callback until the user has clicked one of the tabs.
			$( '.woo-nav-tab-wrapper a' ).off();

			// Fetch the nonce.
			var newNonce = $( 'input#lw_woo_gdpr_new_optin_nonce' ).val();

			// Bail real quick without a nonce.
			if ( '' === newNonce || undefined === newNonce ) {
				return false;
			}

			// Build the data structure for the call.
			var data = {
				action: 'lw_woo_gdpr_optins_add_new',
				required: $( 'input#lw-woo-gdpr-user-optin-required-new' ).is( ':checked' ),
				title: $( 'input#lw-woo-gdpr-user-optin-title-new' ).val(),
				label: $( 'input#lw-woo-gdpr-user-optin-label-new' ).val(),
				nonce: newNonce
			};

			// Send out the ajax call itself.
			jQuery.post( ajaxurl, data, function( response ) {

				// Refresh the sortable table.
				$( sortBody ).sortable( 'refreshPositions' );

				// Handle the failure.
				if ( response.success !== true ) {

					// Set our message.
					$( tabBody ).find( '.woocommerce h1:first' ).after( response.data.notice );

					// And just bail.
					return false;
				}

				// We got table row markup, so show it.
				if ( response.data.markup !== '' ) {

					// Clear the new field inputs.
					clearNewFieldInputs( true );

					// Add the row itself.
					$( 'table#lw-woo-gdpr-user-optins-list-table tr:last' ).after( response.data.markup );
				}
			}, 'json' );
		});

		/**
		 * Handle the individual item deletion.
		 */
		$( sortTable ).on( 'click', 'a.lw-woo-gdpr-user-optins-field-trigger-trash', function( event ) {

			// Stop the actual click.
			event.preventDefault();

			// Set my field block.
			var fieldBlock  = $( this ).parents( 'tr.lw-woo-gdpr-user-optins-single-row' );

			// Fetch my field ID and nonce.
			var fieldID     = $( this ).data( 'field-id' );
			var fieldNonce  = $( this ).data( 'nonce' );

			// Bail real quick without a nonce.
			if ( '' === fieldNonce || undefined === fieldNonce ) {
				return false;
			}

			// Handle the missing field ID.
			if ( '' === fieldID || undefined === fieldID ) {
				return false; // @@todo need a better return.
			}

			// Build the data structure for the call.
			var data = {
				action: 'lw_woo_gdpr_optins_delete_row',
				field_id: fieldID,
				nonce: fieldNonce
			};

			// Send out the ajax call itself.
			jQuery.post( ajaxurl, data, function( response ) {

				// Refresh the sortable table.
				$( sortBody ).sortable( 'refreshPositions' );

				// Handle the failure.
				if ( response.success !== true ) {

					// Set our message.
					$( tabBody ).find( '.woocommerce h1:first' ).after( response.data.notice );

					// And just bail.
					return false;
				}

				// No error, so remove the field
				if ( response.success === true || response.success === 'true' ) {

					// Fade out the field and then remove it.
					$( sortTable ).find( fieldBlock ).fadeOut( 500, function() {
						$( this ).remove();
					});
				}
			}, 'json' );
		});

		/**
		 * Handle the notice dismissal.
		 */
		$( tabBody ).on( 'click', '.notice-dismiss', function() {
			$( tabBody ).find( '.lw-woo-gdpr-user-optins-admin-message' ).remove();
		});


	});

//********************************************************
// You're still here? It's over. Go home.
//********************************************************
});
