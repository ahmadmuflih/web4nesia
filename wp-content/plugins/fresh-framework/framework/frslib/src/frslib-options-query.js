(function($){
    frslib.provide('frslib.options');


/**********************************************************************************************************************/
/* WALKER
/**********************************************************************************************************************/
    frslib.options.query = function( data ) {



        var _ = {};

        _.data = null;

        _.hasBeenCompared = false;
        _.optionsStructure = null;


        _.currentPath = '';



        _.setOptionsStructure = function(optionsStructure) {
            _.optionsStructure = optionsStructure;
        }

        _.setData = function( data ) {
            _.data = data;
        };

        _.get = function( query ) {
            var result = _.getWithoutComparation( query );

            if( result == null && _.hasBeenCompared == false ) {
                _.compareWithStructure();
                return _.get( query );

            } else if( result == null ) {
                console.log('WRONG QUERY BRO');
            } else {
                return result;
            }
        };

        _.getNum = function( query ) {
            return parseInt(_.get(query) );
        }

        _.getJSON = function( query ) {
            var jsonString = _.get( query );

            var data = null;
            try {
                data = JSON.parse( jsonString );
            } catch( e ) {
                data = {};
            }

            return data;
        };

        _.compareWithStructure = function() {
            if(_.optionsStructure == null ) {
                console.log('OPTIONS STRUCTURE IS NOT PRESENTED');

            }

            if(_.hasBeenCompared == true) {
                console.log('query has been already compared');
            }

            var dataConvertor = frslib.options.walkers.defaultDataConvertor();

            dataConvertor.setDataJSON(_.data );
            dataConvertor.setStructureJSON(_.optionsStructure);

            _.data = dataConvertor.walk();

            _.hasBeenCompared = true;
        }

        _.getWithoutComparation = function( query, returnAsArray ) {

            if( returnAsArray == undefined ) {
                returnAsArray = false;
            }

            if(_.data == null ) {
                return null;
            }

            if(_.currentPath != '' ) {

                if( query != undefined && query != '')  {
                    query = _.currentPath + ' ' + query;
                } else {
                    query = _.currentPath;
                }

            }

            var queryArray = query.split(' ');

            var pointer = _.data;

            for( var key in queryArray ) {
                var value = queryArray[ key ];

                //console.log( key, value );

                if( pointer[ value ] != undefined ) {
                    pointer = pointer[ value ];
                } else {
                    return null;
                }
            }

            var type = typeof pointer;

            if( type == 'string' || type == 'number' || returnAsArray ) {
                return pointer;
            } else {
                return _.getNewQuery( query );
            }


        };

        _.getColorFront = function( query ) {
            var color = _.get(query);

            if( color.indexOf('lib') != -1 ) {
                var sign = color.replace('lib-', '');
                color = window.vent.o.dataManager.getColorFromLibrary(sign);
            }

            return color;
        };

        _.queryExists = function() {

        };

        _.each = function( callback ) {
            var queryToTraverse = _.get();

            var pureData = queryToTraverse.getWithoutComparation('', true);

            for( var key in pureData ) {
                var splitted = key.split('-|-');
                var id = splitted[0];
                var sectionType = splitted[1];

                var newQueryString = key + ' ' + sectionType;

                var newQuery = queryToTraverse.getWithoutComparation( newQueryString );

                callback.call(this, newQuery, sectionType, id );
            }

        };

        _.getNumberOfElements = function() {
            var queryToTraverse = _.get();
            var pureData = queryToTraverse.getWithoutComparation('', true);

            return Object.keys(pureData).length;
        };

        _.setPath = function( path ) {
            _.currentPath = path;
        };

        _.getNewQuery = function( path ) {
            var newQuery = frslib.options.query();
            newQuery.setPath( path );
            newQuery.setData(_.data );
            newQuery.setOptionsStructure(_.optionsStructure);
            return newQuery;
        };


        if( data != undefined ) {
            _.setData( data );
        }

        return _;
    }
})(jQuery);