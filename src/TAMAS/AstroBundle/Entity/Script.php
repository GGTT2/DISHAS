<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Script
 *
 * @ORM\Table(name="script")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ScriptRepository")
 */
class Script
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
     * @ORM\Column(name="iso_15924", type="string", length=4)
     */
    private $iso15924;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="script_name", type="string", length=255)
     */
    private $scriptName;

    public function __toString()
    {
        return $this->scriptName;
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
     * Set iso15924
     *
     * @param string $iso15924
     *
     * @return Script
     */
    public function setIso15924($iso15924)
    {
        $this->iso15924 = $iso15924;

        return $this;
    }

    /**
     * Get iso15924
     *
     * @return string
     */
    public function getIso15924()
    {
        return $this->iso15924;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Script
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set scriptName
     *
     * @param string $scriptName
     *
     * @return Script
     */
    public function setScriptName($scriptName)
    {
        $this->scriptName = $scriptName;

        return $this;
    }

    /**
     * Get scriptName
     *
     * @return string
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }

    public function toPublicString()
    {
        $this->scriptName ? $scriptName = "<span class='mainContent'>" . strval($this->scriptName) . "</span>" : $scriptName = "<span class='noInfo'>Unknown script n°" . strval($this->id) . "</span>";
        $this->iso15924 ? $scriptName = $scriptName . "<br/>ISO 15924 : " . strval($this->iso15924) : $scriptName;

        return $scriptName;
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
