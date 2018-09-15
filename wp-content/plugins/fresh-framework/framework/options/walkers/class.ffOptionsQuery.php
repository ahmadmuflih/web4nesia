<?php
/**
 * Try to get the value from the data array. In case of failure ( for example
 * we added another option and user didn't saved them yet ) it will re-create
 * the whole option structure and try to get the value from here. If this 
 * won't help too, it will report error.
 * 
 * @author FRESHFACE
 * @since 0.1
 *
 */
class ffOptionsQuery extends ffBasicObject implements Iterator {
	
/******************************************************************************/
/* VARIABLES AND CONSTANTS
/******************************************************************************/
	protected $_iteratorPointer = null;
	
	protected $_iteratorValidHolder = null;
	
	protected $_data = null;
	
	protected $_path = null;
	/**
	 * 
	 * @var ffOptionsArrayConvertor
	 */
	protected $_arrayConvertor = null;
	
	/**
	 * 
	 * @var ffWPLayer
	 */
	protected $_WPLayer = null;
	
	/**
	 * 
	 * @var ffIOptionsHolder
	 */
	protected $_optionsHolder = null;
	
	protected $_optionsStructureHasBeenCompared = false;
	
	protected $_hasBeenComparedWithStructure = false;

	/**
	 * Function, that gets called every iteration in foreach cycle, it will contain the query and all the important
	 * stuff. It's used mainly in the new builder for printing system things
	 * @var callable
	 */
	protected $_iteratorValidationCallback = null;

	/**
	 * @var callable
	 */
	protected $_iteratorStartCallback = null;

	/**
	 * @var callable
	 */
	protected $_iteratorEndCallback = null;
/******************************************************************************/
/* CONSTRUCT AND PUBLIC FUNCTIONS
/******************************************************************************/
	public function __construct( $data, ffIOptionsHolder $optionsHolder = null, ffOptionsArrayConvertor $arrayConvertor = null, $path = null, $optionsStructureHasBeenCompared = false ) {
		$this->_setData($data);
		$this->_setArrayConvertor($arrayConvertor);
		if( $optionsHolder != null ) {
			$this->_setOptionsHolder($optionsHolder);
		}
		$this->_setPath($path);
	}

	public function setIteratorStartCallback( $callback ) {
		$this->_iteratorStartCallback = $callback;
	}

	public function setIteratorEndCallback( $callback ) {
		$this->_iteratorEndCallback = $callback;
	}

	public function setIteratorValidationCallback( $callback ) {
		$this->_iteratorValidationCallback = $callback;
	}

	public function getOnlyDataPart( $query, $wrappedInSectionName = true ) {
		$exploded = explode(' ', $query);
		$arrayName = end($exploded );
		$toReturn = null;
		if( $wrappedInSectionName ) {
			$toReturn[ $arrayName ] = $this->_get($query);
		} else {
			$toReturn = $this->_get( $query );
		}
		return $toReturn;
		//return $this->_get($query);
	}
	
	public function resetPath() {
		$this->_setPath( null );
	}
	
	public function debug_dump( $short = false )
	{
		if( $short ){
			echo '<pre>';
			print_r($this->_path);
			echo '</pre>';
			echo '<pre>';
			print_r($this->_data);
			echo '</pre>';
		}
		var_dump( $this->_path, $this->_data );
	}

    public function debug_export() {
        var_export( $this->_data );
    }

    public function getWithoutComparationDefault( $query, $default = null ) {
        $data = $this->getWithoutComparation( $query );
        if( $data == null ) {
            return $default;
        } else {
            return $data;
        }
    }

	public function getWithoutComparation( $query ) {
		if( $this->_getPath() !== null ) {
			$query = $this->_getPath() . ' ' . $query;
		}
		$result = $this->_get( $query );
		
		if( is_array( $result ) ) {

			$result = $this->getNew( $query );
		}
		
			return $result;
	}

    public function queryExists( $query ) {
        $result = $this->getWithoutComparation( $query );

        if( $result == null ) {
            return false;
        } else {
            return true;
        }
    }

//    static $allowed_html = null;
//		if( empty($allowed_html) ){
//			$allowed_html = wp_kses_allowed_html('post');
//		}
//		return wp_kses( $html, $allowed_html )
    private $_kses_allowed_html = null;

    public function getWpKses( $query ) {
        if( $this->_kses_allowed_html == null ) {
            $this->_kses_allowed_html = wp_kses_allowed_html('post');
        }

        return wp_kses( $this->get( $query), $this->_kses_allowed_html );
    }

    public function printWpKses( $query ) {
        echo $this->getWpKses( $query );
    }

    public function getEscAttr( $query ) {
        return esc_attr( $this->get( $query ) );
    }

