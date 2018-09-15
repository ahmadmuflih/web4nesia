<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Big_Tree_Island_NFNO_Mailchimp {

	/**
	 * Constuctor for the class.
	 *
	 * @since Version 1.0.0
	 */
	function __construct() {

		// Include MailChimp class on demand
		if ( ! class_exists( 'MailChimp' ) ) {
			include_once BTI_NFNO_PLUGIN_BASE . "/providers/mailchimp/MailChimp.php";
		}

		// Ajax
		add_action( 'wp_ajax_nfno_mailchimp_list', array( $this, 'mailchimp_list_ajax' ) );
		add_action( 'wp_ajax_nfno_mailchimp_key_check', array( $this, 'mailchimp_key_check_ajax' ) );
		add_action( 'wp_ajax_nfno_add_mailchimo_field', array( $this, 'mailchimp_add_field_ajax' ) );

		// Action hooks
		add_action( 'init', array( $this, 'ninja_forms_register_field_optin_mailchimp' ), 50 );
		add_action( 'ninja_forms_post_process', array( $this, 'ninja_forms_field_optin_mailchimp_process' ), 20 );
		add_action( 'admin_head', array( $this, 'admin_custom_css' ) );
	}

	/**
	 * Adds custom css styling to WordPress backend
	 *
	 * @since Version 1.1.0
	 */
	function admin_custom_css() {
		echo '
			<style type="text/css">
				.mailchimp-field-list-options { width: 100%; background: #fcfcfc; border-bottom: 1px solid #ddd; padding: 2px 10px; }
				.mailchimp-field-list-options td {width: 49.5%;}
				.mailchimp-field-list-options a .dashicons { float: none; text-decoration: none; color: #444; padding: 5px 0px 0px 0px; }
				.mailchimp-field-list-options a:hover span { color: gray; }
			</style>';
	}

	/**
	 * Registers new form element 'MailChimp' and field settings.
	 *
	 * @since Version 1.0.0
	 */
	function ninja_forms_register_field_optin_mailchimp() {
		$args = array(
			'name'             => __( 'MailChimp', BTI_NFNO_TRANS ),
			'edit_function'    => array( $this, 'field_optin_mailchimp_list' ),
			'display_function' => array( $this, 'ninja_forms_field_optin_mailchimp_display' ),
			'group'            => 'standard_fields',
			'edit_label'       => true,
			'edit_label_pos'   => true,
			'edit_help'        => true,
			'edit_meta'        => false,
			'sidebar'          => 'template_fields',
			'display_label'    => true,
			'edit_conditional' => false,
			'edit_options' => array(
			)
		);

		if( function_exists( 'ninja_forms_register_field' ) ) {
			ninja_forms_register_field( '_optin_mailchimp', $args );
		}
	}

	/**
	 * Custom settings implementation to provider better UX.
	 *
	 * @since Version 1.0.0
	 */
	function field_optin_mailchimp_list( $field_id, $data ) {

		// Store the status value if needed
		if ( ! isset( $data['optin_mailchimp_api_key_status'] ) ) {
			if ( isset( $data['optin_mailchimp_api_key'] ) && $data['optin_mailchimp_api_key'] ) {
				$mailchimp = new MailChimp( $data['optin_mailchimp_api_key'] );
				$lists = $mailchimp->call( 'lists/list' );
				$data['optin_mailchimp_api_key_status'] = $lists ? '1' : '0';
			}
		}

		// Cache campaign lists ( speeds up UI loading )
		if ( ! isset( $data['optin_mailchimp_list_cache'] ) ) {
			if ( isset( $data['optin_mailchimp_api_key'] ) && $data['optin_mailchimp_api_key'] ) {
				$mailchimp = new MailChimp( $data['optin_mailchimp_api_key'] );
				$lists = $mailchimp->call('lists/list');

				$tmp = '';
				if ( is_array( $lists ) && isset( $lists['total'] ) && 0 < $lists['total'] ) {
					foreach ( $lists['data'] as $list ) {
						$tmp .= $list['id'] . '|' . $list['name'] . '||';
					}
				}

				$data['optin_mailchimp_list_cache'] = $tmp;
			}
		}

		?>
		<!-- API Key -->
		<?php $api_key_status  = isset( $data['optin_mailchimp_api_key_status'] ) ? $data['optin_mailchimp_api_key_status'] : '0'; ?>
		<?php $status_base_url = BTI_NFNO_PLUGIN_URL . '/images/'; ?>
		<div class="description description-wide text" id="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_api_key]_p">
			<span class="field-option">
				<label for="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_api_key" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_api_key_label"><?php _e( 'MailChimp API Key', BTI_NFNO_TRANS ); ?></label><br>
				<div style="position: relative;">
					<input type="text" class="code ninja-forms-_optin_mailchimp-optin_mailchimp_api_key widefat" name="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_api_key]" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_api_key" value="<?php echo ( isset( $data['optin_mailchimp_api_key'] ) ) ? $data['optin_mailchimp_api_key'] : ''; ?>"/>
					<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_api_key_status]" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_api_key_status" value="<?php echo $api_key_status; ?>" />
					<img src="<?php echo $status_base_url; ?><?php echo ( $api_key_status ) ? 'status-ok.png' : 'status-err.png'; ?>" data-base-url="<?php echo $status_base_url; ?>" alt="" style="position: absolute; right: 5px; top: 7px;" />
				</div>
			</span>
		</div>

		<!-- List select -->
		<div class="description description-wide select" id="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_list]_p">
			<span class="field-option">
				<label for="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_list" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_list_label"><?php _e( 'Choose a list', BTI_NFNO_TRANS ); ?></label><br>
				<select id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_list" name="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_list]" class="code ninja-forms-_optin_mailchimp-optin_mailchimp_list widefat">
					<?php
						if ( is_array( $data ) && isset( $data['optin_mailchimp_api_key'] ) && $data['optin_mailchimp_api_key'] ) {
							$lists = explode( '||', $data['optin_mailchimp_list_cache'] );
							if ( is_array( $lists ) && count( $lists ) > 0 ) {
								foreach ( $lists as $list ) {
									if ( $list ) {
										list( $tmp_id, $tmp_name ) = explode( '|', $list );
										$selected = ( isset( $data['optin_mailchimp_list'] ) && $data['optin_mailchimp_list'] == $tmp_id ) ? 'selected' : '';
										printf( '<option value="%s" %s>%s</option>', $tmp_id, $selected, $tmp_name );
									}
								}
							}
						}
					?>
				</select>
				<div style="text-align: right; padding: 5px 5px 0px 0px;">
					<a href="#" class="optin_mailchimp_list_reload"><?php _e( 'Reload List', BTI_NFNO_TRANS ); ?></a>
				</div>
				<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_list_cache]" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_list_cache" value="<?php echo isset( $data['optin_mailchimp_list_cache'] ) ? $data['optin_mailchimp_list_cache'] : ''; ?>" class="ninja-forms-_optin_mailchimp-optin_mailchimp_list_cache"/>
			</span>
		</div>

		<!-- Display -->
		<?php $display = ( isset( $data['optin_mailchimp_display'] ) ) ? $data['optin_mailchimp_display'] : ''; ?>
		<div class="description description-wide select" id="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_display]_p">
			<span class="field-option">
				<label for="ninja_forms_field_<?php echo $field_id; ?>_optin_mailchimp_display" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_display_label"><?php _e( 'Display', BTI_NFNO_TRANS ); ?></label><br>
				<select id="ninja_forms_field_<?php echo $field_id; ?>_optin_mailchimp_display" name="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_display]" class="code ninja-forms-_optin_mailchimp-optin_mailchimp_display widefat">
					<option value="none" <?php echo ( 'none' == $display) ? 'selected="selected"' : ''; ?>><?php _e( 'None', BTI_NFNO_TRANS ); ?></option>
					<option value="checkbox" <?php echo ( 'checkbox' == $display ) ? 'selected="selected"' : ''; ?>><?php _e( 'Checkbox', BTI_NFNO_TRANS ); ?></option>
					<option value="dropdown" <?php echo ( 'dropdown' == $display ) ? 'selected="selected"' : ''; ?>><?php _e( 'Dropdown', BTI_NFNO_TRANS ); ?></option>
				</select>
			</span>
		</div>

		<!-- Checkbox Text -->
		<div class="description description-widefat text container-optin_mailchimp_checkbox_text" id="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_checkbox_text]_p" <?php echo ( 'checkbox' != $display ) ? 'style="display:none;"' : ''; ?>>
			<span class="field-option">
				<label for="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_checkbox_text" id="ninja_forms_field_<?php echo $field_id; ?>_optin_mailchimp_checkbox_text_label"><?php _e( 'Checkbox Text', BTI_NFNO_TRANS ); ?></label><br>
				<input type="text" class="code ninja-forms-_optin_mailchimp-optin_mailchimp_checkbox_text widefat" name="ninja_forms_field_<?php echo $field_id;?>[optin_mailchimp_checkbox_text]" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_checkbox_text" value="<?php echo ( isset( $data['optin_mailchimp_checkbox_text'] ) ) ? $data['optin_mailchimp_checkbox_text'] : ''; ?>" />
			</span>
		</div>

		<!-- Checkbox State -->
		<?php $checkbox_state = ( isset( $data['optin_mailchimp_checkbox_state'] ) && $data['optin_mailchimp_checkbox_state'] ) ? absint( $data['optin_mailchimp_checkbox_state'] ) : 0; ?>
		<div class="description description-widefat text container-optin_mailchimp_checkbox_state" id="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_checkbox_state]_p" <?php echo ( 'checkbox' != $display ) ? 'style="display:none;"' : ''; ?>>
			<span class="field-option">
				<label for="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_checkbox_state" id="ninja_forms_field_<?php echo $field_id; ?>_optin_mailchimp_checkbox_state_label"><?php _e( 'Checkbox State', BTI_NFNO_TRANS ); ?></label><br>
				<select name="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_checkbox_state]" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_checkbox_state" class="code widefat">
					<option value="1" <?php echo ( 1 == $checkbox_state ) ? 'selected' : ''; ?>><?php _e( 'Enabled', BTI_NFNO_TRANS ); ?></option>
					<option value="0" <?php echo ( 0 == $checkbox_state ) ? 'selected' : ''; ?>><?php _e( 'Disabled', BTI_NFNO_TRANS ); ?></option>
				</select>
			</span>
		</div>

		<!-- Double Optin Select box -->
		<?php $double_optin_val = ( isset( $data['optin_mailchimp_double_optin'] ) ) ? $data['optin_mailchimp_double_optin'] : 1; ?>
		<div class="description description-widefat checkbox" id="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_double_optin]_p">
			<span class="field-option">
				<label for="ninja_forms_field_<?php echo $field_id; ?>_optin_mailchimp_double_optin" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_double_optin_label"><?php _e( 'Double Opt-in', BTI_NFNO_TRANS ); ?></label><br>
				<select name="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_double_optin]" id="ninja_forms_field_<?php echo $field_id;?>_optin_mailchimp_double_optin" class="code widefat">
					<option value="1" <?php echo ( 1 == $double_optin_val ) ? 'selected' : ''; ?>><?php _e( 'Enabled', BTI_NFNO_TRANS ); ?></option>
					<option value="0" <?php echo ( 0 == $double_optin_val ) ? 'selected' : ''; ?>><?php _e( 'Disabled', BTI_NFNO_TRANS ); ?></option>
				</select>
			</span>
		</div>

		<!-- Merge Tags Settings -->
		<div class="nf-field-settings description-wide description">
			<div class="title">
				<?php _e( 'MailChimp Merge Tags Settings (Optional)', BTI_NFNO_TRANS ); ?><span class="dashicons nf-field-sub-section-toggle dashicons-arrow-down"></span>
			</div>
			<div class="inside" style="display: none;">
				<p>
					<?php _e('<b>Note:</b> Ninja Forms MailChimp Opt-ins extension automatically maps and submits email, first name and last name when subscribing a new user. Please, refer to documentaton for more details how to use this settings section.', BTI_NFNO_TRANS ); ?>
				</p>
				<a href="#" class="ninja-forms-field-add-mailchimp-field button-secondary" style="margin: 5px 0px 10px 0px;"><?php _e( 'Add New', BTI_NFNO_TRANS ); ?></a>
				<div class="mailchimp-field-container">
					<?php
						$map = isset( $data['optin_mailchimp_map'] ) ? $data['optin_mailchimp_map'] : array();

						// Email Address
						$field_id_email_val = ( isset( $map[0] ) && $map[0] ) ? $map[0] : '';
						echo $this->render_mailchimp_field( __( 'Email Address', BTI_NFNO_TRANS ), $field_id, $field_id_email_val, 0 );

						// Fist Name
						$field_id_first_name_val = ( isset( $map[1] ) && $map[1] ) ? $map[1] : '';
						echo $this->render_mailchimp_field( __( 'First Name', BTI_NFNO_TRANS ), $field_id, $field_id_first_name_val, 1 );

						// Last Name
						$field_id_last_name_val = ( isset( $map[2] ) && $map[2] ) ? $map[2] : '';
						echo $this->render_mailchimp_field( __( 'Last Name', BTI_NFNO_TRANS ), $field_id, $field_id_last_name_val, 2 );

						// Custom fields
						if ( isset( $data['optin_mailchimp_map_name'] ) && 0 < count( $data['optin_mailchimp_map_name'] ) ) {
							$tmp_seq = 3;
							foreach ( $data['optin_mailchimp_map_name'] as $key => $value ) {
								$tmp_val = ( isset( $map[$tmp_seq] ) && $map[$tmp_seq] ) ? $map[$tmp_seq] : '';
								echo $this->render_mailchimp_field( $value, $field_id, $tmp_val, $tmp_seq );

								$tmp_seq++;
							}
						}
					?>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Renders MailChimp fields for the backend
	 *
	 * @since Version 1.1.0
	 */
	function render_mailchimp_field( $title, $field_id, $value, $seq = 0) {
		ob_start();
		?>

		<table class="mailchimp-field-list-options">
			<tbody>
				<tr>
					<td>

						<!-- Delete Button -->
						<?php if ( $seq > 2 ) : ?>
							<a href="#" class="mailchimp-field-list-remove"><span class="dashicons dashicons-dismiss"></span></a>
						<?php else : ?>
							<span>&nbsp;</span>
						<?php endif; ?>

						<!-- MailChimp Field ID -->
						<?php if ( $seq > 2 ) : ?>
							<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_map_name][<?php echo ( $seq - 3 ); ?>]" id="ninja_forms_field_<?php echo $field_id; ?>_optin_mailchimp_map_name" value="<?php echo $title; ?>"/>
						<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>

					</td>
					<td>
						<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>[optin_mailchimp_map][<?php echo $seq; ?>]" id="ninja_forms_field_<?php echo $field_id; ?>_optin_mailchimp_map" value="<?php echo $value; ?>"/>
						<?php _e( '(Field ID)', BTI_NFNO_TRANS ); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<?php
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Renders MailChimp field on the front-end
	 *
	 * @since Version 1.0.0
	 */
	function ninja_forms_field_optin_mailchimp_display( $field_id, $data ) {
		$field_class = ninja_forms_get_field_class( $field_id );
		$default_value = isset( $data['default_value'] ) ? $data['default_value'] : '';

		if ( isset( $data['optin_mailchimp_api_key'] )
			&& isset( $data['optin_mailchimp_display'] )
			&& in_array( $data['optin_mailchimp_display'], array( 'checkbox', 'dropdown' ) ) ) {

			// Checkbox
			if ( $data['optin_mailchimp_display'] == 'checkbox' ) {
				$checkbox_state = ( isset( $data['optin_mailchimp_checkbox_state'] ) && $data['optin_mailchimp_checkbox_state'] ) ? 1 : 0;
				?>

				<input type="checkbox" name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>" value="1" class="<?php echo $data['field_class']; ?> <?php echo $data['class']; ?>" <?php echo checked( $checkbox_state ); ?>/>
				<?php if ( isset( $data['optin_mailchimp_checkbox_text'] ) ) : ?>
					<span><?php echo $data['optin_mailchimp_checkbox_text']; ?></span>
				<?php endif; ?>

				<?php
			}

			// dropdown
			if ( $data['optin_mailchimp_display'] == 'dropdown' ) {
				?>
				<select name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $data['field_class']; ?> <?php echo $data['class']; ?>">
					<?php
						if ( is_array( $data ) && isset( $data['optin_mailchimp_list_cache'] ) && $data['optin_mailchimp_list_cache'] ) {
							$lists = explode( '||', $data['optin_mailchimp_list_cache'] );
							if ( is_array( $lists ) && count( $lists ) > 0 ) {
								foreach ( $lists as $list ) {
									if ( $list ) {
										list( $tmp_id, $tmp_name ) = explode( '|', $list );
										$selected = ( isset( $data['optin_mailchimp_list'] ) && $data['optin_mailchimp_list'] == $tmp_id ) ? 'selected' : '';
										printf( '<option value="%s" %s>%s</option>', $tmp_id, $selected, $tmp_name );
									}
								}
							}
						}
					?>
				</select>
				<?php
			}
		}
	}

	/**
	 * Handles form submission (front-end) and form processing
	 * for MailChimp fields.
	 *
	 * @since Version 1.0.0
	 */
	function ninja_forms_field_optin_mailchimp_process() {
		global $ninja_forms_processing;

		// Raw data
		$all_fields = $ninja_forms_processing->get_all_fields();
		$details    = $ninja_forms_processing->get_user_info();

		// Index all the MailChimp fields
		$mailchimp_process = false;
		$mailchimp_total   = 0;
		$mailchimp_queue   = array();

		$mailchimp_mapping_default = array(
			0 => 'EMAIL',
			1 => 'FNAME',
			2 => 'LNAME'
		);

		$tmp = (array) $ninja_forms_processing;
		if ( isset( $tmp['data'] ) && isset( $tmp['data']['field_data'] ) ) {
			foreach( $tmp['data']['field_data'] as $field_id => $field_data ) {
				if ( is_array( $field_data ) && isset( $field_data['type'] ) && '_optin_mailchimp' == $field_data['type'] ) {

					// MailChimp manual field mapping options
					$mailchimp_mapping = array();
					if ( isset( $field_data['data']['optin_mailchimp_map'] ) && 0 < count( $field_data['data']['optin_mailchimp_map'] ) ) {
						for( $i=0; $i<count( $field_data['data']['optin_mailchimp_map'] ); $i++ ) {
							if ( $i > 2 ) {
								if ( isset( $field_data['data']['optin_mailchimp_map_name'][( $i-3 )] ) && isset( $all_fields[$field_data['data']['optin_mailchimp_map'][$i]] ) ) {
									$tmp_mapping_name = preg_replace('/[^A-Za-z_?!]/', '', strtoupper( $field_data['data']['optin_mailchimp_map_name'][( $i-3 )] ) );
									$mailchimp_mapping[$tmp_mapping_name] = $all_fields[$field_data['data']['optin_mailchimp_map'][$i]];
								}
							} else {
								if ( isset( $field_data['data']['optin_mailchimp_map'][$i] ) && isset( $all_fields[$field_data['data']['optin_mailchimp_map'][$i]] ) ) {
									$tmp_mapping_name = $mailchimp_mapping_default[$i];
									$mailchimp_mapping[$tmp_mapping_name] = $all_fields[$field_data['data']['optin_mailchimp_map'][$i]];
								}
							}
						}
					}

					// Display type 'none' fields are automatically enqueued
					if ( isset( $field_data['data']['optin_mailchimp_display'] ) && 'none' == $field_data['data']['optin_mailchimp_display'] ) {
						$mailchimp_queue[] = array(
							'api_key'      => isset( $field_data['data']['optin_mailchimp_api_key'] ) ? $field_data['data']['optin_mailchimp_api_key'] : '',
							'list_id'      => isset( $field_data['data']['optin_mailchimp_list'] ) ? $field_data['data']['optin_mailchimp_list'] : '',
							'double_optin' => isset( $field_data['data']['optin_mailchimp_double_optin'] ) ? $field_data['data']['optin_mailchimp_double_optin'] : 0,
							'mapping'      => $mailchimp_mapping
						);

						$mailchimp_total++;
					}

					// Display type 'checkbox' has to be cross referenced
					if ( isset( $field_data['data']['optin_mailchimp_display'] ) && 'checkbox' == $field_data['data']['optin_mailchimp_display'] ) {
						$mailchimp_process = false;
						if ( is_array( $all_fields ) && isset( $all_fields[$field_id] ) && $all_fields[$field_id] == '1' ) {
							$mailchimp_queue[] = array(
								'api_key'      => isset( $field_data['data']['optin_mailchimp_api_key'] ) ? $field_data['data']['optin_mailchimp_api_key'] : '',
								'list_id'      => isset( $field_data['data']['optin_mailchimp_list'] ) ? $field_data['data']['optin_mailchimp_list'] : '',
								'double_optin' => isset( $field_data['data']['optin_mailchimp_double_optin'] ) ? $field_data['data']['optin_mailchimp_double_optin'] : 0,
								'mapping'      => $mailchimp_mapping
							);

							$mailchimp_process = true;
						}

						$mailchimp_total++;
					}

					// Display type 'dropdown',
					if ( isset( $field_data['data']['optin_mailchimp_display'] ) && 'dropdown' == $field_data['data']['optin_mailchimp_display'] ) {
						if ( is_array( $all_fields ) && isset( $all_fields[$field_id] ) && $all_fields[$field_id] ) {
							$mailchimp_queue[] = array(
								'api_key'      => isset( $field_data['data']['optin_mailchimp_api_key'] ) ? $field_data['data']['optin_mailchimp_api_key'] : '',
								'list_id'      => $all_fields[$field_id],
								'double_optin' => isset( $field_data['data']['optin_mailchimp_double_optin'] ) ? $field_data['data']['optin_mailchimp_double_optin'] : 0,
								'mapping'      => $mailchimp_mapping
							);
						}

						$mailchimp_total++;
					}

				}
			}
		}

		/**
		 *Fill merge_vars with MailChimp ready data.
		 */
		$merge_vars = array();

		// First name
		if ( isset( $details['first_name'] ) && 0 < strlen( $details['first_name'] ) ) {
			$merge_vars['FNAME'] = $details['first_name'];
		}

		// Last name
		if ( isset( $details['last_name'] ) && 0 < strlen( $details['last_name'] ) ) {
			$merge_vars['LNAME'] = $details['last_name'];
		}

		//Process the subscriptions
		$queue_cnt = count( $mailchimp_queue );
		if ( true == $mailchimp_process || ( 0 < $queue_cnt && $queue_cnt == $mailchimp_total ) ) {
			foreach ( $mailchimp_queue as $item ) {
				// Subscribe if api_key and list_id is set
				if ( $item['api_key'] && $item['list_id'] ) {
					// Double optin option
					$double_optin = ( isset( $item['double_optin'] ) && 1 == $item['double_optin'] ) ? true : false;

					// Email
					$email = $details['email'];
					if ( isset( $item['mapping']['EMAIL'] ) ) {
						$email = $item['mapping']['EMAIL'];
						unset( $item['mapping']['EMAIL'] );
					}

					// Meta details
					$merge_vars = array_merge( $merge_vars, $item['mapping'] );

					$this->subscribe( $item['api_key'], $item['list_id'], $email, $merge_vars, $double_optin );
				}
			}
		}
	}

	/**
	 * Subscribers user to mailing list through MailChimp API
	 *
	 * @since Version 1.0.0
	 */
	function subscribe( $api_key, $list_id, $email, $fields, $double_optin = false ) {
		$mailchimp = new MailChimp( $api_key );
		$result = $mailchimp->call( 'lists/subscribe', array(
			'id'                => $list_id,
			'email'             => array( 'email' => $email ),
			'merge_vars'        => $fields,
			'double_optin'      => $double_optin,
			'update_existing'   => true,
			'replace_interests' => false,
			'send_welcome'      => false,
		));
	}

	/**
	 * Retrieves the lists using MailChimp API
	 *
	 * @since Version 1.0.0
	 */
	function mailchimp_list_ajax() {
		$api_key         = isset( $_GET['api_key'] ) ? $_GET['api_key'] : '';
		$parent_selector = isset( $_GET['parent_selector'] ) ? $_GET['parent_selector'] : '';

		if ( $api_key ) {
			$mailchimp = new MailChimp( $api_key );
			$lists = $mailchimp->call( 'lists/list' );

			$tmp = '';
			$result = array();
			if ( is_array( $lists ) && isset( $lists['total'] ) && 0 < $lists['total'] ) {
				foreach ( $lists['data'] as $list ) {
					$result[] = array( 'id' => $list['id'], 'name' => $list['name'] );
					$tmp .= $list['id'] . '|' . $list['name'] . '||';
				}
			}

			echo json_encode(
				array(
					'success' => true,
					'lists' => $result,
					'cache' => $tmp,
					'parent_selector' => $parent_selector
				)
			);
			exit();
		}

		echo json_encode(
			array(
				'success' => false,
				'lists' => array(),
				'parent_selector' => $parent_selector
			)
		);

		exit();
	}

	/**
	 * Validates the API key
	 *
	 * @since Version 1.0.0
	 */
	function mailchimp_key_check_ajax() {
		$api_key         = isset( $_GET['api_key'] ) ? $_GET['api_key'] : '';
		$parent_selector = isset( $_GET['parent_selector'] ) ? $_GET['parent_selector'] : '';

		if ( $api_key ) {
			$mailchimp = new MailChimp( $api_key );
			$lists = $mailchimp->call( 'lists/list' );

			if ( $lists ) {
				echo json_encode(
					array(
						'success' => true,
						'parent_selector' => $parent_selector
					)
				);

				exit();
			}
		}

		echo json_encode(
			array(
				'success' => false,
				'parent_selector' => $parent_selector
			)
		);

		exit();
	}

	/**
	 * Generates a new custom field for MailChimp Field Mapping
	 *
	 * @since Version 1.1.0
	 */
	function mailchimp_add_field_ajax() {
		$parent_selector = isset( $_GET['parent_selector'] ) ? $_GET['parent_selector'] : false;

		if ( $parent_selector ) {
			$field_id     = isset( $_GET['field_id'] ) ? $_GET['field_id'] : false;
			$total_fields = isset( $_GET['fields'] ) ? $_GET['fields'] : 2;

			echo json_encode( array(
				'success' => true,
				'parent_selector' => $parent_selector,
				'html' => $this->render_mailchimp_field( '', $field_id, '', $total_fields++ )
			) );

			exit();
		}

		echo json_encode( array(
			'success' => true,
			'parent_selector' => '',
			'html' => ''
		) );

		exit();
	}
}
