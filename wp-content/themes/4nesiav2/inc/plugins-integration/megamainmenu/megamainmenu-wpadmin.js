/**
 * Theme back-end JavaScript
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Custom JavaScript used to improve/extend mega main menu plugin
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    GNU GPL, Version 3
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

(function ($) {
	"use strict";

	jQuery(document).ready(function ($) {

		/**
		* ----------------------------------------------------------------------
		* Mega Main Menu item alignment control
		*/

		var alignmentSelector = '';
		alignmentSelector += '<div class="clearboth"></div>';
		alignmentSelector += '<div class="bootstrap">';
		alignmentSelector += '	<div class="menu-item-align option row menu-item-124item_style select_type">';

		alignmentSelector += '		<div class="option_header col-md-3 col-sm-12">';
		alignmentSelector += '			<div class="descr">';
		alignmentSelector += '				Menu item alignment';
		alignmentSelector += '			</div><!-- class="descr" -->';
		alignmentSelector += '		</div>';

		alignmentSelector += '		<div class="option_field col-md-9 col-sm-12">';
		alignmentSelector += '			<select name="lbmn_menu-item-align" class="col-xs-12 form-control input-sm">';
		alignmentSelector += '				<option value="">Default</option>';
		alignmentSelector += '				<option value="menu-align-left">Left</option>';
		alignmentSelector += '				<option value="menu-align-right">Right</option>';
		alignmentSelector += '			</select>';
		// alignmentSelector += '<input type="text" class="widefat code edit-menu-item-classes" name="syntetic-menu-item-classes" value="" />';//style="display:none"
		alignmentSelector += '		</div>';

		alignmentSelector += '		<div class="col-xs-12">';
		alignmentSelector += '			<div class="h_separator">';
		alignmentSelector += '			</div><!-- class="h_separator" -->';
		alignmentSelector += '		</div>';

		alignmentSelector += '	</div>';
		alignmentSelector += '</div>';


		$(".nav-menus-php .menu-item-depth-0").each(function(index, el) {
			$(".menu-item-settings .bootstrap", el).eq('1').before(alignmentSelector);
		});

		// on page load update menu align dropdown value according to menu item class
		$("select[name=lbmn_menu-item-align]").each(function(index, val) {
			var align_selector    = $(this);
			var menu_class_field  = $(this).parents('.menu-item-settings').find('.edit-menu-item-classes');
			var current_class_val = menu_class_field.val();
			var align_class       = current_class_val.match(/menu-align-left|menu-align-right/g, '');

			if ( align_class == 'menu-align-left' ) {
				align_selector.find('option[value="menu-align-left"]').attr('selected', 'selected');
			} else if ( align_class == 'menu-align-right' ) {
				align_selector.find('option[value="menu-align-right"]').attr('selected', 'selected');
			}

		});

		// on menu align dropdown change update class field
		$("select[name=lbmn_menu-item-align]").live('change', function(event) {
			var selected_value    = $(this).find('option:selected').val();
			var menu_class_field  = $(this).parents('.menu-item-settings').find('.edit-menu-item-classes');
			var current_class_val = menu_class_field.val();
			var cleaned_classes   = current_class_val.replace(/menu-align-left|menu-align-right/g, '');

			if (cleaned_classes != null) {
				selected_value = cleaned_classes + ' ' + selected_value;
				selected_value = selected_value.replace(/\s+/g, ' ');
			}

			menu_class_field.val(selected_value);
		});

	}); // End jQuery(document).
})(jQuery);