    public function getEscUrl( $query ) {
        return esc_url( $this->get( $query ) );
    }

	/**
	 *
	 * @param string|unknown $query
	 * @return ffOptionsQuery|string
	 * @throws ffException
	 */
	public function get( $query ) {
		if( $this->_getPath() !== null ) { 
			$query = $this->_getPath() . ' ' . $query; 
		}
		$result = $this->_get( $query );
		
		if( $result === null ) {

//            var_dump( $query, 'xxxxxxxxxxx' );

            if( $this->_getWPLayer()->is_freshface_admin_server_or_local() ) {
//                echo '<script>';
//                echo 'console.log("';
//                    echo 'Query ' . $query .', optionsHolder ' . ( $this->_optionsHolder->getOptionsHolderClassName() ) . ' NOT FOUND, HAVE TO BE COMPARED';
//                echo '");';
//                echo 'Query ' . $query .', optionsHolder ' . ( $this->_optionsHolder->getOptionsHolderClassName() ) . ' NOT FOUND, HAVE TO BE COMPARED';
//                echo '</script>';
//                throw new ffException('Query ' . $query .', optionsHolder ' . 'xx' . ' NOT FOUND, HAVE TO BE COMPARED');
            }

			$this->_compareDataWithStructure();
			$result = $this->_get($query);

            if( $result === null && $this->_getWPLayer()->get_ff_debug() ) {
//                throw new ffException('NON EXISTING QUERY STRING -> "'.$query.'"');
            } else {
                $this->_getWPLayer()->do_action( ffConstActions::ACTION_QUERY_NOT_FOUND_IN_DATA, $query );
            }
		}
		
		
		if( is_array( $result ) ) {
			
		//	if( $this->_getPath() == null ) {
				
				$result = $this->getNew( $query ); 
				//new ffOptionsQuery( $this->_getData(), $this->_getOptionsHolder(), $this->_getArrayConvertor(), $query, $this->_optionsStructureHasBeenCompared );
// 			} else {
// 				$this->_setPath( $query);
// 				$result = $this;
// 			}
		}
		
		
 		
		return $result;
	}

    public function isEmpty( $query ) {
        $result = $this->get( $query );

        return ( $result == false );
    }

    public function notEmpty( $query ) {
        return !$this->isEmpty( $query );
    }

	public function getText( $query ) {
		$text = $this->get( $query );
		
		return $this->_getWPLayer()->do_shortcode( $text );
	}
	
	public function printText( $query ) {
		$text = $this->get( $query );
		
		echo $this->_getWPLayer()->do_shortcode($text);
		
	}

	public function getMultipleSelect( $query ) {
		$valueText = $this->get($query);
		$valueArray = explode('--||--', $valueText);

		return $valueArray;
	}

    public function getMultipleSelect2( $query ) {
		$valueText = $this->get($query);
        if( empty( $valueText ) ) {
            return array();
        }
		$valueArray = explode('--||--', $valueText);

		return $valueArray;
	}
	
	public function getUnserialize( $query ) {
		return unserialize( $this->get($query) );
	}
	
	public function getJsonDecode( $query ) {
        $value = $this->get( $query );

        $value = str_replace('_ffqt_', '"', $value );

		return json_decode( $value );
	}
	
	public function getImage( $query ) {



		$image = $this->getJsonDecode( $query );
		
	
		
		if( !is_object( $image ) ) {
			$image = new stdClass();
			$image->url = '';
		} else {
			
			if( strpos( $image->url, $this->_getWPLayer()->get_freshface_demo_url() )!== false && strpos( $this->_getWPLayer()->get_home_url(), $this->_getWPLayer()->get_freshface_demo_url() ) === false) {
				$image->url = $this->_getWPLayer()->wp_get_attachment_url( $image->id );//wp_get_attachment_url( $image->id );
			}
			//if( strpos($this->_getWPLayer()->get_home_url())
			
			//var_dump( get_home_url() );
			//$image->url = wp_get_attachment_url( $image->id );
		}
		
		
		
		return $image;
	}
	
	public function getIcon( $query ) {
		$icon = $this->get( $query );
		
		$iconFiltered = $this->_getWPLayer()->apply_filters( ffConstActions::FILTER_QUERY_GET_ICON, $icon);
		
		return $iconFiltered;
	}

    public function getColor( $query ) {
        $result = $this->get( $query );
        $resultFiltered = $this->_getWPLayer()->apply_filters( ffConstActions::FILTER_QUERY_GET_COLOR, $result );

        return $resultFiltered;
    }
	
