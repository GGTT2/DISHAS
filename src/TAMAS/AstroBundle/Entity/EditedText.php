<?php

// Symfony/src/TAMAS/AstroBundle/Entity/EditedText.php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;


/**
 * EditedText
 *
 * @ORM\Table(name="edited_text")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\EditedTextRepository")
 */
class EditedText
{
    /**
     * This static attributes state if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;

    // * @UniqueEntity(fields={"editedTextTitle", "date", "type", "tableContents"}, repositoryMethod="findUniqueEntity", ignoreNull=false, message="This référence is already recorded in the database")

    /**
     * @Groups({"editedTextMain"})
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
     * @Groups({"editedTextMain"})
     * 
     * @var string
     *
     * @ORM\Column(name="edited_text_title", type="text", nullable=true)
     * @Assert\NotNull()
     */
    private $editedTextTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="small_edited_text_title", type="text", nullable=true)
     */
    private $smallEditedTextTitle;

    /**
     * @Groups({"editedTextMain"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="date", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^(18|19|20)\d{2}$/", message="The input should be a year between 1800 and 2099.")
     */
    private $date;

    /**
     * @Groups({"editedTextMain"})
     *
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @Groups({"editedTextMain"})
     *
     * @var string
     *
     * @ORM\Column(name="online_resource", type="string", length=500, nullable=true)
     */
    private $onlineResource;

    /**
     * @Groups({"editedTextMain"})
     *
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @Groups({"editedTextMain"})
     *
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean", nullable=true)
     */
    private $public;

    /**
     * @Groups({"editedTextMain"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\SecondarySource", cascade={"persist"})
     */
    private $secondarySource;

    /**
     * @Groups({"externalTableTypeET"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TableType")
     * @Assert\NotBlank()
     */
    private $tableType;

    /**
     * @Groups({"editedTextMain"})
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Historian", cascade={"persist"})
     */
    private $historian;

    /**
     * @Groups({"editedTextMain"})
     *
     * @var string
     *
     * @ORM\Column(name="page_range", type="string", length=20, nullable=true)
     * @Assert\Regex(pattern="/^\d+(-|–)?\d+?$/", message="The input should be a page number followed or not by an other one separeted by an hyphen or an em dash e.g. 12 or 12-120.")
     */
    private $pageRange;

    /**
     * @Groups({"externalOriginalText"})
     *
     * @ORM\ManyToMany(targetEntity="TAMAS\AstroBundle\Entity\OriginalText", inversedBy="editedTexts", cascade={"persist"})
     */
    private $originalTexts;

    /**
     * @ORM\ManyToMany(targetEntity="TAMAS\AstroBundle\Entity\EditedText", cascade={"persist"})
     */
    private $relatedEditions;

    /**
     * @Groups({"editedTextMain"})
     */
    private $relatedEditionsExt;

    /**
     * @Groups({"editedTextMain"})
     * @Type("DateTime<'Y-m-d'>")
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Groups({"editedTextMain"})
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
     * @Groups({"externalTableContentET", "editedTextLimited"})
     *
     * @ORM\OneToMany(targetEntity="TAMAS\AstroBundle\Entity\TableContent", mappedBy="editedText", cascade={"persist"}, orphanRemoval=true)
     */
    private $tableContents;


    /**
     * @Groups({"editedTextMain"})
     * @Type("string")
     * 
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Era")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     * 
     * See the validator function: era are only necessary if the selected table type is a mean motion => accepts multiple values
     */
    private $era;


    /**
     * @Groups({"editedTextMain", "editedTextLimited"})
     * @var string
     */
    private $kibanaName;

    /**
     * @Groups({"kibana"})
     */
    private $kibanaId;

    /**
     * @Groups({"editedTextMain", "editedTextLimited"})
     * @var string
     */
    private $intellectualAuthors;

    /**
     * @PreSerialize
     */
    private function onPreSerialize()
    {
        foreach($this->tableContents as $tc){
            if(!$tc->getPublic())
                $this->removeTableContent($tc);
        }
       // $this->tableContents = new \Doctrine\Common\Collections\ArrayCollection();


        $this->kibanaName = $this->editedTextTitle;
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);
        $this->intellectualAuthors = $this->getFormattedIntellectualAuthors();

        foreach ($this->relatedEditions as $edition) {
            if ($edition->getPublic()) {
                $this->relatedEditionsExt[] = [
                    "kibana_name" => $edition->getEditedTextTitle(),
                    "kibana_id" => PreSerializeTools::generateKibanaId($edition),
                    "intellectual_authors" => $edition->getFormattedIntellectualAuthors()
                ];
            }
        }

        

        foreach($this->originalTexts as $oi){
            if(!$oi->getPublic())
                $this->removeOriginalText($oi);
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
     * Set date
     *
     * @param integer $date
     *
     * @return editedText
     */
    public function setDate($date = null)
    {
        if ($date == null && $this->getSecondarySource()) {
            $date = $this->getSecondarySource()->getSecPubDate();
        }
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return editedText
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set onlineResource
     *
     * @param string $onlineResource
     *
     * @return editedText
     */
    public function setOnlineResource($onlineResource)
    {
        $this->onlineResource = $onlineResource;

        return $this;
    }

    /**
     * Get onlineResource
     *
     * @return string
     */
    public function getOnlineResource()
    {
        return $this->onlineResource;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return editedText
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return EditedText
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
     * @return EditedText
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
     * @return EditedText
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
     * @return EditedText
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

    // Check if null

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return EditedText
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set pageRange
     *
     * @param string $pageRange
     *
     * @return EditedText
     */
    public function setPageRange($pageRange)
    {
        $correctedPageRange = str_replace('-', '–', $pageRange);
        $this->pageRange = $correctedPageRange;

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
     * Set secondarySource
     *
     * @param \TAMAS\AstroBundle\Entity\SecondarySource $secondarySource
     *
     * @return EditedText
     */
    public function setSecondarySource(\TAMAS\AstroBundle\Entity\SecondarySource $secondarySource = null)
    {
        $this->secondarySource = $secondarySource;

        return $this;
    }

    /**
     * Get secondarySource
     *
     * @return \TAMAS\AstroBundle\Entity\SecondarySource
     */
    public function getSecondarySource()
    {
        return $this->secondarySource;
    }

    /**
     * Set historian
     *
     * @param \TAMAS\AstroBundle\Entity\Historian $historian
     *
     * @return EditedText
     */
    public function setHistorian(\TAMAS\AstroBundle\Entity\Historian $historian = null)
    {
        $this->historian = $historian;

        return $this;
    }

    /**
     * Get historian
     *
     * @return \TAMAS\AstroBundle\Entity\Historian
     */
    public function getHistorian()
    {
        return $this->historian;
    }

    /**
     * Set editedTextTitle
     *
     * @param string $editedTextTitle
     *
     * @return EditedText
     */
    public function setEditedTextTitle($editedTextTitle)
    {
        $this->editedTextTitle = $editedTextTitle;

        if (strlen($editedTextTitle) > 50) {
            $smallEditedTextTitle = substr($editedTextTitle, 0, 50) . "...";
        } else {
            $smallEditedTextTitle = $editedTextTitle;
        }
        $this->setSmallEditedTextTitle($smallEditedTextTitle);

        return $this;
    }

    /**
     * Get editedTextTitle
     *
     * @return string
     */
    public function getEditedTextTitle()
    {
        return $this->editedTextTitle;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->originalTexts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relatedEditions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tableContents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add originalText
     *
     * @param \TAMAS\AstroBundle\Entity\OriginalText $originalText
     *
     * @return EditedText
     */
    public function addOriginalText(\TAMAS\AstroBundle\Entity\OriginalText $originalText)
    {
        $this->originalTexts[] = $originalText;
        $originalText->addEditedText($this);

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
        $originalText->removeEditedText($this);
    }

    /**
     * Get originalTexts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOriginalTexts()
    {
        return $this->originalTexts;
    }

    /**
     * Add relatedEdition
     *
     * @param \TAMAS\AstroBundle\Entity\EditedText $relatedEdition
     *
     * @return EditedText
     */
    public function addRelatedEdition(\TAMAS\AstroBundle\Entity\EditedText $relatedEdition)
    {
        $this->relatedEditions[] = $relatedEdition;

        return $this;
    }

    /**
     * Remove relatedEdition
     *
     * @param \TAMAS\AstroBundle\Entity\EditedText $relatedEdition
     */
    public function removeRelatedEdition(\TAMAS\AstroBundle\Entity\EditedText $relatedEdition)
    {
        $this->relatedEditions->removeElement($relatedEdition);
    }

    /**
     * Get relatedEditions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedEditions()
    {
        return $this->relatedEditions;
    }

    /**
     *
     * @Assert\Callback
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    public function isOriginalTextsValid(\Symfony\Component\Validator\Context\ExecutionContextInterface $context)
    {
        $valid = 1;
        if ($this->getType() == "a" && count($this->getOriginalTexts()) > $valid) {
            $context->buildViolation("You can only select one " . OriginalText::getInterfaceName() . " for this type of edition")
                ->atPath('originalTexts')
                ->addViolation();
        }
    }

    /**
     *
     * @Assert\Callback
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    public function isRelatedEditionsValid(\Symfony\Component\Validator\Context\ExecutionContextInterface $context)
    {
    }

    /**
     * Set smallEditedTextTitle
     *
     * @param string $smallEditedTextTitle
     *
     * @return EditedText
     */
    public function setSmallEditedTextTitle($smallEditedTextTitle)
    {
        $this->smallEditedTextTitle = $smallEditedTextTitle;

        return $this;
    }

    /**
     * Get smallEditedTextTitle
     *
     * @return string
     */
    public function getSmallEditedTextTitle()
    {
        return $this->smallEditedTextTitle;
    }

    /**
     * Add tableContent
     *
     * @param \TAMAS\AstroBundle\Entity\TableContent $tableContent
     *
     * @return EditedText
     */
    public function addTableContent(\TAMAS\AstroBundle\Entity\TableContent $tableContent)
    {
        $this->tableContents[] = $tableContent;
        $tableContent->setEditedText($this);

        return $this;
    }

    /**
     * Remove tableContent
     *
     * @param \TAMAS\AstroBundle\Entity\TableContent $tableContent
     */
    public function removeTableContent(\TAMAS\AstroBundle\Entity\TableContent $tableContent)
    {
        $this->tableContents->removeElement($tableContent);
        // When a tableContent it removed from an editedText, we need to set the edited text of this table content to null
        if ($tableContent->getEditedText() == $this) {
            $tableContent->setEditedText(null);
        }
    }

    /**
     * Get tableContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTableContents()
    {
        return $this->tableContents;
    }

    /**
     * This function tests that "$field"->getTableType() is the same as $this->getTableType;
     *
     * @param string $field
     * @param string $fieldName
     * @param object $thatEditedText
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     * @return string|boolean
     */
    private function testTTId($field, $fieldName, $thatEditedText, $context)
    {
        $tableTypeName = TableType::getInterfaceName();
        $tableTypeNames = TableType::getInterfaceName(true);
        $getter = "get" . ucfirst($field);
        if (!empty($thatEditedText->{$getter}()->toArray())) {
            foreach ($thatEditedText->{$getter}() as $attribute) {
                if ($attribute->getTableType()->getId() != $thatEditedText->getTableType()->getId()) {
                    return $context->buildViolation(
                        "It is not possible to build an edition on $fieldName of different $tableTypeNames ($tableTypeName selected: " .
                            $attribute->getTableType()->getId() . " / $tableTypeName of the edition:  " . $thatEditedText->getTableType()
                            ->getId()
                    )
                        ->atPath($field)
                        ->addViolation() . ")";
                }
            }
        }
        return true;
    }

    /**
     *This function checks several points before submitting the form : 
     *1. That the secondary source or the name of the historian is given
     *2. That this edited text + all the table content + all the original text + all the related edition share the same table type.
     *3. If multiple table content are selected, that this table type allows multiple table content.
     *4. If the edited text is a type-A : that only one original text is selected.
     * @Assert\Callback
     * 
     */
    public function validateFields(ExecutionContextInterface $context)
    {
        // If the tabletype is "mean motion" : it must be linked to a specific calendar
        if ($this->tableType && $this->tableType->getAcceptMultipleContent() && !$this->era) {
            $context
                ->buildViolation("Please fill the sub-calendar type")
                ->atPath('era')
                ->addViolation();
        }

        //only the meanmotion table are connected to a calendar.
        if ($this->tableType && !$this->tableType->getAcceptMultipleContent() && $this->era) {
            throw new \Exception('This editedText cannot be associated with a sub-calendar / era');
        }


        if ($this->secondarySource === null) {
            $year = ['field' => "date", "name" => "year of edition"];
            $author = ['field' => "historian", "name" => Historian::getInterfaceName()];
            if ($this->historian === null && $this->date === null) {
                $context
                    ->buildViolation("Please fill the secondary source. If no edition is known, click on \"no known previous edition\", and fill the \"" . $author['name'] . "\" and the \"" . $year['name'] . "\" fields.")
                    ->atPath('secondarySource')
                    ->addViolation();
            } elseif ($this->historian && !$this->date) {
                $context->buildViolation('This value should not be null.')
                    ->atPath($year['field'])
                    ->addViolation();
            } elseif ($this->date && !$this->historian) {
                $context->buildViolation('This value should not be null.')
                    ->atPath($author['field'])
                    ->addViolation();
            }
        }else{
            if ($this->historian !== null && $this->date !== null) {
                $context->buildViolation('This value should not be filled. Either chose a secondary source or detail the date and author.')
                ->atPath($author['field'])
                ->addViolation();
            }

        }


        $thatTableType = null;
        if ($this->tableType) {
            $thatTableType = $this->tableType;
        }

        if($this->tableType){
            $this->testTTId("tableContents", "table contents", $this, $context);
            $this->testTTId("originalTexts", "original items", $this, $context);
            $this->testTTId("relatedEditions", "related editions", $this, $context);
        }
        /* Checks the number of table content that can be selected depending on the table type*/
        if ($thatTableType !== null && ($thatTableType->getAcceptMultipleContent() !== true) && count($this->getTableContents()) > 1) {
            $context->buildViolation('Except for mean motion and syzygy tables, only one set of tabular values can be associated with an ' . EditedText::getInterfaceName())
                ->atPath('tableContents')
                ->addViolation();
            return;
        }


        /*Checks the dependant related edition and original text that can be selected depending on the type of edition */
        $type = $this->type;
        if ($type == 'a' && !empty($this->relatedEditions->toArray())) {
            $context->buildViolation("It is not possible to select related editions with this type of edition")
                ->atPath('relatedEditions')
                ->addViolation();
        } elseif ($type == 'c' && !empty($this->relatedEditions->toArray())) {
            $relatedEditionsTypes = [];
            foreach ($this->relatedEditions as $related) {
                $relatedEditionsTypes[] = $related->getType();
            }
            if (in_array('b', $relatedEditionsTypes)) {
                $context->buildViolation("It is not possible to select a type-B edition for this type of edition (type-c)")
                    ->atPath('relatedEditions')
                    ->addViolation();
            }
        }

        /*Sets date to the date of the edition if null*/
        if (!$this->date) {
            $this->setDate();
        }
    }

    /**
     * Set tableType.
     *
     * @param \TAMAS\AstroBundle\Entity\TableType|null $tableType
     *
     * @return EditedText
     */
    public function setTableType(\TAMAS\AstroBundle\Entity\TableType $tableType = null)
    {
        $this->tableType = $tableType;

        return $this;
    }

    /**
     * Get tableType.
     *
     * @return \TAMAS\AstroBundle\Entity\TableType|null
     */
    public function getTableType()
    {
        return $this->tableType;
    }


    /**
     * Set era
     *
     * @param \TAMAS\AstroBundle\Entity\Era $era
     *
     * @return EditedText
     */
    public function setEra(\TAMAS\AstroBundle\Entity\Era $era = null)
    {
        $this->era = $era;

        return $this;
    }

    /**
     * Get era
     *
     * @return \TAMAS\AstroBundle\Entity\Era
     */
    public function getEra()
    {
        return $this->era;
    }


    /**__________________________________________ Generate the "__toString" _________________________________**/

    /**
     * getTitle
     *
     * This method gives the usual title of the object depending on its main property.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->editedTextTitle ? GT::truncate($this->editedTextTitle, 50) : "Unknown " . EditedText::getInterfaceName() . " n°" . $this->id;
    }


    public function __toString()
    { {
            $type = " | Type-" . ucfirst($this->type);
            $title = $this->getTitle();
            if ($this->historian) {
                $title = $title . " by " . $this->historian;
            } elseif ($this->secondarySource && $this->secondarySource->getHistorians()) {
                $length = count($this->secondarySource->getHistorians());
                $historianNames = "";
                foreach ($this->secondarySource->getHistorians() as $index => $thatHistorian) {
                    $historianNames = $thatHistorian;
                    if ($index !== $length - 1) {
                        $historianNames = $historianNames . "; ";
                    }
                }
                $title = "$title by $historianNames";
            }
            $date = "";
            if ($this->date) {
                $date = " ($this->date)";
            }
            return $title . $date . $type . " (ID n° $this->id)";
        }
    }

    public function toPublicString()
    {
        $type = $this->type ? "<br/>Type " . ucfirst(strval($this->type)) : "";
        $title = $this->editedTextTitle ? "<span class='mainContent'>" . str_replace("_", " ", strval($this->editedTextTitle)) . "</span>" : "<span class='noInfo'>Untitled " . EditedText::getInterfaceName() . " n°" . strval($this->id) . "</span>";
        $year = $this->date ? " (" . strval($this->date) . ")" : " <span class='noInfo'>(unknown year of edition)</span>";

        $title = $title . $year . $type;

        return $title;
    }

    public function toPublicTitle()
    {
        $type = $this->type ? " <span class='mainContent'>Type " . ucfirst(strval($this->type)) . "</span><br/>" : "";
        $title = $this->editedTextTitle ? str_replace("_", " ", strval($this->editedTextTitle)) : "<span class='noInfo'>Untitled " . EditedText::getInterfaceName() . " n°" . strval($this->id) . "</span>";
        $year = $this->date ? " (" . strval($this->date) . ")" : " <span class='noInfo'>(unknown year of edition)</span>";

        $title = $title . $year . $type;

        return $title;
    }

    /**
     * This methods returns a string of the authors of an edition, in order to be used in the public interface
     * @return string
     */
    public function getFormattedIntellectualAuthors()
    {
        if ($this->historian) {
            return $this->historian->__toString();
        } elseif ($this->secondarySource) {
            $authors = $this->secondarySource->getHistorians() ? $this->secondarySource->getHistorians()->toArray() : "<span class='noInfo'>Unknown " . Historian::getInterfaceName() . "</span>";
            if (is_array($authors)) {
                $formattedAuthors = "";
                for ($i = 0; $i < count($authors); ++$i) {
                    if ($i != 0) {
                        $formattedAuthors = $formattedAuthors . "<br>";
                    }
                    $formattedAuthors = $formattedAuthors . $authors[$i]->__toString();
                }
                return $formattedAuthors;
            }
            return $authors;
        } else {
            return "<span class='noInfo'>Unknown " . Historian::getInterfaceName() . "</span>";
        }
    }

    /**
     * This methods returns a array of all historian ids that are associated with the edition
     * @return array
     */
    public function getHistorians()
    {
        $historians = [];
        if ($this->historian) {
            $historians[] = $this->historian;
        } elseif ($this->secondarySource) {
            if ($this->secondarySource->getHistorians()) {
                $historians = $this->secondarySource->getHistorians()->toArray();
            }
        }
        return $historians;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "table edition";
        if (!$isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }

    static function getEditionTypeDefinition($type)
    {
        $originalTextName = OriginalText::getInterfaceName();
        $originalTextNames = OriginalText::getInterfaceName(true);
        $primSourceName = PrimarySource::getInterfaceName();
        $primSourceNames = PrimarySource::getInterfaceName(true);
        $definitions = [
            "a" =>
            "A type-A edition corresponds to the edition of a $originalTextName originating from a single $primSourceName: 
                scribal errors are reproduced.",

            "b" =>
            "A type-B edition corresponds to a critical edition of several $originalTextNames:
                 it is based on several different editions, themselves based on tables from different $primSourceNames.
                 A type-B edition can be based on type-A, type-B or type-C editions.",

            "c" =>
            "A type-C edition is a recalculated version of a table using modern computation methods.",
        ];

        return $definitions[$type];
    }
}
