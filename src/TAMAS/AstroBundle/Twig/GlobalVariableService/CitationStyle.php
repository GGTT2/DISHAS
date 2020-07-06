<?php

/**
 * TAMASObjectProperties is a service that helps displaying global information about the entity managed in the page. 
 * This $properties variable is made accessible for all twig templates - see \app\config\config.yml @twig
 */

namespace TAMAS\AstroBundle\Twig\GlobalVariableService;

class CitationStyle {

    public $csl;
    public $locale;

    public function __construct() {
    	if(file_exists('csl/chicago-author-date.csl')) {
        	$this->csl = file_get_contents('csl/chicago-author-date.csl');
    	}
		if(file_exists('csl/locales-en-US.xml')) {
			$this->locale = file_get_contents('csl/locales-en-US.xml');
		}
    }
}
