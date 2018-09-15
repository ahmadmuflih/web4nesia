<?php
/**
 * Functions used on theme installation
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Our theme has advanced installation process with quick setup wizard.
 * We try to do all hard work automatically:
 * - Install bundled plugins
 * - Configure basic settings
 *    > create system templates
 *    > create basic menu and activate MegaMainMenu for it
 *    > regenerate custom css
 *    > setup LiveComposer tutorial pages
 *    > setup default settings for bundled plugins
 * - Import demo content
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
	exit; // Exit if accessed directly.
}

/**
 * ----------------------------------------------------------------------
 * Perform custom fucntions on theme activation
 * http://wordpress.stackexchange.com/a/80320/34582
 */

/**
 * ----------------------------------------------------------------------
 * Theme has been just activated
 */
// update_option( LBMN_THEME_NAME . '_required_plugins_installed', false);

if ( is_admin() && 'themes.php' === $pagenow ) {

	// Update theme option '_required_plugins_installed'
	// if URL has ?plugins=installed variable set
	if ( isset( $_GET['plugins'] ) && 'installed' === $_GET['plugins'] ) {
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', true );
	}

	// Reset quick theme installer steps
	if ( isset( $_GET['reset_quicksetup'] ) ) {
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', false );
		update_option( LBMN_THEME_NAME . '_basic_config_done', false );
		update_option( LBMN_THEME_NAME . '_democontent_imported', false );
	}

	// Import Demo Ninja Forms manually by visiting /wp-admin/themes.php?import-forms
	if ( isset( $_GET['import-forms'] ) ) {
		lbmn_ninjaforms_import();
	}

	// The next conditions were needed in the first generation of the theme.
	if ( lbmn_updated_from_first_generation() ) {

		$mmm_options = get_option( 'mega_main_menu_options' );

		if ( is_array( $mmm_options['mega_menu_locations'] )
				&& in_array( 'is_checkbox', $mmm_options['mega_menu_locations'], true ) ) {
			update_option( LBMN_THEME_NAME . '_update_mega_main_menu', true );
		}

		if ( ! get_option( 'ninja_forms_load_deprecated', false ) ) {

			 update_option( LBMN_THEME_NAME . '_migration_ninja_forms', true );
		}
	}
} // End if().


/**
 * ----------------------------------------------------------------------
 * Output Theme Installer HTML.
 */

/**
 * Custom 'Theme Config' admin menu item creation.
 */

add_action( 'admin_menu', 'lbmn_themeinstall_submenu_page' );
if ( ! function_exists( 'lbmn_themeinstall_submenu_page' ) ) {
	function lbmn_themeinstall_submenu_page() {
		if ( ! get_option( LBMN_THEME_NAME . '_hide_quicksetup', false ) ) {
			add_submenu_page(
				'themes.php',
				'Demo Content',
				'Theme Config',
				'manage_options',
				'seowp-theme-install',
				'lbmn_setmessage_themeinstall'
			);
		}
	}
}

