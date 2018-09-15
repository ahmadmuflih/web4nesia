<?php

abstract class ffThemeBuilderBlock extends ffBasicObject {
    const INFO_ID = 'id';
    const INFO_WRAPPING_ID = 'wrapping_id';
    const INFO_WRAP_AUTOMATICALLY = 'wrap_automatically';
    const INFO_IS_REFERENCE_SECTION = 'is_reference_section';
    const INFO_SAVE_ONLY_DIFFERENCE = 'save_only_difference';

    const HTML = 'ffThemeBuilderBlock_HTML';
    const BOOTSTRAP_COLUMNS = 'ffThemeBuilderBlock_BootstrapColumns';
    const ADVANCED_TOOLS = 'ffThemeBuilderBlock_AdvancedTools';
    const CUSTOM_CODES = 'ffThemeBuilderBlock_CustomCodes';
    const LINK = 'ffThemeBuilderBlock_Link';


/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    /**
     * Informations - id, wrapping ID and other stuff
     * @var array
     */
    protected $_info = array();

    /**
     * @var params for printing the block
     */
    protected $_param = array();

    /**
     * @var ffThemeBuilderOptionsExtender
     */
    protected $_optionsExtender = null;

	protected $_queryHasBeenFilled = true;

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct( ffThemeBuilderOptionsExtender $optionsExtender ) {
        $this->_setOptionsExtender( $optionsExtender );
        $this->_init();

    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/

    public function setParam( $name, $value ) {
        $this->_param[ $name ] = $value;

        return $this;
    }

    public function getParam( $name, $default = null ) {
        if( isset( $this->_param[ $name ] ) ) {
            return $this->_param[ $name ];
        } else {
            return $default;
        }
    }
/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/
	/**
	 * @param $s ffOneStructure|ffThemeBuilderOptionsExtender
	 */
    public function injectOptions( $s ) {
        if( $this->getWrapAutomatically() ) {

            if( $this->_getInfo( ffThemeBuilderBlock::INFO_IS_REFERENCE_SECTION, false ) ) {
                $section = $s->startReferenceSection( $this->getWrappingId() );
            } else {
                $section =  $s->startSection( $this->getWrappingId() );
            }

            if( $this->_getInfo( ffThemeBuilderBlock::INFO_SAVE_ONLY_DIFFERENCE, false) ) {

                $section->addParam('save-only-difference', true);
            }
        }

        $this->_injectOptions( $s );

        if( $this->getWrapAutomatically() ) {
            if( $this->_getInfo( ffThemeBuilderBlock::INFO_IS_REFERENCE_SECTION, false ) ) {
                $s->endReferenceSection();
            } else {
                $s->endSection();
            }
        }
    }

    public function getWrapAutomatically() {
        return $this->_getInfo( ffThemeBuilderBlock::INFO_WRAP_AUTOMATICALLY, true );
    }

    public function getId() {
        return $this->_getInfo( ffThemeBuilderBlock::INFO_ID);
    }

    public function getWrappingId() {
        return $this->_getInfo( ffThemeBuilderBlock::INFO_WRAPPING_ID );
    }

    public function setWrappingId( $wrappingId ) {
        $this->_setInfo( ffThemeBuilderBlock::INFO_WRAPPING_ID, $wrappingId );
    }

    public function render( ffOptionsQueryDynamic $query ) {
        if( $this->getWrapAutomatically() ) {
            $wrappingId = $this->getWrappingId();

			if( $query->queryExists( $wrappingId ) ) {
				return $this->_render( $query->get( $wrappingId ) );
			} else {
				$this->_queryHasBeenFilled = false;
				$rendered = $this->_render( $query );
				$this->_queryHasBeenFilled = true;

				return $rendered;
			}
        } else {
            return $this->_render( $query );
        }
    }


/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/

    protected function _setInfo( $name, $value ) {
        $this->_info[ $name ] = $value;
    }

    protected function _getInfo( $name, $default = null ) {
        if( isset( $this->_info[ $name ] ) ) {
            return $this->_info[ $name ];
        } else {
            return $default;
        }
    }

	protected function _queryIsEmpty() {
		return !$this->_queryHasBeenFilled;
	}

    abstract protected function _injectOptions( ffThemeBuilderOptionsExtender $s );
    abstract protected function _init();

	/**
	 * @param $query ffOptionsQueryDynamic
	 * @return mixed
	 */
    abstract protected function _render( $query );


/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/

    protected function _setId( $id ) {
        $this->_id = $id;
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