<?php

// Symfony\src\TAMAS\AstroBundle\Entity\PrimarySource.php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;


/**
 * PrimarySource
 *
 * @ORM\Table(name="primary_source")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\PrimarySourceRepository")
 * @UniqueEntity(fields={"shelfmark", "library"}, ignoreNull = true, message="This source is already recorded in the database")
 *
 */
class PrimarySource
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    /**
     * @Groups({"primarySourceMain"})
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
     * @Groups({"primarySourceMain"})
     *
     * @var string
     *
     * @ORM\Column(name="prim_type", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $primType;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="shelfmark", type="string", length=255, nullable=true)
     */
    private $shelfmark;

    // mandatory field for manuscript (see callback)

    /**
     * @Groups({"primarySourceMain"})
     *
     * @var string
     *
     * @ORM\Column(name="digital_identifier", type="string", length=255, nullable=true)
     */
    private $digitalIdentifier;

    /**
     * @Groups({"primarySourceMain"})
     * 
     * @var string
     *
     * @ORM\Column(name="prim_title", type="text", nullable=true)
     */
    private $primTitle;

    /**
     * @Groups({"primarySourceMain"})
     * @var string
     *
     * @ORM\Column(name="prim_title_original_char", type="text", nullable=true)
     */
    private $primTitleOriginalChar;

    /**
     * @var string
     *
     * @ORM\Column(name="small_prim_title", type="text", nullable=true)
     */
    private $smallPrimTitle;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="prim_editor", type="string", length=255, nullable=true)
     */
    private $primEditor;

    /**
     * @Type("string")
     * @Groups("primarySourceMain")
     * @var string
     *
     * @ORM\Column(name="date", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^(-)?\d{1,4}$/", message="The input should be a year e.g. -53 or 1256.")
     */
    private $date;

    /**
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
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
     * @Groups({"primarySourceMain"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Library", cascade={"persist"})
     */
    private $library;

    /**
     * @Groups({"primarySourceMain"})
     * @var string
     */
    private $kibanaName;

    /**
     * @Groups({"primarySourceMain"})
     * @var string
     */
    private $defaultTitle;

    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;


    /**
     * @Groups({"externalOriginalTextPS"})
     * @ORM\OneToMany(targetEntity="TAMAS\AstroBundle\Entity\OriginalText", mappedBy="primarySource", cascade={"persist"})
     */
    private $originalTexts;

    /**
     * @Groups({"primarySourceMain"})
     * @Type("string")
     */
    private $tpq;

    /**
     * @Groups({"primarySourceMain"})
     * @Type("string")
     */
    private $taq;

    /**
     * @Groups({"primarySourceMain"})
     */
    private $tpqDate;

    /**
     * @Groups({"primarySourceMain"})
     */
    private $taqDate;

    /**
     * @Groups({"primarySourceMain"})
     */
    private $places;

    /**
     * @PreSerialize
     */
    private function onPreSerialize()
    {
        $this->setPrimarySourceTerminus(true);
        GT::setValidDateFromYear($this, "tpq", "tpqDate", true);
        GT::setValidDateFromYear($this, "taq", "taqDate", true);
        $this->setPlaces();
        $this->kibanaName = $this->__toString();

        foreach($this->originalTexts as $ot){
            if(!$ot->getPublic()){
                $this->removeOriginalText($ot);   
            }
        }

        $shelfmark = $this->shelfmark ? $this->shelfmark : "Unknown ref n°" . $this->id;
        $library = $this->library ? $this->library->getLibraryName() : "Unknown place of curation";
        $this->defaultTitle = "$shelfmark | $library";

        $this->kibanaId = PreSerializeTools::generateKibanaId($this);
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
     * @return PrimarySource
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set primType
     *
     * @param string $primType
     *
     * @return PrimarySource
     */
    public function setPrimType($primType)
    {
        $this->primType = $primType;

        return $this;
    }

    /**
     * Get primType
     *
     * @return string
     */
    public function getPrimType()
    {
        return $this->primType;
    }

    /**
     * Set shelfmark
     *
     * @param string $shelfmark
     *
     * @return PrimarySource
     */
    public function setShelfmark($shelfmark)
    {
        $this->shelfmark = $shelfmark;

        return $this;
    }

    /**
     * Get shelfmark
     *
     * @return string
     */
    public function getShelfmark()
    {
        return $this->shelfmark;
    }

    /**
     * Set digitalIdentifier
     *
     * @param string $digitalIdentifier
     *
     * @return PrimarySource
     */
    public function setDigitalIdentifier($digitalIdentifier)
    {
        $this->digitalIdentifier = $digitalIdentifier;

        return $this;
    }

    /**
     * Get digitalIdentifier
     *
     * @return string
     */
    public function getDigitalIdentifier()
    {
        return $this->digitalIdentifier;
    }

    /**
     * Set primTitle
     *
     * @param string $primTitle
     *
     * @return PrimarySource
     */
    public function setPrimTitle($primTitle)
    {
        $this->primTitle = $primTitle;

        if (strlen($primTitle) > 50) {
            $smallPrimTitle = mb_substr($primTitle, 0, 50) . "…";
        } else {
            $smallPrimTitle = $primTitle;
        }
        $this->setSmallPrimTitle($smallPrimTitle);

        return $this;
    }

    /**
     * Get primTitle
     *
     * @return string
     */
    public function getPrimTitle()
    {
        return $this->primTitle;
    }

    /**
     * Set primEditor
     *
     * @param string $primEditor
     *
     * @return PrimarySource
     */
    public function setPrimEditor($primEditor)
    {
        $this->primEditor = $primEditor;

        return $this;
    }

    /**
     * Get primEditor
     *
     * @return string
     */
    public function getPrimEditor()
    {
        return $this->primEditor;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return PrimarySource
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
     * @return PrimarySource
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
     * @return PrimarySource
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
     * @return PrimarySource
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
     * Set library
     *
     * @param \TAMAS\AstroBundle\Entity\Library $library
     *
     * @return PrimarySource
     */
    public function setLibrary(\TAMAS\AstroBundle\Entity\Library $library = null)
    {
        $this->library = $library;

        return $this;
    }

    /**
     * Get library
     *
     * @return \TAMAS\AstroBundle\Entity\Library
     */
    public function getLibrary()
    {
        return $this->library;
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
     * Set smallPrimTitle
     *
     * @param string $smallPrimTitle
     *
     * @return PrimarySource
     */
    public function setSmallPrimTitle($smallPrimTitle)
    {
        $this->smallPrimTitle = $smallPrimTitle;

        return $this;
    }

    /**
     * Get smallPrimTitle
     *
     * @return string
     */
    public function getSmallPrimTitle()
    {
        return $this->smallPrimTitle;
    }

    /**
     * Set date.
     *
     * @param string|null $date
     *
     * @return PrimarySource
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return string|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     *
     * @Assert\Callback
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    function isManuscriptValid(\Symfony\Component\Validator\Context\ExecutionContextInterface $context)
    {
        if ($this->shelfmark == "") {
            $context->buildViolation("This value should not be blank")
                ->atPath('shelfmark')
                ->addViolation();
        }
        if ($this->library == null) {
            $context->buildViolation("This value should not be blank")
                ->atPath('library')
                ->addViolation();
        }
        if ($this->getPrimType() == "ep") {
            if ($this->primEditor == null) {
                $context->buildViolation("This value should not be blank")
                    ->atPath('primEditor')
                    ->addViolation();
            }
            if ($this->primTitle == null) {
                $context->buildViolation("This value should not be blank")
                    ->atPath('primTitle')
                    ->addViolation();
            }
            if ($this->date == null) {
                $context->buildViolation("This value should not be blank")
                    ->atPath('date')
                    ->addViolation();
            }
        }
    }

    /**
     * Set primTitleOriginalChar.
     *
     * @param string|null $primTitleOriginalChar
     *
     * @return PrimarySource
     */
    public function setPrimTitleOriginalChar($primTitleOriginalChar = null)
    {
        $this->primTitleOriginalChar = $primTitleOriginalChar;

        return $this;
    }

    /**
     * Get primTitleOriginalChar.
     *
     * @return string|null
     */
    public function getPrimTitleOriginalChar()
    {
        return $this->primTitleOriginalChar;
    }

    /**
     * Add originalText
     *
     * @param \TAMAS\AstroBundle\Entity\OriginalText $originalText
     *
     * @return PrimarySource
     */
    public function addOriginalText(\TAMAS\AstroBundle\Entity\OriginalText $originalText)
    {
        $this->originalTexts[] = $originalText;

        return $this;
    }

    /**
     * Remove parameterSet
     *
     * @param \TAMAS\AstroBundle\Entity\OriginalText $originalText
     */
    public function removeOriginalText(\TAMAS\AstroBundle\Entity\OriginalText $originalText)
    {
        $this->originalTexts->removeElement($originalText);
    }


    /**
     * Get getOriginalTexts.
     *
     * @param bool $onlyPublic : to specify if only public original texts are needed
     * @return array
     */
    public function getOriginalTexts($onlyPublic = false)
    {
        if ($onlyPublic) {
            $originalTexts = $this->originalTexts;
            $publicOriginalTexts = [];
            foreach ($originalTexts as $originalText) {
                if ($originalText->getPublic()) {
                    $publicOriginalTexts[] = $originalText;
                }
            }
            return $publicOriginalTexts;
        }
        return $this->originalTexts->toArray();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->originalTexts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setPrimarySourceTerminus($onlyPublic = false)
    {
        $originalTexts = $this->getOriginalTexts($onlyPublic);
        $datePerMS = [];

        foreach ($originalTexts as $originalText) {
            if ($originalText->getTpq())
                $datePerMS[] = $originalText->getTpq();
            if ($originalText->getTaq())
                $datePerMS[] = $originalText->getTaq();
        }
        if (!empty($datePerMS)) {
            $this->tpq = min($datePerMS);
            $this->taq = max($datePerMS);
        }
        return $this;
    }

    public function getTpq($onlyPublic = false)
    {
        if ($onlyPublic) {
            $this->setPrimarySourceTerminus(true);
        } else {
            $this->setPrimarySourceTerminus();
        }
        return $this->tpq;
    }

    public function setTpq($tpq)
    {
        $this->tpq = $tpq;

        return $this;
    }

    public function setTpqDate($tpqDate)
    {
        $this->tpqDate = $tpqDate;

        return $this;
    }

    public function setTaqDate($taqDate)
    {
        $this->taqDate = $taqDate;

        return $this;
    }

    public function getTaq($onlyPublic = false)
    {
        if ($onlyPublic) {
            $this->setPrimarySourceTerminus(true);
        } else {
            $this->setPrimarySourceTerminus();
        }
        return $this->taq;
    }

    public function setTaq($taq)
    {
        $this->taq = $taq;

        return $this;
    }

    /**
     * Returns the dates of a primary source correctly formatted
     *
     * @param bool $onlyPublic
     * @param bool $withHTML
     * @return string
     */
    public function getTpaq($onlyPublic = false, $withHTML = true)
    {
        if ($onlyPublic) {
            $this->setPrimarySourceTerminus(true);
        } else {
            $this->setPrimarySourceTerminus();
        }

        return GT::getTpaq($this->tpq, $this->taq, "<span class='noInfo'>Unknown time of creation</span>", $withHTML);
    }

    public function setPlaces($onlyPublic = false)
    {
        $places = [];
        $originalTexts = $onlyPublic ? $this->getOriginalTexts(true) : $this->originalTexts;

        if ($originalTexts) {
            foreach ($originalTexts as $originalText) {
                if ($originalText->getPlace()) {
                    $places[$originalText->getPlace()->getId()] = $originalText->getPlace();
                }
            }
            $this->places = array_values($places);
        }
        //$this->places = array_values($places);

        return $this;
    }

    public function getPlaces($onlyPublic = false)
    {
        if ($onlyPublic) {
            $this->setPlaces(true);
        } else {
            $this->setPlaces();
        }
        return $this->places;
    }

    public function getActors($onlyPublic = false)
    {
        $actors = [];
        $originalTexts = $onlyPublic ? $this->getOriginalTexts(true) : $this->originalTexts;

        if ($originalTexts) {
            foreach ($originalTexts as $originalText) {
                if ($originalText->getHistoricalActor()) {
                    $actors[$originalText->getHistoricalActor()->getId()] = $originalText->getHistoricalActor();
                }
            }
        }
        return array_values($actors);
    }

    public function __toString()
    {
        if ($this->primType == 'ms') {
            return $this->getTitle();
        }
        return $this->getBibRef().", ".$this->getTitle() ;
    }

    /**
     * =========== construct toString()
     */
    public function getTitle()
    {
        if ($this->primType == "ms") {
            return $this->getBibRef();
        } else {
            $titleOriginalChar = ($this->primTitleOriginalChar ? ' (' . $this->primTitleOriginalChar . ')' : '');
            $title = $this->primTitle ? $this->primTitle : "Unknown ref n°" . $this->id;
            $primEditor = $this->primEditor ? $this->primEditor : "Unknown editor";
            $date = $this->date ? $this->date : "Unknown year";
            return $title . $titleOriginalChar . ", " . $primEditor . ", " . $date;
        }
    }

    public function getBibRef()
    {
        $shelfmark = $this->shelfmark ? $this->shelfmark : "Unknown ref n°" . $this->id;
        $library = $this->library ? $this->library->toPublicTitle() : "Unknown place of curation";
        return $library . " | " . $shelfmark;
    }

    /**
     * This method returns a html string that can be used as a metadata in the public interface
     *
     * "<span class='mainContent'>Early printed</span><br/>
     * <i>Title</i>, Editor, Date"
     *
     * OR
     *
     * "<span class='mainContent'>Manuscript</span><br/>
     * Library | Shelfmark"
     *
     * @return string
     */
    public function toPublicString()
    {
        $library = $this->library ? $this->library : "<span class='noInfo'>Unknown place of curation</span>";
        $shelfmark = $this->shelfmark ? $this->shelfmark : "<span class='noInfo'>Unknown ref n°" . $this->id . "</span>";
        $primTitle = $this->primTitle ? $this->primTitle : "<span class='noInfo'>Unknown ref n°" . $this->id . "</span>";
        $primEditor = $this->primEditor ? $this->primEditor : "<span class='noInfo'>Unknown editor</span>";
        $date = $this->date ? $this->date : "<span class='noInfo'>Unknown year of edition</span>";

        if ($this->primType == "ms") {
            return "<span class='mainContent'>Manuscript</span><br/>" . strval($library) . " | " . strval($shelfmark);
        } elseif ($this->primType == "ep") {
            return "<span class='mainContent'>Early printed</span><br/><i>" . strval($primTitle) . "</i>, " . strval($primEditor) . ", " . strval($date);
        } else {
            return "<span class='mainContent'>" . ucfirst(PrimarySource::getInterfaceName()) . "</span><br/>" . strval($library) . " | " . strval($shelfmark);
        }
    }

    /**
     * This method returns a html string that can be used as a title in the public interface
     *
     * "<b>Early printed</b><br/>
     * <i>Title</i>, Editor, Date"
     *
     * OR
     *
     * "<b>Manuscript</b><br/>
     * Library | Shelfmark"
     *
     * @param $showPrimType boolean
     * @return string
     */
    public function toPublicTitle($showPrimType = true)
    {
        if ($this->primType == "ms") {
            $library = $this->library ? $this->library : "<span class='noInfo'>Unknown place of curation</span>";
            $shelfmark = $this->shelfmark ? $this->shelfmark : "<span class='noInfo'>Unknown ref n°" . $this->id . "</span>";
            $primType = $showPrimType ? "<b>Manuscript</b><br/>" : "";
            return "$primType $library | $shelfmark";
        } else {
            $primTitle = $this->primTitle ? $this->primTitle : "<span class='noInfo'>Unknown ref n°" . $this->id . "</span>";
            $primEditor = $this->primEditor ? $this->primEditor : "<span class='noInfo'>Unknown editor</span>";
            $date = $this->date ? $this->date : "<span class='noInfo'>Unknown year of edition</span>";
            $primType = $showPrimType ? "<b>Early printed</b><br/>" : "";
            return $primType . "<i>" . strval($primTitle) . "</i>, " . strval($primEditor) . ", " . strval($date);
        }
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "primary source";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