function lbmn_setmessage_themeinstall() {

	$tgmpa = TGM_Plugin_Activation::get_instance();
	$plugins_installed = $tgmpa->is_tgmpa_complete();

	$permalinks_issue = false;
	if ( get_option( 'permalink_structure' ) != '/%postname%/' ) {
		$permalinks_issue = true;
	}

	$wpversion_issue = false;
	$updates = get_core_updates();
	if ( ! isset( $updates[0]->response ) || 'latest' != $updates[0]->response ) {
		$wpversion_issue = true;
	}

	/**
	 * Check PHP memory limit on the server.
	 */
	$memory_issue = false;
	$memory_limit = ini_get( 'memory_limit' );
	$memory_limit = str_replace( 'M', '', $memory_limit );
	$memory_limit = intval( $memory_limit );

	if ( 64 > $memory_limit ) {
		$memory_issue = true;
	}

	/**
	 * Check PHP version installed on the server.
	 */
	$phpversion_issue = false;
	$phpversion = phpversion();

	if ( version_compare( $phpversion, '7.0', '<' ) ) {
		$phpversion_issue = true;
	}

	/**
	 * Check if the GZIP library installed on the server.
	 */
	$gzip_issue = false;
	if ( ! is_callable( 'gzopen' ) ) {
		$gzip_issue = true;
	}

	/**
	 * Check if the server can connect to our demo content website
	 * and the plugins repo.
	 */
	$urls = array(
		'images' => 'http://www.seowptheme.com/wp-content/uploads/seo_specialist_workplace-optimized.png',
		'plugins' => 'http://www.seowptheme.com/themeinstaller/plugins/test.zip',
	);

	$connection_issue = false;
	$error = false;

	foreach ( $urls as $key => $value ) {
		$response = wp_safe_remote_get( $value );

		if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
			if ( ! $error ) {
				$error .= $key;
			} else {
				$error .= ' and ' . $key;
			}
		}
	}

	if ( $error ) {
		$connection_issue = true;
	}

	// Check FTP credentials to install plugins.
	$filesystem_method = get_filesystem_method();
	ob_start();
	$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
	ob_end_clean();
	$request_filesystem_credentials = ( $filesystem_method != 'direct' && ! $filesystem_credentials_are_stored );

	if ( ! $request_filesystem_credentials ) {
		$ftp_issue = false;
	} else {
		$ftp_issue = true;
	}


	?>
	<div class="wrap">
		<img src="<?php echo esc_attr( includes_url() . 'images/spinner.gif' ); ?>" class="theme-installer-spinner" style="position:fixed; left:50%; top:50%;" />
		<style type="text/css">.lumberman-message.quick-setup { display: none; }</style>
		<div class="lumberman-message quick-setup">
			<div class="message-container">
				<p class="before-header"><?php echo esc_html( LBMN_THEME_NAME_DISPLAY ); ?> Quick Setup</p>
				<h4>Thank you for creating with
					<?php echo esc_html( LBMN_DEVELOPER_NAME_DISPLAY ); ?>!
				</h4>
				<h5>Just a few steps left to release the full power of our theme.</h5>

				<!-- Step 0 -->
				<?php
				// Check is this step is already done
				if ( ( $plugins_installed && ! $permalinks_issue && ! $connection_issue && ! $gzip_issue && ! $ftp_issue ) 
					|| ! $permalinks_issue && ! $connection_issue && ! $gzip_issue
						&& ! $phpversion_issue && ! $memory_issue && ! $wpversion_issue && ! $ftp_issue ) {
					echo '<div id="theme-setup-step-0" class="lbmn-wizzard-step step-checkup step-completed">';
				} else {
					echo '<div id="theme-setup-step-0" class="lbmn-wizzard-step step-checkup">';
				}
				?>
					<span class="step"><span class="number">1</span></span>
					<img src="<?php echo esc_attr( includes_url() . '/images/spinner.gif' ); ?>" class="customspinner" />

					<span class="step-body"><a href="#" class="button button-primary" id="pre-install-checkup">Pre-installation checkup</a>
						<span class="step-description">
						Checks various site parameters to make sure the theme and plugins can be installed properly.
						</span>
					</span>
				</div>
				<div id="pre-install-checkup-details" class="details" style="display:none">
					<?php
						if ( $permalinks_issue || $connection_issue || $gzip_issue || $ftp_issue ) {
							echo '<span style="font-size: 16px;">Please, the fix the issues bellow (marked in red) and then continue installation.</span>';
						} elseif ( version_compare( $phpversion, '5.6', '<' ) ) {
							echo '<span style="font-size: 16px; color:#d54e21;">You should update your PHP version to either 5.6 or to 7+ and then continue installation.</span>';
						} else {
							echo '<span style="font-size: 16px; color:#009915;">Some improvements can be made but no critical issues found. <br />You can continue installation.</span>';
						}
					?>
					<ul>
						<?php
						if ( $permalinks_issue ) {
							echo '<li id="checkup-permalinks" class="checkup-fail">';
							echo '<span class="dashicons dashicons-warning"></span><b>Important:</b> It\'s highly recommended to activate clean URLs (better for SEO). <br />';
							echo '<a href="#" class="button button-primary"';
							echo '	id="fix-permalinks"';
							echo '	data-nonce="' . esc_attr( wp_create_nonce( 'lbmn_fix_permalinks_action' ) ) . '">Fix it for me</a>';
							echo '<a class="button" href="http://docs.lumbermandesigns.com/search?collectionId=&query=%22clean+urls%22" target="_blank">More about this problem</a>';
							echo '</li>';
						}

						// If the GZIP library not installed.
						if ( $gzip_issue ) {
							echo '<li class="checkup-fail"><span class="dashicons dashicons-warning"></span><b>Important:</b> Your server doesn\'t support GZIP compression! <a href="http://docs.lumbermandesigns.com/search?collectionId=&query=gzip" target="_blank">See how to fix it.</a></li>';
						}

						// If can't connect to the demo server.
						if ( $connection_issue ) {
							echo '<li class="checkup-fail"><span class="dashicons dashicons-warning"></span> ';
							echo '<b>Important:</b> Your website can\'t access our demo server to download ' . esc_attr( $error ) . '. <br />Theme will not install properly! <br/><a class="button"  href="http://docs.lumbermandesigns.com/search?collectionId=&query=broken_connection" target="_blank">See how to fix it</a></li>';
						}

						// If WP version is outdated.
						if ( $wpversion_issue ) {
							echo '<li class="checkup-fail non-critical">';
							echo '<span class="dashicons dashicons-no-alt"></span><b>Recommended:</b> Outdated WordPress version. <br />';
							echo '<a href="' . esc_attr( self_admin_url( 'update-core.php' ) ) . '" class="button button-primary">Update WordPress</a>';
							echo '</li>';
						}

						// If memory allowance is too low.
						if ( $memory_issue ) {
							echo '<li class="checkup-fail non-critical"><span class="dashicons dashicons-flag"></span> ';
							echo '<b>Recommended:</b>Your hosting company is limiting PHP memory to ' . esc_attr( $memory_limit ) . 'MB. It will slow your site down and can cause errors in the future. <br/><a class="button"  href="http://docs.lumbermandesigns.com/search?collectionId=&query=memory_limit" target="_blank">See how to fix it</a></li>';
						}

						// If PHP version is outdated.
						if ( $phpversion_issue ) {
							echo '<li class="checkup-fail non-critical"><span class="dashicons dashicons-flag"></span> ';
							echo '<b>Recommended:</b> Your server is running an outdated version of PHP (' . esc_attr( $phpversion ) . '). <br />Latest PHP version is 7+. It\'s is MUCH MORE faster and secure. <br /><a href="https://wordpress.org/about/requirements/" target="_blank">PHP7 recommended by WordPress developers</a>. <br/><a class="button"  href="http://docs.lumbermandesigns.com/search?collectionId=&query=php7" target="_blank">See how to fix it</a></li>';
						}

						// If WordPress asks for FTP credentials.
						if ( $ftp_issue ) {
							echo '<li id="checkup-ftp" class="checkup-fail"><span class="dashicons dashicons-warning"></span></li>';
							echo 'WordPress asks for your FTP credentials to install plugins. The easiest way to solve this problem is add the code <b>define(\'FS_METHOD\', \'direct\');</b> in your wp-config.php.';
						}

						?>
					</ul>
				</div>


				<!-- Step 1 **************************************************** -->
				<?php

				// Check is this step is already done
				if ( ! $plugins_installed ) {
					echo '<div id="theme-setup-step-1" class="lbmn-wizzard-step step-plugins">';
				} else {
					echo '<div id="theme-setup-step-1" class="lbmn-wizzard-step step-plugins step-completed">';
				}

				echo '<div class="step-main">';
				echo '<span class="step"><span class="number">2</span></span>';
				echo '<img src="' . esc_attr( includes_url() . '/images/spinner.gif' ) . '" class="customspinner" />';

				$link_plugins_installer = add_query_arg( array( 'page' => 'install-required-plugins' ), admin_url( 'themes.php' ) );
				$link_back_to_this_page = add_query_arg( array( 'page' => 'seowp-theme-install' ), admin_url( 'themes.php' ) );

				echo '<span class="step-body">';
					echo '<a href="' . esc_url( $link_plugins_installer );
					echo '&autoinstall=true&back_link=' . urlencode( esc_url( $link_back_to_this_page ) ) . '" class="button button-primary" id="do_plugins-install">Install required plugins</a>'; ?>
					<span class="step-description">
				Required action to get 100% functionality.<br />
				Installs Page Builder, Mega Menus, Slider, etc.
				</span></span>
				</div>
				<span class="error" style="display:none">Automatic plugin installation failed. Please try to <a href="/wp-admin/themes.php?page=install-required-plugins">install required plugins manually</a>.</span>
				</div>

				<!-- Step 2 **************************************************** -->

				<?php
				// Check is this step is already done
				if ( ! get_option( LBMN_THEME_NAME . '_basic_config_done' ) ) {
					echo '<div id="theme-setup-step-2" class="lbmn-wizzard-step step-basic_config">';
				} else {
					echo '<div id="theme-setup-step-2" class="lbmn-wizzard-step step-basic_config step-completed">';
				}
				?>
					<div class="step-main">
						<span class="step"><span class="number">3</span></span>
						<img src="<?php echo esc_attr( includes_url() . '/images/spinner.gif' ); ?>" class="customspinner" />
						<span class="step-body"><a href="#" class="button button-primary" id="do_basic-config" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'wie_import' ) ); ?>">Integrate installed plugins</a>
						<span class="step-description">
						Required action to get 100% functionality.<br />
						Configures the plugins to work with our theme.
						</span></span><br />
					</div>
					<div class="step-secondary">
						<span class="import-progress"> <span class="progress-indicator"></span> </span>
						<span class="import-progress-desc"> </span>
					</div>
					<span class="error" style="display:none">Something went wrong (<a href="#" class="show-error-log">show log</a>). Please <a href="<?php echo esc_attr( LBMN_SUPPORT_URL ); ?>">contact us</a> for help.</span>
				</div>

				<!-- Step 4 **************************************************** -->

				<?php
				// Check is this step is already done
				if ( ! get_option( LBMN_THEME_NAME . '_democontent_imported' ) ) {
					echo '<div id="theme-setup-step-3" class="lbmn-wizzard-step step-demoimport">';
				} else {
					echo '<div id="theme-setup-step-3" class="lbmn-wizzard-step step-demoimport step-completed">';
				}
				?>
				<div class="step-main">
					<span class="step"><span class="number">4</span></span>
					<img src="<?php echo esc_attr( includes_url() . '/images/spinner.gif' ); ?>" class="customspinner" />
					<span class="step-body">
						<a href="#" class="button button-primary" id="do_demo-import">Import all demo content</a>
						<span class="step-description">
						Optional step to recreate theme demo website<br />
						on your server.
						</span>
					</span>
				</div>
				<div class="step-secondary">
					<span class="import-progress"> <span class="progress-indicator"></span> </span>
					<span class="import-progress-desc"> </span>
				</div>
				<!--
				<span style="margin-right:15px;">OR</span>
				<a href="#" class="button button-secondary" id="do_basic-demo-import">Create only 3 basic pages </a>
				</p>
				-->
				</div>

				<!-- Step 5 **************************************************** -->

				<p class="lbmn-wizzard-step step-four">
					<span class="step"><span class="number">5</span></span>
				<span class="step-body step-four-body">
					<a class="button button-primary">Keep it secure</a>
					<span class="step-description">Subscribe to our private e-mail updates.<br />
					Security updates &#8226; New features</span>
				</span>

				<div class="newsletter-purchase">

					<?php $lbmn_user = get_option( 'lbmn_user' ); ?>

					<form method="post" action="https://lumbermandesigns.activehosted.com/proc.php" id="_form_13_" class="activecampaign_form" novalidate>
						<input type="hidden" name="u" value="13" />
						<input type="hidden" name="f" value="13" />
						<input type="hidden" name="s" />
						<input type="hidden" name="c" value="0" />
						<input type="hidden" name="m" value="0" />
						<input type="hidden" name="act" value="sub" />
						<input type="hidden" name="v" value="2" />
						<div class="_form-content">
							<span class="form_email"><input type="text" name="email" id="activecampaign_email" placeholder="Email" value="<?php if ( '' !== $lbmn_user ) {
								if ( is_array( $lbmn_user ) && array_key_exists( 'email', $lbmn_user ) ) {
									echo esc_attr( $lbmn_user['email'] );
								}
								} ?>" required /></span>
							<span class="form_name"><input type="text" name="firstname" placeholder="First Name" value="<?php if ( '' !== $lbmn_user ) {
								if ( is_array( $lbmn_user ) && array_key_exists( 'name', $lbmn_user ) ) {
									echo esc_attr( $lbmn_user['name'] );
								}
								} ?>" required /></span>

							<button id="_form_13_submit" class="button button-primary" type="submit">Subscribe to email updates</button>
						</div>
						<div class="_form-thank-you" style="display:none;">
						</div>
					</form>
	<!--
					<form method="post" id="lbmn-purchase-code">
						<span id="step_loader" style="display: none"><img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" /></span><span id="step_loader_res"></span>
						<input type="input" name="purchase_code" id="purchase_code" placeholder="Purchase code here" value="<?php if ( $lbmn_user != '' ) {
							if ( array_key_exists( 'purchase_code', $lbmn_user ) ) {
								echo $lbmn_user['purchase_code'];
							}
						} ?>" /><span class="error" style="display: none"></span>
						<input type="submit" name="submit" class="button button-primary" value="Activate theme support" />
					</form>
	-->
				</div>

				</p>

				<!-- Other links *********************************************** -->

				<p class="lbmn-wizzard-step step-support">
					<!-- <span class="step"><span class="number">4</span></span> -->
					<span class="step-body">
						GET SUPPORT: &nbsp; &nbsp;
						<a href="http://docs.lumbermandesigns.com/" target="_blank"><span class="dashicons dashicons-book"></span>
							<strong>Online Docs</strong></a>&nbsp; &nbsp;
						<a href="http://themeforest.net/item/seo-wp-social-media-and-digital-marketing-agency/8012838/support/contact/" target="_blank"><span class="dashicons dashicons-format-chat"></span>
							<strong>One to one support</strong></a>  &nbsp; &nbsp;

						OR SAY HELLO:  &nbsp; &nbsp; <a href="http://facebook.com/lumbermandesigns/" target="_blank"><span class="dashicons dashicons-facebook"></span></a>&nbsp; &nbsp;
						<a href="http://twitter.com/lumbermandesign/" target="_blank"><span class="dashicons dashicons-twitter"></span></a>&nbsp; &nbsp;
						<a href="http://instagram.com/lumbermandesigns/" target="_blank"><span class="dashicons dashicons-format-image"></span></a>&nbsp; &nbsp;
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

			if ( get_option( LBMN_THEME_NAME . '_democontent_imported', false ) ) {
				$can_hide_wizzard = 'block';
			}

			echo ' style="display:' . esc_attr( $can_hide_wizzard ) . '" ';
			?>>
			<span class="dashicons dashicons-thumbs-up"></span> Looks like you completed all the steps. We can hide this page as you will not need it anymore.
			<button
				class="button"
				id="hide-theme-installation-wizzard"
				data-nonce="<?php
								$nonce_hide = wp_create_nonce( 'lbmn_hide_theme_install_wizzard_nonce' );
								echo esc_attr( $nonce_hide );
								?>">Hide this page</button>
		</div>
	</div>


	<?php
} //function lbmn_setmessage_themeinstall()

