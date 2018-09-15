<?php

class ffElContainer extends ffThemeBuilderElementBasic {
    protected function _initData() {
        $this->_setData( ffThemeBuilderElement::DATA_ID, 'container');
        $this->_setData( ffThemeBuilderElement::DATA_NAME, 'Container');
        $this->_setData( ffThemeBuilderElement::DATA_HAS_DROPZONE, true);

        $this->_addDropzoneWhitelistedElement('row');
    }


    protected function _beforeRenderingAdminWrapper( ffOptionsQueryDynamic $query, $content, ffMultiAttrHelper $multiAttrHelper, ffStdClass $otherData ) {

    }

    protected function _getElementGeneralOptions( $s ) {
        $s->addOption( ffOneOption::TYPE_TEXT, 'text', 'Titulek text', 'hodnota ty pico');
 
    }

    protected function _render( ffOptionsQueryDynamic $query, $content, $data, $uniqueId ) {


        echo '<div class="container">';
            echo $this->_doShortcode( $content );
        echo '</div>';


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