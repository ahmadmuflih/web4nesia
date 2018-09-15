<?php

class ffOptionsPostReader extends ffOptionsWalker {
	
	const RETURN_COLORLIB_VALUE = 'return_colorlib_value';
	
	/**
	 * 
	 * @var ffRequest
	 */
	private $_request = null;
	
	private $_settings = null;
	
	public function __construct( ffRequest $request) {
		$this->_request = $request;	
		$this->_settings[ ffOptionsPostReader::RETURN_COLORLIB_VALUE ] = false;
	}
	
	public function setSetting( $name, $value ) {
		$this->_settings[ $name ] = $value;
	}
	
	public function getDataFromArray( $array ) {
		$this->_setOptionsArrayData( $array );
		if( empty( $this->_optionsStructure ) ) {
				
			return $this->_optionsArrayData;
		}
		
		$this->_walk();
		
		return $this->_getOptionsArrayData();
	}
	
	public function getData( $prefixName ) {

        $postData = $this->_getRequest()->post( $prefixName );

        if( !is_array( $postData) ) {
            $postData = json_decode( $postData, true );

            if( isset($postData[ $prefixName ]) ) {
                $postData = $postData[ $prefixName ];
            }
        }

        $this->_setOptionsArrayData( $postData );


		if( empty( $this->_optionsStructure ) ) {
			
			return $this->_optionsArrayData;
		}
		
		$this->_walk();

		return $this->_getOptionsArrayData();
	}

	protected function _beforeContainer( $item ) {}
	protected function _afterContainer( $item ) {}
	protected function _oneOption( $item ) {
		
		switch( $item->getType() ) {
			case ffOneOption::TYPE_CONDITIONAL_LOGIC:
				$valueRaw = $item->getValue();
				$valueParsed = array();
				parse_str($valueRaw, $valueParsed);
				
				$value = ( isset( $valueParsed['option-value'])) ? $valueParsed['option-value'] : array();
				
				$this->_setDataValue( $value );
				break;
				
				
			case ffOneOption::TYPE_COLOR_LIBRARY:
				
					$colorLibrary = ffContainer::getInstance()->getAssetsIncludingFactory()->getColorLibrary();
					$variableName = $item->getParam('less-variable-name');
					$variableValue = $item->getValue();
					
					if( strpos($variableValue, '@') !== false ) {
						$colorLibrary->setUserColor( $variableName, $variableValue);
					} else {
						$colorLibrary->deleteUserColor($variableName);
					}
					if( $this->_settings[ ffOptionsPostReader::RETURN_COLORLIB_VALUE ] == true ) {
						
					} else {
						$this->_setDataValue('');
					}
					
					//var_dump( $variableValue );
				break;
				
			case ffOneOption::TYPE_TEXT:
			case ffOneOption::TYPE_TEXTAREA:
				
				$value = $item->getValue();
				$valueStripped = stripslashes( $value );
				$this->_setDataValue($valueStripped);
				break;
		}
		
	}
	
	/**
	 *
	 * @return ffRequest ss
	 */
	protected function _getRequest() {
		return $this->_request;
	}

	/**
	 *
	 * @param ffRequest $_request        	
	 */
	protected function _setRequest(ffRequest $_request) {
		$this->_request = $_request;
		return $this;
	}
	

	private function _setDataValue( $value ) {
		$route = $this->_getRoute();
		$data = &$this->_optionsArrayData;
		
		foreach( $route as $onePath ) {
			if( isset( $data[ $onePath ] ) ) {
				$data = &$data[$onePath];
			} else {
				return;
			}
		}
		
		$data = $value;
	}

}