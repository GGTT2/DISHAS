<?php
namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * SecondarySource
 *
 * @ORM\Table(name="secondary_source")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\SecondarySourceRepository")
 * @UniqueEntity(fields={"historians", "secTitle", "secVolume"}, repositoryMethod="findUniqueEntity", ignoreNull = false, message="This source is already recorded in the database")
 * @UniqueEntity(fields="secIdentifier", repositoryMethod="findUniqueEntity2", ignoreNull = false, message="This source is already recorded in the database")
 */
class SecondarySource
{

    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;
    
    /**
     * @Groups({"secondarySource", "externalSecondarySource"})
     *
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
     * @ORM\Column(name="sec_type", type="string", length=255, nullable=true)
     * @Assert\NotNull()
     */
    private $secType;

    /**
     * @var string
     *
     * @ORM\Column(name="small_sec_title", type="string", length=255, nullable=true)
     */
    private $smallSecTitle;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="sec_title", type="text", nullable=true)
     * @Assert\NotNull()
     */
    private $secTitle;

    /**
     * @Groups({"secondarySource", "externalSecondarySource"})
     *
     * @var string
     *
     * @ORM\Column(name="sec_identifier", type="string", length=255, nullable=true)
     */
    private $secIdentifier;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="sec_online_identifier", type="string", length=500, nullable=true)
     */
    private $secOnlineIdentifier;

    /**
     * @Groups({"secondarySource", "externalSecondarySource"})
     * @Type("string")
     *
     * @var int
     *
     * @ORM\Column(name="sec_pub_date", type="integer", nullable=true)
     * @Assert\Regex(pattern="/^(18|19|20)\d{2}$/", message="The input should be a year between 1800 and 2099.")
     * @Assert\NotNull()
     */
    private $secPubDate;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="sec_page_range", type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\d+(-\d+)?$/", message="The input should be formated as a unique page number, eg. '11', or as a range, eg. '11-23'.")
     */
    private $secPageRange;

    /**
     * @Groups({"secondarySource"})
     *
     * @var string
     *
     * @ORM\Column(name="sec_publisher", type="string", length=255, nullable=true)
     */
    private $secPublisher;

    /**
     * @Groups({"secondarySource"})
     *
     * @var string
     *
     * @ORM\Column(name="sec_pub_place", type="string", length=255, nullable=true)
     */
    private $secPubPlace;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="sec_volume", type="string", length=255, nullable=true)
     */
    private $secVolume;

    /**
     * @Groups({"editedTextMain"})
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Journal", cascade={"persist"})
     */
    private $journal;

    /**
     * @Groups({"editedTextMain"})
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\SecondarySource", cascade={"persist"})
     */
    private $collectiveBook;

    /**
     * @Groups({"editedTextMain"})
     *
     * @ORM\ManyToMany(targetEntity="TAMAS\AstroBundle\Entity\Historian", cascade={"persist"})
     * @Assert\NotBlank()
     */
    private $historians;

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
     * @Groups({"editedTextMain"})
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
        $name = $this->__toString();
        $page = "";
        if ($this->secPageRange){
            $page = ", p. ".$this->secPageRange;
        }
        if ($this->collectiveBook){
            $in = " in ".$this->collectiveBook->__toString();
        }elseif ($this->journal){
            $in = " in ".$this->journal->__toString();
        }else{ 
            $in = "";
        };
        
        $name.= $in.$page;
        $this->kibanaName = $name;        
        
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
     * Set secType
     *
     * @param string $secType
     *
     * @return SecondarySource
     */
    public function setSecType($secType)
    {
        $this->secType = $secType;

        return $this;
    }

    /**
     * Get secType
     *
     * @return string
     */
    public function getSecType()
    {
        return $this->secType;
    }

    /**
     * Set secTitle
     *
     * @param string $secTitle
     *
     * @return SecondarySource
     */
    public function setSecTitle($secTitle)
    {
        $this->secTitle = $secTitle;

        if (strlen($secTitle) > 50) {
            $smallSecTitle = mb_substr($secTitle, 0, 50) . "...";
        } else {
            $smallSecTitle = $secTitle;
        }
        $this->setSmallSecTitle($smallSecTitle);

        return $this;
    }

    /**
     * Get secTitle
     *
     * @return string
     */
    public function getSecTitle()
    {
        return $this->secTitle;
    }

    /**
     * Set secIdentifier
     *
     * @param string $secIdentifier
     *
     * @return SecondarySource
     */
    public function setSecIdentifier($secIdentifier)
    {
        $this->secIdentifier = $secIdentifier;

        return $this;
    }

    /**
     * Get secIdentifier
     *
     * @return string
     */
    public function getSecIdentifier()
    {
        return $this->secIdentifier;
    }

    /**
     * Set secPubDate
     *
     * @param integer $secPubDate
     *
     * @return SecondarySource
     */
    public function setSecPubDate($secPubDate)
    {
        $this->secPubDate = $secPubDate;

        return $this;
    }

    /**
     * Get secPubDate
     *
     * @return int
     */
    public function getSecPubDate()
    {
        return $this->secPubDate;
    }

    /**
     * Set secPageRange
     *
     * @param string $secPageRange
     *
     * @return SecondarySource
     */
    public function setSecPageRange($secPageRange)
    {
        $this->secPageRange = $secPageRange;

        return $this;
    }

    /**
     * Get secPageRange
     *
     * @return string
     */
    public function getSecPageRange()
    {
        return $this->secPageRange;
    }

    /**
     * Set secPublisher
     *
     * @param string $secPublisher
     *
     * @return SecondarySource
     */
    public function setSecPublisher($secPublisher)
    {
        $this->secPublisher = $secPublisher;

        return $this;
    }

    /**
     * Get secPublisher
     *
     * @return string
     */
    public function getSecPublisher()
    {
        return $this->secPublisher;
    }

    /**
     * Set secPubPlace
     *
     * @param string $secPubPlace
     *
     * @return SecondarySource
     */
    public function setSecPubPlace($secPubPlace)
    {
        $this->secPubPlace = $secPubPlace;

        return $this;
    }

    /**
     * Get secPubPlace
     *
     * @return string
     */
    public function getSecPubPlace()
    {
        return $this->secPubPlace;
    }

    /**
     * Set secVolume
     *
     * @param string $secVolume
     *
     * @return SecondarySource
     */
    public function setSecVolume($secVolume)
    {
        $this->secVolume = $secVolume;

        return $this;
    }

    /**
     * Get secVolume
     *
     * @return string
     */
    public function getSecVolume()
    {
        return $this->secVolume;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->historians = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return SecondarySource
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
     * @return SecondarySource
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
     * Set journal
     *
     * @param \TAMAS\AstroBundle\Entity\Journal $journal
     *
     * @return SecondarySource
     */
    public function setJournal(\TAMAS\AstroBundle\Entity\Journal $journal = null)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Get journal
     *
     * @return \TAMAS\AstroBundle\Entity\Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Set collectiveBook
     *
     * @param \TAMAS\AstroBundle\Entity\SecondarySource $collectiveBook
     *
     * @return SecondarySource
     */
    public function setCollectiveBook(\TAMAS\AstroBundle\Entity\SecondarySource $collectiveBook = null)
    {
        $this->collectiveBook = $collectiveBook;

        return $this;
    }

    /**
     * Get collectiveBook
     *
     * @return \TAMAS\AstroBundle\Entity\SecondarySource
     */
    public function getCollectiveBook()
    {
        return $this->collectiveBook;
    }

    /**
     * Add historian
     *
     * @param \TAMAS\AstroBundle\Entity\Historian $historian
     *
     * @return SecondarySource
     */
    public function addHistorian(\TAMAS\AstroBundle\Entity\Historian $historian)
    {
        $this->historians[] = $historian;

        return $this;
    }

    /**
     * Remove historian
     *
     * @param \TAMAS\AstroBundle\Entity\Historian $historian
     */
    public function removeHistorian(\TAMAS\AstroBundle\Entity\Historian $historian)
    {
        $this->historians->removeElement($historian);
    }

    /**
     * Get historians
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistorians()
    {
        return $this->historians;
    }

    /**
     * Set createdBy
     *
     * @param \TAMAS\AstroBundle\Entity\Users $createdBy
     *
     * @return SecondarySource
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
     * @return SecondarySource
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

    // ____________________________ This method is added to basic entity in order to be able to check in controller if form-submitted entities have only null attributes.
    // This method is used in the context or search originalText. This is still a draft method.
    // see draft section of originalTextRepository
    // public function checkIfNull() {
    // special case : we have to check also array collection. We can't access it through the foreach loop, as it is seen as an object.
    // if (!empty($this->historians)) {
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
     * Set secOnlineIdentifier
     *
     * @param string $secOnlineIdentifier
     *
     * @return SecondarySource
     */
    public function setSecOnlineIdentifier($secOnlineIdentifier)
    {
        $this->secOnlineIdentifier = $secOnlineIdentifier;

        return $this;
    }

    /**
     * Get secOnlineIdentifier
     *
     * @return string
     */
    public function getSecOnlineIdentifier()
    {
        return $this->secOnlineIdentifier;
    }

    /**
     * Set smallSecTitle
     *
     * @param string $smallSecTitle
     *
     * @return SecondarySource
     */
    public function setSmallSecTitle($smallSecTitle)
    {
        $this->smallSecTitle = $smallSecTitle;

        return $this;
    }

    /**
     * Get smallSecTitle
     *
     * @return string
     */
    public function getSmallSecTitle()
    {
        return $this->smallSecTitle;
    }

    /**
     *
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context)
    {
        if ($this->secType == "bookChapter") {
            if (! $this->collectiveBook) {
                $context->buildViolation('The field collective book must be filled in')
                    ->atPath('collectiveBook')
                    ->addViolation();
            }
        } elseif ($this->secType == "journalArticle") {
            if (! $this->journal) {
                $context->buildViolation('The field journal must be filled in')
                    ->atPath('journal')
                    ->addViolation();
            }
        } elseif ($this->secType == "onlineArticle"){
            if (! $this->secOnlineIdentifier){
                $context->buildViolation('The field URL must be filled in')
                    ->atPath('secOnlineIdentifier')
                    ->addViolation();
            }
        }
    }

    /**
     * ======================== toString ====================== *
     */
    public function getTitle()
    {
        $title = $this->secTitle ? GT::truncate($this->secTitle, 50) : "Untitled n°" . $this->id;
        $vol = "";
        if ($this->secType == "anthology" && $this->secVolume) {
            $vol = " (" . $this->secVolume . ")";
        }
        return $title . $vol;
    }

    public function __toString()
    {
        $title = $this->getTitle();
        $date = $this->secPubDate ? ", ".$this->secPubDate:"";
        $historians = "";
        $length = count($this->historians);
        foreach ($this->historians as $index => $historian) {
            $historians = $historians . $historian;
            if ($index !== $length - 1) {
                $historians = $historians . "; ";
            }
        }      

        return $title . ", " . $historians . $date;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "secondary source";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
        return GT::toPlural($name);
    }
}
