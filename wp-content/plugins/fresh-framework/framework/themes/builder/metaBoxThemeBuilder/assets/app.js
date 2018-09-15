(function($, builder){

    window.ffbuilder.App = Backbone.View.extend({
    /**********************************************************************************************************************/
    /* INIT
    /**********************************************************************************************************************/
        xrayMode: 'off',
        blockDraggableOverlay: 'off',

        /*----------------------------------------------------------*/
        /* BINDING ACTIONS
        /*----------------------------------------------------------*/
        bindActions: function() {
            var self = this;



            $(document).bind('keydown', 'a x r e v b', function( event ){

                event.builderOrigin = 'window';
                self.vent.trigger( self.vent.a.keydown, event );
            });

            // context menu located in iframe sending us messages here
            $('body').on('iframe-contextMenu', function( event, data ){
                switch( data.action ) {
                    case 'delete':
                        self.vent.trigger( self.vent.a.contextMenu_delete, data);
                        break;

                    case 'duplicate':
                        self.vent.trigger( self.vent.a.contextMenu_duplicate, data);
                        break;
                }
            });


            this.vent.listenTo( this.vent, this.vent.a.keydown, function( event ){
                switch( event.keyCode ) {


                    // "a"
                    case 65:
                        self.vent.f.toggleBlockDraggableOverlay();
                        break;

                    case 66:
                        self.vent.f.toggleBoxModelMode();
                        break;
                    // "x"
                    case 88:
                        self.vent.f.toggleXrayMode();
                        break;

                    // "r"
                    case 82:
                        self.vent.f.toggleCanvasBreakpoint('next');
                        break;

                    // "e"
                    case 69:
                        self.vent.f.toggleCanvasBreakpoint('prev');
                        break;

                    // "v"
                    case 86:
                        $('.fftm-canvas__editor-mode--toggle').click();
                        break;
                }
            });


            this.listenTo( this.vent, this.vent.a.xrayMode_on, this.xrayModeOn);
            this.listenTo( this.vent, this.vent.a.xrayMode_off, this.xrayModeOff);

            this.listenTo( this.vent, this.vent.a.toggleBlockDraggableOverlay_on, this.addNewBlockOverlayOn);
            this.listenTo( this.vent, this.vent.a.toggleBlockDraggableOverlay_off, this.addNewBlockOverlayOff);

            this.listenTo( this.vent, this.vent.a.toggleBlockDraggableOverlay_on, this.boxModelModeOn);
            this.listenTo( this.vent, this.vent.a.toggleBlockDraggableOverlay_off, this.boxModelModeOff);

            this.listenTo( this.vent, this.vent.a.loader_canvas_on, this.loaderCanvasOn);
            this.listenTo( this.vent, this.vent.a.loader_canvas_off, this.loaderCanvasOff);
        },

        /*----------------------------------------------------------*/
        /* INIT
        /*----------------------------------------------------------*/
        initialize: function() {
            this.vent = this._getVent();
            window.vent = this.vent;
            this.blockManager = new builder.BlockManager({ vent:this.vent });
            this.dataManager = new builder.DataManager({vent:this.vent});
            this.ajaxManager = new builder.AjaxManager({vent:this.vent});
            this.notificationManager = new builder.NotificationManager({vent:this.vent});
            this.revisionManager = new builder.RevisionManager({vent:this.vent});
            this.vent.o.dataManager = this.dataManager;

            this.vent.o.ajaxManager = this.ajaxManager;



            this.iframeView = new builder.IframeView({ vent:this.vent, blockManager:this.blockManager });
            this.editorView = new builder.EditorView({ vent:this.vent, blockManager:this.blockManager });
            this.bindActions();

        },
    /**********************************************************************************************************************/
    /* CONTENT
    /**********************************************************************************************************************/
        /*----------------------------------------------------------*/
        /* LOADER
        /*----------------------------------------------------------*/
        // canvas
        loaderCanvasOn: function() {
            //.fftm-canvas__show-loader--canvas
            $(document).find('html').addClass('fftm-canvas__show-loader--canvas');
        },

        loaderCanvasOff: function() {
            //.fftm-canvas__show-loader--canvas
            $(document).find('html').removeClass('fftm-canvas__show-loader--canvas');
        },


        /*----------------------------------------------------------*/
        /* ADD BLOCK OVERLAY
        /*----------------------------------------------------------*/
        addNewBlockOverlayOn: function() {
            var self = this;
            self.vent.d.blockDraggableOverlayAnimating = true;

            $(document).find('html').removeClass('fftm-canvas__add-new-block-overlay--off fftm-canvas__add-new-block-overlay--anim-exit--ended').addClass('fftm-canvas__add-new-block-overlay--on fftm-canvas__add-new-block-overlay--anim-enter');
            setTimeout(function() {
                $(document).find('html').removeClass('fftm-canvas__add-new-block-overlay--anim-enter').addClass('fftm-canvas__add-new-block-overlay--anim-enter--ended');
                self.vent.d.blockDraggableOverlayAnimating = false;
            }, 157 );

        },

        addNewBlockOverlayOff: function() {
            var self = this;
            self.vent.d.blockDraggableOverlayAnimating = true;
            $(document).find('html').removeClass('fftm-canvas__add-new-block-overlay--on fftm-canvas__add-new-block-overlay--anim-enter--ended').addClass('fftm-canvas__add-new-block-overlay--off fftm-canvas__add-new-block-overlay--anim-exit');
            setTimeout(function() {
                self.vent.d.blockDraggableOverlayAnimating = false;
                $(document).find('html').removeClass('fftm-canvas__add-new-block-overlay--anim-exit').addClass('fftm-canvas__add-new-block-overlay--anim-exit--ended');
            }, 157 );
        },

        /*----------------------------------------------------------*/
        /* XRAY MODE
        /*----------------------------------------------------------*/

        xrayModeOn : function() {
            var self = this;
            self.vent.d.xrayModeAnimating = true;
            $(document).find('html').removeClass('fftm-canvas__xray-mode--off fftm-canvas__xray-mode--anim-exit--ended').addClass('fftm-canvas__xray-mode--on fftm-canvas__xray-mode--anim-enter');
            setTimeout(function() {
                $(document).find('html').removeClass('fftm-canvas__xray-mode--anim-enter').addClass('fftm-canvas__xray-mode--anim-enter--ended');
                self.vent.d.xrayModeAnimating = false;
            }, 157 );
            //this.enableSortable();
        },

        //

        xrayModeOff : function() {
            var self = this;
            self.vent.d.xrayModeAnimating = true;
            $(document).find('html').removeClass('fftm-canvas__xray-mode--on fftm-canvas__xray-mode--anim-enter--ended').addClass('fftm-canvas__xray-mode--off fftm-canvas__xray-mode--anim-exit');
            setTimeout(function() {
                self.vent.d.xrayModeAnimating = false;
                $(document).find('html').removeClass('fftm-canvas__xray-mode--anim-exit').addClass('fftm-canvas__xray-mode--anim-exit--ended');
            }, 157 );
            //this.disableSortable();
        },

        /*----------------------------------------------------------*/
        /* BOX MODEL MODE
        /*----------------------------------------------------------*/
        boxModelModeOn: function() {
            $(document).find('html')
                .removeClass('fftm-canvas__box-model-mode--off')
                .addClass('fftm-canvas__box-model-mode--on');
        },

        boxModelModeOff: function(){
            $(document).find('html')
                .removeClass('fftm-canvas__box-model-mode--off')
                .addClass('fftm-canvas__box-model-mode--on')
        },
        /*----------------------------------------------------------*/
        /* ALL ACTIONS
        /*----------------------------------------------------------*/
        _getVent: function() {
            var vent = _.extend({}, Backbone.Events);




            // actions
            vent.a = {};



            /**
             * Overlay for adding new blocks
             * @type {string}
             */
            vent.a.toggleBlockDraggableOverlay_on = 'toggleBlockDraggableOverlay:on';
            vent.a.toggleBlockDraggableOverlay_off = 'toggleBlockDraggableOverlay:off';


            /**
             * Enable/disable x-ray mode, to make drag and drop more easier
             */
            vent.a.xrayMode_on = 'xrayMode:on';
            vent.a.xrayMode_off = 'xrayMode:off';

            /**
             * Box Model mode
             */
            vent.a.boxModelMode_on = 'boxModelMode:on';
            vent.a.boxModelMode_off = 'boxModelMode:off';


            /**
             * Context menu
             */
            vent.a.contextMenu_delete = 'contextMenu:delete';
            vent.a.contextMenu_duplicate= 'contextMenu:duplicate';



            /**
             * After the canvas iframe has been loaded
             */
            vent.a.canvasIframe_load = 'canvasIframe:load';
            vent.a.canvas_connectBlock = 'canvas:connectBlock';
            vent.a.canvas_blockSelected = 'canvas:blockSelected';

            vent.a.canvas_convertTo = 'canvas:convertTo';
            vent.a.canvas_blockOrderChanged = 'canvas:blockOrderChanged';
            vent.a.canvas_renderBoxModelOnBlockByUniqueId = 'canvas:renderBoxModelOnBlockByUniqueId';

            vent.a.canvas_moveBlock = 'canvas:moveBlock';

            vent.a.canvas_toggleResponsiveBreakpoint = 'canvas:toggleResponsiveBreakpoint';


            vent.a.canvas_couldWeRefresh = 'canvas:couldWeRefresh';
            vent.a.canvas_beforeRefresh = 'canvas:beforeRefresh';
            vent.a.canvas_refresh = 'canvas:refresh';

            vent.a.blockData_load = 'blockData:load';
            vent.a.block_formSubmit = 'block:formSubmit';

            vent.a.keydown = 'keydown';

            vent.a.notification_add = 'notification:add';


            vent.a.loader_canvas_on = 'loader:canvas:on';
            vent.a.loader_canvas_off = 'loader:canvas:off';




            /**
             * Variables
             * @type {{}}
             */
            vent.d = {};

            vent.d.blockDraggableOverlay = 'off';
            vent.d.boxModelMode = 'off';
            vent.d.xrayMode = 'off';
            vent.d.xrayModeAnimating = false;
            vent.d.blockDraggableOverlayAnimating = false;

            vent.d.canvas_couldWeRefresh = true;

            vent.d.responsiveModeBreakpoint = 'auto';

            vent.d.iframeLoaded = false;

            vent.d.focus = false;

            vent.d.loader_canvas = false;


            /*----------------------------------------------------------*/
            /* GLOBAL OBJECTS
            /*----------------------------------------------------------*/
            vent.o = {};

            /*----------------------------------------------------------*/
            /* constants
            /*----------------------------------------------------------*/
            vent.c = {};


            // responsive mode
            vent.c.responsiveMode = {};


            vent.c.responsiveMode.types = {};

            vent.c.responsiveMode.types.xs = 'xs';
            vent.c.responsiveMode.types.sm = 'sm';
            vent.c.responsiveMode.types.md = 'md';
            vent.c.responsiveMode.types.lg = 'lg';
            vent.c.responsiveMode.types.auto = 'auto';




            vent.c.responsiveMode.info = {};

            vent.c.responsiveMode.info.xs = {};
            vent.c.responsiveMode.info.xs.width = '480px';
            vent.c.responsiveMode.info.xs.name = 'Mobile';

            vent.c.responsiveMode.info.sm = {};
            vent.c.responsiveMode.info.sm.width = '768px';
            vent.c.responsiveMode.info.sm.name = 'Tablet';

            vent.c.responsiveMode.info.md = {};
            vent.c.responsiveMode.info.md.width = '992px';
            vent.c.responsiveMode.info.md.name = 'Laptop';

            vent.c.responsiveMode.info.lg = {};
            vent.c.responsiveMode.info.lg.width = '1200px';
            vent.c.responsiveMode.info.lg.name = 'Desktop';

            vent.c.responsiveMode.info.auto = {};
            vent.c.responsiveMode.info.auto.width = '100%';
            vent.c.responsiveMode.info.auto.name = 'Auto';



            /*----------------------------------------------------------*/
            /* EVENT FUNCTIONS
            /*----------------------------------------------------------*/
            vent.f = {};

            vent.f.couldWeRefresh = function() {
                vent.trigger( vent.a.canvas_couldWeRefresh );
                return vent.d.canvas_couldWeRefresh;
            };

            vent.f.refresh = function( url ) {
                if( vent.f.couldWeRefresh() ) {
                    vent.f.loaderCanvasEnable();
                    vent.trigger( vent.a.canvas_beforeRefresh, url );
                    vent.trigger( vent.a.canvas_refresh, url );

                    return true;
                } else {
                    return false;
                }
            };

            vent.f.changeUrl = function( url ) {
                if( vent.f.couldWeRefresh() ) {
                    vent.f.loaderCanvasEnable();
                    vent.trigger( vent.a.canvas_beforeRefresh, url );
                    //vent.trigger( vent.a.canvas_refresh, url );
                    window.location = url;

                    return true;
                } else {
                    return false;
                }
            }

            vent.f.loaderCanvasDisable = function() {
                vent.d.loader_canvas = false;
                vent.trigger( vent.a.loader_canvas_off );
            };

            vent.f.loaderCanvasEnable = function() {
                vent.d.loader_canvas = true;
                vent.trigger( vent.a.loader_canvas_on );
            };

            vent.f.pushNotification = function( type, text ) {
                vent.trigger( vent.a.notification_add, type, text );
            };

            vent.f.toggleBoxModelMode = function() {

               if( vent.d.boxModelMode == 'off' ) {
                   vent.trigger( vent.a.boxModelMode_on )
                   vent.d.boxModelMode = 'on';

               } else {
                   vent.trigger( vent.a.boxModelMode_off )
                   vent.d.boxModelMode = 'off';

               }
            };

            vent.f.toggleCanvasBreakpoint = function( type ) {

                if( type == 'next' ) {
                    var currentMode = vent.d.responsiveModeBreakpoint;
                    var types = vent.c.responsiveMode.types;

                    type = frslib.array.nextKey( types, currentMode );

                } else if( type == 'prev' ) {
                    var currentMode = vent.d.responsiveModeBreakpoint;
                    var types = vent.c.responsiveMode.types;

                    type = frslib.array.prevKey( types, currentMode );
                }

                vent.d.responsiveModeBreakpoint = type;
                vent.trigger( vent.a.canvas_toggleResponsiveBreakpoint, type )




            }

            vent.f.toggleXrayMode = function() {

                if( vent.d.xrayModeAnimating  ) {
                    return false;
                }

                if( vent.d.xrayMode == 'off' ) {
                    vent.trigger( vent.a.xrayMode_on );
                    vent.d.xrayMode = 'on';
                } else {
                    vent.trigger( vent.a.xrayMode_off );
                    vent.d.xrayMode = 'off';
                }
            };

            /*----------------------------------------------------------*/
            /* DRAGGABLE OVERLAY
            /*----------------------------------------------------------*/
            vent.f.toggleBlockDraggableOverlay = function() {
                if( vent.d.blockDraggableOverlayAnimating ) {
                    return false;
                }

                if( vent.d.blockDraggableOverlay == 'off' ) {
                    vent.d.blockDraggableOverlay = 'on';
                    vent.trigger( vent.a.toggleBlockDraggableOverlay_on );


                } else {
                    vent.d.blockDraggableOverlay = 'off';
                    vent.trigger( vent.a.toggleBlockDraggableOverlay_off );

                    ;
                }
            };

            return vent;
        },
    });

})(jQuery, window.builder );


