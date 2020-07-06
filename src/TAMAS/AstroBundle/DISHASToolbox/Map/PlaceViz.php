<?php 

namespace TAMAS\AstroBundle\DISHASToolbox\Map;


class PlaceViz {
    
    /**
     *
     * @var {integer}
     */
    public $id;
    
    /**
     * Name of the geographic place
     * @var {string}
     */
    public $name;
    
    /**
     * Name of the located object
     * @var {string}
     */
    public $title;
    
    /**
     * Latitude of the place
     * @var {string}
     */
    public $lat;
    
    /**
     * Longitude of the place
     * @var {string}
     */
    public $long;
    
    /**
     *
     * @var {array} of object subPlaceName
     */
    public $allPlaces;
    
    /**
     *
     * @var {array} of object SubObject
     */
    public $allObjects;
    
    public function __construct(){
        $this->allPlaces = [];
        $this->allObjects = [];
    }
    
    
}