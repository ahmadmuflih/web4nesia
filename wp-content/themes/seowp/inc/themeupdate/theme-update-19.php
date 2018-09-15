<?php
/**
 * Custom functions for the theme migration to version 1.9 (second generation)
 *
 * -------------------------------------------------------------------
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    GNU GPL, Version 3
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


/**
 * ----------------------------------------------------------------------
 * Complex theme update ver 1.9
 */
if ( ! function_exists( 'lbmn_theme_update_19' ) ) :
	function lbmn_theme_update_19() {

		$already_updated = get_option( LBMN_THEME_NAME . '_updated_to_19', false );

		if ( ! $already_updated ) {

			// Hide 'Theme Config' installation page for the users who upgrade.
			update_option( LBMN_THEME_NAME . '_hide_quicksetup', true );

			// Migrate the footers from the old CPT to the default LC format.
			lbmn_theme_update_19_footers();

			// Migrate the system templates from the old CPT to the default LC format.
			lbmn_theme_update_19_system_templates();

			// Migrate meta box values to LC friendly.
			lbmn_theme_update_19_metaboxes();

			/**
			 * Add empty default header.
			 */

			$post_data = array(
				'post_title'   => LBMN_HEADER_DESIGN_TITLE_DEFAULT,
				'post_content' => '',
				'post_name'    => 'new-header-default',
				'post_status'  => 'publish',
				'post_type'    => 'dslc_hf',
				'meta_input'   => array(
					'dslc_code' => '',
					'dslc_hf_for'  => 'header',
					'dslc_hf_type' => 'default',
				 ),
			);

			$header_id = wp_insert_post( $post_data );

			/**
			 * Migrate Widgets.
			 * On theme/plugin update all the sidebars get lost.
			 */

			// Backup old values for the 'sidebars_widgets'.
			$widgets_backup = get_option( 'sidebars_widgets' );

			// Migrate setting 'dslc_plugin_options_widgets_m' to 'dslc_plugin_options'.
			$widget_areas = get_option( 'dslc_plugin_options_widgets_m' );
			$plugin_options = get_option( 'dslc_plugin_options' );
			$plugin_options['sidebars'] = $widget_areas['sidebars'];
			update_option( 'dslc_plugin_options', $plugin_options );

			// At this points all the widgets lost.
			// Recover backed-up values.
			update_option( 'sidebars_widgets', $widgets_backup );

			/**
			 * Deactivate the next plugins (Ninja Forms addons):
			 * - Ninja Forms - Layout Master
			 *	- Ninja Forms - Mailchimp
			 * - Ninja Forms - Paypal
			 *
			 * Ninja Forms plugin can't be updated to the new generation
			 * with these plugins enabled.
			 */

			deactivate_plugins(
				array(
					'ninja-forms-layout-master/ninja-forms-layout-master.php',
					'ninja-forms-mailchimp-optins/ninja-forms-mailchimp-optins.php',
					'ninja-forms-paypal-standard/nf-paypal-standard.php',
				)
			);

			// All the automatic actions are done.
			// User now can update the plugins and follow migration process.
			update_option( LBMN_THEME_NAME . '_updated_to_19', true );
			update_option( 'lbmn_theme_updated', true );
			delete_option( 'lbmn_update_completed' );

			// Update CPT for Projects.
			lbmn_update_cpt();

		} // End if().
	}
endif;


/**
 * Hide 'Theme Config' Menu item once the theme updated.
 * There is no way to solve it without JS.
 */
function lbmn_admin_inline_js(){
	$screen = get_current_screen();
	$screen_name = $screen->base;

	if ( 'themes' === $screen_name &&  get_option( LBMN_THEME_NAME . '_hide_quicksetup', false ) ) {
		echo "<script type='text/javascript'>\n";
		echo "\nvar menuItemThemeInstall = jQuery('#menu-appearance li a[href=\"themes.php?page=seowp-theme-install\"]').closest('li');";
		echo "\njQuery(menuItemThemeInstall).remove();";
		echo "\n</script>";
	}
}
add_action( 'in_admin_footer', 'lbmn_admin_inline_js' );

/**
 * ------------------------------------------------------------
 * Footers migration.
 */

