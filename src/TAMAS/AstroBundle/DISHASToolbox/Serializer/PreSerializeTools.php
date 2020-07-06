<?php
namespace TAMAS\AstroBundle\DISHASToolbox\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

class PreSerializeTools
{

    public static function generateKibanaId($object) {
    	$fullClassName = explode('\\' , get_class($object));
        $class = end($fullClassName);

        return $class . $object->getId();
    }

    public static function serializeEntity($entity, $groups = []){
        $serializer = SerializerBuilder::create()->build();
        if($groups)
            $object = $serializer->serialize($entity, 'json', SerializationContext::create()->setGroups($groups));
        else{
            $object= $serializer->serialize($entity, 'json');
        }
        return $object;
    }


}