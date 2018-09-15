/**
 * Theme JavaScript of the Beacon HS helper
 *
 * ----------------------------------------------------------------------
 * Beacon is in-app helper and documentation search tool from HelpScout
 * This tool will help our clients to quickly find answers to their
 * questions in the theme documentation and contact us for support if needed.
 * http://www.helpscout.net/features/beacon/
 *
 */

!function(e,o,n){
	"use strict";

		if (self==top) { // make sure we are not loading beacon in iframe

			window.HSCW=o,
			window.HS=n,
			n.beacon=n.beacon||{};
			var t=n.beacon;
			t.userConfig={},
			t.readyQueue=[],
			t.config=function(e){
					this.userConfig=e
			},
			t.ready=function(e){
					this.readyQueue.push(e)
			},
			o.config={
					color: '#0073AA',
					poweredBy: false,
					docs:{
							enabled:!0,
							baseUrl:"//lumberman.helpscoutdocs.com/"
					},
					contact:{
							enabled:!0,formId:"6708f3b4-8d01-11e5-9e75-0a7d6919297d"
					}
			};
			var r=e.getElementsByTagName("script")[0],
			c=e.createElement("script");
			c.type="text/javascript",
			c.async=!0,
			c.src="https://djtflbt20bdde.cloudfront.net/",
			r.parentNode.insertBefore(c,r)
		}
}
(document,window.HSCW||{},window.HS||{});

if ( typeof HS !== 'undefined' ) {
	HS.beacon.ready(function() {
		"use strict";
		HS.beacon.identify({
			// 'PHP Information': beaconGetParametrs.get_php_information,
			'Purchase Code': beaconGetParametrs.purchase_code,
			// 'WP Version': beaconGetParametrs.wp_version,
			// 'Theme Version': beaconGetParametrs.update_theme,
			// 'Permalink': beaconGetParametrs.get_permalink,
			// 'Active Plugins': beaconGetParametrs.get_all_plugins
		});
	});
}



/**
 * ----------------------------------------------------------------------
 * Get Url Page
 */

var URL = window.location.pathname;
var current_page = URL.substring(URL.lastIndexOf('/') + 1);

var getUrlParameter = function getUrlParameter(sParam) {
	"use strict";
	 var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			 sURLVariables = sPageURL.split('&'),
			 sParameterName,
			 i;

	 for (i = 0; i < sURLVariables.length; i++) {
			 sParameterName = sURLVariables[i].split('=');

			 if (sParameterName[0] === sParam) {
					 return sParameterName[1] === undefined ? true : sParameterName[1];
			 }
	 }
};

if ( typeof HS !== 'undefined' ) {

	console.info( current_page );

	switch( current_page ) {
			case 'themes.php':

					if ( getUrlParameter('page') == 'install-required-plugins' ) {

						// Screen: WP Admin > Install Required Plugins
						HS.beacon.ready(function() {
							this.open();
							this.search('required plugins, update premium plugins');
							this.close();
						});

					} else {

						// Screen: WP Admin > Themes
						HS.beacon.ready(function() {
							this.open();
							this.search('theme installation process, plugins, demo content');
							this.close();
						});

						// HS.beacon.suggest([
						//     '524db818e4b0c2199a391f34',
						//     '525d5a44e4b0a3224aa066b2',
						//     '52a603d4e4b010488044bc1a',
						//     '545000abe4b07fce1b00cab1'
						// ]);

					}

					break;

			case 'plugins.php':
					HS.beacon.ready(function() {
						this.open();
						this.search('plugins, Plugins Documentation, update premium plugins');
						this.close();
					});
					break;

			case 'lbmn_footer':
					HS.beacon.ready(function() {
						this.open();
						this.search('footer');
						this.close();
					});
					break;

			case 'page':
					HS.beacon.ready(function() {
						this.open();
						this.search('edit pages, duplicate page, copy a page');
						this.close();
					});
					break;

			default:
					HS.beacon.ready(function() {
						this.close();
					});
	}

}