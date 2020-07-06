<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;

/**
 * Definition
 *
 * @ORM\Table(name="definition")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\DefinitionRepository")
 */
class Definition
{
    /**
     * This static attributes state if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = false;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_interface_color", type="string", length=10, nullable=true)
     */
    private $userInterfaceColor;

    /**
     * @var string
     *
     * @ORM\Column(name="object_database_name", type="string", length=255, nullable=true)
     */
    private $objectDatabaseName;

    /**
     * @var string
     *
     * @ORM\Column(name="object_entity_name", type="string", length=255, nullable=true)
     */
    private $objectEntityName;

    /**
     * @var string
     *
     * @ORM\Column(name="object_user_interface_name", type="string", length=255, nullable=true)
     */
    private $objectUserInterfaceName;

    /**
     * @var string
     *
     * @ORM\Column(name="database_admin_definition", type="text", nullable=true)
     */
    private $databaseAdminDefinition;

    /**
     * @var string
     *
     * @ORM\Column(name="long_definition", type="text", nullable=true)
     */
    private $longDefinition;

    /**
     * @var string
     *
     * @ORM\Column(name="short_definition", type="text", nullable=true)
     */
    private $shortDefinition;

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
     * Set objectDatabaseName
     *
     * @param string $objectDatabaseName
     *
     * @return Definition
     */
    public function setObjectDatabaseName($objectDatabaseName)
    {
        $this->objectDatabaseName = $objectDatabaseName;

        return $this;
    }

    /**
     * Get objectDatabaseName
     *
     * @return string
     */
    public function getObjectDatabaseName()
    {
        return $this->objectDatabaseName;
    }

    /**
     * Set objectUserInterfaceName
     *
     * @param string $objectUserInterfaceName
     *
     * @return Definition
     */
    public function setObjectUserInterfaceName($objectUserInterfaceName)
    {
        $this->objectUserInterfaceName = $objectUserInterfaceName;

        return $this;
    }

    /**
     * Get objectUserInterfaceName
     *
     * @return string
     */
    public function getObjectUserInterfaceName()
    {
        return $this->objectUserInterfaceName;
    }

    /**
     * Set longDefinition
     *
     * @param string $longDefinition
     *
     * @return Definition
     */
    public function setLongDefinition($longDefinition)
    {
        $this->longDefinition = $longDefinition;

        return $this;
    }

    /**
     * Get longDefinition
     *
     * @return string
     */
    public function getLongDefinition()
    {
        return $this->longDefinition;
    }

    /**
     * Set shortDefinition
     *
     * @param string $shortDefinition
     *
     * @return Definition
     */
    public function setShortDefinition($shortDefinition)
    {
        $this->shortDefinition = $shortDefinition;

        return $this;
    }

    /**
     * Get shortDefinition
     *
     * @return string
     */
    public function getShortDefinition()
    {
        return $this->shortDefinition;
    }

    /**
     * Set objectEntityName
     *
     * @param string $objectEntityName
     *
     * @return Definition
     */
    public function setObjectEntityName($objectEntityName)
    {
        $this->objectEntityName = $objectEntityName;

        return $this;
    }

    /**
     * Get objectEntityName
     *
     * @return string
     */
    public function getObjectEntityName()
    {
        return $this->objectEntityName;
    }

    /**
     * Set databaseAdminDefinition
     *
     * @param string $databaseAdminDefinition
     *
     * @return Definition
     */
    public function setDatabaseAdminDefinition($databaseAdminDefinition)
    {
        $this->databaseAdminDefinition = $databaseAdminDefinition;

        return $this;
    }

    /**
     * Get databaseAdminDefinition
     *
     * @return string
     */
    public function getDatabaseAdminDefinition()
    {
        return $this->databaseAdminDefinition;
    }


    /**
     * Set userInterfaceColor
     *
     * @param string $userInterfaceColor
     *
     * @return Definition
     */
    public function setUserInterfaceColor($userInterfaceColor)
    {
        $this->userInterfaceColor = $userInterfaceColor;

        return $this;
    }

    /**
     * Get userInterfaceColor
     *
     * @return string
     */
    public function getUserInterfaceColor()
    {
        return $this->userInterfaceColor;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public function getUserInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = $this->getObjectUserInterfaceName();
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}