<?php

/**
 * Class ffThemeOnePageManager
 *
 * Handle part of the "OnePage" in themes, Hypnos+;
 *
 * @since 1.9.1
 */
class ffThemeOnePageManager extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    /**
     * @var ffClassLoader
     */
    private $_classLoader = null;

    /**
     * @var ffMetaBoxManager
     */
    private $_metaBoxManager = null;
/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    /**
     * @var className of the one page options holder, provided in theme, which contains all the important sections)
     */
    private $_onePageOptionsHolderClassName = null;

    /**
     * @var function, which is called and return the content of the one page options
     */
    private $_loaderCallback = null;

    /**
     * @var function, which is called and save the content of the one page options
     */
    private $_saverCallback = null;

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct() {
        $fwc = ffContainer();

        $this->_setClassLoader( $fwc->getClassLoader() );
        $this->_setMetaBoxManager( $fwc->getMetaBoxes()->getMetaBoxManager() );
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
    /**
     * Theme calls this function and framework provides all the one page stuff. All which theme has to do is to
     * provide page-onepage.php file...
     */
    public function enableOnePageSupport() {
        $this->_registerMetaBoxes();
    }


/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/
    public function setOnePageOptionsHolderClassName( $className ) {
        $this->_onePageOptionsHolderClassName = $className;
    }

    public function setLoaderCallback( $loaderCallback ) {
        $this->_loaderCallback = $loaderCallback;
    }

    public function setSaverCallback( $saverCallback ) {
        $this->_saverCallback = $saverCallback;
    }

    public function getOnePageOptionsHolderClassName() {
        return $this->_onePageOptionsHolderClassName;
    }

    public function getLoaderCallback() {
        return $this->_loaderCallback;
    }

    public function getSaverCallback() {
        return $this->_saverCallback;
    }
/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/
    private function _registerMetaBoxes() {
        $this->_getMetaBoxManager()->addMetaBoxClassName('ffMetaBoxOnePageFramework');
    }
/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
    /**
     * @return ffClassLoader
     */
    private function _getClassLoader()
    {
        return $this->_classLoader;
    }

    /**
     * @param ffClassLoader $classLoader
     */
    private function _setClassLoader($classLoader)
    {
        $this->_classLoader = $classLoader;
    }

    /**
     * @return ffMetaBoxManager
     */
    private function _getMetaBoxManager()
    {
        return $this->_metaBoxManager;
    }

    /**
     * @param ffMetaBoxManager $metaBoxManager
     */
    private function _setMetaBoxManager($metaBoxManager)
    {
        $this->_metaBoxManager = $metaBoxManager;
    }


}