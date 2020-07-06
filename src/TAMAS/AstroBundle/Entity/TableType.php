<?php

//Symfony\src\TAMAS\AstroBundle\Entity\TableType.php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * TableType
 *
 * @ORM\Table(name="table_type")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\TableTypeRepository")
 */
class TableType
{

    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = false;
    
    /**
     * @Groups({"tableTypeMain"})
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\AstronomicalObject", inversedBy="tableTypes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $astronomicalObject;

    /**
     * @Groups({"tableTypeMain"})
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups({"tableTypeMain"})
     * @ORM\Column(name="table_type_name", type="string", length=100)
     */
    private $tableTypeName;

    /**
     * @var array
     *
     * @ORM\Column(name="table_parameter_output", type="json_array", nullable=true)
     */
    private $tableParameterOutput;

    /**
     *
     * @ORM\OneToMany(targetEntity="TAMAS\AstroBundle\Entity\ParameterSet", mappedBy="tableType", cascade={"persist"})
     */
    private $parameterSets;

    /**
     * 
     * @var bool
     * @ORM\Column(name="accept_multiple_content", type="boolean", nullable=true)
     */
    private $acceptMultipleContent;

    /**
     * @var string
     * @Groups({"tableTypeMain"})
     * @ORM\Column(name="astro_definition", type="string", length=100)
     */
    private $astroDefinition;


    /**
     * @Groups({"tableTypeMain"})
     * @var string
     */
    private $kibanaName;


    /**
     * @PreSerialize
     *
     * Convert all Json arrays values to strings
     *
     */
    private function preSerialize()
    {
        $this->kibanaName = $this->toPublicTitle();
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
     * Set tableTypeName
     *
     * @param string $tableTypeName
     *
     * @return TableType
     */
    public function setTableTypeName($tableTypeName)
    {
        $this->tableTypeName = $tableTypeName;

        return $this;
    }

    /**
     * Get tableTypeName
     *
     * @return string
     */
    public function getTableTypeName()
    {
        return $this->tableTypeName;
    }

    /**
     * Get astro definition
     *
     * @return string
     */
    public function getAstroDefinition()
    {
        return $this->astroDefinition;
    }


    /**
     * @param $astroDefinition
     * @return $this
     */
    public function setAstroDefinition($astroDefinition)
    {
        $this->astroDefinition = $astroDefinition;

        return $this;
    }

    /**
     * Set tableParameterOutput
     *
     * @param array $tableParameterOutput
     *
     * @return TableType
     */
    public function setTableParameterOutput($tableParameterOutput)
    {
        $this->tableParameterOutput = $tableParameterOutput;

        return $this;
    }

    /**
     * Get tableParameterOutput
     *
     * @return array
     */
    public function getTableParameterOutput()
    {
        return $this->tableParameterOutput;
    }

    /**
     * Set astronomicalObject
     *
     * @param \TAMAS\AstroBundle\Entity\AstronomicalObject $astronomicalObject
     *
     * @return TableType
     */
    public function setAstronomicalObject(\TAMAS\AstroBundle\Entity\AstronomicalObject $astronomicalObject)
    {
        if ($this->astronomicalObject !== null) {
            $this->astronomicalObject->removeTableType($this); // necessary in bi-directional relation. In case we are doing an update of the data, we have to be careful that the previously related astronomical object array
        }

        if ($astronomicalObject !== null) {
            $astronomicalObject->addTableType($this);
        }
        $this->astronomicalObject = $astronomicalObject;

        return $this;
    }

    /**
     * Get astronomicalObject
     *
     * @return \TAMAS\AstroBundle\Entity\AstronomicalObject
     */
    public function getAstronomicalObject()
    {
        return $this->astronomicalObject;
    }

    /**
     * 
     * This method is particularly used to properly render forms and help selecting the tableType in edit form
     * @return string
     */
    public function __toString()
    {
        /*$objectName = $this->astronomicalObject->getObjectName() ? ucfirst($this->astronomicalObject->getObjectName()) : "";
        $tableTypeTitle = $this->tableTypeName ? $this->tableTypeName : "Unknown " . TableType::getInterfaceName();
        $link = '';
        if ($tableTypeTitle != "Unknown " . TableType::getInterfaceName() && $objectName != "") {
            $link = ": ";
        }
        return $objectName . $link . $tableTypeTitle;*/

        return $this->toPublicTitle();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameterSets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parameterSet
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterSet $parameterSet
     *
     * @return TableType
     */
    public function addParameterSet(\TAMAS\AstroBundle\Entity\ParameterSet $parameterSet)
    {

        $this->parameterSets[] = $parameterSet;

        return $this;
    }

    /**
     * Remove parameterSet
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterSet $parameterSet
     */
    public function removeParameterSet(\TAMAS\AstroBundle\Entity\ParameterSet $parameterSet)
    {
        $this->parameterSets->removeElement($parameterSet);
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
     * Set acceptMultipleContent.
     *
     * @param bool|null $acceptMultipleContent
     *
     * @return TableType
     */
    public function setAcceptMultipleContent($acceptMultipleContent = null)
    {
        $this->acceptMultipleContent = $acceptMultipleContent;

        return $this;
    }

    /**
     * Get acceptMultipleContent.
     *
     * @return bool|null
     */
    public function getAcceptMultipleContent()
    {
        return $this->acceptMultipleContent;
    }

    /**
     * Returns the table type name without the repetition of the object name when it is unnecessary
     * @return string
     */
    public function getPublicName()
    {
        $object = $this->astronomicalObject->getObjectName();
        $type = str_replace(" " . strtolower($object), "", strtolower($this->tableTypeName));
        $type = str_replace(strtolower($object) . " ", "", strtolower($type));

        if ($type == "equation of the") {
            $type = "Equation of the " . ucfirst($object);
        }
        return ucfirst($type);
    }

    public function toPublicString()
    {
        return "<span class='mainContent'>" . ucfirst($this->astronomicalObject->getObjectName()) . "</span><br/>" . $this->getPublicName();
    }

    public function toPublicTitle()
    {
        return ucfirst($this->astronomicalObject->getObjectName()) . " | " . $this->getPublicName();
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "table type";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
