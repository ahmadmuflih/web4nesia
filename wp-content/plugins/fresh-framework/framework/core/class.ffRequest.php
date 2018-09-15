<?php

class ffRequest extends ffBasicObject {
	const ADMIN_SCREEN_VIEW_SLUG = 'view';
	
	public function get( $name, $defaultValue = null ) {
		if( isset( $_GET[ $name ] ) ) {
			return $_GET[ $name ];
		} else {
			return $defaultValue;
		}
	}
	
	public function post( $name ) {
		if( isset( $_POST[ $name ] ) ) {
			return $this->_stripSlashes($_POST[ $name ]);
		} else {
			return null;
		}
	}

    public function cookie( $name ) {
        if( isset( $_COOKIE[ $name ] ) ) {
            return $_COOKIE[ $name ];
        } else {
            return null;
        }
    }
	
	public function postEmpty() {
		return empty( $_POST );
	}
	
	private function _stripSlashes( $value ) {
		return stripslashes_deep( $value );
	}

    public function server( $name ) {
        if( isset( $_SERVER[ $name ] ) ) {
            return $_SERVER[ $name ];
        } else {
            return null;
        }
    }


    public function setPost( $variable ) {
        $_POST = $variable;
    }

    public function setRequest( $variable ) {
        if( empty( $_REQUEST ) ) {
            $_REQUEST = array();
        }
        $_REQUEST = array_merge( $_REQUEST, $variable);
    }
}