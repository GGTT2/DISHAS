<?php
//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;


/**
 * AstronomicalObject
 *
 * @ORM\Table(name="astronomical_object")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\AstronomicalObjectRepository")
 */
class AstronomicalObject {

    /**
     * This static attributes state if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = false;

    /**
     * @ORM\OneToMany(targetEntity="TAMAS\AstroBundle\Entity\TableType", mappedBy="astronomicalObject")
     */
    private $tableTypes;

    /**
     * @Type("string")
     * @var int
     * @Groups({"tableTypeMain"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups({"tableTypeMain"})
     * @ORM\Column(name="object_name", type="string", length=45, unique=true)
     */
    private $objectName;
    
    /**
     * @Groups({"tableTypeMain"})
     * 
     * @ORM\Column(name="color", type="string", length=45, unique=true, nullable=true)
     *
     *  @var string
     */
    private $color;

    /**
     * @Groups({"tableTypeMain"})
     * @ORM\Column(name="object_definition", type="text", nullable=true)
     * @var string
     */
    private $objectDefinition;
    

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set objectName
     *
     * @param string $objectName
     *
     * @return AstronomicalObject
     */
    public function setObjectName($objectName) {
        $this->objectName = $objectName;

        return $this;
    }

    /**
     * Get objectName
     *
     * @return string
     */
    public function getObjectName() {
        return $this->objectName;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tableTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tableType
     *
     * @param \TAMAS\AstroBundle\Entity\TableType $tableType
     *
     * @return AstronomicalObject
     */
    public function addTableType(\TAMAS\AstroBundle\Entity\TableType $tableType)
    {
        $this->tableTypes[] = $tableType;
        return $this;
    }

    /**
     * Remove tableType
     *
     * @param \TAMAS\AstroBundle\Entity\TableType $tableType
     */
    public function removeTableType(\TAMAS\AstroBundle\Entity\TableType $tableType)
    {
        $this->tableTypes->removeElement($tableType);
    }

    /**
     * Get tableTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTableTypes()
    {
        return $this->tableTypes;
    }

    public function getColor(){
        return $this->color;
    }

    public function getObjectDefinition(){
        return $this->objectDefinition;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "astronomical object";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}