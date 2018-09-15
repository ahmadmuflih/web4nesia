<?php

class ffThemeBuilderBlock_AdvancedTools extends ffThemeBuilderBlock {
/**********************************************************************************************************************/
/* OBJECTS
/**********************************************************************************************************************/
	const PARAM_RETURN_AS_STRING = 'return_as_string';
	const PARAM_WRAP_BY_MODAL = 'wrap_by_modal';
/**********************************************************************************************************************/
/* PRIVATE VARIABLES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* CONSTRUCT
/**********************************************************************************************************************/
	protected function _init() {
		$this->_setInfo( ffThemeBuilderBlock::INFO_ID, 'advanced-tools');
		$this->_setInfo( ffThemeBuilderBlock::INFO_WRAPPING_ID, 'a-t');
		$this->_setInfo( ffThemeBuilderBlock::INFO_WRAP_AUTOMATICALLY, true);
		$this->_setInfo( ffThemeBuilderBlock::INFO_IS_REFERENCE_SECTION, false);
		$this->_setInfo( ffThemeBuilderBlock::INFO_SAVE_ONLY_DIFFERENCE, true);
	}
/**********************************************************************************************************************/
/* PUBLIC FUNCTIONS
/**********************************************************************************************************************/
	public function wrapByModal() {
		$this->setParam( ffThemeBuilderBlock_AdvancedTools::PARAM_WRAP_BY_MODAL, true );
		return $this;
	}
/**********************************************************************************************************************/
/* PUBLIC PROPERTIES
/**********************************************************************************************************************/

/**********************************************************************************************************************/
/* PRIVATE FUNCTIONS
/**********************************************************************************************************************/
	private function htmlParser($htmlCode) {
		$htmlPattern = '/.(?)([^>]*)>/';
		preg_match($htmlPattern, $htmlCode, $matches);

		$htmlTag = strtok($matches[1], ' ');
		$htmlAttributes = array(
			'tag' => $htmlTag,
		);

		$attributesPattern = '/(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/';
		preg_match_all($attributesPattern, $matches[0], $matches);

		foreach($matches[1] as $key => $attribute) {
			$htmlAttributes[$attribute] = $matches[2][$key];
		}

		return $htmlAttributes;

	}


	protected function _render( $query ) {

		if( $this->_queryIsEmpty() ){
			return $this->getParam('content');
		}

		$content = $this->getParam('content');
		$originalAttributes = $this->htmlParser($content);
		$content = strstr($content, '>');
		$data = $query->getCurrentQueryDataPart();
		$helper = ffContainer()->getMultiAttrHelper();

		foreach($originalAttributes as $key => $value){
			if($key != 'tag'){
				$helper->addParam($key, $value);
			}
		}

		if(!empty($data)){
			foreach($data as $key => $value){
				switch($key){
					case 'id':
						$helper->addParam($key, $value);
						break;

					case 'cls':
						$helper->addParam('class', $value);
						break;

					case 'v':
						$bootstrapVisibility = $this->styleParser($key, $value);
						$helper->addParam('class', $bootstrapVisibility);
						break;

					default:
						$styleValue = $this->styleParser($key, $value);
						$helper->addParam('style', $styleValue);
						break;
				}
			}
		}

		echo '<' . $originalAttributes['tag'] . ' ' .  $helper->getAttrString() . $content;

	}



