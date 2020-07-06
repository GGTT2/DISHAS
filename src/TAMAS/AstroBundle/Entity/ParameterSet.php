<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\Validator\Constraints as TAMASAssert;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * ParameterSet
 *
 * @ORM\Table(name="parameter_set")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ParameterSetRepository")
 * @TAMASAssert\SmartNumber
 */
class ParameterSet
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    /**
     * @Groups({"parameterSetMain"})
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
     * @Groups({"parameterSetMain"})
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
     *
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @Groups({"externalTableTypePS"})
     * 
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TableType", inversedBy="parameterSets", cascade={"persist"})
     */
    private $tableType;

    /**
     * @Groups({"parameterSetMain"})
     *
     *
     * @ORM\OneToMany(targetEntity="TAMAS\AstroBundle\Entity\ParameterValue", mappedBy="parameterSet", cascade={"persist", "remove"})
     *
     * @Assert\Valid()
     */
    private $parameterValues;

    /**
     * @Groups({"externalTableContentPS"})
     * @ORM\ManyToMany(targetEntity="TAMAS\AstroBundle\Entity\TableContent", mappedBy="parameterSets")
     */
    private $tableContents;


    /**
     * @Groups({"parameterSetMain"})
     * @Type("string")
     *
     * @var string
     */
    private $defaultTitle;

    /**
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
     * @PreSerialize
     */
    private function onPreSerialize()
    {
        $this->kibanaName = $this->__toString();
        $this->defaultTitle = $this->getStringValues();
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);

        foreach($this->tableContents as $tc){
            if(!$tc->getPublic())
                $this->removeTableContent($tc);
        }
    }

    /**
     * Set id
     *
     * @param \int $id
     *
     * @return ParameterSet
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return ParameterSet
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
     * @return ParameterSet
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
     * @return ParameterSet
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
     * @return ParameterSet
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
     * Set tableType
     *
     * @param \TAMAS\AstroBundle\Entity\TableType $tableType
     *
     * @return ParameterSet
     */
    public function setTableType(\TAMAS\AstroBundle\Entity\TableType $tableType = null)
    {
        if ($this->tableType !== null) { // if this entity was previously linked to a tableType
            $this->tableType->removeParameterSet($this); // go to this tableType propriety, and delete the relation
        }

        if ($tableType !== null) {
            $tableType->addParameterSet($this);
        }
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
        $this->parameterValues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tableContents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parameterValue
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterValue $parameterValue
     *
     * @return ParameterSet
     */
    public function addParameterValue(\TAMAS\AstroBundle\Entity\ParameterValue $parameterValue)
    {
        $this->parameterValues[] = $parameterValue;
        $parameterValue->setParameterSet($this);

        return $this;
    }

    /**
     * Remove parameterValue
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterValue $parameterValue
     */
    public function removeParameterValue(\TAMAS\AstroBundle\Entity\ParameterValue $parameterValue)
    {
        $this->parameterValues->removeElement($parameterValue);
    }

    /**
     * Get parameterValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameterValues()
    {
        return $this->parameterValues;
    }

    public function checkIfNull()
    {
        if (!empty($this->parameterValues)) {
            return false;
        }
        foreach ($this as $value) {
            if ($value != null && !is_object($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add tableContent
     *
     * @param \TAMAS\AstroBundle\Entity\TableContent $tableContent
     *
     * @return ParameterSet
     */
    public function addTableContent(\TAMAS\AstroBundle\Entity\TableContent $tableContent)
    {
        if (!$this->tableContents->contains($tableContent)) {
            $this->tableContents[] = $tableContent;
            $tableContent->addParameterSet($this);
        }
        return $this;
    }

    /**
     * Remove tableContent
     *
     * @param \TAMAS\AstroBundle\Entity\TableContent $tableContent
     */
    public function removeTableContent(\TAMAS\AstroBundle\Entity\TableContent $tableContent)
    {
        if ($this->tableContents->contains($tableContent)) {
            $this->tableContents->removeElement($tableContent);
            $tableContent->removeParameterSet($this);
        }
    }

    /**
     * Get tableContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTableContents()
    {
        return $this->tableContents;
    }

    /**
     * toString Methods
     */

    /**
     * Generates the concatenation of all parameter values of a parameter sets
     * @param $withHTML boolean : are html tags allowed in the returned string?
     * @return string
     */
    public function getStringValues($withHTML = true)
    {
        $parameterValues = [];
        foreach ($this->parameterValues as $parameterValue) {
            if ($parameterValue->getValueOriginalBase()) {
                $parameterValues[] = $parameterValue;
            }
        }

        usort($parameterValues, "TAMAS\AstroBundle\Repository\ParameterSetRepository::mySort");
        $stringValues = "";
        $length = count($parameterValues);
        $i = 0;
        foreach ($parameterValues as $parameterValue) {
            $stringValues = $stringValues . $parameterValue->getValueOriginalBase();
            if ($i < $length - 1) {
                if (mb_substr($stringValues, -2, 2) === " ;") {
                    $stringValues = mb_substr($stringValues, 0, strlen($stringValues) - 2);
                }
                $stringValues = "$stringValues | ";
            }
            $i++;
        }

        if (mb_substr($stringValues, -2, 2) === " ;") {
            $stringValues = mb_substr($stringValues, 0, strlen($stringValues) - 2);
        }

        if ($stringValues == "") {
            $stringValues = $withHTML ? "<span class='noInfo'>null</span>" : "null";
        }

        return $stringValues;
    }

    public function getTitle()
    {
        return $this->getStringValues() . " (id n° " . $this->id . ")";
    }

    public function __toString()
    {
        $id = $this->id;
        $tableType = $this->tableType ? $this->tableType : "";

        return "$tableType n°$id [val: " . $this->getStringValues(false) . "]";
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "parameter set";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
