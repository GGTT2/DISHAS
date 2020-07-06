<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * Era
 *
 * @ORM\Table(name="era")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\EraRepository")
 */
class Era
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
     * @Type("string")
     * @var int
     *
     * @ORM\Column(name="epoch", type="integer",  nullable=true)
     */
    private $epoch;

    /**
     * @type("string")
     * @var int
     * 
     * @ORM\Column(name="month_shift", type="integer",  nullable=true)
     */
    private $monthShift;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TAMAS\AstroBundle\Entity\Calendar", inversedBy="eras")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $calendar;

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
     * @return Era
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
     * Set epoch.
     *
     * @param int $epoch
     *
     * @return Era
     */
    public function setEpoch($epoch)
    {
        $this->epoch = $epoch;

        return $this;
    }

    /**
     * Get epoch.
     *
     * @return int
     */
    public function getEpoch()
    {
        return $this->epoch;
    }

    /**
     * Get calendar
     *
     * @return \TAMAS\AstroBundle\Entity\Calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Set calendar.
     *
     * @param  \TAMAS\AstroBundle\Entity\Calendar $calendar
     *
     * @return Era
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function __toString()
    {
        return $this->calendar . ": " . $this->name;
    }

    /**
     * Set monthShift.
     *
     * @param int $monthShift
     *
     * @return Era
     */
    public function setMonthShift($monthShift)
    {
        $this->monthShift = $monthShift;

        return $this;
    }

    /**
     * Get monthShift.
     *
     * @return int
     */
    public function getMonthShift()
    {
        return $this->monthShift;
    }

    public function toPublicString($firstMonth = null)
    {
        $month = $firstMonth ? "<br>First month: ".$this->getCalendar()->getMonthName($firstMonth)." ($firstMonth)" : "";
        return "<span class='mainContent'>".ucfirst($this->calendar)." $this->name</span>$month";
    }
}
