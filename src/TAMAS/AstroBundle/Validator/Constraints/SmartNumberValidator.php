<?php
namespace TAMAS\AstroBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TAMAS\AstroBundle\DISHASToolbox\Conversion\NumberConvertor;
use TAMAS\AstroBundle\DISHASToolbox\Conversion\NumberViewException;

class SmartNumberValidator extends ConstraintValidator
{

    private $numberConvertor;

    public function __construct(NumberConvertor $nc)
    {
        $this->numberConvertor = $nc;
    }

    public function validate($value, Constraint $constraint)
    {
        $numberConvertor = $this->numberConvertor;

        if (is_a($value, 'TAMAS\AstroBundle\Entity\MathematicalParameter')) {

            $codeNameEntry = $value->getTypeOfNumberEntry()->getCodeName();
            $codeNameArgument1 = $value->getTypeOfNumberArgument1()->getCodeName();

            if ($value->getTypeOfParameter() > 0) {
                if ($value->getEntryDisplacementOriginalBase() != "") {
                    try {
                        $numberConvertor->fromString($value->getEntryDisplacementOriginalBase(), $codeNameEntry);
                    } catch (NumberViewException $e) {

                        $this->context->buildViolation($constraint->message)
                            ->atPath('typeOfNumberEntry')
                            ->addViolation();
                    }
                }
                if ($value->getArgument1DisplacementOriginalBase() != "") {
                    try {
                        $numberConvertor->fromString($value->getArgument1DisplacementOriginalBase(), $codeNameArgument1);
                    } catch (NumberViewException $e) {
                        $this->context->buildViolation($constraint->message)
                            ->atPath('typeOfNumberArgument1')
                            ->addViolation();
                    }
                }
                if ($value->getArgNumber() > 1) {
                    $codeNameArgument2 = $value->getTypeOfNumberArgument2()->getCodeName();

                    if ($value->getArgument2DisplacementOriginalBase() != "") {
                        try {
                            $numberConvertor->fromString($value->getArgument2DisplacementOriginalBase(), $codeNameArgument2);
                        } catch (NumberViewException $e) {
                            $this->context->buildViolation($constraint->message)
                                ->atPath('typeOfNumberArgument2')
                                ->addViolation();
                        }
                    }
                }
            }
        }
        if (is_a($value, 'TAMAS\AstroBundle\Entity\ParameterSet')) {
            $i = 0;
            foreach ($value->getParameterValues() as $value) {
                $codeName = $value->getTypeOfNumber()->getCodeName();

                foreach ([
                    $value->getValueOriginalBase(),
                    $value->getRange1InitialOriginalBase(),
                    $value->getRange1FinalOriginalBase(),
                    $value->getRange2InitialOriginalBase(),
                    $value->getRange2FinalOriginalBase(),
                    $value->getRange3InitialOriginalBase(),
                    $value->getRange3FinalOriginalBase()
                ] as $numberOriginal) {
                    if ($numberOriginal) {
                        try {
                            $numberConvertor->fromString($numberOriginal, $codeName);
                        } catch (NumberViewException $e) {
                            $this->context->buildViolation($constraint->message)
                                ->atPath('parameterValues[' . $i . '].typeOfNumber')
                                ->addViolation();
                        }
                    }
                }
                $i ++;
            }
        }
    }
}
