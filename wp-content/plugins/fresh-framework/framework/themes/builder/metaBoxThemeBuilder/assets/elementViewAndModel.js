(function($){
    if( window.ffbuilder == undefined ) {
        window.ffbuilder = {};
    }


/**********************************************************************************************************************/
/* ELEMENT VIEW
/**********************************************************************************************************************/
    /**
     * Element View, taking care about rendering the element and manipulating with it
     */
    window.ffbuilder.ElementView = Backbone.View.extend({
        model : null,
        query: null,
        elementModels: null,
        elementPickerView: null,

        initialize: function() {
        },

        test : function() {
            this.$el.css('opacity', 0.5);
        },

        /**
         * Render option form and set it as a content in the modal window
         */
        renderOptionsForm: function() {
            var walker = frslib.options.walkers.printerBoxed();

            //console.log( this.model );

			// console.log ( this.getOptionsData().split('&quot;').join("\\&quot;") );

            var html = '';
            html += '<div class="ff-options-js-wrapper " data-print-copy-and-paste="false">';
            html += '<div class="ff-options-js-data-wrapper" style="display:none;">';
                html += '<textarea class="ff-options-structure-js"></textarea>';
                html += '<textarea class="ff-options-data-js"></textarea> ';
                html += '<div class="ff-options-prefix">elementData</div>';
            html += '</div>';
            html += '<div class="ff-options-js">';
                //html += '<span class="spinner"></span>';
            html += '</div>';
            html += '</div>';

            //html += '<input type="submit" value="save element" class="ffb-save-element">';

            var $html = $(html);

			// var optionsData = this.getOptionsData();

			// console.log( optionsData );

			$html.find('.ff-options-data-js').data('data-json',  this.getOptionsData() );
			$html.find('.ff-options-structure-js').data('data-json',  JSON.stringify( this.model.get('optionsStructure') ) );



			// ( $html.find('.ff-options-data-js').data('data-json') );




            frslib.options.functions.initOneOptionSet( $html, true )

            var uniqueId = this.getUniqueId();
            var uniqueIdClass = this.generateUniqueIdClass( uniqueId );
            var uniqueIdSelector = '.' + uniqueIdClass;

             $html.find('.ff-insert-unique-id').html( uniqueId).val( uniqueId);
            $html.find('.ff-insert-unique-css-class').html( uniqueIdClass).val( uniqueIdClass);
            $html.find('.ff-insert-unique-css-selector').html( uniqueIdSelector).val( uniqueIdSelector);

            //ff-insert-unique-css-class
            //ff-insert-unique-css-selector

            this.vent.f.modalSetContent( $html );
        },

        generateUniqueId: function() {
            var number = new Date().getTime() -  new Date('2016-01-01').getTime();
            return number.toString(32);
        },

        generateUniqueIdClass: function( uniqueId ) {
            if( uniqueId == undefined ) {
                uniqueId = this.generateUniqueId();
            }

            return 'ffb-id-' + uniqueId;
        },

        renderAddForm: function() {
            var $addElementHtml = this.elementPickerView.renderAddFormHtml();

            this.vent.f.modalSetContent( $addElementHtml );
            this.vent.f.modalShow();

            var self = this;
            this.elementPickerView.callback_ItemSelected = function( $item ) {
                var itemId = $item.attr('data-id');

                var newItemModel = self.elementModels[ itemId ];

                var $newItemHTML = $(newItemModel.get('defaultHtml'));

                var uniqueId = self.generateUniqueId();
                var uniqueIdClass = self.generateUniqueIdClass( uniqueId );

                $newItemHTML.css('opacity', 0);

                //self.$el.find('.ffb-dropzone:first').append( $newItemHTML);

                $newItemHTML.appendTo( self.$el.find('.ffb-dropzone:first') );
                $newItemHTML.animate({opacity:1}, 300);
                $newItemHTML.attr('data-unique-id', uniqueId );
                $newItemHTML.addClass(uniqueIdClass);

                self.vent.f.modalHide();

                //console.log( $newItemHTML, newItemModel.get('defaultHtml') );

                //setTimeout(function(){
                //    console.log( $newItemHTML);
                    self.vent.f.connectElement($newItemHTML);
                //}, 1000);
            };
        },

        /**
         * saveOptionsForm
         * Saves the form into element data-attr directly
         */
        saveOptionsForm: function() {
            var $form = $('.ffb-modal').find('.ff-options-js');

            //function( $form, returnForm, deleteOriginalForm, ignoreDefaultValues )
            var data = frslib.options.template.functions.normalizeFast( $form );
            var dataString = JSON.stringify(data.elementData);
            this.$el.attr('data-options', dataString);

            var query = this.getOptionsQuery().get('o gen');

            var renderContentInfo = this.model.get('functions.renderContentInfo_JS');
            renderContentInfo(query, data.elementData, this.$el.children('.ffb-element-preview'), this.$el );



            this.vent.trigger(this.vent.a.canvasChanged);
        },

        /**
         * convertOptionsToShortcodes
         * Some of the options are meant to be printed as a content. This is because of search and UTF8 characters.
         * This function divides the data to the data-attr part and content part
         * @returns {{}}
         */
        convertOptionsToShortcodes: function() {
            var walker = frslib.options.walkers.toScContentConvertor();
            walker.setDataJSON( this.getOptionsDataJSON() );
            walker.setStructureJSON( this.model.get('optionsStructure'));
            var toReturn = {};
            toReturn.options = ( walker.walk());
            toReturn.shortcodes = walker.contentOutput;

            return toReturn;
        },

        /**
         * Each element has option to render content preview, containing some important data
         */
        renderContentPreview: function() {
            var query = this.getOptionsQuery().get('o gen');
            var data = this.getOptionsDataJSON();

            var renderContentInfo = this.model.get('functions.renderContentInfo_JS');
            renderContentInfo(query, data, this.$el.children('.ffb-element-preview'), this.$el );
        },

        clearOptionsForm: function() {
            this.vent.f.modalSetContent('');
        },

        /**
         * Get the options query object and inject proper data to it
         * @returns {*}
         */
        getOptionsQuery: function() {
            var query = frslib.options.query( this.getOptionsDataJSON() );            // create options query
            var structure = {};
            structure.data = _.extend({},this.model.get('optionsStructure'));

            query.setOptionsStructure(  structure ); // set options structure, in case we would need to get some default data
            return query;
        },

        /**
         * get just the DATA attribute
         * @returns {*}
         */
        getOptionsData: function() {
            var data = this.$el.attr('data-options');


            //data = data.split('&amp;ffblt;').join("&lt;");
            //data = data.split('&amp;ffbgt;').join("&gt;");

            return data;
        },

        getOptionsDataJSON: function() {
            return JSON.parse( this.getOptionsData() );
        },

        getUniqueId: function() {
            return this.$el.attr('data-unique-id');
        },



    });

/**********************************************************************************************************************/
/* ELEMENT MODEL
/**********************************************************************************************************************/
    /**
     * Element Model - having data about the element inside, including options structure and JS functions and all this stuff
     */
    window.ffbuilder.ElementModel = Backbone.DeepModel.extend({
        /**
         * functions are saved as strings, we create a functions from them
         */
        processFunctions: function(){
            var self = this;
            $.each( this.get('functions'), function(key, value){

                var f = null; // temporary function holder

                eval('f = ' + value );
                self.set('functions.'+key, f);

            });
        }
    });



})(jQuery);