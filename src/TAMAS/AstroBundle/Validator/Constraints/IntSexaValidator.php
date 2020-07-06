<?php

namespace TAMAS\AstroBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TAMAS\AstroBundle\DISHASToolbox\Conversion\NumberConvertor;

class IntSexaValidator extends ConstraintValidator {
    
    private $numberConvertor;
    
    public function __construct(NumberConvertor $nc){
        $this->numberConvertor = $nc;
    }

    public function validate($value, Constraint $constraint) {
//    if (!preg_match('/^\d+([;]([0|1|2|3|4|5]\d[,])*([0|1|2|3|4|5]\d))?$/', $value) && $value!==null) {
//      $this->context->addViolation($constraint->message);
//    }
        //Si la valeur n'est pas cohérente avec la conversion smartNumber
        //Envoyer une exception qui sera catch par le controller. 
    }

}
