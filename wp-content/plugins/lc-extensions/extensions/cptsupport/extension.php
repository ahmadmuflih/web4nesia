<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_cptsupport_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'cptsupport',
			'rank' => 11,
			'title' => 'CPT Support',
			'details' => 'https://livecomposerplugin.com/downloads/cpt-support/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=cpt-support',
			'description' => 'This extension adds full support for Custom Post Types. You can create shared LC templates for any CPT or disable page builder completely for any Custom Post Type on your website.',
		)
	);

	/**
	 * Then, check if this extension is active.
	 * Don't load any plugin data if the extension is disabled.
	 */
	if ( LC_Extensions_Core::is_extension_active( 'cptsupport' ) ) :

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
						add_action( 'dslc_extend_admin_panel_options', array( $this, 'register_admin_settings' ) );
						add_filter( 'dslc_post_templates_post_types', array( $this, 'filter_pt_options' ), 1 );
						add_filter( 'dslc_cpt_use_templates', array( $this, 'use_templates' ), 10, 2  );
						add_filter( 'dslc_can_edit_in_lc', array( $this, 'can_edit' ), 10, 2  );
						add_filter( 'dslc_filter_section_description', array( $this, 'add_settings_description' ), 10, 2  );
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

							define( 'LCCPT_VERSION', '1.0.1' );
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
							} else {
								$can_edit = false;
							}
						}

						return $can_edit;
					}

					public function filter_pt_options( $options ) {

						// $options = [];
						$post_types = get_post_types( '', 'objects' );

						foreach ( $post_types as $post_type ) {
							$option = $this->get_pt_option( $post_type->name );

							if ( ! empty( $option ) && 'lc_templates' == $option ) {
								$options[ $post_type->name ] = $post_type->label;
							}
						}

						// delete_transient( 'lc_cache' );
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
				}

				$lccpt = new LC_TemplatesForCPT();

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

	endif; // If is_extension_active.
}

lcproext_cptsupport_init();