(function($) {

    $.extend($.expr[':'], {
        contents: function(elem, i, attr){
          return $(elem).contents().find( attr[3] );
        }
    });

    // Using strict mode
    'use strict';

    /* PERFECT SCROLLBAR */

    $('.fftm-fe__panel--init-scroll').perfectScrollbar({
        suppressScrollX: true,
        includePadding: true
    });

    /* CONTEXT MENU */

    $.contextMenu({
        selector: '.fftm__option-type--repeatable-item',
        // reposition: false,
        animation: {duration: 0, show: 'show', hide: 'hide'},


                       // DO NOT DELETE !!!
                       // ONLY DISABLED AT THE MOMENT TO TEST QTIP DIALOG
                       // ACTION BELOW = CLOSE THE CONTEXTMENU AFTER CLICKING ON ANY ITEM
                       // callback: function(key, options) {
                       //     var m = "clicked: " + key;
                       //     window.console && console.log(m) || alert(m);
                       // },


        items: {
           "edit": {name: "Edit"},
           "cut": {name: "Cut"},
           "copy": {name: "Copy"},
           "paste": {name: "Paste"},
           "duplicate": {name: "Duplicate"},
           "fold1": {
               "name": "Settings",
               "items": {
                   "fold1-key1": {"name": "Copy"},
                   "fold1-key2": {"name": "Paste"}
               }
           },
           "sep1": "-------",
           "delete": {name: "Delete"}
        }
    });

    /* OPTION TYPE - LABEL SMALL - TOOLTIP */

    $('.fftm__option-type--label-small label').qtip({
        content: {
            attr: 'data-labeltip'
        },
        style: {
            tip: {
                corner: false
            },
            classes: 'fftm__option-type--label-small__tooltip'
        },
        position: {
            target: 'mouse',
            my: 'bottom left',  // tooltip
            at: 'top right' // container
        },
        show: {
            delay: 300,
            effect: false, // disable fading animation
        },
        hide: {
            effect: false, // disable fading animation
        }
    });

    /* QTIP DIALOG */

    $('.context-menu-item').qtip({
        content: {
            text: '<div class="fftm__fe-dialog__question">Are you really sure you want to delete this slide?</div><div class="fftm__fe-dialog__buttons fftm__fe-dialog__buttons--2 clearfix"><div class="fftm__fe-dialog__button">Yes</div><div class="fftm__fe-dialog__button">No</div></div>'
        },
        show: {
            event: 'click',
            effect: false, // disable fading animation
            modal: {
                on: true, // turn on modal plugin
                effect: false, // disable fading animation
                blur: true, // hide tooltip by clicking backdrop
                escape: true // hide tooltip when ESC pressed
            }
        },
        hide: {
            event: 'unfocus', // #bug: problem with contextmenu plugin that adds overlaying div that is ignored by qtip so it wont unfocus on first click, a second click is needed
            effect: false // disable fading animation
        },
        position: {
            target: 'mouse',
            adjust: {
                mouse: false // not following the mouse
            },
            viewport: $(window) // force tooltip to stay inside viewport
        },
        style: {
            tip: {
                corner: false
            },
            classes: 'fftm__fe-dialog'
        },
        events: {
            render: function(event, api) {
                // Grab the overlay element
                var elem = api.elements.overlay;
                // Add class
                elem.find('div').addClass('qtip-overlay-minimal');
            }
        }
    })

    /* RESPONSIVE MODE */



    /* OPTION TYPE - TABS */

    $('.fftm__option-type--tabs-item-name').click(function(e){
        $('.fftm__option-type--tabs-item-name').removeClass('fftm__option-type--tabs-item-name--active');
        $(this).addClass('fftm__option-type--tabs-item-name--active');
        $('.fftm__option-type--tabs-item-content--active').hide().fadeIn(300);
    });

    /* OPTION TYPE - SLIDER */

    $('.fftm__option-type--slider__slider').each(function(){

        var slider = $(this);
        slider.slider({
            range: "min",
            value: 3,
            step: 1,
            min: 0,
            max: 12,
            create: function( event, ui ) {
                slider.find('.ui-slider-handle').html('<div class="fftm__option-type--slider__tooltip">' + slider.closest('.fftm__option-type--slider').find('.fftm__option-type--slider__name').text() + '</div>');
            }
        });

    });

    $('.fftm__option-type--slider__applicable-icon').click(function(e){

        if ( $(this).closest('.fftm__option-type--slider').hasClass('fftm__option-type--slider__is-applied--yes') ){
            $(this).closest('.fftm__option-type--slider').removeClass('fftm__option-type--slider__is-applied--yes');
            $(this).closest('.fftm__option-type--slider').addClass('fftm__option-type--slider__is-applied--no')
        } else {
            $(this).closest('.fftm__option-type--slider').removeClass('fftm__option-type--slider__is-applied--no');
            $(this).closest('.fftm__option-type--slider').addClass('fftm__option-type--slider__is-applied--yes')
        }

    });

    /* OPTION TYPE - BOX MODEL MARGIN/PADDING/BORDER-RADIUS */

    $('.fftm__option-type--box-model-mpb__applicable-icon').click(function(e){

        if ( $(this).closest('.fftm__option-type--box-model-mpb__option').hasClass('fftm__option-type--box-model-mpb__is-applied--yes') ){
            $(this).closest('.fftm__option-type--box-model-mpb__option').removeClass('fftm__option-type--box-model-mpb__is-applied--yes');
            $(this).closest('.fftm__option-type--box-model-mpb__option').addClass('fftm__option-type--box-model-mpb__is-applied--no')
        } else {
            $(this).closest('.fftm__option-type--box-model-mpb__option').removeClass('fftm__option-type--box-model-mpb__is-applied--no');
            $(this).closest('.fftm__option-type--box-model-mpb__option').addClass('fftm__option-type--box-model-mpb__is-applied--yes')
        }

    });

    $('.fftm__option-type--box-model-mpb__lock-icon').click(function(e){

        if ( $(this).closest('.fftm__option-type--box-model-mpb').hasClass('fftm__option-type--box-model-mpb__is-locked--yes') ){
            $(this).closest('.fftm__option-type--box-model-mpb').removeClass('fftm__option-type--box-model-mpb__is-locked--yes');
            $(this).closest('.fftm__option-type--box-model-mpb').addClass('fftm__option-type--box-model-mpb__is-locked--no')
        } else {
            $(this).closest('.fftm__option-type--box-model-mpb').removeClass('fftm__option-type--box-model-mpb__is-locked--no');
            $(this).closest('.fftm__option-type--box-model-mpb').addClass('fftm__option-type--box-model-mpb__is-locked--yes')
        }

    });

    $('.fftm__option-type--box-model-mpb__slider').each(function(){

        var slider = $(this);

        slider.slider({
            range: "min",
            value: 100,
            step: 1,
            min: 0,
            max: 500
        }).find('.ui-slider-handle').removeAttr('tabindex'); // so you cant "TAB" into the slider handle

    });

    /* OPTION TYPE - BOX MODEL BORDER/SHADOW */

    // $('.fftm__option-type--box-model-bs__applicable-icon').click(function(e){

    //     if ( $(this).closest('.fftm__option-type--box-model-bs__option').hasClass('fftm__option-type--box-model-bs__is-applied--yes') ){
    //         $(this).closest('.fftm__option-type--box-model-bs__option').removeClass('fftm__option-type--box-model-bs__is-applied--yes');
    //         $(this).closest('.fftm__option-type--box-model-bs__option').addClass('fftm__option-type--box-model-bs__is-applied--no')
    //     } else {
    //         $(this).closest('.fftm__option-type--box-model-bs__option').removeClass('fftm__option-type--box-model-bs__is-applied--no');
    //         $(this).closest('.fftm__option-type--box-model-bs__option').addClass('fftm__option-type--box-model-bs__is-applied--yes')
    //     }

    // });

    $('.fftm__option-type--box-model-bs__show-tooltip').qtip({
        content: {
            attr: 'data-tooltip'
        },
        style: {
            tip: {
                corner: false
            },
            classes: 'fftm__option-type--box-model-bs__tooltip'
        },
        position: {
            // target: 'mouse',
            my: 'bottom center',  // tooltip
            at: 'top center' // container
        },
        show: {
            delay: 0,
            effect: false, // disable fading animation
        },
        hide: {
            effect: false, // disable fading animation
        }
    });


    /* SELECT MENU - JQUERY UI */

    $('.fftm select').selectmenu();


    /* NOTIFICATION SYSTEM */

    $('.spawn').click(function(e){

        if ( $(this).hasClass('info') ){

            $('.fftm-notify__list .fftm-notify__item-type--info').remove();

            $('.fftm-notify__list').append('<div class="fftm-notify__item fftm-notify__item--anim-enter fftm-notify__item-type--info"><div class="fftm-notify__item-content">Switched to Mobile View</div><i class="fftm-notify__item-close-button fftm-notify__item-type-icon fa fa-info-circle"></i></div>');

            var addedThis = $('.fftm-notify__list .fftm-notify__item:last-child');

            setTimeout(function() {

                addedThis.removeClass('fftm-notify__item--anim-enter').addClass('fftm-notify__item--anim-enter--ended');

                /* hide automatically after set timeout */
                setTimeout(function() {
                    addedThis.removeClass('fftm-notify__item--anim-enter--ended').addClass('fftm-notify__item--anim-exit');
                    setTimeout(function() {
                        addedThis.hide().removeClass('fftm-notify__item--anim-exit').addClass('fftm-notify__item--anim-exit--ended');
                    }, 327 );
                }, 673 );

            }, 327 );


        } else if ( $(this).hasClass('success') ){



        return;

            $('.fftm-notify__list .fftm-notify__item-type--success').remove();

            $('.fftm-notify__list').append('<div class="fftm-notify__item fftm-notify__item--anim-enter fftm-notify__item-type--success"><div class="fftm-notify__item-content">Saved!</div><i class="fftm-notify__item-type-icon fa fa-check-circle"></i></div>');

            var addedThis = $('.fftm-notify__list .fftm-notify__item:last-child');

            setTimeout(function() {

                addedThis.removeClass('fftm-notify__item--anim-enter').addClass('fftm-notify__item--anim-enter--ended');

                /* hide automatically after set timeout */
                setTimeout(function() {
                    addedThis.removeClass('fftm-notify__item--anim-enter--ended').addClass('fftm-notify__item--anim-exit');
                    setTimeout(function() {
                        addedThis.hide().removeClass('fftm-notify__item--anim-exit').addClass('fftm-notify__item--anim-exit--ended');
                    }, 327 );
                }, 673 );

            }, 327 );


        } else if ( $(this).hasClass('warning') ){

            $('.fftm-notify__list').append('<div class="fftm-notify__item fftm-notify__item--anim-enter fftm-notify__item-type--warning"><div class="fftm-notify__item-content">\'Column\' block can only be inside the \'Row\' block</div><i class="fftm-notify__item-close-button fftm-notify__item-type-icon fa fa-exclamation-triangle"></i></div>');

            var addedThis = $('.fftm-notify__list .fftm-notify__item:last-child');

            setTimeout(function() {
                addedThis.removeClass('fftm-notify__item--anim-enter').addClass('fftm-notify__item--anim-enter--ended');
            }, 327 );


        } else if ( $(this).hasClass('error') ){

            $('.fftm-notify__list').append('<div class="fftm-notify__item fftm-notify__item--anim-enter fftm-notify__item-type--error"><div class="fftm-notify__item-content">An error occured!</div><i class="fftm-notify__item-type-icon fa fa-times-circle"></i></div>');

            var addedThis = $('.fftm-notify__list .fftm-notify__item:last-child');

            setTimeout(function() {
                addedThis.removeClass('fftm-notify__item--anim-enter').addClass('fftm-notify__item--anim-enter--ended');
            }, 327 );

        }

    });

    /* SAVE BUTTON */

    //$('.fftm-fe__editor-footer-button-save').click(function(e){
    //    $('.spawn.success').click();
    //});

    /* SHOW/HIDE EDITOR */

    $('.fftm-canvas__editor-mode--toggle').click(function(e){

        if ( $('body').hasClass('fftm-canvas__editor-mode--on') ){

            $('body').removeClass('fftm-canvas__editor-mode--on fftm-canvas__editor-mode--anim-enter--ended').addClass('fftm-canvas__editor-mode--off fftm-canvas__editor-mode--anim-exit');
            setTimeout(function() {
                $('body').removeClass('fftm-canvas__editor-mode--anim-exit').addClass('fftm-canvas__editor-mode--anim-exit--ended');
            }, 157 );

        } else {

            $('body').removeClass('fftm-canvas__editor-mode--off fftm-canvas__editor-mode--anim-exit--ended').addClass('fftm-canvas__editor-mode--on fftm-canvas__editor-mode--anim-enter');
            setTimeout(function() {
                $('body').removeClass('fftm-canvas__editor-mode--anim-enter').addClass('fftm-canvas__editor-mode--anim-enter--ended');
            }, 157 );

        }

    });

    $('.fftm-open-editor-button').click(function(e){
        $('.fftm-canvas__editor-mode--toggle').click();
    });

    /* REVISIONS LIST */

    var revisionsListTable = $('.fftm__option-type--revisions-list__table').DataTable({
        // paging: false,
        dom: '<"fftm__option-type--revisions-list__header clearfix"f>tpi', // http://datatables.net/reference/option/dom
        pageLength: 10,
        lengthMenu: [10,25,50],
        // Language: {
        //     sSearch: '<i class="fa fa-search"></i>'
        // },
        language: {
            paginate: {
                previous: '&larr;',
                next: '&rarr;'
            },
            search: '_INPUT_ <i class="fa fa-search"></i>',
            searchPlaceholder: 'Search',
            lengthMenu: '<i class="fa fa-eye"></i> _MENU_'
        },
        // conditionalPaging: true,  // requires dataTables plugin in order to work
        conditionalPaging: {  // requires dataTables plugin in order to work
            style: 'fade',
            speed: 500
        },
        pagingType: 'numbers'
        // 'fnDrawCallback': function ( oSettings ){
        //     console.log( oSettings.fnRecordsTotal() );
        //     if(oSettings.fnRecordsTotal() < 6){
        //         $('.dataTables_length').hide();
        //         $('.dataTables_paginate').hide();
        //     } else {
        //         $('.dataTables_length').show();
        //         $('.dataTables_paginate').show();
        //     }
        // }
    });

    // $('.fftm__option-type--revisions-list__search-input').on('keyup click', function() {
    //     revisionsListTable.search(this.value).draw();
    // });

    $('.fftm__option-type--revisions-list__item').click(function(e){
        $('.fftm__option-type--revisions-list__item').removeClass('fftm__option-type--revisions-list__item--active');
        $(this).addClass('fftm__option-type--revisions-list__item--active');
    });


})(jQuery);


















