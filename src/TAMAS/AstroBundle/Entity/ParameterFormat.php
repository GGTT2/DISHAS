<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * ParameterFormat
 *
 * @ORM\Table(name="parameter_format")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ParameterFormatRepository")
 */
class ParameterFormat
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = false;

    /**
     * @Type("string")
     * @var int
     *
     * @Groups({"parameterSetMain"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Groups({"parameterSetMain"})
     * @var string
     *
     * @ORM\Column(name="parameter_name", type="string", length=255)
     */
    private $parameterName;

    /**
     * @Groups({"parameterSetMain"})
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\ParameterType", cascade={"persist"})
     */
    private $parameterType;

    /**
     * @Groups({"parameterSetMain"})
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\ParameterUnit", cascade={"persist"})
     */
    private $parameterUnit;

    /**
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\ParameterFeature", cascade={"persist"})
     */
    private $parameterFeature;

    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\ParameterGroup", cascade={"persist"})
     */
    private $parameterGroup;

    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TableType", cascade={"persist"})
     */
    private $tableType;

    /**
     * 
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parameterName
     *
     * @param string $parameterName
     *
     * @return ParameterFormat
     */
    public function setParameterName($parameterName)
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    /**
     * Get parameterName
     *
     * @return string
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }

    /**
     * Set parameterType
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterType $parameterType
     *
     * @return ParameterFormat
     */
    public function setParameterType(\TAMAS\AstroBundle\Entity\ParameterType $parameterType = null)
    {
        $this->parameterType = $parameterType;

        return $this;
    }

    /**
     * @Groups({"parameterformat"})
     * Get parameterType
     *
     * @return \TAMAS\AstroBundle\Entity\ParameterType
     */
    public function getParameterType()
    {
        return $this->parameterType;
    }

    /**
     * Set parameterUnit
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterUnit $parameterUnit
     *
     * @return ParameterFormat
     */
    public function setParameterUnit(\TAMAS\AstroBundle\Entity\ParameterUnit $parameterUnit = null)
    {
        $this->parameterUnit = $parameterUnit;

        return $this;
    }

    /**
     * @Groups({"parameterformat"})
     * Get parameterUnit
     *
     * @return \TAMAS\AstroBundle\Entity\ParameterUnit
     */
    public function getParameterUnit()
    {
        return $this->parameterUnit;
    }

    /**
     * Set parameterFeature
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterFeature $parameterFeature
     *
     * @return ParameterFormat
     */
    public function setParameterFeature(\TAMAS\AstroBundle\Entity\ParameterFeature $parameterFeature = null)
    {
        $this->parameterFeature = $parameterFeature;

        return $this;
    }

    /**
     * @Groups({"parameterformat"})
     * Get parameterFeature
     *
     * @return \TAMAS\AstroBundle\Entity\ParameterFeature
     */
    public function getParameterFeature()
    {
        return $this->parameterFeature;
    }

    /**
     * Set parameterGroup
     *
     * @param \TAMAS\AstroBundle\Entity\ParameterGroup $parameterGroup
     *
     * @return ParameterFormat
     */
    public function setParameterGroup(\TAMAS\AstroBundle\Entity\ParameterGroup $parameterGroup = null)
    {
        $this->parameterGroup = $parameterGroup;

        return $this;
    }

    /**
     * Get parameterGroup
     *
     * @return \TAMAS\AstroBundle\Entity\ParameterGroup
     */
    public function getParameterGroup()
    {
        return $this->parameterGroup;
    }

    /**
     * Set tableType
     *
     * @param \TAMAS\AstroBundle\Entity\TableType $tableType
     *
     * @return ParameterFormat
     */
    public function setTableType(\TAMAS\AstroBundle\Entity\TableType $tableType = null)
    {
        $this->tableType = $tableType;

        return $this;
    }

    /**
     * @Groups({"parameterformat"})
     * Get tableType
     *
     * @return \TAMAS\AstroBundle\Entity\TableType
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    public function __toString()
    {
        return $this->parameterName;
    }

    public function toPublicString()
    {
        /* NOTE : irregularities between table type names and parameter format names
        -> hyphens instead of space
        -> double spaces
        -> space before colon
        -> name changes and spelling errors */

        $paramName = $this->parameterName;
        $name = substr($paramName, strpos($paramName, ":")+2, strlen($paramName));

        return str_replace("  ", " ", $name);
    }
}
