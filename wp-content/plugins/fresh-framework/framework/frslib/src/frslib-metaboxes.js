(function($) {
	
	frslib.provide('frslib.metaboxes');
	frslib.provide('frslib.metaboxes.names');
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################
// METABOXES
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################	
	// selectors and names
	frslib.metaboxes.names.action_publishPost = 'action_publish_post';
	frslib.metaboxes.names.postForm = '#post';
	
	frslib.metaboxes.names.normalize_options_class = '.ff-metabox-normalize-options';
	
	
	$( frslib.metaboxes.names.postForm ).submit(function(){
		frslib.callbacks.doCallback( frslib.metaboxes.names.action_publishPost );
        //return false;
	});
	
	var $normalizeMetaboxes = $( frslib.metaboxes.names.normalize_options_class );

	if( $normalizeMetaboxes.length > 0 ) {
		
		frslib.callbacks.addCallback( frslib.metaboxes.names.action_publishPost, function(){

            if( $('.ff-max-input-vars').length > 0 ) {

                var maxInputVars = parseInt( $('.ff-max-input-vars').html() );

            }


		$normalizeMetaboxes.each(function(i, o){
            var $form = $(o);


            if( $form.find('.ff-options-js-wrapper').size() > 1 ) {

                var $normalizedContent = $('<div></div>');

                $form.find('.ff-options-js-wrapper').each(function(){
                    var $formToNormalize = $(this);



                    if( $form.hasClass('ff-metabox-normalize-options-to-one-input') ) {

                        var prefix = $(this).find('.ff-options-prefix').html();//ff-options-prefix
                        var data = frslib.options.template.functions.normalizeFast( $(this) );
                        var dataJSON = JSON.stringify( data );


                        var $inputHolder = $('<input type="hidden" class="ff-hidden-input" name="'+prefix+'">');
                        $inputHolder.val( dataJSON );
                        $normalizedContent.append( $inputHolder );


                    }


                });


            } else {
                var $normalizedContent = null;
                //var content2= frslib.options.template.functions.normalizeFast( $(o) );

                if( $(this).hasClass('ff-metabox-normalize-options-use-old-convertor') ) {
                    $normalizedContent = frslib.options.template.functions.normalize( $(o) );


                }

                else if( $(this).hasClass('ff-metabox-normalize-options-to-one-input') ) {

                    var prefix = $form.find('.ff-options-prefix').html();//ff-options-prefix
                    var data = frslib.options.template.functions.normalizeFast( $(o) );
                    var dataJSON = JSON.stringify( data );


                    var $normalizedContent = $('<input type="hidden" class="ff-hidden-input" name="'+prefix+'">');
                    $normalizedContent.val( dataJSON );
                    //return false;

                }


                else {
                    $normalizedContent = frslib.options.template.functions.normalizeFast( $(o), true, true );
                }

            }

            $(this).find('*').attr('name', '');
            $(this).after( $normalizedContent );


        });


		});
	}
	
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################
// VISIBILITIES
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################
//##############################################################################

//##############################################################################
//# PAGE TEMPLATE
//##############################################################################
	$('#page_template').change(function( value ) {
		var selectValue = $(this).val();
		var pageTemplateType = 'visibility_page_template';
		
		$('.ff-one-visibility').each(function() {
			if( $(this).attr('data-type') == pageTemplateType ) {
				var $parent = $(this).parents('.postbox:first');
				var hasSelectedPageTemplate = false;
				
				$(this).find('.ff-one-visibility-item').each(function(){
					var html = $(this).html();
					if( html == selectValue ) {
						hasSelectedPageTemplate = true;
					}
				});
				
				if( hasSelectedPageTemplate ) {
					$parent.show(500);
				} else {
					$parent.hide(500);
				}
			}
		});
		
		
		
		/*
		$('.ff-one-visibility-item').each(function(){
			var html = $(this).html();
			if( html == selectValue ) {
				console.log( 'YES');
			}
		})*/
	});
	$(document).ready(function() {
		$('#page_template').change();
	});
})(jQuery);