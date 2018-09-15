<?php

class ffElColumn extends ffThemeBuilderElementBasic {
    protected function _initData() {
        $this->_setData( ffThemeBuilderElement::DATA_ID, 'column');
        $this->_setData( ffThemeBuilderElement::DATA_NAME, '1/3');
        $this->_setData( ffThemeBuilderElement::DATA_HAS_DROPZONE, true);

        $this->_addDropzoneBlacklistedElement('column');
    }

    protected function _getElementGeneralOptions( $s ) {
//        $s->addOption( ffOneOption::TYPE_TEXT, 'text', 'Titulek text', 'hodnota ty pico');
       $s->addOption( ffOneOption::TYPE_SELECT, 'col', 'Column XS', '4')
           ->addSelectNumberRange(1,12);
    }

    protected function _beforeRenderingAdminWrapper( ffOptionsQueryDynamic $query, $content, ffMultiAttrHelper $multiAttrHelper, ffStdClass $otherData ) {
        $multiAttrHelper->addParam('class', 'ffb-element-col-4');
    }

    protected function _render( ffOptionsQueryDynamic $query, $content, $data, $uniqueId ) {
        $class = 'col-xs-'.$query->get('col');

        echo '<div class="'.$class.'">';
            echo $this->_doShortcode( $content );
        echo '</div>';

//
//        echo do_shortcode( $content );
    }
}