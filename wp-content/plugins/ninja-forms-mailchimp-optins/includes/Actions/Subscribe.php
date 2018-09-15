<?php
if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_ActionNewsletter' ) ) {
	exit;
}

/**
 * Class NF_MailChimpOptins_Actions_Subscribe
 */
final class NF_MailChimpOptins_Actions_Subscribe extends NF_Abstracts_ActionNewsletter
{
	protected $_name     = 'mailchimp-optins';
	protected $_tags     = array( 'mailchimp', 'newsletter' );
	protected $_timing   = 'normal';
	protected $_priority = '50';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_nicename = __( 'MailChimp Optin', 'ninja-forms-mailchimp-optins' );
		$this->_settings = array_merge( $this->_settings, NF_MailChimpOptins::config( 'ActionSubscribeSettings' ) );
	}

	/**
	 * Fetches latest MailChimp lists.
	 *
	 * @since 3.0.0
	 */
	protected function get_lists()
	{
		// Prep results array
		$results = array();

		// Get API Key
		$api_key = Ninja_Forms()->get_setting( 'mailchimp_api_key' );
		if ( !empty( $api_key ) ) {
			// Get latest list
			$mailchimp = new \DrewM\MailChimp\MailChimp( $api_key );
			$lists     = $mailchimp->get('lists');
			if ( $mailchimp->success() && is_array( $lists ) && isset( $lists['lists'] ) && count( $lists['lists'] ) > 0 ) {
				foreach( $lists['lists'] as $list ) {

					// Let's try to get merge fields
					$fields     = array(); // Storage for merge fields
					$merge_fields = $mailchimp->get( 'lists/' . $list['id'] . '/merge-fields' );
					if ( $mailchimp->success() && is_array( $merge_fields ) && isset( $merge_fields['merge_fields'] ) && count( $merge_fields['merge_fields'] ) > 0 ) {
						foreach( $merge_fields['merge_fields'] as $field ) {
							$fields[] = array(
								'value' => $field['tag'],
								'label' => $field['name']
							);
						}
					}

					$results[] = array(
						'value'  => $list['id'],
						'label'  => $list['name'],
						'fields' => $fields
					);
				}

				// Let's store this data since we will need it later
				// to find merge tags in $data array.
				Ninja_Forms()->update_setting( 'mailchimp_api_data', $results );
			}
		}

		return $results;
	}

	/**
	 * Extra action save functionality should be implemented here (not required).
	 *
	 * @param array $action_settings Action settings key-value pairings.
	 * @since 3.0.0
	 */
	public function save( $action_settings ) {
	}

	/**
	 * Subscribe user to the selected list.
	 *
	 * @param array $action_settings Action settings
	 * @param int $form_id Form ID
	 * @param array $data Form data
	 * @since 3.0.0
	 */
	public function process( $action_settings, $form_id, $data )
	{
		$api_key       = Ninja_Forms()->get_setting( 'mailchimp_api_key' );
		$api_data      = Ninja_Forms()->get_setting( 'mailchimp_api_data' );
		$list_id       = isset( $action_settings['newsletter_list'] ) ? $action_settings['newsletter_list'] : null;
		$email         = isset( $action_settings['mailchimp_to'] ) && filter_var( $action_settings['mailchimp_to'], FILTER_VALIDATE_EMAIL ) ? $action_settings['mailchimp_to'] : null;
		$double_optin  = isset( $action_settings['mailchimp_double_optin'] ) && $action_settings['mailchimp_double_optin'] == true ? true : false;
		$send_language = isset( $action_settings['mailchimp_send_language'] ) && $action_settings['mailchimp_send_language'] == true ? true : false;
		$send_ip       = isset( $action_settings['mailchimp_send_ip'] ) && $action_settings['mailchimp_send_ip'] == true ? true : false;

		// Get merge tags
		$merge_fields = array();
		if ( count( $api_data ) > 0 ) {
			foreach( $api_data as $list ) {
				if ( $list['value'] == $list_id && isset( $list['fields'] ) && count( $list['fields'] ) > 0 ) {
					foreach( $list['fields'] as $field ) {
						if ( isset( $action_settings[$field['value']] ) ) {
							$merge_fields[$field['value']] = $action_settings[$field['value']];
						}
					}
				}
			}
		}

		if ( $api_key && $list_id && $email ) {
			$result = $this->subscribe( $api_key, $list_id, $email, $merge_fields, $double_optin, $send_language, $send_ip );
		}

		return $data;
	}

	/**
	 * Subscribers user to mailing list through MailChimp API
	 *
	 * @param string $action_settings Action settings
	 * @param string $form_id Form ID
	 * @param string $data Form data
	 * @param array $fields Merge fields array (key - value pairs)
	 * @param bool $double_optin When true, enables double optin.
	 * @param bool $send_language Sends language information to MailChimp, when set to true.
	 * @param bool $send_ip Sends (signup) IP address to MailChimp when set to true.
	 * @since 3.0.0
	 */
	private function subscribe( $api_key, $list_id, $email, $fields, $double_optin = false, $send_language = false, $send_ip = false )
	{
		$data = array(
			'email_address' => $email,
			'status'        => ( $double_optin == true ) ? 'pending' : 'subscribed',
			'merge_fields'  => $fields,
		);

		// Language
		$lang = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && strlen( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) > 1
			? substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) : false;

		if ( $send_language && $lang ) {
			$data['language'] = $lang;
		}

		// IP Address
		if ( $send_ip ) {
			$data['ip_signup'] = $this->get_client_ip_address();
		}

		$mailchimp = new \DrewM\MailChimp\MailChimp( $api_key );
		$result    = $mailchimp->post( 'lists/' . $list_id . '/members', $data );
		if ( $mailchimp->success() ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves client IP address (attempting several options).
	 *
	 * @since 3.0.0
	 * @return string IP address.
	 */
	private function get_client_ip_address()
	{
		$ip = '0.0.0.0'; // Unknown IP

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}
