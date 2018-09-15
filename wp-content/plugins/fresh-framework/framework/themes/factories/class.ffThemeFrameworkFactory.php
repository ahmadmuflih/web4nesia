<?php

class ffThemeFrameworkFactory extends ffFactoryAbstract {

    private $_themeAssetsIncluder = null;


    public function getThemeAssetsIncluder() {
        if( $this->_getClassloader()->classRegistered('ffThemeAssetsIncluder') ) {
            if( $this->_themeAssetsIncluder == null ) {

                $this->_getClassloader()->loadClass('ffThemeAssetsIncluderAbstract');
                $this->_getClassloader()->loadClass('ffThemeAssetsIncluder');

                $this->_getClassloader()->loadClass('ffThemeAssetsManager');

                $container = ffContainer::getInstance();

                 $this->_themeAssetsIncluder = new ffThemeAssetsIncluder( $container->getStyleEnqueuer(), $container->getScriptEnqueuer(), $container->getLessWPOptionsManager() );
            }
            return $this->_themeAssetsIncluder;
        }
        return null;
    }

    public function getExternalPluginsTweaker() {
        $this->_getClassloader()->loadClass('ffExternalPluginsTweaker');
        $container = ffContainer();
        $pluginsTweaker = new ffExternalPluginsTweakers( $container->getWPLayer() );

        return $pluginsTweaker;
    }

	public function getThemeAssetsManager() {

		if( $this->_getClassloader()->classRegistered('ffThemeAssetsIncluder') ) {

			$this->_getClassloader()->loadClass('ffThemeAssetsIncluderAbstract');
			$this->_getClassloader()->loadClass('ffThemeAssetsIncluder');

			$this->_getClassloader()->loadClass('ffThemeAssetsManager');

			$container = ffContainer::getInstance();

            $themeAssetsIncluder = $this->getThemeAssetsIncluder();
			$themeAssetsManager = new ffThemeAssetsManager( $themeAssetsIncluder, $container->getWPLayer() );

			return $themeAssetsManager;
		}

		return null;
	}


    private $_themeOnePageManager = null;

    public function getThemeOnePageManager() {
        if( $this->_themeOnePageManager == null ) {
            $this->_getClassloader()->loadClass('ffThemeOnePageManager');

            $this->_themeOnePageManager = new ffThemeOnePageManager();
        }

        return $this->_themeOnePageManager;
    }


    private $_layoutsNamespaceFactory = null;

    public function getLayoutsNamespaceFactory() {
        if( $this->_layoutsNamespaceFactory == null ) {
            $this->_getClassloader()->loadClass('ffLayoutsNamespaceFactory');

            $this->_layoutsNamespaceFactory = new ffLayoutsNamespaceFactory( $this->_getClassloader() );
        }

        return $this->_layoutsNamespaceFactory;
    }



	public function getVideoIncluder() {
		$this->_getClassloader()->loadClass('ffVideoIncluder');

		$videoIncluder = new ffVideoIncluder();

		return $videoIncluder;
	}

	public function getPaginationComputer() {
		$this->_getClassloader()->loadClass('ffPaginationComputer');

		$pagnationComputer = new ffPaginationComputer();

		return $pagnationComputer;
	}

	public function getPaginationWPLoop() {
		$this->_getClassloader()->loadClass('ffPaginationWPLoop');

		$container = ffContainer::getInstance();

		$paginationWPLoop = new ffPaginationWPLoop( $container->getWPLayer(), $this->getPaginationComputer() );

		return $paginationWPLoop;
	}

	public function getThemeViewIdentificator() {
		$this->_getClassloader()->loadClass('ffThemeViewIdentificator');

		$container = ffContainer::getInstance();

		$themeViewIdentificator = new ffThemeViewIdentificator( $container->getWPLayer() );

		return $themeViewIdentificator;
	}

	public function getSocialFeedCreator( $links = null ) {
		$this->_getClassloader()->loadClass('ffSocialFeedCreator');

		$socialFeedCreator = new ffSocialFeedCreator( $links );

		return $socialFeedCreator;
	}

