(function($){
    frslib.provide('frslib.options');
    frslib.provide('frslib.options.walkers');

    frslib.options.walkers.defaultDataConvertor = function() {
        var _ = {};
        var walker = frslib.options.walkers.walker();

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
        _.output = {};

        _.walker = walker;
        walker.ignoreData = true;

        _.setStructureString = walker.setStructureString;
        _.setStructureJSON = walker.setStructureJSON;
        _.setDataString = walker.setDataString;
        _.setDataJSON = walker.setDataJSON;
        _.setIgnoreHideDefault = walker.setIgnoreHideDefault;

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
            _.setData(walker.idRoute, item.value  );
        });



/**********************************************************************************************************************/
/* VARIATION
/**********************************************************************************************************************/
        walker.setCallbackBeforeRepeatableVariationContainer(function( item, id, index ){
            //console.log( id );
        });

        walker.setCallbackAfterRepeatableVariationContainer(function( item, id ){
            //console.log( id );
        });

/**********************************************************************************************************************/
/* VARIABLE
/**********************************************************************************************************************/
        walker.setCallbackBeforeRepeatableVariableContainer(function(item, id ){
            //console.log( id );
        });

        walker.setCallbackAfterRepeatableVariableContainer(function(item, id ){
            //console.log( id );
        });

         walker.setCallbackBeforeNormalSectionContainer(function(item, id ){
            //console.log(item,  id );
        });

        walker.setCallbackAfterNormalSectionContainer(function(item, id ){
            //console.log(item, id );
        });



        return _;
    }
})(jQuery);