<?php

namespace TAMAS\AstroBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SmartNumber extends Constraint
{

    
   public $message = "This type of number doesn't match the string formatting";
    
  //public $message = "This field must match a correct integer plus sexagesimal string e.g. 312;14,59,02";
   public function getTargets()
   {
       return self::CLASS_CONSTRAINT;
   }
   
}
