<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * NumberUnit
 *
 * @ORM\Table(name="number_unit")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\NumberUnitRepository")
 */
class NumberUnit
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
     * @ORM\Column(name="unit", type="string", length=255, unique=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="code_name", type="string", length=255)
     */
    private $codeName;


    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_calendrical", type="boolean", nullable=true)
     */
    private $isCalendrical;

    public function __toString()
    {
        return $this->unit;
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
     * Set unit
     *
     * @param string $unit
     *
     * @return NumberUnit
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


    /**
     * Set codeName.
     *
     * @param string $codeName
     *
     * @return NumberUnit
     */
    public function setCodeName($codeName)
    {
        $this->codeName = $codeName;

        return $this;
    }

    /**
     * Get codeName.
     *
     * @return string
     */
    public function getCodeName()
    {
        return $this->codeName;
    }

    /**
     * Set isCalendrical.
     *
     * @param bool|null $isCalendrical
     *
     * @return NumberUnit
     */
    public function setIsCalendrical($isCalendrical = null)
    {
        $this->isCalendrical = $isCalendrical;

        return $this;
    }

    /**
     * Get isCalendrical.
     *
     * @return bool|null
     */
    public function getIsCalendrical()
    {
        return $this->isCalendrical;
    }
}
