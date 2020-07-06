<?php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TAMAS\AstroBundle\Validator\Constraints as AstroAssert;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * ParameterValue
 *
 * @ORM\Table(name="parameter_value")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ParameterValueRepository")
 */
class ParameterValue
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    private $numberConvertor;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Groups({"parameterSetMain"})
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeOfNumber;

    /**
     * @Type("string")
     * @var float
     *
     * @ORM\Column(name="value_float", type="float", precision=65, scale=30, nullable=true)
     */
    private $valueFloat;

    /**
     * @var string
     *
     * @AstroAssert\IntSexa
     * @ORM\Column(name="value_original_base", type="string", length=255, nullable=true)
     */
    private $valueOriginalBase;

    /**
     * @var bool
     *
     * @ORM\Column(name="value_is_int_sexa", type="boolean", nullable=true)
     */
    private $valueIsIntSexa;

    /**
     * @Type("string")
     * @var float
     *
     * @ORM\Column(name="range1_initial_float", type="float", precision=65, scale=30, nullable=true)
     */
    private $range1InitialFloat;

    /**
     * @var string
     *
     * @AstroAssert\Intsexa
     * @ORM\Column(name="range1_initial_original_base", type="string", length=255, nullable=true)
     */
    private $range1InitialOriginalBase;

    /**
     *
     * @Type("string")
     * @var float
     *
     * @ORM\Column(name="range1_final_float", type="float", precision=65, scale=30, nullable=true)
     */
    private $range1FinalFloat;

    /**
     * @var string
     *
     * @AstroAssert\Intsexa
     * @ORM\Column(name="range1_final_original_base", type="string", length=255, nullable=true)
     */
    private $range1FinalOriginalBase;

    /**
     * @Type("string")
     * @var float
     *
     * @ORM\Column(name="range2_initial_float", type="float", precision=65, scale=30, nullable=true)
     */
    private $range2InitialFloat;

    /**
     * @var string
     *
     * @AstroAssert\Intsexa
     * @ORM\Column(name="range2_initial_original_base", type="string", length=255, nullable=true)
     */
    private $range2InitialOriginalBase;

    /**
     *
     * @Type("string")
     * @var float
     *
     * @ORM\Column(name="range2_final_float", type="float", precision=65, scale=30, nullable=true)
     */
    private $range2FinalFloat;

    /**
     *
     * @var string
     *
     * @AstroAssert\Intsexa
     * @ORM\Column(name="range2_final_original_base", type="string", length=255, nullable=true)
     */
    private $range2FinalOriginalBase;

    /**
     *
     * @Type("string")
     * @var float
     *
     * @ORM\Column(name="range3_initial_float", type="float", precision=65, scale=30, nullable=true)
     */
    private $range3InitialFloat;

    /**
     *
     * @var string
     *
     * @AstroAssert\Intsexa
     * @ORM\Column(name="range3_initial_original_base", type="string", length=255, nullable=true)
     */
    private $range3InitialOriginalBase;

    /**
     *
     * @Type("string")
     * @var float
     *
     * @ORM\Column(name="range3_final_float", type="float", precision=65, scale=30, nullable=true)
     */
    private $range3FinalFloat;

    /**
     *
     * @var string
     *
     * @AstroAssert\Intsexa
     * @ORM\Column(name="range3_final_original_base", type="string", length=255, nullable=true)
     */
    private $range3FinalOriginalBase;

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
     *
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
     * @Groups({"parameterSetMain"})
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\ParameterFormat", cascade={"persist"})
     */
    private $parameterFormat;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\ParameterSet", inversedBy="parameterValues", cascade={"persist"})
     */
    private $parameterSet;

    /**
     *
     * @Groups({"parameterSetMain"})
     * @var string
     */
    private $kibanaName;
    
    
    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;

    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $defaultTitle;

    /**
     * @Groups({"parameterSetMain"})
     * @var string
     */
    public $values;

    public function setValues()
    {
        if (! $this->values) {
            $originalValue = [];
            $originalValue["value"] = $this->valueOriginalBase;
            $originalValue["arg1"] = [
                "from" => $this->range1InitialOriginalBase,
                "to" => $this->range1FinalOriginalBase
            ];
            $originalValue["arg2"] = [
                "from" => $this->range2InitialOriginalBase,
                "to" => $this->range2FinalOriginalBase
            ];
            $originalValue["arg3"] = [
                "from" => $this->range3InitialOriginalBase,
                "to" => $this->range3FinalOriginalBase
            ];

            $floatValue = [];
            $floatValue["value"] = $this->valueFloat;
            $floatValue["arg1"] = [
                "from" => $this->range1InitialFloat,
                "to" => $this->range1FinalFloat
            ];
            $floatValue["arg2"] = [
                "from" => $this->range2InitialFloat,
                "to" => $this->range2FinalFloat
            ];
            $floatValue["arg3"] = [
                "from" => $this->range3InitialFloat,
                "to" => $this->range3FinalFloat
            ];
            $values = [
                "original" => $originalValue,
                "float" => $floatValue
            ];
            
            $this->values = $values;
        }
    }

    /**
     * @PreSerialize
     */
    private function onPreSerialize()
    {
        $this->kibanaName = $this->__toString();
        $this->defaultTitle = $this->getStringValues();
        $this->setValues();
        $this->kibanaId = \TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools::generateKibanaId($this);
    }

    public function __toString()
    {
        if ($this->parameterFormat){
            $tableType = $this->parameterFormat->getTableType()->__toString();
            return $tableType.": ".$this->parameterFormat->toPublicString();
        } else {
            return "Unknown parameter";
        }
    }

    public function getStringValues()
    {
        $paramFormat = ucfirst($this->getParameterFormat()->toPublicString());
        $arg1min = $this->range1InitialFloat ? $this->range1InitialFloat : "null";
        $arg1max = $this->range1FinalFloat ? $this->range1FinalFloat : "null";

        $arg2min = $this->range2InitialFloat ? $this->range2InitialFloat : "null";
        $arg2max = $this->range2FinalFloat ? $this->range2FinalFloat : "null";

        $arg1 = $this->range1InitialFloat || $this->range1FinalFloat ? "$arg1min to $arg1max (arg.1) | ": "";
        $arg2 = $this->range2InitialFloat || $this->range2FinalFloat ? "$arg2min to $arg2max (arg.2) | ": "";
        $entry = $this->valueFloat ? "$this->valueFloat (entry)" : "";

        /*$values = "$arg1$arg2$entry" ? "$arg1$arg2$entry" : "<span class='noInfo'>No value provided</span>";
        return "$paramFormat: $values";*/

        return "$arg1$arg2$entry" ? "$paramFormat: $arg1$arg2$entry" : null;
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
     * Set valueFloat
     *
     * @param string $valueFloat
     *
     * @return ParameterValue
     */
    public function setValueFloat($valueFloat)
    {
        $this->valueFloat = $valueFloat;

        return $this;
    }

    /**
     * Get valueFloat
     *
     * @return string
     */
    public function getValueFloat()
    {
        return $this->valueFloat;
    }

    /**
     * Set valueOriginalBase
     *
     * @param string $valueOriginalBase
     *
     * @return ParameterValue
     */
    public function setValueOriginalBase($valueOriginalBase)
    {
        $this->valueOriginalBase = $valueOriginalBase;

        return $this;
    }

    /**
     * Get valueOriginalBase
     *
     * @return string
     */
    public function getValueOriginalBase()
    {
        return $this->valueOriginalBase;
    }

    /**
     * Set valueIsIntSexa
     *
     * @param boolean $valueIsIntSexa
     *
     * @return ParameterValue
     */
    public function setValueIsIntSexa($valueIsIntSexa)
    {
        $this->valueIsIntSexa = $valueIsIntSexa;

        return $this;
    }

    /**
     * Get valueIsIntSexa
     *
     * @return bool
     */
    public function getValueIsIntSexa()
    {
        return $this->valueIsIntSexa;
    }

    /**
     * Set range1InitialFloat
     *
     * @param string $range1InitialFloat
     *
     * @return ParameterValue
     */
    public function setRange1InitialFloat($range1InitialFloat)
    {
        $this->range1InitialFloat = $range1InitialFloat;

        return $this;
    }

    /**
     * Get range1InitialFloat
     *
     * @return string
     */
    public function getRange1InitialFloat()
    {
        return $this->range1InitialFloat;
    }

    /**
     * Set range1InitialOriginalBase
     *
     * @param string $range1InitialOriginalBase
     *
     * @return ParameterValue
     */
    public function setRange1InitialOriginalBase($range1InitialOriginalBase)
    {
        $this->range1InitialOriginalBase = $range1InitialOriginalBase;

        return $this;
    }

    /**
     * Get range1InitialOriginalBase
     *
     * @return string
     */
    public function getRange1InitialOriginalBase()
    {
        return $this->range1InitialOriginalBase;
    }

    /**
     * Set range1FinalFloat
     *
     * @param string $range1FinalFloat
     *
     * @return ParameterValue
     */
    public function setRange1FinalFloat($range1FinalFloat)
    {
        $this->range1FinalFloat = $range1FinalFloat;

        return $this;
    }

    /**
     * Get range1FinalFloat
     *
     * @return string
     */
    public function getRange1FinalFloat()
    {
        return $this->range1FinalFloat;
    }

    /**
     * Set range1FinalOriginalBase
     *
     * @param string $range1FinalOriginalBase
     *
     * @return ParameterValue
     */
    public function setRange1FinalOriginalBase($range1FinalOriginalBase)
    {
        $this->range1FinalOriginalBase = $range1FinalOriginalBase;

        return $this;
    }

    /**
     * Get range1FinalOriginalBase
     *
     * @return string
     */
    public function getRange1FinalOriginalBase()
    {
        return $this->range1FinalOriginalBase;
    }

    /**
     * Set range2InitialFloat
     *
     * @param string $range2InitialFloat
     *
     * @return ParameterValue
     */
    public function setRange2InitialFloat($range2InitialFloat)
    {
        $this->range2InitialFloat = $range2InitialFloat;

        return $this;
    }

    /**
     * Get range2InitialFloat
     *
     * @return string
     */
    public function getRange2InitialFloat()
    {
        return $this->range2InitialFloat;
    }

    /**
     * Set range2InitialOriginalBase
     *
     * @param string $range2InitialOriginalBase
     *
     * @return ParameterValue
     */
    public function setRange2InitialOriginalBase($range2InitialOriginalBase)
    {
        $this->range2InitialOriginalBase = $range2InitialOriginalBase;

        return $this;
    }

    /**
     * Get range2InitialOriginalBase
     *
     * @return string
     */
    public function getRange2InitialOriginalBase()
    {
        return $this->range2InitialOriginalBase;
    }

    /**
     * Set range2FinalFloat
     *
     * @param string $range2FinalFloat
     *
     * @return ParameterValue
     */
    public function setRange2FinalFloat($range2FinalFloat)
    {
        $this->range2FinalFloat = $range2FinalFloat;

        return $this;
    }

    /**
     * Get range2FinalFloat
     *
     * @return string
     */
    public function getRange2FinalFloat()
    {
        return $this->range2FinalFloat;
    }

    /**
     * Set range2FinalOriginalBase
     *
     * @param string $range2FinalOriginalBase
     *
     * @return ParameterValue
     */
    public function setRange2FinalOriginalBase($range2FinalOriginalBase)
    {
        $this->range2FinalOriginalBase = $range2FinalOriginalBase;

        return $this;
    }

    /**
     * Get range2FinalOriginalBase
     *
     * @return string
     */
    public function getRange2FinalOriginalBase()
    {
        return $this->range2FinalOriginalBase;
    }

    /**
     * Set range3InitialFloat
     *
     * @param string $range3InitialFloat
     *
     * @return ParameterValue
     */
    public function setRange3InitialFloat($range3InitialFloat)
    {
        $this->range3InitialFloat = $range3InitialFloat;

        return $this;
    }

    /**
     * Get range3InitialFloat
     *
     * @return string
     */
    public function getRange3InitialFloat()
    {
        return $this->range3InitialFloat;
    }

    /**
     * Set range3InitialOriginalBase
     *
     * @param string $range3InitialOriginalBase
     *
     * @return ParameterValue
     */
    public function setRange3InitialOriginalBase($range3InitialOriginalBase)
    {
        $this->range3InitialOriginalBase = $range3InitialOriginalBase;

        return $this;
    }

    /**
     * Get range3InitialOriginalBase
     *
     * @return string
     */
    public function getRange3InitialOriginalBase()
    {
        return $this->range3InitialOriginalBase;
    }

    /**
     * Set range3FinalFloat
     *
     * @param string $range3FinalFloat
     *
     * @return ParameterValue
     */
    public function setRange3FinalFloat($range3FinalFloat)
    {
        $this->range3FinalFloat = $range3FinalFloat;

        return $this;
    }

    /**
     * Get range3FinalFloat
     *
     * @return string
     */
    public function getRange3FinalFloat()
    {
        return $this->range3FinalFloat;
    }

    /**
     * Set range3FinalOriginalBase
     *
     * @param string $range3FinalOriginalBase
     *
     * @return ParameterValue
     */
    public function setRange3FinalOriginalBase($range3FinalOriginalBase)
    {
        $this->range3FinalOriginalBase = $range3FinalOriginalBase;

        return $this;
    }

    /**
     * Get range3FinalOriginalBase
     *
     * @return string
     */
    public function getRange3FinalOriginalBase()
    {
        return $this->range3FinalOriginalBase;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return ParameterValue
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
     * @return ParameterValue
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
     * @return ParameterValue
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
     * @return ParameterValue
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
     * Set parameterFormat
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterFormat $parameterFormat
     *
     * @return ParameterValue
     */
    public function setParameterFormat(\TAMAS\AstroBundle\Entity\ParameterFormat $parameterFormat = null)
    {
        $this->parameterFormat = $parameterFormat;

        return $this;
    }

    /**
     * Get parameterFormat
     *
     * @return \TAMAS\AstroBundle\Entity\ParameterFormat
     */
    public function getParameterFormat()
    {
        return $this->parameterFormat;
    }

    /**
     * Set parameterSet
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterSet $parameterSet
     *
     * @return ParameterValue
     */
    public function setParameterSet(\TAMAS\AstroBundle\Entity\ParameterSet $parameterSet = null)
    {
        // call parameterSet
        if ($this->parameterSet !== null) {
            $this->parameterSet->removeParameterValue($this);
        }
        // We comment this section as we want to be able to add new parameters values from parameters set. Keeping it would result to an infinit loop __________//
        // if($parameterSet !== null){
        // $parameterSet->addParameterValue($this);
        // }
        $this->parameterSet = $parameterSet;

        return $this;
    }

    /**
     *
     * @Groups({"parametervalue"})
     * Get parameterSet
     *
     * @return \TAMAS\AstroBundle\Entity\ParameterSet
     */
    public function getParameterSet()
    {
        return $this->parameterSet;
    }

    public function __construct()
    {
        $this->valueIsIntSexa = true;
    }

    // ____________________________ This method is added to basic entity in order to be able to check in controller if form-submitted entities have only null attributes
    public function checkIfNull()
    {
        foreach ($this as $key => $value) {
            if (($value != null) && ! ($key == "valueIsIntSexa" || $key == "parameterFormat" || $key == "parameterSet" || $key == "created" || $key == "updated" || $key == "createdBy" || $key == "updatedBy" || $key == "id")) { // if at least one value is not null (expt the foreign key and the checkbox)
                return false; // false : not null
            }
        }
        return true;
    }

    /**
     * Set typeOfNumber
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumber
     *
     * @return ParameterValue
     */
    public function setTypeOfNumber(\TAMAS\AstroBundle\Entity\TypeOfNumber $typeOfNumber)
    {
        $this->typeOfNumber = $typeOfNumber;

        return $this;
    }

    /**
     * Get typeOfNumber
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getTypeOfNumber()
    {
        return $this->typeOfNumber;
    }

    /**
     * Returns a string that can be use as metadata description of a parameter set
     * in the front office
     * @return string
     */
    public function toPublicString()
    {
        $originalValue = $this->valueOriginalBase ? "<span class='mainContent'>$this->valueOriginalBase</span>" : "<span class='noInfo'>No original base value</span>";
        $floatValue = $this->valueFloat ? "<br/>Decimal: $this->valueFloat": "";

        $range1 = "";
        if ($this->getRange1InitialOriginalBase() && $this->getRange1FinalOriginalBase()){
            $range1 = "<br/>Low bound: $this->range1InitialOriginalBase | High bound: $this->range1FinalOriginalBase";
        }
        $range2 = "";
        if ($this->getRange2InitialOriginalBase() && $this->getRange2FinalOriginalBase()){
            $range2 = "<br/>Low bound: $this->range2InitialOriginalBase | High bound: $this->range1FinalOriginalBase";
        }

        return "$originalValue$floatValue$range1$range2";

    }

    public function toPublicTitle()
    {
        $originalValue = $this->valueOriginalBase ? "<span class='mainContent'>$this->valueOriginalBase</span>" : "<span class='noInfo'>No original base value</span>";
        $floatValue = $this->valueFloat ? "<br/><i>decimal</i> : $this->valueFloat": "";

        return "$originalValue$floatValue";

    }
}
