<?php

/**
 * Scripts
 */

add_action( 'wp_enqueue_scripts', 'spc_scripts' );
function spc_scripts() {

	wp_enqueue_style( 'spc-bai-css', SPC_BAI_URL . 'css/style.css');
	wp_enqueue_script( 'spc-bai-js', SPC_BAI_URL . 'js/javascript.js' );

}
