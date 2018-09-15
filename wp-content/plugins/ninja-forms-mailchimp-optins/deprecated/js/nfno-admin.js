(function($) {

	$(document).ready(function($){

		/**
		 * Reload List
		 *
		 * Handles 'Reload List' button click to fetch and update drop down with
		 * latest available MailChimp subscriber lists.
		 */
		$(document).on( 'click', '.optin_mailchimp_list_reload', function(){
			var api_key = $(this)
				.parents('.menu-item-settings')
				.find('.ninja-forms-_optin_mailchimp-optin_mailchimp_api_key')
				.val();

			var parent_selector = $(this)
				.parents('.menu-item-settings')
				.attr('id');

			// Ajax call
			$.ajax({
				url: ajaxurl,
				data: {
					'action':   'nfno_mailchimp_list',
					'api_key':  api_key,
					'parent_selector': parent_selector
				},
				dataType: 'JSON',
			}).done(function( response ) {
				if (response.success === true) {
					// Update cache list
					$(document)
						.find( '#' + response.parent_selector )
						.find( '.ninja-forms-_optin_mailchimp-optin_mailchimp_list_cache' )
						.val( response.cache );

					// Find the list
					var select = $(document)
						.find( '#' + response.parent_selector )
						.find( '.ninja-forms-_optin_mailchimp-optin_mailchimp_list' );

					// Clear the list
					select.empty();

					// Insert list details into dropdown
					$.each( response.lists, function(i, item) {
						select.append($('<option value="' + item.id + '">' + item.name + '</option>'));
					});
				}
			});

			return false;
		});

		/**
		 * Display
		 *
		 * User experience improvement, show/hides Checkbox Text option when
		 * based on drop down selection.
		 */
		$(document).on( 'change', '.ninja-forms-_optin_mailchimp-optin_mailchimp_display', function() {

			var text = $(this)
				.parents('.menu-item-settings')
				.find('.container-optin_mailchimp_checkbox_text');

			var state = $(this)
				.parents('.menu-item-settings')
				.find('.container-optin_mailchimp_checkbox_state');

			if ( 'checkbox' == $(this).val() ) {
				text.css('display', 'block');
				state.css('display', 'block');
			} else {
				text.css('display', 'none');
				state.css('display', 'none');
			}
		});

		/**
		 * API Key
		 *
		 * User experience improvement, handles API key check and updates status
		 * image accordingly.
		 */
		$(document).on( 'change', '.ninja-forms-_optin_mailchimp-optin_mailchimp_api_key', function() {
			var api_key = $(this).val();
			var parent_selector = $(this)
				.parents('.menu-item-settings')
				.attr('id');

			// Ajax call
			$.ajax({
				method: 'GET',
				url: ajaxurl,
				data: {
					'action': 'nfno_mailchimp_key_check',
					'api_key': api_key,
					'parent_selector': parent_selector
				},
				dataType: 'JSON'
			}).done(function( response ) {
				if (response.success === true) {
					// Find the list
					var container = $(document)
						.find('#' + response.parent_selector )
						.find('.ninja-forms-_optin_mailchimp-optin_mailchimp_api_key')
						.parent();

					container.find('img').attr( 'src', container.find('img').attr('data-base-url') + 'status-ok.png' );
					container.find('input[type="hidden"]').val('1');
				} else {
					// Find the list
					var container = $(document)
						.find('#' + response.parent_selector )
						.find('.ninja-forms-_optin_mailchimp-optin_mailchimp_api_key')
						.parent();

					container.find('img').attr('src', container.find('img').attr('data-base-url') + 'status-err.png' );
					container.find('input[type="hidden"]').val('0');
				}
			});

		});

		/**
		 * Add New ( MailChimp mapping field)
		 *
		 * Adds new field for custom data mapping.
		 */
		$(document).on( 'click', '.ninja-forms-field-add-mailchimp-field', function(){
			var parent_selector = $(this)
				.parents('.menu-item-settings')
				.attr('id');

			var field_id = $(this)
				.parents('.menu-item-settings')
				.find('#field-info strong')
				.text();

			var fields = $(this)
				.parents('.menu-item-settings')
				.find('.mailchimp-field-container table').length;

			$.ajax({
				method: 'GET',
				url: ajaxurl,
				data: {
					'action': 'nfno_add_mailchimo_field',
					'parent_selector': parent_selector,
					'field_id': field_id,
					'fields': fields
				},
				dataType: 'JSON'
			}).done(function( response ) {
				if (response.success === true) {
					// Find the list
					var container = $(document)
						.find('#' + response.parent_selector )
						.find('.mailchimp-field-container');

					// Append the item to the end of the list
					container.append(response.html);
				}
			});

			return false;
		});

		/**
		 * Remove list item
		 *
		 * Remove field from custom data mapping list.
		 */
		$(document).on( 'click', '.mailchimp-field-list-remove', function(){
			$(this)
				.parents('.mailchimp-field-list-options')
				.remove();

			return false;
		});

	});

})(jQuery);