if ( ! function_exists( 'lbmn_theme_update_19_footers' ) ) :
	/**
	 * Migrate footers from CPT 'lbmn_footer' (SEOWP only)
	 * to CPT 'dslc_hf' (LC default).
	 *
	 * @return void Just doing its job.
	 */
	function lbmn_theme_update_19_footers() {

		// Get all the posts of 'lbmn_footer' type.
		$args = array(
				'posts_per_page' => 99, // Not likely someone have more.
				'post_type' => 'lbmn_footer',
			);
		$footer_posts = get_posts( $args );

		// Get default footer ID defined in the Customizer.
		$default_footer_id = get_theme_mod( 'lbmn_footer_design', false );

		if ( false === $default_footer_id ) {
			// Get default footer custom post id.
			$footer_design_default = get_page_by_title( LBMN_FOOTER_DESIGN_TITLE_DEFAULT, 'ARRAY_A', 'lbmn_footer' );

			if ( isset( $footer_design_default ) ) {
				$default_footer_id = $footer_design_default['ID'];
			} else {
				$default_footer_id = false;
			}
		}

		/*
		Not likely users who upgrade the theme never saved Customizer at least once.


		// If the main demo content installed.
		if ( get_option( LBMN_THEME_NAME . '_democontent_imported', false ) ) {
			// Get default footer custom post id.
			$footer_design_default = get_page_by_title( LBMN_FOOTER_DESIGN_TITLE_DEFAULT, 'ARRAY_A', 'lbmn_footer' );
		} else {
			// Get default footer custom post id.
			$footer_design_default = get_page_by_title( LBMN_FOOTER_DESIGN_TITLE_BASIC, 'ARRAY_A', 'lbmn_footer' );
		}

		if ( isset( $footer_design_default ) ) {
			$footer_design_default = $footer_design_default['ID'];
		} else {
			$footer_design_default = '';
		}

		*/


		// If no value set in the Customizer.
		// $default_footer_id = get_theme_mod( 'lbmn_footer_design', $footer_design_default );

		foreach ( $footer_posts as $post ) {

			$footer_code = get_post_meta( $post->ID, 'dslc_code', true );

			if ( false !== $default_footer_id && intval( $default_footer_id ) === intval( $post->ID ) ) {
				$type = 'default';
			} else {
				$type = 'regular';
			}

			$post_data = array(
				'post_title'   => $post->post_title,
				'post_content' => $post->post_content,
				'post_name'    => $post->post_name,
				'post_status'  => $post->post_status,
				'post_type'    => 'dslc_hf',
				'meta_input'   => array(
					  'dslc_code'     => $footer_code,
					  'dslc_hf_for'   => 'footer',
					  'dslc_hf_type'  => $type,
					  'old_footer_id' => $post->ID,
				 ),
			);

			// Save new post with footer.
			$post_id = wp_insert_post( $post_data );
		}
	}
endif;

/**
 * ------------------------------------------------------------
 * System templates migration.
 */

if ( ! function_exists( 'lbmn_theme_update_19_system_templates' ) ) :
	/**
	 * Migrate system templates from CPT 'lbmn_archive' (SEOWP only)
	 * to CPT 'dslc_templates' (LC default).
	 *
	 * @return void Just doing its job.
	 */
	function lbmn_theme_update_19_system_templates() {

		// Get all the posts of the 'lbmn_archive' type.
		$args = array(
				'posts_per_page' => 99, // Not likely someone has more templates.
				'post_type' => 'lbmn_archive',
			);
		$sytem_templates_posts    = get_posts( $args );

		$archive_listing_template = get_page_by_path( 'archive-listing-template', OBJECT, 'lbmn_archive' );
		$search_results_template  = get_page_by_path( 'search-results-listing-template', OBJECT, 'lbmn_archive' );
		$page_not_found_templates = get_page_by_path( '404-page-not-found', OBJECT, 'lbmn_archive' );

		foreach ( $sytem_templates_posts as $post ) :

			// Extract page code.
			$dslc_code = get_post_meta( $post->ID, 'dslc_code', true );

			// Set addition meta data depending on type of the template.
			if ( $post->post_name === $page_not_found_templates->post_name ) {
				$template_for = '404_page';
				$type = 'default';
			} elseif ( $post->post_name === $search_results_template->post_name ) {
				$template_for = 'search_results';
				$type = 'default';
			} elseif ( $post->post_name === $archive_listing_template->post_name ) {
				$template_for = 'author';
				$type = 'default';
			} else {
				$template_for = '';
				$type = 'regular';
			}

			$post_data = array(
				'post_title'   => $post->post_title,
				'post_content' => $post->post_content,
				'post_name'    => $post->post_name,
				'post_status'  => $post->post_status,
				'post_type'    => 'dslc_templates',
				'meta_input'   => array(
					  'dslc_code' => $dslc_code,
					  'dslc_template_base' => 'theme',
					  'dslc_template_for'  => $template_for,
					  'dslc_template_type' => $type,
				 ),
			);

			$post_id = wp_insert_post( $post_data );

		endforeach;
	}
