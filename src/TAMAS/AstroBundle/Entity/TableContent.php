<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Error;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use Symfony\Component\Debug\Exception\ContextErrorException;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use \TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;


/**
 * TableContent
 *
 * @ORM\Table(name="table_content")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\TableContentRepository")
 */
class TableContent
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    // * @UniqueEntity(fields={"valueOriginal", "argument1Name", "argument2Name", "argument3Name"}, ignoreNull = false, message="This source is already recorded in the database", errorPath="false")
    // This line should be added in the class commentary block once "valueOriginal" is actually implemented

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="argument1_number_of_steps", type="integer", nullable=true)
     */
    private $argument1NumberOfSteps;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="argument2_number_of_steps", type="integer", nullable=true)
     */
    private $argument2NumberOfSteps;

    /**
     *
     * @var int
     *
     * @ORM\Column(name="argument3_number_of_steps", type="integer", nullable=true)
     */
    private $argument3NumberOfSteps;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Groups({"tableContentMain"})
     *
     * @var array
     *
     * @ORM\Column(name="value_original", type="json_array", nullable=true)
     */
    private $valueOriginal;

    /**
     * @Groups({"tableContentMain"})
     *
     * @var array
     *
     * @ORM\Column(name="value_float", type="json_array", nullable=true)
     */
    private $valueFloat;

    /**
     * @Groups({"tableContentMain"})
     *
     * @var array
     *
     * @ORM\Column(name="corrected_value_float", type="json_array", nullable=true)
     */
    private $correctedValueFloat;

    /**
     *
     * @var bool
     *
     * @ORM\Column(name="has_difference_table", type="boolean", nullable=true)
     */
    private $hasDifferenceTable;

    /**
     *
     * @var array
     *
     * @ORM\Column(name="difference_value_original", type="json_array", nullable=true)
     */
    private $differenceValueOriginal;

    /**
     *
     * @var array
     *
     * @ORM\Column(name="difference_value_float", type="json_array", nullable=true)
     */
    private $differenceValueFloat;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     * 
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $entryTypeOfNumber;

    /**
     * @Groups({"tableContentMain" })
     * @Type("string")
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\NumberUnit", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $entryNumberUnit;

    /**
     * @Groups({"tableContentMain" })
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="entry_significant_fractional_place", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/", message="The input should be an integer.")
     */
    private $entrySignificantFractionalPlace;

    /**
     * @Groups({"tableContentMain" })
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="entry_number_of_cell", type="integer", nullable=true)
     */
    private $entryNumberOfCell;

    /**
     * @Type("string")
     *
     * @var int
     * @ORM\Column(name="arg_number", type="integer", nullable=true)
     */
    private $argNumber;

    /**
     * @Groups({"tableContentMain" })
     * 
     * @var string
     * @ORM\Column(name="argument1_name", type="string", length=255, nullable=true)
     */
    private $argument1Name;

    /**
     * @Groups({"tableContentMain" })
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $argument1TypeOfNumber;

    /**
     * @Groups({"tableContentMain" })
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\NumberUnit", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $argument1NumberUnit;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="argument1_number_of_cell", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/", message="The input should be an integer.")
     */
    private $argument1NumberOfCell;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="argument1_significant_fractional_place", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/", message="The input should be an integer.")
     */
    private $argument1SignificantFractionalPlace;

    /**
     * @Groups({"tableContentMain" })
     *
     * @var string
     *
     * @ORM\Column(name="argument2_name", type="string", length=255, nullable=true)
     */
    private $argument2Name;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $argument2TypeOfNumber;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\NumberUnit", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $argument2NumberUnit;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="argument2_number_of_cell", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/", message="The input should be an integer.")
     */
    private $argument2NumberOfCell;

    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="argument2_significant_fractional_place", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/", message="The input should be an integer.")
     */
    private $argument2SignificantFractionalPlace;

    /**
     * @var string
     *
     * @ORM\Column(name="argument3_name", type="string", length=255, nullable=true)
     */
    private $argument3Name;

    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $argument3TypeOfNumber;

    /**
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\NumberUnit", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $argument3NumberUnit;

    /**
     * @var int
     *
     * @ORM\Column(name="argument3_number_of_cell", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/", message="The input should be an integer.")
     */
    private $argument3NumberOfCell;

    /**
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="argument3_significant_fractional_place", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/", message="The input should be an integer.")
     */
    private $argument3SignificantFractionalPlace;

    /**
     * @Groups({"tableContentMain"})
     *
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @Groups({"externalTableTypeTC" })
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TableType", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $tableType;

    /**
     * //this is the suggested formulaDefinition, not necessary the one picked by the admin user
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\FormulaDefinition", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $formulaDefinition;

    /**
     * @Groups({"tableContentMain" })
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\MathematicalParameter", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $mathematicalParameter;

    /**
     * @Groups({"externalParameterSet"})
     *
     * @ORM\ManyToMany(targetEntity="TAMAS\AstroBundle\Entity\ParameterSet", inversedBy="tableContents", cascade={"persist"})
     */
    private $parameterSets;

    /**
     * @Groups({"editedTextMain", "tableContentLimited"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\EditedText", inversedBy="tableContents")
     *
     */
    private $editedText;

    /**
     * @Groups({"tableContentMain"})
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Groups({"tableContentMain"})
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
     *
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @Groups({"tableContentMain" })
     *
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean", nullable=true)
     */
    private $public;


    /**
     * Some tableContent are type "meanMotion" ; they require specific metadata.
     * 
     * @Groups({"tableContentMain" })
     * 
     * @ORM\OneToOne(targetEntity="TAMAS\AstroBundle\Entity\MeanMotion",  cascade={"persist", "remove"})
     * @ORM\JoinColumn(referencedColumnName="id")
     * @Assert\Valid()
     */
    private $meanMotion;


    /**
     * @Groups({"tableContentMain", "externalTableContent", "tableContentLimited"})
     * @var string
     */
    private $kibanaName;


    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;


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
     * Set valueOriginal
     *
     * @param array $valueOriginal
     *
     * @return TableContent
     */
    public function setValueOriginal($valueOriginal)
    {
        if (is_string($valueOriginal)) {
            $valueOriginal = json_decode($valueOriginal, 1);
        }
        $this->valueOriginal = $valueOriginal;

        return $this;
    }

    /**
     * Get valueOriginal
     *
     * @return array
     */
    public function getValueOriginal()
    {
        return $this->valueOriginal;
    }

    /**
     * Set valueFloat
     *
     * @param array $valueFloat
     *
     * @return TableContent
     */
    public function setValueFloat($valueFloat)
    {
        if (is_string($valueFloat)) {
            $valueFloat = json_decode($valueFloat, 1);
        }
        $this->valueFloat = $valueFloat;

        return $this;
    }

    /**
     * Get valueFloat
     *
     * @return array
     */
    public function getValueFloat()
    {
        return $this->valueFloat;
    }

    /**
     * Set argument1Name
     *
     * @param string $argument1Name
     *
     * @return TableContent
     */
    public function setArgument1Name($argument1Name)
    {
        $this->argument1Name = $argument1Name;

        return $this;
    }

    /**
     * Get argument1Name
     *
     * @return string
     */
    public function getArgument1Name()
    {
        return $this->argument1Name;
    }

    /**
     * Set argument1NumberOfCell
     *
     * @param integer $argument1NumberOfCell
     *
     * @return TableContent
     */
    public function setArgument1NumberOfCell($argument1NumberOfCell)
    {
        $this->argument1NumberOfCell = $argument1NumberOfCell;

        return $this;
    }

    /**
     * Get argument1NumberOfCell
     *
     * @return int
     */
    public function getArgument1NumberOfCell()
    {
        return $this->argument1NumberOfCell;
    }

    /**
     * Set argument2Name
     *
     * @param string $argument2Name
     *
     * @return TableContent
     */
    public function setArgument2Name($argument2Name)
    {
        $this->argument2Name = $argument2Name;

        return $this;
    }

    /**
     * Get argument2Name
     *
     * @return string
     */
    public function getArgument2Name()
    {
        return $this->argument2Name;
    }

    /**
     * Set argument2NumberOfCell
     *
     * @param integer $argument2NumberOfCell
     *
     * @return TableContent
     */
    public function setArgument2NumberOfCell($argument2NumberOfCell)
    {
        $this->argument2NumberOfCell = $argument2NumberOfCell;

        return $this;
    }

    /**
     * Get argument2NumberOfCell
     *
     * @return int
     */
    public function getArgument2NumberOfCell()
    {
        return $this->argument2NumberOfCell;
    }

    /**
     * Set argument3Name
     *
     * @param string $argument3Name
     *
     * @return TableContent
     */
    public function setArgument3Name($argument3Name)
    {
        $this->argument3Name = $argument3Name;

        return $this;
    }

    /**
     * Get argument3Name
     *
     * @return string
     */
    public function getArgument3Name()
    {
        return $this->argument3Name;
    }

    /**
     * Set argument3NumberOfCell
     *
     * @param string $argument3NumberOfCell
     *
     * @return TableContent
     */
    public function setArgument3NumberOfCell($argument3NumberOfCell)
    {
        $this->argument3NumberOfCell = $argument3NumberOfCell;

        return $this;
    }

    /**
     * Get argument3NumberOfCell
     *
     * @return string
     */
    public function getArgument3NumberOfCell()
    {
        return $this->argument3NumberOfCell;
    }

    /**
     * Set entryTypeOfNumber
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $entryTypeOfNumber
     *
     * @return TableContent
     */
    public function setEntryTypeOfNumber(\TAMAS\AstroBundle\Entity\TypeOfNumber $entryTypeOfNumber = null)
    {
        $this->entryTypeOfNumber = $entryTypeOfNumber;

        return $this;
    }

    /**
     * Get entryTypeOfNumber
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getEntryTypeOfNumber()
    {
        return $this->entryTypeOfNumber;
    }

    /**
     * Set argument1TypeOfNumber
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $argument1TypeOfNumber
     *
     * @return TableContent
     */
    public function setArgument1TypeOfNumber(\TAMAS\AstroBundle\Entity\TypeOfNumber $argument1TypeOfNumber = null)
    {
        $this->argument1TypeOfNumber = $argument1TypeOfNumber;

        return $this;
    }

    /**
     * Get argument1TypeOfNumber
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getArgument1TypeOfNumber()
    {
        return $this->argument1TypeOfNumber;
    }

    /**
     * Set argument2TypeOfNumber
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $argument2TypeOfNumber
     *
     * @return TableContent
     */
    public function setArgument2TypeOfNumber(\TAMAS\AstroBundle\Entity\TypeOfNumber $argument2TypeOfNumber = null)
    {
        $this->argument2TypeOfNumber = $argument2TypeOfNumber;

        return $this;
    }

    /**
     * Get argument2TypeOfNumber
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getArgument2TypeOfNumber()
    {
        return $this->argument2TypeOfNumber;
    }

    /**
     * Set argument3TypeOfNumber
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $argument3TypeOfNumber
     *
     * @return TableContent
     */
    public function setArgument3TypeOfNumber(\TAMAS\AstroBundle\Entity\TypeOfNumber $argument3TypeOfNumber = null)
    {
        $this->argument3TypeOfNumber = $argument3TypeOfNumber;

        return $this;
    }

    /**
     * Get argument3TypeOfNumber
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getArgument3TypeOfNumber()
    {
        return $this->argument3TypeOfNumber;
    }

    /**
     * Set tableType
     *
     * @param \TAMAS\AstroBundle\Entity\TableType $tableType
     *
     * @return TableContent
     */
    public function setTableType(\TAMAS\AstroBundle\Entity\TableType $tableType)
    {
        $this->tableType = $tableType;

        return $this;
    }

    /**
     * Get tableType
     *
     * @return \TAMAS\AstroBundle\Entity\TableType
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameterSets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->editedTexts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parameterSet
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterSet $parameterSet
     *
     * @return TableContent
     */
    public function addParameterSet(\TAMAS\AstroBundle\Entity\ParameterSet $parameterSet)
    {
        if (!$this->parameterSets->contains($parameterSet)) {
            $this->parameterSets[] = $parameterSet;
            $parameterSet->addTableContent($this);
        }
        return $this;
    }

    /**
     * Remove parameterSet
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterSet $parameterSet
     */
    public function removeParameterSet(\TAMAS\AstroBundle\Entity\ParameterSet $parameterSet)
    {
        if ($this->parameterSets->contains($parameterSet)) {
            $this->parameterSets->removeElement($parameterSet);
            $parameterSet->removeTableContent($this);
        }
    }

    /**
     * Get parameterSets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameterSets()
    {
        return $this->parameterSets;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return TableContent
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
     * @return TableContent
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
     * @return TableContent
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
     * @return TableContent
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
     * Set mathematicalParameter
     *
     * @param \TAMAS\AstroBundle\Entity\MathematicalParameter $mathematicalParameter
     *
     * @return TableContent
     */
    public function setMathematicalParameter(\TAMAS\AstroBundle\Entity\MathematicalParameter $mathematicalParameter = null)
    {
        $this->mathematicalParameter = $mathematicalParameter;

        return $this;
    }

    /**
     * Get mathematicalParameter
     *
     * @return \TAMAS\AstroBundle\Entity\MathematicalParameter
     */
    public function getMathematicalParameter()
    {
        return $this->mathematicalParameter;
    }

    /**
     * Set entrySignificantFractionalPlace
     *
     * @param integer $entrySignificantFractionalPlace
     *
     * @return TableContent
     */
    public function setEntrySignificantFractionalPlace($entrySignificantFractionalPlace)
    {
        $this->entrySignificantFractionalPlace = $entrySignificantFractionalPlace;

        return $this;
    }

    /**
     * Get entrySignificantFractionalPlace
     *
     * @return integer
     */
    public function getEntrySignificantFractionalPlace()
    {
        return $this->entrySignificantFractionalPlace;
    }

    /**
     * Set entryNumberOfCell
     *
     * @param integer $entryNumberOfCell
     *
     * @return TableContent
     */
    public function setEntryNumberOfCell($entryNumberOfCell)
    {
        $this->entryNumberOfCell = $entryNumberOfCell;

        return $this;
    }

    /**
     * Get entryNumberOfCell
     *
     * @return integer
     */
    public function getEntryNumberOfCell()
    {
        return $this->entryNumberOfCell;
    }

    /**
     * Set argument1SignificantFractionalPlace
     *
     * @param integer $argument1SignificantFractionalPlace
     *
     * @return TableContent
     */
    public function setArgument1SignificantFractionalPlace($argument1SignificantFractionalPlace)
    {
        $this->argument1SignificantFractionalPlace = $argument1SignificantFractionalPlace;

        return $this;
    }

    /**
     * Get argument1SignificantFractionalPlace
     *
     * @return integer
     */
    public function getArgument1SignificantFractionalPlace()
    {
        return $this->argument1SignificantFractionalPlace;
    }

    /**
     * Set argument2SignificantFractionalPlace
     *
     * @param integer $argument2SignificantFractionalPlace
     *
     * @return TableContent
     */
    public function setArgument2SignificantFractionalPlace($argument2SignificantFractionalPlace)
    {
        $this->argument2SignificantFractionalPlace = $argument2SignificantFractionalPlace;

        return $this;
    }

    /**
     * Get argument2SignificantFractionalPlace
     *
     * @return integer
     */
    public function getArgument2SignificantFractionalPlace()
    {
        return $this->argument2SignificantFractionalPlace;
    }

    /**
     * Set argument3SignificantFractionalPlace
     *
     * @param integer $argument3SignificantFractionalPlace
     *
     * @return TableContent
     */
    public function setArgument3SignificantFractionalPlace($argument3SignificantFractionalPlace)
    {
        $this->argument3SignificantFractionalPlace = $argument3SignificantFractionalPlace;

        return $this;
    }

    /**
     * Get argument3SignificantFractionalPlace
     *
     * @return integer
     */
    public function getArgument3SignificantFractionalPlace()
    {
        return $this->argument3SignificantFractionalPlace;
    }

    /**
     * Set entryNumberUnit
     *
     * @param \TAMAS\AstroBundle\Entity\NumberUnit $entryNumberUnit
     *
     * @return TableContent
     */
    public function setEntryNumberUnit(\TAMAS\AstroBundle\Entity\NumberUnit $entryNumberUnit = null)
    {
        $this->entryNumberUnit = $entryNumberUnit;

        return $this;
    }

    /**
     * Get entryNumberUnit
     *
     * @return \TAMAS\AstroBundle\Entity\NumberUnit
     */
    public function getEntryNumberUnit()
    {
        return $this->entryNumberUnit;
    }

    /**
     * Set argument1NumberUnit
     *
     * @param \TAMAS\AstroBundle\Entity\NumberUnit $argument1NumberUnit
     *
     * @return TableContent
     */
    public function setArgument1NumberUnit(\TAMAS\AstroBundle\Entity\NumberUnit $argument1NumberUnit = null)
    {
        $this->argument1NumberUnit = $argument1NumberUnit;

        return $this;
    }

    /**
     * Get argument1NumberUnit
     *
     * @return NumberUnit
     */
    public function getArgument1NumberUnit()
    {
        return $this->argument1NumberUnit;
    }

    /**
     * Set argument2NumberUnit
     *
     * @param NumberUnit $argument2NumberUnit
     * @return TableContent
     */
    public function setArgument2NumberUnit(\TAMAS\AstroBundle\Entity\NumberUnit $argument2NumberUnit = null)
    {
        $this->argument2NumberUnit = $argument2NumberUnit;

        return $this;
    }

    /**
     * Get argument2NumberUnit
     *
     * @return \TAMAS\AstroBundle\Entity\NumberUnit
     */
    public function getArgument2NumberUnit()
    {
        return $this->argument2NumberUnit;
    }

    /**
     * Set argument3NumberUnit
     *
     * @param \TAMAS\AstroBundle\Entity\NumberUnit $argument3NumberUnit
     *
     * @return TableContent
     */
    public function setArgument3NumberUnit(\TAMAS\AstroBundle\Entity\NumberUnit $argument3NumberUnit = null)
    {
        $this->argument3NumberUnit = $argument3NumberUnit;

        return $this;
    }

    /**
     * Get argument3NumberUnit
     *
     * @return \TAMAS\AstroBundle\Entity\NumberUnit
     */
    public function getArgument3NumberUnit()
    {
        return $this->argument3NumberUnit;
    }

    /**
     * Add editedText
     *
     * @param \TAMAS\AstroBundle\Entity\EditedText $editedText
     *
     * @return TableContent
     */
    public function setEditedText(\TAMAS\AstroBundle\Entity\EditedText $editedText = null)
    {
        $this->editedText = $editedText;

        return $this;
    }

    /**
     * Get editedTexts
     *
     * @return \TAMAS\AstroBundle\Entity\EditedText
     */
    public function getEditedText()
    {
        return $this->editedText;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return TableContent
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }



    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return TableContent
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    // ____________________________ This method is added to basic entity in order to be able to check in controller if form-submitted entities have only null attributes.
    // This method is used in the context or search originalText. This is still a draft method.
    // see draft section of originalTextRepository
    // public function checkIfNull() {
    // //special case : we have to check also array collection. It must be turned into an array. We can't access it through the foreach loop, as it is seen as an object.
    // if (!empty($this->parameterSets->toArray())) {
    // return false;
    // }
    // foreach ($this as $value) {
    // if ($value != null && !is_object($value)) {
    // return false;
    // }
    // }
    // return true;
    // }

    /**
     * Set argument1NumberOfSteps
     *
     * @param integer $argument1NumberOfSteps
     *
     * @return TableContent
     */
    public function setArgument1NumberOfSteps($argument1NumberOfSteps)
    {
        $this->argument1NumberOfSteps = $argument1NumberOfSteps;

        return $this;
    }

    /**
     * Get argument1NumberOfSteps
     *
     * @return integer
     */
    public function getArgument1NumberOfSteps()
    {
        return $this->argument1NumberOfSteps;
    }

    /**
     * Set argument2NumberOfSteps
     *
     * @param integer $argument2NumberOfSteps
     *
     * @return TableContent
     */
    public function setArgument2NumberOfSteps($argument2NumberOfSteps)
    {
        $this->argument2NumberOfSteps = $argument2NumberOfSteps;

        return $this;
    }

    /**
     * Get argument2NumberOfSteps
     *
     * @return integer
     */
    public function getArgument2NumberOfSteps()
    {
        return $this->argument2NumberOfSteps;
    }

    /**
     * Set argument3NumberOfSteps
     *
     * @param integer $argument3NumberOfSteps
     *
     * @return TableContent
     */
    public function setArgument3NumberOfSteps($argument3NumberOfSteps)
    {
        $this->argument3NumberOfSteps = $argument3NumberOfSteps;

        return $this;
    }

    /**
     * Get argument3NumberOfSteps
     *
     * @return integer
     */
    public function getArgument3NumberOfSteps()
    {
        return $this->argument3NumberOfSteps;
    }

    /**
     * Set argNumber
     *
     * @param integer $argNumber
     *
     * @return TableContent
     */
    public function setArgNumber($argNumber)
    {
        $this->argNumber = $argNumber;

        return $this;
    }

    /**
     * Get argNumber
     *
     * @return integer
     */
    public function getArgNumber()
    {
        return $this->argNumber;
    }

    /**
     * Set hasDifferenceTable.
     *
     * @param bool|null $hasDifferenceTable
     *
     * @return TableContent
     */
    public function setHasDifferenceTable($hasDifferenceTable = null)
    {
        $this->hasDifferenceTable = $hasDifferenceTable;

        return $this;
    }

    /**
     * Get hasDifferenceTable.
     *
     * @return bool|null
     */
    public function getHasDifferenceTable()
    {
        return $this->hasDifferenceTable;
    }

    /**
     * Set differenceValueOriginal.
     *
     * @param array|null $differenceValueOriginal
     *
     * @return TableContent
     */
    public function setDifferenceValueOriginal($differenceValueOriginal = null)
    {
        if (is_string($differenceValueOriginal)) {
            $differenceValueOriginal = json_decode($differenceValueOriginal, 1);
        }
        $this->differenceValueOriginal = $differenceValueOriginal;

        return $this;
    }

    /**
     * Get differenceValueOriginal.
     *
     * @return array|null
     */
    public function getDifferenceValueOriginal()
    {
        return $this->differenceValueOriginal;
    }

    /**
     * Set differenceValueFloat.
     *
     * @param array|null $differenceValueFloat
     *
     * @return TableContent
     */
    public function setDifferenceValueFloat($differenceValueFloat = null)
    {
        if (is_string($differenceValueFloat)) {
            $differenceValueFloat = json_decode($differenceValueFloat, 1);
        }
        $this->differenceValueFloat = $differenceValueFloat;

        return $this;
    }

    /**
     * Get differenceValueFloat.
     *
     * @return array|null
     */
    public function getDifferenceValueFloat()
    {
        return $this->differenceValueFloat;
    }

    /**
     * Set correctedValueFloat.
     *
     * @param array|null $correctedValueFloat
     *
     * @return TableContent
     */
    public function setCorrectedValueFloat($correctedValueFloat = null)
    {
        if (is_string($correctedValueFloat)) {
            $correctedValueFloat = json_decode($correctedValueFloat, 1);
        }
        $this->correctedValueFloat = $correctedValueFloat;

        return $this;
    }

    /**
     * Get correctedValueFloat.
     *
     * @return array|null
     */
    public function getCorrectedValueFloat()
    {
        return $this->correctedValueFloat;
    }

    /**
     * Set formulaDefinition.
     *
     * @param \TAMAS\AstroBundle\Entity\FormulaDefinition|null $formulaDefinition
     *
     * @return TableContent
     */
    public function setFormulaDefinition(\TAMAS\AstroBundle\Entity\FormulaDefinition $formulaDefinition = null)
    {
        $this->formulaDefinition = $formulaDefinition;
        return $this;
    }

    /**
     * Get formulaDefinition.
     *
     * @return \TAMAS\AstroBundle\Entity\FormulaDefinition|null
     */
    public function getFormulaDefinition()
    {
        return $this->formulaDefinition;
    }


    /**
     * Set meanMotion.
     *
     * @param \TAMAS\AstroBundle\Entity\MeanMotion|null $meanMotion
     *
     * @return TableContent
     */
    public function setMeanMotion(\TAMAS\AstroBundle\Entity\MeanMotion $meanMotion = null)
    {
        $this->meanMotion = $meanMotion;

        return $this;
    }

    /**
     * Get meanMotion.
     *
     * @return \TAMAS\AstroBundle\Entity\MeanMotion|null
     */
    public function getMeanMotion()
    {
        return $this->meanMotion;
    }

    /* ====================================================== transformation and validation tools ==================================== */




    /**
     * Generate and return the JSON object corresponding to the template of the table
     *
     * @return {Json} Template of the table (as a JSON object)
     */
    public function getTemplate()
    {
        $res = [];
        $res['table_type'] = (string) $this->getTableType()->getId();
        $res['readonly'] = false;
        $res['mean_motion'] = $this->getMeanMotion() ? true : false;

        $args = [];

        if ($this->getArgNumber() > 0) {
            $arg1 = [];
            $arg1['name'] = $this->getArgument1Name();
            $arg1['type'] = $this->getArgument1TypeOfNumber() ? $this->getArgument1TypeOfNumber()->getCodeName() : "Unknown code name";
            $arg1['unit'] = $this->getArgument1NumberUnit() ? $this->getArgument1NumberUnit()->getId() : null;
            $arg1['unitName'] = $this->getArgument1NumberUnit() ? $this->getArgument1NumberUnit()->__toString() : null;
            $arg1['nsteps'] = $this->getArgument1NumberOfSteps();
            $precision = $this->getArgument1SignificantFractionalPlace();
            $arg1['ncells'] = $this->getArgument1NumberOfCell() + $precision;
            $arg1['decpos'] = $arg1['ncells'] - $precision;
            $arg1['subUnit'] = null;
            $arg1['firstMonth'] = null;
            if ($this->getMeanMotion()) {
                if ($this->getMeanMotion()->getSubTimeUnit()) {
                    $arg1['subUnit'] = $this->getMeanMotion()->getSubTimeUnit()->getId();
                    $arg1['subUnitName'] = $this->getMeanMotion()->getSubTimeUnit()->__toString();
                }
                if ($this->getMeanMotion()->getFirstMonth()) {
                    $arg1['firstMonth'] = $this->getMeanMotion()->getFirstMonth();
                }
            }
            $args[] = $arg1;
        }
        if ($this->getArgNumber() > 1) {
            $arg2 = [];
            $arg2['name'] = $this->getArgument2Name();
            $arg2['type'] = $this->getArgument2TypeOfNumber() ? $this->getArgument2TypeOfNumber()->getCodeName() : "Unknown code name";
            $arg2['unit'] = $this->getArgument2NumberUnit() ? $this->getArgument2NumberUnit()->getId() : null;
            $arg2['nsteps'] = $this->getArgument2NumberOfSteps();
            $precision = $this->getArgument2SignificantFractionalPlace();
            $arg2['ncells'] = $this->getArgument2NumberOfCell() + $precision;
            $arg2['decpos'] = $arg2['ncells'] - $precision;
            $args[] = $arg2;
        }

        $res['args'] = $args;

        $entries = [];

        $entry = [];
        $entry['name'] = 'Entry';
        $entry['type'] = $this->getEntryTypeOfNumber() ? $this->getEntryTypeOfNumber()->getCodeName() : " Unknown code name";
        $precision = $this->getEntrySignificantFractionalPlace();
        $entry['ncells'] = $this->getEntryNumberOfCell() + $precision;
        $entry['decpos'] = $entry['ncells'] - $precision;
        $entry['unit'] = $this->getEntryNumberUnit() ? $this->getEntryNumberUnit()->getId() : null;
        $entries[] = $entry;

        if ($this->getHasDifferenceTable()) {
            $diff = [];
            $diff['name'] = 'Difference';
            $diff['type'] = $this->getEntryTypeOfNumber() ? $this->getEntryTypeOfNumber()->getCodeName() : "Unknown code name";
            $precision = $this->getEntrySignificantFractionalPlace();
            $diff['ncells'] = $this->getEntryNumberOfCell() + $precision;
            $diff['decpos'] = $diff['ncells'] - $precision;
            $entries[] = $diff;
        }

        $res['entries'] = $entries;

        return $res;
    }

    /**
     * Convert this table Object into an adequate JSON representation
     * This function is used in order to allow a JSON export from DTI. 
     * 
     * TODO : cette fonction ne retourne pas de JSON. Vérifier qui l'appelle et pourquoi.
     * Note : cette fonction est particulièrement employée pour la copie d'une table dans DTI (pour CATE, ou pour dupliquer). 
     * A voir si c'est nécessaire maintenant que les tables sont correctement json-isée depuis l'interface. 
     *
     * @return {Json}
     */
    public function toJson()
    {
        $tableContent = $this;
        $res = [
            'original' => $tableContent->getValueOriginal(),
            'differenceOriginal' => $tableContent->getDifferenceValueOriginal(),
            'table_type' => (string) $tableContent->getTableType()->getId(),
            'edited_text' => null
        ];

        if ($tableContent->getEditedText()) {
            $res['edited_text'] = $tableContent->getEditedText()->getId();
        }

        $original = $res['original'];

        if ($original !== NULL && count($original) !== 0) {
            $template = $this->getTemplate();
        } else {
            $template = NULL;
        }

        $mathematical_parameter_set = "";
        $astronomical_parameter_sets = [];

        if ($tableContent->getMathematicalParameter()) {
            $mathematical_parameter_set = $tableContent->getMathematicalParameter()->getId();
        }

        if (count($tableContent->getParameterSets()) > 0) {
            foreach ($tableContent->getParameterSets() as $astronomicalParameterSet) {
                $astronomical_parameter_sets[] = $astronomicalParameterSet->getId();
            }
        }

        $res['template'] = $template;
        $res['mathematical_parameter_set'] = $mathematical_parameter_set;
        $res['astronomical_parameter_sets'] = $astronomical_parameter_sets;
        return $res;
        //return json_encode($res);
    }

    /**
     * Convert this table Object into an adequate JSON representation
     * in order to display a table in the front office
     *
     * @return array $table
     */
    public function toPublicTable()
    {
        $tableContent = $this;
        $table = [
            'original' => $tableContent->getValueOriginal(),
            'csv' => $tableContent->contentToCsv(),
            'table_type' => [
                'id' => $tableContent->getTableType()->getId(),
                'title' => $tableContent->getTableType()->getTableTypeName()
            ],
            'edited_text' => null,
            'template' => null,
            'table_content' => [
                'id' => $tableContent->getId(),
                'title' => $tableContent->getTitle()
            ],
            'mathematical_parameter_set' => null,
            'astronomical_parameter_sets' => null
        ];

        if ($tableContent->getEditedText()) {
            $table['edited_text'] = [
                'id' => $tableContent->getEditedText()->getId(),
                'title' => $tableContent->getEditedText()->getTitle()
            ];
        }

        if ($table['original']) {
            $table['template'] = $this->getTemplate();
        }

        if ($tableContent->getMathematicalParameter()) {
            $table['mathematical_parameter_set'] = [
                'id' => $tableContent->getMathematicalParameter()->getId(),
                'title' => $tableContent->getMathematicalParameter()->__toString()
            ];
        }

        if (count($tableContent->getParameterSets()) > 0) {
            $table['astronomical_parameter_sets'] = [];
            foreach ($tableContent->getParameterSets() as $astronomicalParameterSet) {
                $table['astronomical_parameter_sets'][] = [
                    'id' => $astronomicalParameterSet->getId(),
                    'title' => $astronomicalParameterSet->__toString()
                ];
            }
        }

        return $table;
    }

    /**
     * toString methods *
     */
    public function getTitle()
    {
        $id = $this->getId();

        if (!$this->getTableType()) {
            throw new Exception(TableContent::getInterfaceName(true) . ' can\'t have null ' . TableType::getInterfaceName());
        }
        $unit = "";
        if ($this->meanMotion) {
            $unit = strval($this->argument1NumberUnit);
            if ($this->meanMotion->getSubTimeUnit()) {
                $unit = $this->meanMotion->getSubTimeUnit();
            }
            $unit = 'for ' . $unit . ' ';
        }

        $arg1 = $this->argument1Name ? "$this->argument1Name " : "";
        $arg2 = $this->argument2Name ? "$this->argument2Name " : "";
        $arg3 = $this->argument3Name ? "$this->argument3Name " : "";

        return "$arg1$arg2$arg3$unit(id n°$id)";
    }

    /**
     * toString methods *
     */
    public function __toString()
    {

        $title = $this->getTitle();
        $edition = "";
        if ($this->editedText) {
            $edition = GT::truncate(" in " . $this->editedText->getTitle(), 50);
        }

        return $title . $edition;
    }


    private function checkNullField($thisObject, $context, $fieldNames)
    {
        $errorMessage = [];
        foreach ($fieldNames as $field) {
            if ($thisObject->{"get" . ucfirst($field)}() === null) {
                $errorMessage[] = $field;
            }
        }
        foreach ($errorMessage as $message) {
            $context->buildViolation('This field must be filled.')
                ->atPath($message)
                ->addViolation();
        }
    }


    /**
     * This validation is for level "public" (3)
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context)
    {
        /* =============================== Check arguments fields ====================== */
        $argNumber = $this->argNumber;
        //We delete the information concerning the un-necessary arg-number. 
        for ($i = $argNumber + 1; $i <= 3; $i++) {
            $this->{"argument" . $i . "Name"} = null;
            $this->{"argument" . $i . "NumberOfCell"} = null;;
            $this->{"argument" . $i . "SignificantFractionalPlace"} = null;;
            $this->{"argument" . $i . "NumberUnit"} = null;;
            $this->{"argument" . $i . "NumberOfSteps"} = null;;
            $this->{"argument" . $i . "TypeOfNumber"} = null;;
        }

        /* =============================== Check not null if not Empty values ==================== */
        /* This assert is only triggered for the public entity.
        * Drafts and pre-set versions are not necessarely completed.*/
        if ($this->valueOriginal) {
            $narg = $this->getArgNumber();
            for ($i = 1; $i <= $narg; $i++) {
                $this->checkNullField($this, $context, [
                    "argument" . $i . "Name",
                    "argument" . $i . "TypeOfNumber",
                    "argument" . $i . "NumberUnit",
                    "argument" . $i . "SignificantFractionalPlace",
                    "argument" . $i . "NumberOfCell",
                    "argument" . $i . "NumberOfSteps"
                ]);
            }

            $this->checkNullField($this, $context, [
                "entryTypeOfNumber",
                "entryNumberUnit",
                "entrySignificantFractionalPlace",
                "entryNumberOfCell",
                "argNumber"
            ]);
        }


        /* ============================== Check mean motion logic ===========================*/
        if (!$this->editedText || !$this->tableType) {
            throw new Exception('All table content must have an editedText and a table type!');
        }
        $mm = false; //by default, the table type is not a mean motion. 
        if ($this->tableType->getAcceptMultipleContent()) {
            $mm = true;
        }
        if (!$mm) {
            if ($this->meanMotion) {
                throw new Exception('This table type is not linkable with mean-motion table');
            }
            if (count($this->editedText->getTableContents()) > 1) {
                throw new Exception('This edited text already contains a table content. No extra table content allowed for this type of tables');
            }
        } else {
            if (!$this->meanMotion) {
                throw new Exception('This table type should be linked to a mean-motion table');
            }
        }
    }



    /**
     * This method aims at checking that the json representation of the table are correctly represented. 
     * Without check, it might cause ES indexing issues. 
     * 
     * @Assert\Callback
     */
    public function validateJSON()
    {
        $tables = ["valueOriginal", "valueFloat", "correctedValueFloat"];
        foreach ($tables as $t) {
            $table = &$this->{$t};
            if (!$table) {
                continue;
            }
            if (!isset($table["template"])) {
                $table["template"] = $this->getTemplate();
            }
            $template = &$table["template"];

            try {
                if(array_key_exists("cwidth", $template))
                    $template["cwidth"] = intval($template["cwidth"]);
                $template["table_type"] = strval($template["table_type"]);
                if (isset($template["readonly"])) {
                    $template["readonly"] = boolval($template["readonly"]);
                }
                $args = &$template['args'];
                foreach ($args as &$arg) {
                    $arg["name"] = $arg["name"] ? strval($arg["name"]) : null;
                    $arg["type"] = $arg["type"] ? strval($arg["type"]) : null;
                    $arg["unit"] = $arg["unit"] ? intval($arg["unit"]) : null;
                    $arg["nsteps"] = $arg["nsteps"] ? intval($arg["nsteps"]) : null;
                    $arg["ncells"] = $arg["ncells"] ? intval($arg["ncells"]) : null;
                    $arg["decpos"] = $arg["decpos"] ? intval($arg["decpos"]) : null;
                }
                unset($arg);
                $args[0]["firstMonth"] = $args[0]["firstMonth"] ? intval($args[0]["firstMonth"]) : null;
                $args[0]["subUnit"] = $args[0]["subUnit"] ? intval($args[0]["subUnit"]) : null;
                $entry = &$template["entries"][0];
                $entry["name"] = $entry["name"] ? strval($entry["name"]) : null;
                $entry["type"] = $entry["type"] ? strval($entry["type"]) : null;
                $entry["unit"] = $entry["unit"] ? intval($entry["unit"]) : null;
                $entry["ncells"] = $entry["ncells"] ? intval($entry["ncells"]) : null;
                $entry["decpos"] = $entry["decpos"] ? intval($entry["decpos"]) : null;
            } catch (ContextErrorException $e) {
                $table["template"] = $this->getTemplate();
            }
        }
    }

    /**
     * Get a string representing the arguments as name. 
     */
    public function getArgNames()
    {
        $arg1 = $this->argument1Name ? "$this->argument1Name" : "";
        $arg2 = $this->argument2Name ? "$this->argument2Name" : "";
        $sep = $this->argument1Name && $this->argument2Name ? " ╲ " : "";

        return "$arg1$sep$arg2";
    }

    /**
     * @PreSerialize
     *
     * Convert all Json arrays values to strings
     *
     */
    private function preSerialize()
    {
        $this->kibanaName = $this->__toString();
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);

        if($this->editedText && !$this->editedText->getPublic())
            $this->setEditedText(null);

        // arrays with fields to convert to string
        $float_json_arrays = ["valueFloat" => $this->valueFloat, "differenceValueFloat" => $this->differenceValueFloat, "correctedValueFloat" => $this->correctedValueFloat];
        $original_json_arrays = ["valueOriginal" => $this->valueOriginal, "differenceValueOriginal" => $this->differenceValueOriginal];

        // keys to check in those arrays
        $float_keys = ["args", "entry"];
        $original_keys = ["symmetries"];

        // loop where we convert to strings each field needed of each float values json array
        foreach ($float_json_arrays as $array_name => &$array) {
            if ($array) { // check if the array is not empty
                // check for each key if it exists and if the array is not empty
                foreach ($float_keys as $float_key) {
                    if (array_key_exists($float_key, $array) && $array[$float_key]) {
                        // for each value of the current key (wether a value or an array of undefined depth), we convert it to strings
                        foreach ($array[$float_key] as $key => &$float_values_array) {
                            $float_values_array = $this->valueToString($float_values_array);
                        }
                        unset($float_values_array);
                    }
                }
            }

            // set the field of the object to the fully converted array
            $setMethod = "set" . ucfirst($array_name);
            $this->$setMethod($array);
        }
        unset($array);

        // loop where we convert to strings each field needed of each original values json array
        foreach ($original_json_arrays as $array_name => &$array) {
            if ($array) { // check if the array is not empty
                // check for each key if it exists and if the array is not empty
                foreach ($original_keys as $original_key) {
                    if (array_key_exists($original_key, $array) && $array[$original_key]) {
                        // for each value of the current key (wether a value or an array of undefined depth), we convert it to strings
                        foreach ($array[$original_key] as $key => &$original_values_array) {
                            $original_values_array = $this->valueToString($original_values_array);
                        }
                        unset($original_values_array);
                    }
                }
            }

            // set the field of the object to the fully converted array
            $setMethod = "set" . ucfirst($array_name);
            $this->$setMethod($array);
        }
        unset($array);
    }

    /*
    *
    * Recursive function to convert to strings values of an array of any depth
    *
    */
    private function valueToString($value)
    {
        // value sent is either an array or a primitive type (convertible to string)
        if (is_array($value)) {
            $result = []; // if value is an array, result is an array of its values converted to string

            foreach ($value as $key => &$sub_value) { // if the value is an array, we check each element
                if (is_array($sub_value)) { // if the element itself is an array, we make a recursive call
                    $result[$key] = $this->valueToString($sub_value);
                } else { // if it's a primitive type, we convert it to string and add it to the return array
                    $result[$key] = strval($sub_value);
                }
            }
        } else {
            $result = strval($value); // if value is a primitive type, result is value converted to string
        }

        return $result;
    }

    /**
     * This method generates a csv string of the current table content
     * The table content is surrounded with a grid (row and column headers as in Excel)
     * Il allows to generate a critical apparatus with those cells coordinates
     *
     * @return string
     */
    public function contentToCsv()
    {
        if (!$this->getValueOriginal()) {
            return "";
        }

        $template = $this->getTemplate();

        $arg1 = $this->getValueOriginal()["args"]["argument1"];
        $arg2 = null;
        if (count($template["args"]) == 2 && isset($this->getValueOriginal()["args"]["argument2"])) {
            $arg2 =  $this->getValueOriginal()["args"]["argument2"];
        }
        $entries = $this->getValueOriginal()["entry"];

        $nbOfSuperCol = $arg2 ? $template["args"][1]["nsteps"] : 1;
        $widthSuperCol = $arg2 ? max($template["entries"][0]["ncells"], $template["args"][1]["ncells"]) : $template["entries"][0]["ncells"];

        $nbOfRow = $template["args"][0]["nsteps"] + 1; // one less than the actual number (because it refers to indexes in array)
        $nbOfCol = ($widthSuperCol * $nbOfSuperCol) + $template["args"][0]["ncells"]; // one less than the actual number (because it refers to indexes in array)

        $csv = "";
        $sep = ",";

        $critApp = "\nCRITICAL APPARATUS\n";
        $comment = "\nCOMMENT(S)\n";

        for ($rowNb = 0; $rowNb <= $nbOfRow; $rowNb++) {
            $superColNb = 0; // to keep count of the super cell we are in
            $subColNb = 0; // to keep track of the position in the super cell of the cell we are in

            for ($colNb = 0; $colNb <= $nbOfCol; $colNb++) {

                /* ROW HEADERS */
                if ($colNb == 0) {
                    if ($rowNb == 0) { // put argument names in the upper left corner
                        $csv = $csv . "╲";
                    } else { // generate first row of table (second argument row or empty)
                        $csv = $csv . $rowNb;
                    }
                } else {
                    $csv = $csv . $sep;

                    /* COLUMN HEADERS */
                    if ($rowNb == 0) { // generate column header
                        $csv = $csv . GT::toColumnName($colNb);
                    } else {

                        /* FIRST ROW */
                        if ($rowNb == 1) {
                            /* ARGUMENT NAMES */
                            if ($superColNb == 0) {
                                if ($subColNb == 0) {
                                    $csv = $csv . $this->getArgNames();
                                }

                                /* SECOND ARGUMENT ROW */
                            } elseif ($arg2 && count($arg2[$superColNb - 1]["value"]) > $subColNb) {
                                $csv = $csv . $arg2[$superColNb - 1]["value"][$subColNb];

                                // Check if critical apparatus associated to the cell
                                if (isset($arg2[$superColNb - 1]["critical_apparatus"])) {
                                    if ($arg2[$superColNb - 1]["critical_apparatus"][$subColNb] != "") {
                                        $apparatus = str_replace("\n", ",", $arg2[$superColNb - 1]["critical_apparatus"][$subColNb]);
                                        $critApp = $critApp . GT::toColumnName($colNb) . $rowNb . " = $apparatus\n";
                                    }
                                }

                                if ($subColNb == 0) {
                                    // Check if comment associated to the superCell
                                    if ($arg2[$superColNb - 1]["comment"] != "") {
                                        $coord = GT::toColumnName($colNb) . $rowNb . ":" . GT::toColumnName($colNb + $template["args"][1]["ncells"] - 1) . $rowNb;
                                        $comm = str_replace("\n", ",", $arg2[$superColNb - 1]['comment']);
                                        $comment = "$comment$coord = $comm\n";
                                    }
                                }
                            }
                        } else {

                            /* FIRST ARGUMENT SUPER COLUMN */
                            if ($superColNb == 0) { // generate first arg
                                if (isset($arg1[$rowNb - 2])) {
                                    $csv = $csv . $arg1[$rowNb - 2]["value"][$subColNb];

                                    // Check if critical apparatus associated to the cell
                                    if (isset($arg1[$rowNb - 2]["critical_apparatus"])) {
                                        if ($arg1[$rowNb - 2]["critical_apparatus"][$subColNb] != "") {
                                            $apparatus = str_replace("\n", ",", $arg1[$rowNb - 2]["critical_apparatus"][0]);
                                            $critApp = $critApp . GT::toColumnName($colNb) . $rowNb . " = $apparatus\n";
                                        }
                                    }

                                    if ($subColNb == 0) {
                                        // Check if comment associated to the superCell
                                        if ($arg1[$rowNb - 2]["comment"] != "") {
                                            $firstCell = GT::toColumnName($colNb) . $rowNb;
                                            $secondCell = isset($template["args"][1]) ? GT::toColumnName($colNb + $template["args"][1]["ncells"] - 1) . $rowNb : GT::toColumnName($colNb + $template["args"][0]["ncells"] - 1) . $rowNb;
                                            $coord = "$firstCell:$secondCell";
                                            $comm = str_replace("\n", ",", $arg1[$rowNb - 2]["comment"]);
                                            $comment = "$comment$coord = $comm\n";
                                        }
                                    }
                                }

                                /* ENTRY VALUES */
                            } else {
                                if ($arg2) {
                                    $csv = $csv . $entries[$rowNb - 2][$superColNb - 1]["value"][$subColNb];

                                    // Check if critical apparatus associated to the cell
                                    if (isset($entries[$rowNb - 2][$superColNb - 1]["critical_apparatus"])) {
                                        if ($entries[$rowNb - 2][$superColNb - 1]["critical_apparatus"][$subColNb] != "") {
                                            $apparatus = str_replace("\n", ",", $entries[$rowNb - 2][$superColNb - 1]["critical_apparatus"][$subColNb]);
                                            $critApp = $critApp . GT::toColumnName($colNb) . $rowNb . " = $apparatus\n";
                                        }
                                    }

                                    if ($subColNb == 0) {
                                        // Check if comment associated to the superCell
                                        if ($entries[$rowNb - 2][$superColNb - 1]["comment"] != "") {
                                            $coord = GT::toColumnName($colNb) . $rowNb . ":" . GT::toColumnName($colNb + $template["entries"][0]["ncells"] - 1) . $rowNb;
                                            $comm = str_replace("\n", ",", $entries[$rowNb - 2][$superColNb - 1]["comment"]);
                                            $comment = "$comment$coord = $comm\n";
                                        }
                                    }
                                } else {
                                    if (isset($entries[$rowNb - 2]["value"])) {
                                        $csv = $csv . $entries[$rowNb - 2]["value"][$subColNb];

                                        // Check if critical apparatus associated to the cell
                                        if (isset($entries[$rowNb - 2]["critical_apparatus"])) {
                                            if ($entries[$rowNb - 2]["critical_apparatus"][$subColNb] != "") {
                                                $apparatus = str_replace("\n", ",", $entries[$rowNb - 2]["critical_apparatus"][$subColNb]);
                                                $critApp = $critApp . GT::toColumnName($colNb) . $rowNb . " = $apparatus\n";
                                            }
                                        }

                                        if ($subColNb == 0) {
                                            // Check if comment associated to the superCell
                                            if ($entries[$rowNb - 2]["comment"] != "") {
                                                $coord = GT::toColumnName($colNb) . $rowNb . ":" . GT::toColumnName($colNb + $template["entries"][0]["ncells"] - 1) . $rowNb;
                                                $comm = str_replace("\n", ",", $entries[$rowNb - 2]["comment"]);
                                                $comment = "$comment$coord = $comm\n";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // keep track of the super column we are in
                if ($colNb != 0) {
                    if ($superColNb == 0) { // if we in the first super column, the width corresponds to the nb of cells of the first arg
                        if ($subColNb > $template["args"][0]["ncells"] - 2) {
                            $superColNb++;
                            $subColNb = 0;
                        } else {
                            $subColNb++;
                        }
                    } else {
                        if ($subColNb > $widthSuperCol - 2) {
                            $superColNb++;
                            $subColNb = 0;
                        } else {
                            $subColNb++;
                        }
                    }
                }
            }
            $csv = $csv . "\n";
        }

        if ($critApp == "\nCRITICAL APPARATUS\n") {
            $critApp = "";
        }
        if ($comment == "\nCOMMENT(S)\n") {
            $comment = "";
        }

        $csv = "$csv$critApp$comment";

        return $csv;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "table content";
        if (!$isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }

    /**
     * This method returns a string that can be displayed in the sidebar of metadata
     * to describe table contents associated to an edition
     * @return string
     */
    public function getUnits()
    {
        $id = $this->getId();
        if ($this->getArgument1Name()) {
            $arg1unit = $this->getArgument1NumberUnit() ? $this->getArgument1NumberUnit()->getUnit() : "<span class='noInfo'>No unit</span>";
            $arg1nb = $this->argument1TypeOfNumber ? $this->argument1TypeOfNumber : "<span class='noInfo'>No type of number</span>";
            /*$arg1 = "Arg.1 (".$this->getArgument1Name()."): $arg1unit in $arg1nb";*/
            $arg1 = "<span class='underline'>Arg.1</span>: $arg1unit in $arg1nb";
        } else {
            return "<span class='noInfo'>Empty table — n°$id</span>";
        }

        if ($this->meanMotion) {
            $mm = $this->getMeanMotion();
            $subUnit = "";
            if ($mm->getSubTimeUnit()) {
                $subUnit = " (" . $mm->getSubTimeUnit()->getSubType() . ")";
                /*if ($arg1unit == "month"){
                    $firstMonth = $mm->getFirstMonth();
                    $arg1 = $arg1."<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(first month: $firstMonth)";
                }*/
            }
            $tableName = ucfirst($arg1unit) . $subUnit . " — n°$id";
        } else {
            /*$tableName = ucfirst($this->getArgNames())." — n°$id";*/
            $tableName = "Table for " . $this->getArgNames() . " — n°$id";
        }

        $arg2 = "";
        if ($this->getArgument2Name()) {
            $arg2unit = $this->getArgument2NumberUnit() ? $this->getArgument2NumberUnit()->getUnit() : "<span class='noInfo'>No unit</span>";
            $arg2nb = $this->argument2TypeOfNumber ? $this->argument2TypeOfNumber : "<span class='noInfo'>No type of number</span>";
            /*$arg2 = "<br>Arg.2 (".$this->getArgument2Name()."): $arg2unit in $arg2nb";*/
            $arg2 = "<br><span class='underline'>Arg.2</span>: $arg2unit in $arg2nb";
        }

        $entryUnit = $this->getEntryNumberUnit() ? $this->getEntryNumberUnit()->getUnit() : "<span class='noInfo'>No unit</span>";
        $entryNb = $this->entryTypeOfNumber ? $this->entryTypeOfNumber : "<span class='noInfo'>No type of number</span>";
        $entry = "<span class='underline'>Entry</span>: $entryUnit in $entryNb";

        return "<span class='mainContent'>$tableName</span><br>$arg1$arg2<br>$entry";
    }


    /**
     * Short function to shift an array
     */
    private function correctedIndex($index, $shift, $array)
    {
        $totalValue = count($array);
        return ($index-$shift+$totalValue)%$totalValue;
    }

    /**
     * This method copies the javascript convertor for corrected values
     * It corrects every float values according to its mathematical parameters
     */
    public function convertCorrectedValues(bool $addTemplate = True)
    {
        $float = $this->valueFloat;
        $mathParam = $this->mathematicalParameter;
        $corrected = [];
        $entries = $float['entry'];
        $arguments = $float['args'];

        foreach ($entries as $index => $value) {
            if (count($float['args']) === 1) { // Management of 1arg table
                $correctedIndex = $this->correctedIndex($index,$mathParam->getEntryShift(), $entries);
                $corrected['entry'][$index] = $entries[$correctedIndex] + $mathParam->getEntryDisplacementFloat();
            } else {
                $entryShift2 = ($mathParam->getEntryShift2() ?? 0);
                foreach ($value as $index2 => $value2) {
                    $correctedIndex = $this->correctedIndex($index, $mathParam->getEntryShift(), $entries);
                    $correctedIndex2 = $this->correctedIndex($index2, $entryShift2, $value);
                    $corrected['entry'][$index][$index2] = $entries[$correctedIndex][$correctedIndex2] + $mathParam->getEntryDisplacementFloat();
                }
            }
        }
        foreach ($arguments as $i => $argument) {
            $argumentShift = $mathParam->{"get" . ucfirst($i) . "Shift"}() ?? 0;
            $argumentDisplacement = $mathParam->{"get" . ucfirst($i) . "DisplacementFloat"}() ?? 0;
            foreach ($argument as $indexArg => $value) {
                $correctedIndexArg = $this->correctedIndex($indexArg, $argumentShift, $argument);
                $corrected['args'][$i][$indexArg] = $argument[$correctedIndexArg] + $argumentDisplacement;
            }
        }
        if($addTemplate)
            $corrected["template"] = $this->getTemplate();
        
        
        return $corrected;
    }
}
