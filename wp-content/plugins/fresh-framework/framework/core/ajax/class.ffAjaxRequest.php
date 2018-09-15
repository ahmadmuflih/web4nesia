<?php
/**
 * This class automatically loads all necessary files. It will be also used
* across the whole template, when you need to load something dynamically
* @author freshface
* @since 1.1.2
*/
class ffAjaxRequest extends ffBasicObject {
/******************************************************************************/
/* VARIABLES AND CONSTANTS
/******************************************************************************/
	public $owner = NULL;
	
	public $specification = NULL;

	public $data = NULL;
/******************************************************************************/
/* CONSTRUCT AND PUBLIC FUNCTIONS
/******************************************************************************/
	public function getData( $name, $default = null ) {
        if( isset( $this->data[ $name ] ) ) {
            if( is_array( $this->data[$name ])  ) {
                return $this->data[ $name ];
            } else {
                return stripslashes($this->data[ $name ]);
            }
        } else {
            return $default;
        }
    }
/******************************************************************************/
/* PRIVATE FUNCTIONS
/******************************************************************************/	
	
/******************************************************************************/
/* SETTERS AND GETTERS
/******************************************************************************/
}