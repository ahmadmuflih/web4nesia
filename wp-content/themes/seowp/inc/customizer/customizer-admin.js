/**
 * Theme Customizer enhancements for a better user experience.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This JavaScript is loading as part of Theme Customizer admin
 * not site preview iframe
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

( function( $ ) {
	"use strict";

$(document).ready(function() {
	// $(window).load(function() {
		// setTimeout(function(){

	/**
	 * ----------------------------------------------------------------------
	 * Helper function to get the value from URL Parameter
	 */

	var QueryString = function () {
	  // This function is anonymous, is executed immediately and
	  // the return value is assigned to QueryString!
	  var query_string = {};
	  var query = window.location.search.substring(1);
	  var vars = query.split("&");
	  for (var i=0;i<vars.length;i++) {
	    var pair = vars[i].split("=");
	    	// If first entry with this name
	    if (typeof query_string[pair[0]] === "undefined") {
	      query_string[pair[0]] = pair[1];
	    	// If second entry with this name
	    } else if (typeof query_string[pair[0]] === "string") {
	      var arr = [ query_string[pair[0]], pair[1] ];
	      query_string[pair[0]] = arr;
	    	// If third or later entry with this name
	    } else {
	      query_string[pair[0]].push(pair[1]);
	    }
	  }
	    return query_string;
	} ();

	/**
	* ----------------------------------------------------------------------
	* Add help text and additional instructions for some sections
	*/

	function addCSSRule(sheet, selector, rules, index) {
		if("insertRule" in sheet) {
			sheet.insertRule(selector + "{" + rules + "}", index);
		}
		else if("addRule" in sheet) {
			sheet.addRule(selector, rules, index);
		}
	}


	addCSSRule(document.styleSheets[0], '#accordion-panel-lbmn_panel_notificationpanel:before', 'content: "' + customizerDataSentOnLoad.strings.beforeHeader  + '"');
	addCSSRule(document.styleSheets[0], '#accordion-panel-lbmn_panel_headertop:before', 'content: "' + customizerDataSentOnLoad.strings.header  + '"');
	addCSSRule(document.styleSheets[0], '#accordion-section-page_layout:before', 'content: "' + customizerDataSentOnLoad.strings.content  + '"');
	addCSSRule(document.styleSheets[0], '#accordion-section-lbmn_calltoaction:before', 'content: "' + customizerDataSentOnLoad.strings.footer  + '"');


	$('#accordion-section-lbmn_fonts .customize-section-description-container .description').after('<p class="lbmn-notice">' + customizerDataSentOnLoad.strings.applyFontChanges + '</p>');
	$('#accordion-section-lbmn_fonts .customize-section-description-container .description').after('<p>' + customizerDataSentOnLoad.strings.whereToFindFonts + '</p>');
	$('#accordion-section-lbmn_footer #customize-control-lbmn_footer_design').after('<p>' + customizerDataSentOnLoad.strings.linkToFooters + '</p>');


	/**
	* ----------------------------------------------------------------------
	* Apply Google Fonts selector to selected textfield controls
	*/

	// function-helper
	$.fn.elementsToGroupExist =function() {
		return jQuery(this).length>0;
	};

	$.fn.activateGoogleFontsSelector = function() {
		var originalelementsToGroup = $(this); // checkbox: use google web fonts
		var customizerelementsToGroupGoogleFont = $(this).prev().prev(); //google web font
		var customizerelementsToGroupCustomFont = $(this).prev(); // standard font

		function updateFontSelectorState ( checkboxParentelementsToGroup ) {
			// Apply Chosen JQuery Selector to the controls stated below
			$(customizerelementsToGroupGoogleFont).find('select').chosen({
				no_results_text: "Oops, no font found!",
				width: "100%"
			});

			if ( $(checkboxParentelementsToGroup).find('input').is(':checked') ) {
				$(customizerelementsToGroupGoogleFont).show();
				$(customizerelementsToGroupCustomFont).hide();
			} else {
				$(customizerelementsToGroupCustomFont).show();
				$(customizerelementsToGroupGoogleFont).hide();
			}
		}

		// activate google fonts selector on load
		updateFontSelectorState ( originalelementsToGroup ); 

		$(originalelementsToGroup).find('input').bind('change', function(){
			updateFontSelectorState ( originalelementsToGroup );
		});
	}

	$("li[id^='customize-control-lbmn_font_preset_usegooglefont_']").each(function( index ) {
  		$( this ).activateGoogleFontsSelector();
	});

	/* ------------------------------------------------ */

	$.fn.activateAnotherFontsSelector = function() {
		var originalelementsToGroup = $(this); // checkbox: Use another
		var customizerelementsToGroupGoogleFont = $(this).next(); // standard font
		var customizerelementsToGroupCustomFont = $(this).next().next(); //google web font
		var customizerelementsToGroupWebFont = $(this).next().next().next(); //use google web fonts


		function updateFontSelectorState ( checkboxParentelementsToGroup ) {

			if ( $(checkboxParentelementsToGroup).find('input').is(':checked') ) {
				$(customizerelementsToGroupGoogleFont).show(300);
				//$(customizerelementsToGroupCustomFont).show();
				$(customizerelementsToGroupWebFont).show(300);
			} else {
				$(customizerelementsToGroupCustomFont).hide(300);
				$(customizerelementsToGroupGoogleFont).hide(300);
				$(customizerelementsToGroupWebFont).hide(300);
			}

		}

		// activate google fonts selector on load
		updateFontSelectorState ( originalelementsToGroup ); 

		$(originalelementsToGroup).find('input').bind('change', function(){
			updateFontSelectorState ( originalelementsToGroup );
		});
	}

	$("li[id^='customize-control-lbmn_use_another_font_preset_switch_']").each(function( index ) {
  		$( this ).activateAnotherFontsSelector();
	});


	/**
	* ----------------------------------------------------------------------
	* Show "Font Weights" drop down only with font-weight
	* that available for the font selected
	*/

	var fontSelectors = new Array();

	/* -------------------------------- */

	fontSelectors[0] = {
		fontNameSelector:"#customize-control-lbmn_megamenu_firstlevelitems_font",
		fontWeightSelector:"#customize-control-lbmn_megamenu_firstlevelitems_fontweight"
	};

	updateFontWeight(fontSelectors[0]);
	$(fontSelectors[0].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[0]);
	});

	/* -------------------------------- */

	fontSelectors[1] = {
		fontNameSelector:"#customize-control-lbmn_megamenu_dropdown_font",
		fontWeightSelector:"#customize-control-lbmn_megamenu_dropdown_fontweight"
	};

	updateFontWeight(fontSelectors[1]);
	$(fontSelectors[1].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[1]);
	});

	/* -------------------------------- */

	fontSelectors[2] = {
		fontNameSelector:"#customize-control-lbmn_topbar_firstlevelitems_font",
		fontWeightSelector:"#customize-control-lbmn_topbar_firstlevelitems_fontweight"
	};

	updateFontWeight(fontSelectors[2]);
	$(fontSelectors[2].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[2]);
	});

	/* -------------------------------- */

	fontSelectors[3] = {
		fontNameSelector:"#customize-control-lbmn_calltoaction_font",
		fontWeightSelector:"#customize-control-lbmn_calltoaction_fontweight"
	};

	updateFontWeight(fontSelectors[3]);
	$(fontSelectors[3].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[3]);
	});

	/* -------------------------------- */

	fontSelectors[4] = {
		fontNameSelector:"#customize-control-lbmn_typography_p_font",
		fontWeightSelector:"#customize-control-lbmn_typography_p_fontweight"
	};

	updateFontWeight(fontSelectors[4]);
	$(fontSelectors[4].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[4]);
	});

	/* -------------------------------- */

	fontSelectors[5] = {
		fontNameSelector:"#customize-control-lbmn_typography_h1_font",
		fontWeightSelector:"#customize-control-lbmn_typography_h1_fontweight"
	};

	updateFontWeight(fontSelectors[5]);
	$(fontSelectors[5].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[5]);
	});

	/* -------------------------------- */

	fontSelectors[6] = {
		fontNameSelector:"#customize-control-lbmn_typography_h2_font",
		fontWeightSelector:"#customize-control-lbmn_typography_h2_fontweight"
	};

	updateFontWeight(fontSelectors[6]);
	$(fontSelectors[6].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[6]);
	});

	/* -------------------------------- */

	fontSelectors[7] = {
		fontNameSelector:"#customize-control-lbmn_typography_h3_font",
		fontWeightSelector:"#customize-control-lbmn_typography_h3_fontweight"
	};

	updateFontWeight(fontSelectors[7]);
	$(fontSelectors[7].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[7]);
	});

	/* -------------------------------- */

	fontSelectors[8] = {
		fontNameSelector:"#customize-control-lbmn_typography_h4_font",
		fontWeightSelector:"#customize-control-lbmn_typography_h4_fontweight"
	};

	updateFontWeight(fontSelectors[8]);
	$(fontSelectors[8].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[8]);
	});

	/* -------------------------------- */

	fontSelectors[9] = {
		fontNameSelector:"#customize-control-lbmn_typography_h5_font",
		fontWeightSelector:"#customize-control-lbmn_typography_h5_fontweight"
	};

	updateFontWeight(fontSelectors[9]);
	$(fontSelectors[9].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[9]);
	});

	/* -------------------------------- */

	fontSelectors[10] = {
		fontNameSelector:"#customize-control-lbmn_typography_h6_font",
		fontWeightSelector:"#customize-control-lbmn_typography_h6_fontweight"
	};

	updateFontWeight(fontSelectors[10]);
	$(fontSelectors[10].fontNameSelector + " select").on('change', function() {
		updateFontWeight(fontSelectors[10]);
	});

	/* -------------------------------- */

	// This function updates Font Weight select elementsToGroup
	// with only available values for the selected font preset
	function updateFontWeight(fontPresetSelector) {
		var activeFontPreset = $(fontPresetSelector.fontNameSelector + ' option:selected').val();
		var activeFontWeights = customizerDataSentOnLoad.fontPresetsWeights[activeFontPreset];

		// Convert activeFontWeights object into array
		var activeFontWeightsArray = new Array;
		for(var o in activeFontWeights) {
			 activeFontWeightsArray.push(activeFontWeights[o]);
		}

		// Mark as disabled all the Font Weights that's not available for the current font
		$(fontPresetSelector.fontWeightSelector + ' option').each(function(index, el) {
			$(this).removeAttr( 'disabled');
			if (activeFontWeightsArray.indexOf($(this).val()) === -1  ) {
				$(this).attr('disabled', '');
			};
		});
	};

	/**
	* ----------------------------------------------------------------------
	* Welcome message for quick guide on theme installation
	*/

	var hash = location.hash.slice(1);

	if (hash == 'first-time-visit') {

		var msg_welcome = '<div class="customizer-welcome-message"><div class="content"><h1>Welcome.</h1><h2>This is theme customizer</h2>' +
		'<p>This panel contains all the designs settings for&nbsp;your&nbsp;theme.</p>' +
		'<p>On the right you can preview live the changes you do on the left.</p>' +
		'<p>Depending on your server preformance it can take a few seconds to update preview area.</p>' +
		'<p>Enjoy customization and don’t forget to click “Save” button when you finish. </p><a href="#close" class="button close-message">Close this message</a></div></div>' ;

		$(msg_welcome).insertBefore('#customize-preview');

		$('.customizer-welcome-message').on('click', function(){
			$('.customizer-welcome-message').fadeOut('800');
		});

	};

	/**
	* ----------------------------------------------------------------------
	* Reload page on each 'Save' button click to make sure custom css cahe reset
	* (we generate dynamic in head css on each Theme Customizer save)
	*/

	$('#customize-header-actions #save').on('click', function(){
		$("#customize-controls").ajaxSuccess(function() {
			console.log('Form Submited');
			parent.location.href=parent.location.href;
		});
	});




	/**
	* ---------------------------------------------------------------------------
	* Highlight website secitons on customizer controls hover
	*/
	$('.control-section > h3').on('hover', function(){
		var section_id = $(this).parent().attr('id');

		if ( section_id == 'accordion-panel-lbmn_panel_notificationpanel' ) {
			$('#customize-preview iframe').contents().find('.notification-panel').addClass('highlighted-element')
		}

		if ( section_id == 'accordion-panel-lbmn_panel_topbar' ) {
			$('#customize-preview iframe').contents().find('.topbar  .menu_holder').addClass('highlighted-element')
		}

		if ( section_id == 'accordion-panel-lbmn_panel_logo' ) {
			$('#customize-preview iframe').contents().find('.header-menu .nav_logo').addClass('highlighted-element')
		}

		// if ( section_id == 'accordion-section-lbmn_header_main' ) {
		// 	$('#customize-preview iframe').contents().find('.header-menu .menu_inner').addClass('highlighted-element')
		// }

		if ( section_id == 'accordion-panel-lbmn_panel_headertop' ) {
			$('#customize-preview iframe').contents().find('.header-menu .menu_inner').addClass('highlighted-element')
		}

		if ( section_id == 'accordion-panel-lbmn_panel_megamenu' ) {
			$('#customize-preview iframe').contents().find('.header-menu .mega_main_menu_ul > .menu-item').addClass('highlighted-element')
		}

		if ( section_id == 'accordion-section-lbmn_searchblock' ) {
			$('#customize-preview iframe').contents().find('.header-menu .nav_search_box').addClass('highlighted-element')
		}

		if ( section_id == 'accordion-section-lbmn_calltoaction' ) {
			$('#customize-preview iframe').contents().find('.calltoaction-area').addClass('highlighted-element')
		}

		if ( section_id == 'accordion-section-lbmn_footer' ) {
			$('#customize-preview iframe').contents().find('.site-footer').addClass('highlighted-element')
		}

	});

	$('.control-section > h3').on('mouseleave', function(){
		$('#customize-preview iframe').contents().find('.highlighted-element').removeClass('highlighted-element');
	});


	/**
	* ---------------------------------------------------------------------------
	* Fucntions to hide/show controls based on triger
	*/

	// elementsToGroupsToSwitch – array of jQuery like DOM addresses of elementsToGroups to hide/show
	// elementsToGroupsAction – show / hide
	// selectorAnimationSpeed – 0, 300, 800

	function showHideFormelementsToGroups( elementsToGroupsToSwitch, elementsToGroupsAction, selectorAnimationSpeed ) {
		if (typeof selectorAnimationSpeed == 'undefined' ) selectorAnimationSpeed = 300;

		// go through elementsToGroupsToSwitch array
		// and hide all the elementsToGroups first
		var elementsToGroupsToSwitch_length = elementsToGroupsToSwitch.length;

		for(var i =0; i < elementsToGroupsToSwitch_length; i++){
			var elementsToGroupToToggle = $(elementsToGroupsToSwitch[i]);

			if ( elementsToGroupsAction === 'hide' ) {
				elementsToGroupToToggle.hide(selectorAnimationSpeed);
			} else if ( elementsToGroupsAction === 'show' ) {
				elementsToGroupToToggle.show(selectorAnimationSpeed);
			}
		}

	} // fucntion end

	/**
	* ----------------------------------------------------------------------
	* Show/hide sub-controls: Notification panel
	*/

	// collapsible on Enable/Disable
	var section_notificationpanel_switch = $('#customize-control-lbmn_notificationpanel_switch input[type=checkbox]');
	var notificationpanelelementsGroupsToSwitch = Array();
	notificationpanelelementsGroupsToSwitch[0] = "#customize-control-lbmn_notificationpanel_height";
	notificationpanelelementsGroupsToSwitch[1] = ".grouped-controls.notification-message";
	notificationpanelelementsGroupsToSwitch[2] = ".grouped-controls.notification-colors";
	notificationpanelelementsGroupsToSwitch[3] = ".grouped-controls.notification-hovercolors";

	// Set visible / hidden stat on the first load
	if ( section_notificationpanel_switch.is(':checked') ) {
		showHideFormelementsToGroups( notificationpanelelementsGroupsToSwitch, 'show', 0 );
	} else {
		showHideFormelementsToGroups( notificationpanelelementsGroupsToSwitch, 'hide', 0 );
	}

	// listen 'Notification panel' enable/disable switch for changes
	section_notificationpanel_switch.change(function() {
		if ( section_notificationpanel_switch.is(':checked') ) {
			showHideFormelementsToGroups( notificationpanelelementsGroupsToSwitch, 'show' );
		} else {
			showHideFormelementsToGroups( notificationpanelelementsGroupsToSwitch, 'hide' );
		}
	});

	/**
	* ----------------------------------------------------------------------
	* Show/hide sub-controls: Top bar panel
	*/

	// collapsible on Enable/Disable
	var section_topbar_switch = $('#customize-control-lbmn_topbar_switch input[type=checkbox]');
	var topbarElementsGroupsToSwitch = Array();
	topbarElementsGroupsToSwitch[0] = "#customize-control-lbmn_topbar_height";
	topbarElementsGroupsToSwitch[1] = ".grouped-controls.topbar-colors";
	topbarElementsGroupsToSwitch[2] = ".grouped-controls.topbar-typography";
	topbarElementsGroupsToSwitch[3] = ".grouped-controls.topbar-settings";

	// Set visible / hidden stat on the first load
	if ( section_topbar_switch.is(':checked') ) {
		showHideFormelementsToGroups( topbarElementsGroupsToSwitch, 'show', 0 );
	} else {
		showHideFormelementsToGroups( topbarElementsGroupsToSwitch, 'hide', 0 );
	}

	// listen 'Notification panel' enable/disable switch for changes
	section_topbar_switch.change(function() {
		if ( section_topbar_switch.is(':checked') ) {
			showHideFormelementsToGroups( topbarElementsGroupsToSwitch, 'show' );
		} else {
			showHideFormelementsToGroups( topbarElementsGroupsToSwitch, 'hide' );
		}
	});

	/**
	* ----------------------------------------------------------------------
	* Show/hide menu special background settings based on Logo > Placement selection
	* Show 'MENU > SECTION BACKGROUND' only for the next 'Logo > Placement' options
	* 	– Top-Left
	*  	– Top-Center
	*   	– Top-Right
	*/

	var logoPlacement = $('#customize-control-lbmn_logo_placement select');
	var logoPlacementelementsToGroupsToSwitch = Array();
	logoPlacementelementsToGroupsToSwitch[0] = "#customize-control-lbmn_megamenu_menusectionbackground";
	logoPlacementelementsToGroupsToSwitch[1] = "#customize-control-lbmn_megamenu_sectionbackgroundcolor";
	logoPlacementelementsToGroupsToSwitch[2] = "#customize-control-lbmn_megamenu_sectionbackgroundopacity";

	// Set visible / hidden stat on the first load
	if ( logoPlacement.find('option:selected').val() ==='top-left' || logoPlacement.find('option:selected').val() ==='top-center' || logoPlacement.find('option:selected').val() ==='top-right'  ) {
		showHideFormelementsToGroups( logoPlacementelementsToGroupsToSwitch, 'show', 0 );
	} else {
		showHideFormelementsToGroups( logoPlacementelementsToGroupsToSwitch, 'hide', 0 );
	}

	// listen 'Notification panel' enable/disable switch for changes
	logoPlacement.change(function() {
		if ( logoPlacement.find('option:selected').val() ==='top-left' || logoPlacement.find('option:selected').val() ==='top-center' || logoPlacement.find('option:selected').val() ==='top-right'  ) {
			showHideFormelementsToGroups( logoPlacementelementsToGroupsToSwitch, 'show' );
		} else {
			showHideFormelementsToGroups( logoPlacementelementsToGroupsToSwitch, 'hide' );
		}
	});

	/**
	* ----------------------------------------------------------------------
	* Show/hide sub-controls: Page layout
	*/

	// collapsible on Enable/Disable
	var section_boxedpagebackground_switch = $('#customize-control-lbmn_pagelayoutboxed_switch input[type=checkbox]');
	var boxedpagebackgroundelementsGroupsToSwitch = Array();
	boxedpagebackgroundelementsGroupsToSwitch[0] = ".grouped-controls.boxedpagebackground";
	// notificationpanelelementsGroupsToSwitch[1] = ".grouped-controls.boxedpagebackground";

	// Set visible / hidden stat on the first load
	if ( section_boxedpagebackground_switch.is(':checked') ) {
		showHideFormelementsToGroups( boxedpagebackgroundelementsGroupsToSwitch, 'show', 0 );
	} else {
		showHideFormelementsToGroups( boxedpagebackgroundelementsGroupsToSwitch, 'hide', 0 );
	}

	// listen 'Notification panel' enable/disable switch for changes
	section_boxedpagebackground_switch.change(function() {
		if ( section_boxedpagebackground_switch.is(':checked') ) {
			showHideFormelementsToGroups( boxedpagebackgroundelementsGroupsToSwitch, 'show' );
		} else {
			showHideFormelementsToGroups( boxedpagebackgroundelementsGroupsToSwitch, 'hide' );
		}
	});

	/**
	* ----------------------------------------------------------------------
	* Show/hide sub-controls: Call to action area
	*/

	// collapsible on Enable/Disable
	var section_calltoactionarea_switch = $('#customize-control-lbmn_calltoaction_switch input[type=checkbox]');
	var calltoactionareaElementsGroupsToSwitch = Array();
	calltoactionareaElementsGroupsToSwitch[0] = "#customize-control-lbmn_calltoaction_height";
	calltoactionareaElementsGroupsToSwitch[1] = ".grouped-controls.calltoaction-message";
	calltoactionareaElementsGroupsToSwitch[2] = ".grouped-controls.calltoaction-colors";
	calltoactionareaElementsGroupsToSwitch[3] = ".grouped-controls.calltoaction-hovercolors";

	// Set visible / hidden stat on the first load
	if ( section_calltoactionarea_switch.is(':checked') ) {
		showHideFormelementsToGroups( calltoactionareaElementsGroupsToSwitch, 'show', 0 );
	} else {
		showHideFormelementsToGroups( calltoactionareaElementsGroupsToSwitch, 'hide', 0 );
	}

	// listen 'Call to action area' enable/disable switch for changes
	section_calltoactionarea_switch.change(function() {
		if ( section_calltoactionarea_switch.is(':checked') ) {
			showHideFormelementsToGroups( calltoactionareaElementsGroupsToSwitch, 'show' );
		} else {
			showHideFormelementsToGroups( calltoactionareaElementsGroupsToSwitch, 'hide' );
		}
	});



	/**
	* ---------------------------------------------------------------------------
	* Custom Color Sliders Picker control initialization
	* http://www.virtuosoft.eu/code/jquery-colorpickersliders/
	*/

	$('[data-color-format]').each(function(index, el) {
		var colorInput = $(this);
		var currentColor = colorInput.val();

		colorInput.ColorPickerSliders({
			previewontriggerelementsToGroup: true,
			preventtouchkeyboardonshow: false,
			flat: false,
			// flat: true,
			color: colorInput.val(),
			previewformat: 'hsl',
			customswatches: false,
			updateinterval: '120',
			// Update interval of the sliders while dragging (ms).
			// The default is 30.

			swatches: false,//['red', 'green', 'blue'],
			labels: {
				hslhue: 'Hue',
				hslsaturation: 'Saturation',
				hsllightness: 'Lightness',
			},
			order: {
				hsl: 1,
				opacity: 2,
				// rgb: 1,
				// preview: 2
			},
			onchange: function(container, color) {

				var newColor = color.tiny.toRgbString();

				if (typeof color !== 'undefined' ) {
					if ( currentColor !== newColor ) {
						// Need these checks to make sure there is no
						// onChange loop and 100% CPU load

						colorInput.change();
						// make Theme Customizer to catch updated input field
						currentColor = newColor;
						// update current color var with a new value
					}
				}

			}
		});
	});


	/**
	* ----------------------------------------------------------------------
	* Add Icon Select Modal for Notification panel
	*/

	// add 'name' attribute for the icon code input field
	// this name attribute is needed for Mega Main Menu modal work right
	$('#customize-control-lbmn_notificationpanel_icon input').attr('name','lbmn_notificationpanel_icon');
	$('#customize-control-lbmn_notificationpanel_icon input').attr('data-icon','icons_list_notificationpanel_icon');

	// Show Icons button in the 'Notification panel' section
	$('#customize-control-lbmn_notificationpanel_icon input').after(
		'<a class="button" data-target="#icons_list_notificationpanel_icon" href="./?mm_page=icons_list&amp;input_name=lbmn_notificationpanel_icon__input&amp;modal_id=icons_list_notificationpanel_icon" data-toggle="modal">Show Icons</a>'
	);

	// Icon selection modal window content (from Mega Main Menu plugin)
	$('#customize-preview').after(
		'<div class="bootstrap mmmp_icons_modal"><div id="icons_list_notificationpanel_icon" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="icons_listLabel" aria-hidden="true"></div></div>'
	);

	//On modal close fire 'change' event for icon code input
	// $('#icons_list_notificationpanel_icon').on('hidden.bs.mmpm_modal', function (e) {
	// 	$('#customize-control-lbmn_notificationpanel_icon input').change();
	// });

	$('#icons_list_notificationpanel_icon').on('click', '.ok_button', function (e) {
		$('#customize-control-lbmn_notificationpanel_icon input').change();
	});



	/**
	* ---------------------------------------------------------------------------
	* Open mega menu dropdowns one by one
	*/

	// Create "Open Dropdown" button

	$('#accordion-section-lbmn_megamenu_dropdown .accordion-section-content > li:first-child').after(
		'<a class="button dropdown-open" href="#">' + customizerDataSentOnLoad.strings.dropdownPreview + '</a>' +
		'<a class="button dropdown-close" href="#">' + customizerDataSentOnLoad.strings.close + '</a>'
	);

	var mm_current_dropdown = 0;

	// "Open Dropdowns" button listener
	$('.dropdown-open').live('click', function(event) {
		event.preventDefault();
		$(this).text(customizerDataSentOnLoad.strings.nextDropdown);
		var mm_dropdowns = $('#customize-preview iframe').contents().find('#mega_main_menu.header-menu .mega_main_menu_ul > .menu-item').filter('.menu-item-has-children, .post_type_dropdown');
		var mm_dropdowns_count = mm_dropdowns.length - 1;
		mm_dropdowns.find('.mega_dropdown').attr('style', '');
		mm_dropdowns.eq(mm_current_dropdown).find('.mega_dropdown').css({
			'max-height': '3000px',
			'max-width': '3000px',
			'opacity': '1',
			'overflow': 'visible',
			'transform': 'translateY(0px)',
			'transition': 'none',
			'display': 'block'
		});

		if ( mm_current_dropdown < mm_dropdowns_count ) {
			mm_current_dropdown += 1;
		} else {
			mm_current_dropdown = 0;
		}

	});

	// "Close" button listener
	$('.dropdown-close').live('click', function(event) {
		event.preventDefault();

		$('.dropdown-open').text(customizerDataSentOnLoad.strings.dropdownPreview);

		var mm_dropdowns = $('#customize-preview iframe').contents().find('#mega_main_menu.header-menu .mega_main_menu_ul > .menu-item-has-children');
		mm_dropdowns.find('.mega_dropdown').attr('style', '');
	});




	/**
	* ----------------------------------------------------------------------
	* Set a warning message when on of the required plugin isn't installed
	*/
