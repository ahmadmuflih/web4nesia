<?php

class ffThemeBuilderShortcodesWalker extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    /**
     * @var ffThemeBuilderElementManager
     */
    private $_themeBuilderElementManager = null;

    /**
     * @var ffWPLayer
     */
    private $_WPLayer = null;

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    private $_isEditMode = false;

    private $_shortcodesRegistered = false;

    private $_contentParams = array();

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/

    public function __construct( ffThemeBuilderElementManager $themeBuilderElementManager ) {
        $this->_setThemeBuilderElementManager( $themeBuilderElementManager );
        $this->_setWPLayer( ffContainer()->getWPLayer() );
    }

/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/
    public function render( $content ) {
        $this->_registerShortcodes();
        echo $this->_getWPLayer()->do_shortcode( $content );
    }

    public function setIsEditMode( $value ) {
        $this->_isEditMode = $value;

        $this->_getThemeBuilderElementManager()->setIsEditMode( $value );
    }

    public function getContentsParamCallback( $atts, $content, $shortcodeName ) {
        $route = $atts['route'];

        $this->_contentParams[ $route ] = $content;
    }

    private function _getOriginalShortcodeName( $shortcodeNameWithDepth ) {
        $lastStr = strrpos( $shortcodeNameWithDepth, '_');
        $shortcodeName = substr($shortcodeNameWithDepth, 0, $lastStr );
        return $shortcodeName;
    }

    private function _getShortcodeDepth( $shortcodeNameWithDepth ) {
        $lastStr = strrpos( $shortcodeNameWithDepth, '_');
        $strLen = strlen( $shortcodeNameWithDepth );
        $shortcodeDepth = substr( $shortcodeNameWithDepth, $lastStr + 1, $strLen - $lastStr );

        return $shortcodeDepth;
    }

    public function shortcodesCallback( $atts, $content, $shortcodeNameWithDepth ) {
        $shortcodeName = $this->_getOriginalShortcodeName( $shortcodeNameWithDepth );
        $shortcodeDepth = $this->_getShortcodeDepth( $shortcodeNameWithDepth );

        $elementId = $this->_getElementIdFromShortcodeName( $shortcodeName );
        $data = null;

        if( isset( $atts['data'] ) ) {
            $data = $this->_decodeDataAttrFromShortcode( $atts['data'] );
        }

        $uniqueId = null;
        if( isset( $atts['unique_id'] ) ) {
            $uniqueId = $atts['unique_id'];
        }

        $element = $this->_getThemeBuilderElementManager()->getElementById( $elementId );

        $this->_contentParams = null;
        if( $element->hasContentParams() ) {
            $this->_contentParams = array();
            $this->_getWPLayer()->do_shortcode( $content );
        }

        $result = $element->render( $data, $content, $uniqueId, $this->_contentParams );

        return $result;
    }

    private function _decodeDataAttrFromShortcode( $data ) {
        $data = urldecode( $data );

        return json_decode($data, true);
    }

    public function registerShortcodes() {
        return $this->_registerShortcodes();
    }

/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/
    private function _registerShortcodes() {
        if( $this->_shortcodesRegistered == true ) {
            return false;
        }

        $shortcodesId = $this->_getThemeBuilderElementManager()->getAllElementsIds();
        $WPLayer = $this->_getWPLayer();

        foreach( $shortcodesId as $oneId ) {
            $shortcodeName = $this->_getShortcodeNameFromElementId( $oneId );

            $WPLayer->add_shortcode( $shortcodeName, array( $this, 'shortcodesCallback' ) );

            for( $i = 0; $i < 10; $i++ ) {
                $WPLayer->add_shortcode( $shortcodeName.'_'.$i, array( $this, 'shortcodesCallback' ) );
            }
        }

        $WPLayer->add_shortcode('ffb_param', array( $this, 'getContentsParamCallback'));
    }

    private function _renderEditor( $content ) {

    }

    private function _getShortcodeNameFromElementId( $elementId ) {
        return 'ffb_' . $elementId;
    }

    private function _getElementIdFromShortcodeName( $elementId ) {
        return str_replace( 'ffb_', '', $elementId );
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
     * @return ffWPLayer
     */
    private function _getWPLayer()
    {
        return $this->_WPLayer;
    }

    /**
     * @param ffWPLayer $WPLayer
     */
    private function _setWPLayer($WPLayer)
    {
        $this->_WPLayer = $WPLayer;
    }


}