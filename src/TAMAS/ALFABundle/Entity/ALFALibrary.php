<?php

namespace TAMAS\ALFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Library
 *
 * @ORM\Table(name="alfa_library")
 * @ORM\Entity(repositoryClass="TAMAS\ALFABundle\Repository\LibraryRepository")
 */
class ALFALibrary
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=1000, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=1000, nullable=true)
     */
    private $city;


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
     * Set name.
     *
     * @param string|null $name
     *
     * @return Library
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set city.
     *
     * @param string|null $city
     *
     * @return Library
     */
    public function setCity($city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }
}
