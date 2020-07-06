<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\Validator\Constraints as TAMASAssert;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * MathematicalParameter
 *
 * @ORM\Table(name="mathematical_parameter")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\MathematicalParameterRepository")
 * @UniqueEntity(fields={ "entryShift","entryShift2", "entryDisplacementOriginalBase","argument1Shift", "argument1DisplacementOriginalBase", "argument2Shift", "argument2DisplacementOriginalBase"}, ignoreNull=false, errorPath="entryDisplacementOriginalBase", message="This parameter is already recorded in the database")
 * @TAMASAssert\SmartNumber
 */
class MathematicalParameter
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    /**
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="arg_number", type="integer", nullable=true)
     * @Assert\NotNull()
     */
    private $argNumber;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $typeOfNumberArgument1;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $typeOfNumberArgument2;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $typeOfNumberEntry;

    /**
     * @Type("string")
     *
     * @var int 0 = shift ; 1 = argument ; 2 = shiftAndDisplacement
     *     
     * @ORM\Column(name="type_of_parameter", type="integer", nullable=true)
     * @Assert\NotNull()
     */
    private $typeOfParameter;

    /**
     * @Type("string")
     *
     * @var int
     * @Assert\Type("int")
     * @ORM\Column(name="argument1_shift", type="integer", nullable=true)
     */
    private $argument1Shift;

    /**
     * @Type("string")
     *
     * @var int
     * @Assert\Type("int")
     * @ORM\Column(name="argument2_shift", type="integer", nullable=true)
     */
    private $argument2Shift;

    /**
     * @Type("string")
     *
     * @var int
     * @Assert\Type("int")
     * @ORM\Column(name="entry_shift", type="integer", nullable=true)
     */
    private $entryShift;

    /**
     * @Type("string")
     *
     * @var int
     * @Assert\Type("int")
     * @ORM\Column(name="entry_shift2", type="integer", nullable=true)
     */
    private $entryShift2;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="argument1_displacement_original_base", type="string", length=255, nullable=true)
     */
    private $argument1DisplacementOriginalBase;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="argument2_displacement_original_base", type="string", length=255, nullable=true)
     */
    private $argument2DisplacementOriginalBase;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="entry_displacement_original_base", type="string", length=255, nullable=true)
     */
    private $entryDisplacementOriginalBase;

    /**
     * @Type("string")
     *
     * @var float
     * @ORM\Column(name="argument1_displacement_float", type="float", length=255, nullable=true)
     */
    private $argument1DisplacementFloat;

    /**
     * @Type("string")
     *
     * @var float
     * @ORM\Column(name="argument2_displacement_float", type="float", length=255, nullable=true)
     */
    private $argument2DisplacementFloat;

    /**
     * @Type("string")
     *
     * @var float
     * @ORM\Column(name="entry_displacement_float", type="float", length=255, nullable=true)
     */
    private $entryDisplacementFloat;

    /**
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     *
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @Groups({"tableContentMain"})
     * @var string
     */
    private $kibanaName;

    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;

    /**
     * @PreSerialize
     */
    private function onPreSerialize()
    {
        $this->kibanaName = $this->__toString();
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set entryShift
     *
     * @param integer $entryShift
     *
     * @return MathematicalParameter
     */
    public function setEntryShift($entryShift)
    {
        $this->entryShift = $entryShift;

        return $this;
    }

    /**
     * Get entryShift
     *
     * @return int
     */
    public function getEntryShift()
    {
        return $this->entryShift;
    }

    /**
     * Set entryDisplacementOriginalBase
     *
     * @param string $entryDisplacementOriginalBase
     *
     * @return MathematicalParameter
     */
    public function setEntryDisplacementOriginalBase($entryDisplacementOriginalBase)
    {
        $this->entryDisplacementOriginalBase = $entryDisplacementOriginalBase;

        return $this;
    }

    /**
     * Get entryDisplacementOriginalBase
     *
     * @return string
     */
    public function getEntryDisplacementOriginalBase()
    {
        return $this->entryDisplacementOriginalBase;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return MathematicalParameter
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return MathematicalParameter
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set createdBy
     *
     * @param \TAMAS\AstroBundle\Entity\Users $createdBy
     *
     * @return MathematicalParameter
     */
    public function setCreatedBy(\TAMAS\AstroBundle\Entity\Users $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \TAMAS\AstroBundle\Entity\Users $updatedBy
     *
     * @return MathematicalParameter
     */
    public function setUpdatedBy(\TAMAS\AstroBundle\Entity\Users $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    
    /**
     * Set entryDisplacementFloat
     *
     * @param float $entryDisplacementFloat
     *
     * @return MathematicalParameter
     */
    public function setEntryDisplacementFloat($entryDisplacementFloat)
    {
        $this->entryDisplacementFloat = $entryDisplacementFloat;

        return $this;
    }

    /**
     * Get entryDisplacementFloat
     *
     * @return float
     */
    public function getEntryDisplacementFloat()
    {
        return $this->entryDisplacementFloat;
    }

    /**
     * Set typeOfParameter
     *
     * @param integer $typeOfParameter
     *
     * @return MathematicalParameter
     */
    public function setTypeOfParameter($typeOfParameter)
    {
        $this->typeOfParameter = $typeOfParameter;

        return $this;
    }

    /**
     * Get typeOfParameter
     *
     * @return integer
     */
    public function getTypeOfParameter()
    {
        return $this->typeOfParameter;
    }

    /**
     *
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context)
    {
        $shift = [$this->argument1Shift, $this->entryShift];
        $displacement = [$this->argument1DisplacementFloat, $this->entryDisplacementFloat];
        if ($this->argNumber == 2) {
            $shift = array_merge($shift, [$this->argument2Shift, $this->entryShift2]);
            $displacement[] = $this->argument2DisplacementFloat;
        }

        if ($this->typeOfParameter == 0 || $this->typeOfParameter == 2) {
            // We need a shift for arg or Entry
            $validShift = false;
            foreach ($shift as $s) {
                if ($s) {
                    $validShift = true;
                    break;
                }
            }
            if (!$validShift) {
                $context->buildViolation('At least one of the shift must be filled; otherwise chose "displacement" as "Type of parameter"')
                ->atPath('typeOfParameter')    
                ->addViolation();
            }
        }
        if ($this->typeOfParameter == 1 || $this->typeOfParameter == 2) {
            $validDisplacement = false;
            foreach ($displacement as $d) {
                if ($d) {
                    $validDisplacement = true;
                    break;
                }
            }
            if (!$validDisplacement){
                $context->buildViolation('At least one of the displacement must be filled; otherwise chose "shift" as "Type of parameter"')
                ->atPath('typeOfParameter')        
                ->addViolation();
            }
        }

/*         if ($this->typeOfParameter === 2) {
            if ($this->entryShift === null && $this->argument1Shift === null) {
                $context->buildViolation('One of the shift must be filled')
                    ->atPath('argumentShift')
                    ->addViolation();
                $context->buildViolation('')
                    ->atPath('entryShift')
                    ->addViolation();
            }
            if ($this->entryDisplacementOriginalBase === null && $this->argument1DisplacementOriginalBase === null) {
                $context->buildViolation('One of the displacement must be filled')
                    ->atPath('argumentDisplacementSmartNumber')
                    ->addViolation();
                $context->buildViolation('')
                    ->atPath('entryDisplacementSmartNumber')
                    ->addViolation();
            }
        } */
    }


    /**
     * Set argNumber.
     *
     * @param int|null $argNumber
     *
     * @return MathematicalParameter
     */
    public function setArgNumber($argNumber = null)
    {
        $this->argNumber = $argNumber;

        return $this;
    }

    /**
     * Get argNumber.
     *
     * @return int|null
     */
    public function getArgNumber()
    {
        return $this->argNumber;
    }

    /**
     * Set argument1Shift.
     *
     * @param int|null $argument1Shift
     *
     * @return MathematicalParameter
     */
    public function setArgument1Shift($argument1Shift = null)
    {
        $this->argument1Shift = $argument1Shift;
        return $this;
    }

    /**
     * Get argument1Shift.
     *
     * @return int|null
     */
    public function getArgument1Shift()
    {
        return $this->argument1Shift;
    }

    /**
     * Set argument2Shift.
     *
     * @param int|null $argument2Shift
     *
     * @return MathematicalParameter
     */
    public function setArgument2Shift($argument2Shift = null)
    {
        $this->argument2Shift = $argument2Shift;

        return $this;
    }

    /**
     * Get argument2Shift.
     *
     * @return int|null
     */
    public function getArgument2Shift()
    {
        return $this->argument2Shift;
    }

    /**
     * Set argument1DisplacementOriginalBase.
     *
     * @param string|null $argument1DisplacementOriginalBase
     *
     * @return MathematicalParameter
     */
    public function setArgument1DisplacementOriginalBase($argument1DisplacementOriginalBase = null)
    {
        $this->argument1DisplacementOriginalBase = $argument1DisplacementOriginalBase;

        return $this;
    }

    /**
     * Get argument1DisplacementOriginalBase.
     *
     * @return string|null
     */
    public function getArgument1DisplacementOriginalBase()
    {
        return $this->argument1DisplacementOriginalBase;
    }

    /**
     * Set argument2DisplacementOriginalBase.
     *
     * @param string|null $argument2DisplacementOriginalBase
     *
     * @return MathematicalParameter
     */
    public function setArgument2DisplacementOriginalBase($argument2DisplacementOriginalBase = null)
    {
        $this->argument2DisplacementOriginalBase = $argument2DisplacementOriginalBase;

        return $this;
    }

    /**
     * Get argument2DisplacementOriginalBase.
     *
     * @return string|null
     */
    public function getArgument2DisplacementOriginalBase()
    {
        return $this->argument2DisplacementOriginalBase;
    }

    /**
     * Set argument1DisplacementFloat.
     *
     * @param float|null $argument1DisplacementFloat
     *
     * @return MathematicalParameter
     */
    public function setArgument1DisplacementFloat($argument1DisplacementFloat = null)
    {
        $this->argument1DisplacementFloat = $argument1DisplacementFloat;

        return $this;
    }

    /**
     * Get argument1DisplacementFloat.
     *
     * @return float|null
     */
    public function getArgument1DisplacementFloat()
    {
        return $this->argument1DisplacementFloat;
    }

    /**
     * Set argument2DisplacementFloat.
     *
     * @param float|null $argument2DisplacementFloat
     *
     * @return MathematicalParameter
     */
    public function setArgument2DisplacementFloat($argument2DisplacementFloat = null)
    {
        $this->argument2DisplacementFloat = $argument2DisplacementFloat;

        return $this;
    }

    /**
     * Get argument2DisplacementFloat.
     *
     * @return float|null
     */
    public function getArgument2DisplacementFloat()
    {
        return $this->argument2DisplacementFloat;
    }

    /**
     * Set typeOfNumberArgument1.
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumberArgument1
     *
     * @return MathematicalParameter
     */
    public function setTypeOfNumberArgument1(\TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumberArgument1)
    {
        $this->typeOfNumberArgument1 = $typeOfNumberArgument1;

        return $this;
    }

    /**
     * Get typeOfNumberArgument1.
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getTypeOfNumberArgument1()
    {
        return $this->typeOfNumberArgument1;
    }

    /**
     * Set typeOfNumberArgument2.
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumberArgument2
     *
     * @return MathematicalParameter
     */
    public function setTypeOfNumberArgument2(\TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumberArgument2)
    {
        $this->typeOfNumberArgument2 = $typeOfNumberArgument2;

        return $this;
    }

    /**
     * Get typeOfNumberArgument2.
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getTypeOfNumberArgument2()
    {
        return $this->typeOfNumberArgument2;
    }

    /**
     * Set typeOfNumberEntry.
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumberEntry
     *
     * @return MathematicalParameter
     */
    public function setTypeOfNumberEntry(\TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumberEntry)
    {
        $this->typeOfNumberEntry = $typeOfNumberEntry;

        return $this;
    }

    /**
     * Get typeOfNumberEntry.
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getTypeOfNumberEntry()
    {
        return $this->typeOfNumberEntry;
    }

    /**
     * Set entryShift2.
     *
     * @param int|null $entryShift2
     *
     * @return MathematicalParameter
     */
    public function setEntryShift2($entryShift2 = null)
    {
        $this->entryShift2 = $entryShift2;

        return $this;
    }

    /**
     * Get entryShift2.
     *
     * @return int|null
     */
    public function getEntryShift2()
    {
        return $this->entryShift2;
    }

    public function __toString()
    {
        $argumentShift = $this->argument1Shift ? $this->argument1Shift . " [arg. shift]  " : " 0 [arg. shift]  ";
        $entryShift = $this->entryShift ? $this->entryShift . " [entry shift]  " : " 0 [entry shift]";
        $argumentDisplacementOriginalBase = $this->argument1DisplacementOriginalBase ? $this->argument1DisplacementOriginalBase . " [arg. displacement]  " : " 0 [arg. displacement]  ";
        $entryDisplacementOriginalBase = $this->entryDisplacementOriginalBase ? $this->entryDisplacementOriginalBase . " [entry displacement]  " : " 0 [entry displacement]  ";
        $argument2Shift = "";
        $entryShift2 = "";
        $argument2DisplacementOriginalBase = "";
        $base = $this->getTypeOfNumbers();

        if ($this->getArgNumber() > 1) {
            $argument2Shift = $this->argument2Shift ? $this->argument2Shift . " [arg2. shift]  " : " 0 [arg2. shift]  ";
            $entryShift2 = $this->entryShift2 ? $this->entryShift2 . " [entry shift2]  " : " 0 [entry shift2]";
            $argument2DisplacementOriginalBase = $this->argument2DisplacementOriginalBase ? $this->argument2DisplacementOriginalBase . " [arg2. displacement]  " : " 0 [arg2. displacement]  ";
        }

        return $argumentShift . $argument2Shift . $entryShift . $entryShift2 . $argumentDisplacementOriginalBase . $argument2DisplacementOriginalBase . $entryDisplacementOriginalBase . " ($base)";
    }

    /**
     * Convert the mathematical parameter type id into its string name
     * @return string
     */
    public function getParameterTypeName()
    {
        if ($this->typeOfParameter == 0) {
            return "Shift";
        } elseif ($this->typeOfParameter == 1) {
            return "Displacement";
        } else {
            return "Shift / Displacement";
        }
    }

    public function getTypeOfParameterDefinition()
    {
        if ($this->typeOfParameter == 0) {
            return "A table is said to be ‘shifted’ when the ordering of the arguments and/or entry is modified according to a permutation cycle.";
        } elseif ($this->typeOfParameter == 1) {
            return "A table is said to be ‘displaced’ when a constant value is added to its arguments and/or its entry.";
        } else {
            return "A table is said to be ‘shifted’ and ‘displaced’ when the ordering of the arguments and/or entry is
             modified according to a permutation cycle and a constant value is added to its arguments and/or its entry.";
        }
    }

    /**
         * returns a simplified string in order to shorten the value to make it appear entirely in a box
         * @param $value
         * @param bool $longDef
         * @return string
         */
        private function normalize($value, $longDef = true)
        {
            if ($longDef) {
                return $value;
            }
            // if the value ends with " ;"
            if (mb_substr($value, -2, 2) === " ;") {
                $value = mb_substr($value, 0, strlen($value) - 2);
            }
            if ($value == "00") {
                $value = "0";
            }
            return $value;
        }

    /**
     * Generates a string of all the parameter values for the arguments (1 and 2) and entry
     * @param bool $longDef
     * @return string
     */
    public function getParamValues($longDef = true)
    {
        $paramType = $this->typeOfParameter;

        $shift = $longDef ? " (shift)" : "";
        $displ = $longDef ? " (displ.)" : "";
        $arg1 = $longDef ? "<span class='mainContent'>Arg. 1</span>: " : "Arg. 1: ";
        $arg2 = $longDef ? "<span class='mainContent'>Arg. 2</span>: " : "Arg. 2: ";
        $entry = $longDef ? "<span class='mainContent'>Entry</span>: " : "Entr.: ";

        

        if ($paramType == 1) {
            $arg1Dis = $this->argument1DisplacementOriginalBase ? $this->normalize($this->argument1DisplacementOriginalBase, $longDef) : 0;
            $entryDis = $this->entryDisplacementOriginalBase ? $this->normalize($this->entryDisplacementOriginalBase, $longDef) : 0;

            $arg2values = "";
            if ($this->getArgNumber() > 1) {
                $arg2Dis = $this->argument2DisplacementOriginalBase ? $this->normalize($this->argument2DisplacementOriginalBase, $longDef) : 0;
                $arg2values = "$arg2$arg2Dis | ";
            }

            return "$arg1$arg1Dis | $entry$arg2values$entryDis";
        } elseif ($paramType == 2) {
            $arg1Shi = $this->argument1Shift ? $this->normalize($this->argument1Shift, $longDef) : 0;
            $entryShi = $this->entryShift ? $this->normalize($this->entryShift, $longDef) : 0;
            $entryShi = $this->entryShift2 ? $entryShi . " " . $this->normalize($this->entryShift2, $longDef) : $entryShi;

            $arg1Dis = $this->argument1DisplacementOriginalBase ? $this->normalize($this->argument1DisplacementOriginalBase, $longDef) : 0;
            $entryDis = $this->entryDisplacementOriginalBase ? $this->normalize($this->entryDisplacementOriginalBase, $longDef) : 0;

            $arg2values = "";
            if ($this->getArgNumber() > 1) {
                $arg2Shi = $this->argument2Shift ? $this->normalize($this->argument2Shift, $longDef) : 0;
                $arg2Dis = $this->argument2DisplacementOriginalBase ? $this->normalize($this->argument2DisplacementOriginalBase, $longDef) : 0;
                $arg2values = "$arg2$arg2Shi$shift / $arg2Dis$displ | ";
            }

            return "$arg1$arg1Shi$shift / $arg1Dis$displ | " . $arg2values . "$entry$entryShi$shift / $entryDis$displ";
        } else {
            $arg1Shi = $this->argument1Shift ? $this->normalize($this->argument1Shift, $longDef) : "0";
            $entryShi = $this->entryShift ? $this->normalize($this->entryShift, $longDef) : "0";
            $entryShi = $this->entryShift2 ? $entryShi . " ; " . $this->normalize($this->entryShift2, $longDef) : $entryShi;

            $arg2values = "";
            if ($this->getArgNumber() > 1) {
                $arg2Shi = $this->argument2Shift ? $this->normalize($this->argument2Shift, $longDef) : "0";
                $arg2values = "$arg2$arg2Shi | ";
            }

            return "$arg1$arg1Shi | $entry$arg2values$entryShi";
        }
    }

    /**
     * Generates a concatenation of the number types of the first, second argument and the entry
     * @return string
     */
    public function getTypeOfNumbers()
    {
        $typeOfNumbers = "";

        if ($this->typeOfNumberArgument1) {
            $typeOfNumbers = $typeOfNumbers . ucfirst($this->getTypeOfNumberArgument1()->getTypeName());
        }
        if ($this->typeOfNumberArgument2) {
            if ($this->getTypeOfNumberArgument2()->getTypeName() != $this->getTypeOfNumberArgument1()->getTypeName()) {
                $sep = $typeOfNumbers != "" ? " | " : "";
                $typeOfNumbers = $typeOfNumbers . $sep . ucfirst($this->getTypeOfNumberArgument2()->getTypeName());
            }
        }
        if ($this->typeOfNumberEntry) {
            if (($this->getTypeOfNumberEntry()->getTypeName() != $this->getTypeOfNumberArgument1()->getTypeName()) &&
                ($this->getTypeOfNumberEntry()->getTypeName() != $this->getTypeOfNumberArgument2()->getTypeName())
            ) {
                $sep = $typeOfNumbers != "" ? " | " : "";
                $typeOfNumbers = $typeOfNumbers . $sep . ucfirst($this->getTypeOfNumberEntry()->getTypeName());
            }
        }
        return $typeOfNumbers != "" ? $typeOfNumbers : null;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "mathematical parameter";
        if (!$isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
