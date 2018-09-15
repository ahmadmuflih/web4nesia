<?php

class ffDataStorage_WPPostMetas extends ffDataStorage {



	protected function _maxOptionNameLength() { return 255; }
	
	protected function _setOption( $namespace /* = Post ID */, $name, $value ) {
		return $this->_getWPLayer()->update_post_meta_slashed($namespace, $name, $value);
	}
	protected function _getOption( $namespace /* = Post ID */, $name, $default=null ) {
		return $this->_getWPLayer()->get_post_meta( $namespace, $name, true );
	}
	protected function _deleteOption( $namespace /* = Post ID */, $name ) {
		return $this->_getWPLayer()->delete_post_meta($namespace, $name);
	}

	public function setOption($namespace, $name, $value ) {
		return $this->_setOption($namespace, $name, $value);
	}

    public function setOptionCoded( $namespace, $name, $value ) {
        $valueSerialized = serialize( $value );
        $valueBase64 = base64_encode( $valueSerialized );

        return $this->_setOption( $namespace, $name, $valueBase64 );
    }

	public function getOption( $namespace, $name, $default = null ) {
		return $this->_getOption($namespace, $name, $default );
	}

    private function _isBase64( $string ) {
        if (!preg_match('~[^0-9a-zA-Z+/=]~', $string)) {
        $check = str_split(base64_decode($string));
        $x = 0;
            foreach ($check as $char) if (ord($char) > 126) {
                $x++;
            }
            if ($x/count($check)*100 < 30) {
                return  true;
            }
        }
        return false;
    }

	
	public function getOptionCoded( $namespace, $name, $default = null ) {
		$value = $this->getOption($namespace, $name, $default );


        if( is_array( $value ) ) {
            return $value;
        } else if( $value !== $default ) {
            if( $this->isOptionValueJSON( $value ) ) {
                $value = $this->JSONRemoveFlagFromValue( $value );

//                $value = str_replace('u0022', '\"', $value );
//                $value = str_replace('u0027', "&#39;", $value );
//                $value = str_replace('u0026', "&", $value );
//                $value = str_replace('u2019', "&#8217;", $value );
//                $value = str_replace('u00a0', "&nbsp;", $value );
//
////                $value = utf8_decode($value );


                $value = json_decode( $value, true );
            } else if( !empty( $value ) ){
                $value = base64_decode( $value );
			    $value = unserialize( $value );
            }
		}
		
		return $value;
	}

    public function getOptionCodedJSON( $namespace, $name, $default ) {
        $value = $this->getOption($namespace, $name, $default );


        $value = $this->JSONRemoveFlagFromValue( $value );

        return json_decode( $value, true );
    }

    public function setOptionCodedJSON( $namespace, $name, $value ) {
        $valueJSON = json_encode( $value,JSON_HEX_QUOT |  JSON_HEX_AMP );
        $valueJSONWithFlag = $this->JSONAddFlagToValue( $valueJSON );
        return $this->_setOption( $namespace, $name, ($valueJSONWithFlag) );
    }

	public function deleteOption( $namespace, $name ) {
		return $this->_deleteOption($namespace, $name);
	}

}