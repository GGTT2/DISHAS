<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

/**
 * ParameterFeature
 *
 * @ORM\Table(name="parameter_feature")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ParameterFeatureRepository")
 */
class ParameterFeature
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
     * @ORM\Column(name="feature", type="string", length=255, unique=true)
     */
    private $feature;


    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set feature
     *
     * @param string $feature
     *
     * @return ParameterFeature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * Get feature
     *
     * @return string
     */
    public function getFeature()
    {
        return $this->feature;
    }

    public function __toString()
    {
        return $this->feature;
    }   

}

