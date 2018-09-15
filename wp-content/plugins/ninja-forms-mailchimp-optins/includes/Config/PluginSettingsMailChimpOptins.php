<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'mailchimp_api_key' => array(
		'id'    => 'mailchimp_api_key',
		'type'  => 'textbox',
		'label' => __( 'MailChimp API Key', 'ninja-forms-mailchimp-optins' ),
	),
	'envato_license' => array(
		'id'    => 'envato_license',
		'type'  => 'textbox',
		'label' => __( 'Item Purchase Code', 'ninja-forms-mailchimp-optins' ),
		'desc'  => __( 'Item purchase code is required to receive free automatic updates. If you have troubles obtaining your purchase code, please refer to Ninja Forms - MailChimp Opt-ins documentation (docs-3.x).', 'ninja-forms-mailchimp-optins' )
	),
);
