<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * SubTimeUnit
 *
 * @ORM\Table(name="sub_time_unit")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\SubTimeUnitRepository")
 */
class SubTimeUnit
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
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
     * @Groups({"subTimeUnit"})
     * @ORM\Column(name="sub_type", type="string", length=255)
     */
    private $subType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="definition", type="text", nullable=true)
     */
    private $definition;

    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\NumberUnit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $numberUnit;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subType.
     *
     * @param string $subType
     *
     * @return SubTimeUnit
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * Get subType.
     *
     * @return string
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * Set definition.
     *
     * @param string|null $definition
     *
     * @return SubTimeUnit
     */
    public function setDefinition($definition = null)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get definition.
     *
     * @return string|null
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set numberUnit.
     *
     * @param \TAMAS\AstroBundle\Entity\NumberUnit $numberUnit
     *
     * @return SubTimeUnit
     */
    public function setNumberUnit(\TAMAS\AstroBundle\Entity\NumberUnit $numberUnit)
    {
        $this->numberUnit = $numberUnit;

        return $this;
    }

    /**
     * Get numberUnit.
     *
     * @return \TAMAS\AstroBundle\Entity\NumberUnit
     */
    public function getNumberUnit()
    {
        return $this->numberUnit;
    }

    public function __toString()
    {
        return $this->subType;
    }
}
