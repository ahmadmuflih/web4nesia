<?php

class ffElSection extends ffThemeBuilderElementBasic {
    protected function _initData() {
        $this->_setData( ffThemeBuilderElement::DATA_ID, 'section');
        $this->_setData( ffThemeBuilderElement::DATA_NAME, 'Section');
        $this->_setData( ffThemeBuilderElement::DATA_HAS_DROPZONE, true);

        $this->_addDropzoneWhitelistedElement('row');
        $this->_addDropzoneWhitelistedElement('container');
    }


    protected function _beforeRenderingAdminWrapper( ffOptionsQueryDynamic $query, $content, ffMultiAttrHelper $multiAttrHelper, ffStdClass $otherData ) {

    }

    protected function _getElementGeneralOptions( $s ) {
        $s->addOption( ffOneOption::TYPE_TEXT, 'text', 'Titulek text', 'hodnota ty pico');

    }

    protected function _render( ffOptionsQueryDynamic $query, $content, $data, $uniqueId ) {


        echo '<section class="aa" data-test="testik" id="xx" >';
            echo $this->_doShortcode( $content );
        echo '</section>';


    }



        protected function _renderContentInfo_JS() {
    ?>
        <script data-type="ffscript">
            function ( query, options, $elementPreview, $element ) {


//                var content = query.get('select');

//                $elementPreview.html( 'sdsdsds' );


            }
        </script data-type="ffscript">
    <?php
    }

}