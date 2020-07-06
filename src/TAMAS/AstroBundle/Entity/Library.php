<?php
//Symfony\src\TAMAS\AstroBundle\Entity\Library.php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Intl;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;


/**
 * Library
 *
 * @ORM\Table(name="library")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\LibraryRepository")
 * @UniqueEntity(fields="libraryName", message="This library name is already recorded in the database") 
 * @UniqueEntity(fields="libraryIdentifier" , message="This identifier is already recorded in the database") 
 */
class Library
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

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
     * @ORM\Column(name="library_name", type="string", length=255)
     * @Assert\NotNull()
     */
    private $libraryName;

    /**
     * @var string
     *
     * @ORM\Column(name="library_country", type="string", length=100, nullable=true)
     * @Assert\NotNull()
     */
    private $libraryCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="library_identifier", type="string", length=100, nullable=true)
     */
    private $libraryIdentifier;

    /**
     * @var string
     * 
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Assert\NotNull()
     */
    private $city;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @Groups({"primarySourceMain"})
     * @var string
     */
    private $kibanaName;


    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;


    /**
     * @PreSerialize
     */
    private function onPreSerialize()
    {
        $this->kibanaName = $this->__toString();
        $this->kibanaId = \TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools::generateKibanaId($this);
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
     * Set libraryName
     *
     * @param string $libraryName
     *
     * @return Library
     */
    public function setLibraryName($libraryName)
    {
        $this->libraryName = $libraryName;

        return $this;
    }

    /**
     * Get libraryName
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Set libraryCountry
     *
     * @param string $libraryCountry
     *
     * @return Library
     */
    public function setLibraryCountry($libraryCountry)
    {
        $this->libraryCountry = $libraryCountry;

        return $this;
    }

    /**
     * Get libraryCountry
     *
     * @return string
     */
    public function getLibraryCountry()
    {
        return $this->libraryCountry;
    }

    /**
     * Set libraryIdentifier
     *
     * @param string $libraryIdentifier
     *
     * @return Library
     */
    public function setLibraryIdentifier($libraryIdentifier)
    {
        $this->libraryIdentifier = $libraryIdentifier;

        return $this;
    }

    /**
     * Get libraryIdentifier
     *
     * @return string
     */
    public function getLibraryIdentifier()
    {
        return $this->libraryIdentifier;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Library
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Library
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Library
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set createdBy
     *
     * @param \TAMAS\AstroBundle\Entity\Users $createdBy
     *
     * @return Library
     */
    public function setCreatedBy(\TAMAS\AstroBundle\Entity\Users $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \TAMAS\AstroBundle\Entity\Users $updatedBy
     *
     * @return Library
     */
    public function setUpdatedBy(\TAMAS\AstroBundle\Entity\Users $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /* public function getLibraryCountryName() #THIS DOES NOT WORK IN THE SERVER
    {
        return $this->libraryCountry ? Intl::getRegionBundle()->getCountryName($this->libraryCountry) : "";
    } */

    public function __toString()
    {
        if ($this->libraryName) {
            $title = $this->libraryName;
        } else {
            $title = "Unknown library n°" . $this->id;
        }

        $city = $this->city ? $this->city . ", " : "";
        $country = $this->libraryCountry ? $this->libraryCountry . ", " : "";
        return $city . $country . $title;
    }

    public function toPublicTitle()
    {
        if ($this->libraryName) {
            $title = $this->libraryName;
        } else {
            $title = "Unknown library n°" . $this->id;
        }
        $city = $this->city ? $this->city . "," : "";

        return "$city $title";
    }

    public function toPublicString()
    {
        //$country = Intl::getRegionBundle()->getCountryName($this->libraryCountry);

        $country = $this->libraryCountry;
        $libName = $this->libraryName ? "<span class='mainContent'>" . strval($this->libraryName) . "</span>" : "<span class='noInfo'>Unknown library n°" . strval($this->id) . "</span>";
        $libName = $this->city ? $libName . "<br/>" . strval($this->city) : $libName;
        $libName = $country ? $libName . ", " . strval($country) : $libName;
        $libId = $this->libraryIdentifier ? "<br/><a href='http://www.isni.org/" . strval($this->libraryIdentifier) . "'>" . strval($this->libraryIdentifier) . "</a>" : "";

        return $libName . $libId;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "library";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
