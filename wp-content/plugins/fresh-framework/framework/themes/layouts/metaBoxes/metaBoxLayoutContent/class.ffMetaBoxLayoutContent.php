<?php

class ffMetaBoxLayoutContent extends ffMetaBox {
	protected function _initMetaBox() {
        $themeLayoutManager = ffContainer()->getThemeFrameworkFactory()->getLayoutsNamespaceFactory()->getThemeLayoutManager();

		$this->_addPostType( $themeLayoutManager->getLayoutPostTypeName() );
		$this->_setTitle('Layout');
		$this->_setContext( ffMetaBox::CONTEXT_NORMAL);
		
		$this->_setParam( ffMetaBox::PARAM_NORMALIZE_OPTIONS, true);
        $this->_setParam( ffMetaBox::PARAM_NORMALIZE_OPTIONS_TO_ONE_INPUT, true );
		//$this->_addVisibility( ffMetaBox::VISIBILITY_PAGE_TEMPLATE, 'page-onepage.php');
	}
}