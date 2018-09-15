(function($){

    $(document).ready(function(){

        if( !wpNavMenu ) {
            return false;
        }

        var backupeventOnClickMenuSave = wpNavMenu.eventOnClickMenuSave;

        wpNavMenu.eventOnClickMenuSave = function() {
            backupeventOnClickMenuSave();

            var ourInputName = 'ff-navigation-menu-serialized';
            var $form = $('#update-nav-menu');

            $form.append('<input type="hidden" name="save_menu" value="Save Menu">');

            var serializedForm = ( $form.serialize() );

            //console.log( serializedForm );
            //console.log( $form.serializeArray() );

            $form.find('input, checkbox, radio, textarea').attr('name', '');


            $form.append('<input type="hidden" class="'+ourInputName+'" name="'+ourInputName+'">');
            $('.'+ourInputName).val( serializedForm );

            $form.submit();

        }
    });

})(jQuery);