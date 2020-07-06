<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * Language
 *
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\LanguageRepository")
 */
class Language
{

    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
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
     * @ORM\Column(name="iso_639_2", type="string", length=3, nullable=true)
     */
    private $iso6392;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_639_1", type="string", length=2, nullable=true)
     */
    private $iso6391;

    /**
     * @var string
     *
     * @ORM\Column(name="language_name", type="string", length=255)
     */
    private $languageName;


    public function __toString()
    {
        return $this->languageName;
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
     * Set iso6392
     *
     * @param string $iso6392
     *
     * @return Language
     */
    public function setIso6392($iso6392)
    {
        $this->iso6392 = $iso6392;

        return $this;
    }

    /**
     * Get iso6392
     *
     * @return string
     */
    public function getIso6392()
    {
        return $this->iso6392;
    }

    /**
     * Set iso6391
     *
     * @param string $iso6391
     *
     * @return Language
     */
    public function setIso6391($iso6391)
    {
        $this->iso6391 = $iso6391;

        return $this;
    }

    /**
     * Get iso6391
     *
     * @return string
     */
    public function getIso6391()
    {
        return $this->iso6391;
    }

    /**
     * Set languageName
     *
     * @param string $languageName
     *
     * @return Language
     */
    public function setLanguageName($languageName)
    {
        $this->languageName = $languageName;

        return $this;
    }

    /**
     * Get languageName
     *
     * @return string
     */
    public function getLanguageName()
    {
        return $this->languageName;
    }

    public function toPublicString()
    {
        $this->languageName ? $languageName = "<span class='mainContent'>" . strval($this->languageName) . "</span>" : $languageName = "<span class='noInfo'>Unknown language n°" . strval($this->id) . "</span>";
        $this->iso6392 ? $languageName = $languageName . "<br/>ISO 639-2 : " . strval($this->iso6392) : $languageName;

        return $languageName;
    }
}
