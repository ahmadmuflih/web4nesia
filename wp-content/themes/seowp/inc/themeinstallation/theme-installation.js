/*
 * Theme Installation back-end JavaScript
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

	jQuery(document).ready(function($){


		function ajax_importcontent_part(content_type, step_current, step_desc, step_current_no, steps_total){
			// sets defaults
			content_type = typeof content_type !== 'undefined' ?  content_type : 'alldemocontent';
			step_current = typeof step_current !== 'undefined' ?  step_current : '';
			step_desc = typeof step_desc !== 'undefined' ?  step_desc : '';
			step_current_no = typeof step_current_no !== 'undefined' ?  step_current_no : 0;
			steps_total = typeof steps_total !== 'undefined' ?  steps_total : 100;

			var progressbar_position = ( parseInt(step_current_no) + 2) * (100 / steps_total); // 100% / 20steps = 5% per step

			// var progressbar_position = (parseInt(step_current)+1) * 5; // 100% / 20steps = 5% per step
			var currentdate = new Date();
			console.info( "--------------------" );
			console.info( 'current part id: ' + step_current );
			console.info( 'current part no: ' + step_current_no );
			console.info( 'current part desc: ' + step_desc );
			console.info( 'steps_total: ' + steps_total );

			var step_css_class = '.step-demoimport';
			if ( content_type == 'basic-templates' ) {
				step_css_class = '.step-basic_config';
			}

			$(step_css_class + " .import-progress-desc").text(step_desc);

			console.info( currentdate.getHours() + ":"
							+ currentdate.getMinutes() + ":"
							+ currentdate.getSeconds() + "  >>  "
							+ step_current);

			if(step_current)
				step_current = '&importcontent_step_current_id=' + step_current;
			else
				step_current = '&importcontent_step_current_id';

			$.ajax({
				cache: false,
				url: location.protocol + '//' + location.host + location.pathname + "?page=lbmn-demo-import&importcontent=" + content_type + step_current,
				success: function(response){

					$(step_css_class + ' .progress-indicator').css('width', progressbar_position + '%');

					console.info ( location.protocol + '//' + location.host + location.pathname + "?page=lbmn-demo-import&importcontent=" + content_type + step_current );
					// var response_jq = $(response);
					// console.info( $('.wpbody-content .ajax-log', response_jq['div#wpwrap']) );
					// console.info( $('.wpbody-content .ajax-log', response_jq['div#wpwrap']).text() );
					// console.info('------------------------------------------------');

					if ( $(response).find('#importcontent_step_next_id').length > 0 ) {
						console.info( 'part' + step_current );

						if ( $(response).find(".ajax-request-error").length > 0 ) {
							$(".lumberman-message.quick-setup .step-basic_config .error").css('display', 'inline-block');
							$(".lumberman-message.quick-setup").after('<div class="error-log-window" style="display:none"></div>');
							$(".error-log-window").append( $(response).find(".ajax-log") );
							$('.lumberman-message.quick-setup .step-basic_config .error-log-window').css('display','inline-block');

							// config process succeeded
						}else{
							ajax_importcontent_part( content_type, $(response).find('#importcontent_step_next_id').val(), $(response).find('#importcontent_step_current_descr').val(), $(response).find('#importcontent_step_current_no').val(), $(response).find('#importcontent_steps_total').val() );
						}

					// no #importcontent_step_next_id field in the content
					} else {

						// config process failed
						if ( $(response).find(".ajax-request-error").length > 0 ) {
							$(step_css_class).removeClass('loading');

							$(".lumberman-message.quick-setup " + step_css_class + " .error").css('display', 'inline-block');
							$(".lumberman-message.quick-setup").after('<div class="error-log-window" style="display:none"></div>');
							$(".error-log-window").append( $(response).find(".ajax-log") );
							$('.lumberman-message.quick-setup ' + step_css_class + ' .error-log-window').css('display','inline-block');

							// config process succeeded
						} else {
							// update option "LBMN_THEME_NAME . '_democontent_imported'"
							// with 'true' value

							$.ajax({
								cache: false,
								url: location.protocol + '//' + location.host + location.pathname + "?demoimport=" + content_type + "completed"
							});

							if ( content_type == 'alldemocontent' ) {

								// Build Initial Site Cache.

								var importantPages = [
									'home-ver2',
									'about',
									'services',
									'case-studies',
									'blog',
									'contact-us',
									'request-a-free-seo-analysis',
									// 'pricing',

									// 'resources',
									// 'clients',
									// 'testimonials',
									// 'contact-us-2',
									// 'order-digital-marketing-services',

									// 'services-social-media-marketing',
									// 'services-search-engine-optimization',
									// 'services-pay-per-click-management-ppc',
									// 'services-web-development',
									// 'services-web-design',
									// 'services-content-marketing',
									// 'services-content-strategy',
									// 'services-seo-copyrighting',
									// 'services-email-marketing',
									// 'services-local-seo',
									// 'services-mobile-marketing',
									// 'services-affiliate-management',
									// 'services-company-online-presence-analysis-and-audit',
									// 'services-conversion-rate-optimization',
									// 'services-conversion-rate-optimization-cro',
									// 'services-digital-consultancy',
									// 'services-reputation-management',
								];

								jQuery(step_css_class).find('.import-progress-desc').text('Building basic site cache...');

								// Form initial site cache for important pages:
								jQuery.each( importantPages, function(index, val) {
									jQuery('body').append( '<iframe class="lbmn-cache-iframe" id="lbmn-cache-iframe-' + val + '" src="'+ LBMNWP.siteurl + '/?pagename=' + val +'" ></iframe>' );

								});

								jQuery('.lbmn-cache-iframe').each(function(index, el) {
									jQuery(el).load(function() {
										jQuery(el).remove();
										// Cache built at this point. Remove iframe.

										if ( jQuery('.lbmn-cache-iframe').size() == 0 ) {
											jQuery(step_css_class).addClass('step-completed');
											jQuery(step_css_class).removeClass('loading');
										}
									});
								});
							} else {
								jQuery(step_css_class).addClass('step-completed');
								jQuery(step_css_class).removeClass('loading');
							}
						}
					}
				}
			}); //ajax
		}

		/**
		 * ----------------------------------------------------------------------
		 * Theme installation wizard action:
		 * 0. Pre-installation checkup
		 */

		$("#pre-install-checkup").click(function(event) {
			event.preventDefault();

			$('#pre-install-checkup-details').css('display','block');

			$('.step-checkup').addClass('step-completed');
		});

		/**
		 * Fix permalinks action.
		 */
		$("#fix-permalinks").click(function(event) {
			event.preventDefault();
			var nonce = event.target.getAttribute('data-nonce');

			jQuery.ajax({
				url: ajaxurl,
				data: {
					action: 'lbmn_fix_permalinks',
					nonce: nonce,
				}
			});

			$('#checkup-permalinks').fadeOut('slow'); //.delay(1000).hide();
		});



		/**
		 * ----------------------------------------------------------------------
		 * Theme installation wizard action:
		 * 1. Plugins installation
		 */

		$("#do_plugins-install").click(function(event) {
			if ( $(".step-plugins").hasClass('step-completed') ) {
				event.preventDefault();
			} else {
				// Show spinner.
				$("#theme-setup-step-1").addClass('loading');
			}
		});

		// @todo: can delete it?
		// Check if all plugins were installed manually
		// If all installed: redirect to themes.php with url var set
		/*
		var window_hash = window.location.hash.substr(1);
		if ( window_hash === 'checkifallinstalled' ) {
			if ( $(".wp-list-table.plugins tr.inactive").length < 1 ) {
				window.location.replace(location.protocol + '//' + location.host + location.pathname+"?plugins=installed");
			}
		}
		*/

		/**
		 * ----------------------------------------------------------------------
		 * Theme installation wizard action:
		 * 2. Configure basic settings
		 */

		$("#do_basic-config").click(function(event) {
			event.preventDefault();

			// Do not run multiply times
			if ( ! $("#theme-setup-step-2").hasClass('step-completed') ) {
				// Do not run before step 1
				if ( $("#theme-setup-step-1").hasClass('step-completed') ) {

					$("#theme-setup-step-2").addClass('loading');
					ajax_importcontent_part('basic-templates');

				} else {
					$( "#theme-setup-step-1" ).effect( "bounce", 1000);
				}
			}
		});

		// Show error log functionality
		$('.show-error-log').on('click', function(event) {
			event.preventDefault();
			/* Act on the event */
			$(".error-log-window").show();
		});

		/**
		 * ----------------------------------------------------------------------
		 * Theme installation wizard action:
		 * 3. Import demo content
		 */

		$("#do_demo-import").click(function(event) {
			event.preventDefault();

			// Do not run before step 1
			if ( $("#theme-setup-step-1").hasClass('step-completed') ) {
				// Do not run before step 2
				if ( $("#theme-setup-step-2").hasClass('step-completed') ) {
					$(".step-demoimport").addClass('loading');
					ajax_importcontent_part();
				} else {
					$( "#theme-setup-step-2" ).effect( "bounce", 1000);
				}
			} else {
				$( "#theme-setup-step-1" ).effect( "bounce", 1000);
			}
		});

		/**
		 * Call Ajax action to mark completed install.
		 */
		jQuery(document).on( 'click', '#hide-theme-installation-wizzard', function(event) {

			var nonce = event.target.getAttribute("data-nonce");

			jQuery.ajax({
				url: ajaxurl,
				data: {
					action: 'lbmn_hide_theme_installation_wizzard',
					nonce: nonce,
				}
			});

			var link = $('#toplevel_page_dslc_plugin_options a').attr('href');
			window.location.replace( link );
		});



	}); //JQUERY.ready().

	/**
	 * ----------------------------------------------------------------------
	 * Special Functions That Called from TGMPA php file to update
	 * installer required plugins status.
	 *
	 * @todo: Not used anymore. Delete it or find a way to use again.
	 */

	// This function will be caled on the end of plugin installation from
	// hidden 'iframe-plugins-install' iframe
	window.pluginsInstalledSuccessfully = function () {
		// disable spinner
		$("#theme-setup-step-1").removeClass('loading');
		// mark installer step as completed
		$(".lumberman-message.quick-setup .step-plugins").addClass("step-completed");
		// hide standard TGMPA notice
		$("#setting-error-tgmpa").hide();
	}

	window.pluginsInstallFailed = function () {
		// disable spinner
		$("#theme-setup-step-1").removeClass('loading');
		// show error message
		$(".lumberman-message.quick-setup .step-plugins .error").css("display","inline-block");
	}


})(jQuery);