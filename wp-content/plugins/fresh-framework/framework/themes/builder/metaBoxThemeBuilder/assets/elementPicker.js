(function($){
    if( window.ffbuilder == undefined ) {
        window.ffbuilder = {};
    }
/**********************************************************************************************************************/
/* Element Picker View
/**********************************************************************************************************************/
    window.ffbuilder.ElementPickerView = Backbone.View.extend({

        /*
            How it looks like:
            <div class="ffb-filterable-library clearfix">
                <ul class="ffb-filterable-library__filters clearfix">
                    <li class="ffb-filterable-library__filter--all">aaaaa</li>
                </ul>
                <ul class="ffb-filterable-library__content clearfix">
                    <li class="filt--video">aaaaa</li>
                </ul>
            </div>
        */

        callback_ItemSelected: null,
        /**
         * Array containing all registered elements models (which contains all important data)
         */
        elementModels : null,

        /**
         * Array containing all section picker elements menu's
         */
        menuItems: null,

        bindActions: function() {
            var self = this;
            $(document).on('click', '.filt-click', function(){
                if( self.callback_ItemSelected != null ) {
                    self.callback_ItemSelected( $(this));
                }
            });
        },

        initialize: function( options ) {
            this.bindActions();
            this.vent = options.vent;
            this.elementModels = options.elementModels;
            this.menuItems = options.menuItems;
        },



        _getBasicHtml: function() {
            var html = '';

            html += '<div class="ffb-filterable-library clearfix">';
                html += '<ul class="ffb-filterable-library__filters clearfix">';

                html += '</ul>';
                html += '<ul class="ffb-filterable-library__content clearfix">';

                html += '</ul>';
            html += '</div>';



            return $(html);
        },


        renderAddFormHtml: function() {
            var self = this;
            var $html = this._getBasicHtml();

            $html.find('.ffb-filterable-library__filters').html( this.renderMenuItems() );
            $html.find('.ffb-filterable-library__content').html( this.renderContentItems() );


            return $html;
        },

        renderMenuItems: function() {
            //var $html = $('<div></div>');

            var html = '';

            for( var key in this.menuItems ) {
                var item = this.menuItems[ key ];

                html += '<li data-filter="'+ item.id +'" class="ffb-filterable-library__filter ffb-filterable-library__filter--active">'+ item.name +'</li>';
            }

            return $(html);
        },

        renderContentItems: function() {
            var html = '';

            for( var key in this.elementModels ) {
                var model = this.elementModels[ key ];
                var modelId = model.get('id');

                var previewImage = '<img src="' + model.get('previewImage') + '" />';

                html += '<li data-id="'+modelId+'" class="filt-click filt--'+modelId+'">'+ previewImage+ model.get('name') + '</li>';
            }

            var $html = $(html);

            return $html;
        },

    });

})(jQuery);