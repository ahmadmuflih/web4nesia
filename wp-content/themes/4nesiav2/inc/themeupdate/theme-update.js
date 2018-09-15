/*
 * Theme Update back-end JavaScript
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Custom JavaScript used to run actions for theme installation wizard
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



	});

	/**
	 * ----------------------------------------------------------------------
	 * On click events.
	 */

	/**
	 * Theme Update:
	 * 1. Backup Needed
	 */
	$("#step-backup").click(function(event) {
		// event.preventDefault();

		// Do not run multiply times
		if ( ! $("#theme-update-step-1.step-backup").hasClass('step-completed') ) {

				// $("#step-backup").text('I completed site backup');
				$("#theme-update-step-1.step-backup").addClass('step-completed');


		}
	});

	/**
	 * Theme Update:
	 * 2. Migration Mega Main Menu
	 */
	jQuery(document).on( 'click', '#migration-mmm', function(event) {
		event.preventDefault();
		mmm_migration();
	});

	/**
	 * Theme Update:
	 * 3. Migration Ninja Forms
	 */
	jQuery(document).on( 'click', '#migration-nf', function(event) {
		event.preventDefault();
		nf_migration();
	});

	/**
	 * Call Ajax action to dismiss the admin notice.
	 */
	jQuery(document).on( 'click', '.lbmn-notice .notice-dismiss', function(event) {

		var notice_id = event.target.parentNode.id;
		var nonce = event.target.parentNode.getAttribute("data-nonce");

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'lbmn_dismiss_notice',
				nonce: nonce,
				notice_id: notice_id,
			}
		});
	});

	/**
	 * Call Ajax action to mark completed update.
	 */
	jQuery(document).on( 'click', '#theme-update-final a', function(event) {

		var nonce = event.target.getAttribute("data-nonce");

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'lbmn_theme_update_completed',
				nonce: nonce,
			}
		});

		$('#theme-update-final').addClass('step-completed');
		$('.lbmn-can-hide-wizzard').css('display', 'block');
	});

	/**
	 * Call Ajax action to mark completed update.
	 */
	jQuery(document).on( 'click', '#hide-theme-update-wizzard', function(event) {
		var nonce = event.target.getAttribute("data-nonce");
		jQuery( event.target ).prepend('<span class="dashicons dashicons-update rotating"></span> ');

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'lbmn_hide_theme_update_wizzard',
				nonce: nonce,
			}
		}).done(function() {
			// var link = jQuery('#toplevel_page_dslc_plugin_options > a').attr('href');
			// window.location.replace( link );
			window.location.replace( 'admin.php?page=dslc_plugin_options' );
			// window.location.replace(location.protocol + '//' + location.host + 'admin.php?page=dslc_plugin_options');
		});

	});

	/**
	 * ----------------------------------------------------------------------
	 * Plugin Migrations.
	 */

	/**
	 * Migrate Mega Main Menu plugin settings
	 * @return {void}
	 */
	function mmm_migration(){

		console.log( 'mmm_migration: Migrating Mega Main Menu settings.' );

		$('#theme-update-mmm-migration .step .number').css( "color", "#ffffff" );
		$('#theme-update-mmm-migration .customspinner').show();

		var mmm_pathname = location.pathname.replace('/themes.php', '/admin.php?page=mega_main_menu_options');

		$('body').append('<iframe id="mmm-iframe" src="'+ mmm_pathname +'"></iframe>');

		var mmm_saved = false;

		$('iframe#mmm-iframe').load(function() {
			console.log('iframe loaded');
			console.log( "mmm_saved:" ); console.log( mmm_saved );

			if ( mmm_saved === true ) {
				$('iframe#mmm-iframe').remove();
				$('#theme-update-mmm-migration').addClass('step-completed');
				$('#theme-update-mmm-migration .customspinner').hide();
			}

				$('#mmm-iframe').contents().find('body input[type=submit]').trigger( "click" );

				mmm_saved = true;
		});
	}

	/**
	 * Migrate Ninja Forms plugin settings.
	 * @return {void}
	 */
	function nf_migration(){

		console.log( 'Migrating Ninja Forms (function nf_migration)' );

		$('#theme-update-nf-migration .step .number').css( "color", "#ffffff" );
		$('#theme-update-nf-migration .customspinner').show();

		var nf_pathname = location.pathname.replace('/themes.php', '/admin.php?page=ninja-forms-three');

		console.log( "nf_pathname:" ); console.log( nf_pathname );

		$('body').append('<iframe id="nf-iframe" src="'+ nf_pathname +'"></iframe>');

		var nf_saved = false;

		$('iframe#nf-iframe').load(function() {
			if ( nf_saved === true ) {
				// Once NF migrated to version 3, launch Layout migration.
				var nf_layout_pathname = location.pathname + '?nf_migration_layout=true';
				$('body').append('<iframe id="nf-layout-iframe" src="'+ nf_layout_pathname +'" style="display:none"></iframe>');

				// Remove iframe opened for NF migration.
				$('iframe#nf-iframe').remove();

				$('iframe#nf-layout-iframe').load(function() {
					$('#theme-update-nf-migration').addClass('step-completed');
					$('#theme-update-nf-migration .customspinner').hide();
				});

			} else {

				var nfInterval;

				var nf_layout = function() {
					if ( $('#nf-iframe').contents().find('body button').hasClass( 'nf-upgrade-button' ) ) {
						window.clearInterval(nfInterval);
						$('#nf-iframe').contents().find('body button.nf-upgrade-button').trigger( "click" );
						console.log('Clicked on Upgrade NF button...');
					} else {
						console.log('Waiting for Ninja forms migration...');
					}
				};

				nfInterval = setInterval(nf_layout,4000);

				nf_saved = true;
			}
		});
	}

})(jQuery);
