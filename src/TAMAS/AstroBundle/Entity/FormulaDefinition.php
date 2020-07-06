<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * FormulaDefinition
 *
 * @ORM\Table(name="formula_definition")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\FormulaDefinitionRepository")
 */
class FormulaDefinition
{

    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="TAMAS\AstroBundle\Entity\ImageFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Assert\Valid()
     */
    private $image;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=1000)
     */
    private $name;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var Historian $historian
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Historian")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $author;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var string
     *
     * @ORM\Column(name="explanation", type="text", nullable=true)
     */
    private $explanation;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var string
     *
     * @ORM\Column(name="tip", type="text", nullable=true)
     */
    private $tip;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var string
     *
     * @ORM\Column(name="modern_definition", type="text", nullable=true)
     */
    private $modernDefinition;

    /**
     * @var array
     *
     * @ORM\Column(name="formula_JSON", type="json_array", nullable=true)
     */
    private $formulaJSON;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var string
     *
     * @ORM\Column(name="bibliography", type="text", nullable=true)
     */
    private $bibliography;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var string
     *
     * @ORM\Column(name="parameter_explanation", type="text", nullable=true)
     */
    private $parameterExplanation;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @var string
     *
     * @ORM\Column(name="estimator_definition", type="text", nullable=true)
     */
    private $estimatorDefinition;

    /**
     * @Groups({"formulaDefinitionMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="arg_number", type="integer", nullable=false)
     */
    private $argNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="latex_formula", type="text", nullable=true)
     */
    private $latexFormula;


    /**
     * @Groups({"externalTableTypeFD"})
     * @var string $tableType
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TableType")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $tableType;

    /**
     * @Groups({"formulaDefinition"})
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Groups({"formulaDefinition"})
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
     * @Groups({"formulaDefinitionMain"})
     * @var string
     */
    private $kibanaName;


    /**
     * @Groups({"kibana"})
     * @var string
     *
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
     * Set name
     *
     * @param string $name
     *
     * @return FormulaDefinition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set explanation
     *
     * @param string $explanation
     *
     * @return FormulaDefinition
     */
    public function setExplanation($explanation)
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * Get explanation
     *
     * @return string
     */
    public function getExplanation()
    {
        return $this->explanation;
    }

    /**
     * Set modernDefinition
     *
     * @param string $modernDefinition
     *
     * @return FormulaDefinition
     */
    public function setModernDefinition($modernDefinition)
    {
        $this->modernDefinition = $modernDefinition;

        return $this;
    }

    /**
     * Get modernDefinition
     *
     * @return string
     */
    public function getModernDefinition()
    {
        return $this->modernDefinition;
    }

    /**
     * Set formulaJSON
     *
     * @param array $formulaJSON
     *
     * @return FormulaDefinition
     */
    public function setFormulaJSON($formulaJSON)
    {
        if (is_string($formulaJSON)) {
            $formulaJSON = json_decode($formulaJSON, 1);
        }
        $this->formulaJSON = $formulaJSON;

        return $this;
    }

    /**
     * Get formulaJSON
     *
     * @return array
     */
    public function getFormulaJSON()
    {
        return $this->formulaJSON;
    }

    /**
     * Set bibliography
     *
     * @param string $bibliography
     *
     * @return FormulaDefinition
     */
    public function setBibliography($bibliography)
    {
        $this->bibliography = $bibliography;

        return $this;
    }

    /**
     * Get bibliography
     *
     * @return string
     */
    public function getBibliography()
    {
        return $this->bibliography;
    }

    /**
     * Set parameterExplanation
     *
     * @param string $parameterExplanation
     *
     * @return FormulaDefinition
     */
    public function setParameterExplanation($parameterExplanation)
    {
        $this->parameterExplanation = $parameterExplanation;

        return $this;
    }

    /**
     * Get parameterExplanation
     *
     * @return string
     */
    public function getParameterExplanation()
    {
        return $this->parameterExplanation;
    }

    /**
     * Set estimatorDefinition
     *
     * @param string $estimatorDefinition
     *
     * @return FormulaDefinition
     */
    public function setEstimatorDefinition($estimatorDefinition)
    {
        $this->estimatorDefinition = $estimatorDefinition;

        return $this;
    }

    /**
     * Get estimatorDefinition
     *
     * @return string
     */
    public function getEstimatorDefinition()
    {
        return $this->estimatorDefinition;
    }

    /**
     * Set image
     *
     * @param \TAMAS\AstroBundle\Entity\ImageFile $image
     *
     * @return FormulaDefinition
     */
    public function setImage(\TAMAS\AstroBundle\Entity\ImageFile $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \TAMAS\AstroBundle\Entity\ImageFile
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return FormulaDefinition
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
     * @return FormulaDefinition
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
     * @return FormulaDefinition
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
     * @return FormulaDefinition
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

    public function setDefaultOptions($resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
        ));
    }


    /**
     * Set tableType
     *
     * @param \TAMAS\AstroBundle\Entity\TableType $tableType
     *
     * @return FormulaDefinition
     */
    public function setTableType(\TAMAS\AstroBundle\Entity\TableType $tableType = null)
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
     * Set author
     *
     * @param string $author
     *
     * @return FormulaDefinition
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set tip.
     *
     * @param string|null $tip
     *
     * @return FormulaDefinition
     */
    public function setTip($tip = null)
    {
        $this->tip = $tip;

        return $this;
    }

    /**
     * Get tip.
     *
     * @return string|null
     */
    public function getTip()
    {
        return $this->tip;
    }

    /**
     * Set latexFormula.
     *
     * @param string|null $latexFormula
     *
     * @return FormulaDefinition
     */
    public function setLatexFormula($latexFormula = null)
    {
        $this->latexFormula = $latexFormula;

        return $this;
    }

    /**
     * Get latexFormula.
     *
     * @return string|null
     */
    public function getLatexFormula()
    {
        return $this->latexFormula;
    }

    /**
     * Set argNumber.
     *
     * @param int $argNumber
     *
     * @return FormulaDefinition
     */
    public function setArgNumber($argNumber)
    {
        $this->argNumber = $argNumber;

        return $this;
    }

    /**
     * Get argNumber.
     *
     * @return int
     */
    public function getArgNumber()
    {
        return $this->argNumber;
    }

    public function __toString()
    {
        $tableType = $this->getTableType()->toPublicTitle();
        return ucfirst(str_replace("_", " ", $this->name)) . " | " . str_replace(" |", ":", $tableType);
    }

    public function toPublicTitle()
    {
        if (!$this->name)
            return "<span class='noInfo'>Unnamed " . FormulaDefinition::getInterfaceName() . "</span>";
        return str_replace("_", " ", ucfirst($this->getName()));
    }

    public function toPublicString()
    {
        if (!$this->getLatexFormula()) {
            return "No associated formula";
        }
        $formula = $this->getLatexFormula();

        // remove the paragraph elements (<p>) before and after the formula
        return substr($formula, 3, strlen($formula) - 7);
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "formula definition";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
