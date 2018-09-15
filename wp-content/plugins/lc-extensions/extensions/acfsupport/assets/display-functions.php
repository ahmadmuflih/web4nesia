<?php
/**
 * All the core functions
 *
 * @package Live Composer - ACF integration
 */

/**
 * Display default data for templates.
 *
 * @param  array $array options.
 */
function lcacf_display_default_data( $array ) {

	switch ( $array['module_id'] ) {
		case 'ACF_Text':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				$content = '<h1>This Is An Example Of A Heading 1</h1>
				<h2>This Is An Example Of A Heading 2</h2>
				<h3>This Is An Example Of A Heading 3</h3>
				<h4>This Is An Example Of A Heading 4</h4>
				<h5>This Is An Example Of A Heading 5</h5>
				<h6>This Is An Example Of A Heading 6</h6>
				<p>This is a paragraph. <a href="#">Consectetur adipisicing elit</a>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';
				echo $content;
			}

			break;
		case 'ACF_Textarea':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				$content = '<h1>This Is An Example Of A Heading 1</h1>
				<h2>This Is An Example Of A Heading 2</h2>
				<h3>This Is An Example Of A Heading 3</h3>
				<h4>This Is An Example Of A Heading 4</h4>
				<h5>This Is An Example Of A Heading 5</h5>
				<h6>This Is An Example Of A Heading 6</h6>
				<p>This is a paragraph. <a href="#">Consectetur adipisicing elit</a>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<ul>
				<li>Unordered List item</li>
				<li>Unordered List item</li>
				<li>Unordered List item</li>
				<li>Unordered List item</li>
				</ul>
				<ol>
				<li>Ordered List item</li>
				<li>Ordered List item</li>
				<li>Ordered List item</li>
				<li>Ordered List item</li>
				</ol>
				<blockquote>This is a blockquote. <a href="#">Consectetur adipisicing elit</a>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</blockquote>
				<input size="40" type="text" />
				<input type="submit" value="get" />';
				echo $content;
			}

			break;
		case 'ACF_Wysiwyg':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				$content = '<h1>This Is An Example Of A Heading 1</h1>
				<h2>This Is An Example Of A Heading 2</h2>
				<h3>This Is An Example Of A Heading 3</h3>
				<h4>This Is An Example Of A Heading 4</h4>
				<h5>This Is An Example Of A Heading 5</h5>
				<h6>This Is An Example Of A Heading 6</h6>
				<p>This is a paragraph. <a href="#">Consectetur adipisicing elit</a>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<ul>
				<li>Unordered List item</li>
				<li>Unordered List item</li>
				<li>Unordered List item</li>
				<li>Unordered List item</li>
				</ul>
				<ol>
				<li>Ordered List item</li>
				<li>Ordered List item</li>
				<li>Ordered List item</li>
				<li>Ordered List item</li>
				</ol>
				<blockquote>This is a blockquote. <a href="#">Consectetur adipisicing elit</a>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</blockquote>
				<input size="40" type="text" />
				<input type="submit" value="get" />';
				echo $content;
			}

			break;
		case 'ACF_Number':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				echo __( '10', 'lc-acf-integration' );
			}

			break;
		case 'ACF_Page_Link':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				if ( ! empty( $array['label'] ) ) {
					$label = $array['label'];
				} else {
					$label = 'Link';
				}

				if ( 'link' === $array['display'] ) {
					$output = '<a href="#">' . $label . '</a>';
					echo $output;
				} elseif ( 'button' === $array['display'] ) { ?>
					<div class="lcacf-button">
						<a href="#">
							<?php if ( 'enabled' == $array['button_state'] && 'left' == $array['icon_pos'] ) : ?>
								<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
							<?php endif; ?>
							<?php echo stripslashes( $label ); ?>
							<?php if ( 'enabled' == $array['button_state'] && 'right' == $array['icon_pos'] ) : ?>
								<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
							<?php endif; ?>
						</a>
					</div>
				<?php
				}
			}

			break;
		case 'ACF_Date_Picker':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				echo __( 'dd/mm/YYYY', 'lc-acf-integration' );
			}

			break;
		case 'ACF_Image':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<img src="<?php echo LC_ACFINTEGRATION_PLUGIN_URL; ?>assets/images/placeholder.png" />
			<?php
			}

			break;
		case 'ACF_Checkbox':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				if ( 'single_line' === $array['display'] ) {
					echo '<span class="label">Checkbox:</span> one, two, three.';
				} else {
					?>
					<span class="label">Checkbox:</span>
					<ul>
						<li>one</li>
						<li>two</li>
						<li>three</li>
					</ul>
				<?php
				}
			}

			break;
		case 'ACF_Select':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				if ( 'single_line' === $array['display'] ) {
					echo '<span class="label">Select:</span> one, two, three.';
				} else {
					?>
					<span class="label">Select:</span>
					<ul>
						<li>one</li>
						<li>two</li>
						<li>three</li>
					</ul>
				<?php
				}
			}

			break;
		case 'ACF_File':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<a href="#">
					<?php if ( isset( $array['button_icon_id'] ) && '' !== $array['button_icon_id'] ) : ?>
						<span class="dslc-icon dslc-icon-<?php echo esc_attr( $array['button_icon_id'] ); ?>"></span>
					<?php endif; ?>
					<?php if ( $array['dslc_is_admin'] ) : ?>
						<span class="dslca-editable-content" data-id="button_text" data-type="simple" <?php if ( $array['dslc_is_admin'] ) { echo 'contenteditable'; } ?>><?php echo esc_html( $array['button_text'] ); ?></span>
					<?php else : ?>
						<span><?php echo esc_html( $array['button_text'] ); ?></span>
					<?php endif; ?>
				</a>
			<?php
			}

			break;
		case 'ACF_Taxonomy':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<ul>
					<li><a href="#">one</a></li>
					<li><a href="#">two</a></li>
					<li><a href="#">three</a></li>
					<li><a href="#">four</a></li>
				</ul>
			<?php
			}

			break;
		case 'ACF_Link':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				if ( ! empty( $array['label'] ) ) {
					$label = $array['label'];
				} else {
					$label = 'Link';
				}

				if ( 'link' === $array['display'] ) {
					$output = '<a href="#">' . $label . '</a>';
					echo $output;
				} elseif ( 'button' === $array['display'] ) {
					?>
					<div class="lcacf-button">
						<a href="#">
							<?php if ( 'enabled' == $array['button_state'] && 'left' == $array['icon_pos'] ) : ?>
								<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
							<?php endif; ?>
							<?php echo stripslashes( $label ); ?>
							<?php if ( 'enabled' == $array['button_state'] && 'right' == $array['icon_pos'] ) : ?>
								<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
							<?php endif; ?>
						</a>
					</div>
				<?php
				}
			}

			break;
		case 'ACF_Email':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['label'] ) ) {
					$label = $array['label'];
				} else {
					$label = 'test@test.com';
				}

				echo '<a href="mailto:#">' . $label . '</a>';
			}

			break;
		case 'ACF_URL':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['label'] ) ) {
					$label = $array['label'];
				} else {
					$label = 'http://test.com';
				}

				if ( 'link' == $array['display'] ) {
					echo '<a href="#">' . $label . '</a>';
				} else {
					echo 'http://test.com';
				}
			}

			break;
		case 'ACF_oEmbed':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<img src="<?php echo LC_ACFINTEGRATION_PLUGIN_URL; ?>assets/images/big-placeholder.png" />
			<?php
			}

			break;
		case 'ACF_Gallery':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<div class="dslc-slider"  data-animation="<?php echo $array['animation']; ?>" data-animation-speed="<?php echo $array['animation_speed']; ?>" data-autoplay="<?php echo $array['autoplay']; ?>" data-flexible-height="<?php echo $array['flexible_height']; ?>">
					<?php
					for ( $i = 0; $i < 15; $i++ ) {
						?>
						<div class="dslc-slider-item"><img src="<?php echo LC_ACFINTEGRATION_PLUGIN_URL; ?>assets/images/big-placeholder.png" /></div>
						<?php
					}
					?>
				</div><!-- .dslc-slider -->
			<?php
			}

			break;
		case 'ACF_Radio_Button':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				echo '<span class="label">Label:</span> Value';
			}

			break;
		case 'ACF_Date_Time_Picker':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				echo __( 'dd/mm/YYYY 00:00 am', 'lc-acf-integration' );
			}

			break;
		case 'ACF_Time_Picker':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				echo __( '00:00 am', 'lc-acf-integration' );
			}

			break;
		case 'ACF_Button_Group':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				echo '<span class="label">Label:</span> Value';
			}

			break;
		case 'ACF_Post_Object':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<ul>
					<li><a href="#">one</a></li>
					<li><a href="#">two</a></li>
					<li><a href="#">three</a></li>
					<li><a href="#">four</a></li>
				</ul>
			<?php
			}

			break;
		case 'ACF_Relationship':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<ul>
					<li><a href="#">one</a></li>
					<li><a href="#">two</a></li>
					<li><a href="#">three</a></li>
					<li><a href="#">four</a></li>
				</ul>
			<?php
			}

			break;
		case 'ACF_User':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				?>
				<ul>
					<li><a href="#">Author One</a></li>
					<li><a href="#">Author Two</a></li>
					<li><a href="#">Author Three</a></li>
				</ul>
			<?php
			}

			break;
		case 'ACF_Google_Map':

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {
				$google_api = $array['google_api'];

				if ( ! empty( $google_api ) ) {
				?>
					<img src="<?php echo LC_ACFINTEGRATION_PLUGIN_URL; ?>assets/images/google-map.png" />
				<?php
				} else {
					echo lcacf_display_notice( 'google-map-api' );
				}
			}
			break;
		default:
			break;
	}
}