endif;

/**
 * ------------------------------------------------------------
 * Meta box values migration.
 */

if ( ! function_exists( 'lbmn_theme_update_19_metaboxes' ) ) :
	/**
	 * Migrate meta box values from SEOWP format to LC friendly values.
	 *
	 * @return void Just doing its job.
	 */
	function lbmn_theme_update_19_metaboxes() {

		/**
		 * Compose array of old->new ids for the each footer.
		 * This array needed to not repeat expensive WP_Query
		 * for the each footer migration.
		 */
		$old_new_footer_id = array();

		$footers = new WP_Query(
			array(
				'posts_per_page' => 999,
				'post_type' => 'dslc_hf',
			)
		);

		$footers = $footers->get_posts();

		foreach ( $footers as $footer ) {
			$old_new_footer_id[ $footer->ID ] = get_post_meta( $footer->ID, 'old_footer_id', true );
		}

		/**
		 * Go through all the pages and find 'old' custom footer ids.
		 * Migrate each custom meta to the new format with new id.
		 */

		$pages = new WP_Query(
			array(
				'posts_per_page' => 999,
				'post_type' => 'page',
				'meta_key' => 'lbmn_custom_footer_id',
			)
		);

		$pages = $pages->get_posts();

		foreach ( $pages as $page ) {

			$footer_id_old = get_post_meta( $page->ID, 'lbmn_custom_footer_id', true );
			$footer_id_new = array_search( $footer_id_old, $old_new_footer_id, true );

			if ( $footer_id_new ) {
				// Rewrite old custom footer id with the new value in the page meta.
				update_post_meta( $page->ID, 'dslc_footer', $footer_id_new );
			}
		}
	}
endif;

/**
 * ------------------------------------------------------------
 * Ninja Forms Layout migration.
 */

if ( ! function_exists( 'lbmn_nf_migration_layout' ) ) {
	/**
	 * Ninja Forms layout master isn't working NF3, so we need to find a fix.
	 * This function migrates 'old' layout settings (columns)
	 * to the CSS-class-based solution.
	 *
	 * @return void Just doing migration without any output.
	 */
	function lbmn_nf_migration_layout() {

		$all_forms = Ninja_Forms()->form()->get_forms();

		if ( is_array( $all_forms ) && ! empty( $all_forms ) ) {

			foreach ( $all_forms as $form ) {
				$form_id = $form->get_id();
				$layout_master = $form->get_setting( 'bti_layout_master' );
				$forms_cols = $layout_master['cols'];

				// Returns an array of Field Models for Form ID.
				$form_fields = Ninja_Forms()->form( $form_id )->get_fields();

				foreach ( $form_fields as $field ) {
					$layout_setting = $field->get_setting( 'bti_layout_master' );
					$column = $layout_setting['col'];
					$sizex = $layout_setting['sizex'];

					if ( 1 === intval( $forms_cols ) ) {
						$field->update_setting( 'container_class', '' )->save();
					} elseif ( 2 === intval( $forms_cols ) ) {

						if ( 1 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-half first' )->save();
						} elseif ( 2 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-half' )->save();
						} else {
							$field->update_setting( 'container_class', '' )->save();
						}
					} elseif ( 3 === intval( $forms_cols ) ) {

						if ( 1 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-third first' )->save();
						} elseif ( 2 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-third' )->save();
						} elseif ( 3 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-third' )->save();
						} elseif ( 1 === intval( $column ) && 2 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'two-thirds first' )->save();
						} elseif ( 2 === intval( $column ) && 2 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'two-thirds' )->save();
						} elseif ( 3 === intval( $column ) && 2 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'two-thirds' )->save();
						} else {
							$field->update_setting( 'container_class', '' )->save();
						}
					} elseif ( 4 === intval( $forms_cols ) ) {

						if ( 1 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-fourth first' )->save();
						} elseif ( 2 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-fourth' )->save();
						} elseif ( 3 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-fourth' )->save();
						} elseif ( 4 === intval( $column ) && 1 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-fourth' )->save();
						} elseif ( 1 === intval( $column ) && 2 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-half first' )->save();
						} elseif ( 2 === intval( $column ) && 2 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-half' )->save();
						} elseif ( 3 === intval( $column ) && 2 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-half' )->save();
						} elseif ( 4 === intval( $column ) && 2 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'one-half' )->save();
						} elseif ( 1 === intval( $column ) && 3 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'three-fourths first' )->save();
						} elseif ( 2 === intval( $column ) && 3 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'three-fourths' )->save();
						} elseif ( 3 === intval( $column ) && 3 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'three-fourths' )->save();
						} elseif ( 4 === intval( $column ) && 3 === intval( $sizex ) ) {
							$field->update_setting( 'container_class', 'three-fourths' )->save();
						} else {
							$field->update_setting( 'container_class', '' )->save();
						}
					} // End if().
				} // End foreach().

				// Delete the form cache to make sure css classes show in the editor.
				delete_option( 'nf_form_' . $form_id );
			} // End foreach().
		} // End if().
	}
} // End if().