/*
	if ( customizerDataSentOnLoad.notInstalled_requiredPlugin ) {
		if (QueryString.theme === 'seowp') {
			// Theme not activated, yet
			$('#customize-preview').after('<div class="themecustomizer-fullscreen-message required-plugin-not-istalled"><div class="message-content welcome-panel"><h3>Thank you for installing our theme!</h3><p class="about-description">To make sure our theme works as expected you need to activate it and make some basic configurations.</p> <a href="/wp-admin/themes.php" class="button button-primary button-hero">Open themes page</a></div></div>');
		} else {
			// Required plugins not installed
			$('#customize-preview').after('<div class="themecustomizer-fullscreen-message required-plugin-not-istalled"><div class="message-content welcome-panel"><h3>We\'re almost there</h3><p class="about-description">There are few premium plugins that are included with our theme and need to be installed/activated to make sure everything is working properly.</p> <a href="/wp-admin/themes.php?page=install-required-plugins" class="button button-primary button-hero">Open plugins installation page</a></div></div>');
		}
	}
*/
	/**
	* ----------------------------------------------------------------------
	* Set a warning message that Mega Main Menu plugin is required
	*/
/*
	if ( customizerDataSentOnLoad.notInstalled_MegaMainMenu ) {
		$( '#accordion-section-lbmn_headertop, #accordion-section-lbmn_topbar' ).each(function(index, el) {

			var accordionContent = $(this).find('.accordion-section-content');

			$(accordionContent).find('li').hide();
			$(accordionContent).find('div').hide();
			$(accordionContent).append('<li><div class="tc-notice"><h3>Can\'t see website header?</h3><p><strong>Mega Main Menu</strong> plugin is required for our theme to work properly. <br />The plugin is already included with a theme (you just need to install it).</p> <a href="/wp-admin/themes.php?page=install-required-plugins" class="button button-primary">Open plugins installation page</a></div></li>');

			$('#accordion-section-lbmn_logo, '+
			'#accordion-section-lbmn_megamenu,'+
			'#accordion-section-lbmn_megamenu_dropdown, '+
			'#accordion-section-lbmn_searchblock').hide();
		});
	}
*/
	/**
	* ----------------------------------------------------------------------
	* Set a warning message that Header Menu location isn't assigned
	*/

	if ( customizerDataSentOnLoad.notAssigned_HeaderMenu ) {

			var accordionContent = $( '#accordion-section-lbmn_headertop' ).find('.accordion-section-content');

			$(accordionContent).find('li').hide();
			$(accordionContent).find('div').hide();
			$(accordionContent).append('<li><div class="tc-notice"><h3>No menu assigned to \'Header\' location</h3><p>Please visit <a href="/wp-admin/nav-menus.php?action=locations">Appearance &gt; Menu Locations</a> page to assign menu to \'Header Main Menu\' location.</p> <a href="/wp-admin/nav-menus.php?action=locations" class="button button-primary">Take me there</a></div></li>');

			$('#accordion-section-lbmn_logo, '+
			'#accordion-section-lbmn_megamenu,'+
			'#accordion-section-lbmn_megamenu_dropdown, '+
			'#accordion-section-lbmn_searchblock').hide();
	}

	/**
	* ----------------------------------------------------------------------
	* Set a warning message that Top Bar Menu location isn't assigned
	*/

	if ( customizerDataSentOnLoad.notAssigned_TopBar ) {

		var accordionContent = $( '#accordion-section-lbmn_topbar' ).find('.accordion-section-content');

		$(accordionContent).find('li').hide();
		$(accordionContent).find('div').hide();
		$(accordionContent).append('<li><div class="tc-notice"><h3>No menu assigned to \'Top Bar Menu\' location</h3><p>Please visit <a href="/wp-admin/nav-menus.php?action=locations">Appearance &gt; Menu Locations</a> page to assign menu to \'Top Bar Menu\' location.</p> <a href="/wp-admin/nav-menus.php?action=locations" class="button button-primary">Take me there</a></div></li>');
	}


		// }, 9000);
	// });
}); // document.ready




/**
* ----------------------------------------------------------------------
* WordPress Media Manager that we call
* for each Image control in Tehme Customier
*/

// Object for creating WordPress 3.5 media upload menu
// for selecting theme images.
wp.media.lbmnMediaManager = {

	 init: function() {
		  // Create the media frame.
		  this.frame = wp.media.frames.lbmnMediaManager = wp.media({
				title: 'Choose Image',
				library: {
					 type: 'image'
				},
				button: {
					 text: 'Insert',
				}
		  });

		  // When an image is selected, run a callback.
		  this.frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = wp.media.lbmnMediaManager.frame.state().get('selection').first(),
				controllerName = wp.media.lbmnMediaManager.$el.data('controller');

				var controller = wp.customize.control.instance(controllerName);
				controller.thumbnailSrc(attachment.attributes.url);
				controller.setting.set(attachment.attributes.url);
		  });


		  $('.choose-from-library-link').click( function( event ) {
				wp.media.lbmnMediaManager.$el = $(this);
				var controllerName = $(this).data('controller');
				event.preventDefault();

				wp.media.lbmnMediaManager.frame.open();
		  });

	 } // end init
}; // end lbmnMediaManager

wp.media.lbmnMediaManager.init();


} )( jQuery );