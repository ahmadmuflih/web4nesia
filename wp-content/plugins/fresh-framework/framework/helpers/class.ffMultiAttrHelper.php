<?php

class ffMultiAttrHelper extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    private $_attributes = array();
/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
    public function setParam( $name, $value ) {
        $this->_attributes[ $name ] = array();
        $this->_attributes[ $name ][] = $value;
    }

    public function addParam( $name, $value ) {
        if( $this->isParamSet( $name ) ) {
            $this->_attributes[ $name ][] = $value;
        } else {
            $this->setParam( $name, $value );
        }
    }

    public function isParamSet( $name ) {
        return isset( $this->_attributes[ $name ] );
    }

    public function removeParam( $name ) {
        unset( $this->_attributes[ $name ] );
    }

    public function getParamValueAsArray( $name ) {
        if( $this->isParamSet( $name ) ) {
            return $this->_attributes[ $name ];
        } else {
            return array();
        }
    }

    public function getParamValueAsString( $name, $separator = ' ') {
        if( !$this->isParamSet( $name ) ) {
            return '';
        }

        return implode( $separator, $this->_attributes[ $name ] );
    }

    public function getParamString( $name, $separator = ' ')  {
        return $name .'="'. $this->getParamValueAsString( $name, $separator ) .'"';
    }

    public function getAttrString( $separator = ' ' ) {
        $toReturn = array();

        foreach( $this->_attributes as $name => $value ) {
            $toReturn[] = $this->getParamString( $name, $separator );
        }

        return implode( ' ', $toReturn );
    }

    public function reset() {
        $this->_attributes = array();
    }


/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
}