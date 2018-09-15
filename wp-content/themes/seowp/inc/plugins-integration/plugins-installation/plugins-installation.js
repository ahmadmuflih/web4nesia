/*
 * Plugins Installation back-end JavaScript
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Custom JavaScript used to improve/extend some bundled plugins UI,
 * run actions for theme installation wizard
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    GNU GPL, Version 3
 * @link       http://themeforest.net/user/lumbermandesigns
 */

(function ($) {
	"use strict";

	// Helper function to get the value from URL Parameter
	var QueryString = function () {
		// This function is anonymous, is executed immediately and
		// the return value is assigned to QueryString!
		var query_string = {};
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			// If first entry with this name
			if (typeof query_string[pair[0]] === "undefined") {
			query_string[pair[0]] = pair[1];
			// If second entry with this name
			} else if (typeof query_string[pair[0]] === "string") {
			var arr = [ query_string[pair[0]], pair[1] ];
			query_string[pair[0]] = arr;
			// If third or later entry with this name
			} else {
			query_string[pair[0]].push(pair[1]);
			}
		}
			return query_string;
	} ();

	jQuery(document).ready(function($){

		/**
		 * ----------------------------------------------------------------------
		 * TGM plugin activation page improvements.
		 */

		// Make sure we are on the install required plugins page.
		if ( $('body').hasClass('appearance_page_install-required-plugins') ) {

			// Duplicate action link next to "Status" column
			$('.row-actions a').each(function(index, el) {
				var statusColumns = $(this).closest('tr').find('.type');
				$(this).clone().addClass('button button-primary').prependTo( statusColumns );
			});

			// Remove source and version columns, they only confuse users
			$('.column-source').hide();
			$('.column-version').hide();

			/**
			 * --------------------------------------------------------------------
			 * Automatic plugins installation/update.
			 */

			if (QueryString.autoinstall) {
				/*$('.check-column input').attr('checked','checked').change();
				$('select[name=action]').find('option').removeAttr('selected');
				$('select[name=action] option[value=tgmpa-bulk-install]').attr('selected','selected');
				$('select[name=action]').change();
				$('input#doaction').trigger('click');*/


				$('body').addClass('lbmn-autoinstall');

				var actionsQueue = [];

				$(".has-row-actions").each(function() {

					var actionUrl = $(this).find(".row-actions a").attr('href');
					var pluginName = $(this).find("strong a").text();

					if ( pluginName === '' ) {
						pluginName = $(this).find("strong").text();
					}

					// Add plugin name and action link into the queue.
					actionsQueue.unshift({name:pluginName, action:actionUrl});
				});

				if ( actionsQueue.length !== 0 ) {
					$('#tgmpa-plugins').before('<div id="lbmn-tgmpa-wait"><span class="dashicons dashicons-admin-generic rotating"></span>  Installing required plugins. <strong>Don&rsquo;t refresh this page.</strong> We will send you back automatically.</div>')
				}

				// Launch the queue processing and check the status every 2 seconds.
				var nextStep = window.setInterval(function(){ runQueueStep(actionsQueue) }, 2000);

				/**
				 * ------------------------------------------------------------
				 * Go over actionsQueue array and activate/install each plugin
				 * in this array.
				 */

				var runQueueStep = function ( queue ) {

					console.log( "Processing queue:" ); console.log( queue );

					if ( queue.length === 0 ) {
						console.log( 'All plugins installed/activated.' );
						window.clearInterval(nextStep);

						// Check if page reload needed or send user back to installer.
						checkStatus();

					} else if ( $('.lbmn-installer-working').length ) {

						console.log('Previous process is still working. Need to wait more.');

					} else if ( queue.length !== 0 ) {

						console.log( 'Running plugin action for the ' + queue[queue.length-1]['name'] );

						// Load plugin action URL (install/update) in an iframe.
						lbmnLoadLinkInIframe( queue[queue.length-1]['action'], queue[queue.length-1]['name'] );

						// delete element from array
						queue.splice(queue.length-1, 1);
					}
				};

			}
		}

	});

	/**
	 * ------------------------------------------------------------
	 * Check if page reload needed to work on more plugin installs.
	 * Otherwise send the user back to theme installer.
	 */

	var repeatCheck;

	var checkStatus = function () {
		if ( $('#tgmpa-plugins #the-list > tr[status="error"]').length ) {
			// Errors in plugins installation. Stop here for now.
			window.clearTimeout(repeatCheck);

			$('body').removeClass('lbmn-autoinstall');
			$('#lbmn-tgmpa-wait').text('Something went wrong. Please, check error status bellow.')
			$('#lbmn-tgmpa-wait dashicons').removeClass('dashicons-admin-generic rotating').addClass('dashicons-warning');

		} else if ( $('#tgmpa-plugins #the-list > tr[status="working"]').length ) {
			// Not all the plugins installed yet.
			repeatCheck = window.setTimeout(checkStatus, 2000);
		} else {
			window.clearTimeout(repeatCheck);

			var linkToPluginInstaller = $('#adminmenu a.current').attr('href');
			lbmnCheckPluginsQueue( linkToPluginInstaller );
		}
	};

	/**
	 * Check if plugin installation/updates are completed
	 * or page reload and another cycle needed.
	 *
	 * @param  {string} address Src. attribute for the iframe.
	 * @return {void}
	 */
	var lbmnCheckPluginsQueue = function ( address ){
		console.log( 'Check Plugins Queue' );

		if ( address === undefined ) {
			return;
		}

		$.ajax( address )
			.done(function() {
				// Can load plugins installation page - More work to do.
				// Reload current screen.
				window.location.reload(true);
			})
			.fail(function() {
				// Can't load plugins installation page anymore - All done.
				// Redirect the users back to the Appearance > Themes page.

				if ( jQuery( 'a[href="themes.php?page=seowp-theme-update"]' ).length == 1 ) {
					window.location.replace('themes.php?page=seowp-theme-update');
				} else {
					window.location.replace('themes.php?page=seowp-theme-install');
				}
			});
	};

	/**
	 * Open plugin install/update/activation action in a hidden iframe.
	 *
	 * @param  {string} link       Link to open in iframe.
	 * @param  {string} pluginName Name of the plugin (needed for error msgs).
	 * @return {void}
	 */
	var lbmnLoadLinkInIframe = function ( link, pluginName ){

		if ( link === undefined ) {
			return;
		}

		if ( pluginName === undefined ) {
			pluginName = '';
		}

		// Add rotating arrows icon.
		var plugin_cell = $('a[href="' + link + '"]').closest('.has-row-actions');
		$(plugin_cell).append(' <span class="lbmn-installer-working dashicons dashicons-update rotating"></span>');
		$(plugin_cell).closest('tr').attr('status', 'working');

		var random_id = Math.floor(Math.random() * (999999 - 0 + 1)) + 0;
		$('body').append('<iframe id="lbmn-plugin-autoupdate-iframe-' + random_id + '" src="'+ link +'" class="lbmn-plugin-autoupdate-iframe"></iframe>');

		$('iframe#lbmn-plugin-autoupdate-iframe-' + random_id).load(function() {

			$('a[href="' + link + '"]').closest('.has-row-actions').find('.lbmn-installer-working').remove();

			if ( $(this).contents().find('#message.error').length ) {
				var error_message = $(this).contents().find('#message.error').text();
				var error_message_search = error_message.split(' ').join('+'); // URL-friendly search query for the docs.

				// Error icon and action link.
				$('a[href="' + link + '"]').closest('.has-row-actions').append(
					'<span class="lbmn-installer-error dashicons dashicons-warning"></span>');
				$('a[href="' + link + '"]').closest('.has-row-actions').append(
					'<a href="#installer-errors" class="lbmn-installer-error-message">Plugin install/update failed</a>');

				// Error message explanation.
				$('#tgmpa-plugins').append(
					'<a name="installer-errors">&nbsp;</a><br />'
					+ '<div class="lbmn-installer-error-explanation error"><strong>'
					+ pluginName + ' Plugin Error:</strong> ' + error_message
					+ ' <br /><span class="lbmn-installer-error-solution"><span class="dashicons dashicons-sos"></span> <a href="http://docs.lumbermandesigns.com/search?query=' 
						+ error_message_search
						+ '" target="_blank">Check documentation</a>'
					+ ' or <a href="http://themeforest.net/item/seo-wp-social-media-and-digital-marketing-agency/8012838/support/contact/" target="_blank">contact our support team</a> for help.</span>'
					+ '</div>');

				// Update the plugin status in attributes as status = 'error'.
				$('a[href="' + link + '"]').closest('tr').attr('status', 'error');
			} else {
				$('a[href="' + link + '"]').closest('.has-row-actions').append('<span class="lbmn-installer-done dashicons dashicons-yes"></span>');
				$('a[href="' + link + '"]').closest('tr').attr('status','completed');
			}

			$('iframe#lbmn-plugin-autoupdate-iframe-' + random_id).remove();
		});
	};

})(jQuery);
