<?php
namespace TAMAS\AstroBundle\DISHASToolbox\Conversion;

// Array with loop ability on the last entry
class RadixBase extends \ArrayObject
{
    
    // When $i exceed the array size, return the last entry
    public function offsetGet($i)
    {
        if ($i >= count($this)) {
            return parent::offsetGet(count($this) - 1);
        }
        return parent::offsetGet($i);
    }
    
    public function fromArray($array)
    {
        foreach ($array as $key => $entry) {
            parent::offsetSet($key, $entry);
        }
    }
}