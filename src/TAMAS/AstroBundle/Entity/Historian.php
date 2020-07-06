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
 * Historian
 *
 * @ORM\Table(name="historian")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\HistorianRepository")
 * @UniqueEntity(fields={"lastName", "firstName"}, message="This intellectual author is already recorded in the database")
 */
class Historian
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
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Assert\NotNull()
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Assert\NotNull()
     */
    private $firstName;

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
     * @Groups({"editedTextMain", "formulaDefinitionMain"})
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
     * Set id
     *
     * @param int $id
     *
     * @return Historian
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Historian
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Historian
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Historian
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
     * @return Historian
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
     * @return Historian
     */
    public function setCreatedBy(\TAMAS\AstroBundle\Entity\Users $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string : \TAMAS\AstroBundle\Entity\Users
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
     * @return Historian
     */
    public function setUpdatedBy(\TAMAS\AstroBundle\Entity\Users $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return string : \TAMAS\AstroBundle\Entity\Users
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function __toString()
    {
        if ($this->lastName) {
            $lastName = $this->lastName;
            if ($this->firstName) {
                $lastName = $lastName . " (" . $this->firstName . ")";
            }
        } else {
            $lastName = "Anonymous n°" . $this->id;
        }
        return $lastName;
    }

    public function toPublicString()
    {
        $firstName = $this->firstName ? ucfirst(strval($this->firstName)) . " " : "";
        $lastName = $this->lastName ? ucfirst(strval($this->lastName)) : "<span class='noInfo'>Anonymous n°" . strval($this->id) . "</span>";

        return $firstName . $lastName;
    }

    /**
     * returns the object entity name as formulated for the user interface
     * @param bool $isPlural : library => libraries
     * @param bool $hasDeterminer : edition => an edition
     * @return mixed|string
     */
    public static function getInterfaceName($isPlural = false, $hasDeterminer = false)
    {
        $name = "intellectual author";
        if (! $isPlural && !$hasDeterminer)
            return $name;
        if ($hasDeterminer)
            return GT::toDeterminer($name);
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