if ( ! empty( $_GET['nf_migration_layout'] ) ) {
	if ( current_user_can( 'install_plugins' )
			&& 'true' === $_GET['nf_migration_layout'] ) {
		lbmn_nf_migration_layout();
	}
}

/**
 * ------------------------------------------------------------
 * Ninja Forms Mailchimp Opt-In Settings migration.
 */

add_filter( 'ninja_forms_upgrade_settings', 'lbmn_nf_convert_settings_to_action' );

if ( ! function_exists( 'lbmn_nf_convert_settings_to_action' ) ) {
	function lbmn_nf_convert_settings_to_action( $form_data ) {

		$forms_with_mailchimp = false;
		$mailchimp_fields = array();
		$email_field_id = '';

		foreach ( $form_data['fields'] as $field ) {

			if ( '_optin_mailchimp' === $field['type'] ) {
				$forms_with_mailchimp = true;
				$mailchimp_fields = $field['data'];
			}

			if ( '_text' === $field['type']
					&& isset( $field['data'] )
					&& isset( $field['data']['email'] )
					&& 1 === intval( $field['data']['email'] ) ) {

				$email_field_id = $field['id'];

			}
		}

		if ( $forms_with_mailchimp ) {

			$new_action = array(
				'type' => 'mailchimp-optins',
				'label' => __( 'MailChimp Optin', 'ninja-forms-paypal-express' ),
			);

			if ( isset( $mailchimp_fields['optin_mailchimp_api_key'] ) ) {
				$mailchimp_api_key = $mailchimp_fields['optin_mailchimp_api_key'];
				if ( ! stristr( $mailchimp_api_key, '>' ) ) {
					Ninja_Forms()->update_setting( 'mailchimp_api_key', $mailchimp_api_key );
				}
			}

			if ( isset( $mailchimp_fields['optin_mailchimp_list'] ) ) {
				$new_action['newsletter_list'] = $mailchimp_fields['optin_mailchimp_list'];
			}

			if ( isset( $mailchimp_fields['optin_mailchimp_double_optin'] ) ) {
				$new_action['mailchimp_double_optin'] = $mailchimp_fields['optin_mailchimp_double_optin'];
			}

			if ( '' !== $email_field_id ) {
				$new_action['mailchimp_to'] = '{field:email_' . $email_field_id . '}';
			}

			$form_data['actions'][] = $new_action;
		}

		return $form_data;
	}
} // End if().

/**
 * ----------------------------------------------------------------
 * Disable default welcome screen in the Live Composer.
 */

add_filter( 'dslc_show_welcome_screen', 'lbmn_donot_show_lc_welcome' );

if ( ! function_exists( 'lbmn_donot_show_lc_welcome' ) ) {
	/**
	 * LC Welcome Screen on plugin activation breaks TGMPA automatic plugins update.
	 * In this function we disable Live Composer Welcome Screen redirect.
	 *
	 * @param  bool $show_lc_welcome Redirect user to the welcome screen or not.
	 * @return bool                  Filtered parameter.
	 */
	function lbmn_donot_show_lc_welcome( $show_lc_welcome ) {
		$show_lc_welcome = false;
		return $show_lc_welcome;
	}
}

/**
 * ----------------------------------------------------------------
 * Custom theme update notification.
 */