add_action( 'load-themes.php', 'lbmn_themeinstaller_add_help' );
function lbmn_themeinstaller_add_help() {

	// Prepare button hide/show theme setup panel
	if ( ! get_option( LBMN_THEME_NAME . '_hide_quicksetup' ) ) {
		$action_button = '<a href="' . esc_url( add_query_arg( 'hide_quicksetup', 'true', admin_url( 'themes.php' ) ) ) . '">Hide</a>';
	} else {
		$action_button = '<a href="' . esc_url( add_query_arg( 'show_quicksetup', 'true', admin_url( 'themes.php' ) ) ) . '">Show</a>';
	}

	$screen = get_current_screen();

	$help_tab_content = '<p><strong>Quick theme installer:</strong> <ul><li>' . $action_button . ' theme setup options panel</li>' . '<li><a href="' . esc_url( add_query_arg( 'reset_quicksetup', 'true', admin_url( 'themes.php' ) ) ) . '">Reset</a> completed quick theme installer steps</a></li>' . '</ul></p>' . '<p><strong>Get help:</strong> <ul><li><a href="http://docs.lumbermandesigns.com" target="_blank">Online theme documentation</a></li>' . '<li><a href="http://themeforest.net/item/seo-wp-social-media-and-digital-marketing-agency/8012838/support" target="_blank">One to one support</a></li></p>';

	// Add filter to make it possible to add more elements on our help panel
	$help_tab_content = apply_filters( 'lbmn_theme_help_tab_content', $help_tab_content );

	$screen->add_help_tab( array(
		'id'      => 'my-plugin-default',
		'title'   => __( 'SEOWP Theme', 'lbmn' ),
		'content' => $help_tab_content,
	) );

}

