<?php

abstract class ffThemeBuilderElement extends ffBasicObject {
    const DATA_ID = 'id';
    const DATA_NAME = 'name';
    const DATA_HAS_DROPZONE = 'has_dropzone';
    const DATA_CONNECT_WITH = 'connect_with';
    const DATA_HAS_CONTENT_PARAMS = 'has_content_params';

/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    /**
     * @var ffOptionsQueryDynamic
     */
    private $_queryDynamic = null;

    /**
     * @var ffWPLayer
     */
    private $_WPLayer = null;

    /**
     * @var ffThemeBuilderBlockFactory
     */
    private $_themeBuilderBlockFactory = null;

    /**
     * @var ffThemeBuilderOptionsExtender
     */
    private $_optionsExtender = null;

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    /**
     * Settings like "name, id, has dropzone" are stored here. See the constants at the top of this file
     * @var array()
     */
    private $_data = array();

    /**
     * If the element is printed in backend builder or fronted (For user)
     * @var bool
     */
	protected $_isEditMode = false;

	protected $_defaultOptionsData = null;

    protected $_elementOptionsStructure = null;


/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct( ffThemeBuilderOptionsExtender $optionsExtender ) {
        $this->_setOptionsExtender( $optionsExtender );
        $this->_initData();


		$queryDynamic =ffContainer()->getOptionsFactory()->createQueryDynamic(null, array($this, 'getElementOptionsStructure') );

		$queryDynamic->setIteratorValidationCallback( array( $this, 'queryIteratorValidation') );
		$queryDynamic->setIteratorStartCallback( array( $this, 'queryIteratorStart') );
		$queryDynamic->setIteratorEndCallback( array( $this, 'queryIteratorEnd') );

		$this->_setQueryDynamic($queryDynamic);


        

        $this->_setWPLayer( ffContainer()->getWPLayer() );

        $this->_setThemeBuilderBlockFactory( ffContainer()->getThemeFrameworkFactory()->getThemeBuilderBlockFactory() );
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/

    public function getPreviewImageUrl() {
        return $this->getBaseUrlOfElement() . '/preview.jpg';
    }

    public function getBaseUrlOfElement() {
        $className = get_class( $this );
        $classUrl = ffContainer()->getClassLoader()->getClassUrl( $className );
        $toReturn = dirname( $classUrl );

        return $toReturn;
    }

    public function getElementOptionsJSONString() {
        return json_encode( $this->getElementOptionsJSON() );
    }

    /**
     * get the options json (basic options, the default ones)
     * @return array
     */
    public function getElementOptionsJSON() {
        $structure = $this->getElementOptionsStructure();
        $jsonConvertor = ffContainer()->getOptionsFactory()->createOptionsPrinterJSONConvertor( null, $structure );
//
        $json = $jsonConvertor->walk();
        return $json;
    }

    public function getElementOptionsData() {
        if( $this->_defaultOptionsData == null ) {
            $structure = $this->getElementOptionsStructure();
            $arrayConvertor = ffContainer()->getOptionsFactory()->createOptionsArrayConvertor( null, $structure );

            $this->_defaultOptionsData = $arrayConvertor->walk();
        }
//
        return $this->_defaultOptionsData;
    }

        /**
     * get the options structure (Basic options, the default ones)
     * @return ffOneStructure
     */
    abstract public function getElementOptionsStructure();

    public function render( $data, $content = null, $uniqueId = null, $contentParams = null ) {
        $query = $this->_getQueryDynamic();

        $query->setData( $data );

        if( $this->hasContentParams() && $contentParams != null ) {
            foreach( $contentParams as $route => $value ) {
                //setDataValue
                $query->setDataValue( $route, $value );

            }
        }

        ob_start();
        if( $this->_isEditMode ) {
            $this->_renderAdmin( $query->get('o gen'), $content, $query->getOnlyData(), $uniqueId);
        } else {

            $this->_render( $query->get('o gen'), $content, $query->getOnlyData(), $uniqueId );
        }
        $content = ob_get_contents();
        ob_end_clean();

        if( !$this->_isEditMode ) {
            if ($query->queryExists('o a-t')) {
                $content = $this->_getBlock(ffThemeBuilderBlock::ADVANCED_TOOLS)->setParam('content', $content)->render($query->get('o'));
            }
        }

        return $content;
    }


/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/
    public function setIsEditMode( $value ) {
        $this->_isEditMode = $value;
    }

    public function getIsEditMode() {
        return $this->_isEditMode;
    }

    public function getID() {
        return $this->_getData( ffThemeBuilderElement::DATA_ID );
    }

    public function getData() {
        return $this->_data;
    }

    public function getElementDataForBuilder() {
        $data = array();
        $data['id'] = $this->_getData( ffThemeBuilderElement::DATA_ID );
        $data['name'] = $this->_getData( ffThemeBuilderElement::DATA_NAME );

        $data['optionsStructure'] = $this->getElementOptionsJSONString();

        $data['functions'] = array();
        $data['functions']['renderContentInfo_JS'] = $this->_getJSFunction('_renderContentInfo_JS');

        $data['defaultHtml'] = $this->_getDefaultHTML();

        $data['previewImage'] = $this->getPreviewImageUrl();

        return $data;
    }

    public function hasContentParams() {
        return $this->_getData( ffThemeBuilderElement::DATA_HAS_CONTENT_PARAMS, false);
    }

/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/

    protected function _getDefaultHTML() {
        $query = $this->_getQueryDynamic()->setData(null);
        $data = $this->getElementOptionsData();


        ob_start();
        $this->_renderAdmin( $query, '', $data, null );
        $defaultHTML = ob_get_contents();
        ob_end_clean();

        return $defaultHTML;
//        return '';
    }


    protected abstract function _initData();

	/**
	 * @param $s ffOneStructure|ffThemeBuilderOptionsExtender
	 * @return mixed
	 */
    protected abstract function _getElementGeneralOptions( $s );
    protected abstract function _render( ffOptionsQueryDynamic $query, $content, $data, $uniqueId );
    protected abstract function _renderAdmin( ffOptionsQueryDynamic $query, $content, $data, $uniqueId );
    protected abstract function _beforeRenderingAdminWrapper( ffOptionsQueryDynamic $query, $content, ffMultiAttrHelper $multiAttrHelper, ffStdClass $otherData );
	public abstract function queryIteratorValidation( $query );
	public abstract function queryIteratorStart( $query );
	public abstract function queryIteratorEnd( $query );

    protected abstract function _renderContentInfo_JS();

    protected function _getJSFunction( $functionName ) {
        ob_start();
            call_user_func( array( $this, $functionName) );
        $content = ob_get_contents();
        ob_end_clean();

        $content = str_replace('<script data-type="ffscript">', '', $content);
        $content = str_replace('</script data-type="ffscript">', '', $content);

        return $content;
    }

    protected function _setData( $name, $value ) {
        $this->_data[ $name ] = $value;
    }

    protected function _doShortcode( $content ) {
        return $this->_getWPLayer()->do_shortcode( $content );
    }

    protected function _getData( $name, $default = null ) {
        if( isset( $this->_data[ $name ] ) ) {
            return $this->_data[ $name ];
        } else {
            return $default;
        }
    }





    /**
     * @param $blockClassName
     * @return ffThemeBuilderBlock
     */
    protected function _getBlock( $blockClassName ) {
        return $this->_getThemeBuilderBlockFactory()->createBlock( $blockClassName );
    }


/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
    /**
     * @return ffOptionsQueryDynamic
     */
    private function _getQueryDynamic()
    {
        return $this->_queryDynamic;
    }

    /**
     * @param ffOptionsQueryDynamic $queryDynamic
     */
    private function _setQueryDynamic($queryDynamic)
    {
        $this->_queryDynamic = $queryDynamic;
    }

    /**
     * @return ffWPLayer
     */
    protected function _getWPLayer()
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

    /**
     * @return ffThemeBuilderBlockFactory
     */
    private function _getThemeBuilderBlockFactory()
    {
        return $this->_themeBuilderBlockFactory;
    }

    /**
     * @param ffThemeBuilderBlockFactory $themeBuilderBlockFactory
     */
    private function _setThemeBuilderBlockFactory($themeBuilderBlockFactory)
    {
        $this->_themeBuilderBlockFactory = $themeBuilderBlockFactory;
    }

    /**
     * @return ffThemeBuilderOptionsExtender
     */
    protected function _getOptionsExtender()
    {
        return $this->_optionsExtender;
    }

    /**
     * @param ffThemeBuilderOptionsExtender $optionsExtender
     */
    private function _setOptionsExtender($optionsExtender)
    {
        $this->_optionsExtender = $optionsExtender;
    }





}