	public function getSocialSharerFeedCreator( $links = null ) {
		$this->_getClassloader()->loadClass('ffSocialSharerFeedCreator');

		$container = ffContainer::getInstance();

		$socialSharerFeedCreator = new ffSocialSharerFeedCreator( $container->getWPLayer() );

		return $socialSharerFeedCreator;
	}

	private $_postMetaGetter = null;

	public function getPostMetaGetter() {
		if( $this->_postMetaGetter == null ) {
			$this->_getClassloader()->loadClass('ffPostMetaGetter');
			$container = ffContainer::getInstance();
			$this->_postMetaGetter = new ffPostMetaGetter( $container->getWPLayer() );
		}
		return $this->_postMetaGetter;

	}

    public function getCommentsFormPrinter() {
        $this->_getClassloader()->loadClass('ffCommentsFormPrinter');

        $commentsFormPrinter = new ffCommentsFormPrinter( ffContainer()->getWPLayer() );

        return $commentsFormPrinter;
    }

    private $_menuOptionsManager = null;

    public function getMenuOptionsManager() {
        if( $this->_menuOptionsManager == null ) {
            $this->_getClassloader()->loadClass('ffMenuOptionsManager');

            $fwc = ffContainer();

            $this->_menuOptionsManager = new ffMenuOptionsManager(
                $fwc->getWPLayer(),
                $fwc->getOptionsFactory(),
                $fwc->getScriptEnqueuer(),
                $fwc->getDataStorageFactory()->createDataStorageOptionsPostType_NamespaceFacade(),
                $fwc->getRequest(),
                $fwc->getModalWindowFactory()
            );
        }

        return $this->_menuOptionsManager;
    }

    private $_menuJavascriptSaver = null;

    public function getMenuJavascriptSaver() {
        if( $this->_menuJavascriptSaver == null ) {
            $fwc = ffContainer();

            $fwc->getClassLoader()->loadClass('ffMenuJavascriptSaver');
            $this->_menuJavascriptSaver = new ffMenuJavascriptSaver($fwc->getScriptEnqueuer(), $fwc->getRequest());
        }

        return $this->_menuJavascriptSaver;

    }


    private $_themeBuilderManager = null;

    public function getThemeBuilderManager() {
        if( $this->_themeBuilderManager == null ) {
            $this->_getClassloader()->loadClass('ffThemeBuilderManager');

            $this->_themeBuilderManager = new ffThemeBuilderManager();
        }

        return $this->_themeBuilderManager;
    }


    private $_themeBuilderElementManager = null;

    public function getThemeBuilderElementManager() {
        if( $this->_themeBuilderElementManager == null ) {
            $this->_getClassloader()->loadClass('ffThemeBuilderElementManager');
            $this->_getClassloader()->loadClass('ffThemeBuilderElementFactory');

            $this->_themeBuilderElementManager = new ffThemeBuilderElementManager(
                ffContainer()->createNewCollection(),
                ffContainer()->createNewCollection(),
                new ffThemeBuilderElementFactory( $this->_getClassloader() )
            );
        }

        return $this->_themeBuilderElementManager;
    }


    private $_themeBuilderShortcodesWalker = null;

    public function getThemeBuilderShortcodesWalker() {
        if( $this->_themeBuilderShortcodesWalker == null ) {
            $this->_getClassloader()->loadClass('ffThemeBuilderShortcodesWalker');
            $this->_themeBuilderShortcodesWalker = new ffThemeBuilderShortcodesWalker(
                $this->getThemeBuilderElementManager()
            );
        }

        return $this->_themeBuilderShortcodesWalker;
    }

    public function getThemeBuilderBlockFactory() {
        $this->_getClassloader()->loadClass('ffThemeBuilderBlock');
        $this->_getClassloader()->loadClass('ffThemeBuilderBlockFactory');

        return new ffThemeBuilderBlockFactory( $this->_getClassloader() );
    }
}