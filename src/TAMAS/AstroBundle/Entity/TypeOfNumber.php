<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * TypeOfNumber
 *
 * @ORM\Table(name="type_of_number")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\TypeOfNumberRepository")
 */
class TypeOfNumber
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
     * @ORM\Column(name="type_name", type="string", length=255)
     */
    private $typeName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code_name", type="string", length=255)
     */
    private $codeName;

    /**
     * @var string
     *
     * @ORM\Column(name="type_definition", type="string", length=1000, nullable=true)
     */
    private $typeDefinition;
    
    /**
     * @var string
     *
     * @ORM\Column(name="base_JSON", type="string", length=1000, nullable=false)
     */
    private $baseJSON;
    
    /**
     * @var string
     *
     * @ORM\Column(name="integer_separator_JSON", type="string", length=1000, nullable=true)
     */
    private $integerSeparatorJSON;

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
     * Set typeName
     *
     * @param string $typeName
     *
     * @return TypeOfNumber
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * Get typeName
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set typeDefinition
     *
     * @param string $typeDefinition
     *
     * @return TypeOfNumber
     */
    public function setTypeDefinition($typeDefinition)
    {
        $this->typeDefinition = $typeDefinition;

        return $this;
    }

    /**
     * Get typeDefinition
     *
     * @return string
     */
    public function getTypeDefinition()
    {
        return $this->typeDefinition;
    }

    /**
     * Set baseJSON
     *
     * @param string $baseJSON
     *
     * @return TypeOfNumber
     */
    public function setBaseJSON($baseJSON)
    {
        $this->baseJSON = $baseJSON;

        return $this;
    }

    /**
     * Get baseJSON
     *
     * @return string
     */
    public function getBaseJSON()
    {
        return $this->baseJSON;
    }

    /**
     * Set codeName
     *
     * @param string $codeName
     *
     * @return TypeOfNumber
     */
    public function setCodeName($codeName)
    {
        $this->codeName = $codeName;

        return $this;
    }

    /**
     * Get codeName
     *
     * @return string
     */
    public function getCodeName()
    {
        return $this->codeName;
    }

    /**
     * Set integerSeparatorJSON
     *
     * @param string $integerSeparatorJSON
     *
     * @return TypeOfNumber
     */
    public function setIntegerSeparatorJSON($integerSeparatorJSON)
    {
        $this->integerSeparatorJSON = $integerSeparatorJSON;

        return $this;
    }

    /**
     * Get integerSeparatorJSON
     *
     * @return string
     */
    public function getIntegerSeparatorJSON()
    {
        return $this->integerSeparatorJSON;
    }

    public function __toString()
    {
        return $this->typeName;
    }

    public function toPublicString()
    {
        return $this->typeName."
                <button type='button' class='btn' data-container='body' data-toggle='popover' data-placement='bottom' data-html='true' data-trigger='focus' 
                        data-original-title='About this type of number' 
                        data-content=".$this->typeDefinition." 
                        title='' style='background-color: rgb(250,250,250); padding: 0;'>
                    <img src='/img/question.svg' style='height: 16px; margin-left: 6px; margin-top: -5px' alt='Question mark'>
                </button>";
    }

    public function getShortName()
    {
        $shortNames = [
            "sexagesimal" => "sexa.",
            "floating sexagesimal" => "float. sexa.",
            "historical" => "hist.",
            "integer and sexagesimal" => "int. sexa.",
            "historical decimal" => "hist. dec.",
            "temporal" => "temp.",
            "decimal" => "dec."
        ];
        $typeName = $this->typeName;
        return $shortNames[$typeName] ? $shortNames[$typeName] : $typeName;
    }
}
