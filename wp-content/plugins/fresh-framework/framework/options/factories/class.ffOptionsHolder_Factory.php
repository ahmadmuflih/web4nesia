<?php

class ffOptionsHolder_Factory extends ffFactoryAbstract {
	
	/**
	 * 
	 * @var ffOneStructure_Factory
	 */
	private $_oneStructureFactory = null;
	
	public function __construct( ffClassLoader $classLoader, ffOneStructure_Factory $oneStructureFactory ) {
		$this->_setOnestructurefactory($oneStructureFactory);
		parent::__construct( $classLoader );
	}

    /**
     * @param $className
     * @return ffOptionsHolder
     * @throws Exception
     */
	public function createOptionsHolder( $className ) {

        $this->_getClassloader()->loadClass('ffIOptionsHolder');
		$this->_getClassloader()->loadClass('ffOptionsHolder');
        $this->_getClassloader()->loadClass('ffOptionsHolder_CachingFacade');
		$this->_getClassloader()->loadClass( $className );
        $this->_getClassloader()->loadClass('ffIOneDataNode');
        $this->_getClassloader()->loadClass('ffOneSection');
        $this->_getClassloader()->loadClass('ffOneOption');
        $this->_getClassloader()->loadClass('ffOneStructure');
        $this->_getClassloader()->loadClass('ffOneElement');


        $container = ffContainer();
        $classLoader = $container->getClassLoader();
        $fileSystem = $container->getFileSystem();

		$optionsHolder = new $className( $this->_getOnestructurefactory(), $this );

        if( $container->getWPLayer()->get_ff_debug() || $container->getWPLayer()->is_ff_server_admin() ) {

//            if( $classLoader->isThemeClass( $className ) ) {
//
//                $themeVersion = $container->getWPLayer()->getThemeVersion();
//                $themeVersionClean = str_replace('.', '', $themeVersion );
//
//
//                $fileName = $className . '-' . $themeVersionClean .'.ser';
//                $relativePath = '/framework/components/serialized/' . $fileName;
//                $absolutePath = $fileSystem->getAbsolutePathInTemplate( $relativePath );
//
//                if( !$fileSystem->fileExists( $absolutePath ) ) {
//                    $options = $optionsHolder->getOptions();
//
//                    $optionsSerialized = serialize($options);
//
//                    $fileSystem->putContents( $absolutePath, $optionsSerialized, 'rwx');
//                    $fileSystem->chmod( $absolutePath, 'rwx');
//                }
//
//            }

            return $optionsHolder;
        }

        $themeVersion = $container->getWPLayer()->getThemeVersion();
        $themeVersionClean = str_replace('.', '', $themeVersion );

        $cache = $container->getDataStorageCache();
        $optionsHolderCachingFacade = new ffOptionsHolder_CachingFacade( $optionsHolder, $classLoader, $fileSystem, $cache, $themeVersionClean);

        return $optionsHolderCachingFacade;

	}

	/**
	 * @return ffOneStructure_Factory
	 */
	protected function _getOnestructurefactory() {
		return $this->_oneStructureFactory;
	}
	
	/**
	 * @param ffOneStructure_Factory $oneStructureFactory
	 */
	protected function _setOnestructurefactory(ffOneStructure_Factory $oneStructureFactory) {
		$this->_oneStructureFactory = $oneStructureFactory;
		return $this;
	}
	
}