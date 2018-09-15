(function($){
    frslib.provide('frslib.options');
    frslib.provide('frslib.options.walkers');

    frslib.options.walkers.toScContentConvertor = function() {
        var _ = {};
        var walker = frslib.options.walkers.walker();

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
        _.output = {};
        _.contentOutput = '';

        _.walker = walker;
        walker.ignoreData = true;

        _.setStructureString = walker.setStructureString;
        _.setStructureJSON = walker.setStructureJSON;
        _.setDataString = walker.setDataString;
        _.setDataJSON = walker.setDataJSON;
        _.setIgnoreHideDefault = walker.setIgnoreHideDefault;

        _.saveOnlyDifferece = false;

        _.getCurrentRouteCount = function() {
            return Object.keys(_.walker.idRoute).length;
        }

        _.setPrefix = walker.setPrefix;

        _.walk = function() {

            _.output = {};
            walker.walk();

            return _.output;
        }


/**********************************************************************************************************************/
/* ROUTE AND QUERYING
/**********************************************************************************************************************/

        _.initRoute = function( route ) {

            var pointer = _.output;

            var routeLength = Object.keys(route).length

            var counter = 0;
            for( var id in route ) {
	            counter++;

	            var key = route[id];

                if( pointer[key] == undefined ) {
                    pointer[key] = {};
                }

                //if( counter == routeLength ) {



                    //pointer[key] = value;
                //} else {
                //    var swap = pointer[key];
                //    pointer = swap;
                //}

                var swap = pointer[key];
                pointer = swap;

            }

        }

        _.setData = function( route, value ) {

            var pointer = _.output;

            var routeLength = Object.keys(route).length

            var counter = 0;
            for( var id in route ) {
	            counter++;

	            var key = route[id];

                if( pointer[key] == undefined ) {
                    pointer[key] = {};
                }

                if( counter == routeLength ) {
                    pointer[key] = value;
                } else {
                    var swap = pointer[key];
                    pointer = swap;
                }

            }
        };

/**********************************************************************************************************************/
/* ITEM HELPERS
/**********************************************************************************************************************/
        _.escapeValue = function( value ) {
            value = value.split('&').join('&amp;');
            value = value.split('<').join('&lt;');
            value = value.split('>').join('&gt;')
            value = value.split('"').join('&quot;')
            value = value.split("'").join('&apos;');

            return value;
        }

        _.getItemParam = function ( item, param, defaultValue ) {
            if( item == null ) {
                return null;
            }
            if( item.params == undefined || item.params == null ) {
               if( defaultValue != undefined) {
                    return defaultValue;
                } else {
                    return null;
                }
            }
            if( item.params[param] != undefined &&  item.params[param] != null ) {
                return item.params[param][0];
            } else {
                if( defaultValue != undefined) {
                    return defaultValue;
                } else {
                    return null;
                }
            }
        }
        _.getItemParamArray = function( item, param ) {
            if( item.params == undefined || item.params == null ) {
                return null;
            }
            if( item.params[param] != undefined && item.params[param] != null ) {
                return item.params[param];
            } else {
                return null;
            }
        }
/**********************************************************************************************************************/
/* OPTIONS & ELEMENTS FUNCTIONS
/**********************************************************************************************************************/



/**********************************************************************************************************************/
/* OPTION
/**********************************************************************************************************************/



        _.missingOptions = {};

        walker.setCallbackOneOption(function(item, id, nameRoute ){
            var printInContent = _.getItemParam(item, 'print-in-content', false);

            //console.log( item);

            //if( item.id == 'clr' ) {
            //    console.log( item.defaultValue == item.value );
            //    console.log( _.saveOnlyDifference );
            //
            //    if(_.saveOnlyDifference == false || _.saveOnlyDifference == undefined ) {
            //        console.log( nameRoute );
            //    }
            //}

            if( item.defaultValue == item.value && _.saveOnlyDifference ) {
                return false;
            }

            if( printInContent ) {
                _.printInContent( item, id, nameRoute );
            } else {
                console.log( item.value );
                _.setData(walker.idRoute, item.value  );
            }
            //console.log( printInContent );

            //1

            //console.log( item, id, nameRoute );
        });

        _.routeToString = function( route ) {
            var toReturn = [];
            for( var id in route ) {
                var value = route[id];

                toReturn.push( value );
            }

            return toReturn.join(' ');
        }

        _.printInContent = function( item, id, nameRoute ) {

            //console.log( id );

            var routeString = _.routeToString( id );
            var value = item.value;

            var scString = '[ffb_param route="'+ routeString +'"]' + value + '[/ffb_param]';

            _.contentOutput += scString;
            //console.log( item, id, walker.idRoute, nameRoute );
        };

/**********************************************************************************************************************/
/* VARIATION
/**********************************************************************************************************************/
        walker.setCallbackBeforeRepeatableVariationContainer(function( item, id, index ){
            //console.log( id );
        });

        walker.setCallbackAfterRepeatableVariationContainer(function( item, id ){
             _.initRoute(walker.idRoute );
        });

/**********************************************************************************************************************/
/* VARIABLE
/**********************************************************************************************************************/

        _.saveOnlyDifferenceSetOn = '';


        walker.setCallbackBeforeRepeatableVariableContainer(function(item, id ){
            _.checkSaveOnlyDifferenceBefore( item );
        });

        walker.setCallbackAfterRepeatableVariableContainer(function(item, id ){
           _.checkSaveOnlyDifferenceAfter( item );
        });



        walker.setCallbackBeforeNormalSectionContainer(function(item, id ){
             _.checkSaveOnlyDifferenceBefore( item );
        });

        walker.setCallbackAfterNormalSectionContainer(function(item, id ){
            _.checkSaveOnlyDifferenceAfter( item );
        });

        _.checkSaveOnlyDifferenceBefore = function( item ) {

            if( _.saveOnlyDifference == false || _.saveOnlyDifference == undefined) {

                 _.saveOnlyDifference = _.getItemParam( item, 'save-only-difference', false);
                console.log(_.saveOnlyDifference );

                 if(_.saveOnlyDifference == true ) {
                     console.log( frslib.array.objectToArray( walker.idRoute ) );
                     _.saveOnlyDifferenceSetOn = frslib.array.objectToArray( walker.idRoute );
                 }

             }

        }

        _.checkSaveOnlyDifferenceAfter = function( item ) {
            if( _.saveOnlyDifference == true) {

                var currentRouteString = frslib.array.objectToArray( walker.idRoute );

                if( currentRouteString == _.saveOnlyDifferenceSetOn ) {
                    _.saveOnlyDifference = false;
                }

            }
        }



        return _;
    }
})(jQuery);