<?php

class ffElRow extends ffThemeBuilderElementBasic {
    protected function _initData() {
        $this->_setData( ffThemeBuilderElement::DATA_ID, 'row');

        $this->_setData( ffThemeBuilderElement::DATA_NAME, 'Row');
        $this->_setData( ffThemeBuilderElement::DATA_HAS_DROPZONE, true);

        $this->_addDropzoneWhitelistedElement('column');
    }

    protected function _getElementGeneralOptions( $s ) {
        $s->addOption( ffOneOption::TYPE_SELECT, 'col', 'Column XS', '4')
           ->addSelectNumberRange(1,12);
    }

    protected function _beforeRenderingAdminWrapper( ffOptionsQueryDynamic $query, $content, ffMultiAttrHelper $multiAttrHelper, ffStdClass $otherData ) {

    }

    protected function _render( ffOptionsQueryDynamic $query, $content, $data, $uniqueId ) {



//        var_dump( '['.$query->get('text').']' );
//        var_Dump( $query->getOnlyData() );

        echo '<div class="row">';
            echo $this->_doShortcode( $content );
        echo '</div>';

//        var_Dump( $query->getOnlyData() );
//         var_dump( '[/'.$query->get('text').']' );
//        echo 'xxx';
//        var_dump( $query->get('text'));

//        foreach( $query->get('sub-headings') as $oneHeading ) {
//            var_dump( $oneHeading->get('text') );
//        }
    }

    protected function _renderContentInfo_JS() {
    ?>
        <script data-type="ffscript">
            function ( query, options, $elementPreview, $element ) {

//                $element.find('.ffb-header-name:first').html('Normal Row - hovno');
//                $elementInfo.html('sdsdsdsd');
//                $elementInfo.html( '<h3>Text value:</h3>' + query.get('text') );


            }
        </script data-type="ffscript">
    <?php
    }


//    protected function _renderAdmin( ffOptionsQueryDynamic $query, $content, $data ) {
//        $id = $this->getID();
//
//        $dataCoded = htmlspecialchars(json_encode( $data ));
//        echo '<div class="ffb-element ffb-element-'.$id.' clearfix ffb-element--position--block" data-options="'.$dataCoded.'" data-element-id="'.$id.'">';
//
//            echo '<div class="ffb-header clearfix">';
//                echo '<div class="ffb-header-name">'.$id.'</div>';
//                echo '<div class="ffb-header__button action-toggle-context-menu dashicons dashicons-admin-generic"></div>';
//                echo '<div class="ffb-header__button action-edit-element dashicons dashicons-admin-customizer"></div>';
//            echo '</div>';
//
//            echo '<div class="ffb-element-info">';
//
//            echo '</div>';
//
//            echo '<div class="ffb-dropzone clearfix">';
//                echo do_shortcode( $content );
//            echo '</div>';
//
//        echo '</div>';
//    }

}