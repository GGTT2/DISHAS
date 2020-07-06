<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

/**
 * ParameterGroup
 *
 * @ORM\Table(name="parameter_group")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ParameterGroupRepository")
 */
class ParameterGroup
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
     * @ORM\Column(name="group_name", type="string", length=255, nullable=true)
     */
    private $groupName;

    /**
     * @var string
     *
     * @ORM\Column(name="group_color", type="string", length=255, nullable=true)
     */
    private $groupColor;


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
     * Set groupName
     *
     * @param string $groupName
     *
     * @return ParameterGroup
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set groupColor
     *
     * @param string $groupColor
     *
     * @return ParameterGroup
     */
    public function setGroupColor($groupColor)
    {
        $this->groupColor = $groupColor;

        return $this;
    }

    /**
     * Get groupColor
     *
     * @return string
     */
    public function getGroupColor()
    {
        return $this->groupColor;
    }
}

