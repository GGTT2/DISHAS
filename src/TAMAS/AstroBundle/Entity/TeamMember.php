<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * TeamMember
 *
 * @ORM\Table(name="team_member")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\TeamMemberRepository")
 * @UniqueEntity(fields={"lastName","firstName"}, ignoreNull = false, message="This team member is already recorded in the database")

 */
class TeamMember
{
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
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="affiliation", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $affiliation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="support", type="string", length=1000, nullable=true)
     *      
     */
    private $support;

    /**
     * @var array|null
     *
     * @ORM\Column(name="role", type="array", length=1000, nullable=true)
     * @Assert\NotBlank()
     */
    private $role;

    /**
     * @var string|null
     *
     * @ORM\Column(name="presentation", type="text", nullable=true)
     * @Assert\NotBlank()
     *
     */
    private $presentation;

    /**
     * @var array|null
     *
     * @ORM\Column(name="links", type="array", nullable=true)
     */
    private $links;


    /**
     * @ORM\OneToOne(targetEntity="TAMAS\AstroBundle\Entity\ImageFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Assert\Valid()
     */
    private $picture;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lastName.
     *
     * @param string|null $lastName
     *
     * @return TeamMember
     */
    public function setLastName($lastName = null)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName.
     *
     * @param string|null $firstName
     *
     * @return TeamMember
     */
    public function setFirstName($firstName = null)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set affiliation.
     *
     * @param string|null $affiliation
     *
     * @return TeamMember
     */
    public function setAffiliation($affiliation = null)
    {
        $this->affiliation = $affiliation;

        return $this;
    }

    /**
     * Get affiliation.
     *
     * @return string|null
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * Set support.
     *
     * @param string|null $support
     *
     * @return TeamMember
     */
    public function setSupport($support = null)
    {
        $this->support = $support;

        return $this;
    }

    /**
     * Get support.
     *
     * @return string|null
     */
    public function getSupport()
    {
        return $this->support;
    }

    /**
     * Set role.
     *
     * @param string|null $role
     *
     * @return TeamMember
     */
    public function setRole($role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return string|null
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set presentation.
     *
     * @param string|null $presentation
     *
     * @return TeamMember
     */
    public function setPresentation($presentation = null)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation.
     *
     * @return string|null
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set links.
     *
     * @param array|null $array
     *
     * @return TeamMember
     */
    public function setLinks($links = null)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Get link.
     *
     * @return array|null
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return TeamMember
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
     * @return TeamMember
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
     * Set picture.
     *
     * @param \TAMAS\AstroBundle\Entity\ImageFile|null $picture
     *
     * @return TeamMember
     */
    public function setPicture(\TAMAS\AstroBundle\Entity\ImageFile $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return \TAMAS\AstroBundle\Entity\ImageFile|null
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set createdBy.
     *
     * @param \TAMAS\AstroBundle\Entity\Users|null $createdBy
     *
     * @return TeamMember
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
     * @return TeamMember
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

    public function __toString()
    {
        if ($this->lastName && $this->firstName){
            return ucfirst($this->lastName).' '.ucfirst($this->firstName);
        } else {
            return "<span class='noInfo'>Unknown member</span>";
        }
    }
}