/**
 * ----------------------------------------------------------------------
 * Start basic theme settings setup process
 * @todo: Change to the right hook
 */
add_action( 'admin_notices', 'lbmn_wordpress_content_importer' );
function lbmn_wordpress_content_importer() {

	$theme_dir = get_template_directory();

	if ( is_admin() && current_user_can( 'install_themes' ) && isset( $_GET['importcontent'] ) ) {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				include $class_wp_importer;
			}
		}
		if ( ! class_exists( 'lbmn_WP_Import' ) ) {
			$class_wp_import = $theme_dir . '/inc/themeinstallation/content-importer/wordpress-importer.php';
			if ( file_exists( $class_wp_import ) ) {
				include $class_wp_import;
			}
		}
		if ( class_exists( 'WP_Importer' ) && class_exists( 'lbmn_WP_Import' ) ) {
			$importer        = new lbmn_WP_Import();
			$files_to_import = array();

			if ( 'basic-templates' === $_GET['importcontent'] ) {
				$import_path = $theme_dir . '/design/basic-config/';

				$files_array = array(
					'start'                  => array(),
					'templates-system'       => array(
						'files'       => array(
							'seowp-templates.xml',
						),
						'description' => __( 'Importing: System Templates...', 'lbmn' ),
					),
					'menu-topbar'            => array(
						'files'       => array(
							'seowp-topbar.xml',
						),
						'description' => __( 'Importing: Top Bar Menu...', 'lbmn' ),
					),
					'menu-topbar-config' => array(
						'description' => __( 'Configuring Top Bar...', 'lbmn' ),
					),
					'menu-basic' => array(
						'files' => array(
							'seowp-basicmenu.xml',
						),
						'description' => __( 'Importing: Basic Main Menu...', 'lbmn' ),
					),
					'menu-basic-config' => array(
						'description' => __( 'Configuring Basic Menu...', 'lbmn' ),
					),
					'header-basic' => array(
						'files' => array(
							'seowp-header-default.xml',
						),
						'description' => __( 'Importing: Basic Header...', 'lbmn' ),
					),
					'footer-basic' => array(
						'files' => array(
							'seowp-footer-default.xml',
						),
						'description' => __( 'Importing: Basic Footer...', 'lbmn' ),
					),
					'basic-config' => array(
						'description' => __( 'Final touches...', 'lbmn' ),
					),
					'finish-basic-templates' => array(
						'description' => __( 'Finishing...', 'lbmn' ),
					),
				);

				dslc_refresh_template_ids();
			}

			if ( $_GET['importcontent'] == 'alldemocontent' ) {
				$import_path = $theme_dir . '/design/demo-content/';

				$files_array = array(
					'start'               => array(),
					'home'                => array(
						'description' => __( 'Importing: Home Page...', 'lbmn' ),
						'files'       => array(
							'seowp-homepages.xml',
							'seowp-predesignedpages-1.xml',
						),
					),
					'predesigned-1' => array(
						'description' => __( 'Importing: Inner Pages – part 1...', 'lbmn' ),
						'files' => array(
							'seowp-predesignedpages-2.xml',
							'seowp-predesignedpages-3.xml',
						),
					),
					'predesigned-2' => array(
						'description' => __( 'Importing: Inner Pages – part 2...', 'lbmn' ),
						'files' => array(
							'seowp-predesignedpages-4.xml',
							'seowp-predesignedpages-5.xml',
						),
					),
					'predesigned-3' => array(
						'description' => __( 'Importing: Inner Pages – part 3...', 'lbmn' ),
						'files' => array(
							'seowp-predesignedpages-6.xml',
							'seowp-predesignedpages-7.xml',
						),
					),
					'predesigned-4' => array(
						'description' => __( 'Importing: Inner Pages – part 4...', 'lbmn' ),
						'files' => array(
							'seowp-predesignedpages-8.xml',
							'seowp-predesignedpages-9.xml',
						),
					),
					'predesigned-5'       => array(
						'description' => __( 'Importing: Inner Pages – part 5...', 'lbmn' ),
						'files'       => array(
							'seowp-predesignedpages-10.xml',
							'seowp-predesignedpages-11.xml',
						),
					),
					'downloads'           => array(
						'files'       => array(
							'seowp-downloads.xml',
							'seowp-partners.xml',
						),
						'description' => __( 'Importing: Resources & Partners...', 'lbmn' ),
					),
					'staff'               => array(
						'files'       => array(
							'seowp-staff.xml',
							'seowp-testimonials.xml',
						),
						'description' => __( 'Importing: Staff & Testimonials...', 'lbmn' ),
					),
					'posts'               => array(
						'files'       => array(
							'seowp-posts.xml',
							'seowp-projects.xml',
						),
						'description' => __( 'Importing: Posts & Projects...', 'lbmn' ),
					),
					'media-home'          => array( // 10
															  'files'       => array(
																  'seowp-media-homepage.xml',
															  ),
															  'description' => __( 'Importing: Home Page – Images...', 'lbmn' ),
					),
					'media-menu'          => array(
						'files'       => array(
							'seowp-media-menuimages.xml',
						),
						'description' => __( 'Importing: Menu – Images...', 'lbmn' ),
					),
					'media-slider'        => array(
						'files'       => array(
							'seowp-media-sliderimages.xml',
						),
						'description' => __( 'Importing: Slider - Images...', 'lbmn' ),
					),
					'media-clinentlogos'  => array(
						'files'       => array(
							'seowp-media-clientlogos.xml',
						),
						'description' => __( 'Importing: Client Logos – Images...', 'lbmn' ),
					),
					'media-blogthumbs'    => array(
						'files'       => array(
							'seowp-media-blogpostthumbs.xml',
						),
						'description' => __( 'Importing: Posts – Images...', 'lbmn' ),
					),
					'media-footer'        => array(
						'files'       => array(
							'seowp-media-footerimages.xml',
						),
						'description' => __( 'Importing: Footer – Images...', 'lbmn' ),
					),
					'media-staff'         => array( // 15
						'files'       => array(
							'seowp-media-staffavatars.xml',
						),
						'description' => __( 'Importing: Staff – Images...', 'lbmn' ),
					),
					'media-servicepage'   => array( // 16 - lots of other import happening here
					  'files'       => array(
						  'seowp-media-servicepage.xml',
					  ),
					  'description' => __( 'Importing: Services – Images...', 'lbmn' ),
					),
					'media-bg'       => array(
						'files'       => array(
							'seowp-media-sectionbackgrounds.xml',
						),
						'description' => __( 'Importing: Background Images...', 'lbmn' ),
					),
					'media-ebooks'   => array(
						'files'       => array(
							'seowp-media-ebookcovers.xml',
						),
						'description' => __( 'Importing E-book Covers...', 'lbmn' ),
					),
					'media-projectthumbs' => array( // 19
						'files'       => array(
							'seowp-media-projectthumbs.xml',
						),
						'description' => __( 'Importing: Project – Images...', 'lbmn' ),
					),
					'mainmenu'       => array(
						'files'       => array(
							'seowp-mainmenu.xml',
						),
						'description' => __( 'Importing: Main Menu...', 'lbmn' ),
					),
					'menu-config'    => array(
						'description' => __( 'Configuring: Menus...', 'lbmn' ),
					),
					'ninjaforms'     => array(
						'description' => __( 'Importing: Ninja Forms...', 'lbmn' ),
					),
					'masterslider'   => array(
						'description' => __( 'Importing: Master Slider...', 'lbmn' ),
					),
					'footers'        => array(
						'files'       => array(
							'seowp-themefooters.xml',
						),
						'description' => __( 'Importing: Theme Footers...', 'lbmn' ),
					),
					'finish-maincontent'  => array(
						'description' => __( 'Finishing...', 'lbmn' ),
					),
				);
			}

			if ( isset( $_GET['importcontent_step_current_id'] ) ) {
				$content_part_id = sanitize_key( $_GET['importcontent_step_current_id'] );

				if ( ! $content_part_id ) {
					$content_part_id = 'start';
				}

				lbmn_debug_console( $content_part_id );

				// Get array internal pointer to the position needed.
				reset( $files_array );
				while ( key( $files_array ) !== $content_part_id && key( $files_array ) !== null ) {
					next( $files_array );
				}
				if ( key( $files_array ) === null ) {
					end( $files_array );
				}

				$files_current     = current( $files_array );
				$files_current_id  = key( $files_array );
				$files_current_pos = array_search( $files_current_id, array_keys( $files_array ) );


				if ( isset( $files_array[ $files_current_id ]['files'] ) ) {
					// Prepare array of the files to import during the current step
					foreach ( $files_array[ $files_current_id ]['files'] as $file_name ) {
						$files_to_import[] = $import_path . $file_name;
					}
				}

				// Transmit to the JS number of the current step and how many steps in total
				// to make possible to update the progress bar
				echo '<input type="hidden" name="importcontent_steps_total" id="importcontent_steps_total" value="' . count( $files_array ) . '" />';
				echo '<input type="hidden" name="importcontent_step_current_no" id="importcontent_step_current_no" value="' . $files_current_pos . '" />';
				$description = '';
				if ( isset( $files_array[ $files_current_id ]['description'] ) ) {
					$description = $files_array[ $files_current_id ]['description'];
				}
				echo '<input type="hidden" name="importcontent_step_current_descr" id="importcontent_step_current_descr" value="' . $description . '" />';

				// Set internal array pointer to the next position
				$files_next    = next( $files_array );
				$files_next_id = key( $files_array );

				if ( key( $files_array ) !== null ) {
					echo '<input type="hidden" name="importcontent_step_next_id" id="importcontent_step_next_id" value="' . esc_attr( $files_next_id ) . '" />';
				}
			}

			// Start Import.

			if ( file_exists( $class_wp_importer ) ) {
				// Import included images.
				$importer->fetch_attachments = true;


				foreach ( $files_to_import as $import_file ) {
					if ( is_file( $import_file ) ) {
						ob_start();
						$importer->import( $import_file );

						$log = ob_get_contents();
						ob_end_clean();

						// output log in the hidden div
						echo '<div class="ajax-log">';
						echo $log;
						echo '</div>';

						if ( stristr( $log, 'error' ) || ! stristr( $log, 'All done.' ) ) {
							// Set marker div that will be fildered by ajax request
							echo '<div class="ajax-request-error"></div>';

							// output log in the div
							echo '<div class="ajax-error-log">';
							echo $log;
							echo '</div>';
						}

					} else {
						// Set marker div that will be fildered by ajax request
						echo '<div class="ajax-request-error"></div>';

						// output log in the div
						echo '<div class="ajax-error-log">';
						echo "Can't open file: " . $import_file . "</ br>";
						echo '</div>';
					}
				}


			} else {
				// Set marker div that will be filtered by ajax request
				echo '<div class="ajax-request-error"></div>';

				// output log in the div
				echo '<div class="ajax-error-log">';
				echo "Failed to load: " . $class_wp_import . "</ br>";
				echo '</div>';
			}

		}

		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'menu-topbar-config' ) {
			// Asign 'Demo Mega Menu' to the 'Header Menu' location
			$menu_object    = wp_get_nav_menu_object( 'top-bar-menu' );
			$menu_object_id = $menu_object->term_id;

			$locations           = get_nav_menu_locations();
			$locations['topbar'] = $menu_object_id;
			set_theme_mod( 'nav_menu_locations', $locations );

			// Activate Mega Main Menu functionality for the 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) ) {
				lbmn_activate_mainmegamenu_locations();
			}
		}

		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'menu-basic-config' ) {
			// Asign 'Demo Mega Menu' to the 'Header Menu' location
			$menu_object    = wp_get_nav_menu_object( 'basic-main-menu' );
			$menu_object_id = $menu_object->term_id;

			$locations                = get_nav_menu_locations();
			$locations['header-menu'] = $menu_object_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'menu-config' ) {
			// Asign 'Demo Mega Menu' to the 'Header Menu' location
			$menu_object    = wp_get_nav_menu_object( 'mega-main-menu' );
			$menu_object_id = $menu_object->term_id;

			$locations                = get_nav_menu_locations();
			$locations['header-menu'] = $menu_object_id;
			set_theme_mod( 'nav_menu_locations', $locations );

			// Activate Mega Main Menu functionality for the 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) ) {
				lbmn_activate_mainmegamenu_locations();
			}
		}

		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'ninjaforms' ) {
			// Import Demo Ninja Forms
			lbmn_ninjaforms_import();
		}

		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'masterslider' ) {
			$import_path_demo_content = $theme_dir . '/design/demo-content/';

			// Import pre-designed MasterSlider Slides
			// Check if MasterSlider is active

			// http://support.averta.net/envato/support/ticket/regenerate-custom-css-programatically/#post-16478
			if ( defined( 'MSWP_AVERTA_VERSION' ) ) {

				$current_sliders         = get_masterslider_names( 'title-id' );
				$slider_already_imported = false;

				foreach ( $current_sliders as $slider => $slider_id ) {
					if ( stristr( $slider, 'Flat Design Style' ) ) {
						$slider_already_imported = true;
					}
				}

				if ( ! $slider_already_imported ) {
					global $ms_importer;
					if ( is_null( $ms_importer ) ) {
						$ms_importer = new MSP_Importer();
					}

					// * @return bool   true on success and false on failure
					$slider_import_state = $ms_importer->import_data( file_get_contents( $import_path_demo_content . 'seowp-masterslider.json' ) );
				}

				// Force Master Slider Custom CSS regeneration
				include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/msp-admin-functions.php' );

				if ( function_exists( 'msp_save_custom_styles' ) ) {
					msp_update_preset_css(); // Presets re-generation
					msp_save_custom_styles(); // Save sliders custom css
				}

			}
		}

		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'finish-maincontent' ) {

			// Update footer - simplified post_mete 'dslc_hf_type'
			$footer_design_default = get_page_by_path( 'footer-basic', OBJECT, 'dslc_hf' );
			update_post_meta( $footer_design_default->ID, 'dslc_hf_type', 'regular' );

			// Use a static front page
			$home_page = get_page_by_title( LBMN_HOME_TITLE );
			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

			// Set 'Organize my uploads into month- and year-based folders' setting
			// to its original state
			$setting_original_useyearmonthfolders = get_option( 'uploads_use_yearmonth_folders_backup', 0);
			update_option( 'uploads_use_yearmonth_folders', $setting_original_useyearmonthfolders );


			// Set the blog page (not needed)
			// $blog = get_page_by_title( LBMN_BLOG_TITLE );
			// update_option( 'page_for_posts', $blog->ID );

			lbmn_debug_console( 'lbmn_customized_css_cache_reset' );
			// Regenerate Custom CSS
			lbmn_customized_css_cache_reset( false ); // refresh custom css without printig css (false)

			if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) ) {
				// call the function that normally starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}

		} // End if().

		/**
		 * ----------------------------------------------------------------------
		 * Basic configuration:
		 * Post import actions
		 */

		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'basic-config' ) {

			// 1. Import Menus
			// 2. Activate Mega Main Menu for menu locations
			// 3. Import Widgets
			// 4. Demo description for author
			// 5. Tutorial Pages for LiveComposer
			// 6. Newsletter Sign-Up Plugin Settings
			// 7. Rotating Tweets Default Options Setup
			// 8. Regenerate Custom CSS

			// Path to the folder with basic import files
			$import_path_basic_config = $theme_dir . '/design/basic-config/';

			// $locations = get_nav_menu_locations();
			// set_theme_mod('nav_menu_locations', $locations);
			/*
			// 2: Activate Mega Main Menu for 'topbar' and 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) ) {
				lbmn_activate_mainmegamenu_locations();
			}
			*/
			// Predefine Custom Sidebars in LiveComposer
			// First set new sidebars in options table
			// update_option( 'dslc_plugin_options_widgets_m', array(
			// 		'sidebars' => 'Sidebar,404 Page Widgets,Comment Form Area,',
			// 	) );

			update_option( 'dslc_plugin_options', array(
					'sidebars' => 'Sidebar,404 Page Widgets,Comment Form Area,',
				) );

			// Define default Archive and Search options with System Templates
			/*
			// 404 Page Template
			$current_lc_archive_options             = get_option( 'dslc_plugin_options_archives' );
			$current_lc_archive_options['404_page'] = lbmn_get_page_by_title( LBMN_SYSTEMPAGE_404_DEFAULT, 'lbmn_archive' );

			// Archive Page Template
			$new_archive_listing_id                       = lbmn_get_page_by_title( LBMN_SYSTEMPAGE_ARCHIVE_DEFAULT, 'lbmn_archive' );
			$current_lc_archive_options['post']           = $new_archive_listing_id;
			$current_lc_archive_options['dslc_projects']  = $new_archive_listing_id;
			$current_lc_archive_options['dslc_galleries'] = $new_archive_listing_id;
			$current_lc_archive_options['dslc_downloads'] = $new_archive_listing_id;
			$current_lc_archive_options['dslc_staff']     = $new_archive_listing_id;
			$current_lc_archive_options['dslc_partners']  = $new_archive_listing_id;
			$current_lc_archive_options['author']         = $new_archive_listing_id;

			// Search Results
			$new_search_listing_id                        = lbmn_get_page_by_title( LBMN_SYSTEMPAGE_SEARCHRESULTS_DEFAULT, 'lbmn_archive' );
			$current_lc_archive_options['search_results'] = $new_search_listing_id;

			update_option( 'dslc_plugin_options_archives', $current_lc_archive_options );
			*/
			// Then run LiveComposer function that creates sidebars dynamically
			dslc_sidebars();

			// 3: Import widgets
			$files_with_widgets_to_import   = array();
			$files_with_widgets_to_import[] = $import_path_basic_config . 'seowp-widgets.wie';

			// Remove default widgets from 'mobile-offcanvas' widget area
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			if ( is_array( $sidebars_widgets['mobile-offcanvas'] ) ) {
				$sidebars_widgets['mobile-offcanvas'] = null;
			}
			update_option( 'sidebars_widgets', $sidebars_widgets );

			// There are dynamic values in 'seowp-widgets.wie' that needs to be replaced
			// before import processing
			global $widget_strings_replace;
			$widget_strings_replace = array(
				'TOREPLACE_OFFCANVAS_MENUID' => lbmn_get_menuid_by_menutitle( 'Basic Main Menu' ),
			);

			foreach ( $files_with_widgets_to_import as $file ) {
				lbmn_import_data( $file );
			}

			// 4: Put some demo description into current user info field
			// that used in the blog user boxes
			$user_ID   = get_current_user_id();
			$user_info = get_userdata( $user_ID );

			if ( ! $user_info->description ) {
				update_user_meta( $user_ID, 'description', 'This is author biographical info, ' . 'that can be used to tell more about you, your iterests, ' . 'background and experience. ' . 'You can change it on <a href="/wp-admin/profile.php">Admin &gt; Users &gt; Your Profile &gt; Biographical Info</a> page."' );
			}

			/*
			// Add custom Mega Main Menu options
			$mmm_options = get_option( 'mega_main_menu_options' );

			// Add custom Additional Mega Menu styles
			$mmm_options['additional_styles_presets'] = array(
				'1' => array(
					'style_name'        => "Call to action item",
					'text_color'        => "rgba(255,255,255,1)",
					'font'              => array(
						"font_size"   => "15",
						"font_weight" => "600",
					),
					'icon'              => array(
						"font_size" => "16",
					),
					'bg_gradient'       => array(
						"color1"      => "#A1C627",
						"start"       => "0",
						"color2"      => "#A1C627",
						"end"         => "100",
						"orientation" => "top",
					),
					"text_color_hover"  => "rgba(255,255,255,1)",
					"bg_gradient_hover" => array(
						"color1"      => "#56AEE3",
						"start"       => "0",
						"color2"      => "#56AEE3",
						"end"         => "100",
						"orientation" => "top",
					),
				),
				'2' => array(
					'style_name'        => "Dropdown Heading",
					'text_color'        => "rgba(0,0,0,1)",
					'font'              => array(
						"font_size"   => "15",
						"font_weight" => "400",
					),
					'icon'              => array(
						"font_size" => "15",
					),
					'bg_gradient'       => array(
						"color1"      => "",
						"start"       => "0",
						"color2"      => "",
						"end"         => "100",
						"orientation" => "top",
					),
					"text_color_hover"  => "rgba(0,0,0,1)",
					"bg_gradient_hover" => array(
						"color1"      => "",
						"start"       => "0",
						"color2"      => "",
						"end"         => "100",
						"orientation" => "top",
					),
				),
				'3' => array(
					'style_name'        => "Dropdown Menu Text",
					'text_color'        => "rgba(0,0,0,1)",
					'icon'              => array(
						"font_size" => "21",
					),
					'font'              => array(
						"font_size"   => "21",
						"font_weight" => "300",
					),
					'bg_gradient'       => array(
						"color1"      => "",
						"start"       => "0",
						"color2"      => "",
						"end"         => "100",
						"orientation" => "top",
					),
					"text_color_hover"  => "rgba(0,0,0,1)",
					"bg_gradient_hover" => array(
						"color1"      => "",
						"start"       => "0",
						"color2"      => "",
						"end"         => "100",
						"orientation" => "top",
					),
				),
			);

			// Add custom icons
			$mmm_options['set_of_custom_icons'] = array(
				'1'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-spain.png' ),
				),
				'2'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-italy.png' ),
				),
				'3'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-france.png' ),
				),
				'4'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-uk.png' ),
				),
				'5'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-us.png' ),
				),
				'6'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-austria.png' ),
				),
				'7'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-belgium.png' ),
				),
				'8'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-germany.png' ),
				),
				'9'  => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-netherlands.png' ),
				),
				'10' => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-poland.png' ),
				),
				'11' => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-portugal.png' ),
				),
				'12' => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-romania.png' ),
				),
				'13' => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-russia.png' ),
				),
				'14' => array(
					'custom_icon' => esc_url_raw( get_template_directory_uri() . '/images/flag-ukraine.png' ),
				),
			);

			// Put Mega Main Menu options back
			update_option( 'mega_main_menu_options', $mmm_options );
			*/
			// 8: Regenerate Custom CSS
			lbmn_customized_css_cache_reset( false ); // refresh custom css without printig css (false)
			/*
			if ( is_plugin_active( 'mega_main_menu/mega_main_menu.php' ) ) {
				// call the function that normaly starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}
			*/
		} // End if().

		// Set default theme logo.
		if ( ! has_custom_logo() ) {
			$logo_url = get_template_directory_uri() . '/design/images/seo-wordpress-theme-logo-horizontal.png';
			$logo_post_id = 0;
			$log_desc = "SEOWP Logo";

			$logo_id = media_sideload_image( $logo_url, $logo_post_id, $log_desc, 'id' );
			set_theme_mod( 'custom_logo', $logo_id );
		}

		lbmn_update_cpt();

		// Update theme option '_basic_config_done'
		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'finish-basic-templates' ) {
			update_option( LBMN_THEME_NAME . '_basic_config_done', true );
			if ( ! defined( 'LBMN_THEME_CONFUGRATED' ) ) {
				define( 'LBMN_THEME_CONFUGRATED', true );
			}
		}

		// Update theme option '_basic_config_done'
		if ( isset( $_GET['importcontent_step_current_id'] ) && $_GET['importcontent_step_current_id'] == 'finish-maincontent' ) {
			update_option( LBMN_THEME_NAME . '_democontent_imported', true );
		}

	} // is isset($_GET['importcontent'])
}

