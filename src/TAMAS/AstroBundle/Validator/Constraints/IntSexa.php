<?php

namespace TAMAS\AstroBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IntSexa extends Constraint
{
  public $message = "This field must match a correct integer plus sexagesimal string e.g. 312;14,59,02";
}
