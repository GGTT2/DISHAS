<?php
namespace TAMAS\AstroBundle\DISHASToolbox\Table;
/**
* Objects of this class contains the basic specification on each column of a table: whether the column contains a link to another data; whether it is a public column. 
* Depending n this two boolean, the process of building each fields will change. 
* Maybe other specification could be needed in the fucture. 
**/
class TAMASListColumnSpec
{
    
    public $link;
    
    public $public;
    
    public function __construct(bool $link = true, bool $public = false)
    {
        $this->link = $link;
        $this->public = $public;
    }
}