/**
 * ----------------------------------------------------------------------
 * In some situations after theme switch WordPress forgets menus
 * that were assigned to menu locations.
 *
 * The next code saves [menu id > location] pairs before the theme
 * switch and redeclare it back when our theme is active again.
 */

add_action( 'current_screen', 'lbmn_save_menu_locations' );
function lbmn_save_menu_locations( $current_screen ) {
	// If Apperance > Menu screen visited
	if ( 'nav-menus' === $current_screen->id ) {
		// Remember menus assigned to our locations.
		$locations = get_nav_menu_locations();
		update_option( LBMN_THEME_NAME . '_menuid_topbar', $locations['topbar'] );
		update_option( LBMN_THEME_NAME . '_menuid_header', $locations['header-menu'] );
	}
}

add_action( 'after_switch_theme', 'lbmn_redeclare_menu_locations' );
function lbmn_redeclare_menu_locations() {

	// check if 'header' locaiton has no menu assigned
	$menuid_header = get_option( LBMN_THEME_NAME . '_menuid_header' );
	if ( ! has_nav_menu( 'header-menu' ) && isset( $menuid_header ) ) {
		// Attach saved before menu id to 'topbar' location
		$locations                = get_nav_menu_locations();
		$locations['header-menu'] = $menuid_header;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// check if 'topbar' locaiton has no menu assigned
	$menuid_topbar = get_option( LBMN_THEME_NAME . '_menuid_topbar' );
	if ( ! has_nav_menu( 'topbar' ) && isset( $menuid_topbar ) ) {
		// Attach saved before menu id to 'topbar' location
		$locations           = get_nav_menu_locations();
		$locations['topbar'] = $menuid_topbar;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

// Replace dynamic values of widgets import (called from widgets-importer.php)
function lbmn_strreplace_on_widgetsimport( $data ) {
	if ( $data ) {
		global $widget_strings_replace;
		foreach ( $widget_strings_replace as $search => $replace ) {
			$data = str_replace( $search, $replace, $data );
		}
	}

	return $data;
}


/**
 * ----------------------------------------------------------------------
 * Ninja Forms Importer
 */
function lbmn_ninjaforms_import() {
	$import_path = get_template_directory() . '/design/demo-content/ninja-forms/';

	lbmn_debug_console( 'Ninja Forms Import Started' );
	// Import demo forms for Ninja Forms Plugin
	if ( class_exists( 'Ninja_Forms' ) && $handle = opendir( $import_path ) ) {
		while ( false !== ( $entry = readdir( $handle ) ) ) {
			if ( $entry != "." && $entry != ".." ) {
				$id = rand( 8000, 9000 );
				Ninja_Forms()->form()->import_form( file_get_contents( $import_path . $entry ), $id );
				lbmn_debug_console( $entry );
			}
		}
		closedir( $handle );
	}
}

/**
 * ----------------------------------------------------------------
 * Custom theme install notification.
 */

if ( ! function_exists( 'lbmn_is_theme_installation_page' ) ) {
	function lbmn_is_theme_installation_page() {
		if ( empty( $_GET['page'] ) ) {
			return false;
		} elseif (	! empty( $_GET['page'] ) && 'seowp-theme-install' !== $_GET['page'] ) {
			return false;
		} else {
			return true;
		}
	}
}

add_action( 'admin_notices', 'lbmn_themeinstall_notification' );
if ( ! function_exists( 'lbmn_themeinstall_notification' ) ) {
	function lbmn_themeinstall_notification() {

		$screen = get_current_screen();
		$theme_configured = get_option( LBMN_THEME_NAME . '_basic_config_done', false );

		if ( 'appearance_page_install-required-plugins' !== $screen->id
				&& current_user_can( 'install_plugins' )
				&& ! $theme_configured
				&& ! lbmn_is_theme_installation_page() ) {
		?>
			<div class="update-nag lbmn-themeupdate-notification">
				<span class="dashicons dashicons-admin-generic"></span>
				<div>
					<h3>A few steps left to activate your theme...</h3>
					<p>We need to install some premium plugins and configure the theme.<br />
					<span style="color:#D54E21">Theme will not work to the full potential</span> until you complete this process.</p>
				</div>
				<a class="button button-primary button-hero"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'seowp-theme-install' ), admin_url( 'themes.php' ) ) ); ?>">Install now</a>
			</div>
		<?php
		}
	}
}

