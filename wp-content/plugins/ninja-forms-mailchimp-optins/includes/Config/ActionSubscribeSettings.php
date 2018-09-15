<?php if ( ! defined( 'ABSPATH' ) ) exit;

return array(
	'mailchimp_to' => array(
		'name'  => 'mailchimp_to',
		'type'  => 'textbox',
		'group' => 'primary',
		'label' => __( 'To', 'ninja-forms-mailchimp-optins' ),
		'placeholder' => __( 'Choose email a field', 'ninja-forms-mailchimp-optins' ),
		'value' => '',
		'width' => 'full',
		'use_merge_tags' => TRUE,
	),
	'mailchimp_double_optin' => array(
		'name'  => 'mailchimp_double_optin',
		'type'  => 'toggle',
		'label' => __( 'Double Optin', 'ninja-forms-mailchimp-optins' ),
		'group' => 'primary',
	),
	'mailchimp_send_language' => array(
		'name'  => 'mailchimp_send_language',
		'type'  => 'toggle',
		'label' => __( 'Send User\'s Language Data', 'ninja-forms-mailchimp-optins' ),
		'group' => 'primary',
	),
	'mailchimp_send_ip' => array(
		'name'  => 'mailchimp_send_ip',
		'type'  => 'toggle',
		'label' => __( 'Send User\'s IP', 'ninja-forms-mailchimp-optins' ),
		'group' => 'primary'
	),
);
