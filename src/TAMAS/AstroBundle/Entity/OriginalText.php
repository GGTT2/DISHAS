<?php

// Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TAMAS\AstroBundle\Validator\Constraints as TAMASAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * OriginalText
 *
 * @ORM\Table(name="original_text")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\OriginalTextRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"originalTextTitle","primarySource", "pageMin", "pageMax"}, ignoreNull = false, message="This document is already recorded in the database")
 * @TAMASAssert\Terminus
 */
class OriginalText
{

    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;
    
    /**
     * @Groups({"originalTextMain" })
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
     * @Groups({"originalTextMain" })
     *
     * @var string
     *
     * @ORM\Column(name="text_type", type="string", length=50)
     */
    private $textType;

    /**
     * @Groups({"originalTextMain" })
     *
     * @var string
     *
     * @ORM\Column(name="original_text_title", type="text", nullable=true)
     * @Assert\NotNull()
     */
    private $originalTextTitle;

    /**
     * @Groups({"originalTextMain" })
     *
     * @var string
     *
     * @ORM\Column(name="original_text_title_original_char", type="text", nullable=true)
     */
    private $originalTextTitleOriginalChar;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="small_text_title", type="text", nullable=true)
     */
    private $smallTextTitle;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=500, nullable=true)
     */
    private $imageUrl;

    /**
     * @Groups({"originalTextMain" })
     * @Type("string")
     * 
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Language", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $language;

    /**
     * @Groups({"originalTextMain" })
     * @Type("string")
     * 
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Script", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $script;

    /**
     * @Groups({"originalTextMain" })
     * @Type("string")
     *
     * @var int
     * @Assert\NotBlank()
     * @ORM\Column(name="tpq", type="integer", nullable=true)
     */
    private $tpq;

    /**
     * @Groups({"originalTextMain"})
     * @Type("string")
     *
     * @var int
     * @Assert\NotBlank()
     * @ORM\Column(name="taq", type="integer", nullable=true)
     */
    private $taq;

    /**
     * @var string
     *
     * @ORM\Column(name="page_range", type="string", length=20, nullable=true)
     */
    private $pageRange;

    /**
     * @Groups({"originalTextMain"})
     *
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="page_min", type="string", length=20, nullable=true)
     */
    private $pageMin;

    /**
     * @Groups({"originalTextMain"})
     *
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="page_max", type="string", length=20, nullable=true)
     */
    private $pageMax;

    /**
     * @Groups({"originalTextMain"})
     *
     * @var bool
     *
     * @ORM\Column(name="is_folio", type="boolean", nullable=true)
     */
    private $isFolio;

    /**
     * @Groups({"originalTextMain"})
     *
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @Groups({"originalTextMain"})
     *
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean", nullable=true)
     */
    private $public;

    /**
     * @Groups({"originalTextMain"})
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Groups({"originalTextMain"})
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
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Users")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @Groups({"originalTextMain"})
     * @Type("string")
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TableType")
     */
    private $tableType;

    /**
     * @Groups({"originalTextMain"})
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Place", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $place;

    /**
     * @Groups({"primarySourceMain"})
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\PrimarySource", inversedBy="originalTexts", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $primarySource;

    /**
     * @Groups({"workMain"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Work", inversedBy="originalTexts", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $work;

    /**
     * @Groups({"originalTextMain"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\HistoricalActor", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $historicalActor;

    /**
     * @Groups({"externalEditedText", "editedTextLimited"})
     *
     * @ORM\ManyToMany(targetEntity="TAMAS\AstroBundle\Entity\EditedText", mappedBy="originalTexts")
     */
    private $editedTexts;

    /**
     * @Groups({"originalTextMain"})
     *
     */
    private $tpqDate;

    /**
     * @Groups({"originalTextMain"})
     *
     */
    private $taqDate;

    /**
     * @Groups({"originalTextMain"})
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
     *
     */
    private function onPreSerialize()
    {
        GT::setValidDateFromYear($this, "tpq", "tpqDate");
        GT::setValidDateFromYear($this, "taq", "taqDate");
        $this->kibanaName = $this->__toString();
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);
        foreach($this->editedTexts as $et){
            if(!$et->getPublic()){
                $this->removeEditedText($et);
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

    // _______________________________This methods enable us to get the ID of a selected object in form after autocomplete __________________________//
    /**
     * Set id
     *
     * @param int $id
     *
     * @return OriginalText
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set textType
     *
     * @param string $textType
     *
     * @return OriginalText
     */
    public function setTextType($textType)
    {
        $this->textType = $textType;

        return $this;
    }

    /**
     * Get textType
     *
     * @return string
     */
    public function getTextType()
    {
        return $this->textType;
    }

    /**
     * Set originalTextTitle
     *
     * @param string $originalTextTitle
     *
     * @return OriginalText
     */
    public function setOriginalTextTitle($originalTextTitle)
    {
        $this->originalTextTitle = $originalTextTitle;
        if (strlen($originalTextTitle) > 50) {
            $smallTextTitle = mb_substr($originalTextTitle, 0, 50) . "...";
        } else {
            $smallTextTitle = $originalTextTitle;
        }
        $this->setSmallTextTitle($smallTextTitle);

        return $this;
    }

    /**
     * Get originalTextTitle
     *
     * @return string
     */
    public function getOriginalTextTitle()
    {
        return $this->originalTextTitle;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return OriginalText
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set tpq
     *
     * @param integer $tpq
     *
     * @return OriginalText
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
     * @return OriginalText
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
     * Set pageRange
     *
     * @param string $pageRange
     *
     * @return OriginalText
     */
    public function setPageRange($pageRange)
    {
        $this->pageRange = $pageRange;

        return $this;
    }

    /**
     * Get pageRange
     *
     * @return string
     */
    public function getPageRange()
    {
        return $this->pageRange;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return OriginalText
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return OriginalText
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return bool
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return OriginalText
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
     * @return OriginalText
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
     * @param string $createdBy
     *
     * @return OriginalText
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     *
     * @return OriginalText
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set tableType
     *
     * @param \TAMAS\AstroBundle\Entity\TableType $tableType
     *
     * @return OriginalText
     */
    public function setTableType(\TAMAS\AstroBundle\Entity\TableType $tableType = null)
    {
        $this->tableType = $tableType;

        return $this;
    }

    /**
     * Get tableType
     *
     * @return \TAMAS\AstroBundle\Entity\TableType
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    /**
     * Set place
     *
     * @param \TAMAS\AstroBundle\Entity\Place $place
     *
     * @return OriginalText
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
     * Set primarySource
     *
     * @param \TAMAS\AstroBundle\Entity\PrimarySource $primarySource
     *
     * @return OriginalText
     */
    public function setPrimarySource(\TAMAS\AstroBundle\Entity\PrimarySource $primarySource = null)
    {
        if ($this->primarySource !== null) { // if this entity was previously linked to a tableType
            $this->primarySource->removeOriginalText($this); // go to this tableType propriety, and delete the relation
        }

        if ($primarySource !== null) {
            $primarySource->addOriginalText($this);
        }
        $this->primarySource = $primarySource;

        return $this;
    }

    /**
     * Get primarySource
     *
     * @return \TAMAS\AstroBundle\Entity\PrimarySource
     */
    public function getPrimarySource()
    {
        return $this->primarySource;
    }

    /**
     * Set work
     *
     * @param \TAMAS\AstroBundle\Entity\Work $work
     *
     * @return OriginalText
     */
    public function setWork(\TAMAS\AstroBundle\Entity\Work $work = null)
    {
        if ($this->work !== null) { // if this entity was previously linked to a tableType
            $this->work->removeOriginalText($this); // go to this tableType propriety, and delete the relation
        }

        if ($work !== null) {
            $work->addOriginalText($this);
        }
        $this->work = $work;

        return $this;
    }

    /**
     * Get work
     *
     * @return \TAMAS\AstroBundle\Entity\Work
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * Set historicalActor
     *
     * @param \TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor
     *
     * @return OriginalText
     */
    public function setHistoricalActor(\TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor = null)
    {
        $this->historicalActor = $historicalActor;

        return $this;
    }

    /**
     * Get historicalActor
     *
     * @return \TAMAS\AstroBundle\Entity\HistoricalActor
     */
    public function getHistoricalActor()
    {
        return $this->historicalActor;
    }

    /**
     * Set language
     *
     * @param \TAMAS\AstroBundle\Entity\Language $language
     *
     * @return OriginalText
     */
    public function setLanguage(\TAMAS\AstroBundle\Entity\Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \TAMAS\AstroBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set script
     *
     * @param \TAMAS\AstroBundle\Entity\Script $script
     *
     * @return OriginalText
     */
    public function setScript(\TAMAS\AstroBundle\Entity\Script $script = null)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * Get script
     *
     * @return \TAMAS\AstroBundle\Entity\Script
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set smallTextTitle
     *
     * @param string $smallTextTitle
     *
     * @return OriginalText
     */
    public function setSmallTextTitle($smallTextTitle)
    {
        $this->smallTextTitle = $smallTextTitle;

        return $this;
    }

    /**
     * Get smallTextTitle
     *
     * @return string
     */
    public function getSmallTextTitle()
    {
        return $this->smallTextTitle;
    }

    /**
     * Set pageMin
     *
     * @param string $pageMin
     *
     * @return OriginalText
     */
    public function setPageMin($pageMin)
    {
        $this->pageMin = $pageMin;

        return $this;
    }

    /**
     * Get pageMin
     *
     * @return string
     */
    public function getPageMin()
    {
        return $this->pageMin;
    }

    /**
     * Set pageMax
     *
     * @param string $pageMax
     *
     * @return OriginalText
     */
    public function setPageMax($pageMax)
    {
        $this->pageMax = $pageMax;

        return $this;
    }

    /**
     * Get pageMax
     *
     * @return string
     */
    public function getPageMax()
    {
        return $this->pageMax;
    }

    /**
     * Set isFolio
     *
     * @param boolean $isFolio
     *
     * @return OriginalText
     */
    public function setIsFolio($isFolio)
    {
        $this->isFolio = $isFolio;

        return $this;
    }

    /**
     * Get isFolio
     *
     * @return boolean
     */
    public function getIsFolio()
    {
        return $this->isFolio;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->editedTexts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add editedText
     *
     * @param \TAMAS\AstroBundle\Entity\EditedText $editedText
     *
     * @return OriginalText
     */
    public function addEditedText(\TAMAS\AstroBundle\Entity\EditedText $editedText)
    {
        $this->editedTexts[] = $editedText;

        return $this;
    }

    /**
     * Remove editedText
     *
     * @param \TAMAS\AstroBundle\Entity\EditedText $editedText
     */
    public function removeEditedText(\TAMAS\AstroBundle\Entity\EditedText $editedText)
    {
        $this->editedTexts->removeElement($editedText);
    }

    /**
     * Get editedTexts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEditedTexts()
    {
        return $this->editedTexts;
    }

    /**
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context)
    {
        if ($this->pageMin !== null || $this->pageMax !== null) {
            if ($this->isFolio == 1) {
                if (!preg_match("/^\d+[r|v]$/", $this->pageMin)) {
                    $wrongFolio = 'pageMin';
                    $context->buildViolation('The folio number must be followed by "r" or "v" e.g. 14v .')
                        ->atPath($wrongFolio)
                        ->addViolation();
                }
                if (!preg_match("/^\d+[r|v]$/", $this->pageMax)) {
                    $wrongFolio = 'pageMax';
                    $context->buildViolation('The folio number must be followed by "r" or "v" e.g. 14v .')
                        ->atPath($wrongFolio)
                        ->addViolation();
                }
            } else {
                if (!preg_match("/^\d+$/", $this->pageMin)) {
                    $context->buildViolation('The page number must be an integer')
                        ->atPath('pageMin')
                        ->addViolation();
                }
                if (!preg_match("/^\d+$/", $this->pageMax)) {
                    $context->buildViolation('The page number must be an integer.')
                        ->atPath('pageMax')
                        ->addViolation();
                }
            }
        }
    }

    /**
     * Set originalTextTitleOriginalChar.
     *
     * @param string|null $originalTextTitleOriginalChar
     *
     * @return OriginalText
     */
    public function setOriginalTextTitleOriginalChar($originalTextTitleOriginalChar = null)
    {
        $this->originalTextTitleOriginalChar = $originalTextTitleOriginalChar;

        return $this;
    }

    /**
     * @Groups({"originaltext", "editedtext", "parameterset", "tablecontent"})
     * Get originalTextTitleOriginalChar.
     *
     * @return string|null
     */
    public function getOriginalTextTitleOriginalChar()
    {
        return $this->originalTextTitleOriginalChar;
    }

    /**
     * @Groups({"originaltext", "editedtext"})
     * _________________ toString() methods _______________*
     */
    public function getTitle()
    {
        $titleOriginalChar = $this->originalTextTitleOriginalChar ? ' (' . $this->originalTextTitleOriginalChar . ')' : '';
        $title = $this->originalTextTitle ? GT::truncate($this->originalTextTitle) : "Untitled n° " . $this->id;
        return $title . $titleOriginalChar;
    }

    public function getAstronomicalObject()
    {
        if (!$this->tableType) {
            return null;
        }
        return $this->tableType->getAstronomicalObject();
    }

    /**
     * Returns the dates of an original items correctly formatted
     *
     * @return int|string
     */
    public function getTpaq()
    {
        return GT::getTpaq($this->tpq, $this->taq, "Unknown time of copy");
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

    /**
     * @return string
     */
    public function getPages()
    {
        return GT::getTpaq($this->getPageMin(), $this->getPageMax());
    }

    /**
     * Convert a folio number into a page number
     * @param int|string $page
     * @return int
     */
    public function convertFoliosInPages($page)
    {
        if (GT::str_ends($page, "r")) {
            $page = str_replace("r", "", $page);
            return (intval($page) * 2) - 1;
        } elseif (GT::str_ends($page, "v")) {
            $page = str_replace("v", "", $page);
            return intval($page) * 2;
        } else {
            return $page;
        }
    }

    /**
     * returns the extends of the original item in page
     * @return int|string
     */
    public function getNumberOfPages()
    {
        $pageMin = $this->getPageMin();
        $pageMax = $this->getPageMax();

        if (!$pageMin or !$pageMax)
            return '?';

        $totalPage = intval($this->convertFoliosInPages($pageMax)) - intval($this->convertFoliosInPages($pageMin));

        /* 1 is added to for the first page that was substracted from the total count */
        $totalPage += 1;

        return $totalPage;
    }

    /**
     * getConvertedPageMin
     *
     * This method converts the pages in folio or page in an unit manageable by the visualizations
     * used in the notices of primary sources and works
     */
    public function getConvertedPageMin()
    {
        if ($this->getPageMin()) {
            if (GT::str_ends($this->getPageMin(), "r")) {
                return intval(mb_substr($this->getPageMin(), 0, -1)) - 0.5;
            } elseif (GT::str_ends($this->getPageMin(), "v")) {
                return mb_substr($this->getPageMin(), 0, -1);
            } else {
                return $this->getPageMin();
            }
        } else {
            return 1;
        }
    }

    /**
     * getConvertedPageMin
     *
     * This method converts the pages in folio or page in an unit manageable by the visualizations
     * used in the notices of primary sources and works
     */
    public function getConvertedPageMax()
    {
        $pageMax = $this->getPageMax();
        if ($pageMax) {
            if (GT::str_ends($pageMax, "r")) {
                return intval(mb_substr($pageMax, 0, -1)) + 0.5;
            } elseif (GT::str_ends($pageMax, "v")) {
                return intval(mb_substr($pageMax, 0, -1)) + 1;
            } else {
                return intval($pageMax) + 1;
            }
        } else {
            return 2;
        }
    }

    public function __toString()
    {
        $title = $this->getTitle();
        $folio = "";

        $source = $this->primarySource ? $this->primarySource : "Unknown " . PrimarySource::getInterfaceName();
        $work = $this->work ? $this->work : "Unknown " . Work::getInterfaceName();

        $date = $this->getTpaq();

        if ($this->pageMax || $this->pageMin) {
            $pageMax = $this->pageMax ? $this->pageMax : '';
            $pageMin = $this->pageMin ? $this->pageMin : '';
            $folio = ', ' . $pageMin . '-' . $pageMax;
        }
        $leftToRight = mb_convert_encoding('&#8234;', 'UTF-8', 'HTML-ENTITIES');
        return $title . json_decode('"' . $leftToRight . '"') . " ($date) ; $source $folio ; $work";
    }

    public function toPublicString()
    {
        if ($this->originalTextTitle) {
            $title = "<span class='mainContent'><i>" . ucfirst($this->originalTextTitle) . "</i></span>";
        } else {
            $title = "<span class='noInfo'>Untitled " . OriginalText::getInterfaceName() . " n°" . $this->id . "</span>";
        }

        $primSource = "<span class='noInfo'>Unknown " . PrimarySource::getInterfaceName() . "</span>";
        if ($this->primarySource) {
            if ($this->primarySource->getPrimType() == "ms") {
                $shelfmark = $this->primarySource->getShelfmark() ? $this->primarySource->getShelfmark() : "<span class='noInfo'>Unknown ref n°" . $this->primarySource->getId() . "</span>";
                $primSource = "$shelfmark";
            } elseif ($this->primarySource->getPrimType() == "ep") {
                $primTitle = $this->primarySource->getPrimTitle() ? $this->primarySource->getPrimTitle() : "<span class='noInfo'>Unknown ref n°" . $this->primarySource->getId() . "</span>";
                $primEditor = $this->primarySource->getPrimEditor() ? $this->primarySource->getPrimEditor() : "<span class='noInfo'>Unknown editor</span>";
                $date = $this->primarySource->getDate() ? $this->primarySource->getDate() : "<span class='noInfo'>Unknown year of edition</span>";
                $primSource = "<i>$primTitle</i>, $primEditor, $date";
            }
        }

        return $title . " (" . $this->getTpaq() . ") | " . $primSource;
    }

    public function toPublicTitle()
    {
        if ($this->originalTextTitle) {
            $title = "<i>" . ucfirst(strval($this->originalTextTitle)) . "</i>";
        } else {
            $entityName = OriginalText::getInterfaceName();
            $title = "<span class='noInfo'>Untitled $entityName n°" . strval($this->id) . "</span>";
        }

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
        $name = "table witness";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