/**
 * ----------------------------------------------------------------------
 * AJAX action - hide update wizzard.
 */
add_action( 'wp_ajax_lbmn_hide_theme_installation_wizzard', 'lbmn_hide_theme_installation_wizzard' );
if ( ! function_exists( 'lbmn_hide_theme_installation_wizzard' ) ) {
	/**
	 * Mark hide installation wizzard option as true on AJAX request.
	 *
	 * @return void
	 */
	function lbmn_hide_theme_installation_wizzard() {

		// Verify nonce
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'lbmn_hide_theme_install_wizzard_nonce' ) ) {
			wp_die( 'No naughty business please' );
		}

		// Check access permissions
		if ( ! current_user_can( 'install_themes' ) ) {
			wp_die( 'You do not have rights to do this' );
		}

		update_option( LBMN_THEME_NAME . '_hide_quicksetup', true );

		wp_die();
	}
}

/* Hide quick tour message block */
if ( is_admin() && current_user_can( 'install_themes' ) && isset( $_GET['hide_quicksetup'] ) && $pagenow == "themes.php" ) {
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}

/* Show quick tour message block */
if ( is_admin() && current_user_can( 'install_themes' ) && isset( $_GET['show_quicksetup'] ) && $pagenow == "themes.php" ) {
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', false ); // set option to not show quick setup block anymore
}

