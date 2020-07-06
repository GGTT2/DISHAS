<?php

namespace TAMAS\AstroBundle\DISHASToolbox\Map;


class SubPlaceName{
    
    /**
     *
     * @var {string}
     */
    public $title;
    
    public function __construct($title) {
        $this->title = $title;
    }
    
    public function __toString(){
        return $this->title;
    }
}