<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_PaypalStandard_PaymentGateways_PaypalStandard extends NF_Abstracts_PaymentGateway
{


	private $live_url = 'https://www.paypal.com/cgi-bin/webscr/';

	private $sandbox_url =  'https://www.sandbox.paypal.com/cgi-bin/webscr/';

	protected $_slug = 'paypal-standard';

	public function __construct() {
		parent::__construct();

		$this->_name = __( 'Paypal Standard Payment Gateway', 'ninja-forms-paypal-express' );
	}

	public function process( $action_settings, $form_id, $data ) {

		$new_total = $this->get_total( $action_settings, $data );

		$data[ 'actions' ][ 'paypal-standard' ] = array(
			'total' => $new_total,
		);

		// $total = $action_settings['payment_total'];
		$total = number_format( $action_settings[ 'payment_total' ], 2, '.', '' );
		$quantity =  isset( $action_settings['paypal_standard_product_quantity'] ) ? $action_settings['paypal_standard_product_quantity'] : 1 ;
		$total = $total/$quantity;

		//check if business email is set
		$business_email = isset( $action_settings['paypal_standard_business_email'] ) ? $action_settings['paypal_standard_business_email']: '' ;

		if ( empty( $business_email ) ) {
			return $data;
		}

		if ( empty( $total ) ) {
			return $data;

		}

		//Submission Id
		$sub_id        = $data[ 'actions' ][ 'save' ][ 'sub_id' ];
		$custom_field  = $sub_id . "|" . wp_hash( $sub_id );

		//Plugin mode-- Test/Live
		$plugin_mode   = $action_settings['paypal_standard_gateway_mode'];

		//Currency
		$currency_type = isset( $action_settings['paypal_standard_currency_type'] ) ? $action_settings['paypal_standard_currency_type'] : 'USD' ;

		//Urls
		$ipn_url       = get_bloginfo( "url" ) . "/?page=nf_paypal_standard_ipn";
		$success_url   = $action_settings['paypal_standard_payment_success_url'];
		$cancel_url    = $action_settings['paypal_standard_payment_cancel_url'];

		//user info
		$product_name  = isset( $action_settings['paypal_standard_product_name'] ) ? $action_settings['paypal_standard_product_name'] : '' ;
		$first_name    = isset( $action_settings['paypal_standard_first_name'] ) ? $action_settings['paypal_standard_first_name'] : '' ;
		$last_name     = isset( $action_settings['paypal_standard_last_name'] ) ? $action_settings['paypal_standard_last_name'] : '' ;
		$email         = isset( $action_settings['paypal_standard_email'] ) ? $action_settings['paypal_standard_email'] : '' ;
		$address_1     = isset( $action_settings['paypal_standard_address_1'] ) ? $action_settings['paypal_standard_address_1'] : '' ;
		$address_2     = isset( $action_settings['paypal_standard_address_2'] ) ? $action_settings['paypal_standard_address_2'] : '' ;
		$city          = isset( $action_settings['paypal_standard_city'] ) ? $action_settings['paypal_standard_city'] : '' ;
		$zip           = isset( $action_settings['paypal_standard_zip'] ) ? $action_settings['paypal_standard_zip'] : '' ;
		$state         = isset( $action_settings['paypal_standard_state'] ) ? $action_settings['paypal_standard_state'] : '' ;
		$country       = isset( $action_settings['paypal_standard_country'] ) ? $action_settings['paypal_standard_country'] : '' ;

		// check recurring
		$recurring = $action_settings['paypal_standard_enable_recurring'];

		$paypal_args = array(
			'business'      => $business_email,
			'currency_code' => $currency_type,
			'charset'       => 'UTF-8',
			'rm'            => 2,
			'upload'        => 1,
			'no_note'       => 1,
			'return'        => $success_url,
			'cancel_return' => $cancel_url,
			//'invoice'     => strtoupper( str_replace( ' ', '-', get_bloginfo( 'name' ) ) ) . '-DONATION-' . $sub_id,
			'notify_url'    => $ipn_url,
			'success_url'   => $success_url,
			'cancel_url'    => $cancel_url,
			'no_shipping'   => 1,
			'item_name'     => $product_name,
			'quantity'      => $quantity,
			'first_name'    => $first_name,
			'last_name'     => $last_name,
			'lc'            => '',
			'country'    => $country,
			'state'         => $state,
			'city'          => $city,
			'email'         => $email,
			'on0'           => '',
			'custom'        => $custom_field,
		) ;




		if ( $recurring ) {
			$recurring_times     = $action_settings['paypal_standard_ecurring_times'];

			$paypal_args['cmd'] = "_xclick-subscriptions";
			$paypal_args['a3']  = $total;
			$paypal_args['t3']  = $action_settings['paypal_standard_billing_cycle_type'];
			$paypal_args['p3']  = $action_settings['paypal_standard_billing_cycle_number'];
			$paypal_args['src'] = 1;
			$paypal_args['sra'] = 1;

			if ( !empty( $recurring_time ) ) {
				$paypal_args['srt'] = $recurring_times;
			}
		} else {
			$paypal_args['cmd'] = "_xclick";
			$paypal_args['amount'] = $total;
		}
		$paypal_args = apply_filters( 'nf_paypal_standard_args', $paypal_args );
		//print_r($paypal_args); die;
		$paypal_args = http_build_query( $paypal_args, '', '&' );

		if ( $plugin_mode == 'sandbox' ) {
			$paypal_adr = $this->sandbox_url . '?test_ipn=1&';
		} else {
			$paypal_adr = $this->live_url . '?';
		}

		$payment_link = $paypal_adr . $paypal_args;
		if ( $action_settings['paypal_standard_halt_processing'] ) {
			$data[ 'halt' ] = TRUE;
		}
		$data[ 'actions' ][ 'redirect' ] = $payment_link;
		return $data;
	}

	private function get_total( $settings, $data ) {
		if ( isset( $data[ 'new_total' ] ) && $data[ 'new_total' ] ) {
			return $data[ 'new_total' ];
		} else {
			return FALSE;
		}
	}

	public function get_settings() {

		$paypal_field[$this->_slug.'_general_setting_fieldset' ] = array(
			'name'     => $this->_slug.'_general_setting_fieldset',
			'label'    => __( 'General Settings', 'ninja-forms-paypal-standard' ),
			'type'     => 'fieldset',
			'group'    => 'primary',
			'deps'  => array(
				'payment_gateways' => $this->_slug
			),
			'settings' => $this->general_settings_fields()
		);
		$paypal_field[$this->_slug.'_recurring_payment_fieldset' ] = array(
			'name'     => $this->_slug.'_recurring_payment_fieldset',
			'label'    => __( 'Recurring Payment Settings', 'ninja-forms-paypal-standard' ),
			'type'     => 'fieldset',
			'group'    => 'primary',
			'deps'  => array(
				'payment_gateways' => $this->_slug
			),
			'settings' => $this->recurring_payment_fields()
		);
		$paypal_field[$this->_slug.'_user_fields_fieldset' ] = array(
			'name'     => $this->_slug.'_user_fields_fieldset',
			'label'    => __( 'User Profile Fields', 'ninja-forms-paypal-standard' ),
			'type'     => 'fieldset',
			'group'    => 'primary',
			'deps'  => array(
				'payment_gateways' => $this->_slug
			),
			'settings' => $this->user_fields()
		);



		return $paypal_field;
	}

	public function general_settings_fields() {

		return array( array(
				'name'    => 'paypal_standard_gateway_mode',
				'type'    => 'select',
				'label'   => 'Gateway Mode<small style="color:red">(required)</small>',
				'group'   => 'primary',
				'width'  => 'one-half',
				'options' => array(
					array(
						'label'=>'Live', 'value'=>'live'
					),
					array(
						'label'=>'Sandbox', 'value'=>'sandbox'
					)
				),
			),
			array(
				'name' => 'paypal_standard_business_email',
				'type' => 'textbox',
				'width'  => 'one-half',
				'label' => 'Paypal Business Email<small style="color:red">(required)</small>',

				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),

			array(
				'name'    => 'paypal_standard_product_name',
				'type' => 'textbox',
				'width'  => 'one-half',
				'label' => 'Product/Service Name<small style="color:red">(required)</small>',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name'    => 'paypal_standard_product_quantity',
				'type' => 'textbox',
				'width'  => 'one-half',
				'value' => '1',
				'label' => 'Product Quantity<small style="color:red">(required)</small>',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_currency_type',
				'type' => 'textbox',
				'width'  => 'one-half',
				'label' => 'Payment Currency Code<small style="color:red">(required)</small>',
				'value' => 'USD',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
				'help' => 'Get your currecny code from <a target="_blank" href="https://developer.paypal.com/docs/integration/direct/rest/currency-codes">here</a>',

			),

			array(
				'name'    => 'paypal_standard_halt_processing',
				'type'    => 'toggle',
				'label'   => 'Halt other processing on submit ?',
				'width'  => 'one-half',
				'help' => 'Halts processing of other actions on form submit',

			),

			array(
				'name'  => 'paypal_standard_payment_success_url',
				'type'  => 'textbox',
				'width' => 'full',
				'label' => 'Payment Success Page Link',

			),

			array(
				'name'  => 'paypal_standard_payment_cancel_url',
				'type'  => 'textbox',
				'width' => 'full',
				'label' => 'Payment Cancel Page Link',

			),

		);
	}

	public function recurring_payment_fields() {
		return array( array(
				'name'    => 'paypal_standard_enable_recurring',
				'type'    => 'toggle',
				'label'   => 'Enable Recurring Payments',
				'group'   => 'primary',
				'width' =>'full'

			),
			array(
				'name'    => 'paypal_standard_billing_cycle_number',
				'type'    => 'select',
				'label'   => 'Billing Cycle',
				'width' =>'one-third',

			),

			array(
				'name'    => 'paypal_standard_billing_cycle_type',
				'type'    => 'select',
				'label'   => '<span style="visibility:hidden">Year</span>',
				'options' => array(
					array( 'label'=>'Days', 'value'=>'D' ),
					array( 'label'=>'Weeks', 'value'=>'W' ),
					array( 'label'=>'Months', 'value'=>'M' ),
					array( 'label'=>'Years', 'value'=>'Y' ),
				),
				'width' =>'one-third'

			),
			array(
				'name'    => 'paypal_standard_recurring_times',
				'type'    => 'select',
				'label'   => 'Recurring Times',
				'width' =>'one-half',
				'options' => $this->get_recurring_times(),


			),
		);
	}


	public function user_fields() {
		return  array( array(
				'name' => 'paypal_standard_email',
				'type' => 'textbox',
				'width' => 'full',
				'label' => 'Email',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_first_name',
				'type' => 'textbox',
				'label' => 'First Name',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_last_name',
				'type' => 'textbox',
				'label' => 'Last Name',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_address_1',
				'type' => 'textbox',
				'label' => 'Address 1',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_address_2',
				'type' => 'textbox',
				'label' => 'Address 2',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_zip',
				'type' => 'textbox',
				'label' => 'Zip',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_city',
				'type' => 'textbox',
				'label' => 'City',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_state',
				'type' => 'textbox',
				'label' => 'State',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),
			array(
				'name' => 'paypal_standard_country',
				'type' => 'textbox',
				'label' => 'Country',
				'use_merge_tags' => array(
					'exclude' => array(
						'system', 'querystrings'
					)
				),
			),

		);

	}

	function get_recurring_times() {
		$recurring_times = array();
		array_push( $recurring_times, array( 'label' => __( 'Infinite', 'ninja-forms' ), 'value' => 'infinite' ) );
		for ( $i=2;$i<=52;$i++ ) {
			array_push( $recurring_times, array( 'label' => $i, 'value' => $i ) );
		}
		return $recurring_times;
	}


}
