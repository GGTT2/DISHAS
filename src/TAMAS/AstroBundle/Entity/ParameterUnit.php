<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

/**
 * ParameterUnit
 *
 * @ORM\Table(name="parameter_unit")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ParameterUnitRepository")
 */
class ParameterUnit
{

    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = false;

    /**
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Groups({"parameterSetMain"})
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=255, unique=true)
     */
    private $unit;


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
     * Set unit
     *
     * @param string $unit
     *
     * @return ParameterUnit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    public function __toString()
    {
        return $this->unit;
    }
}
