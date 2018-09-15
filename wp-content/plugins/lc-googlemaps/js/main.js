"use strict";

function lc_googlemaps_init() {

	var gmap, lat, lng, position, settings, map, marker, optZooming, optMapUI, optDblZooming, optDragging, optType, optCustomMarker;

	jQuery('.sklc-gmaps:not(.sklc-gmaps-initiated)').each(function(){

		gmap = jQuery(this);
		lat = gmap.data('lat');
		lng = gmap.data('lng');

		optZooming = false;
		if ( gmap.data('zooming') == 'enabled' )
			optZooming = true;

		optDblZooming = true;
		if ( gmap.data('dblzooming') == 'enabled' )
			optDblZooming = false;

		optDragging = false;
		if ( gmap.data('dragging') == 'enabled' )
			optDragging = true;

		optMapUI = true;
		if ( gmap.data('mapui') == 'enabled' )
			optMapUI = false;

		optCustomMarker = false;
		if ( gmap.data('custom-marker').length > 0 )
			optCustomMarker = gmap.data('custom-marker');

		if ( gmap.data('maptype') == 'HYBRID' )
			optType = google.maps.MapTypeId.HYBRID;
		else if ( gmap.data('maptype') == 'ROADMAP' )
			optType = google.maps.MapTypeId.ROADMAP;
		else if ( gmap.data('maptype') == 'SATELLITE' )
			optType = google.maps.MapTypeId.SATELLITE;
		else if ( gmap.data('maptype') == 'TERRAIN' )
			optType = google.maps.MapTypeId.TERRAIN;

		position = new google.maps.LatLng( lat, lng );
		settings = {
			zoom: gmap.data('zoom'),
			center: position,
			scrollwheel: optZooming,
			disableDefaultUI: optMapUI,
			disableDoubleClickZoom: optDblZooming,
			mapTypeId: optType,
			draggable: optDragging
		}

		map = new google.maps.Map( gmap[0], settings );

		if ( optCustomMarker ) {

			marker = new google.maps.Marker({
				position: position,
				map: map,
				icon : optCustomMarker
			});

		} else {

			marker = new google.maps.Marker({
				position: position,
				map: map
			});

		}

		gmap.addClass('sklc-gmaps-initiated');

	});

}

jQuery(document).ready(function($){

	lc_googlemaps_init();

	$(document).ajaxComplete(function() {

		if ( $('body').hasClass('dslca-enabled') ) {
			lc_googlemaps_init();
		}

		setTimeout( function(){
			if ( $('body').hasClass('dslca-enabled') ) {
				lc_googlemaps_init();
			}
		}, 1000 );

	});

});
