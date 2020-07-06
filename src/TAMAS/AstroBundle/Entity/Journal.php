<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;


/**
 * Journal
 *
 * @ORM\Table(name="journal")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\JournalRepository")
 * @UniqueEntity(fields="journalTitle", message="This title is already recorded in the database.")

 */
class Journal {

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
     * @ORM\Column(name="journal_title", type="string", length=500)
     * @Assert\NotBlank()
     */
    private $journalTitle;

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
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;
    
    /**
     * @PreSerialize
     */
    private function onPreSerialize(){
        $this->kibanaName = $this->__toString();
        $this->kibanaId = \TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools::generateKibanaId($this);
        
    }
    

    public function __toString(){
        if ($this->journalTitle){
            $title = $this->journalTitle;
        } else {
            $title = "Untitled n°" . $this->id;
        }
        return $title;}
    
    /**
     * @Groups({"elastica"})
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @Groups({"elastica"})
     * Set id
     *
     * @param int $id
     *
     * @return Journal
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Set journalTitle
     *
     * @param string $journalTitle
     *
     * @return Journal
     */
    public function setJournalTitle($journalTitle) {
        $this->journalTitle = $journalTitle;

        return $this;
    }

    /**
     * Get journalTitle
     *
     * @return string
     */
    public function getJournalTitle() {
        return $this->journalTitle;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Journal
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Journal
     */
    public function setUpdated($updated) {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * Set createdBy
     *
     * @param \TAMAS\AstroBundle\Entity\Users $createdBy
     *
     * @return Journal
     */
    public function setCreatedBy(\TAMAS\AstroBundle\Entity\Users $createdBy = null) {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \TAMAS\AstroBundle\Entity\Users $updatedBy
     *
     * @return Journal
     */
    public function setUpdatedBy(\TAMAS\AstroBundle\Entity\Users $updatedBy = null) {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
     */
    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    public function toPublicString() {
        return $this->journalTitle ? $this->journalTitle : "<span class='noInfo'>Journal n°$this->id</span>";
    }

    public static $definition = [
        "objectEntityName" => "journal",
        "objectDatabaseName" => "journal",
        "objectUserInterfaceName" => "journal",
        "longDefinition" => "",
        "shortDefinition" => "",
        "userInterfaceColor" => ""
    ];

    public static function getInterfaceName($isPlural = false)
    {
        $name = "journal";
        if (! $isPlural)
            return $name;
        return GT::toPlural($name);
    }

    //____________________________ This method is added to basic entity in order to be able to check in controller if form-submitted entities have only null attributes. 
    //This method is used in the context or search originalText. This is still a draft method.  
//see draft section of originalTextRepository
//    public function checkIfNull() {
//        foreach ($this as $value) {
//            if ($value != null) {
//                return false;
//            }
//        }
//        return true;
//    }

}
