<?php
/*
	Plugin Name: Ninja Forms Paypal Standard
	Description:Paypal Standard Payment Gateway for Ninja forms
	Author: Aman Saini
	Author URI: http://amansaini.me
	Plugin URI: http://amansaini.me
	Version: 1.1.3
	Requires at least: 3.5
	Tested up to: 4.6
*/

// don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );


add_action( 'plugins_loaded', array( 'Ninja_Forms_Paypal_Standard', 'setup' ) );

class Ninja_Forms_Paypal_Standard {

	/**
	 * Paypal Live Url
	 * @var string
	 */
	private static $production_url = "https://www.paypal.com/cgi-bin/webscr/";

	/**
	 * Paypal Sandbox Url
	 * @var string
	 */
	private static $sandbox_url = "https://www.sandbox.paypal.com/cgi-bin/webscr/";

	/**
	 * Add necessary hooks and filters functions
	 *
	 * @author Aman Saini
	 * @since  1.0
	 */
	function __construct() {


		if ( is_admin() ) {

			//Add config metabox to the form setting tab
			add_filter( 'admin_init', array( $this, 'paypal_settings_metabox' ) );

			//Add payment metabox to submission page
			add_action( 'add_meta_boxes', array( $this, 'add_payment_status_meta_box' ) );

			//Add Payment status Label  to Export file.
			add_filter( 'nf_subs_csv_label_array', array( $this, 'add_payment_label_sub_export' ) ,11, 2 );

			//Add Payment status field to Export file.
			add_filter( 'nf_subs_csv_value_array', array( $this, 'add_payment_field_sub_export' ) ,11, 2 );


		}else {
			add_action( 'init', array( $this, 'nf_init_paypal_standard_hook' ) );
		}
		add_action( 'parse_request', array( $this, "process_ipn" ) );

	}

	function nf_init_paypal_standard_hook() {
		add_action( 'ninja_forms_post_process', array( $this, 'nf_paypal_standard_processing' ),1200 );
	}

	//Plugin starting point.
	public static function setup() {
		if ( ! self::is_ninjaform_installed() ) {
			return;
		}
		$class = __CLASS__;
		new $class;
	}


	function nf_paypal_standard_processing() {

		global $ninja_forms_processing;

		//check is paypal is enabled for this form
		$paypal_enabled = $ninja_forms_processing->get_form_setting( 'enable_paypal_standard' );
		if ( empty( $paypal_enabled ) )
			return;

		//check if business email is set
		$business_email = $ninja_forms_processing->get_form_setting( 'paypal_standard_business_email' );
		if ( empty( $business_email ) )
			return;

		// get the form total
		$form_cost = $ninja_forms_processing->get_calc_total(false);
		//echo $form_cost; die;
		if ( empty( $form_cost ) )
			return;

		//submission Id
		$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );
		update_post_meta( $sub_id, 'payment_standard_status', 'Not Paid' );
		$custom_field = $sub_id . "|" . wp_hash( $sub_id );

		//Get an array of all user-submitted values:
		$all_fields = $ninja_forms_processing->get_all_fields();

		// get the user info
		$user_info = $ninja_forms_processing->get_user_info();

		// Plugin mode-- Test/Live
		$plugin_mode = $ninja_forms_processing->get_form_setting( 'paypal_standard_mode' );

		// Fields
		$product_name_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_product_name' );
		$first_name_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_first_name' );
		$last_name_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_last_name' );
		$email_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_email' );
		$address1_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_address1' );
		$address2_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_address2' );
		$city_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_city' );
		$state_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_state' );
		$zip_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_zip' );
		$country_field_id = $ninja_forms_processing->get_form_setting( 'paypal_standard_country' );


		$product_name = ( !empty( $all_fields[$product_name_field_id] ) )?$all_fields[$product_name_field_id] :'';
		$first_name = ( !empty( $all_fields[$first_name_field_id] ) ) ? $all_fields[$first_name_field_id]:'';
		$last_name = ( !empty( $all_fields[$last_name_field_id] ) ) ? $all_fields[$last_name_field_id]:'';
		$email = ( !empty( $all_fields[$email_field_id] ) )?$all_fields[$email_field_id]:'';
		$address_1 = ( !empty( $all_fields[$address1_field_id] ) ) ? $all_fields[$address1_field_id]:'';
		$address_2 = ( !empty( $all_fields[$address2_field_id] ) ) ? $all_fields[$address2_field_id]:'';
		$city = ( !empty( $all_fields[$city_field_id] ) ) ? $all_fields[$city_field_id]:'';
		$zip = ( !empty( $all_fields[$zip_field_id] ) ) ? $all_fields[$zip_field_id]:'';
		$state = ( !empty( $all_fields[$state_field_id] ) ) ? $all_fields[$state_field_id]:'';
		$country = ( !empty( $all_fields[$country_field_id] ) ) ? $all_fields[$country_field_id]:'';