/**
 * Display real data for templates.
 *
 * @param  array $array options.
 */
function lcacf_display_real_data( $array ) {

	switch ( $array['module_id'] ) {
		case 'ACF_Text':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_text = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_text = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_text['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( ! empty( $arr_text['prepend'] ) && ! empty( $arr_text['append'] ) ) {
						echo '<span class="prepend">' . $arr_text['prepend'] . '</span> ' . $arr_text['value'] . ' <span class="append">' . $arr_text['append'] . '</span> ';
					} elseif ( ! empty( $arr_text['prepend'] ) ) {
						echo '<span class="prepend">' . $arr_text['prepend'] . '</span> ' . $arr_text['value'];
					} elseif ( ! empty( $arr_text['append'] ) ) {
						echo $arr_text['value'] . ' <span class="append">' . $arr_text['append'] . '</span> ';
					} else {
						echo $arr_text['value'];
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Textarea':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_textarea = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_textarea = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_textarea['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					echo $arr_textarea['value'];
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Wysiwyg':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_wysiwyg = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_wysiwyg = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_wysiwyg['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					echo $arr_wysiwyg['value'];
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Number':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_number = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_number = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_number['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( ! empty( $arr_number['prepend'] ) && ! empty( $arr_number['append'] ) ) {
						echo '<span class="prepend">' . $arr_number['prepend'] . '</span> ' . $arr_number['value'] . ' <span class="append">' . $arr_number['append'] . '</span> ';
					} elseif ( ! empty( $arr_number['prepend'] ) ) {
						echo '<span class="prepend">' . $arr_number['prepend'] . '</span> ' . $arr_number['value'];
					} elseif ( ! empty( $arr_number['append'] ) ) {
						echo $arr_number['value'] . ' <span class="append">' . $arr_number['append'] . '</span> ';
					} else {
						echo $arr_number['value'];
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Page_Link':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_page_link = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_page_link = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_page_link['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( ! empty( $array['label'] ) ) {
						$label = $array['label'];
					} else {
						$label = $arr_page_link['value'];
					}

					if ( 'link' === $array['display'] ) {
						$output = '<a href="' . $arr_page_link['value'] . '">' . $label . '</a>';
						echo $output;
					} elseif ( 'button' === $array['display'] ) {
						?>
						<div class="lcacf-button">
							<a href="<?php echo $arr_page_link['value']; ?>">
								<?php if ( 'enabled' == $array['button_state'] && 'left' == $array['icon_pos'] ) : ?>
									<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
								<?php endif; ?>
								<?php echo stripslashes( $label ); ?>
								<?php if ( 'enabled' == $array['button_state'] && 'right' == $array['icon_pos'] ) : ?>
									<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
								<?php endif; ?>
							</a>
						</div>
					<?php
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Date_Picker':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_date_picker = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_date_picker = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_date_picker['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					echo $arr_date_picker['value'];
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Image':

			$anchor_class = '';
			$anchor_target = '_self';
			$anchor_href = '#';

			if ( 'url_new' === $array['link_type'] ) {
				$anchor_target = '_blank';
			}

			if ( '' !== $array['link_url'] ) {
				$anchor_href = do_shortcode( $array['link_url'] );
			}

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_image = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_image = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_image['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					if ( is_array( $arr_image['value'] ) ) {

						$image_size = $arr_image['preview_size'];

						if ( 'full' === $image_size ) {
							$image_url = $arr_image['value']['url'];
						} else {
							$image_url = $arr_image['value']['sizes'][ $image_size ];
						}

						?>

						<?php if ( 'none' !== $array['link_type'] ) : ?>
							<a class="<?php echo esc_attr( $anchor_class ); ?>" href="<?php echo esc_url( $anchor_href ); ?>" target="<?php echo esc_attr( $anchor_target ); ?>">
						<?php endif; ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $arr_image['value']['alt'] ); ?>" title="<?php echo esc_attr( $arr_image['value']['title'] ); ?>" />
						<?php if ( 'none' !== $array['link_type'] ) : ?>
							</a>
						<?php endif; 

					} else {

						$acf_version = get_option( 'acf_version' );

						if ( version_compare( $acf_version, '5.0.0', '>=' ) ) {
							if ( is_string( $arr_image['value'] ) ) {
								$image_url = $arr_image['value'];
							} else {
								$image_url = wp_get_attachment_url( $arr_image['value'] );
							}
						} else {
							if ( 'url' === $arr_image['save_format'] ) {
								$image_url = $arr_image['value'];
							} else {
								$image_url = wp_get_attachment_url( intval( $arr_image['value'] ) );
							}
						}

						?>

						<?php if ( 'none' !== $array['link_type'] ) : ?>
							<a class="<?php echo esc_attr( $anchor_class ); ?>" href="<?php echo esc_url( $anchor_href ); ?>" target="<?php echo esc_attr( $anchor_target ); ?>">
						<?php endif; ?>
							<img src="<?php echo esc_url( $image_url ); ?>" />
						<?php if ( 'none' !== $array['link_type'] ) : ?>
							</a>
						<?php endif; ?>
				<?php }
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Checkbox':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_checkbox = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_checkbox = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_checkbox['value'] ) && 'single_line' === $array['display'] && lcacf_exits_field( $current_id, $current_field ) ) {

					$arr_value = $arr_checkbox['value'];
					$last_key = array_search( end( $arr_value ), $arr_value );
					$label_checkbox = $arr_checkbox['label'];
					$acf_version = get_option( 'acf_version' );

					echo '<span class="label">' . $label_checkbox . ':</span> ';

					foreach ( $arr_value as $key => $value ) {

						if ( version_compare( $acf_version, '5.0.0', '>=' ) && 'array' == $arr_checkbox['return_format'] ) {

							if ( is_array( $value ) ) {
								if ( $key === $last_key ) {
									$checkbox = $value['label'] . '.';
								} else {
									$checkbox = $value['label'] . ', ';
								}
							} else {
								if ( $key === $last_key ) {
									$checkbox = $value . '.';
								} else {
									$checkbox = $value . ', ';
								}
							}
						} else {
							if ( $key === $last_key ) {
								$checkbox = $value . '.';
							} else {
								$checkbox = $value . ', ';
							}
						}

						echo $checkbox;
					}
				} elseif ( ! empty( $arr_checkbox['value'] ) && 'list' === $array['display'] && lcacf_exits_field( $current_id, $current_field ) ) {

					$arr_value = $arr_checkbox['value'];
					$label_checkbox = $arr_checkbox['label'];
					$acf_version = get_option( 'acf_version' );

					echo '<span class="label">' . $label_checkbox . ':</span> ';
					echo '<ul>';

					foreach ( $arr_value as $value ) {
						if ( version_compare( $acf_version, '5.0.0', '>=' ) && 'array' == $arr_checkbox['return_format'] ) {
							echo '<li>' . $value['label'] . '</li>';
						} else {
							echo '<li>' . $value . '</li>';
						}
					}
					echo '</ul>';
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Select':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_select = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_select = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_select['value'] ) && 'single_line' === $array['display'] && lcacf_exits_field( $current_id, $current_field ) ) {
					$arr_value = $arr_select['value'];
					$label_select = $arr_select['label'];

					echo '<span class="label">' . $label_select . ':</span> ';

					if ( 0 === $arr_select['multiple'] && is_string( $arr_select['value'] ) ) {
						echo $arr_select['value'] . '.';
					} else {

						if ( is_string( $arr_select['value'] ) ) {
							echo $arr_select['value'] . '.';
						} else {
							$last_key = array_search( end( $arr_value ), $arr_value );
							$acf_version = get_option( 'acf_version' );

							foreach ( $arr_value as $key => $value ) {

								if ( version_compare( $acf_version, '5.0.0', '>=' ) && 'array' == $arr_select['return_format'] ) {

									if ( is_array( $value ) ) {
										if ( $key === $last_key ) {
											$select = $value['label'] . '.';
										} else {
											$select = $value['label'] . ', ';
										}
									} else {
										if ( $key === $last_key ) {
											$select = $value . '.';
										} else {
											$select = $value . ', ';
										}
									}
								} else {
									if ( $key === $last_key ) {
										$select = $value . '.';
									} else {
										$select = $value . ', ';
									}
								}

								echo $select;
							}
						}
					}
				} elseif ( ! empty( $arr_select['value'] ) && 'list' === $array['display'] && lcacf_exits_field( $current_id, $current_field ) ) {
					$arr_value = $arr_select['value'];
					$label_select = $arr_select['label'];

					echo '<span class="label">' . $label_select . ':</span> ';
					echo '<ul>';
					if ( is_string( $arr_select['value'] ) ) {
						echo '<li>' . $arr_select['value'] . '</li>';
					} else {
						$acf_version = get_option( 'acf_version' );

						foreach ( $arr_value as $value ) {

							if ( version_compare( $acf_version, '5.0.0', '>=' ) && 'array' == $arr_select['return_format'] && 1 == $arr_select['multiple'] ) {
								echo '<li>' . $value['label'] . '</li>';
							} else {
								echo '<li>' . $value . '</li>';
							}
						}
					}
					echo '</ul>';
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_File':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_file = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_file = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_file['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( 'object' === $arr_file['save_format'] ) {
						$url = $arr_file['value']['url'];
					} elseif ( 'url' === $arr_file['save_format'] ) {
						$url = $arr_file['value'];
					} else {
						$url = wp_get_attachment_url( $arr_file['value'] );
					}

					?>
						<a target="_blank" href="<?php echo $url; ?>">
							<?php if ( isset( $array['button_icon_id'] ) && '' !== $array['button_icon_id'] ) : ?>
								<span class="dslc-icon dslc-icon-<?php echo esc_attr( $array['button_icon_id'] ); ?>"></span>
							<?php endif; ?>
							<?php if ( $array['dslc_is_admin'] ) : ?>
								<span class="dslca-editable-content" data-id="button_text" data-type="simple" <?php if ( $array['dslc_is_admin'] ) { echo 'contenteditable'; } ?>><?php echo esc_html( $array['button_text'] ); ?></span>
							<?php else : ?>
								<span><?php echo esc_html( $array['button_text'] ); ?></span>
							<?php endif; ?>
						</a>
					<?php
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Taxonomy':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_taxonomy = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_taxonomy = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_taxonomy['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					$field_type = $arr_taxonomy['field_type'];

					if ( 'object' === $arr_taxonomy['return_format'] ) {

						echo '<ul>';
						if ( 'radio' === $field_type || 'select' === $field_type ) {
							echo '<li><a href="' . get_term_link( $arr_taxonomy['value']->term_id ) . '">' . $arr_taxonomy['value']->name . '</a></li>';
						} else {
							foreach ( $arr_taxonomy['value'] as $taxonomy ) {
								echo '<li><a href="' . get_term_link( $taxonomy->term_taxonomy_id ) . '">' . $taxonomy->name . '</a></li>';
							}
						}
						echo '</ul>';
					} elseif ( 'id' === $arr_taxonomy['return_format'] ) {

						echo '<ul>';
						if ( 'radio' === $field_type || 'select' === $field_type ) {
							$term = get_term( $arr_taxonomy['value'] );
							echo '<li><a href="' . get_term_link( $arr_taxonomy['value'] ) . '">' . $term->name . '</a></li>';
						} else {
							foreach ( $arr_taxonomy['value'] as $id ) {
								$term = get_term( $id );
								echo '<li><a href="' . get_term_link( $id ) . '">' . $term->name . '</a></li>';
							}
						}
						echo '</ul>';
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Link':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_link = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_link = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_link['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( ! empty( $array['label'] ) ) {
						$label = $array['label'];
					} else {
						$label = $arr_link['value'];
					}

					if ( 'link' === $array['display'] ) {

						if ( is_array( $arr_link['value'] ) ) {

							if ( ! empty( $array['label'] ) ) {
								$label = $array['label'];
							} else {
								$label = $arr_link['value']['title'];
							}

							$output = '<a href="' . $arr_link['value']['url'] . '" target="' . $arr_link['value']['target'] . '" >' . $label . '</a>';
						} else {
							$output = '<a href="' . $arr_link['value'] . '">' . $label . '</a>';
						}

						echo $output;
					} elseif ( 'button' === $array['display'] ) { 
						?>
						<div class="lcacf-button">
							<?php if ( is_array( $arr_link['value'] ) ) : ?>
								<a href="<?php echo $arr_link['value']['url']; ?>">
									<?php
									if ( ! empty( $array['label'] ) ) {
										$label = $array['label'];
									} else {
										$label = $arr_link['value']['title'];
									}
									?>
									<?php if ( 'enabled' == $array['button_state'] && 'left' == $array['icon_pos'] ) : ?>
										<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
									<?php endif; ?>
									<?php echo stripslashes( $label ); ?>
									<?php if ( 'enabled' == $array['button_state'] && 'right' == $array['icon_pos'] ) : ?>
										<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
									<?php endif; ?>
								</a>
							<?php else : ?>
								<a href="<?php echo $arr_link['value']; ?>">
									<?php if ( 'enabled' == $array['button_state'] && 'left' == $array['icon_pos'] ) : ?>
										<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
									<?php endif; ?>
									<?php echo stripslashes( $label ); ?>
									<?php if ( 'enabled' == $array['button_state'] && 'right' == $array['icon_pos'] ) : ?>
										<span class="dslc-icon dslc-icon-<?php echo $array['button_icon_id']; ?>"></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Email':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_email = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_email = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_email['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( ! empty( $array['label'] ) ) {
						$label = $array['label'];
					} else {
						$label = $arr_email['value'];
					}

					if ( ! empty( $arr_email['prepend'] ) && ! empty( $arr_email['append'] ) ) {
						echo '<span class="prepend">' . $arr_email['prepend'] . '</span> <a href="mailto:' . $arr_email['value'] . '">' . $label . '</a> <span class="append">' . $arr_email['append'] . '</span> ';
					} elseif ( ! empty( $arr_email['prepend'] ) ) {
						echo '<span class="prepend">' . $arr_email['prepend'] . '</span> <a href="mailto:' . $arr_email['value'] . '">' . $label . '</a>';
					} elseif ( ! empty( $arr_email['append'] ) ) {
						echo '<a href="mailto:' . $arr_email['value'] . '">' . $label . '</a> <span class="append">' . $arr_email['append'] . '</span> ';
					} else {
						echo '<a href="mailto:' . $arr_email['value'] . '">' . $label . '</a>';
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_URL':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_url = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_url = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_url['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( ! empty( $array['label'] ) ) {
						$label = $array['label'];
					} else {
						$label = $arr_url['value'];
					}

					if ( 'link' == $array['display'] ) {
						echo '<a href="' . $arr_url['value'] . '">' . $label . '</a>';
					} else {
						echo $arr_url['value'];
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_oEmbed':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_oembed = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_oembed = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_oembed['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					echo $arr_oembed['value'];
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Google_Map':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_google_map = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_google_map = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				// Check if Error-Proof mode activated in module options
				$error_proof_mode = false;
				if ( isset( $array['error_proof_mode'] ) && $array['error_proof_mode'] != '' ) {
					$error_proof_mode = true;
				}

				// Check if module rendered via ajax call
				$ajax_module_render = true;
				if ( isset( $array['module_render_nonajax'] ) ) {
					$ajax_module_render = false;
				}

				// Decide if we should render the module or wait for the page refresh
				$render_code = true;
				if ( $array['dslc_is_admin'] && $error_proof_mode && $ajax_module_render ) {
					$render_code = false;
				}

				if ( ! empty( $arr_google_map['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					$google_api = $array['google_api'];

					if ( ! empty( $google_api ) ) {
						if ( $render_code ) {

						?>
							<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api; ?>"></script>
							<script src="<?php echo LC_ACFINTEGRATION_PLUGIN_URL . 'modules/jquery/google-map/google-map.js' ?>"></script>
							<div class="acf-map" style="width: 100%; height: <?php echo $arr_google_map['height']; ?>px;">
								<div class="marker" 
									data-lat="<?php echo $arr_google_map['value']['lat']; ?>" 
									data-lng="<?php echo $arr_google_map['value']['lng']; ?>"
									data-zoom="<?php echo $arr_google_map['zoom']; ?>">
								</div>
							</div>
						<?php
						} else {
							echo lcacf_display_notice( 'google-map-refresh' );
						}
					} else {
						echo lcacf_display_notice( 'google-map-api' );
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Gallery':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_gallery = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_gallery = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_gallery['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					?>
					
						<div class="dslc-slider"  data-animation="<?php echo $array['animation']; ?>" data-animation-speed="<?php echo $array['animation_speed']; ?>" data-autoplay="<?php echo $array['autoplay']; ?>" data-flexible-height="<?php echo $array['flexible_height']; ?>">

							<?php

							$gallery_images = $arr_gallery['value'];
							$size = 'full';

							if ( $gallery_images ) {

								foreach ( $gallery_images as $gallery_image ) {

									$gallery_image_src = wp_get_attachment_image_src( $gallery_image['ID'], $size );
									$gallery_image_src = $gallery_image_src[0];

									$gallery_image_alt = $gallery_image['alt'];
									if ( ! $gallery_image_alt ) {
										$gallery_image_alt = '';
									}

									$gallery_image_title = $gallery_image['title'];
									if ( ! $gallery_image_title ) {
										$gallery_image_title = '';
									}

									?>
									<div class="dslc-slider-item"><img src="<?php echo $gallery_image_src; ?>" alt="<?php echo $gallery_image_alt; ?>" title="<?php echo $gallery_image_title; ?>" /></div>
									<?php
								}
							}

							?>

						</div><!-- .dslc-slider -->
	
					<?php

				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Radio_Button':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_radio_button = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_radio_button = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_radio_button['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					$acf_version = get_option( 'acf_version' );

					if ( version_compare( $acf_version, '5.0.0', '>=' ) ) {
						if ( 'value' == $arr_radio_button['return_format'] ) {
							echo $arr_radio_button['value'];
						} elseif ( 'label' == $arr_radio_button['return_format'] ) {
							echo '<span class="label">' . $arr_radio_button['value'] . '</span>';
						} else {
							echo '<span class="label">' . $arr_radio_button['value']['label'] . ': </span>' . $arr_radio_button['value']['value'];
						}
					} else {
						echo $arr_radio_button['value'];
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Date_Time_Picker':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_date_time_picker = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_date_time_picker = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_date_time_picker['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					echo $arr_date_time_picker['value'];
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Time_Picker':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_time_picker = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_time_picker = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_time_picker['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					echo $arr_time_picker['value'];
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Button_Group':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_button_group = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_button_group = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_button_group['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					if ( 'value' == $arr_button_group['return_format'] ) {
						echo $arr_button_group['value'];
					} elseif ( 'label' == $arr_button_group['return_format'] ) {
						echo '<span class="label">' . $arr_button_group['value'] . '</span>';
					} else {
						echo '<span class="label">' . $arr_button_group['value']['label'] . ': </span>' . $arr_button_group['value']['value'];
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Post_Object':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_post_object = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_post_object = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_post_object['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					$field_type = $array['field'];

					if ( 'object' === $arr_post_object['return_format'] ) {

						echo '<ul>';
						if ( 0 == $arr_post_object['multiple'] ) {

							$post = $arr_post_object['value'];

							echo '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
						} else {

							foreach ( $arr_post_object['value'] as $post ) {
								echo '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
							}
						}
						echo '</ul>';
					} elseif ( 'id' === $arr_post_object['return_format'] ) {

						echo '<ul>';
						if ( 0 == $arr_post_object['multiple'] ) {

							$id = $arr_post_object['value'];

							echo '<li><a href="' . get_permalink( $id ) . '">' . get_the_title( $id ) . '</a></li>';
						} else {

							foreach ( $arr_post_object['value'] as $id ) {
								echo '<li><a href="' . get_permalink( $id ) . '">' . get_the_title( $id ) . '</a></li>';
							}
						}
						echo '</ul>';
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_Relationship':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_relationship = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_relationship = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_relationship['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {
					$field_type = $array['field'];

					if ( 'object' === $arr_relationship['return_format'] ) {

						echo '<ul>';
						foreach ( $arr_relationship['value'] as $post ) {
							echo '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
						}
						echo '</ul>';
					} elseif ( 'id' === $arr_relationship['return_format'] ) {

						echo '<ul>';
						foreach ( $arr_relationship['value'] as $id ) {
							echo '<li><a href="' . get_permalink( $id ) . '">' . get_the_title( $id ) . '</a></li>';
						}
						echo '</ul>';
					}
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		case 'ACF_User':

			if ( 'dslc_templates' === get_post_type( $array['post_id'] ) ) {
				$arr_user = get_field_object( $array['field'], $array['preview_id'] );
			} else {
				$arr_user = get_field_object( $array['field'], $array['post_id'] );
			}

			if ( ( 'not_set' === $array['field'] ) && $array['dslc_is_admin'] ) {
				echo lcacf_display_notice( 'select' );
			} else {

				if ( ! empty( $array['preview_id'] ) ) {
					$current_id = $array['preview_id'];
				} else {
					$current_id = $array['post_id'];
				}

				$current_field = $array['field'];

				if ( ! empty( $arr_user['value'] ) && lcacf_exits_field( $current_id, $current_field ) ) {

					echo '<ul>';
					if ( 0 == $arr_user['multiple'] ) {

						if ( 'nickname' == $array['display_name'] ) {
							$name = $arr_user['value']['nickname'];
						} else {
							$name = $arr_user['value']['display_name'];
						}

						echo '<li><a href="' . get_author_posts_url( $arr_user['value']['ID'] ) . '" rel="author">' . $name . '</a></li>';
					} else {

						foreach ( $arr_user['value'] as $user ) {

							if ( 'nickname' == $array['display_name'] ) {
								$name = $user['nickname'];
							} else {
								$name = $user['display_name'];
							}

							echo '<li><a href="' . get_author_posts_url( $user['ID'] ) . '" rel="author">' . $name . '</a></li>';
						}
					}
					echo '</ul>';
				} else {

					if ( $array['dslc_is_admin'] ) {
						if ( lcacf_exits_field( $current_id, $current_field ) ) {
							echo lcacf_display_notice( 'empty' );
						} else {
							echo lcacf_display_notice( 'select' );
						}
					}
				}
			}

			break;
		default:
			break;
	}
}

/**
 * Display notice.
 *
 * @param  string $notice Options.
 */
function lcacf_display_notice( $notice ) {

	if ( 'select' === $notice ) {
		$output = '<div class="dslc-notification dslc-red">' . __( 'Click the cog icon on the right of this box to choose which field to show.', 'lc-acf-integration' ) . '<span class="dslca-module-edit-hook dslc-icon dslc-icon-cog"></span></div>';
	} elseif ( 'empty' === $notice ) {
		$output = '<div class="dslc-notification dslc-red">' . __( 'Custom field is empty.', 'lc-acf-integration' ) . '</div>';
	} elseif ( 'google-map-api' === $notice ) {
		$output = '<div class="dslc-notification dslc-green">' . __( 'Click to set Google key.', 'lc-acf-integration' ) . '</div>';
	} elseif ( 'google-map-refresh' === $notice ) {
		$output = '<div class="dslc-notification dslc-green">' . __( 'Please, save and refresh this page to load the map.', 'lc-acf-integration' ) . '</div>';
	} else {
		$output = '';
	}

	return $output;
}

/**
 * Get all fields by group
 *
 * @param  string $type_field current type.
 */
function lcacf_get_all_fields_by_group( $type_field = '' ) {

	$acf_version = get_option( 'acf_version' );

	if ( version_compare( $acf_version, '5.0.0', '>=' ) ) {
		$acf_groups = get_posts(array(
			'numberposts' => -1,
			'post_type' => 'acf-field-group',
			'orderby' => 'menu_order title',
			'order' => 'asc',
			'suppress_filters' => false,
		));
	} else {
		$acf_groups = get_posts(array(
			'numberposts' => -1,
			'post_type' => 'acf',
			'orderby' => 'menu_order title',
			'order' => 'asc',
			'suppress_filters' => false,
		));
	}

	if ( $acf_groups ) {
		foreach ( $acf_groups as $acf_group ) {

			$groups[] = array(
				'id' => $acf_group->ID,
				'title' => $acf_group->post_title,
			);
		}
	}

	$choices = array();

	foreach ( $groups as $group ) {

		if ( version_compare( $acf_version, '5.0.0', '>=' ) ) {
			$acf_fields = acf_get_fields_by_id( $group['id'] );
		} else {
			$acf_fields = apply_filters( 'acf/field_group/get_fields', array(), $group['id'] );
		}

		foreach ( $acf_fields as $acf_field ) {

			if ( $type_field === $acf_field['type'] ) {

				if ( ! empty( $acf_field['label'] ) && ! empty( $acf_field['name'] ) ) {
					$choices[] = array(
						'label' => $group['title'] . ': ' . $acf_field['label'],
						'value' => $acf_field['name'],
					);
				}
			}
		}
	}

	return $choices;
}

/**
 * Get all fields by id and type
 *
 * @param  number $id current id.
 * @param  string $type current type.
 */
function lcacf_get_all_fields( $id, $type ) {

	$fields = get_field_objects( $id );

	$choices = array();

	if ( is_array( $fields ) ) {
		foreach ( $fields as $field_name => $value ) {
			if ( $type === $value['type'] ) {
				$label = $value['label'];
				$value = strtolower( $value['name'] );

				if ( ! empty( $label ) && ! empty( $value ) ) {
					$choices[] = array(
						'label' => $label,
						'value' => $value,
					);
				}
			}
		}
	}

	return $choices;
}

/**
 * Check exist field
 *
 * @param  number $current_id current id.
 * @param  string $current_field current field.
 */
function lcacf_exits_field( $current_id, $current_field ) {

	global $dslc_active;

	if ( $dslc_active ) {

		$fields = get_field_objects( $current_id );

		if ( array_key_exists( $current_field, $fields ) ) {
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}
