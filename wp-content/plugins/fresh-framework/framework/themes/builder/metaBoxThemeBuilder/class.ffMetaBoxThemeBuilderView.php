<?php

class ffMetaBoxThemeBuilderView extends ffMetaBoxView {

//    const JSON_FLAG = 'ff_json';

//    /**
//     * @var ffThemeOnePageManager
//     */
//    private $_themeOnePageManager = null;
//
//    protected function _beforeRendering() {
//
//    }

    /**
     * @return ffIOptionsHolder
     */
//    private function _getOptionsHolder() {
//        $optionsHolderClassName = $this->_getThemeOnePageManager()->getOnePageOptionsHolderClassName();
//        return ffContainer()->getOptionsFactory()->createOptionsHolder($optionsHolderClassName);
//    }

    /**
     * Get the data from standardised source, or from theme callback function
     *
     * @return array
     */
    private function _getOnePageData( $postId ) {
//        $loader = $this->_getThemeOnePageManager()->getLoaderCallback();
//
//        $onePageData = null;
//
//        if( is_callable( $loader ) ) {
//            $onePageData = $loader( $postId );
//        } else {
//            $fwc = ffContainer();
//            $onePageData = $fwc->getDataStorageFactory()->createDataStorageWPPostMetas_NamespaceFacade( $postId )->getOptionCoded('onepage');
//
//        }
//
//        return $onePageData;
    }

    /**
     * Set the one page data
     *
     * @param $postId
     * @param $data
     */
    private function _setOnePageData( $postId, $data ) {
//        $saver = $this->_getThemeOnePageManager()->getSaverCallback();
//
//        if( is_callable( $saver ) ) {
//            $result = $saver( $postId, $data );
//        } else {
//
//            $fwc = ffContainer();
//
//            $optionsStructure = $this->_getOptionsHolder()->getOptions();
//            $postReader = $fwc->getOptionsFactory()->createOptionsPostReader();
//            $postReader->setOptionsStructure( $optionsStructure );
//
//
//            $value = $postReader->getDataFromArray( $data );
//
//            $value = $value['onepage'];
//
//            $saver = $fwc->getDataStorageFactory()->createDataStorageWPPostMetas_NamespaceFacade( $postId );
//            $saver->setOptionCodedJSON('onepage', $value );
//
//
//            $revisionManager = ffContainer()->getThemeFrameworkFactory()->getLayoutsNamespaceFactory()->getOnePageRevisionManager();
//            $revisionManager->setPostId( $postId );
//            $revisionManager->addNewRevision( $value );
//        }
    }

    /**
     * Handle ajax request - junction
     * @param ffAjaxRequest $ajaxRequest
     */
    public function ajaxRequest( ffAjaxRequest $ajaxRequest ) {

        switch( $ajaxRequest->getData('action') ) {
            case 'getElementsData':
                    $this->_ajaxGetElementsData( $ajaxRequest );
                break;
        }

    }

    /**
     * Generate JSON with datas about ALL our elements, important for builder
     * @param ffAjaxRequest $ajaxRequest
     */
    private function _ajaxGetElementsData( ffAjaxRequest $ajaxRequest ) {

//        $builderManager = ffContainer()->getThemeFrameworkFactory()->getThemeBuilderManager();
//        $builderManager->enableBuilderSupport();
//        $builderManager->setIsEditMode( true);
////        $builderManager->addElement('ffElSection');
//        $builderManager->addElement('ffElRow');
//        $builderManager->addElement('ffElColumn');
//
//        $builderManager->addElement('ffElServices1');

        $elementManager = ffContainer()->getThemeFrameworkFactory()->getThemeBuilderElementManager();
        $elementManager->addMenuItem('All', 'all');

        $data = ffContainer()->getThemeFrameworkFactory()->getThemeBuilderElementManager()->getElementsData();
        echo json_encode( $data );

    }

    function tom_sc( $atts, $content ) {
//            vaR_dump($atts, $content);

        $data = $atts['data'];

        $dataDecoded = htmlspecialchars_decode( $data );

        $dataJson = json_decode( $dataDecoded );

        var_dump(json_last_error_msg());

        var_dump( $dataDecoded, $dataJson );

    }


