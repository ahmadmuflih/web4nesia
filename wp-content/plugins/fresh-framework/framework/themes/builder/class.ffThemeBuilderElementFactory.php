<?php
/**
 * Class ffThemeBuilderElementFactory
 */
class ffThemeBuilderElementFactory extends ffFactoryAbstract {


//    private $_blocksAssetsManager = null;
//
//    private $_optionBlocksManager = null;
//
//    public function setBlocksAssetsManage( $bsm ) {
//        $this->_blocksAssetsManager = $bsm;
//    }
//
//    public function setOptionBlocksManager( $obm ) {
//        $this->_optionBlocksManager = $obm;
//    }

//    public function createBlock( $blockClassName ) {

//        $this->_getClassloader()->loadClass( $blockClassName );
//        return new $blockClassName(
//            ffContainer()->getOptionsFactory(),
//            ffContainer()->getMultiAttrHelper(),
//            ffThemeContainer::getInstance()->getTemplatingEngine(),
//            ffContainer()->getFileSystem(),
//            $this->_blocksAssetsManager,
//            $this->_optionBlocksManager,
//            true
//        );
//    }

    public function loadElement( $elementClassName ){
        $this->_getClassloader()->loadClass('ffThemeBuilderElement');
        $this->_getClassloader()->loadClass('ffThemeBuilderElementBasic');
        $this->_getClassloader()->loadClass('ffThemeBuilderOptionsExtender');
        $this->_getClassloader()->loadClass( $elementClassName );
    }

    /**
     * @param $elementClassName
     * @return ffThemeBuilderElement
     * @throws Exception
     */

    public function createElement( $elementClassName ) {
        $this->loadElement( $elementClassName );

        $optionsExtender = new ffThemeBuilderOptionsExtender();

        return new $elementClassName( $optionsExtender );
    }

}