	public function getNew( $query = null ) {


		$query =  new ffOptionsQuery( $this->_data, $this->_getOptionsHolder(), $this->_getArrayConvertor(), $query, $this->_optionsStructureHasBeenCompared );
		$query->setWPLayer( $this->_getWPLayer() );
		$query->setIteratorValidationCallback( $this->_getIteratorValidationCallback() );
		$query->setIteratorStartCallback( $this->_iteratorStartCallback );
		$query->setIteratorEndCallback( $this->_iteratorEndCallback );
		return $query;
	}

    public function getJSON( $query ) {
        $jsonString = $this->get( $query );
        $data = json_decode( $jsonString );

        if( $data == null ) {
            $data = new stdClass();
        }

        return $data;
    }
	
	
	public function getIndex( $query, $index ) {
		$currentQuery = $this->get( $query );
		$toReturn = null;
		
		foreach( $currentQuery as $key => $oneSubItem ) {
			if( $key == $index ) {
				 $toReturn = $oneSubItem;
				 break;
			}
		}
		
		return $toReturn;
	}
	
	public function getOnlyData() {
		return $this->_data;
	}

    public function setDataValue( $routeString, $value ) {
        $current = &$this->_data;


        $completeRoute = explode( ' ', $routeString );
		$routeEnd = end( $completeRoute );

        foreach( $completeRoute as $route ) {
            $route = (string)$route;

            if( !isset( $current[ $route ] ) ) {
				if( !is_array( $current ) ) {
					$current = array();
				}
				$current[ $route ] = array();
			}
			if( $route == $routeEnd ) {
				$current[ $route ] = $value;
			}
			if( is_array( $current ) ) {
				$current = &$current[$route ];
			}
        }
    }
/******************************************************************************/
/* PRIVATE FUNCTIONS
/******************************************************************************/
	protected function _compareDataWithStructure() {
		if ($this->_getOptionsstructureHasBeenCompared() == false && $this->_optionsHolder != null ) {

			$this->_setOptionsstructureHasBeenCompared(true);
			$options = $this->_getOptionsHolder()->getOptions();
			$this->_getArrayConvertor()->setOptionsArrayData( $this->_data );
			$this->_getArrayConvertor()->setOptionsStructure( $options );
			$this->_data = $this->_getArrayConvertor()->walk();
			$this->_setOptionsstructureHasBeenCompared(true);
		} else if( $this->_getOptionsstructureHasBeenCompared() == false && $this->_optionsHolder == null ) {
			$this->_setOptionsstructureHasBeenCompared(true);
		}
	}
	
	private function _get( $query ) {
		$queryArray = $this->_convertQueryToArray( $query );
		$result = $this->_getFromData($queryArray);
		return $result;
	}
	
	private function _convertQueryToArray( $query ) {
		$queryArray = explode(' ', $query);
		return $queryArray;
	}	

	private function _getFromData( $queryArray ){
		$dataPointer = &$this->_data;
		
		if( empty( $dataPointer ) ) {
			return null;
		}
		
		foreach( $queryArray as $oneArraySection ) {
			if( isset( $dataPointer[ $oneArraySection ] ) ) {
				$dataPointer = &$dataPointer[ $oneArraySection ];
			} else {
				return null;
			}
		}
		
		return ( $dataPointer );
	}
	
	
/******************************************************************************/
/* ITERATOR INTERFACE
/******************************************************************************/
	private $_currentKeys = array();
	private $_currentKeysCount = 0;
	
	private $_currentVariationType = null;
	
	public function getVariationType() {
		return $this->_currentVariationType;
	}
	
	public function getNumberOfElements() { 
		$this->_recalculateKeys();
		return count( $this->_currentKeys );
	}
	
	public function setVariationType( $variationType ) {
		$this->_currentVariationType = $variationType;
	}

    public function getCurrentQueryDataPart() {
        return $this->getOnlyDataPart( $this->_getPath(), false );
    }
	
	private function _recalculateKeys() {
		$dataPart = $this->getOnlyDataPart( $this->_getPath(), false );
		$this->_currentKeys = array_keys( $dataPart );
		$this->_currentKeysCount = count( $this->_currentKeys );
		$this->_currentVariationType = null;
	}

	private $_valid = true;

	private $_current = null;
	
