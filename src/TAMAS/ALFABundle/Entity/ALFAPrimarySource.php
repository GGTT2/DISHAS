<?php

namespace TAMAS\ALFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrimarySource
 *
 * @ORM\Table(name="alfa_primary_source")
 * @ORM\Entity(repositoryClass="TAMAS\ALFABundle\Repository\PrimarySourceRepository")
 */
class ALFAPrimarySource
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
     * @ORM\Column(name="collection", type="string", length=255, nullable=true)
     */
    private $collection;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shelfmark", type="string", length=500, nullable=true)
     */
    private $shelfmark;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=5000, nullable=true)
     */
    private $title;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="extent", type="string", length=5000, nullable=true)
     */
    private $extent;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="dimension", type="string", length=5000, nullable=true)
     */
    private $dimension;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="editor", type="string", length=5000, nullable=true)
     */
    private $editor;
    
        /**
     * @var bool
     *
     * @ORM\Column(name="early_printed", type="boolean", nullable=true)
     */
    private $earlyPrinted;
    
     

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
     * @var string|null
     *
     * @ORM\Column(name="place_of_production", type="string", length=1000, nullable=true)
     */
    private $placeOfProduction;

    /**
     * @var string|null
     *
     * @ORM\Column(name="historicalActor", type="text", nullable=true)
     */
    private $historicalActor;

    
    
    /**
     * @ORM\ManyToOne(targetEntity="TAMAS\ALFABundle\Entity\ALFALibrary")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $library;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;


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
     * Set collection.
     *
     * @param string|null $collection
     *
     * @return PrimarySource
     */
    public function setCollection($collection = null)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection.
     *
     * @return string|null
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set shelfmark.
     *
     * @param string|null $shelfmark
     *
     * @return PrimarySource
     */
    public function setShelfmark($shelfmark = null)
    {
        $this->shelfmark = $shelfmark;

        return $this;
    }

    /**
     * Get shelfmark.
     *
     * @return string|null
     */
    public function getShelfmark()
    {
        return $this->shelfmark;
    }

    /**
     * Set tpq.
     *
     * @param int|null $tpq
     *
     * @return PrimarySource
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
     * @return PrimarySource
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
     * Set placeOfProduction.
     *
     * @param string|null $placeOfProduction
     *
     * @return PrimarySource
     */
    public function setPlaceOfProduction($placeOfProduction = null)
    {
        $this->placeOfProduction = $placeOfProduction;

        return $this;
    }

    /**
     * Get placeOfProduction.
     *
     * @return string|null
     */
    public function getPlaceOfProduction()
    {
        return $this->placeOfProduction;
    }

    /**
     * Set historicalActor.
     *
     * @param string|null $historicalActor
     *
     * @return PrimarySource
     */
    public function setHistoricalActor($historicalActor = null)
    {
        $this->historicalActor = $historicalActor;

        return $this;
    }

    /**
     * Get historicalActor.
     *
     * @return string|null
     */
    public function getHistoricalActor()
    {
        return $this->historicalActor;
    }

   
    /**
     * Set library.
     *
     * @param \TAMAS\ALFABundle\Entity\ALFALibrary|null $library
     *
     * @return ALFAPrimarySource
     */
    public function setLibrary(\TAMAS\ALFABundle\Entity\ALFALibrary $library = null)
    {
        $this->library = $library;

        return $this;
    }

    /**
     * Get library.
     *
     * @return \TAMAS\ALFABundle\Entity\ALFALibrary|null
     */
    public function getLibrary()
    {
        return $this->library;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return ALFAPrimarySource
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set editor.
     *
     * @param string|null $editor
     *
     * @return ALFAPrimarySource
     */
    public function setEditor($editor = null)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get editor.
     *
     * @return string|null
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set earlyPrinted.
     *
     * @param bool|null $earlyPrinted
     *
     * @return ALFAPrimarySource
     */
    public function setEarlyPrinted($earlyPrinted = null)
    {
        $this->earlyPrinted = $earlyPrinted;

        return $this;
    }

    /**
     * Get earlyPrinted.
     *
     * @return bool|null
     */
    public function getEarlyPrinted()
    {
        return $this->earlyPrinted;
    }

    /**
     * Set extent.
     *
     * @param string|null $extent
     *
     * @return ALFAPrimarySource
     */
    public function setExtent($extent = null)
    {
        $this->extent = $extent;

        return $this;
    }

    /**
     * Get extent.
     *
     * @return string|null
     */
    public function getExtent()
    {
        return $this->extent;
    }

    /**
     * Set dimension.
     *
     * @param string|null $dimension
     *
     * @return ALFAPrimarySource
     */
    public function setDimension($dimension = null)
    {
        $this->dimension = $dimension;

        return $this;
    }

    /**
     * Get dimension.
     *
     * @return string|null
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return ALFAPrimarySource
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function __toString()
    {
        return $this->id.': '.$this->shelfmark.', '.$this->tpq.'-'.$this->taq.' in '.$this->url;
    }
}
