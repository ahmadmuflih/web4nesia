<?php

/**
 * Class ffMetaBoxOnePageFrameworkView
 *
 * Here happens all the one page magic
 *
 * @since 1.9.1
 */
class ffMetaBoxOnePageFrameworkView extends ffMetaBoxView {

    const JSON_FLAG = 'ff_json';

    /**
     * @var ffThemeOnePageManager
     */
    private $_themeOnePageManager = null;

    protected function _beforeRendering() {

    }

    /**
     * @return ffIOptionsHolder
     */
    private function _getOptionsHolder() {
        $optionsHolderClassName = $this->_getThemeOnePageManager()->getOnePageOptionsHolderClassName();
        return ffContainer()->getOptionsFactory()->createOptionsHolder($optionsHolderClassName);
    }

    /**
     * Get the data from standardised source, or from theme callback function
     *
     * @return array
     */
    private function _getOnePageData( $postId ) {
        $loader = $this->_getThemeOnePageManager()->getLoaderCallback();

        $onePageData = null;

        if( is_callable( $loader ) ) {
            $onePageData = $loader( $postId );
        } else {
            $fwc = ffContainer();
            $onePageData = $fwc->getDataStorageFactory()->createDataStorageWPPostMetas_NamespaceFacade( $postId )->getOptionCoded('onepage');

        }

        return $onePageData;
    }

    /**
     * Set the one page data
     *
     * @param $postId
     * @param $data
     */
    private function _setOnePageData( $postId, $data ) {
        $saver = $this->_getThemeOnePageManager()->getSaverCallback();

        if( is_callable( $saver ) ) {
            $result = $saver( $postId, $data );
        } else {

            $fwc = ffContainer();

            $optionsStructure = $this->_getOptionsHolder()->getOptions();
            $postReader = $fwc->getOptionsFactory()->createOptionsPostReader();
            $postReader->setOptionsStructure( $optionsStructure );


            $value = $postReader->getDataFromArray( $data );

            $value = $value['onepage'];

            $saver = $fwc->getDataStorageFactory()->createDataStorageWPPostMetas_NamespaceFacade( $postId );
            $saver->setOptionCodedJSON('onepage', $value );


            $revisionManager = ffContainer()->getThemeFrameworkFactory()->getLayoutsNamespaceFactory()->getOnePageRevisionManager();
            $revisionManager->setPostId( $postId );
            $revisionManager->addNewRevision( $value );
        }
    }

    /**
     * Handle ajax request - junction
     * @param ffAjaxRequest $ajaxRequest
     */
    public function ajaxRequest( ffAjaxRequest $ajaxRequest ) {
        $action = $ajaxRequest->getData('action', 'getOptions');
        $postId = $ajaxRequest->getData('postId');

        switch( $action ) {
            case 'getOptions' :
                    $this->_renderOptionsAjax( $postId );
                break;

            case 'saveOptions':
                    $this->_saveOptionsAjax( $postId, $ajaxRequest );
                break;

            case 'setRevision':
                    $this->_setRevisionAjax( $postId, $ajaxRequest );
                break;
        }

    }

    /**
     * @param $postId
     * @param ffAjaxRequest $ajaxRequest
     */
    private function _setRevisionAjax( $postId, ffAjaxRequest $ajaxRequest ) {
        $revisionNumber = $ajaxRequest->getData('revisionNumber');

        $revisionManager = ffContainer()->getThemeFrameworkFactory()->getLayoutsNamespaceFactory()->getOnePageRevisionManager();
        $revisionManager->setPostId( $postId );
        $revisionManager->setRevisionAsContent( $revisionNumber);

        $this->_renderOptionsAjax( $postId );
    }


    /**
     * @param $postId
     * @param ffAjaxRequest $ajaxRequest
     * @return bool
     */
    private function _saveOptionsAjax( $postId, ffAjaxRequest $ajaxRequest ) {

        $dataJSON = $ajaxRequest->getData('normalizedFormJSON');
        $data = json_decode( $dataJSON, true );

        if( !isset( $data['has-been-normalized'] ) ) {
            return false;
        } else {
            unset( $data['has-been-normalized']);
        }

        $this->_setOnePageData( $postId, $data );

        $this->_printRevisionList( $postId );

    }

