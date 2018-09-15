<?php

abstract class ffThemeBuilderElementBasic extends ffThemeBuilderElement {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    /**
     * List of elements which are declined in the dropzone
     * @var null
     */
    protected $_dropzoneElementBlacklist = array();

    /**
     * List of elements, which are accepted in the dropzone
     * @var null
     */
    protected $_dropzoneElementWhitelist = array();
/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/
    /*----------------------------------------------------------*/
    /* DROPZONES
    /*----------------------------------------------------------*/
    protected function _addDropzoneBlacklistedElement( $elementId ) {
        $this->_dropzoneElementBlacklist[] = $elementId;
    }

    protected function _addDropzoneWhitelistedElement( $elementId ) {
        $this->_dropzoneElementWhitelist[] = $elementId;
    }

    protected function _getBlacklistedElements() {
        $toReturn = null;
        if( !empty( $this->_dropzoneElementBlacklist ) ) {
            $toReturn = htmlspecialchars(json_encode($this->_dropzoneElementBlacklist));
        }

        return $toReturn;
    }

    protected function _getWhitelistedElements() {
        $toReturn = null;
        if( !empty( $this->_dropzoneElementWhitelist ) ) {
            $toReturn = htmlspecialchars(json_encode($this->_dropzoneElementWhitelist));
        }

        return $toReturn;
    }

    /*----------------------------------------------------------*/
    /* RENDER ADMIN
    /*----------------------------------------------------------*/
    protected function _renderAdmin_getClasses( ffMultiAttrHelper $multiAttrHelper ) {
        $multiAttrHelper->addParam('class', 'ffb-element');
        $multiAttrHelper->addParam('class', 'ffb-element-'. $this->getID() );
    }

    protected function _renderAdmin_getParams( ffMultiAttrHelper $multiAttrHelper, ffOptionsQueryDynamic $query, $content, $data, $uniqueId ) {
        $dataCoded = htmlspecialchars(json_encode( $data ));
        $multiAttrHelper->setParam('data-options', $dataCoded);
        $multiAttrHelper->setParam('data-element-id', $this->getID());
        $multiAttrHelper->setParam('data-unique-id', $uniqueId);

        $blacklistedElements = $this->_getBlacklistedElements();
        $whitelistedElements = $this->_getWhitelistedElements();

        if( $blacklistedElements != null ) {
            $multiAttrHelper->setParam('data-dropzone-mode', 'blacklist');
            $multiAttrHelper->setParam('data-dropzone-list', $blacklistedElements);
        } else if( $whitelistedElements != null ) {
            $multiAttrHelper->setParam('data-dropzone-mode', 'whitelist');
            $multiAttrHelper->setParam('data-dropzone-list', $whitelistedElements);
        }

    }

    protected function _renderAdmin( ffOptionsQueryDynamic $query, $content, $data, $uniqueId ) {
        $name = $this->_getData( ffThemeBuilderElement::DATA_NAME);
        $multiAttrHelper = ffContainer()->getMultiAttrHelper();

        $this->_renderAdmin_getClasses( $multiAttrHelper );
        $this->_renderAdmin_getParams( $multiAttrHelper, $query, $content, $data, $uniqueId );

        $otherData = new ffStdClass();
        $otherData->uniqueId = $uniqueId;
        $this->_beforeRenderingAdminWrapper( $query, $content, $multiAttrHelper, $otherData );

        $hasDropzone = $this->_getData( ffThemeBuilderElement::DATA_HAS_DROPZONE, false);

        echo '<div ' . $multiAttrHelper->getAttrString() . '>';
            echo '<div class="ffb-header clearfix">';
                echo '<div class="ffb-header__button ffb-header__button-left dashicons dashicons-arrow-left-alt2 action-column-smaller"></div>';
                echo '<div class="ffb-header__button ffb-header__button-left dashicons dashicons-arrow-right-alt2 action-column-bigger"></div>';
                echo '<div class="ffb-header-name">'.$name.'</div>';
                echo '<div class="ffb-header__button ffb-header__button-right dashicons dashicons-admin-generic action-toggle-context-menu"></div>';
                echo '<div class="ffb-header__button ffb-header__button-right dashicons dashicons-edit action-edit-element"></div>';
                if( $hasDropzone ) {
                    echo '<div class="ffb-header__button ffb-header__button-right dashicons dashicons-plus action-add-element"></div>';
                }
            echo '</div>';
            echo '<div class="ffb-element-preview">';

            echo '</div>';
            if( $hasDropzone ) {
                echo '<div class="ffb-dropzone ffb-dropzone-'.$this->getID().' clearfix">';
                echo do_shortcode($content);
                echo '</div>';
            }
        echo '</div>';
    }

    /*----------------------------------------------------------*/
    /* RENDER CONTENT
    /*----------------------------------------------------------*/
    protected function _renderContentInfo_JS() {
        ?>
            <script data-type="ffscript">
                function ( query, options, $elementInfo, $element ) {

    //                $elementInfo.html( '<h3>Text value:</h3>' + query.get('text') );


                }
            </script data-type="ffscript">
        <?php
    }


        /**
     * get the options structure (Basic options, the default ones)
     * @return ffOneStructure
     */
    public function getElementOptionsStructure() {
        if( $this->_elementOptionsStructure == null ) {
            $s = ffContainer()->getOptionsFactory()->createStructure();


            $extender = $this->_getOptionsExtender();

            $extender->setStructure( $s );


            $s->startSection('o');


            $extender->startTabs();

                $extender->startTab('General', true);
                    $s->startSection('gen');
                        $this->_getElementGeneralOptions($extender);
                    $s->endSection();
                $extender->endTab();

                $extender->startTab( 'Advanced');
                    $this->_getBlock( ffThemeBuilderBlock::ADVANCED_TOOLS )->injectOptions( $extender );
                $extender->endTab();

                $extender->startTab('CSS');
                    $this->_getBlock( ffThemeBuilderBlock::CUSTOM_CODES )->injectOptions( $extender );
                $extender->endTab();

            $extender->endTabs();

            $s->endSection();

            $this->_elementOptionsStructure = $s;
        }

        return $this->_elementOptionsStructure;
    }

    /**
     * @param ffOptionsQueryDynamic $query
     */
    public function queryIteratorValidation( $query ) {

        $variation = $query->getVariationType();

        if( $variation == 'html' ) {

            $this->_getBlock(ffThemeBuilderBlock::HTML)->render($query);
            return false;
        }


        return true;
    }


    /**
     * @param $query ffOptionsQueryDynamic
     */
    public function queryIteratorStart( $query ) {
//        var_dump( $query->getOnlyData());


        if( $query->queryExists( 'a-t')) {
            ob_start();
//            var_dump( $query->get('a-t')->getCurrentQueryDataPart());
//            $query->debug_dump();
        }

//        if( $query->queryExists('a-t') ) {
//            var_dump($query->getOnlyDataPart('a-t'));
//            echo 'xxxxxxxxxx';
//            ob_start();
//        }
    }
    /**
     * @param $query ffOptionsQueryDynamic
     */
    public function queryIteratorEnd( $query ) {
        if( $query->queryExists( 'a-t') ) {
            $content = ob_get_contents();
            ob_end_clean();
            $content = $this->_getBlock(ffThemeBuilderBlock::ADVANCED_TOOLS)->setParam('content', $content)->render($query);
            echo $content;
        }
    }

/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/














} 