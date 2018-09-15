<?php
/**
 *  Plugin Name: Live Composer - Menu Pro
 *  Plugin URI: https://livecomposerplugin.com/add-ons/?utm_source=lc-menu-pro&utm_medium=wp-admin/plugins-list&utm_campaign=plugin_uri
 *  Description: Extension for Live Composer – Pro Navigation Module
 *  Author: Live Composer Team
 *  Version: 1.0.2
 *  Author URI: https://livecomposerplugin.com/?utm_source=lc-menu-pro&utm_medium=wp-admin/plugins-list&utm_campaign=author_uri
 *  License: GPL2
 *  License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *  Domain Path: /lang
 *
 *  Live Composer - Menu Pro Plugin is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Live Composer - Menu Pro. If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package Live Composer - Menu Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Run plugin only if the Live Composer and Menu Pro plugin is active
 */
function lcmenupro_plugin_init() {

	if ( ! class_exists( 'LC_MenuPro' ) && class_exists( 'DSLC_Module' )  ) {

		if ( version_compare( DS_LIVE_COMPOSER_VER, '1.3', '>=' ) ) {

			/**
			 * Main LC_MenuPro Class.
			 *
			 * @since 1.0
			 */
			class LC_MenuPro {

				/**
				 * Construct
				 */
				public function __construct() {
					$this->setup_constants();
					$this->includes();
					add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
					add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
					add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
					add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );


					// https://github.com/lumbermandesigns/SEOWP/issues/66 - only SEOWP
					/*add_action( 'admin_init', array( $this, 'lcmenupro_edd_updater' ), 0 );
					add_action( 'dslc_extend_admin_panel_options', array( $this, 'lcmenupro_register_admin_settings_license' ) );
					add_filter( 'dslc_filter_section_description', array( $this, 'lcmenupro_add_settings_description_license' ), 10, 2 );
					add_action( 'admin_init', array( $this, 'lcmenupro_edd_register_option' ) );
					add_action( 'admin_init', array( $this, 'lcmenupro_edd_activate_license' ) );
					add_action( 'admin_notices', array( $this, 'lcmenupro_edd_admin_notices' ) );
					add_action( 'admin_init', array( $this, 'lcmenupro_edd_deactivate_license' ) );*/
				}

				/**
				 * Output icon-fonts css files on menu editing admin screen.
				 *
				 * @return void
				 */
				function icon_files_in_admin( $admin_screens ) {
					$admin_screens[] = 'nav-menus';
					// ↑↑↑ output icon font css on Menu Editing admin screen.

					return $admin_screens;
				}

				/**
				 * Setup plugin constants.
				 *
				 * @access private
				 * @since 1.0
				 * @return void
				 */
				private function setup_constants() {

					// Plugin version.
					if ( ! defined( 'LCMENUPRO_VERSION' ) ) {
						define( 'LCMENUPRO_VERSION', '1.0.2' );
					}

					// Plugin Folder Path.
					if ( ! defined( 'LC_MenuPro_PLUGIN_DIR' ) ) {

						define( 'LC_MenuPro_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
					}

					// Plugin Folder URL.

					if ( ! defined( 'LCMENUPRO_PLUGIN_URL' ) ) {
						define( 'LCMENUPRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
					}

					// Plugin Root File.
					if ( ! defined( 'LC_MenuPro_PLUGIN_FILE' ) ) {

						define( 'LC_MenuPro_PLUGIN_FILE', __FILE__ );
					}

					// This is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
					// IMPORTANT: change the name of this constant to something unique to prevent conflicts with other plugins using this system.
					if ( ! defined( 'EDD_LCMENUPRO_STORE_URL' ) ) {

						define( 'EDD_LCMENUPRO_STORE_URL', 'https://livecomposerplugin.com' );
					}

					// The name of your product. This is the title of your product in EDD and should match the download title in EDD exactly
					// IMPORTANT: change the name of this constant to something unique to prevent conflicts with other plugins using this system.
					if ( ! defined( 'EDD_LCMENUPRO_ITEM_NAME' ) ) {

						define( 'EDD_LCMENUPRO_ITEM_NAME', 'Live Composer - Menu Pro' );
					}

					// The name of the settings page for the license input to be displayed.
					if ( ! defined( 'EDD_LCMENUPRO_LICENSE_PAGE' ) ) {

						define( 'EDD_LCMENUPRO_LICENSE_PAGE', 'dslc_options_menupro_license' );
					}
				}

				/**
				 * Loads additional php files.
				 *
				 * @access public
				 * @since 1.0
				 * @return void
				 */
				public function includes() {
					include( dirname( __FILE__ ) . '/includes/module.php' );

					// Load EDD custom updater.
					if ( ! class_exists( 'EDD_LCMenuPro_Plugin_Updater' ) ) {
						include( dirname( __FILE__ ) . '/includes/EDD_LCMenuPro_Plugin_Updater.php' );
					}
				}

				/**
				 * Load Scripts styles on front end
				 *
				 * @access public
				 * @return void
				 */
				public function load_scripts() {
					wp_enqueue_script(
						'lcmenupro-js', // handle
						LCMENUPRO_PLUGIN_URL . 'js/main.js',
						array( 'jquery' ), // deps
						LCMENUPRO_VERSION,  // ver
						true        // In_footer.
					);
				}

				/**
				 * Load Scripts styles on back end
				 *
				 * @access public
				 * @return void
				 */
				public function load_admin_scripts() {
					$screen_data = get_current_screen();
					$screen = $screen_data->base;

					// Loads scripts only on Admin > Appearance > Menus page.
					if ( 'nav-menus' === $screen ) {
						wp_enqueue_script( 'jquery-ui-core' );
						wp_enqueue_script( 'jquery-ui-dialog' );

						wp_enqueue_script(
							'lcmenupro-admin-js', // handle
							LCMENUPRO_PLUGIN_URL . 'js/admin.js',
							array( 'jquery' ), // deps
							LCMENUPRO_VERSION,  // ver
							true        // In_footer.
						);

						wp_enqueue_script(
							'dslc-builder-modals-js',
							DS_LIVE_COMPOSER_URL . 'js/builder/builder.modalwindow.functions.js',
							array( 'jquery', 'jquery-ui-dialog' ),
							LCMENUPRO_VERSION,
							true
						);
					}

					// Loads scripts only on Admin > Appearance > Menus page.
					if ( 'toplevel_page_livecomposer_editor' === $screen ) {
						// wp_enqueue_script( 'jquery-ui-core' );
						// wp_enqueue_script( 'jquery-ui-dialog' );

						wp_enqueue_script(
							'lcmenupro-admin-js', // handle
							LCMENUPRO_PLUGIN_URL . 'js/admin.js',
							array( 'jquery' ), // deps
							LCMENUPRO_VERSION,  // ver
							true        // In_footer.
						);

					/*	wp_enqueue_script(
							'dslc-builder-modals-js',
							DS_LIVE_COMPOSER_URL . 'js/builder/builder.modalwindow.functions.js',
							array( 'jquery', 'jquery-ui-dialog' ),
							LCMENUPRO_VERSION,
							true
						);*/
					}
				}

				/**
				 * Load CSS styles on back end
				 *
				 * @access public
				 * @return void
				 */
				public function load_admin_styles() {
					$screen_data = get_current_screen();
					$screen = $screen_data->base;

					// Loads scripts only on Admin > Appearance > Menus page.
					if ( 'nav-menus' === $screen ) {
						wp_enqueue_style(
							'lcmenupro-css',
							LCMENUPRO_PLUGIN_URL . 'css/admin.css',
							false
						);
					}

					if ( 'toplevel_page_livecomposer_editor' === $screen ) {
						wp_enqueue_style(
							'lcmenupro-css',
							LCMENUPRO_PLUGIN_URL . 'css/editing-admin-screen.css',
							false
						);
					}
				}

				/**
				 * Load CSS styles on front end
				 *
				 * @access public
				 * @return void
				 */
				public function load_styles() {
					wp_enqueue_style(
						'lcmenupro-css',
						LCMENUPRO_PLUGIN_URL . 'css/main.css',
						false
					);
				}

				/**
				 * Setup the updater.
				 */
				public function lcmenupro_edd_updater() {

					// Retrieve our license key from the DB.
					$license_key = trim( get_option( 'lcmenupro_edd_license_key' ) );

					// Setup the updater.
					$edd_updater = new EDD_LCMenuPro_Plugin_Updater( EDD_LCMENUPRO_STORE_URL, __FILE__, array(
							'version' 	=> '1.0.2', 		// Current version number.
							'license' 	=> $license_key, 	// license key (used get_option above to retrieve from DB).
							'item_name' => EDD_LCMENUPRO_ITEM_NAME, 	// name of this plugin.
							'author' 	=> 'Live Composer Team',  // Author of this plugin.
							'url'       => home_url(),
						)
					);
				}

				/**
				 * Creates settings in the options table.
				 */
				public function lcmenupro_edd_register_option() {
					register_setting( 'lcmenupro_edd_license', 'lcmenupro_edd_license_key', array( $this, 'lcmenupro_edd_sanitize_license' ) );
				}

				/**
				 * A callback function that sanitizes the option's value
				 *
				 * @param string $new option value.
				 */
				public function lcmenupro_edd_sanitize_license( $new ) {
					$old = get_option( 'lcmenupro_edd_license_key' );
					if ( $old && $old != $new ) {
						delete_option( 'lcmenupro_edd_license_status' ); // New license has been entered, so must reactivate.
					}
					return $new;
				}

				/**
				 * Activatea license key.
				 */
				public function lcmenupro_edd_activate_license() {

					// Listen for our activate button to be clicked.
					if ( isset( $_POST['lcmenupro_edd_license_activate'] ) ) {

						// Run a quick security check.
						if ( ! check_admin_referer( 'lcmenupro_edd_nonce', 'lcmenupro_edd_nonce' ) ) {
							return; // Get out if we didn't click the Activate button.
						}

						// Retrieve the license from the database.
						$license = trim( get_option( 'lcmenupro_edd_license_key' ) );

						// Data to send in our API request.
						$api_params = array(
							'edd_action' => 'activate_license',
							'license'    => $license,
							'item_name'  => urlencode( EDD_LCMENUPRO_ITEM_NAME ), // The name of our product in EDD.
							'url'        => home_url(),
						);

						// Call the custom API.
						$response = wp_remote_post( EDD_LCMENUPRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

						// Make sure the response came back okay.
						if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

							if ( is_wp_error( $response ) ) {
								$message = $response->get_error_message();
							} else {
								$message = __( 'An error occurred, please try again.' );
							}
						} else {

							$license_data = json_decode( wp_remote_retrieve_body( $response ) );

							if ( false === $license_data->success ) {

								switch ( $license_data->error ) {

									case 'expired' :

										$message = sprintf(
											__( 'Your license key expired on %s.' ),
											date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
										);
										break;

									case 'revoked' :

										$message = __( 'Your license key has been disabled.' );
										break;

									case 'missing' :

										$message = __( 'Invalid license.' );
										break;

									case 'invalid' :
									case 'site_inactive' :

										$message = __( 'Your license is not active for this URL.' );
										break;

									case 'item_name_mismatch' :

										$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_LCMENUPRO_ITEM_NAME );
										break;

									case 'no_activations_left':

										$message = __( 'Your license key has reached its activation limit.' );
										break;

									default :

										$message = __( 'An error occurred, please try again.' );
										break;
								}
							}
						}

						// Check if anything passed on a message constituting a failure.
						if ( ! empty( $message ) ) {
							$base_url = admin_url( 'admin.php?page=' . EDD_LCMENUPRO_LICENSE_PAGE );
							$redirect = add_query_arg( array( 'lcmenupro_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

							wp_redirect( $redirect );
							exit();
						}

						update_option( 'lcmenupro_edd_license_status', $license_data->license );
						wp_redirect( admin_url( 'admin.php?page=' . EDD_LCMENUPRO_LICENSE_PAGE ) );
						exit();
					}
				}

				/**
				 * Deactivate license key.
				 */
				function lcmenupro_edd_deactivate_license() {

					// Listen for our activate button to be clicked.
					if ( isset( $_POST['lcmenupro_edd_license_deactivate'] ) ) {

						// Run a quick security check.
						if ( ! check_admin_referer( 'lcmenupro_edd_nonce', 'lcmenupro_edd_nonce' ) ) {
							return; // Get out if we didn't click the Activate button.
						}

						// Retrieve the license from the database.
						$license = trim( get_option( 'lcmenupro_edd_license_key' ) );

						// Data to send in our API request.
						$api_params = array(
							'edd_action' => 'deactivate_license',
							'license'    => $license,
							'item_name'  => urlencode( EDD_LCMENUPRO_ITEM_NAME ), // The name of our product in EDD.
							'url'        => home_url(),
						);

						// Call the custom API.
						$response = wp_remote_post( EDD_LCMENUPRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

						// Make sure the response came back okay.
						if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

							if ( is_wp_error( $response ) ) {
								$message = $response->get_error_message();
							} else {
								$message = __( 'An error occurred, please try again.' );
							}

							$base_url = admin_url( 'admin.php?page=' . EDD_LCMENUPRO_LICENSE_PAGE );
							$redirect = add_query_arg( array( 'lcmenupro_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

							wp_redirect( $redirect );
							exit();
						}

						// Decode the license data.
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );

						// $license_data->license will be either "deactivated" or "failed"
						if ( $license_data->license == 'deactivated' ) {
							delete_option( 'lcmenupro_edd_license_status' );
						}

						wp_redirect( admin_url( 'admin.php?page=' . EDD_LCMENUPRO_LICENSE_PAGE ) );
						exit();

					}
				}

				/**
				 * This is a means of catching errors from the activation method above and displaying it to the customer
				 */
				public function lcmenupro_edd_admin_notices() {
					if ( isset( $_GET['lcmenupro_activation'] ) && ! empty( $_GET['message'] ) ) {
						switch ( $_GET['lcmenupro_activation'] ) {
							case 'false':
								$message = urldecode( $_GET['message'] );
								?>
								<div class="error">
									<p><?php echo $message; ?></p>
								</div>
								<?php
								break;
							case 'true':
							default:
								// Developers can put a custom success message here for when activation is successful if they way.
								break;
						}
					}
				}

				/**
				 * Register Admin Settings
				 */
				public function lcmenupro_register_admin_settings_license() {

					global $dslc_options_extender;

					$array = [
						'title' => __( 'Menu Pro - License', 'lcmenupro' ),
						'extension_id' => 'menupro_license',
						'sections' => [
							[
								'id' => 'main',
								'title' => __( 'Menu Pro - License', 'lcmenupro' ),
								'options' => '',
							],
						],
					];

					$dslc_options_extender->add_settings_panel( $array );
				}

				/**
				 * Add Settings Description
				 *
				 * @param HTML   $description output HTML.
				 * @param string $pannel_id panel id.
				 */
				public function lcmenupro_add_settings_description_license( $description, $pannel_id ) {

					if ( 'dslc_menupro_license_main' === $pannel_id ) {

						$license = get_option( 'lcmenupro_edd_license_key' );
						$status  = get_option( 'lcmenupro_edd_license_status' );

						// Start output fetching.
						ob_start();
						?>
						<div class="wrap">

								<?php settings_fields( 'lcmenupro_edd_license' ); ?>

								<table class="form-table">
									<tbody>
										<tr valign="top">
											<th scope="row" valign="top">
												<?php _e( 'License Key' ); ?>
											</th>
											<td>
												<input id="lcmenupro_edd_license_key" name="lcmenupro_edd_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
												<label class="description" for="lcmenupro_edd_license_key"><?php _e( 'Enter your license key' ); ?></label>
											</td>
										</tr>
										<?php if ( false !== $license ) { ?>
											<tr valign="top">
												<th scope="row" valign="top">
													<?php _e( 'Activate License' ); ?>
												</th>
												<td>
													<?php if( $status !== false && $status == 'valid' ) { ?>
														<span style="color:green;"><?php _e( 'active' ); ?></span>
														<?php wp_nonce_field( 'lcmenupro_edd_nonce', 'lcmenupro_edd_nonce' ); ?>
														<input type="submit" class="button-secondary" name="lcmenupro_edd_license_deactivate" value="<?php _e( 'Deactivate License' ); ?>"/>
													<?php } else {
														wp_nonce_field( 'lcmenupro_edd_nonce', 'lcmenupro_edd_nonce' ); ?>
														<input type="submit" class="button-secondary" name="lcmenupro_edd_license_activate" value="<?php _e( 'Activate License' ); ?>"/>
													<?php } ?>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>

						<?php

						// Get the output and stop fetching.
						$description = ob_get_contents();
						ob_end_clean();
					}

					return $description;
				}
			}

			$lcmenupro = new LC_MenuPro();
		} else {

			/**
			 * Admin Notice
			 */
			function lcmenupro_notice_lc_version() {
			?>
			<div class="notice notice-error">
				<p><?php printf( __( 'The "Live Composer - Menu Pro" add-on requires Live Composer version 1.3+. %sContact our support team%s if you need a previous version.', 'lcmenupro' ), '<a target="_blank" href="https://livecomposerplugin.com/support/">', '</a>' ); ?></p>
			</div>
			<?php }
			add_action( 'admin_notices', 'lcmenupro_notice_lc_version' );
		}

	} else {
		/**
		 * Admin Notice
		 */
		function lcmenupro_inactive_notice() {
		?>
		<div class="error">
			<p><?php printf( __( '%sCan\'t activate Menu Pro extension for Live Composer.%s %sLive Composer%s plugins should be active.', 'lcmenupro' ), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/live-composer-page-builder/">', '</a>' ); ?></p>
		</div>
		<?php }
		add_action( 'admin_notices', 'lcmenupro_inactive_notice' );

	} // End if class_exists check.

} add_action( 'plugins_loaded', 'lcmenupro_plugin_init' );



function prefix_nav_description( $item_output, $item, $depth, $args ) {
	if ( ! empty( $item->description ) ) {
		$item_output = str_replace( '">' . $args->link_before . $item->title, '">' . $args->link_before . '<span class="menu-item-description">' . $item->description . '</span>' . $item->title, $item_output );
	}

	return $item_output;
}
// add_filter( 'walker_nav_menu_start_el', 'prefix_nav_description', 10, 4 );


function dslc_nav_menu_item_args( $args, $item, $depth ) {

	if ( ! empty( $item->description ) ) {
		$prefix = '<span class="menu-item-description">';
		$suffix = '</span>';
		$args->link_after = $prefix . $item->description . $suffix;
	} else {
		$args->link_after = ''; // For some reason this line is required.
	}

	return $args;
}
add_filter( 'nav_menu_item_args', 'dslc_nav_menu_item_args', 10, 3 );

function dslc_enable_icons_on_nav_menu( $admin_screens ) {

	$admin_screens[] = 'nav-menus';

	return $admin_screens;
}
add_filter( 'dslc_icons_admin_screens', 'dslc_enable_icons_on_nav_menu', 10, 1 );

/**
 * Functions filters $controls_without_toggle array to determine what
 * controls in the module options need no on/off toggle.
 * In this case we disable toggle for 'Mobile Menu Preview' button.
 */
function dslc_controls_without_toggle_func( $controls_without_toggle ) {

	$controls_without_toggle[] = 'css_toggle_menu_preview';
	$controls_without_toggle[] = 'css_mobile_toggle_show_on';
	$controls_without_toggle[] = 'css_fullmenu_show_on';
	return $controls_without_toggle;

} add_filter( 'dslc_controls_without_toggle', 'dslc_controls_without_toggle_func', 10, 1 );
