(function($) {

    frslib.provide('frslib.options.themebuilder');
    frslib.provide('frslib.options.themebuilder');

/**********************************************************************************************************************/
/* INIT TABS
/**********************************************************************************************************************/

    frslib.options.themebuilder.initTabs = function( $options ) {
        $options.find('.ffb-modal__tabs').each(function(){

            //ffb-modal__tab-header--active
            //ffb-modal__tab-content--active


            var $contents = $(this).find('.ffb-modal__tab-contents');
            var $headers = $(this).find('.ffb-modal__tab-headers');


            // copy headers from content to the header part
            var $headersToCopy = $contents.find('.ffb-modal__tab-header');
            $headersToCopy.appendTo(  $headers );


            var activateTabFromHeader = function ( $header ) {

                var index = $header.index();

                $headers.children('.ffb-modal__tab-header').removeClass('ffb-modal__tab-header--active');
                $header.addClass('ffb-modal__tab-header--active');

                $contents.children('.ffb-modal__tab-content').removeClass('ffb-modal__tab-content--active');
                $contents.children('.ffb-modal__tab-content').eq( index).addClass('ffb-modal__tab-content--active');

            };
            $headers.find('.ffb-modal__tab-header').click(function(){

                activateTabFromHeader( $(this) );

            });



        });
    };
    frslib.callbacks.addCallback( 'initOneOptionSet', frslib.options.themebuilder.initTabs );

    $(document).on('click', '.ffb-modal-opener-button', function() {

        $(this).parent().children('.ffb-modal').css('display', 'block');

    });

    $(document).on('click', '.ffb-modal__action-done', function(e) {
        if(e.target != this) return;
        $(this).parents('.ffb-modal:first').css('display', 'none');
        $(this).parents('.ff-repeatable-item:first').removeClass('ff-repeatable-item-closed__open-popup');
    });

    $(document).on('click', '.ff-show-advanced-tools', function(e){

        var $parent = $(this).parents('.ff-repeatable-item:first');

        if( $parent.hasClass('ff-repeatable-item-closed') ) {
            $parent.addClass('ff-repeatable-item-closed__open-popup');
        }

        e.stopPropagation();

        $(this).parents('.ff-repeatable-item:first').find('.ff-advanced-options:first').find('.ffb-modal:first').css('display','block');

        return false;

    });


})(jQuery);