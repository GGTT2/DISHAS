<?php

namespace TAMAS\AstroBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Terminus extends Constraint {

    public $messageErrorLogic = 'The terminus post quem must be smaller than the terminus ante quem.';
    public $messageBothField = 'Both fields TPQ and TAQ must be filled. For a specific year, specify the value in both boxes. ';
    public $messageDateError = 'The input should be a year e.g. -53 or 1256.';

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }

}