if ( ! function_exists( 'lbmn_is_theme_update_page' ) ) {
	function lbmn_is_theme_update_page() {
		if ( empty( $_GET['page'] ) ) {
			return false;
		} elseif ( ! empty( $_GET['page'] )
			&& 'seowp-theme-update' !== $_GET['page']
			&& 'install-required-plugins' !== $_GET['page'] ) {

			return false;

		} else {
			return true;
		}
	}
}

add_action( 'admin_notices', 'lbmn_themeupdate_notification' );

if ( ! function_exists( 'lbmn_themeupdate_notification' ) ) {
	function lbmn_themeupdate_notification() {

		$theme_updated = get_option( 'lbmn_theme_updated', false );
		$theme_update_completed = get_option( 'lbmn_update_completed', false );

		if ( current_user_can( 'install_plugins' )
				&& $theme_updated && ! $theme_update_completed
				&& ! lbmn_is_theme_update_page() ) {
		?>
			<div class="update-nag lbmn-themeupdate-notification">
				<span class="dashicons dashicons-update"></span>
				<div>
					<h3>A few steps left to update your theme...</h3>
					<p>We need to migrate your design settings to the new version.<br />
					<span style="color:#D54E21">Website will not work properly</span> until you complete this process.</p>
				</div>
				<a class="button button-primary button-hero"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'seowp-theme-update' ), admin_url( 'themes.php' ) ) ); ?>">Update now</a>
			</div>
		<?php
		}
	}
}


/**
 * ----------------------------------------------------------------
 * Custom 'Theme Update' admin menu item creation.
 */

if ( lbmn_updated_from_first_generation()
		&& ! function_exists( 'lbmn_themeupdate_submenu_page' ) ) {

	add_action( 'admin_menu', 'lbmn_themeupdate_submenu_page' );

	/**
	 * Create menu item for the theme update.
	 */
	function lbmn_themeupdate_submenu_page() {

		if ( ! get_option( LBMN_THEME_NAME . '_hide_update_wizzard', false ) ) {
			add_submenu_page(
				'themes.php',
				'Theme Update',
				'Theme Update',
				'manage_options',
				'seowp-theme-update',
				'lbmn_themeupdate_submenu_page_callback'
			);
		}
	}
}

/**
 * Theme Update Page Output
 */