		//currency
		$currency_type = $ninja_forms_processing->get_form_setting( 'paypal_standard_currency_type' );
		//Urls
		$ipn_url = get_bloginfo( "url" ) . "/?page=nf_paypal_standard_ipn";

		$success_url = $ninja_forms_processing->get_form_setting( 'paypal_standard_success_page' );

		$cancel_url = $ninja_forms_processing->get_form_setting( 'paypal_standard_cancel_page' );


		// check recurring
		$recurring = $ninja_forms_processing->get_form_setting( 'paypal_standard_recurring' );

		$paypal_args = apply_filters( 'nf_paypal_standard_args', array(
				'business'      => $business_email,
				'currency_code' => $currency_type,
				'charset'       => 'UTF-8',
				'rm'            => 2,
				'upload'        => 1,
				'no_note'       => 1,
				'return'        => $success_url,
				'cancel_return' => $cancel_url,
				//'invoice'       => strtoupper( str_replace( ' ', '-', get_bloginfo( 'name' ) ) ) . '-DONATION-' . $sub_id,
				'custom'        => $custom_field,
				'notify_url'    => $ipn_url,
				'success_url' => $success_url,
				'cancel_url' => $cancel_url,
				'no_shipping'   => 1,
				'item_name'   => $product_name,
				'quantity'    => 1,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'lc' => '',
				'country' => $country,
				'state' => $state,
				'city' => $city,
				'email' => $email,
				'on0' => '',
				'custom' => $custom_field,
			) );


		if ( $recurring ) {
			$cycle_number = $ninja_forms_processing->get_form_setting( 'paypal_standard_billing_cycle_number' );
			$cycle_type = $ninja_forms_processing->get_form_setting( 'paypal_standard_billing_cycle_type' );
			$recurring_time = $ninja_forms_processing->get_form_setting( 'paypal_standard_recurring_time' );

			$paypal_args['cmd'] = "_xclick-subscriptions";
			$paypal_args['a3'] = $form_cost;
			$paypal_args['t3'] = $cycle_type;
			$paypal_args['p3'] = $cycle_number;
			$paypal_args['src'] = 1;
			$paypal_args['sra'] = 1;

			if ( !empty( $recurring_time ) ) {
				$paypal_args['srt'] = $recurring_time;
			}
		} else {
			$paypal_args['cmd'] = "_xclick";
			$paypal_args['amount'] = $form_cost;
		}

		$paypal_args = http_build_query( $paypal_args, '', '&' );

		if ( $plugin_mode == 'sandbox' ) {
			$paypal_adr = self::$sandbox_url . '?test_ipn=1&';
		}else {
			$paypal_adr = self::$production_url . '?';
		}

		$payment_link = $paypal_adr . $paypal_args;

