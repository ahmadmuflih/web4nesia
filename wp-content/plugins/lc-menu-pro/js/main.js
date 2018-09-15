"use strict";

jQuery(document).ready(function($){

	jQuery('.lcmenu-mobile-hook, .lcmenupro-site-overlay, .lcmenu-mobile-close-hook').on('click', function(event){

		var mobileNavigation = $(this).parents().eq(2).find('.lcmenupro-mobile-navigation');
		// var siteOverlay = $(this).parents().eq(2).find('.site-overlay');
		var siteOverlay = $(this).parents().eq(2).find('.lcmenupro-site-overlay');

		if ( mobileNavigation.hasClass('open') ) {
			mobileNavigation.removeClass('open');
			siteOverlay.hide();
		} else {
			mobileNavigation.addClass('open');
			siteOverlay.show();
		}
	});

	/* Calculate left offset for the full-width dropdowns on page load.*/
	jQuery('.menu-width-full').each(function(index, el) {
		setLeftMenuOffset(el);
	});

	/* Calculate left offset for the full-width dropdowns on hover.*/
	jQuery('.menu-width-full').on('hover', function(event) {
		event.preventDefault();
		// @todo: cache it somehow?
		if ( jQuery(event.target).hasClass('menu-width-full') ) {
			setLeftMenuOffset(event.target);
		}
	});


});

function setLeftMenuOffset (element) {
	// Fix error: Can't get getBoundingClientRect property of undefined.
	if ( element === undefined ) {
		return;
	}

	var parentItemRect = element.getBoundingClientRect();
	var parentSection = jQuery(element).closest('.dslc-modules-section-wrapper')[0];
	var parentSectionRect = parentSection.getBoundingClientRect();
	var offset = parseInt(parentItemRect.left) - parseInt(parentSectionRect.left);

	jQuery(element).children('.sub-menu').first().css('left', '-' + offset + 'px');
}