<?php
/**
 *  Plugin Name: Live Composer - Templates for CPT
 *  Plugin URI: https://livecomposerplugin.com/add-ons/?utm_source=lc-template-cpt&utm_medium=wp-admin/plugins-list&utm_campaign=plugin_uri
 *  Description: Extension for Live Composer – Templates for CPT
 *  Author: Live Composer Team
 *  Version: 1.0
 *  Author URI: https://livecomposerplugin.com/?utm_source=lc-template-cpt&utm_medium=wp-admin/plugins-list&utm_campaign=author_uri
 *  License: GPL2
 *  License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *  Domain Path: /lang
 *
 *  Live Composer - Templates for CPT Plugin is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Live Composer - Templates for CPT Plugin. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function lccpt_plugin_init() {

	if ( ! class_exists( 'LC_TemplatesForCPT' ) && class_exists( 'DSLC_Module' )  ) {

		if ( version_compare( DS_LIVE_COMPOSER_VER, '1.3', '>=' ) ) {

			/**
			 * Main LC_TemplatesForCPT Class.
			 *
			 * @since 1.0
			 */
			class LC_TemplatesForCPT {

				/**
				 * Instance var
				 *
				 * @var LC_TemplatesForCPT The one true LC_TemplatesForCPT
				 * @since 1.0
				 */
				private static $instance;

				private static $default_cpt_settings = array(

					'dslc_hf' => 'hidden',
					'dslc_popup' => 'hidden',
					'attachment' => 'hidden',
					'comments' => 'hidden',
					'dslc_popup' => 'hidden',
					'revision' => 'hidden',
					'customize_changeset' => 'hidden',
					'nav_menu_item' => 'hidden',
					'dslc_templates' => 'hidden',
					'acf' => 'hidden',
					'custom_css' => 'hidden',

					'page' => 'unique',

					'post' => 'lc_templates',
					'dslc_downloads' => 'lc_templates',
					'dslc_galleries' => 'lc_templates',
					'dslc_partners' => 'lc_templates',
					'dslc_projects' => 'lc_templates',
					'dslc_staff' => 'lc_templates',
					'dslc_testimonials' => 'lc_templates',

				);


				public function __construct() {
					$this->setup_constants();
					//add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
					// $this->includes();
					// add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
					// add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
					// add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );
					// add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );

					add_action( 'dslc_extend_admin_panel_options', array( $this, 'register_admin_settings' ) );
					add_filter( 'dslc_post_templates_post_types', array( $this, 'filter_pt_options' ), 1 );
					add_filter( 'dslc_cpt_use_templates', array( $this, 'use_templates' ), 10, 2  );
					add_filter( 'dslc_can_edit_in_lc', array( $this, 'can_edit' ), 10, 2  );
					add_filter( 'dslc_filter_section_description', array( $this, 'add_settings_description' ), 10, 2  );

					add_action( 'admin_init', array( $this, 'lccpt_edd_updater' ), 0 );
					add_action( 'dslc_extend_admin_panel_options', array( $this, 'lccpt_register_admin_settings_license' ) );
					add_filter( 'dslc_filter_section_description', array( $this, 'lccpt_add_settings_description_license' ), 10, 2 );
					add_action( 'admin_init', array( $this, 'lccpt_edd_register_option' ) );
					add_action( 'admin_init', array( $this, 'lccpt_edd_activate_license' ) );
					add_action( 'admin_notices', array( $this, 'lccpt_edd_admin_notices' ) );
					add_action( 'admin_init', array( $this, 'lccpt_edd_deactivate_license' ) );
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
					if ( ! defined( 'LCCPT_VERSION' ) ) {

						define( 'LCCPT_VERSION', '1.0' );
					}

					// Plugin Folder Path.
					if ( ! defined( 'LCCPT_PLUGIN_DIR' ) ) {

						define( 'LCCPT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
					}

					// Plugin Folder URL.
					if ( ! defined( 'LCCPT_PLUGIN_URL' ) ) {

						define( 'LCCPT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
					}

					// Plugin Root File.
					if ( ! defined( 'LCCPT_PLUGIN_FILE' ) ) {

						define( 'LCCPT_PLUGIN_FILE', __FILE__ );
					}

					// Plugin Text Domain.
					if ( ! defined( 'LCCPT_TEXTDOMAIN' ) ) {

						define( 'LCCPT_TEXTDOMAIN', 'lccpt' );
					}

					// This is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
					// IMPORTANT: change the name of this constant to something unique to prevent conflicts with other plugins using this system.
					if ( ! defined( 'EDD_LCCPT_STORE_URL' ) ) {

						define( 'EDD_LCCPT_STORE_URL', 'https://livecomposerplugin.com' );
					}

					// The name of your product. This is the title of your product in EDD and should match the download title in EDD exactly
					// IMPORTANT: change the name of this constant to something unique to prevent conflicts with other plugins using this system.
					if ( ! defined( 'EDD_LCCPT_ITEM_NAME' ) ) {

						define( 'EDD_LCCPT_ITEM_NAME', 'CPT Integration' );
					}

					// The name of the settings page for the license input to be displayed.
					if ( ! defined( 'EDD_LCCPT_LICENSE_PAGE' ) ) {

						define( 'EDD_LCCPT_LICENSE_PAGE', 'dslc_options_cpt_license' );
					}
				}

				/**
				 * Loads the plugin language files.
				 *
				 * @access public
				 * @since 1.0
				 * @return void
				 */
				public function load_textdomain() {

					// Set filter for plugin's languages directory.
					$plugin_lang_dir  = dirname( plugin_basename( LCCPT_PLUGIN_FILE ) ) . '/lang/';
					load_plugin_textdomain( LCCPT_TEXTDOMAIN, false, $plugin_lang_dir );
				}

	/*
				public function toggle_lc_enabling() {

					error_log('toggle_lc_enabling');

					$dslc_admin_interface_on = true;

					if ( is_singular() || is_admin() ) {

						$post_type = '';

		 				if ( is_singular() ) {

		 					$post_type = get_post_type();
		 				} else {

		 					if ( isset( $_GET['post'] ) ) {

		 						$post_type = get_post_type( $_GET['post'] );
		 					} else {

		 						if ( function_exists( 'get_current_screen' ) ) {

			 						$screen = get_current_screen();
			 						$post_type = $screen->post_type;
		 						}
		 					}
		 				}

		 				// $option = dslc_get_option( 'lc_tpl_for_cpt_' . $post_type, 'dslc_custom_options_templatesforcpt' );
		 				$option = $this->get_pt_option( $post_type->name );

		 				error_log ( $option );

		 				if ( 'disabled' === $option ) {

		 					$dslc_admin_interface_on = false;
		 				}
					}

					return $dslc_admin_interface_on;
				}
	*/

				/**
				 * True if the provided post type uses LC templates system.
				 *
				 * @param  [boolean] $use_templates Variable to filter.
				 * @param  [string] $post_type      Post type slug.
				 * @return [boolean]                Filtered variable.
				 */
				public function use_templates( $use_templates, $post_type ) {

					if ( is_singular() || is_admin() ) {

		 				$option = $this->get_pt_option( $post_type );

		 				if ( 'lc_templates' === $option ) {

		 					$use_templates = true;
		 				}
					}

					return $use_templates;
				}


				/**
				 * Determine if the Live Composer can edit posts of the provided
				 * contenet type. Returns true if can be edited or can have LC template.
				 *
				 * @param  [boolean] $can_edit  Variable to filter.
				 * @param  [string] $post_type  String with post type slug.
				 * @return [boolean]            Filtered variable.
				 */
				public function can_edit( $can_edit, $post_type ) {

					if ( is_singular() || is_admin() ) {

		 				$option = $this->get_pt_option( $post_type );

		 				if ( 'disabled' !== $option ) {

		 					$can_edit = true;
		 				}
					}

					return $can_edit;
				}

				public function filter_pt_options( $options ) {

					$options = [];

					$post_types = get_post_types( '', 'objects' );

					foreach ( $post_types as $post_type ) {

						$option = $this->get_pt_option( $post_type->name );

						if ( ! empty( $option ) && 'lc_templates' == $option ) {

							$options[ $post_type->name ] = $post_type->label;
						}
					}

					return $options;
				}

				/**
				 * Try to find the CPT template type in wp-options. If not found
				 * provide default values.
				 *
				 * @param  [string] $post_type CPT slug
				 * @return [string]            CPT templating setting
				 */
				public function get_pt_option( $post_type ) {

					$options_templatesforcpt = get_option( 'dslc_custom_options_templatesforcpt' );
					$output = 'disabled'; // By default disabled.

					if ( ! empty( $options_templatesforcpt[ 'lc_tpl_for_cpt_' . $post_type ] ) ) {

						$output = $options_templatesforcpt[ 'lc_tpl_for_cpt_' . $post_type ];

					} elseif( array_key_exists( $post_type, self::$default_cpt_settings ) ) {

						$output = self::$default_cpt_settings[$post_type];

					}

					return $output;
				}


				public function register_admin_settings() {

					global $dslc_options_extender;

					$post_types = get_post_types( '', 'objects');
					$avail_types = array();

					foreach ( $post_types as $post_type ) {

						$slug = $post_type->name;

						// error_log( $slug );
						// error_log( $this->get_pt_option( $slug ) );
						// error_log( '=========' );

						if( 'hidden' !== $this->get_pt_option( $slug ) ) {

							$avail_types[] = [
								'id' => 'lc_tpl_for_cpt_' . $slug,
								'section' => 'lc_tpl_for_cpt_settings',
								'label' => __( $post_type->label . '<br /><span style="font-weight:normal">' .  $slug . '</span>', 'live-composer-page-builder' ),
								'std' => $this->get_pt_option( $slug ),
								'type' => 'select',
								'choices' => array(
									[
										'label' => __('Disable Page Builder', 'lccpt' ),
										'value' => 'disabled',
									],
									[
										'label' => __('Use Live Composer Templates', 'lccpt' ),
										'value' => 'lc_templates',
									],
									[
										'label' => __('Unique Design for Each Post', 'lccpt' ),
										'value' => 'unique',
									],
								),
								// 'descr' => __( 'Choose how Live Composer should work with ' . $slug, 'live-composer-page-builder' ),
							];
						}
					}

					$array = [
						'title' => __('Templates for CPT', 'lccpt' ),
						'extension_id' => 'templatesforcpt',
						'sections' => [
							[
								'id' => 'main',
								'title' => __('Templates for Custom Post Types', 'lccpt' ),
								'options' => $avail_types,
							],
						],
					];

					$dslc_options_extender->add_settings_panel( $array );
				}

				public function add_settings_description( $description, $pannel_id ) {
					if ( 'dslc_templatesforcpt_main' === $pannel_id ) {
						$description .= '<p>';
						$description .= 'Live Composer can be used to create reusable templates for any Custom Post Type. For example, you can have CPT with Case Studies. Create a new template in Live Composer for this CPT and it will be used for every Case Study.';
						$description .= '</p>';
						$description .= '<p style="padding:20px; border: 2px solid #F1F1F1;">';
						$description .= '<strong>Disable Page Builder</strong> <br>Use standard WP Editor and the current theme design.';
						$description .= '<br><br>';
						$description .= '<strong>Use Live Composer Templates</strong> <br>Use templates from <a href="/wp-admin/edit.php?post_type=dslc_templates">WP Admin > Appearance > Templates</a>.';
						$description .= '<br><br>';
						$description .= '<strong>Unique Design for Each Post</strong> <br>Use a page builder to create a unique design for the each post.';
						$description .= '</p>';
					}

					return $description;
				}

				/**
				 * Setup the updater.
				 */
				public function lccpt_edd_updater() {

					// Retrieve our license key from the DB.
					$license_key = trim( get_option( 'lccpt_edd_license_key' ) );

					// Setup the updater.
					$edd_updater = new EDD_LCCPT_Plugin_Updater( EDD_LCCPT_STORE_URL, __FILE__, array(
							'version' 	=> '1.0', 		// Current version number.
							'license' 	=> $license_key, 	// license key (used get_option above to retrieve from DB).
							'item_name' => EDD_LCCPT_ITEM_NAME, 	// name of this plugin.
							'author' 	=> 'Live Composer Team',  // Author of this plugin.
							'url'       => home_url(),
						)
					);
				}

				/**
				 * Creates settings in the options table.
				 */
				public function lccpt_edd_register_option() {
					register_setting( 'lccpt_edd_license', 'lccpt_edd_license_key', array( $this, 'lccpt_edd_sanitize_license' ) );
				}

				/**
				 * A callback function that sanitizes the option's value
				 *
				 * @param string $new option value.
				 */
				public function lccpt_edd_sanitize_license( $new ) {
					$old = get_option( 'lccpt_edd_license_key' );
					if ( $old && $old != $new ) {
						delete_option( 'lccpt_edd_license_status' ); // New license has been entered, so must reactivate.
					}
					return $new;
				}

				/**
				 * Activatea license key.
				 */
				public function lccpt_edd_activate_license() {

					// Listen for our activate button to be clicked.
					if ( isset( $_POST['lccpt_edd_license_activate'] ) ) {

						// Run a quick security check.
					 	if ( ! check_admin_referer( 'lccpt_edd_nonce', 'lccpt_edd_nonce' ) ) {
							return; // Get out if we didn't click the Activate button.
					 	}

						// Retrieve the license from the database.
						$license = trim( get_option( 'lccpt_edd_license_key' ) );

						// Data to send in our API request.
						$api_params = array(
							'edd_action' => 'activate_license',
							'license'    => $license,
							'item_name'  => urlencode( EDD_LCCPT_ITEM_NAME ), // The name of our product in EDD.
							'url'        => home_url(),
						);

						// Call the custom API.
						$response = wp_remote_post( EDD_LCCPT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

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

										$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_LCCPT_ITEM_NAME );
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
							$base_url = admin_url( 'admin.php?page=' . EDD_LCCPT_LICENSE_PAGE );
							$redirect = add_query_arg( array( 'lccpt_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

							wp_redirect( $redirect );
							exit();
						}

						update_option( 'lccpt_edd_license_status', $license_data->license );
						wp_redirect( admin_url( 'admin.php?page=' . EDD_LCCPT_LICENSE_PAGE ) );
						exit();
					}
				}

				/**
				 * Deactivate license key.
				 */
				function lccpt_edd_deactivate_license() {

					// Listen for our activate button to be clicked.
					if ( isset( $_POST['lccpt_edd_license_deactivate'] ) ) {

						// Run a quick security check.
					 	if ( ! check_admin_referer( 'lccpt_edd_nonce', 'lccpt_edd_nonce' ) ) {
							return; // Get out if we didn't click the Activate button.
					 	}

						// Retrieve the license from the database.
						$license = trim( get_option( 'lccpt_edd_license_key' ) );

						// Data to send in our API request.
						$api_params = array(
							'edd_action' => 'deactivate_license',
							'license'    => $license,
							'item_name'  => urlencode( EDD_LCCPT_ITEM_NAME ), // The name of our product in EDD.
							'url'        => home_url(),
						);

						// Call the custom API.
						$response = wp_remote_post( EDD_LCCPT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

						// Make sure the response came back okay.
						if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

							if ( is_wp_error( $response ) ) {
								$message = $response->get_error_message();
							} else {
								$message = __( 'An error occurred, please try again.' );
							}

							$base_url = admin_url( 'admin.php?page=' . EDD_LCCPT_LICENSE_PAGE );
							$redirect = add_query_arg( array( 'lccpt_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

							wp_redirect( $redirect );
							exit();
						}

						// Decode the license data.
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );

						// $license_data->license will be either "deactivated" or "failed"
						if ( $license_data->license == 'deactivated' ) {
							delete_option( 'lccpt_edd_license_status' );
						}

						wp_redirect( admin_url( 'admin.php?page=' . EDD_LCCPT_LICENSE_PAGE ) );
						exit();

					}
				}

				/**
				 * This is a means of catching errors from the activation method above and displaying it to the customer
				 */
				public function lccpt_edd_admin_notices() {
					if ( isset( $_GET['lccpt_activation'] ) && ! empty( $_GET['message'] ) ) {
						switch ( $_GET['lccpt_activation'] ) {
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
				public function lccpt_register_admin_settings_license() {

					global $dslc_options_extender;

					$array = [
						'title' => __( 'CPT - License', 'lccpt' ),
						'extension_id' => 'cpt_license',
						'sections' => [
							[
								'id' => 'main',
								'title' => __( 'CPT - License', 'lccpt' ),
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
				public function lccpt_add_settings_description_license( $description, $pannel_id ) {

					if ( 'dslc_cpt_license_main' === $pannel_id ) {

						$license = get_option( 'lccpt_edd_license_key' );
						$status  = get_option( 'lccpt_edd_license_status' );

						// Start output fetching.
						ob_start();
						?>
						<div class="wrap">

								<?php settings_fields( 'lccpt_edd_license' ); ?>

								<table class="form-table">
									<tbody>
										<tr valign="top">
											<th scope="row" valign="top">
												<?php _e( 'License Key' ); ?>
											</th>
											<td>
												<input id="lccpt_edd_license_key" name="lccpt_edd_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
												<label class="description" for="lccpt_edd_license_key"><?php _e( 'Enter your license key' ); ?></label>
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
														<?php wp_nonce_field( 'lccpt_edd_nonce', 'lccpt_edd_nonce' ); ?>
														<input type="submit" class="button-secondary" name="lccpt_edd_license_deactivate" value="<?php _e( 'Deactivate License' ); ?>"/>
													<?php } else {
														wp_nonce_field( 'lccpt_edd_nonce', 'lccpt_edd_nonce' ); ?>
														<input type="submit" class="button-secondary" name="lccpt_edd_license_activate" value="<?php _e( 'Activate License' ); ?>"/>
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

			$lccpt = new LC_TemplatesForCPT();

			// Load EDD custom updater.
			if ( ! class_exists( 'EDD_LCCPT_Plugin_Updater' ) ) {
				include( dirname( __FILE__ ) . '/includes/EDD_LCCPT_Plugin_Updater.php' );
			}
		} else {

			/**
			 * Admin Notice
			 */
			function lccpt_notice_lc_version() {
			?>
			<div class="notice notice-error">
				<p><?php printf( __( 'The "Live Composer - Templates for CPT" add-on requires Live Composer version 1.3+. %sContact our support team%s if you need a previous version.', 'lccpt' ), '<a target="_blank" href="https://livecomposerplugin.com/support/">', '</a>' ); ?></p>
			</div>
			<?php }
			add_action( 'admin_notices', 'lccpt_notice_lc_version' );
		}

	} else {
		/**
		 * Admin Notice
		 */
		function lccpt_inactive_notice() {
		?>
		<div class="error">
			<p><?php printf( __( '%sCan\'t activate CPT extension for Live Composer.%s %sLive Composer%s plugins should be active.', 'lccpt' ), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/live-composer-page-builder/">', '</a>' ); ?></p>
		</div>
		<?php }
		add_action( 'admin_notices', 'lccpt_inactive_notice' );

	} // End if class_exists check.

} add_action( 'plugins_loaded', 'lccpt_plugin_init' );
