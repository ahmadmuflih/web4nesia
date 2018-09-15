<?php

class ffThemeBuilderOptionsExtender extends ffBasicObject {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/
	/**
	 * @var ffThemeBuilderBlockFactory
	 */
	private $_themeBuilderBlockFactory = null;

	/**
	 * @var ffOneStructure
	 */
	private $_s = null;
/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/


	public function setStructure( ffOneStructure $s ) {
		$this->_s = $s;
	}

    public function startRepVariableSection( $id  ) {
		$this->_s->startSection($id,ffOneSection::TYPE_REPEATABLE_VARIABLE)
			->addParam('work-as-accordion', true)
			->addParam('all-items-opened', true)
		;
	}
	

	public function endRepVariableSection() {
		$s = $this->_s;

		$this->startRepVariationSection('html', 'Html')->addParam('hide-default', true);
			$this->_getBlock(ffThemeBuilderBlock::HTML)->injectOptions( $this );
		$this->endRepVariationSection();
//
		$s->endSection();
	}

	public function startRepVariationSection( $id, $name ) {
		$s = $this->_s;
		$section = $s->startSection($id, ffOneSection::TYPE_REPEATABLE_VARIATION)->addParam('section-name', $name )->addParam('show-advanced-tools', true);
			$this->_getBlock( ffThemeBuilderBlock::ADVANCED_TOOLS )->wrapByModal()->injectOptions( $this );

		return $section;
	}

	public function endRepVariationSection() {
		$this->_s->endSection();
	}

	public function addElement($type, $id = NULL, $name = NULL){
		$this->_s->addElement( $type, $id, $name );
	}

	public function addOption($type, $id = NULL, $label = NULL, $content = NULL){
		return $this->_s->addOption( $type, $id, $label, $content );
	}

	public function addOptionNL($type, $id = NULL, $label = NULL, $content = NULL){
		return $this->_s->addOptionNL( $type, $id, $label, $content );
	}


	public function startSection($id, $type = NULL){
		return $this->_s->startSection( $id, $type );
	}

	public function endSection(){
		$this->_s->endSection();
	}




    public function startTabs() {
		$s = $this->_s;

        // start tab
        $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ffb-modal__tabs">');

            // empty header, will be added lately
            $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ffb-modal__tab-headers clearfix">');
            $s->addElement(ffOneElement::TYPE_HTML,'', '</div>');

            // start tab content
            $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ffb-modal__tab-contents clearfix">');
    }

    public function endTabs() {
		$s = $this->_s;

            // end contents
            $s->addElement(ffOneElement::TYPE_HTML,'', '</div>');

        // end tabs
        $s->addElement(ffOneElement::TYPE_HTML,'', '</div>');
    }


    public function startTab( $name, $isActive = false ) {

		$s = $this->_s;

        $headerActive = '';
        $contentActive = '';

        if( $isActive ) {
            $headerActive = ' ffb-modal__tab-header--active';
            $contentActive = ' ffb-modal__tab-content--active';
        }

        // Header



        $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ffb-modal__tab-header '.$headerActive.'" data-tab-header-name="'.$name.'">'.$name.'</div>');

        // Content
        $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ffb-modal__tab-content '.$contentActive.'" data-tab-content-name="'.$name.'">');
            $s->addElement(ffOneElement::TYPE_HTML,'', '<div class="ffb-modal__content--options ffb-options">');


    }

    public function endTab() {
			$this->_s->addElement(ffOneElement::TYPE_HTML,'', '</div>'); // end content--options
		$this->_s->addElement(ffOneElement::TYPE_HTML,'', '</div>'); // end content
    }


    public function startModal( $modalName, $modalClass ) {

        $html = '';

        $html .= '<div class="ffb-modal-holder '.$modalClass.'">';

        $html .= '<div class="ffb-modal-opener-button"></div>';

        $html .='<div class="ffb-modal ffb-modal-nested">';
            $html .='<div class="ffb-modal__vcenter-wrapper">';
                $html .='<div class="ffb-modal__vcenter ffb-modal__action-done">';
                    $html .='<div class="ffb-modal__box">';
                        $html .='<div class="ffb-modal__header">';
                            $html .='<div class="ffb-modal__name">';
                                $html .=$modalName;
                            $html .='</div>';
                        $html .='</div>';
                        $html .='<div class="ffb-modal__body">';
                            $html .='<div class="ffb-modal__content--options ffb-options">';

		$this->_s->addElement(ffOneElement::TYPE_HTML,'', $html); // end content--options
    }

    public function endModal() {
        $html = '';

                            $html .='</div>';
                        $html .='</div>';
                        $html .='<div class="ffb-modal__footer">';
                            $html .='<a href="#" class="ffb-modal__button-save ffb-modal__action-done">Done</a>';
                        $html .='</div>';
                    $html .='</div>';
                $html .='</div>';
            $html .='</div>';
        $html .='</div>';

        $html .='</div>';

        $this->_s->addElement(ffOneElement::TYPE_HTML,'', $html); // end content--options
    }

	public function getStructure() {

	}
/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/
	protected function _getBlock( $blockClassName ) {
		return $this->_getThemeBuilderBlockFactory()->createBlock( $blockClassName );
	}
/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/
	/**
	 * @return ffThemeBuilderBlockFactory
	 */
	private function _getThemeBuilderBlockFactory()
	{
		if( $this->_themeBuilderBlockFactory == null ) {

			$this->_themeBuilderBlockFactory = ffContainer()->getThemeFrameworkFactory()->getThemeBuilderBlockFactory();
		}
		return $this->_themeBuilderBlockFactory;
	}
/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS 
/**********************************************************************************************************************/
}