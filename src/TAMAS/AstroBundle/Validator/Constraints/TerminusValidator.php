<?php

namespace TAMAS\AstroBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TerminusValidator extends ConstraintValidator {

    public function validate($object, Constraint $constraint) {

        if ($object->getTpq() == null && $object->getTaq() !== null) {
            $empty = 'tpq';
        } elseif ($object->getTaq() == null && $object->getTpq() !== null) {
            $empty = 'taq';
        }
        if (isset($empty)) {
            $this->context->buildViolation($constraint->messageBothField)
                    ->atPath($empty)
                    ->addViolation();
        }

        if ($object->getTpq() > $object->getTaq()) {
            $this->context->buildViolation($constraint->messageErrorLogic)
                    ->atPath('tpq')
                    ->addViolation();
        }

        foreach (['tpq', 'taq'] as $date) {
            $getter = "get" . $date;

            if ($object->{$getter}() !== null && !preg_match('/^(-)?\d{1,4}$/', $object->{$getter}(), $matches)) {
                $this->context->buildViolation($constraint->messageDateError)
                        ->atPath($date)
                        ->addViolation();
            }
        }
    }

}
