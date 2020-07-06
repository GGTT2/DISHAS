<?php

namespace TAMAS\ALFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrimarySourceWork
 *
 * @ORM\Table(name="alfa_primary_source_work")
 * @ORM\Entity(repositoryClass="TAMAS\ALFABundle\Repository\PrimarySourceWorkRepository")
 */
class ALFAPrimarySourceWork {

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
     * @ORM\Column(name="locus_from", type="string", length=255, nullable=true)
     */
    private $locusFrom;
    
     /**
     * @var string
     *
     * @ORM\Column(name="locus_to", type="string", length=255, nullable=true)
     */
    private $locusTo;
    
    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\ALFABundle\Entity\ALFAWork")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $work;
    
    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\ALFABundle\Entity\ALFAAuthority")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $authority;
    
    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\ALFABundle\Entity\ALFAPrimarySource")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $primarySource;
    
     /**
     * @var string
     *
     * @ORM\Column(name="info_source", type="text")
     */
    private $infoSource;
    


    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }




    /**
     * Set locusFrom.
     *
     * @param string|null $locusFrom
     *
     * @return ALFAPrimarySourceWork
     */
    public function setLocusFrom($locusFrom = null)
    {
        $this->locusFrom = $locusFrom;

        return $this;
    }

    /**
     * Get locusFrom.
     *
     * @return string|null
     */
    public function getLocusFrom()
    {
        return $this->locusFrom;
    }

    /**
     * Set locusTo.
     *
     * @param string|null $locusTo
     *
     * @return ALFAPrimarySourceWork
     */
    public function setLocusTo($locusTo = null)
    {
        $this->locusTo = $locusTo;

        return $this;
    }

    /**
     * Get locusTo.
     *
     * @return string|null
     */
    public function getLocusTo()
    {
        return $this->locusTo;
    }

    /**
     * Set work.
     *
     * @param \TAMAS\ALFABundle\Entity\ALFAWork $work
     *
     * @return ALFAPrimarySourceWork
     */
    public function setWork(\TAMAS\ALFABundle\Entity\ALFAWork $work)
    {
        $this->work = $work;

        return $this;
    }

    /**
     * Get work.
     *
     * @return \TAMAS\ALFABundle\Entity\ALFAWork
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * Set primarySource.
     *
     * @param \TAMAS\ALFABundle\Entity\ALFAPrimarySource $primarySource
     *
     * @return ALFAPrimarySourceWork
     */
    public function setPrimarySource(\TAMAS\ALFABundle\Entity\ALFAPrimarySource $primarySource)
    {
        $this->primarySource = $primarySource;

        return $this;
    }

    /**
     * Get primarySource.
     *
     * @return \TAMAS\ALFABundle\Entity\ALFAPrimarySource
     */
    public function getPrimarySource()
    {
        return $this->primarySource;
    }
    
        /**
     * Set infoSource.
     *
     * @param string $infoSource
     *
     * @return PrimarySource
     */
    public function setInfoSource($infoSource)
    {
        $this->infoSource = $infoSource;

        return $this;
    }

    /**
     * Get infoSource.
     *
     * @return string
     */
    public function getInfoSource()
    {
        return $this->infoSource;
    }
    


    /**
     * Set authority.
     *
     * @param \TAMAS\ALFABundle\Entity\ALFAAuthority $authority
     *
     * @return ALFAPrimarySourceWork
     */
    public function setAuthority(\TAMAS\ALFABundle\Entity\ALFAAuthority $authority)
    {
        $this->authority = $authority;

        return $this;
    }

    /**
     * Get authority.
     *
     * @return \TAMAS\ALFABundle\Entity\ALFAAuthority
     */
    public function getAuthority()
    {
        return $this->authority;
    }
}
