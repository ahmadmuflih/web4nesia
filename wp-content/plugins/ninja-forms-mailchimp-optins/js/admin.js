(function($) {
	$(document).ready(function($){

		var validate_mailchimp_api_key = function( root_el, api_key ) {
			root_el.css('position', 'relative');

			// Create / re-use dashicon element.
			if ( root_el.find('.dashicons').length ) {
				root_el
					.find('span')
					.removeClass('dashicons-yes')
					.removeClass('dashicons-no-alt')
					.addClass('dashicons-update');
			} else {
				root_el
					.find('input')
					.css('padding-right', '30px');

				root_el
					.find('input')
					.after($('<span class="dashicons dashicons-update" style="position: absolute; right: 15px; top: 20px;"></span>'));
			}

			// Ajax
			$.post(
				ajaxurl,
				{
					action: 'mailchimp_optins_validate_key',
					mailchimp_api_key: api_key
				},
				function(response) {
					if ( response.success === true ) {
						root_el.find('span')
							.removeClass('dashicons-update')
							.removeClass('dashicons-no-alt')
							.addClass('dashicons-yes');
					} else {
						root_el.find('span')
							.removeClass('dashicons-update')
							.addClass('dashicons-yes')
							.addClass('dashicons-no-alt');
					}
					root_el.find('input').focus();
				},
				'json'
			);
		};

		$(document).on( 'change', 'input[name="ninja_forms[mailchimp_api_key]"]', function() {
			validate_mailchimp_api_key( $(this).parents('td'), $(this).val() );
		});

		setTimeout(function(){
			var el = $('input[name="ninja_forms[mailchimp_api_key]"]');
			validate_mailchimp_api_key(el.parents('td'), el.val());
		}, 100);

	});
})(jQuery);
