// REQUEST ANIMATION FRAME

window.fgRAF = (function(){
	return  window.requestAnimationFrame		|| 
			window.webkitRequestAnimationFrame 	|| 
			window.mozRequestAnimationFrame    	|| 
			window.oRequestAnimationFrame      	|| 
			window.msRequestAnimationFrame     	|| 
			function( callback ){
				window.setTimeout(callback, 1000 / 60);
			};
})();

(function($){

	"use strict"; // Start of use strict

	// GET WINDOW SIZE

	var fgWinWidth;
	var fgWinHeight;

	function calcFgWinWidth(){
		fgWinWidth = $(window).width();
	}

	function calcFgWinHeight(){
		fgWinHeight = $(window).height();
	}

	calcFgWinWidth();
	calcFgWinHeight();

	// BREAKPOINT DETECTION

	$('body').append('<div class="fg-breakpoint"></div>');

	var $fgBreakpoint = $('.fg-breakpoint');
	var fgBreakpoint;

	function calcFgBreakpoint(){
		fgBreakpoint = $fgBreakpoint.width();	
	}

	calcFgBreakpoint();

	// DOCUMENT READY

	$(document).ready(function(){

		// RANDOMIZE COLORS - REMOVE LATER

		// $('div[class|="col"]').each(function(){
		// 	var randomColor = Math.floor(Math.random()*16777215).toString(16);
		// 	$(this).css('background', '#' + randomColor);
		// });

		// MATCH ROW HEIGHT

		$('.fg-row-match').each(function(){
			$(this).children('div[class|="col"]').matchHeight();
		});

		// WOW ANIMATIONS

		$('[data-fg-wow]').each(function(){
			var $this = $(this);
			$this.addClass('fg-wow ' + $this.attr('data-fg-wow'));
		});

		var fgWow = new WOW(
			{
				boxClass: 'fg-wow',
				mobile: false,
				tablet: false
			}
		);

		fgWow.init();

		// BACKGROUNDS

		$.fn.fgBackground = function(){

			function fgBgInitYT(){
				if (typeof window.YT !== 'undefined' && typeof window.YT.Player !== 'undefined') {

					$('.fg-youtube-iframe').each(function(){

						var $this = $(this);

					    var player = new YT.Player($this[0], {
					        videoId: $this.attr('data-videoId'),
					        playerVars: {
								iv_load_policy: 3,
								modestbranding: 1,
								autoplay: 1,
								controls: 0,
								showinfo: 0,
								wmode: 'opaque',
								branding: 0,
								autohide: 0,
								loop: 1
					        },
							events: {
								'onReady': onPlayerReady
							}
					    });

						function onPlayerReady(event) {
							player.mute();
						}

					});

				} else {
					window.setTimeout(fgBgInitYT, 100);
				}

			}

			fgBgInitYT();

			function bg_type_color(bgLayerData, $bgLayer, $this_el){
				$bgLayer.css('background-color', bgLayerData.color);
			}

			function bg_type_image(bgLayerData, $bgLayer, $this_el){
				$bgLayer.css('background-image', "url('" + bgLayerData.url + "')");
			}

			function bg_type_video(bgLayerData, $bgLayer, $this_el){
				var url = bgLayerData.url;
				var videoID = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);

				$bgLayer.append('<div class="fg-youtube-iframe" data-videoId="' + videoID[1] + '"></div>');

				var thisElWidth = $this_el.outerWidth();
				var thisElHeight = $this_el.outerHeight();

				$bgLayer.css('width', thisElWidth).css('height', thisElWidth/16*9 );

				if ( thisElHeight > thisElWidth/16*9 ){
					console.log(111);
					// $bgLayer.css('width', thisElWidth).css('height', thisElWidth/16*9 );
					$bgLayer.css('width', thisElHeight/9*16).css('height', thisElHeight );
				}

			}

			function bg_type_parallax(bgLayerData, $bgLayer, $this_el){

				var lastLoop = new Date;

				function fgParallax() {

					fgRAF(fgParallax);

					if ( fgBreakpoint >= 3 ) {

						thisElWidth = $this_el.outerWidth();
						thisElHeight = $this_el.outerHeight();
						thisElTop = $this_el.offset().top;
						thisElLeft = $this_el.offset().left;
						bgLayerHeight = $bgLayer.outerHeight();

						$bgLayer.removeClass('parallax-off').addClass('parallax-on');

						var winScrollTop = $(window).scrollTop();

						if ( $bgLayer.isOnScreen(0,0) ){

							if ( 'auto' == bgLayerData.size ){

								// CHANGE BACKGROUND SIZE
								
								$bgLayer.css('background-size', 'auto');

								// CHANGE BACKGROUND POSITION

								var calcBgPosX = bgLayerData.offset_h + '%';
								var calcBgPosY = ( ( thisElTop - winScrollTop ) * bgLayerData.speed / 100 ) + ( thisElHeight - bgLayerData.height ) * ( bgLayerData.offset_v / 100 )  + 'px';

								var finalBgPos = calcBgPosX + ' ' + calcBgPosY;

								$bgLayer.css('background-position', finalBgPos);

								// ATTACH BACKGROUND IMAGE

								if ( $bgLayer.css('background-image') ){
									$bgLayer.css('background-image', "url('" + bgLayerData.url + "')");
								}

							} else if ( 'cover' == bgLayerData.size ){

								// CHANGE BACKGROUND SIZE

								var newBgWidth = thisElWidth;
								var newBgHeight = ( thisElWidth / bgLayerData.width ) * bgLayerData.height;

								var finalBgSize;

							 	if ( newBgHeight < ( fgWinHeight - ( fgWinHeight - thisElHeight ) * ( bgLayerData.speed / 100 ) ) ){
									newBgHeight = ( fgWinHeight - ( fgWinHeight - thisElHeight ) * ( bgLayerData.speed / 100 ) );
									newBgWidth = ( bgLayerData.width / bgLayerData.height ) * newBgHeight;
								}

								finalBgSize = newBgWidth + 'px ' + newBgHeight + 'px';
								
								$bgLayer.css('background-size', finalBgSize);

								// CHANGE BACKGROUND POSITION

								var calcBgPosX = thisElLeft - ( ( newBgWidth - thisElWidth ) / 2 ) + 'px';
								var calcBgPosY = ( thisElTop - winScrollTop ) * ( bgLayerData.speed / 100 ) + 'px';

								var finalBgPos = calcBgPosX + ' ' + calcBgPosY;

								$bgLayer.css('background-position', finalBgPos);

								// ATTACH BACKGROUND IMAGE

								if ( $bgLayer.css('background-image') ){
									$bgLayer.css('background-image', "url('" + bgLayerData.url + "')");
								}

							}

						}

					} else {

						$bgLayer.removeClass('parallax-on').addClass('parallax-off');

						$bgLayer.css('background-position', '');
						$bgLayer.css('background-size', '');

						if ( $bgLayer.css('background-image') ){
							$bgLayer.css('background-image', "url('" + bgLayerData.url + "')");
						}

					}

				}

				function fgParallaxInit(){

					if ( fgBreakpoint >= 3 ) {
					
						fgParallax();

					} else {

						$bgLayer.removeClass('parallax-on').addClass('parallax-off');
						
						$bgLayer.css('background-image', "url('" + bgLayerData.url + "')");

					}

				}

				var thisElWidth, thisElHeight, thisElTop, thisElLeft, bgLayerHeight;

				fgParallaxInit();

			}

			this.each(function() {
				var $this_el = $(this);
				var bgData = JSON.parse($this_el.attr('data-fg-bg'));

				var len, i, bgLayerData, $bgLayer, $bgLayers;

				if ( '' == bgData ){
					return;
				}

				$bgLayers = $('<div class="fg-bg"></div>');
				$this_el.prepend($bgLayers);

				for (i = 0, len = bgData.length; i < len; i++) {
					bgLayerData = bgData[i];
					$bgLayer = $('<div></div>');
					$bgLayer.addClass('fg-bg-layer fg-bg-type-' + bgLayerData.type);

					$bgLayer.css('opacity', bgLayerData.opacity);

					switch (bgLayerData.type){
						case 'color': bg_type_color(bgLayerData, $bgLayer, $this_el); break;
						case 'image': bg_type_image(bgLayerData, $bgLayer, $this_el); break;
						case 'video': bg_type_video(bgLayerData, $bgLayer, $this_el); break;
						case 'parallax': bg_type_parallax(bgLayerData, $bgLayer, $this_el); break;
					}

					$bgLayers.append($bgLayer);

				}
			});

			return this;
		};

		$('[data-fg-bg]').fgBackground();

	});

	// WINDOW LOAD+RESIZE

	$(window).on('load resize', function() {
		calcFgBreakpoint();
		calcFgWinWidth();
		calcFgWinHeight();
	});
		
		

})(jQuery);




