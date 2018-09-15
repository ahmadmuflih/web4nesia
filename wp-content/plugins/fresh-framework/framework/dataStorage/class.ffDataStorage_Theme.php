<?php

/**
 * Class ffDataStorage_Theme
 *
 * Allows you to access the current theme files
 *
 * @since 1.9.8
 */
class ffDataStorage_Theme extends ffDataStorage {
    const DIR_NAME = 'data_storage';
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

    /**
     * @var ffFileSystem
     */
    private $_fileSystem = null;

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
    public function __construct() {
        $fwc = ffContainer();
        parent::__construct( $fwc->getWPLayer() );
        $this->_setFileSystem( $fwc->getFileSystem() );


//        $fwc->getThemeLoader()
    }
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/


/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/

    protected function _maxOptionNameLength() {
        return 255;
    }

    protected function _setOption( $namespace, $name, $value ) {
        if( !$this->_getWPLayer()->getIsOurTheme() ) {
            return false;
        }
        $namespaceDir = $this->_getAbsPath( $namespace );
        $this->_initializeNamespaceDir( $namespaceDir );

        $absPath = $this->_getAbsPath( $namespace, $name );

        $this->_getFileSystem()->putContents( $absPath, $value );
    }

    protected function _getOption( $namespace, $name ) {

    }

    public function getOption( $namespace, $name, $default = null ) {
        $absPath = $this->_getAbsPath( $namespace, $name );

        $fileSystem = $this->_getFileSystem();

        if( $fileSystem->fileExists( $absPath ) ) {
            return $fileSystem->getContents( $absPath );
        } else {
            return $default;
        }
    }

	protected function _deleteOption( $namespace, $name ) {

    }


    private function _initializeNamespaceDir( $namespaceDirPath ) {
		if( !$this->_getFileSystem()->fileExists( $namespaceDirPath ) ) {
			$this->_getFileSystem()->makeDir( $namespaceDirPath );
		}
	}

    private function _getAbsPath( $namespace, $name = null, $ext = 'opt' ) {
        $directoryAbsPath = $this->_getDirectoryAbsPath();

        $path = $directoryAbsPath.'/' . $namespace;

        if( $name != null ) {
            $path .= '/' . $name .'.'.$ext;
        }

        return $path;
    }

    private function _getDirectoryAbsPath() {
        $themeDirectory = $this->_getFileSystem()->getAbsolutePathInTemplate( '/' . ffDataStorage_Theme::DIR_NAME );

        return $themeDirectory;
    }

/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
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

}