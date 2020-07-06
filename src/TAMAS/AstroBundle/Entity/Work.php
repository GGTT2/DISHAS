<?php

// Symfony\src\TAMAS\AstroBundle\Entity\Work.php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\Validator\Constraints as TAMASAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * Work
 *
 * @ORM\Table(name="work")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\WorkRepository")
 * @UniqueEntity(fields={"incipit", "historicalActors"}, repositoryMethod="findUniqueEntity", ignoreNull=true, message="This source is already recorded in the database")
 * @UniqueEntity(fields={"title", "historicalActors"}, repositoryMethod="findUniqueEntity", ignoreNull=true, message="This source is already recorded in the database")
 * @TAMASAssert\Terminus
 */
class Work
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    /**
     * @Groups({"workMain"})
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
     * @Groups({"workMain"})
     *
     * @var string
     *
     * @ORM\Column(name="incipit", type="text", nullable=true)
     */
    private $incipit;

    /**
     * @Groups({"workMain"})
     *
     * @var string
     *
     * @ORM\Column(name="incipit_original_char", type="text", nullable=true)
     */
    private $incipitOriginalChar;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="small_incipit", type="text", nullable=true)
     */
    private $smallIncipit;

    /**
     * @Groups({"workMain"})
     *
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @Groups({"workMain"})
     *
     * @var string
     *
     * @ORM\Column(name="title_original_char", type="text", nullable=true)
     */
    private $titleOriginalChar;

    /**
     * @Groups({"workMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="taq", type="integer", nullable=true)
     * @Assert\NotBlank()
     *
     */
    private $taq;

    // @TAMASAssert\HistoricalDate

    /**
     * @Groups({"workMain"})
     * @Type("string")
     *
     * @var int
     * @Assert\NotBlank()
     * @ORM\Column(name="tpq", type="integer", nullable=true)
     *
     */
    private $tpq;


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
     * @Groups({"workMain"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Place", cascade={"persist"})
     * @Assert\NotBlank()
     */
    private $place;

    /**
     * @Groups({"workMain"})
     *
     * @ORM\ManyToMany(targetEntity="TAMAS\AstroBundle\Entity\HistoricalActor", cascade={"persist"})
     */
    private $historicalActors;

    /**
     * @Groups({"workMain"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\HistoricalActor", cascade={"persist"})
     */
    private $translator;

    /**
     * @Groups({"externalOriginalTextWork", "historic"})
     * @ORM\OneToMany(targetEntity="TAMAS\AstroBundle\Entity\OriginalText", mappedBy="work", cascade={"persist"})
     */
    private $originalTexts;

    /**
     * @Groups({"workMain"})
     *
     */
    private $tpqDate;

    /**
     * @Groups({"workMain"})
     *
     */
    private $taqDate;

    /**
     * @Groups({"workMain"})
     * @var string
     */
    private $kibanaName;

    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;

    /**
     * @Groups({"workMain"})
     */
    private $defaultTitle;

    /**
     * @PreSerialize
     *
     */
    private function onPreSerialize()
    {
        GT::setValidDateFromYear($this, "tpq", "tpqDate");
        GT::setValidDateFromYear($this, "taq", "taqDate");

        $this->defaultTitle = $this->title ? $this->title : $this->incipit; // if title is set, it's the default title, else it's the incipit

        $this->kibanaName = $this->__toString();
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);
        foreach($this->originalTexts as $ot){
            if(!$ot->getPublic()){
                $this->removeOriginalText($ot);   
            }
        }
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
     * Set id
     *
     * @param int $id
     *
     * @return Work
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set incipit
     *
     * @param string $incipit
     *
     * @return Work
     */
    public function setIncipit($incipit)
    {
        $this->incipit = $incipit;
        if (strlen($incipit) > 50) {
            $smallIncipit = mb_substr($incipit, 0, 50) . "…";
        } else {
            $smallIncipit = $incipit;
        }
        $this->setSmallIncipit($smallIncipit);

        return $this;
    }

    /**
     * Get incipit
     *
     * @return string
     */
    public function getIncipit()
    {
        return $this->incipit;
    }

    /**
     * Set taq
     *
     * @param integer $taq
     *
     * @return Work
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
     * Set tpq
     *
     * @param integer $tpq
     *
     * @return Work
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Work
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
     * @return Work
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
     * @return Work
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
     * @return Work
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
     * @return Work
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

    /**
     * Set translator
     *
     * @param \TAMAS\AstroBundle\Entity\HistoricalActor $translator
     *
     * @return Work
     */
    public function setTranslator(\TAMAS\AstroBundle\Entity\HistoricalActor $translator = null)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Get translator
     *
     * @return \TAMAS\AstroBundle\Entity\HistoricalActor
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->historicalActors = new \Doctrine\Common\Collections\ArrayCollection();

        $this->originalTexts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add historicalActor
     *
     * @param \TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor
     *
     * @return Work
     */
    public function addHistoricalActor(\TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor)
    {
        $this->historicalActors[] = $historicalActor;
        return $this;
    }

    /**
     * Remove historicalActor
     *
     * @param \TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor
     */
    public function removeHistoricalActor(\TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor)
    {
        $this->historicalActors->removeElement($historicalActor);
    }

    /**
     * Get historicalActors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoricalActors()
    {
        return $this->historicalActors;
    }

    /**
     * Set smallIncipit
     *
     * @param string $smallIncipit
     *
     * @return Work
     */
    public function setSmallIncipit($smallIncipit)
    {
        $this->smallIncipit = $smallIncipit;

        return $this;
    }

    /**
     * Get smallIncipit
     *
     * @return string
     */
    public function getSmallIncipit()
    {
        return $this->smallIncipit;
    }

    // ____________________________ This method is added to basic entity in order to be able to check in controller if form-submitted entities have only null attributes.
    // This method is used in the context or search originalText. This is still a draft method.
    // see draft section of originalTextRepository
    // public function checkIfNull() {
    // //special case : we have to check also array collection. It must be turned into an array. We can't access it through the foreach loop, as it is seen as an object.
    // if (!empty($this->historicalActors->toArray())) {
    // return false;
    // }
    // foreach ($this as $value) {
    // if ($value != null && !is_object($value)) {
    // return false;
    // }
    // }
    // return true;
    // }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Work
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context)
    {
        if ($this->title == null && $this->incipit == null) {
            $context->buildViolation('At least one of the fields title or incipit must be filled')
                ->atPath('title')
                ->addViolation();
        }

        if ($this->incipitOriginalChar && !$this->incipit) {
            $context->buildViolation('This value should not be blank')
                ->atPath('incipit')
                ->addViolation();
        }
        if ($this->titleOriginalChar && !$this->title) {
            $context->buildViolation('This value should not be blank')
                ->atPath('title')
                ->addViolation();
        }
        return;
    }

    /**
     * Set incipitOriginalChar.
     *
     * @param string|null $incipitOriginalChar
     *
     * @return Work
     */
    public function setIncipitOriginalChar($incipitOriginalChar = null)
    {
        $this->incipitOriginalChar = $incipitOriginalChar;

        return $this;
    }

    /**
     * Get incipitOriginalChar.
     *
     * @return string|null
     */
    public function getIncipitOriginalChar()
    {
        return $this->incipitOriginalChar;
    }

    /**
     * Set titleOriginalChar.
     *
     * @param string|null $titleOriginalChar
     *
     * @return Work
     */
    public function setTitleOriginalChar($titleOriginalChar = null)
    {
        $this->titleOriginalChar = $titleOriginalChar;

        return $this;
    }

    /**
     * Get titleOriginalChar.
     *
     * @return string|null
     */
    public function getTitleOriginalChar()
    {
        return $this->titleOriginalChar;
    }

    /**
     * Add originalText
     *
     * @param \TAMAS\AstroBundle\Entity\OriginalText $originalText
     * @return Work
     */
    public function addOriginalText(\TAMAS\AstroBundle\Entity\OriginalText $originalText)
    {

        $this->originalTexts[] = $originalText;

        return $this;
    }

    /**
     * Remove originalText
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
     * * toString() method **
     */
    public function getStringTitle()
    {
        $leftToRight = mb_convert_encoding('&#8234;', 'UTF-8', 'HTML-ENTITIES');
        if ($this->title !== null) {
            $titleOriginalChar = $this->titleOriginalChar ? " (".mb_substr($this->titleOriginalChar, 0, 15)."…)" : '';
            $title = $this->title . $titleOriginalChar . json_decode('"' . $leftToRight . '"');
        } else {
            if ($this->incipit !== null) {
                $incipitOriginalChar = $this->incipitOriginalChar ? " (".mb_substr($this->incipitOriginalChar, 0, 15)."…)" : '';

                $title = $this->incipit . $incipitOriginalChar . json_decode('"' . $leftToRight . '"');
            } else {
                $entityName = Work::getInterfaceName();
                $title = "Untitled $entityName n°" . $this->id;
            }
        }
        return $title;
    }


    public function getTpaq()
    {
        return GT::getTpaq($this->tpq, $this->taq, "Unknown time of conception");
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

    public function getStringActors()
    {
        $creators = '';
        foreach ($this->historicalActors as $historicalActor) {
            $actor = $historicalActor->getActorName() ? $historicalActor->getActorName() : "<span class='noInfo'>Unnamed actor</span>";
            $creators .= $actor . ", ";
        }
        if (!$creators) {
            $creators = "<span class='noInfo'>Unknown creator</span>";
        } else {
            $creators = mb_substr($creators, 0, -2); // to remove the last ", "
        }

        return $creators;
    }

    public function __toString()
    {
        $workTitle = $this->getStringTitle();
        $creators = '';
        $length = count($this->historicalActors);
        foreach ($this->historicalActors as $index => $historicalActor) {
            $creators .= $historicalActor;
            if ($index !== $length - 1) {
                $creators = $creators . "; ";
            }
        }
        if (!$creators) {
            $creators = "Unknown creator";
        }

        if (!$this->getTpq() && !$this->getTaq()) {
            return $workTitle . " [" . $creators . " (Unknown period of creation)]";
        }

        $date = $this->getTpaq();

        return "$workTitle ($date) [$creators]";
    }

    public function toPublicTitle()
    {
        if ($this->title) {
            $title = "<i>" . strval($this->title) . "</i>";
        } elseif ($this->titleOriginalChar) {
            $title = strval($this->titleOriginalChar);
        } elseif ($this->incipit) {
            $title = "<i>" . strval($this->incipit) . "</i>";
        } else {
            $entityName = Work::getInterfaceName();
            $title = "<span class='noInfo'>Untitled $entityName n°" . strval($this->id) . "</span>";
        }

        return $title;
    }

    public function toPublicString()
    {
        if ($this->title) {
            $title = "<span class='mainContent'><i>" . strval($this->title) . "</i></span>";
        } elseif ($this->titleOriginalChar) {
            $title = "<span class='mainContent'>" . strval($this->titleOriginalChar) . "</span>";
        } elseif ($this->incipit) {
            $title = "<span class='mainContent'><i>" . strval($this->incipit) . "</i></span>";
        } else {
            $entityName = Work::getInterfaceName();
            $title = "<span class='noInfo'>Untitled $entityName n°" . strval($this->id) . "</span>";
        }

        $tpaq = $this->getTpaq();

        $title = "$title ($tpaq)";

        return $title;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "work";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