	public function _current () {
		$this->_valid = true;
		$this->_currentVariationType = null;
		
		$currentKey = $this->_currentKeys[ $this->_iteratorPointer ];

		if( is_numeric($currentKey) ) {
			return $this->getNew( $this->_getPath() .' '.$this->_iteratorPointer);
		}
		
		$potentialSplit = explode('-|-', $currentKey);
		
		// 0-|-one-text-item
		$queryAddition = $this->_iteratorPointer;
		if( count( $potentialSplit )  == 2 ) {
			$index = $potentialSplit[0];
			$type = $potentialSplit[1];
			
			$queryAddition = $currentKey . ' ' . $type;
			$this->_currentVariationType = $type;
		}
		
		$newQuery = $this->getNew( $this->_getPath() .' '.$queryAddition);
		$newQuery->setVariationType( $this->_currentVariationType );

		if( $this->_iteratorStartCallback != null ) {
			$callback = $this->_iteratorStartCallback;

			$callback( $newQuery );
		}


		if( $this->_iteratorValidationCallback != null ) {
			$callback = $this->_iteratorValidationCallback;

			$isValid = $callback( $newQuery );
			if( $isValid === null ) {
				throw new ffException('ffOptionsQuery - iterator validation callback is null');
			}
			if( !$isValid ) {
				$this->_valid = false;
			}
		}


		return $newQuery;

	}
	public function key () {
		return $this->_iteratorPointer;
	}
	public function next () {

		if( $this->_iteratorEndCallback != null ) {
			$callback = $this->_iteratorEndCallback;

			$callback( $this->_current );
		}

		$this->_iteratorPointer++;
	}
	public function rewind () {
		$this->_iteratorPointer = 0;
		$this->_recalculateKeys();
	}

	public function current() {
		return $this->_current;
	}


	public function valid () {
		$valid = true;

		if( $this->_iteratorPointer == 0) {
			$valid = $this->_validFirst();
		} else {
			$valid = $this->_validNotFirst();
		}
		if( $valid == false ) {
			return false;
		}

		$this->_current = $this->_current();

		if( !$this->_valid ) {

			for( $i = $this->_iteratorPointer+1; $i <= $this->_currentKeysCount-1; $i++ ) {


				$this->next();

				$this->_current = $this->_current();
				if( $this->_valid ) {
					return true;
					break;
				}


//				if( $i == $this->_currentKeysCount - 1 ) {
//
//				}

			}

			// IS LAST - WE HAVE TO CALL THIS SHIT HERE
			if( $this->_iteratorEndCallback != null ) {
				$callback = $this->_iteratorEndCallback;

				$callback( $this->_current );
			}

		} else {
			return true;
		}

		return false;
	}
	
	private function _validFirst() {
		if( $this->_currentKeysCount == 0 ) {
			$this->_compareDataWithStructure();
			$this->_recalculateKeys();
			
			return $this->_validNotFirst();
		}
		return true;
	} 
	
	private function _validNotFirst() {
		if( $this->_iteratorPointer == $this->_currentKeysCount || $this->_currentKeysCount == 0 ) {
			return false;
		}
		
		return true;
	}
	
/******************************************************************************/
/* SETTERS AND GETTERS
/******************************************************************************/
	public function setWPLayer( ffWPLayer $WPLayer ) {
		$this->_WPLayer = $WPLayer;
	}
	
	protected function _getWPLayer() {
		return $this->_WPLayer;
	}
	/********** DATA **********/
	protected function _setData( $data ) {
		$this->_data = $data;
	}
	
	/**
	 * 
	 */
	protected function _getData() {
		return $this->_data;
	}
	
	/********** ARRAY CONVERTOR **********/
	protected function _setArrayConvertor(ffOptionsArrayConvertor $arrayConvertor ){
		$this->_arrayConvertor = $arrayConvertor;
	}
	
	/**
	 * 
	 * @return ffOptionsArrayConvertor
	 */
	protected function _getArrayConvertor() {
		return $this->_arrayConvertor;
	}
	
	/********** OPTIONS HOLDER **********/
	protected function _setOptionsHolder(ffIOptionsHolder $optionsHolder ) {
		$this->_optionsHolder = $optionsHolder;
	}
	/**
	 * 
	 * @return ffIOptionsHolder
	 */
	protected function _getOptionsHolder() {
		return $this->_optionsHolder;
	}

	/**
	 * @return unknown_type
	 */
	protected function _getPath() {
		return $this->_path;
	}
	
	/**
	 * @param unknown_type $path
	 */
	protected function _setPath($path) {
		$this->_path = $path;
		return $this;
	}

	/**
	 * @return unknown_type
	 */
	protected function _getOptionsstructureHasBeenCompared() {
		return $this->_optionsStructureHasBeenCompared;
	}
	
	/**
	 * @param unknown_type $optionsStructureHasBeenCompared
	 */
	protected function _setOptionsstructureHasBeenCompared($optionsStructureHasBeenCompared) {
		$this->_optionsStructureHasBeenCompared = $optionsStructureHasBeenCompared;
		return $this;
	}

	protected function _getIteratorValidationCallback() {
		return $this->_iteratorValidationCallback;
	}
	
	
}