"use strict";

jQuery(document).ready(function($){


	$('.field-move').before('<hr class="menu-controls-divider" />');


/**
 * Mega Menu (Columns & Width) Controls.
 * -----------------------------------------------------------------------------
 */

	$('.field-move').each(function(index, el) {
		var control = '<select class="select-dslc-menu-type">';
			control += '<option value="" selected="selected">Standard</option>';
			control += '<option value="menu-type-columns">Columns (Mega Menu)</option>';
			control += '</select>';
		$(el).before('<p class="dslc-columns-control description description-thin" ><label>Dropdown Type<br />' + control + '</label></p>');

	 	// var current_icon = '<span class="dslc-current-icon"></span> ';
	 	var control = '<select class="select-dslc-menu-width">';
	 		control += '<option value="" selected="selected">Auto</option>';
	 		control += '<option value=""></option>';
	 		control += '<option value="menu-width-2s">400px (2 columns – S)</option>';
	 		control += '<option value="menu-width-2m">460px (2 columns – M)</option>';
	 		control += '<option value="menu-width-2l">540px (2 columns – L)</option>';
	 		control += '<option value=""></option>';
	 		control += '<option value="menu-width-3s">600px (3 columns – S)</option>';
	 		control += '<option value="menu-width-3m">700px (3 columns – M)</option>';
	 		control += '<option value="menu-width-3l">800px (3 columns – L)</option>';
	 		control += '<option value=""></option>';
	 		control += '<option value="menu-width-4s">880px (4 columns – S)</option>';
	 		control += '<option value="menu-width-4m">940px (4 columns – M)</option>';
	 		control += '<option value="menu-width-4l">1000px (4 columns – L)</option>';
	 		control += '<option value=""></option>';
	 		control += '<option value="menu-width-full">Full Width</option>';
	 		control += '</select>';
	 	$(el).before('<p class="dslc-columns-width description description-thin" ><label>Dropdown Width<br />' + control + '</label></p>');
	});

	jQuery(document).on('change', '.select-dslc-menu-type', function(el) {

		jQuery(this).closest('.menu-item').addClass('active-editing-menu-element');

		var oldClassesValue = $('.menu-item.active-editing-menu-element .edit-menu-item-classes').val();

		if ( oldClassesValue !== undefined ) {
			var oldClasses = oldClassesValue.split(' ');

			$.each(oldClasses, function(index, val) {

				const regex = /(menu-type-(?:[a-zA-Z-_0-9]*))/g;
				let m;

				while ((m = regex.exec(val)) !== null) {
					// This is necessary to avoid infinite loops with zero-width matches.
					if (m.index === regex.lastIndex) {
						regex.lastIndex++;
					}

				    // Remove old icon class value form the input element.
					if ( oldClasses !== undefined ) {
						oldClasses.splice(index, 1);
					}
				}
			});

			oldClassesValue = oldClasses.join(' ');

			if ( oldClassesValue !== '' ) {
				oldClassesValue = oldClassesValue + ' ';
			}
		} else {
			oldClassesValue = '';
		}

		jQuery('.menu-item.active-editing-menu-element .edit-menu-item-classes').val( oldClassesValue + el.target.value ).change();

		if ( el.target.value ) {
			jQuery('.menu-item.active-editing-menu-element .dslc-columns-width').css('display', 'block');
		}
		jQuery('.active-editing-menu-element').removeClass('active-editing-menu-element');
	});

	/**
	 * Column width control selector on change event.
	 */

	jQuery(document).on('change', '.select-dslc-menu-width', function(el) {

		jQuery(this).closest('.menu-item').addClass('active-editing-menu-element');

		var oldClassesValue = $('.menu-item.active-editing-menu-element .edit-menu-item-classes').val();

		if ( oldClassesValue !== undefined ) {
			var oldClasses = oldClassesValue.split(' ');

			$.each(oldClasses, function(index, val) {

				const regex = /(menu-width-(?:[a-zA-Z-_0-9]*))/g;
				let m;

				while ((m = regex.exec(val)) !== null) {
					// This is necessary to avoid infinite loops with zero-width matches.
					if (m.index === regex.lastIndex) {
						regex.lastIndex++;
					}

				    // Remove old icon class value form the input element.
					if ( oldClasses !== undefined ) {
						oldClasses.splice(index, 1);
					}
				}
			});

			oldClassesValue = oldClasses.join(' ');

			if ( oldClassesValue !== '' ) {
				oldClassesValue = oldClassesValue + ' ';
			}
		} else {
			oldClassesValue = '';
		}

		jQuery('.menu-item.active-editing-menu-element .edit-menu-item-classes').val( oldClassesValue + el.target.value ).change();
		jQuery('.active-editing-menu-element').removeClass('active-editing-menu-element');
	});


/**
 * Icon Control.
 * -----------------------------------------------------------------------------
 */

	$('.field-move').each(function(index, el) {
		var label = '<label>Menu Item Icon</label><br />';
		var current_icon = '<span class="dslc-current-icon"></span> ';
		var button = '<span id="dslc-open-icons-popup" class="button">' + current_icon +' Select Icon</span>';
		$(el).before('<p class="dslc-open-icons-button dslca-open-modal-hook description description-thin"  data-modal=".dslc-list-icons">' + label + button + '</p>');
		$(el).before('<p class="dslc-delete-icon-button description description-thin"><span class="button">Delete Icon</span</p>');
		
		var classesValue = jQuery(this).closest(".menu-item").eq(0).find('.edit-menu-item-classes').val();
		
		if ( classesValue !== '' ) {
			jQuery(this).closest(".menu-item").eq(0).find('.dslc-delete-icon-button').show();
		} else {
			jQuery(this).closest(".menu-item").eq(0).find('.dslc-delete-icon-button').hide();
		}
	});


	jQuery(document).on('click', '.dslca-open-modal-hook[data-modal^=".dslc-list-icons"]', function(el) {
		jQuery(this).closest('.menu-item').addClass('icon-modal-active');
	});

	jQuery(document).on('click', '.dslca-modal-icons .icon-item', function(el) {
		// Get selected item code.
		var selectedIconCode = $('.icon-item_name', this).text();
		var oldClassesValue = $('.menu-item.icon-modal-active .edit-menu-item-classes').val();

		if ( oldClassesValue !== undefined ) {
			var oldClasses = oldClassesValue.split(' ');

			$.each(oldClasses, function(index, val) {

				const regex = /(dslc-icon-(?:[a-zA-Z-_0-9]*))/g;
				let m;

				while ((m = regex.exec(val)) !== null) {
					// This is necessary to avoid infinite loops with zero-width matches.
					if (m.index === regex.lastIndex) {
						regex.lastIndex++;
					}

 				    // Remove old icon class value form the input element.
					if ( oldClasses !== undefined ) {
						oldClasses.splice(index, 1);
					}
				}
			});

			oldClassesValue = oldClasses.join(' ');

			if ( oldClassesValue !== '' ) {
				oldClassesValue = oldClassesValue + ' ';
			}
		} else {
			oldClassesValue = '';
		}

		jQuery('.menu-item.icon-modal-active .edit-menu-item-classes').val( oldClassesValue + 'dslc-icon-' + selectedIconCode ).change();
		jQuery('.menu-item.icon-modal-active .dslc-current-icon').attr('class', 'dslc-current-icon dslc-icon-' + selectedIconCode );
		
		var newClassesValue = $('.menu-item.icon-modal-active .edit-menu-item-classes').val();
		
		if ( newClassesValue !== '' ) {
			$('.menu-item.icon-modal-active .dslc-delete-icon-button').show();
		}

		// Close modal window.
		dslc_hide_modal( '', jQuery('.dslca-modal:visible') );
		jQuery('.icon-modal-active').removeClass('icon-modal-active');

	});
	
	jQuery(document).on('click', '.dslc-delete-icon-button span', function(el) {
		var classesValue = jQuery(this).closest(".menu-item").eq(0).find('.edit-menu-item-classes').val();
		
		if ( classesValue !== '' ) {
			jQuery(this).closest(".menu-item").eq(0).find('.edit-menu-item-classes').val('');
			jQuery(this).closest(".menu-item").eq(0).find('.dslc-current-icon').removeClass().addClass('dslc-current-icon');
			jQuery(this).parent().hide();
		}
	});


/**
 * Select current value for the each dynamically added control.
 * -----------------------------------------------------------------------------
 */

	$('.menu-item').each(function(index, el) {
		var cssClases = $('.edit-menu-item-classes', this).val();

		if ( cssClases !== '' ) {
			/**
			 * Icon class separation and control init.
			 */
			const iconRegex = /(dslc-icon-(?:[a-zA-Z-_0-9]*))/g;
			let iconMatch;

			while ((iconMatch = iconRegex.exec(cssClases)) !== null) {
				// This is necessary to avoid infinite loops with zero-width matches
				if (iconMatch.index === iconRegex.lastIndex) {
					iconRegex.lastIndex++;
				}

				// Update Current Icon element with the class value.
				jQuery('.dslc-current-icon', el).attr('class', 'dslc-current-icon ' + iconMatch[0] );
			}

			/**
			 * Menu type control value init.
			 */
			const menuTypeRegex = /(menu-type-(?:[a-zA-Z-_0-9]*))/g;
			let menuTypeMatch;

			while ((menuTypeMatch = menuTypeRegex.exec(cssClases)) !== null) {
				// This is necessary to avoid infinite loops with zero-width matches
				if (menuTypeMatch.index === menuTypeRegex.lastIndex) {
					menuTypeRegex.lastIndex++;
				}

				// Update Current menu type control with the class value.
				jQuery('.select-dslc-menu-type', el).val(menuTypeMatch[0] );

				// Show width selector.
				jQuery('.dslc-columns-width', el).css('display','block');
					// '.select-dslc-menu-width'

				/**
				 * Menu width control value init.
				 */
				const menuWidthRegex = /(menu-width-(?:[a-zA-Z-_0-9]*))/g;
				let menuWidthMatch;

				while ((menuWidthMatch = menuWidthRegex.exec(cssClases)) !== null) {
					// This is necessary to avoid infinite loops with zero-width matches
					if (menuWidthMatch.index === menuWidthRegex.lastIndex) {
						menuWidthRegex.lastIndex++;
					}

					// Update Current menu type control with the class value.
					jQuery('.select-dslc-menu-width', el).val(menuWidthMatch[0] );

				}
			}
		}
	});

});