    /**
     * Render javascript options and revision system
     * @param $postId
     */
    private function _renderOptionsAjax( $postId ) {
        $optionsHolder = $this->_getOptionsHolder();
        $onePageData = $this->_getOnePageData( $postId );

        $fwc = ffContainer::getInstance();

        $printer = $fwc->getOptionsFactory()->createOptionsPrinterJavascriptConvertor( $onePageData );
        $printer->setOptionsHolder( $optionsHolder ) ;
		$printer->setNameprefix('onepage');

        echo '<br>';
//        ffStopWatch::memoryStart();
		echo  $printer->walk();
//        ffStopWatch::memoryEndDump();
        echo '<br>';
		echo '<input type="submit" style="display:none;" class="ff-onepage-save-ajax button-primary" value="Save All Sections">';
		echo '<div class="ff-post-id" style="display:none;">'.  $postId .'</div>';
		echo "\n\n".'<script>jQuery(window).load(function(){ jQuery(".ff-default-wp-color-picker").wpColorPicker();});</script>';
        echo '<div class="ff-post-id-holder" style="display:none;">'.  $postId.'</div>';
        echo '<div class="ff-revision-list-content">';
            $this->_printRevisionList( $postId );
        echo '</div>';
    }

    /**
     * Render just the post id, the rest of the options will be loaded trough ajax call
     * @param $post
     */
    protected function _render( $post ) {

        $fwc = ffContainer();
        $fwc->getOptionsFactory()->createOptionsPrinterDataboxGenerator()->printAll();
        echo '<div class="ff-post-id-holder" style="display:none;">'.  $post->ID.'</div>';
        echo '<div class="ff-repeatable-spinner"></div>';
        $fwc->getWPLayer()->add_action('admin_footer', array($this,'requireModalWindows'), 1);
    }


    protected function _printRevisionList( $postId ) {

        $revisionManager = ffContainer()->getThemeFrameworkFactory()->getLayoutsNamespaceFactory()->getOnePageRevisionManager();
        $revisionManager->setPostId( $postId );

        $currentRevisionNumber = $revisionManager->getCurrentRevisionNumber();
        echo '<div class="ff-revision-list">';
            echo '<h4 style="margin-bottom:0;">Revisions</h4>';
            echo '<ul style="margin-top:4px">';

                foreach( $revisionManager->getListOfRevisionsForCurrentPost() as $revisionNumber => $oneRevision ) {
                    $revisionNumberText = $revisionNumber;
                    if( $revisionNumber == $currentRevisionNumber ) {
                        $revisionNumberText = 'current';
                    }

                    echo '<li>';
                        echo 'Revision ' . $oneRevision['number'] . ' (' . $oneRevision['human_time'].') ';

                        if( $revisionNumber == $currentRevisionNumber ) {
                            echo 'Current';
                        } else {
                            echo '<a href="" class="ff-revision-switch" data-revision-number="'.$revisionNumberText.'">';
                                echo 'Rollback';
                            echo '</a>';
                        }
                    echo '</li>';
                }

            echo '</ul>';

        echo '</div>';
    }

    /**
     * @return ffThemeOnePageManager
     */
    private function _getThemeOnePageManager()
    {
        if( $this->_themeOnePageManager == null ) {
            $this->_setThemeOnePageManager( ffContainer()->getThemeFrameworkFactory()->getThemeOnePageManager() );
        }

        return $this->_themeOnePageManager;
    }

    /**
     * @param ffThemeOnePageManager $themeOnePageManager
     */
    private function _setThemeOnePageManager($themeOnePageManager)
    {
        $this->_themeOnePageManager = $themeOnePageManager;
    }



	protected function _requireAssets() {
        $scriptEnqueuer = ffContainer()->getScriptEnqueuer();

        $scriptEnqueuer->getFrameworkScriptLoader()->requireFfAdmin()->requireFrsLibOptions();
        $scriptEnqueuer->addScriptFramework('ff-onePage', '/framework/themes/onePage/metaBoxOnePageFramework/onePage.js');

	}

	public function requireModalWindows() {
        $fwc = ffContainer();

        $fwc->getModalWindowFactory()->printModalWindowManagerLibraryColor();
		$fwc->getModalWindowFactory()->printModalWindowManagerLibraryIcon();
	}



	protected function _save( $postId ) {


        $fwc = ffContainer();
        $options = $this->_getOptionsHolder()->getOptions();

        $postReader = $fwc->getOptionsFactory()->createOptionsPostReader();
		$postReader->setOptionsStructure($options);



		$value = $fwc->getOptionsFactory()->createOptionsPostReader()->getData( 'onepage' );


        $newValue['onepage'] = $value;
//        $value['onepage'] = $value;
        $this->_setOnePageData($postId, $newValue );
	}
}