/**
 * ----------------------------------------------------------------------
 * AJAX action - fix permalinks.
 */
add_action( 'wp_ajax_lbmn_fix_permalinks', 'lbmn_fix_permalinks' );

if ( ! function_exists( 'lbmn_fix_permalinks' ) ) {
	/**
	 * Mark hide installation wizzard option as true on AJAX request.
	 *
	 * @return void
	 */
	function lbmn_fix_permalinks() {
		// Verify nonce.
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'lbmn_fix_permalinks_action' ) ) {
			wp_die( 'No naughty business please' );
		}

		// Check access permissions.
		if ( ! current_user_can( 'install_themes' ) ) {
			wp_die( 'You do not have rights to do this' );
		}

		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();

		// global $wp_rewrite;
		// $wp_rewrite->flush_rules();

		wp_die();
	}
}

/**
 * Add Custom CSS Style and JS Files on installation screen.
 *
 * @return void 	No data returned.
 */
add_action( 'admin_enqueue_scripts', 'lbmn_themeinstall_scripts' );

if ( ! function_exists( 'lbmn_themeinstall_scripts' ) ) {
	function lbmn_themeinstall_scripts( $admin_page_suffix ) {

		if ( 'appearance_page_seowp-theme-install' === $admin_page_suffix ) {

			wp_enqueue_style( 'lbmn_themeinstall_css', get_template_directory_uri() . '/inc/themeinstallation/theme-installation.css', false, SEOWP_THEME_VER );
			wp_enqueue_script( 'lbmn_themeinstall_js', get_template_directory_uri() . '/inc/themeinstallation/theme-installation.js', array( 'jquery' ), SEOWP_THEME_VER );
			wp_localize_script(
				'lbmn_themeinstall_js',
				'LBMNWP',
				array(
					'nonce' => wp_create_nonce( 'lbmn_themeinstall_scripts' ),
					'siteurl' => get_option( 'siteurl' ),
				)
			);

		}
	}
}

