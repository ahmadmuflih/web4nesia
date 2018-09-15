<?php

class ffThemeBuilderBlock_BootstrapColumns extends ffThemeBuilderBlock {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    const PARAM_RETURN_AS_STRING = 'return_as_string';
/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    protected function _init() {
        $this->_setInfo( ffThemeBuilderBlock::INFO_ID, 'bootstrap-columns');
        $this->_setInfo( ffThemeBuilderBlock::INFO_WRAPPING_ID, 'bootstrap-columns');
        $this->_setInfo( ffThemeBuilderBlock::INFO_WRAP_AUTOMATICALLY, true);
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
    private function _getBreakpoints() {
        $breakpoints = Array();
        $breakpoints[] = 'col-xs';
        $breakpoints[] = 'col-sm';
        $breakpoints[] = 'col-md';
        $breakpoints[] = 'col-lg';

        return $breakpoints;
    }

    protected function _render( $query ) {

        $classes = Array();

        foreach( $this->_getBreakpoints() as $oneBreakpoint ) {
            $value = $query->get( $oneBreakpoint );

            if( $value == 'none' ) {
                continue;
            }

            // like col-md-6
            $classString = $oneBreakpoint .'-' . $value;

            $classes[] = $classString;
        }


        if( $this->getParam( ffThemeBuilderBlock_BootstrapColumns::PARAM_RETURN_AS_STRING, true ) ) {
            return implode(' ', $classes);
        } else {
            return $classes;
        }
    }

    protected function _injectOptions( ffThemeBuilderOptionsExtender $s ) {
        $colXS = $this->_getInfo('col-xs', 'none');
        $colSM = $this->_getInfo('col-sm', 'none');
        $colMD = $this->_getInfo('col-md', 'none');
        $colLG = $this->_getInfo('col-lg', 'none');

        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-xs', 'Col XS', $colXS)
            ->addSelectValue('None', 'none')
            ->addSelectNumberRange(1,12);
        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-sm', 'Col SM', $colXS)
            ->addSelectValue('None', 'none')
            ->addSelectNumberRange(1,12);
        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-md', 'Col MD', $colXS)
            ->addSelectValue('None', 'none')
            ->addSelectNumberRange(1,12);
        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-lg', 'Col LG', $colXS)
            ->addSelectValue('None', 'none')
            ->addSelectNumberRange(1,12);
    }
/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
}