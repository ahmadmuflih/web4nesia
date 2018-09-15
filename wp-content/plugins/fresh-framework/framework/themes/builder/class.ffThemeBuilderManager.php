<?php

class ffThemeBuilderManager extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    /**
     * @var ffThemeBuilderElementManager
     */
    private $_themeBuilderElementManager = null;

    /**
     * @var ffThemeBuilderShortcodesWalker
     */
    private $_themeBuilderShortcodesWalker = null;
/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    private $_isEditMode = false;

    private $_overloadedFrameworkElements = array();

    private $_removedFrameworkElements = array();
/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct() {
        $this->_setThemeBuilderElementManager( ffContainer()->getThemeFrameworkFactory()->getThemeBuilderElementManager() );
        $this->_setThemeBuilderShortcodesWalker( ffContainer()->getThemeFrameworkFactory()->getThemeBuilderShortcodesWalker() );
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
    public function setIsEditMode( $value ) {
        $this->_isEditMode = $value;
        $this->_getThemeBuilderShortcodesWalker()->setIsEditMode( $value );
        $this->_getThemeBuilderElementManager()->setIsEditMode( $value );
    }

    public function enableBuilderSupport() {
        ffContainer()->getMetaBoxes()->getMetaBoxManager()->addMetaBoxClassName('ffMetaBoxThemeBuilder');
        $this->_addFrameworkElements();


    }

    public function render( $value ) {
        $this->_getThemeBuilderShortcodesWalker()->render( $value );
    }

    public function overloadFrameworkElement( $frameworkElementClassName, $newClassName ) {

        $this->_overloadedFrameworkElements[ $frameworkElementClassName ] = $newClassName;
    }

    public function removeFrameworkElement( $frameworkElementClassName ) {
        $this->_removedFrameworkElements[] = $frameworkElementClassName;
    }

    public function addElement( $className ) {
        $this->_getThemeBuilderElementManager()->addElement( $className );
    }
/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/
    private function _addFrameworkElements() {
        $this->_addFrameworkElement('ffElSection');
        $this->_addFrameworkElement('ffElRow');
        $this->_addFrameworkElement('ffElColumn');
        $this->_addFrameworkElement('ffElContainer');
//        $this->_addFrameworkElement('ffElServices1');
    }

    private function _addFrameworkElement( $elementName ) {
        $hasBeenOverloaded = isset($this->_overloadedFrameworkElements[ $elementName]);


        if( $hasBeenOverloaded ) {
            $newClassName = $this->_overloadedFrameworkElements[ $elementName];
            $oldClassName = $elementName;
            $this->_getThemeBuilderElementManager()->addOverloadedElement( $newClassName, $oldClassName );
        } else {
            $this->_getThemeBuilderElementManager()->addElement( $elementName );
        }
    }
/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
    /**
     * @return ffThemeBuilderElementManager
     */
    private function _getThemeBuilderElementManager()
    {
        return $this->_themeBuilderElementManager;
    }

    /**
     * @param ffThemeBuilderElementManager $themeBuilderElementManager
     */
    private function _setThemeBuilderElementManager($themeBuilderElementManager)
    {
        $this->_themeBuilderElementManager = $themeBuilderElementManager;
    }

    /**
     * @return ffThemeBuilderShortcodesWalker
     */
    private function _getThemeBuilderShortcodesWalker()
    {
        return $this->_themeBuilderShortcodesWalker;
    }

    /**
     * @param ffThemeBuilderShortcodesWalker $themeBuilderShortcodesWalker
     */
    private function _setThemeBuilderShortcodesWalker($themeBuilderShortcodesWalker)
    {
        $this->_themeBuilderShortcodesWalker = $themeBuilderShortcodesWalker;
    }
}