function dslc_show_dropdown() {
	var openNext = false,
		opened = false,
		skipNext= false,
		indexTotal = jQuery('.dslca-module-being-edited .menu > li.menu-item-has-children').length;

	jQuery('.dslca-module-being-edited .menu > li.menu-item-has-children', LiveComposer.Builder.PreviewAreaDocument).each(function(index, el) {

		if ( jQuery(el).hasClass('lc-menu-hover') ) {
			jQuery(el).removeClass('lc-menu-hover');
			openNext = true;

			/* Set skipNext to true to skip one click after full cycle. */
			if (indexTotal = index) {
				skipNext = true;
			}
		} else if ( openNext ) {
			jQuery(el).addClass('lc-menu-hover');
			opened = true;
			openNext = false;
		}
	});

	// Open first dropdown.
	if ( ! opened && ! skipNext) {
		jQuery('.dslca-module-being-edited .menu > li.menu-item-has-children', LiveComposer.Builder.PreviewAreaDocument).first().addClass('lc-menu-hover');
	}

	// Open third-level submenu.
	jQuery('.dslca-module-being-edited .lc-menu-hover li.menu-item-has-children:first-child', LiveComposer.Builder.PreviewAreaDocument).addClass('lc-menu-hover');
}

function dslc_show_menu() {

	jQuery('.dslca-options-filter-hook[data-section="styling"], .dslca-options-filter-hook[data-section="functionality"], .dslca-module-edit-save, .dslca-module-edit-cancel').on('click', function() {
		jQuery('.dslca-module-being-edited .lcmenupro-mobile-navigation', LiveComposer.Builder.PreviewAreaDocument).removeClass( "open" );
		jQuery('.dslca-module-being-edited .lcmenupro-site-overlay', LiveComposer.Builder.PreviewAreaDocument).css( 'display', 'none');
	});

	jQuery('.dslca-module-being-edited .lcmenupro-mobile-navigation', LiveComposer.Builder.PreviewAreaDocument).toggleClass('open');

	if ( jQuery('.dslca-module-being-edited .lcmenupro-mobile-navigation', LiveComposer.Builder.PreviewAreaDocument).hasClass('open') ) {
		jQuery('.dslca-module-being-edited .lcmenupro-site-overlay', LiveComposer.Builder.PreviewAreaDocument).css( 'display', 'block');
	} else {
		jQuery('.dslca-module-being-edited .lcmenupro-site-overlay', LiveComposer.Builder.PreviewAreaDocument).css( 'display', 'none');
	}

}

/**
 * Hide element when click on another element on the page.
 */
/*jQuery.fn.outerHide = function(params)
{
    var $ = jQuery;
    params = params ? params : {};

    var self = this;

    if ( 'destroy' == params ) {

        $(document).unbind('click.outer_hide');
        return false;
    }

    $(document).bind('click.outer_hide', function(e) {

        if ($(e.target).closest(self).length == 0 &&
            e.target != self &&
            $.inArray($(e.target)[0], $(params.clickObj)) == -1 &&
            $(self).css('display') != 'none'
        )
        {
            if(params.clbk)
            {
                params.clbk();
            }else{
                $(self).hide();
            }
        }
    });
}*/
