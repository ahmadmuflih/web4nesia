<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lcproext_animations_init() {

	// Register a new feature:
	LC_Extensions_Core::register_extension(
		array(
			'id' => 'animations',
			'rank' => 38,
			'title' => 'Additinal Animations',
			'details' => 'https://livecomposerplugin.com/downloads/additional-animations/?utm_source=lcproext&utm_medium=extensions-list&utm_campaign=aditional-animations',
			'description' => '47 additional animations for Live Composer modules. Extension adds new options into Styling > Animation > On Load Animation. Animate any module with advanced effects when a page gets loaded.',				
		)
	);

}

lcproext_animations_init();


/**
 * Then, check if this extension is active.
 * Don't load any plugin data if the extension is disabled.
 */
if ( LC_Extensions_Core::is_extension_active( 'animations' ) ) :

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0
	 */

	//@todo: add css only if this extension is active.
	function sklc_addon_anim_scripts() {

		wp_enqueue_style( 'sklc-addon-anim-css', plugin_dir_url( __FILE__ ) . 'css/animations.css', array(), '1.0' );

	} add_action( 'wp_enqueue_scripts', 'sklc_addon_anim_scripts' );

	/**
	 * Filter the animations options
	 *
	 * @since 1.0
	 */
	//@todo: run this function only if this extension is active.
	function sklc_addon_anim_filter( $options ) {

		// New options
		$addon_options = array(
			array(
				'label' => 'Flash',
				'value' => 'sklcFlash'
			),
			array(
				'label' => 'Pulse',
				'value' => 'sklcPulse'
			),
			array(
				'label' => 'Rubber Band',
				'value' => 'sklcRubberBand'
			),
			array(
				'label' => 'Shake',
				'value' => 'sklcShake'
			),
			array(
				'label' => 'Swing',
				'value' => 'sklcSwing'
			),
			array(
				'label' => 'Tada',
				'value' => 'sklcTada'
			),
			array(
				'label' => 'Wobble',
				'value' => 'sklcWobble'
			),
			array(
				'label' => 'Bounce',
				'value' => 'sklcBounce'
			),
			array(
				'label' => 'BounceIn',
				'value' => 'sklcBounceIn'
			),
			array(
				'label' => 'BounceInDown',
				'value' => 'sklcBounceInDown'
			),
			array(
				'label' => 'BounceInLeft',
				'value' => 'sklcBounceInLeft'
			),
			array(
				'label' => 'BounceInRight',
				'value' => 'sklcBounceInRight'
			),
			array(
				'label' => 'BounceInUp',
				'value' => 'sklcBounceInUp'
			),
			array(
				'label' => 'BounceOut',
				'value' => 'sklcBounceOut'
			),
			array(
				'label' => 'BounceOutDown',
				'value' => 'sklcBounceOutDown'
			),
			array(
				'label' => 'BounceOutLeft',
				'value' => 'sklcBounceOutLeft'
			),
			array(
				'label' => 'BounceOutRight',
				'value' => 'sklcBounceOutRight'
			),
			array(
				'label' => 'BounceOutUp',
				'value' => 'sklcBounceOutUp'
			),
			array(
				'label' => 'Flip In X',
				'value' => 'sklcFlipInX'
			),
			array(
				'label' => 'Flip In Y',
				'value' => 'sklcFlipInY'
			),
			array(
				'label' => 'Flip Out X',
				'value' => 'sklcFlipOutX'
			),
			array(
				'label' => 'Flip Out Y',
				'value' => 'sklcFlipOutY'
			),
			array(
				'label' => 'Light Speed In',
				'value' => 'sklcLightSpeedIn'
			),
			array(
				'label' => 'Light Speed Out',
				'value' => 'sklcLightSpeedOut'
			),
			array(
				'label' => 'Rotate In',
				'value' => 'sklcRotateIn'
			),
			array(
				'label' => 'Rotate In Down Left',
				'value' => 'sklcRotateInDownLeft'
			),
			array(
				'label' => 'Rotate In Down Right',
				'value' => 'sklcRotateInDownRight'
			),
			array(
				'label' => 'Rotate In Up Left',
				'value' => 'sklcRotateInUpLeft'
			),
			array(
				'label' => 'Rotate In Up Right',
				'value' => 'sklcRotateInUpRight'
			),
			array(
				'label' => 'Rotate Out',
				'value' => 'sklcRotateOut'
			),
			array(
				'label' => 'Rotate Out Down Left',
				'value' => 'sklcRotateOutDownLeft'
			),
			array(
				'label' => 'Rotate Out Down Right',
				'value' => 'sklcRotateOutDownRight'
			),
			array(
				'label' => 'Rotate Out Up Left',
				'value' => 'sklcRotateOutUpLeft'
			),
			array(
				'label' => 'Rotate Out Up Right',
				'value' => 'sklcRotateOutUpRight'
			),
			array(
				'label' => 'Hinge',
				'value' => 'sklcHinge'
			),
			array(
				'label' => 'Roll In',
				'value' => 'sklcRollIn'
			),
			array(
				'label' => 'Roll Out',
				'value' => 'sklcRollOut'
			),
			array(
				'label' => 'Zoom In',
				'value' => 'sklcZoomIn'
			),
			array(
				'label' => 'Zoom In Down',
				'value' => 'sklcZoomInDown'
			),
			array(
				'label' => 'Zoom In Left',
				'value' => 'sklcZoomInLeft'
			),
			array(
				'label' => 'Zoom In Right',
				'value' => 'sklcZoomInRight'
			),
			array(
				'label' => 'Zoom In Up',
				'value' => 'sklcZoomInUp'
			),
			array(
				'label' => 'Zoom Out',
				'value' => 'sklcZoomOut'
			),
			array(
				'label' => 'Zoom Out Down',
				'value' => 'sklcZoomOutDown'
			),
			array(
				'label' => 'Zoom Out Left',
				'value' => 'sklcZoomOutLeft'
			),
			array(
				'label' => 'Zoom Out Right',
				'value' => 'sklcZoomOutRight'
			),
			array(
				'label' => 'Zoom Out Up',
				'value' => 'sklcZoomOutUp'
			),
		);
		
		// Merge new options with original options
		$new_options = array_merge( $options, $addon_options );

		// Pass it back
		return $new_options;

	} add_filter( 'dslc_animation_options', 'sklc_addon_anim_filter' );


endif; // If is_extension_active.