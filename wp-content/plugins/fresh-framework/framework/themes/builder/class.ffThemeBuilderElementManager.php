<?php

/**
 * Class ffThemeBuilderElementManager
 */
class ffThemeBuilderElementManager extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    /**
     * @var ffCollection
     */
    private $_elementCollection = null;

    /**
     * @var ffThemeBuilderElementFactory
     */
    private $_themeBuilderElementFactory = null;

    /**
     * @var ffCollection
     */
    private $_menuItemCollection = null;

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/

    private $_isEditMode = false;
/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct( ffCollection $elementCollection, ffCollection $menuItemCollection, ffThemeBuilderElementFactory $elementFactory ) {
        $this->_setElementCollection( $elementCollection );
        $this->_setMenuItemCollection( $menuItemCollection );
        $this->_setThemeBuilderElementFactory( $elementFactory );

    }

    public function addOverloadedElement( $elementClassName, $overloadedElementClassName ) {

        $this->_getThemeBuilderElementFactory()->loadElement( $overloadedElementClassName );
        $this->addElement( $elementClassName );
    }

    public function addElement( $elementClassName ) {
        $element = $this->_getThemeBuilderElementFactory()->createElement( $elementClassName );
        $element->setIsEditMode( $this->_isEditMode );
        $this->_getElementCollection()->addItem( $element, $element->getID() );
    }

    public function addMenuItem( $name, $id ) {
        $menuItem = new ffStdClass();
        $menuItem->name = $name;
        $menuItem->id = $id;

        $menuItemArray = $menuItem->getArray();

        $this->_getMenuItemCollection()->addItem( $menuItemArray, $id );
    }

    public function getAllElementsIds() {
        $toReturn = [];

        foreach( $this->_getElementCollection() as $key => $value ) {
            $toReturn[] = $key;
        }

        return $toReturn;
    }

    public function setIsEditMode( $value ) {
        $this->_isEditMode = $value;
        foreach( $this->_getElementCollection() as $oneItem ) {
            $oneItem->setIsEditMode( $value );
        }
    }

    public function getElementsData() {
        $data = array();
        $data['elements'] = array();
        foreach( $this->_getElementCollection() as $id => $element ) {
            $data['elements'][$id ] = $element->getElementDataForBuilder();
        }

        $data['menuItems'] = array();
        foreach( $this->_getMenuItemCollection() as $id => $menuItem ) {
            $data['menuItems'][$id] = $menuItem;
        }

        return $data;
    }

    public function getElementById( $id ) {
        return $this->_getElementCollection()->getItemById( $id );
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/

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
    private function _getElementCollection()
    {
        return $this->_elementCollection;
    }

    /**
     * @param ffCollection $elementCollection
     */
    private function _setElementCollection($elementCollection)
    {
        $this->_elementCollection = $elementCollection;
    }

    /**
     * @return ffThemeBuilderElementFactory
     */
    private function _getThemeBuilderElementFactory()
    {
        return $this->_themeBuilderElementFactory;
    }

    /**
     * @param ffThemeBuilderElementFactory $themeBuilderElementFactory
     */
    private function _setThemeBuilderElementFactory($themeBuilderElementFactory)
    {
        $this->_themeBuilderElementFactory = $themeBuilderElementFactory;
    }

    /**
     * @return ffCollection
     */
    private function _getMenuItemCollection()
    {
        return $this->_menuItemCollection;
    }

    /**
     * @param ffCollection $menuItemCollection
     */
    private function _setMenuItemCollection($menuItemCollection)
    {
        $this->_menuItemCollection = $menuItemCollection;
    }

}