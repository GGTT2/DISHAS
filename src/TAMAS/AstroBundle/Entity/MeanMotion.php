<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use TAMAS\AstroBundle\Validator\Constraints as AstroAssert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

/**
 * MeanMotion
 *
 * @ORM\Table(name="mean_motion")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\MeanMotionRepository")
 */
class MeanMotion
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = true;
    
    /**
     * @var int
     *
     * @Groups({"tableContentMain"})
     * @Type("string")
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool|null
     * 
     * @Groups({"tableContentMain"})
     *
     * @ORM\Column(name="complete_time", type="boolean", nullable=true)
     * 
     * States if the calendrical information are understood as "complete" or "incomplete"
     */
    private $completeTime;

    /**
     * @var string|null
     * @Groups({"tableContentMain"})
     * 
     * @Assert\Type("string")
     * @ORM\Column(name="place_name", type="string", length=1000, nullable=true)
     */
    private $placeName;

    /**
     * @var string|null
     * 
     * @Groups({"tableContentMain"})
     *
     * @Assert\Type("string")
     * @ORM\Column(name="place_name_translit", type="string", length=1000, nullable=true)
     */
    private $placeNameTranslit;

    /**
     * @var string|null
     * 
     * @Groups({"tableContentMain"})
     *
     * @Assert\Type("string")
     * @ORM\Column(name="place_name_original_char", type="string", length=1000, nullable=true)
     */
    private $placeNameOriginalChar;

    /**
     * @var string|null
     * 
     * @Groups({"tableContentMain"})
     *
     * @AstroAssert\IntSexa
     * @ORM\Column(name="long_orig_base", type="string", length=255, nullable=true)
     * 
     * Longitude of the place according to the base meridian
     */
    private $longOrigBase;

    /**
     * @var float|null
     * 
     * @Groups({"tableContentMain"})
     * @Type("string")
     * 
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     *)
     * @ORM\Column(name="long_float", type="float", nullable=true)
     */
    private $longFloat;

    
    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber")
     * @ORM\JoinColumn(nullable=true)
     */
    private $longTypeOfNumber;

    /**
     * @var string|null
     *
     * @Groups({"tableContentMain"})
     * @ORM\Column(name="meridian", type="string", length=1000, nullable=true)
     * 
     * @Assert\Type("string")
     * base meridian
     */
    private $meridian;

    /**
     * @var string|null
     *
     * @Groups({"tableContentMain"})
     * @Assert\Type("string")
     * @ORM\Column(name="meridian_translit", type="string", length=1000, nullable=true)
     */
    private $meridianTranslit;

    /**
     * @var string|null
     *
     * @Groups({"tableContentMain"})
     * @Assert\Type("string")
     * @ORM\Column(name="meridian_original_char", type="string", length=1000, nullable=true)
     */
    private $meridianOriginalChar;

   
    /**
     * @var string|null
     * 
     * @Groups({"tableContentMain"})
     * @AstroAssert\IntSexa
     * @ORM\Column(name="root_orig_base", type="string", length=255, nullable=true)
     */
    private $rootOrigBase;

    /**
     * @var float|null
     * 
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     *)
     * @ORM\Column(name="root_float", type="float", nullable=true)
     */
    private $rootFloat;
    
    
    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\TypeOfNumber")
     * @ORM\JoinColumn(nullable=true)
     */
    private $rootTypeOfNumber;
    
    
    /**
     * @var float|null
     * 
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     *)
     * @ORM\Column(name="epoch", type="float", nullable=true)
     * 
     * Epoch of the table expressed in JDN
     */
    private $epoch;

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
     * @var int|null
     * 
     * @Groups({"tableContentMain"})
     * @Type("string")
     *
     * @ORM\Column(name="first_month", type="integer", nullable=true)
     */
    private $firstMonth;

    
    /**
     * @Groups({"tableContentMain"})
     * @Type("string")
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\SubTimeUnit")
     * @ORM\JoinColumn(nullable=true)
     */
    private $subTimeUnit;


    /**
     * @Groups({"tableContentMain"})
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
     * Generates the kibanaName
     *
     */
    private function preSerialize() {
        $this->kibanaName = $this->__toString();
        $this->kibanaId = PreSerializeTools::generateKibanaId($this);
    }

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
     * Set completeTime.
     *
     * @param bool|null $completeTime
     *
     * @return MeanMotion
     */
    public function setCompleteTime($completeTime = null)
    {
        $this->completeTime = $completeTime;

        return $this;
    }

    /**
     * Get completeTime.
     *
     * @return bool|null
     */
    public function getCompleteTime()
    {
        return $this->completeTime;
    }

    /**
     * Set placeName.
     *
     * @param string|null $placeName
     *
     * @return MeanMotion
     */
    public function setPlaceName($placeName = null)
    {
        $this->placeName = $placeName;

        return $this;
    }

    /**
     * Get placeName.
     *
     * @return string|null
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * Set placeNameTranslit.
     *
     * @param string|null $placeNameTranslit
     *
     * @return MeanMotion
     */
    public function setPlaceNameTranslit($placeNameTranslit = null)
    {
        $this->placeNameTranslit = $placeNameTranslit;

        return $this;
    }

    /**
     * Get placeNameTranslit.
     *
     * @return string|null
     */
    public function getPlaceNameTranslit()
    {
        return $this->placeNameTranslit;
    }

    /**
     * Set placeNameOriginalChar.
     *
     * @param string|null $placeNameOriginalChar
     *
     * @return MeanMotion
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

    /**
     * Set longOrigBase.
     *
     * @param string|null $longOrigBase
     *
     * @return MeanMotion
     */
    public function setLongOrigBase($longOrigBase = null)
    {
        $this->longOrigBase = $longOrigBase;

        return $this;
    }

    /**
     * Get longOrigBase.
     *
     * @return string|null
     */
    public function getLongOrigBase()
    {
        return $this->longOrigBase;
    }

    /**
     * Set longFloat.
     *
     * @param float|null $longFloat
     *
     * @return MeanMotion
     */
    public function setLongFloat($longFloat = null)
    {
        $this->longFloat = $longFloat;

        return $this;
    }

    /**
     * Get longFloat.
     *
     * @return float|null
     */
    public function getLongFloat()
    {
        return $this->longFloat;
    }

    /**
     * Set meridian.
     *
     * @param string|null $meridian
     *
     * @return MeanMotion
     */
    public function setMeridian($meridian = null)
    {
        $this->meridian = $meridian;

        return $this;
    }

    /**
     * Get meridian.
     *
     * @return string|null
     */
    public function getMeridian()
    {
        return $this->meridian;
    }

    /**
     * Set meridianTranslit.
     *
     * @param string|null $meridianTranslit
     *
     * @return MeanMotion
     */
    public function setMeridianTranslit($meridianTranslit = null)
    {
        $this->meridianTranslit = $meridianTranslit;

        return $this;
    }

    /**
     * Get meridianTranslit.
     *
     * @return string|null
     */
    public function getMeridianTranslit()
    {
        return $this->meridianTranslit;
    }

    /**
     * Set meridianOriginalChar.
     *
     * @param string|null $meridianOriginalChar
     *
     * @return MeanMotion
     */
    public function setMeridianOriginalChar($meridianOriginalChar = null)
    {
        $this->meridianOriginalChar = $meridianOriginalChar;

        return $this;
    }

    /**
     * Get meridianOriginalChar.
     *
     * @return string|null
     */
    public function getMeridianOriginalChar()
    {
        return $this->meridianOriginalChar;
    }



    /**
     * Set rootOrigBase.
     *
     * @param string|null $rootOrigBase
     *
     * @return MeanMotion
     */
    public function setRootOrigBase($rootOrigBase = null)
    {
        $this->rootOrigBase = $rootOrigBase;

        return $this;
    }

    /**
     * Get rootOrigBase.
     *
     * @return string|null
     */
    public function getRootOrigBase()
    {
        return $this->rootOrigBase;
    }

    /**
     * Set rootFloat.
     *
     * @param float|null $rootFloat
     *
     * @return MeanMotion
     */
    public function setRootFloat($rootFloat = null)
    {
        $this->rootFloat = $rootFloat;

        return $this;
    }

    /**
     * Get rootFloat.
     *
     * @return float|null
     */
    public function getRootFloat()
    {
        return $this->rootFloat;
    }


    
    /**
     * Set epoch.
     *
     * @param float|null $epoch
     *
     * @return MeanMotion
     */
    public function setEpoch($epoch = null)
    {
        $this->epoch =$epoch;
        
        return $this;
    }
    
    /**
     * Get epoch.
     *
     * @return float|null
     */
    public function getEpoch()
    {
        return $this->epoch;
    }

    
    /**
     * Set firstMonth.
     *
     * @param int|null $firstMonth
     *
     * @return MeanMotion
     */
    public function setFirstMonth($firstMonth = null)
    {
        $this->firstMonth = $firstMonth;

        return $this;
    }

    /**
     * Get firstMonth.
     *
     * @return int|null
     */
    public function getFirstMonth()
    {
        return $this->firstMonth;
    }

    /**
     * Set longTypeOfNumber.
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $longTypeOfNumber
     *
     * @return MeanMotion
     */
    public function setLongTypeOfNumber(\TAMAS\AstroBundle\Entity\TypeOfNumber $longTypeOfNumber)
    {
        $this->longTypeOfNumber = $longTypeOfNumber;

        return $this;
    }

    /**
     * Get longTypeOfNumber.
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getLongTypeOfNumber()
    {
        return $this->longTypeOfNumber;
    }

    /**
     * Set rootTypeOfNumber.
     *
     * @param \TAMAS\AstroBundle\Entity\TypeOfNumber $rootTypeOfNumber
     *
     * @return MeanMotion
     */
    public function setRootTypeOfNumber(\TAMAS\AstroBundle\Entity\TypeOfNumber $rootTypeOfNumber)
    {
        $this->rootTypeOfNumber = $rootTypeOfNumber;

        return $this;
    }

    /**
     * Get rootTypeOfNumber.
     *
     * @return \TAMAS\AstroBundle\Entity\TypeOfNumber
     */
    public function getRootTypeOfNumber()
    {
        return $this->rootTypeOfNumber;
    }

 
    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return MeanMotion
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return MeanMotion
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set createdBy.
     *
     * @param \TAMAS\AstroBundle\Entity\Users|null $createdBy
     *
     * @return MeanMotion
     */
    public function setCreatedBy(\TAMAS\AstroBundle\Entity\Users $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return \TAMAS\AstroBundle\Entity\Users|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy.
     *
     * @param \TAMAS\AstroBundle\Entity\Users|null $updatedBy
     *
     * @return MeanMotion
     */
    public function setUpdatedBy(\TAMAS\AstroBundle\Entity\Users $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy.
     *
     * @return \TAMAS\AstroBundle\Entity\Users|null
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set subTimeUnit.
     *
     * @param \TAMAS\AstroBundle\Entity\SubTimeUnit $subTimeUnit
     *
     * @return MeanMotion
     */
    public function setSubTimeUnit(\TAMAS\AstroBundle\Entity\SubTimeUnit $subTimeUnit = null)
    {
        $this->subTimeUnit = $subTimeUnit;

        return $this;
    }

    /**
     * Get subTimeUnit.
     *
     * @return \TAMAS\AstroBundle\Entity\SubTimeUnit
     */
    public function getSubTimeUnit()
    {
        return $this->subTimeUnit;
    }

    public function hasLocalizationParameters(){
        if($this->placeName || 
        $this->placeNameTranslit || $this->placeNameOriginalChar || $this->longOrigBase || $this->longFloat || $this->meridian || 
        $this->meridianTranslit || $this->meridianOriginalChar || $this->rootOrigBase || $this->rootFloat || $this->epoch){
            return true;
        }
    }
    public function setNullLocalizationParameters(){
        $this->placeName = null;
        $this->placeNameTranslit = null; $this->placeNameOriginalChar = null; $this->longOrigBase = null; $this->longFloat = null; $this->meridian = null;
        $this->meridianTranslit = null;$this->meridianOriginalChar = null; $this->rootOrigBase= null; $this->rootFloat= null; $this->epoch= null;
    }


    /**
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context)
    {

        if((!($this->subTimeUnit) || $this->subTimeUnit->getId() !== 1) && $this->hasLocalizationParameters() ){
            //add error : this subtime is not supposed to allow parameters. 
            /* $context->buildViolation('Only "collected years" mean motion can accept localization parameters.
            They will be automatically removed. You can save again the document.')
                ->addViolation(); */
            //delete all the loc parameters: 
            $this->setNullLocalizationParameters();
            //in order to garantee the saving in AJAX from DTI, this step is done automatically, without warning.
            //in the case where the front is bugged, we need to assure the fact that the table is saved while the user won't be able to correct it manually.  
        }
    }

    public function __toString(){
        return "mm n°".$this->id;
    }
}