    /**
     * Render just the post id, the rest of the options will be loaded trough ajax call
     * @param $post
     */
    protected function _render( $post ) {


//        add_shortcode('sc_test', array( $this, 'tom_sc'));
//
//
//        $data = '{"o":{"text":"start \" jednoduche \' slozene \" hovno \" ][","sub-headings":{"0-|-one-heading":{"one-heading":{"text":"hmm vyser si oko kurvo"}}}}}';
//
//        $data = htmlspecialchars( $data );
//
//        $data = str_replace('[', '$hranatal;', $data );
//        $data = str_replace(']', '$hranatar;', $data );
//
//        var_Dump( $data );
//
//        $sc = '[sc_test data="'.$data.'"]';
//        do_shortcode( $sc );


//        return;



//        return;
        echo '<div class="ff-temporary-options-holder"></div>';
//        var_dump( $post );

        $builderManager = ffContainer()->getThemeFrameworkFactory()->getThemeBuilderManager();
//        $builderManager->enableBuilderSupport();
        $builderManager->setIsEditMode( true);
//        $builderManager->addElement('ffElSection');
//        $builderManager->addElement('ffElRow');
//        $builderManager->addElement('ffElColumn');
//
//        $builderManager->addElement('ffElServices1');



//        $data = ffContainer()->getThemeFrameworkFactory()->getThemeBuilderElementManager()->getElementsData();

//        echo json_encode( $data );

        $content = $post->post_content;

        echo '<div class="ffb-canvas">';
        $builderManager->render( $content );
        echo '<div class="ffb-canvas__bottom-button dashicons dashicons-plus action-add-section"></div></div>';

        ffContainer()->getWPLayer()->add_action('admin_footer-post.php', array($this,'addFreshBuilderModal'), 1);
		ffContainer()->getWPLayer()->add_action('admin_footer', array($this,'requireModalWindows'), 1);



    }

	protected function _requireAssets() {
        $scriptEnqueuer = ffContainer()->getScriptEnqueuer();
        $styleEnqueuer = ffContainer()->getStyleEnqueuer();

//
        $scriptEnqueuer->getFrameworkScriptLoader()->requireFfAdmin()->requireFrsLibOptions()->requireBackboneDeepModel();
        $scriptEnqueuer->addScript('backbone');
        $scriptEnqueuer->addScript('underscore');
        $styleEnqueuer->addStyleFramework('ffb-builder-style', '/framework/themes/builder/metaBoxThemeBuilder/assets/style.css');
        
        $scriptEnqueuer->addScriptFramework('ffb-builder-scroll-lock', '/framework/themes/builder/metaBoxThemeBuilder/assets/extern/jquery.scrollLock.min.js');
        $scriptEnqueuer->addScriptFramework('ffb-builder-frslib-options-addons', '/framework/themes/builder/metaBoxThemeBuilder/assets/frslib-options-addons.js');
        $scriptEnqueuer->addScriptFramework('ffb-builder-toScContentConvertor', '/framework/themes/builder/metaBoxThemeBuilder/assets/frslib-options-walkers-toScContentConvertor.js');

        $scriptEnqueuer->addScriptFramework('ffb-builder-element-view-and-model', '/framework/themes/builder/metaBoxThemeBuilder/assets/elementViewAndModel.js');
        $scriptEnqueuer->addScriptFramework('ffb-builder-element-picker', '/framework/themes/builder/metaBoxThemeBuilder/assets/elementPicker.js');
        $scriptEnqueuer->addScriptFramework('ffb-builder-script', '/framework/themes/builder/metaBoxThemeBuilder/assets/main.js');
//        $scriptEnqueuer->addScriptFramework('ffb-builder-script', '/framework/themes/builder/metaBoxThemeBuilder/assets/app.js');
//
	}



    function addFreshBuilderModal() {
        ?>

        <div class="ffb-modal ffb-modal-origin">
            <div class="ffb-modal__vcenter-wrapper">
                <div class="ffb-modal__vcenter ffb-modal__action-cancel">
                    <div class="ffb-modal__box">
                        <div class="ffb-modal__header">
                            <div class="ffb-modal__name">
                                Slider Navigation
                            </div>
                        </div>
                        <div class="ffb-modal__body">
                            <div class="ffb-modal__tabs">
                                <div class="ffb-modal__tab-headers clearfix"></div>
                                <div class="ffb-modal__tab-contents clearfix">
                                    <div class="ffb-modal__tab-header" data-tab-header-name="General">General</div>
                                    <div class="ffb-modal__tab-content" data-tab-content-name="General">
                                        <div class="ffb-modal__content--options ffb-options">GGGGGGGGGeneral</div>
                                    </div>
                                    <div class="ffb-modal__tab-header" data-tab-header-name="Advanced">Advanced</div>
                                    <div class="ffb-modal__tab-content" data-tab-content-name="Advanced">
                                        <div class="ffb-modal__content--options ffb-options">AAAAAAAAAdvanced</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ffb-modal__footer">
                            <a href="#" class="ffb-modal__button-cancel ffb-modal__action-cancel">Cancel</a>
                            <a href="#" class="ffb-modal__button-save ffb-modal__action-save">Save Changes</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php

    }


	public function requireModalWindows() {
        $fwc = ffContainer();
//

//        echo 'aaaaaa';
//        echo 'bbbbbb';
        $fwc->getModalWindowFactory()->printModalWindowManagerLibraryColor();
		$fwc->getModalWindowFactory()->printModalWindowManagerLibraryIcon();
	}



	protected function _save( $postId ) {

	}
}