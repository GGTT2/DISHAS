<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * Calendar
 *
 * @ORM\Table(name="calendar")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\CalendarRepository")
 */
class Calendar
{

    /**
     * This static attributes state if the class object can be created, edited or deleted by admin users through the database interface
     */
    static $manageable = false;
    
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
     * @ORM\Column(name="name", type="string", length=500)
     */
    private $name;

    /**
     * @var array|null
     *
     * @ORM\Column(name="month_list", type="json_array", nullable=true)
     */
    private $monthList;

    /**
     *
     * @ORM\OneToMany(targetEntity="TAMAS\AstroBundle\Entity\Era", mappedBy="calendar")
     */
    private $eras;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eras = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Calendar
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set monthList.
     *
     * @param array|null $monthList
     *
     * @return Calendar
     */
    public function setMonthList($monthList = null)
    {
        $this->monthList = $monthList;

        return $this;
    }

    /**
     * Get monthList.
     *
     * @return array|null
     */
    public function getMonthList()
    {
        return $this->monthList;
    }


    //     /**
    //      * Add era
    //      *
    //      * @param \TAMAS\AstroBundle\Entity\Era $era
    //      *
    //      * @return Calendar
    //      */
    //     public function addEra(\TAMAS\AstroBundle\Entity\Era $era)
    //     {
    //         $this->eras[] = $era;
    //         $era->addCalendar($this);

    //         return $this;
    //     }

    //     /**
    //      * Remove era
    //      *
    //      * @param \TAMAS\AstroBundle\Entity\Era $era
    //      */
    //     public function removeEra(\TAMAS\AstroBundle\Entity\Era $era)
    //     {
    //         $this->era->removeElement($era);
    //         $era->removeCalendar($this);
    //     }

    /**
     * Get eras
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEras()
    {
        return $this->eras;
    }

    public function getMonthName($monthNumber)
    {
        return $this->getMonthList()[$monthNumber];
    }

    public function __toString()
    {
        return $this->name;
    }
}
