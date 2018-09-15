<?php

class ffOptionsHolder_CachingFacade extends ffOptionsHolder implements  ffIOptionsHolder {
    const OPTIONS_CACHING_NAMESPACE = 'cached_options';
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
    /**
     * @var ffIOptionsHolder
     */
    private $_optionsHolder = null;

    /**
     * @var ffClassLoader
     */
    private $_classLoader = null;

    /**
     * @var ffFileSystem
     */
    private $_fileSystem  = null;

    /**
     * @var ffDataStorage_Cache;
     */
    private $_cache = null;

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
    private $_themeVersionClean = null;
/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct( ffIOptionsHolder $optionsHolder, $classLoader, $fileSystem, $cache, $themeVersionClean ) {
        $this->_setOptionsHolder( $optionsHolder );
        $this->_setClassLoader( $classLoader );
        $this->_setFileSystem( $fileSystem );
        $this->_setCache( $cache );
        $this->_setThemeVersionClean( $themeVersionClean );
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
    public function getOptions() {
        $optionsFromCache = $this->_getOptionsFromCache();

        if( $optionsFromCache == null ) {

            $opt =  $this->_getOptionsHolder()->getOptions();

            return $opt;
        } else {
            return $optionsFromCache;
        }
    }

    public function getOptionsHolderClassName() {
        return $this->_getOptionsHolder()->getOptionsHolderClassName();
    }

/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/
    private function _getOptionsFromCache() {
        $namespace = ffConstCache::CACHED_OPTIONS_NAMESPACE;
        $fileHash = $this->_getOptionsHolderFileHash();

        $className = get_class($this->_getOptionsHolder());

        // cached options newly in theme directly
        if( $this->_getClassLoader()->isThemeClass( $className ) ) {
            $absolutePath = $this->_getOptionsHolderThemeCachedAbsolutePath();

            if( $this->_getFileSystem()->fileExists( $absolutePath ) ) {
                $optionsSerialized = $this->_getFileSystem()->getContents( $absolutePath ) ;

                $optionsUnserialized = unserialize( $optionsSerialized );

                return $optionsUnserialized;
            }
        }

        // wp-content/themes/frashframework cache
        if( $this->_getCache()->optionExists( $namespace, $fileHash ) ) {
            $optionsSerialized = $this->_getCache()->getOption( $namespace, $fileHash );
            $optionsUnserialized = unserialize( $optionsSerialized );

            return $optionsUnserialized;
        } else {
            $optionsUnserialized = $this->_getOptionsHolder()->getOptions();
            $optionsSerialized = serialize( $optionsUnserialized );

            $this->_getCache()->setOption( $namespace, $fileHash, $optionsSerialized );

            return $optionsUnserialized;
        }
    }

    private function _getOptionsHolderThemeCachedAbsolutePath() {
        $relativeDir = '/framework/components/serialized/';
        $className = get_class( $this->_getOptionsHolder());
        $fileName = $className .'-' . $this->_getThemeVersionClean() . '.ser';

        $relativePath = $relativeDir . $fileName;

        return $this->_getFileSystem()->getAbsolutePathInTemplate( $relativePath );
    }

    private function _getOptionsHolderFileHash() {
        $currentClass = get_class( $this->_getOptionsHolder() );
        $currentClassPath = $this->_getClassLoader()->getClassPath( $currentClass );

        if( $currentClassPath == null ) {
            return null;
        }

        $fileHash = $this->_getFileSystem()->getFileHashBasedOnPathAndTimeChange( $currentClassPath );

        return $fileHash;
    }
/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/

    /**
     * @return ffIOptionsHolder
     */
    private function _getOptionsHolder()
    {
        return $this->_optionsHolder;
    }

    /**
     * @param ffIOptionsHolder $optionsHolder
     */
    private function _setOptionsHolder($optionsHolder)
    {
        $this->_optionsHolder = $optionsHolder;
    }

    /**
     * @return ffDataStorage_Cache
     */
    private function _getCache()
    {
        return $this->_cache;
    }

    /**
     * @param ffDataStorage_Cache $cache
     */
    private function _setCache($cache)
    {
        $this->_cache = $cache;
    }

    /**
     * @return ffFileSystem
     */
    private function _getFileSystem()
    {
        return $this->_fileSystem;
    }

    /**
     * @param ffFileSystem $fileSystem
     */
    private function _setFileSystem($fileSystem)
    {
        $this->_fileSystem = $fileSystem;
    }

    /**
     * @return ffClassLoader
     */
    private function _getClassLoader()
    {
        return $this->_classLoader;
    }

    /**
     * @param ffClassLoader $classLoader
     */
    private function _setClassLoader($classLoader)
    {
        $this->_classLoader = $classLoader;
    }

    /**
     * @return null
     */
    private function _getThemeVersionClean()
    {
        return $this->_themeVersionClean;
    }

    /**
     * @param null $themeVersionClean
     */
    private function _setThemeVersionClean($themeVersionClean)
    {
        $this->_themeVersionClean = $themeVersionClean;
    }


}