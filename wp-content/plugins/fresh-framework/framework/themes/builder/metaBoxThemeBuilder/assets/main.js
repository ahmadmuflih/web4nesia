(function($){
    if( window.ffbuilder == undefined ) {
        window.ffbuilder = {};
    }


/**********************************************************************************************************************/
/* APP
/**********************************************************************************************************************/
    window.ffbuilder.App = Backbone.View.extend({

        /**
         * Element Picker View
         */
        elementPickerView: null,

        /**
         * All menu items
         */
        menuItems: null,

        /**
         * This variable contains data about every element
         */
        elementModels: null,

        /**
         * Currently selected (opened options) element view
         */
        selectedElementView: null,


        /*----------------------------------------------------------*/
        /* BIND ACTIONS
        /*----------------------------------------------------------*/
        /**
         * All dynamic actions binding and interaction with dome
         */
        bindActions: function() {
            var $el = this.$el;
            var self = this;

            /**
             * Click on edit button at every section
             */
            $el.on('click','.ffb-header__button.action-edit-element, .ffb-header, .ffb-header-name', function(e){
                if(e.target != this) return;

                self.selectedElementView = self._createElementViewFromElement( $(this) );
                self.selectedElementView.renderOptionsForm();

                self.vent.f.modalShow();
            });

            $(document).on('click', '.ffb-modal__action-cancel, .ffb-modal__button-cancel', function(e){
                if(e.target != this) return;
                
                self.selectedElementView = null;
                self.vent.f.modalHide();
                self.vent.f.modalSetContent('');

                return false;
            });

            $(document).on('click', '.ffb-modal__action-save', function() {
                self.vent.f.modalHide();
                self.selectedElementView.saveOptionsForm();
                self.selectedElementView.clearOptionsForm();
                //saveOptionsForm
                //alert ('sadasd');
                self.selectedElementView = null;


                return false;
            });

            $(document).on('click', '.action-toggle-context-menu', function(){
				var confirmation = confirm('Do you really want to delete this alement and everything inside?');

				if( confirmation ) {

					$(this).parents('.ffb-element:first').hide(1000, function(){

						$(this).remove();
						self.writeCanvasToPostContentArea();

					})

				}
			});

            $(document).on('click', '.action-add-element', function(){

                self.selectedElementView = self._createElementViewFromElement( $(this) );
                self.selectedElementView.renderAddForm();

            });

            // when canvas is changed, convert to shortcodes and fill it into WP editor
            this.vent.listenTo(this.vent, this.vent.a.canvasChanged, function(){
                self.writeCanvasToPostContentArea();
            });

            // when all elements data are loaded, we are going to refresh the canvas
            // to render the preview of all elements
            this.vent.listenTo(this.vent, this.vent.a.elementsDataLoaded, function(){
                self.refreshElementsPreview( self.$el );
                self.elementPickerView = new window.ffbuilder.ElementPickerView({vent: self.vent, elementModels: self.elementModels, menuItems: self.menuItems});

                //this.elementModels = options.elementModels;
                //this.menuItems = options.menuItems;
            });
        },


        /*----------------------------------------------------------*/
        /* INITIALIZE - constructor
        /*----------------------------------------------------------*/
        /**
         * First initialization
         */
        initialize: function() {
            this.vent = this._getVent();
            this.$el = $('.ffb-canvas');


            this.init();
            this.bindActions();
        },

        /*----------------------------------------------------------*/
        /* INIT
        /*----------------------------------------------------------*/
        /**
         * First call - after loading the builder, we need to initialise every js functions, connect JS things with the
         * canvas and all other stuff
         */
        init: function(){
            this.loadElementsData();
            this.connectElements( this.$el );

        },

        refreshElementsPreview: function ( $elements ) {
            var self = this;

            if( $elements.hasClass('ffb-element' ) ) {
                self._createElementViewFromElement( $elements ).renderContentPreview();
            }
            $elements.find('.ffb-element').each(function(){
                self._createElementViewFromElement( $(this) ).renderContentPreview();
            });
        },


        /*----------------------------------------------------------*/
        /* LOAD ELEMENTS DATA
        /*----------------------------------------------------------*/
        /**
         * load data about all builder elements and create backboneJS models from them, for better rendering and
         * everything next time
         * @param $elements
         */
        loadElementsData :function() {
            var self = this;
            this.elementModels = [];
            this.menuItems = null;
            this._metaBoxAjax( this.vent.ajax.getElementsData, {}, function( response ){
                var data = JSON.parse( response );

                // information about all menu items in the "Add" section
                self.menuItems = data.menuItems;

                var elementsData = data.elements;
                var key = null;
                for( key in elementsData ) {
                    var oneElement = elementsData[ key ];
                    var elementModel = new window.ffbuilder.ElementModel();

                    for( var attr in oneElement ) {
                        elementModel.set( attr, oneElement[attr]);
                    }

                    elementModel.set('optionsStructure', JSON.parse( elementModel.get('optionsStructure') ) );
                    elementModel.processFunctions();

                    self.elementModels[ key ] = elementModel;



                }
                self.vent.trigger(self.vent.a.elementsDataLoaded);
            });

            ;
        },



        /*----------------------------------------------------------*/
        /* writeCanvasToPostContentArea
        /*----------------------------------------------------------*/
        /**
         * Converts all our shortcodes to canvas and then write it
         * to post content area
         */
        writeCanvasToPostContentArea: function() {
            var canvasShortcodeNotation = this.convertToShortcodes( this.$el );

            this._setTinyMCEContent( canvasShortcodeNotation );
        },




        /*----------------------------------------------------------*/
        /* convertToShortcodes
        /*----------------------------------------------------------*/
        /**
         * Convert given elements to ShortCodes notation
         * @param $elements
         */
        convertToShortcodes_data: '',
        convertToShortcodes_depth: 0,

        convertToShortcodes: function ( $data ) {
            this.convertToShortcodes_depth = 0;
            this.convertToShortcodes_data = '';
            if( $data.hasClass('ffb-element') ) {

            } else if ($data.hasClass('ffb-dropzone') ) {

            } else {
                var $elements = $data.children('.ffb-element');
                var self = this;

                $elements.each(function(){
                    self.convertToShortcodes_Element( $(this) );
                });
            }

            var toReturn = this.convertToShortcodes_data;
            this.convertToShortcodes_data = '';
            return toReturn;
        },

        convertToShortcodes_encodeAttribute: function( attribute ) {

            attribute = encodeURI( attribute );

            attribute = attribute.split('%20').join(' ');


            return attribute;
        },

        convertToShortcodes_Element: function( $element ) {
            var self = this;

            var elementId = $element.attr('data-element-id');
            //var dataString = $element.attr('data-options');


            var elementModel = this._createElementViewFromElement($element );
            var elementData = elementModel.convertOptionsToShortcodes();

            // options, shortcodes

            var dataString = JSON.stringify( elementData.options );

            //var walker = frslib.options.walkers.toScContentConvertor();
            //walker.setDataJSON( data );
            //walker.setStructureJSON( this.model.get('optionsStructure'));
            //console.log( walker.walk());

            //console.log( dataString );
            var data = this.convertToShortcodes_encodeAttribute( dataString );
            if( data == 'null' ) {
                data = '';
            }
            var dataAttr = 'data="' + data + '"';

            var uniqueID = $element.attr('data-unique-id');


            this.convertToShortcodes_data += '[ffb_' + elementId +'_' + this.convertToShortcodes_depth + ' unique_id="' + uniqueID + '" ' + dataAttr + ']';
                this.convertToShortcodes_data += elementData.shortcodes;
                $element.children('.ffb-dropzone').each(function(){
                    self.convertToShortcodes_Dropzone( $(this) );
                });

            this.convertToShortcodes_data += '[/ffb_' + elementId +'_' + this.convertToShortcodes_depth + ']';

        },

        convertToShortcodes_Dropzone: function( $dropzone ) {
            var self = this;
            this.convertToShortcodes_depth++;
            $dropzone.children('.ffb-element').each(function(){
                self.convertToShortcodes_Element( $(this) );
            });
            this.convertToShortcodes_depth--;
        },

        /*----------------------------------------------------------*/
        /* CONNECT ELEMENTS
        /*----------------------------------------------------------*/
        /**
         * function called on every new element, which wants to be added into the canvas - do some js hooks
         * @param $elements
         */
        connectElements: function( $elements ) {

            var $dropzones = $elements.find('.ffb-dropzone');

            if( $dropzones.size() > 0 ) {
                this.initSortableOnDropzones( $dropzones );
            }
        },

        reInitSortableOnDropzones: function( $dropzones)  {
            this.initSortableOnDropzones( $dropzones );
            this.$el.find('.ffb-dropzone').sortable('refresh');
        },

        initSortableOnDropzones: function( $dropzones ) {
            var self = this;

            $dropzones.each(function(){
                var $this = $(this);
                var $element = $(this).parents('.ffb-element:first');
                var connectWith = '.ffb-dropzone';

                $(this).sortable({
                connectWith: connectWith,
                cursor: 'move',
                tolerance: 'pointer',
                    //items: function(a,b,c){
                    //  alert('s');
                    //},
                // helper: 'clone',
                // cursorAt: { left: -10, top: -10 },

                placeholder: {
                    element: function($currentItem ) {
                        var $placeholder = $currentItem.clone().html('').addClass('ui-sortable-placeholder').css('position','').css('width','');

                        $placeholder.attr( 'data-element-id', $currentItem.attr('data-element-id') );
                        // if( $currentItem.hasClass('ffb-element--position--block') ) {
                        //     $placeholder.addClass('ffb-element-sortable-placeholder--position--block');
                        // } else if ( $currentItem.hasClass('ffb-element--position--float') ) {
                        //     $placeholder.addClass('ffb-element-sortable-placeholder--position--float');
                        // }
                        // $placeholder.addClass('ffb-element-sortable-placeholder--position--float');
                        return $placeholder;

                     },
                     update: function( event, $placeholder) {

                         var $parent = $placeholder.parents('.ffb-element:first');

                         var canBeDropped = self.canBeDropped( $parent, $placeholder );

                         if( canBeDropped ) {
                             // $placeholder.removeClass('ui-sortable-placeholder--cant-be-dropped');
                             // $placeholder.css('display', 'block');

                         } else {
                             // $placeholder.addClass('ui-sortable-placeholder--cant-be-dropped');
                             // $placeholder.css('display', 'none');

                         }
                         return false;
                     }
                },

                // helper: {
                //     element: function($currentItem ) {
                //         var $helper = $currentItem.clone().html('').addClass('ui-sortable-placeholder').css('position','').css('width','');
                //         return $helper;

                //      }
                // },

               stop: function (event, ui) {

                   var $droppedElement = ui.item;
                   var $elementWithDropzone = $droppedElement.parents('.ffb-element:first');

                   if( self.canBeDropped( $elementWithDropzone,$droppedElement )  ) {
                       self.vent.trigger(self.vent.a.canvasChanged);
                   } else {
                       $this.sortable('cancel');
                   }
               }
            });


            });
//            $dropzones.
        },

        canBeDropped: function( $elementWithDropzone, $droppedElement ) {
            var droppedElementId = $droppedElement.attr('data-element-id');
            var dropzoneMode = $elementWithDropzone.attr('data-dropzone-mode');

            if( dropzoneMode == undefined ) {
                return true;
            }

            var dropzoneList = JSON.parse($elementWithDropzone.attr('data-dropzone-list'));


            if( dropzoneMode == 'whitelist' ) {

                if ( $.inArray( droppedElementId, dropzoneList ) != -1) {
                    return true;
                } else {
                    return false;
                }

            } else if (dropzoneMode == 'blacklist') {
                if ( $.inArray( droppedElementId, dropzoneList ) == -1) {
                    return true;
                } else {
                    return false;
                }
            }
        },

        /*----------------------------------------------------------*/
        /* EVENTS
        /*----------------------------------------------------------*/
        /**
         * Generate our event class, which stores all event names as as "pseudo constants"
         * This class is basically backbone of our eventing system
         * @private
         */
        _getVent: function() {
            var self = this;
            var vent = _.extend({}, Backbone.Events);

            vent.ajax = {};
            vent.ajax.getElementsData = 'getElementsData';

            vent.a = {};
            vent.a.canvasChanged = 'canvasChanged';
            vent.a.elementsDataLoaded = 'elementsDataLoaded';


            vent.f = {};

            vent.f.connectElement = function( $element ) {
                self.connectElements( $element );
                self.refreshElementsPreview( $element );
                self.writeCanvasToPostContentArea();
            };

            vent.f.modalShow = function(){
                console.log( 'show ');
                $.scrollLock();
                $('.ffb-modal-origin').css('display', 'block');
            };

            vent.f.modalHide = function() {
                $.scrollLock();
                $('.ffb-modal-origin').css('display','none');
            };

            vent.f.modalSetContent = function( content ) {
                $('.ffb-modal-origin').find('.ffb-modal__body').html( content);
            };



            return vent;
        },

        _setTinyMCEContent: function( content ) {
            var activeEditor = tinyMCE.get(wpActiveEditor);

            if( activeEditor ==undefined ) {
                $('.wp-editor-area').val( content );
            } else {
                activeEditor.setContent(content);
            }

                ///wp-editor-area
            //console.log( wpActiveEditor, tinyMCE,  tinyMCE.get(wpActiveEditor) );
            //tinyMCE.get(wpActiveEditor).setContent(content);
        },

        /*----------------------------------------------------------*/
        /* metaBoxAjax
        /*----------------------------------------------------------*/
        /**
         * Ajax request to our meta box "Theme Builder"
         * @param action
         * @param data
         * @param callback
         * @private
         */
        _metaBoxAjax: function( action, data, callback ) {
            data.action = action;
            var specification = {};
			specification.metaboxClass = 'ffMetaBoxThemeBuilder';
            frslib.ajax.frameworkRequest( 'ffMetaBoxManager', specification, data, function( response ) {
                callback( response );
            });
        },

        _createElementViewFromElement: function( $element ) {

            if( !$element.hasClass('ffb-element') ) {
                $element = $element.parents('.ffb-element:first');
            }

            var elementId = $element.attr('data-element-id');


            var view = this._createElementViewFromId( elementId );
            view.$el = $element;

            return view;
        },

        _createElementViewFromId: function( elementId ) {
            var view = new window.ffbuilder.ElementView();
            view.elementPickerView = this.elementPickerView;
            view.model = this._createElementModelFromId( elementId );
            view.vent = this.vent;
            view.elementModels = this.elementModels;

            return view;
        },

        _createElementModelFromId: function( elementId ) {
            var model = this.elementModels[ elementId ];
            var modelCopy = model.clone();
            modelCopy.vent = this.vent;

            return modelCopy;
        },

    });


    $(document).ready(function(){
        new window.ffbuilder.App();
    });

})(jQuery);