		wp_redirect( $payment_link );
		exit;

	}





	/**
	 * Add Paypal Settings metabox on Form setting Page
	 *
	 * @author Aman Saini
	 * @since  1.1
	 * @param unknown
	 */

	function paypal_settings_metabox() {

		if ( !isset( $_GET['page'] )  || !isset( $_GET['tab'] ) )
			return;

		if ( $_GET['page']!='ninja-forms' || $_GET['tab']!='form_settings' )
			return;

		if ( $_GET['form_id']!='new' ) {

			//Get all the fields that are in form .
			$all_fields = ninja_forms_get_fields_by_form_id( $_GET['form_id'] );

			$fields_list=array( array( 'name'=>'', 'value'=>'' ) );

			foreach ( $all_fields as $field ) {
				array_push( $fields_list, array( 'name' =>$field['data']['label'] , 'value' => $field['id'] ) );
			}

		}else {
			$fields_list=array();
			array_push( $fields_list, array( 'name' => 'Please add fields', 'value' => '' ) );
		}


		$pages = get_pages();
		$pages_list = array();
		array_push( $pages_list, array( 'name' => __( '- None', 'ninja-forms' ), 'value' => '' ) );
		foreach ( $pages as $pagg ) {
			array_push( $pages_list, array( 'name' => $pagg->post_title, 'value' => get_page_link( $pagg->ID ) ) );
		}



		$fields = array(
			'page' => 'ninja-forms',
			'tab' => 'form_settings',
			'slug' => 'paypal_standard_settings',
			'title' => __( 'Paypal Standard Settings', 'ninja-paypal1' ),
			'state' => 'closed',

			'settings' => array(
				array(
					'name' => 'enable_paypal_standard',
					'type' => 'checkbox',
					'label' => __( 'Enable Paypal Standard', 'ninja-paypal' ),
					'desc' => __( 'Enable Paypal standard gateway on this form', 'ninja-paypal' ),
				),
				array(
					'name' => 'paypal_standard_mode',
					'type' => 'radio',
					'default_value' => 'live',
					'label' => __( 'Gateway Mode', 'ninja-paypal' ),
					'options'=>array(
						array(
							'name'=>'Live',
							'value'=>'live'
						),
						array(
							'name'=>'Test',
							'value'=>'sandbox'

						)
					),
					'desc' => '',
				),
				array(
					'name' => 'paypal_standard_business_email',
					'type' => 'text',
					'label' => __( 'Business Email', 'ninja-paypal' ),
					'desc' => '',
				),

				array(
					'name' => 'paypal_standard_currency_type',
					'type' => 'text',
					'label' => __( 'Currency Type', 'ninja-paypal' ),
					'default_value' => 'USD',
					'desc' => __( 'e.g. USD, EUR, visit <a href="https://developer.paypal.com/docs/classic/api/currency_codes/">paypal</a> to see supported currency codes', 'ninja-forms' ),
				),
				array(
					'name' => 'paypal_standard_recurring',
					'type' => 'checkbox',
					'label' => __( 'Enable Recurring Payments', 'ninja-paypal' ),
					'desc' => __( 'Enable Recurring Payments ', 'ninja-paypal' ),
				),

				array(
					'name' => 'paypal_standard_billing_cycle_number_and_type',
					'type' =>'',
					'display_function'=>array( $this, 'paypal_standard_billing_cycle_fields' ),
					'label' => __( 'Billing Cycle', 'ninja-paypal' ),
					'desc' => __( 'Select how often you want recurring payments to occur', 'ninja-paypal' ),
				),
				array(
					'name' => 'paypal_standard_recurring_time',
					'type' =>'',
					'display_function'=>array( $this, 'paypal_standard_recurring_time_field' ),
					'label' => __( 'Recurring Times', 'ninja-paypal' ),
					'desc' => __( 'Set how many times recurring payments should be made, default is infinite', 'ninja-paypal' ),
				),

				array(
					'name' => 'paypal_standard_product_name',
					'type' => 'select',
					'label' => __( 'Product/Service Name', 'ninja-paypal' ),
					'options' =>$fields_list,
					'desc'=> 'This will show in Paypal as the item name for which you are taking payment.You can use hidden field in form and set that field here'

				),


				array(
					'name' => 'paypal_standard_first_name',
					'type' => 'select',
					'label' => __( 'First Name', 'ninja-paypal' ),
					'options' =>$fields_list
				),

				array(
					'name' => 'paypal_standard_last_name',
					'type' => 'select',
					'label' => __( 'Last Name', 'ninja-paypal' ),
					'options' =>$fields_list
				),
				array(
					'name' => 'paypal_standard_email',
					'type' => 'select',
					'label' => __( 'Email', 'ninja-paypal' ),
					'options' =>$fields_list
				),

				array(
					'name' => 'paypal_standard_address1',
					'type' => 'select',
					'label' => __( 'Address 1', 'ninja-paypal' ),
					'options' =>$fields_list
				),

				array(
					'name' => 'paypal_standard_address2',
					'type' => 'select',
					'label' => __( 'Address 2', 'ninja-paypal' ),
					'options' =>$fields_list
				),
				array(
					'name' => 'paypal_standard_city',
					'type' => 'select',
					'label' => __( 'City', 'ninja-paypal' ),
					'options' =>$fields_list
				),

				array(
					'name' => 'paypal_standard_state',
					'type' => 'select',
					'label' => __( 'State', 'ninja-paypal' ),
					'options' =>$fields_list
				),

				array(
					'name' => 'paypal_standard_zip',
					'type' => 'select',
					'label' => __( 'Zip', 'ninja-paypal' ),
					'options' =>$fields_list
				),

				array(
					'name' => 'paypal_standard_country',
					'type' => 'select',
					'label' => __( 'Country', 'ninja-paypal' ),
					'options' =>$fields_list
				),

				array(
					'name' => 'paypal_standard_success_page',
					'type' => 'select',
					'label' => __( 'Payment Success Page', 'ninja-paypal' ),
					'options' => $pages_list,
					'desc' => __( 'Select the page user will return after making successful payment.', 'ninja-forms' ),
				),
				array(
					'name' => 'paypal_standard_cancel_page',
					'type' => 'select',
					'options' => $pages_list,
					'label' => __( 'Payment Cancel Page', 'ninja-paypal' ),
					'desc' => __( 'Select the page user will return if payment is canceled.', 'ninja-forms' ),
				),



			)
		);

		ninja_forms_register_tab_metabox( $fields );
	}
	/**
	 * Adds billing cycle and type dropdown to metabox
	 *
	 * @author Aman Saini
	 * @since  1.1
	 * @param [type]  $form_id          [description]
	 * @param [type]  $current_settings [description]
	 * @param [type]  $s                [description]
	 * @return [type]                   [description]
	 */
	function paypal_standard_billing_cycle_fields( $form_id, $current_settings, $s ) {

		//Get current values
		$value_number = self::get_value( $current_settings, 'paypal_standard_billing_cycle_number' );
		$value_type = self::get_value( $current_settings, 'paypal_standard_billing_cycle_type' );

		if ( $value_number == '' ) {
			$value = 1;
		}
		if ( $value_type == '' ) {
			$value = 'D';
		}
?>
		<select name="paypal_standard_billing_cycle_number" id="paypal_standard_billing_cycle_number" >
		<?php for ( $i=1;$i<=100;$i++ ) { ?>
		<option value="<?php echo $i;?>" <?php selected( $value_number, $i ); ?>><?php echo $i;?></option>
		<?php } ?>
		</select>

		<select onchange="SetPeriodNumberPayPal('#paypal_standard_billing_cycle_number', jQuery(this).val())" name="paypal_standard_billing_cycle_type" id="paypal_standard_billing_cycle_type" >

		<option value="D" <?php selected( $value_type, "D" ); ?>>days</option>
		<option value="W" <?php selected( $value_type, "W" ); ?>>weeks</option>
		<option value="M" <?php selected( $value_type, "M" ); ?>>months</option>
		<option value="Y" <?php selected( $value_type, "Y" ); ?>>years</option>

		</select>
	<script type="text/javascript">
		 jQuery(document).ready(function(){
                SetPeriodNumberPayPal('#paypal_standard_billing_cycle_number', jQuery("#paypal_standard_billing_cycle_type").val());

            });


		 function SetPeriodNumberPayPal(element, type){
              var prev = jQuery(element).val();

                var min = 1;
                var max = 0;
                switch(type){
                    case "D" :
                        max = 100;
                    break;
                    case "W" :
                        max = 52;
                    break;
                    case "M" :
                        max = 12;
                    break;
                    case "Y" :
                        max = 5;
                    break;
                }
                var str="";
                for(var i=min; i<=max; i++){
                    var selected = prev == i ? "selected='selected'" : "";
                    str += "<option value='" + i + "' " + selected + ">" + i + "</option>";
                }
                jQuery(element).html(str);
            }

            </script>
<?php
	}

	/**
	 * Add Recurring times field
	 *
	 * @author Aman Saini
	 * @since  1.0
	 * @param [type]  $form_id          [description]
	 * @param [type]  $current_settings [description]
	 * @param [type]  $s                [description]
	 * @return [type]                   [description]
	 */
	function paypal_standard_recurring_time_field( $form_id, $current_settings, $s ) {

		//Get current values
		$recurring_times = self::get_value( $current_settings, 'paypal_standard_recurring_time' );

		if ( $recurring_times == '' ) {
			$recurring_times ='infinite';
		}
?>

		<select name="paypal_standard_recurring_time" id="paypal_standard_recurring_time" >
		<option value="" <?php selected( $recurring_times, 'infinite' ); ?>>Infinite</option>
		<?php for ( $i=2;$i<=52;$i++ ) { ?>
		<option value="<?php echo $i;?>" <?php selected( $recurring_times, $i ); ?>><?php echo $i;?></option>
		<?php } ?>
		</select>
<?php

	}


	/**
	 * Get value of a setting field
	 *
	 * @author Aman Saini
	 * @since  1.1
	 * @param [type]  $current_settings [description]
	 * @param [type]  $name             [description]
	 * @return [type]                   [description]
	 */
	public static function get_value( $current_settings, $name ) {

		if ( isset( $current_settings[$name] ) ) {
			if ( is_array( $current_settings[$name] ) ) {
				$value = ninja_forms_stripslashes_deep( $current_settings[$name] );
			}else {
				$value = stripslashes( $current_settings[$name] );
			}
		}else {
			$value = '';
		}

		return $value;
	}


	/**
	 * Add payment box to submission page for Ninja Forms
	 *
	 * @author Aman Saini
	 * @since  1.0
	 */
	function add_payment_status_meta_box( $post_type ) {


		if ( $post_type=='nf_sub' ) {
			add_meta_box(
				'nf_paypal_standard_box'
				, __( 'Paypal Standard', 'ninja_forms' )
				, array( $this, 'render_payment_meta_box_content' )
				, $post_type
				, 'side'
				, 'low'
			);
		}

	}

	/**
	 * Displays the payment status in the submission detail page in admin
	 *
	 * @author Aman Saini
	 * @since  1.0
	 * @return [type] [description]
	 */
	function render_payment_meta_box_content( $post ) {

		$payment_status= get_post_meta( $post->ID, 'payment_standard_status', true );
		echo '<span >';
		_e( 'Payment Status : ', 'ninja_forms' );

		echo $payment_status.'</span>';

	}

	/**
	 * Add coloumn header to export file
	 *
	 * @author Aman Saini
	 * @since  1.1.2
	 * @param  $label_array
	 * @param  $sub_ids
	 */
	function add_payment_label_sub_export( $label_array, $sub_ids ){

		$label_array[0]['paypal_payment_status']="Payment Status";
		return $label_array;

	}

	/**
	 * Add status of payment to export file
	 *
	 * @author Aman Saini
	 * @since  1.1.2
	 * @param  $value_array
	 * @param  $sub_ids
	 */
	function add_payment_field_sub_export( $value_array, $sub_ids ){
		$payment_status = get_post_meta( $sub_ids[0], 'payment_standard_status',true);
		$value_array[0]['paypal_payment_status']=$payment_status;
		return $value_array;

	}



	public static function process_ipn( $wp ) {

		if ( !self::is_ninjaform_installed() )
			return;

		//Ignore requests that are not IPN
		if ( self::get( 'page' ) != "nf_paypal_standard_ipn" )
			return;

		//Send request to paypal and verify it has not been spoofed
		if ( !self::verify_paypal_ipn() ) {

			return;
		}

		//Valid IPN requests must have a custom field
		$custom = self::post( "custom" );
		if ( empty( $custom ) ) {

			return;
		}

		//Getting submission associated with this IPN message (sub id is sent in the "custom" field)
		list( $sub_id, $hash ) = explode( "|", $custom );

		$hash_matches = wp_hash( $sub_id ) == $hash;
		//Validates that Sub Id wasn't tampered with
		if ( !self::post( "test_ipn" ) && !$hash_matches ) {

			return;
		}

		// Update payment status
		if ( self::post( "payment_status" )=='Completed' ) {
			update_post_meta( $sub_id, 'payment_standard_status', 'Paid' );
		}


	}


	private static function verify_paypal_ipn() {

		//read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		foreach ( $_POST as $key => $value ) {
			$value = urlencode( stripslashes( $value ) );
			$req .= "&$key=$value";
		}
		$url = self::post( "test_ipn" ) ? self::$sandbox_url : self::$production_url;

		//Post back to PayPal system to validate
		$request = new WP_Http();
		$response = $request->post( $url, array( "sslverify" => false, "ssl" => true, "body" => $req, "timeout"=>20 ) );

		return !is_wp_error( $response ) && $response["body"] == "VERIFIED";
	}

	public static function get( $name, $array=null ) {
		if ( !$array )
			$array = $_GET;

		if ( isset( $array[$name] ) )
			return $array[$name];

		return "";
	}


	public static function post( $name ) {
		if ( isset( $_POST[$name] ) )
			return $_POST[$name];

		return "";
	}

	/*
	 * Check if Ninja form is  installed
	 */
	private static function is_ninjaform_installed() {
		return defined( 'NINJA_FORMS_VERSION' );
	}


}
