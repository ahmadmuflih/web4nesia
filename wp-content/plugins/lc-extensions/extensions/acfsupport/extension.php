<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lcproext_acfsupport_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'acfsupport',
			'rank' => 10,
			'title' => 'ACF Support',
			'details' => 'https://livecomposerplugin.com/downloads/acf-support/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=acf-support',
			'description' => 'Output any content from custom fields on pages, posts or templates created with Live Composer page builder.',
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'acfsupport' ) ) :

		if ( class_exists( 'acf' ) ) {

			/**
			 * Main LC_AcfIntegration Class.
			 */
			class LC_AcfIntegration {

				/**
				 * Instance var
				 *
				 * @var LC_AcfIntegration The one true LC_AcfIntegration
				 */
				private static $instance;

				/**
				 * Prevent creating multiple instances
				 */
				public function __construct() {

					$this->setup_constants();
					$this->includes();
					add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
				}

				/**
				 * Setup plugin constants.
				 *
				 * @access private
				 * @return void
				 */
				private function setup_constants() {

					// Plugin version.
					if ( ! defined( 'LC_ACFINTEGRATION_VERSION' ) ) {

						define( 'LC_ACFINTEGRATION_VERSION', '1.0' );
					}

					// Plugin Folder Path.
					if ( ! defined( 'LC_ACFINTEGRATION_PLUGIN_DIR' ) ) {

						define( 'LC_ACFINTEGRATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
					}

					// Plugin Folder URL.
					if ( ! defined( 'LC_ACFINTEGRATION_PLUGIN_URL' ) ) {

						define( 'LC_ACFINTEGRATION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
					}

					// Plugin Root File.
					if ( ! defined( 'LC_ACFINTEGRATION_PLUGIN_FILE' ) ) {

						define( 'LC_ACFINTEGRATION_PLUGIN_FILE', __FILE__ );
					}

					// Plugin Text Domain.
					if ( ! defined( 'LC_ACFINTEGRATION_TEXTDOMAIN' ) ) {

						define( 'LC_ACFINTEGRATION_TEXTDOMAIN', 'lc-acfintegration' );
					}
				}

				/**
				 * Include required files.
				 *
				 * @access private
				 * @return void
				 */
				private function includes() {

					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'assets/display-functions.php';

					/**
					 * Basic
					 */
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/basic/text/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/basic/textarea/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/basic/number/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/basic/email/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/basic/url/module.php';

					/**
					 * Content
					 */
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/content/image/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/content/file/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/content/wysiwyg/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/content/oembed/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/content/gallery/module.php';

					/**
					 * Choice
					 */
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/choice/select/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/choice/checkbox/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/choice/radio-button/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/choice/button-group/module.php';

					/**
					 * Relational
					 */
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/relational/link/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/relational/post-object/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/relational/page-link/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/relational/relationship/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/relational/taxonomy/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/relational/user/module.php';

					/**
					 * jQuery
					 */
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/jquery/google-map/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/jquery/date-picker/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/jquery/date-time-picker/module.php';
					require_once LC_ACFINTEGRATION_PLUGIN_DIR . 'modules/jquery/time-picker/module.php';
				}

				/**
				 * Load CSS styles on front end
				 *
				 * @access public
				 * @return void
				 */
				public function load_styles() {

					// Use minified libraries if SCRIPT_DEBUG is turned off.
					// $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
					$suffix  = ''; // Not used for now.
					$css_dir = LC_ACFINTEGRATION_PLUGIN_URL . 'assets/css/';

					wp_register_style( LC_ACFINTEGRATION_TEXTDOMAIN . '-frontend-css', $css_dir . 'lc-acfintegration-extender' . $suffix . '.css', array(), LC_ACFINTEGRATION_VERSION, 'all' );
					wp_enqueue_style( LC_ACFINTEGRATION_TEXTDOMAIN . '-frontend-css' );
				}

			}

			$lc_acf = new LC_AcfIntegration;

		} else {
			/**
			 * Admin Notice
			 */
			function lcacf_inactive_notice() {
			?>
			<div class="error">
				<p><?php printf( __( '%1$sCan\'t activate ACF extension for Live Composer.%2$s Both %3$sACF%4$s and %5$sLive Composer%6$s plugins should be active.', 'lc-acfintegration' ), '<strong>', '</strong>', '<a target="_blank" href="https://en-ca.wordpress.org/plugins/advanced-custom-fields/">', '</a>', '<a target="_blank" href="https://wordpress.org/plugins/live-composer-page-builder/">', '</a>' ); ?></p>
			</div>
			<?php
			}
			add_action( 'admin_notices', 'lcacf_inactive_notice' );
		}

	endif; // If is_extension_active.
}

lcproext_acfsupport_init();
