<?php

/**
 * Class ffThemeLayoutManager
 * Handles the whole layout business. Only thing required is a Options Holder class name, and theme name
 */
class ffThemeLayoutManager extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    /**
     * @var ffLayoutPostType
     */
    private $_layoutPostType = null;

    /**
     * @var ffWPLayer
     */
    private $_WPLayer = null;

    /**
     * @var ffLayoutPrinter
     */
    private $_layoutPrinter = null;
/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    private $_themeName = null;

    private $_optionsHolderName = null;

    private $_defaultOptionsCallbacks = null;
/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct( $layoutPostType, ffWPLayer $WPLayer, ffLayoutPrinter $layoutPrinter ) {
        $this->_setLayoutPostType( $layoutPostType );
        $this->_setWPLayer( $WPLayer );
        $this->_setLayoutPrinter( $layoutPrinter );
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
    public function addLayoutSupport() {
        $this->_getLayoutPostType()->setThemeName( $this->_getThemeName() );
        $this->_getLayoutPostType()->registerPostType();

       $this->_getWPLayer()->add_action('admin_bar_menu', array( $this, 'actAdminBarMenu'), 999);
    }

    public function actAdminBarMenu( $wp_admin_bar ) {
        $WPLayer = $this->_getWPLayer();
        $layoutIds = $this->_getLayoutPrinter()->getPrintedLayoutsId();

        $layoutPostName = $this->_getLayoutPostType()->getPostTypeName();

        $topMenu = array();
        $topMenu['id'] = 'freshface-layouts';
        $topMenu['title'] = '<span style="margin-top: 2px; font: 400 20px/1 dashicons !important; -moz-osx-font-smoothing: grayscale; -moz-osx-font-smoothing: grayscale; background-image: none !important; float: left; font: 400 20px/1 dashicons; margin-right: 6px; padding: 4px 0; position: relative;
" class="dashicons dashicons-layout"></span>Current Layouts';
        $topMenu['parent'] = false;
        $topMenu['href'] = $WPLayer->admin_url('edit.php?post_type='.$layoutPostName);

        $wp_admin_bar->add_node( $topMenu );

        foreach( $layoutIds as $oneID ){
            $post = ffContainer()->getPostLayer()->getPostGetter()->getPostByID( $oneID );

            $layoutItem = array();
            $layoutItem['id'] = $oneID;
            $layoutItem['title'] = $post->getTitle();
            $layoutItem['parent'] = 'freshface-layouts';
            $layoutItem['href'] = $WPLayer->get_edit_post_link( $oneID );

            $wp_admin_bar->add_node( $layoutItem );

        }
    }

    public function printLayout() {
        // mit nejaky printer, ten
    }


    public function getLayoutPostTypeName() {
        return $this->_getLayoutPostType()->getPostTypeName();
    }



/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/
    public function setThemeName( $themeName ) {
        $this->_themeName = $themeName;
    }

    public function setLayoutsOptionsHolderClassName( $className ) {
        $this->_optionsHolderName = $className;
    }

    public function getLayoutsOptionsHolderClassName() {
        return $this->_optionsHolderName;
    }

    public function getThemeName() {
        return $this->_getThemeName();
    }
/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
    /**
     * @return null
     */
    private function _getThemeName()
    {
        return $this->_themeName;
    }

    /**
     * @return ffLayoutPostType
     */
    private function _getLayoutPostType()
    {
        return $this->_layoutPostType;
    }

    /**
     * @param ffLayoutPostType $layoutPostType
     */
    private function _setLayoutPostType($layoutPostType)
    {
        $this->_layoutPostType = $layoutPostType;
    }

    /**
     * @return ffLayoutPrinter
     */
    private function _getLayoutPrinter()
    {
        return $this->_layoutPrinter;
    }

    /**
     * @param ffLayoutPrinter $layoutPrinter
     */
    private function _setLayoutPrinter($layoutPrinter)
    {
        $this->_layoutPrinter = $layoutPrinter;
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