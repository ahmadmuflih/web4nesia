<?php

class ffThemeBuilderBlock_Link extends ffThemeBuilderBlock {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
	protected function _init() {
		$this->_setInfo( ffThemeBuilderBlock::INFO_ID, 'hyperlink');
		$this->_setInfo( ffThemeBuilderBlock::INFO_WRAPPING_ID, 'link');
		$this->_setInfo( ffThemeBuilderBlock::INFO_WRAP_AUTOMATICALLY, true);
		$this->_setInfo( ffThemeBuilderBlock::INFO_IS_REFERENCE_SECTION, false);
		$this->_setInfo( ffThemeBuilderBlock::INFO_SAVE_ONLY_DIFFERENCE, true);
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


	protected function _render( $query ) {
		if( $this->_queryIsEmpty() ) {
			return false;
		}


		echo 'href="'. esc_url($query->get('type')). $query->get('url') . '" ';
		echo ( $query->get('target')=='_blank' ? 'target="_blank " ' : ' ' );
		echo ( $query->get('title')!='' ? 'title="'. esc_attr($query->get('title')) .'" ' : ' ' );

	}

	protected function _injectOptions( ffThemeBuilderOptionsExtender $s ) {

		$s->addElement( ffOneElement::TYPE_TABLE_START );

		$s->addOptionNL(ffOneOption::TYPE_TEXT, 'url', '', '//themeforest.net/user/freshface/portfolio')
			->addParam( ffOneOption::PARAM_TITLE_AFTER, 'URL' );

		$s->addElement(ffOneElement::TYPE_DESCRIPTION, '', 'Do not use prefix [http(s):]! Use prefix [//] only.');
		$s->addElement(ffOneElement::TYPE_NEW_LINE);

		$s->addOptionNL(ffOneOption::TYPE_SELECT, 'target','', '_blank')
			->addSelectValue('Same Window', '')
			->addSelectValue('New Window', '_blank')
			->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Open in' );
		;

		$s->addOptionNL(ffOneOption::TYPE_SELECT, 'type', '', '')
			->addSelectValue('Email', 'mailto:')
			->addSelectValue('Ordinary Link', '')
			->addSelectValue('Skype', 'callto:')
			->addSelectValue('Telephone', 'tel:')
			->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Use Link as Type' );
		;

		$s->addOptionNL(ffOneOption::TYPE_TEXT, 'title', '',  '')
			->addParam('placeholder', ' - no title - ')
			->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Title' );
		;


		$s->addElement( ffOneElement::TYPE_TABLE_END );
	}
/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
}