<?php

// Symfony\src\TAMAS\AstroBundle\Entity\Place.php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * Place
 *
 * @ORM\Table(name="place")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\PlaceRepository")
 * @UniqueEntity(fields={"placeName", "placeLat", "placeLong"}, ignoreNull = false, message="This place is already recorded in the database")
 */
class Place
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
     * @ORM\Column(name="place_name", type="string", length=200, nullable=true)
     * @Assert\NotBlank()
     */
    private $placeName;

    /**
     * @var string
     *
     * @ORM\Column(name="place_name_original_char", type="string", length=200, nullable=true)
     */
    private $placeNameOriginalChar;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="place_lat", type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^(\+|-)?\d+(.)?\d+?$/", message="The input should be a correct latitude.")
     * @Assert\Range(min = -89.5, max = 89.5)
     */
    private $placeLat;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="place_long", type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^(\+|-)?\d+(.)?\d+?$/", message="The input should be a correct longitude.")
     * @Assert\Range(min = -180.0, max = 180)
     */
    private $placeLong;

    /**
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     *
     */
    private $created;

    /**
     *
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     *
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $createdBy;

    /**
     *
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;
    

    /**
     * @Groups({"originalTextMain", "workMain"})
     * 
     */
    private $location;
    
    /**
     * @Groups({"originalTextMain", "workMain"})
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
    private function onPreSerialize(){
        $this->kibanaName = $this->__toString();
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);

        $this->location = $this->placeLat != null && $this->placeLong != null ? strval($this->placeLat) . "," . strval($this->placeLong) : null;
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
     * Set placeName
     *
     * @param string $placeName
     *
     * @return Place
     */
    public function setPlaceName($placeName)
    {
        $this->placeName = $placeName;

        return $this;
    }

    /**
     * Get placeName
     *
     * @return string
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * Set placeLat
     *
     * @param string $placeLat
     *
     * @return Place
     */
    public function setPlaceLat($placeLat)
    {
        $this->placeLat = $placeLat;

        return $this;
    }

    /**
     * Get placeLat
     *
     * @return string
     */
    public function getPlaceLat()
    {
        return $this->placeLat;
    }

    /**
     * Set placeLong
     *
     * @param string $placeLong
     *
     * @return Place
     */
    public function setPlaceLong($placeLong)
    {
        $this->placeLong = $placeLong;

        return $this;
    }

    /**
     * Get placeLong
     *
     * @return string
     */
    public function getPlaceLong()
    {
        return $this->placeLong;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Place
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
     * @return Place
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
     * @return Place
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
     * @return Place
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

    /**
     * getLong
     *
     * this method returns a the longitude value or 0
     *
     * @return float
     */
    public function getLong()
    {
        $long=$this->placeLong?$this->placeLong:0;

        return $long;
    }

    /**
     * getLat
     *
     * this method returns a the latitude value or 0
     *
     * @return float
     */
    public function getLat()
    {
        $lat=$this->placeLat?$this->placeLat:0;

        return $lat;
    }

    /**
     * Set placeNameOriginalChar.
     *
     * @param string|null $placeNameOriginalChar
     *
     * @return Place
     */
    public function setPlaceNameOriginalChar($placeNameOriginalChar = null)
    {
        $this->placeNameOriginalChar = $placeNameOriginalChar;

        return $this;
    }

    /**
     * Get placeNameOriginalChar.
     *
     * @return string|null
     */
    public function getPlaceNameOriginalChar()
    {
        return $this->placeNameOriginalChar;
    }

    public function __toString()
    {
        $nameOriginalChar = ($this->placeNameOriginalChar ? ' (' . $this->placeNameOriginalChar . ')' : '');
        $id = $this->id;
        if ($this->placeName !== null) {
            $title = $this->placeName;
        } else {
            $title = "Unknown place n°" . $id;
        }
        return $title . $nameOriginalChar;
    }

    /**
     * This method returns a html string that can be used in the public interface
     * @return string
     */
    public function toPublicString()
    {
        return $this->placeName ? $this->placeName : "<span class='noInfo'>Unknown place n°" . strval($this->id) . "</span>";
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "place";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
