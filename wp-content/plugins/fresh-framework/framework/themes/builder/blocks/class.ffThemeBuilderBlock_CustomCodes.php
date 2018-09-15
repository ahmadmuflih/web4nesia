<?php

class ffThemeBuilderBlock_CustomCodes extends ffThemeBuilderBlock {
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
        $this->_setInfo( ffThemeBuilderBlock::INFO_ID, 'custom-codes');
        $this->_setInfo( ffThemeBuilderBlock::INFO_WRAPPING_ID, 'cc');
        $this->_setInfo( ffThemeBuilderBlock::INFO_WRAP_AUTOMATICALLY, true);
        $this->_setInfo( ffThemeBuilderBlock::INFO_IS_REFERENCE_SECTION, false);
        $this->_setInfo( ffThemeBuilderBlock::INFO_SAVE_ONLY_DIFFERENCE, true);
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

        $s->addElement( ffOneElement::TYPE_TABLE_START );

            $s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Info');

                $s->addElement(ffOneElement::TYPE_DESCRIPTION,'', 'Here you can add your custom CSS and JS codes. Every element has unique css class, so you can bound your CSS and JS to this class. CSS and JS will be printed at the very bottom of the page');

                $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ff-insert-unique-id">UNIQUE ID</div>');
                $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ff-insert-unique-css-class">UNIQUE CSS CLASS</div>');
                $s->addElement(ffOneElement::TYPE_HTML,'', '<input type="text" class="ff-insert-unique-css-selector">');

            $s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

            $s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'CSS');

                $s->addOptionNL(ffOneOption::TYPE_TEXTAREA, 'css', 'Custom CSS', '');

            $s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

        $s->addElement( ffOneElement::TYPE_TABLE_END );
//        $colXS = $this->_getInfo('col-xs', 'none');
//        $colSM = $this->_getInfo('col-sm', 'none');
//        $colMD = $this->_getInfo('col-md', 'none');
//        $colLG = $this->_getInfo('col-lg', 'none');
//
//        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-xs', 'Col XS', $colXS)
//            ->addSelectValue('None', 'none')
//            ->addSelectNumberRange(1,12);
//        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-sm', 'Col SM', $colXS)
//            ->addSelectValue('None', 'none')
//            ->addSelectNumberRange(1,12);
//        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-md', 'Col MD', $colXS)
//            ->addSelectValue('None', 'none')
//            ->addSelectNumberRange(1,12);
//        $s->addOptionNL( ffOneOption::TYPE_SELECT, 'col-lg', 'Col LG', $colXS)
//            ->addSelectValue('None', 'none')
//            ->addSelectNumberRange(1,12);
    }
/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
}