<?php

class ffWPMLBridge extends ffBasicObject {
	
	public function isWPMLActive() {
		return class_exists('SitePress');
	}
	
	public function getListOfLanguages() {
		
		if( !$this->isWPMLActive() ) {
			return array();
		}
		
		$langs = array();
		global $sitepress;
	
		foreach ($sitepress->get_active_languages() as $key => $value) {
			$oneLanguage = array(
				'name' => $value['native_name'],
				'value' =>  strtolower( $key ),
			);
			$langs[] = $oneLanguage;
		}
		
		return $langs;
	}

    private function icl_get_languages( $a = '' ) {
        if ( $a ) {
            parse_str( $a, $args );
        } else {
            $args = '';
        }
        global $sitepress;
        $langs = $sitepress->get_ls_languages( $args );

        return $langs;
    }

    public function getLanguagesLinkingToCurrentView() {
        $languages = apply_filters( 'wpml_active_languages', '', 'skip_missing=0&orderby=id&order=asc&link_empty_to=');
        return $languages;
    }

    public function getLanguageUrl( $languageCode ) {
        global $sitepress;
        return $sitepress->language_url( $languageCode );
    }

    public function getLanguages() {
        if( !$this->isWPMLActive() ) {
			return array();
		}

		$langs = array();
		global $sitepress;

        return ( $sitepress->get_active_languages() );

		foreach ($sitepress->get_active_languages() as $key => $value) {
			$oneLanguage = array(
				'name' => $value['native_name'],
				'value' =>  strtolower( $key ),
			);
			$langs[] = $oneLanguage;
		}

		return $langs;
    }
	
	public function getCurrentLanguage() {
		
		if( !$this->isWPMLActive() ) {
			return null;
		}
		
		global $sitepress;
		$active_lang = $sitepress->get_current_language();
		return $active_lang;
	}

    public function getDefaultLanguage() {
        if( !$this->isWPMLActive() ) {
			return null;
		}

        global $sitepress;
        $defaultLang = $sitepress->get_default_language();

        return $defaultLang;
    }

    public function getPostIdInDifferentLanguage( $postId, $postType, $language ) {
        return wpml_object_id_filter( $postId, $postType, false, $language);
    }

    public function getTaxonomyIdInDifferentLanguage( $taxonomyId, $taxonomyType, $language ) {
        return wpml_object_id_filter($taxonomyId, $taxonomyType, false, $language);
    }

}