<?php

// Symfony\src\TAMAS\AstroBundle\Entity\HistoricalActor
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\Validator\Constraints as TAMASAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * HistoricalActor
 *
 * @ORM\Table(name="historical_actor")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\HistoricalActorRepository")
 * @UniqueEntity(fields="viafIdentifier", message="This identifier is already recorded in the database")
 * @UniqueEntity(fields={"actorName", "tpq", "taq", "place"} , message="This référence is already recorded in the database")
 * @TAMASAssert\Terminus
 */
class HistoricalActor
{

    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    /**
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="actor_name", type="string", length=500)
     * @Assert\NotBlank()
     */
    private $actorName;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="actor_name_original_char", type="string", length=500, nullable=true)
     */
    private $actorNameOriginalChar;

    /**
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="tpq", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $tpq;

    /**
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="taq", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $taq;

    /**
     * @Groups({"workMain", "originalItemMain"})
     *
     * @var string
     *
     * @ORM\Column(name="viaf_identifier", type="string", length=255, nullable=true)
     */
    private $viafIdentifier;

    /**
     * @Type("DateTime")
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Type("DateTime")
     *
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
     *
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Place", cascade={"persist"})
     * @Assert\NotBlank()
     */
    private $place;
    
    /**
     * @Groups({"workMain", "originalItemMain"})
     * @var string
     */
    private $kibanaName ;
    
    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;
    

    /**
     * @PreSerialize
     *
     */
    private function onPreSerialize() {
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);
        $this->kibanaName = $this->__toString();
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

    // ____________________________ This method is added to basic entity creation in order to be able to send ID by form, when autocomplete is used. It should not be used to actually set the id. It prevents errors when sending the form.
    /**
     * Set id
     *
     * @param int $id
     *
     * @return HistoricalActor
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set actorName
     *
     * @param string $actorName
     *
     * @return HistoricalActor
     */
    public function setActorName($actorName)
    {
        $this->actorName = $actorName;

        return $this;
    }

    /**
     * Get actorName
     *
     * @return string
     */
    public function getActorName()
    {
        return $this->actorName;
    }

    /**
     * Set tpq
     *
     * @param integer $tpq
     *
     * @return HistoricalActor
     */
    public function setTpq($tpq)
    {
        $this->tpq = $tpq;

        return $this;
    }

    /**
     * Get tpq
     *
     * @return int
     */
    public function getTpq()
    {
        return $this->tpq;
    }

    /**
     * Set taq
     *
     * @param integer $taq
     *
     * @return HistoricalActor
     */
    public function setTaq($taq)
    {
        $this->taq = $taq;

        return $this;
    }

    /**
     * Get taq
     *
     * @return int
     */
    public function getTaq()
    {
        return $this->taq;
    }

    /**
     * Set viafIdentifier
     *
     * @param string $viafIdentifier
     *
     * @return HistoricalActor
     */
    public function setViafIdentifier($viafIdentifier)
    {
        $this->viafIdentifier = $viafIdentifier;

        return $this;
    }

    /**
     * Get viafIdentifier
     *
     * @return string
     */
    public function getViafIdentifier()
    {
        return $this->viafIdentifier;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return HistoricalActor
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
     * @return HistoricalActor
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
     * @return HistoricalActor
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
     * @return HistoricalActor
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
     * Set place
     *
     * @param \TAMAS\AstroBundle\Entity\Place $place
     *
     * @return HistoricalActor
     */
    public function setPlace(\TAMAS\AstroBundle\Entity\Place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \TAMAS\AstroBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    // ____________________________ This method is added to basic entity in order to be able to check in controller if form-submitted entities have only null attributes.
    // This method is used in the context or search originalText. This is still a draft method.
    // see draft section of originalTextRepository
    // public function checkIfNull() {
    // foreach ($this as $value) {
    // if ($value != null) {
    // return false;
    // }
    // }
    // return true;
    // }

    /**
     * Set actorNameOriginalChar.
     *
     * @param string $actorNameOriginalChar
     *
     * @return HistoricalActor
     */
    public function setActorNameOriginalChar($actorNameOriginalChar)
    {
        $this->actorNameOriginalChar = $actorNameOriginalChar;

        return $this;
    }

    /**
     * Get actorNameOriginalChar.
     *
     * @return string
     */
    public function getActorNameOriginalChar()
    {
        return $this->actorNameOriginalChar;
    }

    public function getTitle()
    {
        $nameOriginalChar = ($this->actorNameOriginalChar ? ' (' . $this->actorNameOriginalChar . ')' : '');
        return $this->actorName ? $this->actorName . $nameOriginalChar : "Unknown actor n°" . $this->id;
    }

    public function setTpqDate($tpqDate) {
        $this->tpqDate = $tpqDate;

        return $this;
    }

    public function setTaqDate($taqDate) {
        $this->taqDate = $taqDate;

        return $this;
    }

    public function getTpaq()
    {
        if ($this->tpq || $this->taq) {
            $tpq = $this->tpq ? $this->tpq : "?";
            $taq = $this->taq ? $this->taq : "?";
            if (($tpq == $taq) && ($tpq != "?")) {
                $date = $tpq;
            } else {
                $date = $tpq . "–" . $taq;
            }
        } else {
            $date = "Unknown time of flourishment";
        }

        return $date;
    }

    public function __toString()
    {
        $title = $this->getTitle();
        $date = $this->getTpaq();
        return "$title ($date)";
    }

    public function toPublicString()
    {
        $this->actorName?$name=strval(ucfirst($this->actorName)):$name="<span class='noInfo'>Unknown actor n°" . strval($this->id) . "</span>";
        $tpaq = "<br/>(".$this->getTpaq().")";

        $nameDate = $name . $tpaq;

        if ($this->viafIdentifier){
            $viaf = "<br/><a href='https://viaf.org/viaf/" . strval($this->viafIdentifier) . "'>" . strval($this->viafIdentifier) . "</a>";
            $nameDate = $nameDate . $viaf;
        }
        return $nameDate;
    }

    public function toPublicTitle()
    {
        $this->actorName?$name=strval(ucfirst($this->actorName)):$name="<span class='noInfo'>Unknown actor n°" . strval($this->id) . "</span>";
        $tpaq = $this->getTpaq();

        return "$name ($tpaq)";
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "historical actor";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
