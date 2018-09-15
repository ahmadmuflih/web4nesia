<?php

class ffJsTreeDataBuilder extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    /**
     * @var ffCollection
     */
    private $_dataCollection = [];

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct( ffCollection $collection ) {
        $this->_dataCollection = $collection;
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
    /*----------------------------------------------------------*/
    /* NODES
    /*----------------------------------------------------------*/
    public function addNode( $id, $text, $parent = '#' ) {

        $newNode = new ffStdClass();

        $newNode->id = $id;
        $newNode->parent = $parent;
        $newNode->text = $text;

        $this->_getDataCollection()->addItem( $newNode );

        return $this;
    }

    public function addLiAttr( $value ) {
        $this->_getDataCollection()->getLastItem()->li_attr = $value;

        return $this;
    }

    public function addAAttr( $value ) {
        $this->_getDataCollection()->getLastItem()->a_attr = $value;

        return $this;
    }

    public function addData( $name, $value ) {
        $this->_getDataCollection()->getLastItem()->data[ $name ] = $value;

        return $this;
    }

    /*----------------------------------------------------------*/
    /* MERGING
    /*----------------------------------------------------------*/
    public function addMultipleNodes( $nodes ) {

        if( $nodes instanceof ffJsTreeDataBuilder ) {
            $this->_getDataCollection()->addCollection( $nodes->getCollection() );
        }

        else if( is_array( $nodes ) ) {
            foreach( $nodes as $oneNode ) {
                $this->_getDataCollection()->addItem( $oneNode );
            }
        }
    }


    public function getCollection() {
        return $this->_getDataCollection();
    }

    public function clean() {
        $this->_getDataCollection()->clean();
        return $this;
    }

    public function getData() {

        $toReturn = [];

        foreach( $this->_getDataCollection() as $oneItem ) {
            $toReturn[] = $oneItem->getArray();
        }

        return $toReturn;
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
    /**
     * @return ffCollection
     */
    private function _getDataCollection()
    {
        return $this->_dataCollection;
    }

    /**
     * @param ffCollection $dataCollection
     */
    private function _setDataCollection($dataCollection)
    {
        $this->_dataCollection = $dataCollection;
    }
}