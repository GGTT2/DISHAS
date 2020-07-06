<?php

namespace TAMAS\ALFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Work
 *
 * @ORM\Table(name="alfa_work")
 * @ORM\Entity(repositoryClass="TAMAS\ALFABundle\Repository\WorkRepository")
 */
class ALFAWork
{
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
     * @ORM\Column(name="place", type="string", length=1000, nullable=true)
     */
    private $place;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tpq", type="integer", nullable=true)
     */
    private $tpq;

    /**
     * @var int|null
     *
     * @ORM\Column(name="taq", type="integer", nullable=true)
     */
    private $taq;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\ALFABundle\Entity\ALFAWorkType")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $workType;
    
    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\ALFABundle\Entity\ALFAAuthor")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $author;

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
     * Set place.
     *
     * @param string|null $place
     *
     * @return Work
     */
    public function setPlace($place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return string|null
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set tpq.
     *
     * @param int|null $tpq
     *
     * @return Work
     */
    public function setTpq($tpq = null)
    {
        $this->tpq = $tpq;

        return $this;
    }

    /**
     * Get tpq.
     *
     * @return int|null
     */
    public function getTpq()
    {
        return $this->tpq;
    }

    /**
     * Set taq.
     *
     * @param int|null $taq
     *
     * @return Work
     */
    public function setTaq($taq = null)
    {
        $this->taq = $taq;

        return $this;
    }

    /**
     * Get taq.
     *
     * @return int|null
     */
    public function getTaq()
    {
        return $this->taq;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Work
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set workType.
     *
     * @param \TAMAS\ALFABundle\Entity\ALFAWorkType|null $workType
     *
     * @return ALFAWork
     */
    public function setWorkType(\TAMAS\ALFABundle\Entity\ALFAWorkType $workType = null)
    {
        $this->workType = $workType;

        return $this;
    }

    /**
     * Get workType.
     *
     * @return \TAMAS\ALFABundle\Entity\ALFAWorkType|null
     */
    public function getWorkType()
    {
        return $this->workType;
    }

    /**
     * Set author.
     *
     * @param \TAMAS\ALFABundle\Entity\ALFAAuthor|null $author
     *
     * @return ALFAWork
     */
    public function setAuthor(\TAMAS\ALFABundle\Entity\ALFAAuthor $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return \TAMAS\ALFABundle\Entity\ALFAAuthor|null
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