if ( ! function_exists( 'lbmn_themeupdate_submenu_page_callback' ) ) {
	function lbmn_themeupdate_submenu_page_callback() {
		?>
		<div class="wrap">
		<img src="<?php echo includes_url() . 'images/spinner.gif' ?>" class="theme-installer-spinner" style="position:fixed; left:50%; top:50%;" />
		<style type="text/css">.lumberman-message.quick-setup { display: none; }</style>
		<div class="lumberman-message major-update">
			<div class="message-container">
				<p class="before-header"><?php echo LBMN_THEME_NAME_DISPLAY; ?> Update</p>
				<h4>Let&rsquo;s update your theme...
				</h4>
				<h5>a few simple steps required to properly migrate your settings.</h5>

				<?php
					$tgmpa = TGM_Plugin_Activation::get_instance();
					$plugins_installed = $tgmpa->is_tgmpa_complete();
				?>

					<!-- Step 1 -->
					<?php
					// Check is this step is already done
					if ( ! $plugins_installed ) {
						echo '<p id="theme-update-step-1" class="lbmn-wizzard-step step-backup">';
					} else {
						echo '<p id="theme-update-step-1" class="lbmn-wizzard-step step-backup step-completed">';
					}
					?>
					<span class="step"><span class="number">1</span></span>
					<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

					<span class="step-body"><a href="http://docs.lumbermandesigns.com/article/218-how-to-create-a-complete-wordpress-site-backup/" target="_blank" class="button button-primary" id="step-backup">Make a full site backup</a>
						<span class="step-description">
							Without a full backup <br />you are risking to lose all the data!
						</span>
					</span>
				</p>

				<!-- Step 2 -->
				<?php

				// Check is this step is already done
				if ( ! $plugins_installed ) {
					echo '<p id="theme-update-step-2" class="lbmn-wizzard-step step-plugins">';
				} else {
					echo '<p id="theme-update-step-2" class="lbmn-wizzard-step step-plugins step-completed">';
				}
				?>
				<span class="step"><span class="number">2</span></span>
				<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

				<span class="step-body"><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'install-required-plugins' ), admin_url( 'themes.php' ) ) ); ?>&autoinstall=true&back_link=<?php echo esc_url( add_query_arg( array( 'page' => 'seowp-theme-update' ), admin_url( 'themes.php' ) ) );  ?>" class="button button-primary" id="do_plugins-install">Update premium plugins</a>
					<span class="step-description">In this step we will update all the premium plugins included with SEOWP theme.</span>
				</span>
				<span class="error" style="display:none">Automatic plugin installation failed. Please try to <a href="/wp-admin/themes.php?page=install-required-plugins">install required plugins manually</a>.</span>
				</p>

				<!-- Step 3 -->
				<?php
				// Check is this step is already done
				if ( ! get_option( LBMN_THEME_NAME . '_update_mega_main_menu' ) ) {
					echo '<p id="theme-update-mmm-migration" class="lbmn-wizzard-step ">';
				} else {
					echo '<p id="theme-update-mmm-migration" class="lbmn-wizzard-step step-completed">';
				}
				?>
				<span class="step"><span class="number">3</span></span>
				<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

				<span class="step-body">
					<a href="#" class="button button-primary" id="migration-mmm">Migrate menu settings</a>
					<span class="step-description">New version of the Mega Main Menu plugin requires migration of the menu settings.</span>
				</span>
				</p>

				<!-- Step 4 -->
				<?php
				// Check is this step is already done
				if ( ! get_option( LBMN_THEME_NAME . '_migration_ninja_forms' ) ) {
					echo '<p id="theme-update-nf-migration" class="lbmn-wizzard-step ">';
				} else {
					echo '<p id="theme-update-nf-migration" class="lbmn-wizzard-step step-completed">';
				}
				?>
				<span class="step"><span class="number">4</span></span>
				<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

				<span class="step-body">
					<a href="" class="button button-primary" id="migration-nf">Migrate form settings</a>
					<span class="step-description">New version of the Ninja Forms plugin requires migration of all the forms.</span>
				</span>
				</p>

				<!-- Step 4 -->

				<?php
				// Check is this step is already done
				if ( ! get_option( 'lbmn_update_completed' ) ) {
					echo '<p id="theme-update-final" class="lbmn-wizzard-step ">';
				} else {
					echo '<p id="theme-update-final" class="lbmn-wizzard-step step-completed">';
				}
				?>
				<span class="step"><span class="number">5</span></span>
				<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />


				<span class="step-body">
					<a href="http://docs.lumbermandesigns.com/article/217-theme-update-to-version-1-9#checklist"
						class="button button-primary"
						id="migration-finish"
						data-nonce="<?php
							$nonce_complete = wp_create_nonce( "lbmn_update_completed_nonce" );
							echo $nonce_complete;
							?>"
						target="_blank">After update checklist</a>
					<span class="step-description">You need to check a few plugins for a proper work. Click on the button for more details.</span>
				</span>
				</p>

				<!-- other links -->

				<p class="step-support">
					<!-- <span class="step"><span class="number">4</span></span> -->
			<span class="step-body">
				GET SUPPORT: &nbsp; &nbsp;
				<a href="http://docs.lumbermandesigns.com/" target="_blank"><span class="dashicons dashicons-book"></span>
					<strong>Online Docs</strong></a>&nbsp; &nbsp;
				<a href="http://themeforest.net/item/seo-wp-social-media-and-digital-marketing-agency/8012838/support/contact/" target="_blank"><span class="dashicons dashicons-format-chat"></span>
					<strong>One to one support</strong></a>  &nbsp; &nbsp;
			</span>
				</p>

			</div>
			<a name="focus-after-installer" id="focus-after-installer">&nbsp;</a>
			<style type="text/css">.theme-installer-spinner { display: none; }</style>
			<style type="text/css">.lumberman-message.quick-setup { display: block; }</style>
		</div>

		<div class="lbmn-can-hide-wizzard"
		<?php
			// Check if theme update process completed
			$can_hide_wizzard = 'none';

			if ( get_option( 'lbmn_update_completed', false ) ) {
				$can_hide_wizzard = 'block';
			}

			echo ' style="display:' . $can_hide_wizzard  . '" ';
		?>>
		<span class="dashicons dashicons-thumbs-up"></span> Looks like you completed all the steps. We can hide this page as you will not need it anymore.
		<button
			class="button"
			id="hide-theme-update-wizzard"
			data-nonce="<?php
							$nonce_hide = wp_create_nonce( "lbmn_hide_theme_update_wizzard_nonce" );
							echo $nonce_hide;
							?>">Hide this page</button>
		</div>

		</div>


		<?php
	}
}

