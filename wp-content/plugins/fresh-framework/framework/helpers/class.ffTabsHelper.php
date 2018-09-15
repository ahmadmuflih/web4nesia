<?php

/**
 * Class ffTabsHelper
 *
 * Adding and rendering tabs with callbacks
 */

class ffTabsHelper extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    private $_start = null;

    private $_end = null;

    private $_menuStart = null;

    private $_beforeMenuItem = null;

    private $_menuItem = null;

    private $_afterMenuItem = null;

    private $_menuEnd = null;

    private $_contentStart = null;

    private $_beforeContentItem = null;

    private $_contentItem = null;

    private $_afterContentItem = null;

    private $_contentEnd = null;

    private $_contentItemSpecific = array();

    /*----------------------------------------------------------*/
    /* actual settings
    /*----------------------------------------------------------*/

    private $_tabs = array();

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct() {

    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
    public function addTab( $name, $id, $callback = null ) {
        $tab = new ffStdClass();

        $tab->name = $name;
        $tab->id = $id;
        $tab->callback = $callback;

        $this->_tabs[ $id ] = $tab;
    }

    public function render() {
        $this->_call('start');

            $this->_call('menuStart');

                foreach( $this->_tabs as $oneTab ) {
                    $this->_call('beforeMenuItem', $oneTab );
                        $this->_call('menuItem', $oneTab );
                    $this->_call('afterMenuItem', $oneTab );
                }

            $this->_call('menuEnd');


            $this->_call('contentStart');

                foreach( $this->_tabs as $oneTab ) {
                    $this->_call('beforeContentItem', $oneTab );
                    $this->_call('contentItem', $oneTab );
                    $this->_call('afterContentidnItem', $oneTab );
                }

            $this->_call('contentEnd');

        $this->_call('end');
    }

    private function _call( $name, $args = null ) {
        $name = '_' . $name;
        if( is_callable( $this->$name ) ) {
            return $this->$name( $args );
        } else {
            return false;
        }
    }


/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/
    public function callbackStart( $callback ) { $this->_start = $callback; }
    public function callbackEnd( $callback ) { $this->_end = $callback; }

    public function callbackMenuStart( $callback ) { $this->_menuStart = $callback; }
    public function callbackBeforeMenuItem( $callback ) { $this->_beforeMenuItem = $callback; }
    public function callbackMenuItem( $callback ) { $this->_menuItem = $callback; }
    public function callbackAfterMenuItem( $callback ) { $this->_afterMenuItem = $callback; }
    public function callbackMenuEnd( $callback ) { $this->_menuEnd = $callback; }

    public function callbackContentStart( $callback ) { $this->_contentStart = $callback; }
    public function callbackBeforeContentItem( $callback ) { $this->_beforeContentItem = $callback; }

    public function callbackContentItem( $callback, $contentItemId = '' ) {
        if( !empty( $contentItemId ) ) {
            $this->_contentItemSpecific[$contentItemId] =  $callback;
        } else {
            $this->_contentItem = $callback;
        }
    }

    public function callbackAfterContentItem( $callback ) { $this->_afterContentItem = $callback; }
    public function callbackContentEnd( $callback ) { $this->_contentEnd = $callback; }
/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
}