/**
 * Create a hidden empty admin page to output demo importer messages.
 */

add_action( 'admin_menu', 'lbmn_content_importer_hidden_page' );

if ( ! function_exists( 'lbmn_content_importer_hidden_page' ) ) {
	function lbmn_content_importer_hidden_page() {
		if ( ! get_option( LBMN_THEME_NAME . '_hide_quicksetup', false ) ) {
			add_submenu_page(
				null, // We want to have it hidden from menu.
				'Demo Content Importer',
				'Demo Content Importer',
				'manage_options',
				'lbmn-demo-import',
				'lbmn_content_importer_hidden_page_html'
			);
		}
	}
}

if ( ! function_exists( 'lbmn_content_importer_hidden_page_html' ) ) {
	function lbmn_content_importer_hidden_page_html() {
		// Empty for now.
		// All the magic happens via WP-importer class.
	}
}

/**
 * Update CPT for Projects when to install the theme
 */
function lbmn_update_cpt() {
	if ( ! get_option( 'dslc_custom_options_templatesforcpt', false ) ) {
		$default_cpt_settings = array(
			'lc_tpl_for_cpt_page' => 'unique',
			'lc_tpl_for_cpt_post' => 'lc_templates',
			'lc_tpl_for_cpt_dslc_downloads' => 'lc_templates',
			'lc_tpl_for_cpt_dslc_galleries' => 'lc_templates',
			'lc_tpl_for_cpt_dslc_partners' => 'lc_templates',
			'lc_tpl_for_cpt_dslc_projects' => 'unique',
			'lc_tpl_for_cpt_dslc_staff' => 'lc_templates',
			'lc_tpl_for_cpt_dslc_testimonials' => 'lc_templates',
		);

		update_option( 'dslc_custom_options_templatesforcpt', $default_cpt_settings );
	}
}