	protected function _injectOptions( ffThemeBuilderOptionsExtender $s ) {

		$wrapByModal = $this->getParam( ffThemeBuilderBlock_AdvancedTools::PARAM_WRAP_BY_MODAL, false );

		if( $wrapByModal ) {
			$s->startModal(  'Advanced Options', 'ff-advanced-options' );
		}

		$s->addElement( ffOneElement::TYPE_TABLE_START );

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Info');

				$s->addElement(ffOneElement::TYPE_DESCRIPTION,'', 'This is advanced options tab. These settings will be printed as inline settings into the wrapper, where there are applied');

			//    $s->addOption(ffOneOption::TYPE_TEXT,'advanced-tools', 'Advanced Tools', 'ssss');
			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Identification');
				$s->addOptionNL( ffOneOption::TYPE_TEXT, 'id', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'ID (attribute)');
				$s->addOptionNL( ffOneOption::TYPE_TEXT, 'cls', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'CSS class');
			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Margin');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'mgt', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Margin Top (px)');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'mgr', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Margin Right (px)');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'mgb', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Margin Bottom (px)');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'mgl', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Margin Left (px)');
			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Padding');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'pdt', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Padding Top (px)');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'pdr', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Padding Right (px)');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'pdb', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Padding Bottom (px)');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'pdl', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Padding Left (px)');
			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Design');
				$s->addOptionNL( ffOneOption::TYPE_SELECT, 'clr', '', '')
					->addSelectValue('', '')
					->addSelectValue('Accent', 'accent')
					->addSelectValue('Custom', 'custom')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Color');
				$s->addOptionNL( ffOneOption::TYPE_NUMBER, 'oc', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Opacity');
				$s->addOption( ffOneOption::TYPE_NUMBER, 'rc', '', '')
					->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Border radius (px)');
			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Background');
				$s->startSection('bg');
					$s->addOptionNL( ffOneOption::TYPE_SELECT, 'clr', '', '')
						->addSelectValue('', '')
						->addSelectValue('Accent', 'accent')
						->addSelectValue('Custom', 'custom')
						->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Background color');

					$s->addOptionNL( ffOneOption::TYPE_IMAGE, 'img', 'Background image', '');

					$s->addOptionNL( ffOneOption::TYPE_SELECT, 'rpt', '', '')
						->addSelectValue('', '')
						->addSelectValue('no-repeat', 'no-repeat')
						->addSelectValue('repeat-x', 'repeat-x')
						->addSelectValue('repeat-y', 'repeat-y')
						->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Background repeat');

					$s->addOptionNL( ffOneOption::TYPE_SELECT, 'sz', '', '')
						->addSelectValue('', '')
						->addSelectValue('Cover', 'cover')
						->addSelectValue('Contain', 'contain')
						->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Background size');
				$s->endSection();


			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Typography');
				$s->startSection('f');
					$s->addOptionNL(ffOneOption::TYPE_TEXT, 's', '', '')
						->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Font size (px)');
					$s->addOptionNL(ffOneOption::TYPE_TEXT, 'f', '', '')
						->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Font family');
					$s->addOptionNL( ffOneOption::TYPE_SELECT, 'w', '', '')
						->addSelectValue('', '')
						->addSelectValue('Normal', 'normal')
						->addSelectValue('Bold', 'bold')
						->addSelectNumberRange(100, 900, 100)
						->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Font weight');
					$s->addOptionNL( ffOneOption::TYPE_SELECT, 't', '', '')
						->addSelectValue('', '')
						->addSelectValue('Normal', 'normal')
						->addSelectValue('Italic', 'italic')
						->addParam( ffOneOption::PARAM_TITLE_AFTER, 'Font style');
				$s->endSection();
			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

			$s->addElement( ffOneElement::TYPE_TABLE_DATA_START, '', 'Visibility');
				$s->startSection('v');
					$s->addOptionNL( ffOneOption::TYPE_CHECKBOX, 'xs', 'Hide on XS (Phone)', 0);
					$s->addOptionNL( ffOneOption::TYPE_CHECKBOX, 'sm', 'Hide on SM (Tablet)', 0);
					$s->addOptionNL( ffOneOption::TYPE_CHECKBOX, 'md', 'Hide on MD (Laptop)', 0);
					$s->addOptionNL( ffOneOption::TYPE_CHECKBOX, 'lg', 'Hide on LG (Desktop)', 0);
				$s->endSection();
			$s->addElement( ffOneElement::TYPE_TABLE_DATA_END);

		$s->addElement( ffOneElement::TYPE_TABLE_END );


		if( $wrapByModal ) {
			$s->endModal();
		}

	}


	private function styleParser($parameter, $value) {
		if( empty( $value ) ) {
			return '';
		}

		switch($parameter){

			/* MARGINS */
			case 'mgt':
				return 'margin-top:'.$value.'px;';
				break;

			case 'mgr':
				return 'margin-right:'.$value.'px;';
				break;

			case 'mgb':
				return 'margin-bottom:'.$value.'px;';
				break;

			case 'mgl':
				return 'margin-left:'.$value.'px;';
				break;

			/* PADDINGS */
			case 'pdt':
				return 'padding-top:'.$value.'px;';
				break;

			case 'pdr':
				return 'padding-right:'.$value.'px;';
				break;

			case 'pdb':
				return 'padding-bottom:'.$value.'px;';
				break;

			case 'pdl':
				return 'padding-left:'.$value.'px;';
				break;

			/* COLORS */
			case 'oc':
				return 'opacity:'.$value.';';
				break;

			case 'rc':
				return 'border-radius:'.$value.'px;';
				break;

			/* BACKGROUND-SETTINGS */
			case 'bg':
				$backgroundStyle = '';
				foreach($value as $key => $item){
					switch($key){
						case 'img':
							$image = substr($item, strpos($item, 'http'));
							$imageUrl = substr($image, 0, strpos($image, '"'));
							$backgroundStyle .= 'background-image: url('.$imageUrl.');';
							break;

						case 'rpt':
							$backgroundStyle .= 'background-repeat:'.$item.';';
							break;

						case 'sz':
							$backgroundStyle .= 'background-size:'.$item.';';
							break;
					}
				}

				return $backgroundStyle;
				break;

			/* FONT-SETTINGS */
			case 'f':
				$fontStyle = '';
				foreach($value as $key => $item){
					switch($key){
						case 's':
							$fontStyle .= 'font-size:'.$item.'px;';
							break;
						case 'f':
							$fontStyle .= 'font-family:'.$item.';';
							break;
						case 'w':
							$fontStyle .= 'font-weight:'.$item.';';
							break;
						case 't':
							$fontStyle .= 'font-style:'.$item.';';
							break;
					}
				}

				return $fontStyle;
				break;

			/* BOOTSTRAP-SETTINGS */
			case 'v':
				$bootstrapVisibility = '';
				foreach($value as $key => $item){
					switch($key){
						case 'xs':
							$bootstrapVisibility .= 'hidden-sm ';
							break;
						case 'sm':
							$bootstrapVisibility .= 'hidden-sm ';
							break;
						case 'md':
							$bootstrapVisibility .= 'hidden-md ';
							break;
						case 'lg':
							$bootstrapVisibility .= 'hidden-lg';
							break;
					}
				}

				return $bootstrapVisibility;
				break;

			default:
				return 'style="'.$value.'" ';
				break;
		}

	}

/**********************************************************************************************************************/
/* PRIVATE GETTERS & SETTERS
/**********************************************************************************************************************/
}