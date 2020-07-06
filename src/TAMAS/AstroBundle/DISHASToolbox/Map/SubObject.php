<?php
namespace TAMAS\AstroBundle\DISHASToolbox\Map;


class SubObject{
    /**
     *
     * @var {integer}
     */
    public $id;
    
    /**
     *
     * @var {string}
     */
    public $title;
    
    public function __construct($id, $title) {
        $this->id = $id;
        $this->title = $title;
    }
    public function __toString(){
        return $this->title;
    }
}