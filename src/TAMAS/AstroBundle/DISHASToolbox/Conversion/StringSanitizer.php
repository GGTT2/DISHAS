<?php

namespace TAMAS\AstroBundle\DISHASToolbox\Conversion;
/**
 * Service for sanitizing a string
 */
class StringSanitizer {
    /**
     * sanitizeName
     * 
     * Return the string in a filename-friendly style
     * 
     * @param string $string
     * @return string
     */
    public function sanitizeName($string) {
        $step1 = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '-', $string);
        $step2 = preg_replace('/ /','-',$step1);
        return $step2;
    }
}

