<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PythonScript
 *
 * @ORM\Table(name="python_script")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\PythonScriptRepository")
 * @UniqueEntity(
 *     fields={"scriptUserName"},
 *     errorPath="scriptUserName",
 *     message="This name of script is alredy taken"
 * )
 * @Vich\Uploadable
 */
class PythonScript
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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="python_scripts", fileNameProperty="scriptName", size="scriptSize", originalName="scriptOriginalName")
     * 
     * @var File
     */
    private $scriptFile;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $scriptName;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $scriptUserName;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $scriptOriginalName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integerest
     */
    private $scriptSize;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;
    
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
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
    */
    private $updatedAt;
    
    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $script
     */
    public function setScriptFile($script = null)
    {
        $this->scriptFile = $script;

        if (null !== $script) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
        
        return $this;
    }
    
    public function getScriptFile() {
        return $this->scriptFile;
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
     * Set comment
     *
     * @param string $comment
     *
     * @return PythonScript
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return PythonScript
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
     *
     * @return PythonScript
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
     *
     * @return PythonScript
     */
    public function setCreatedBy(\TAMAS\AstroBundle\Entity\Users $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
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
     * @return PythonScript
     */
    public function setUpdatedBy(\TAMAS\AstroBundle\Entity\Users $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \TAMAS\AstroBundle\Entity\Users
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set scriptName
     *
     * @param string $scriptName
     *
     * @return PythonScript
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
    
    /**
     * This methode is called to determined the file name (as stored on the drive) of the script
     * 
     * @return string
     */
    public function getFullName() {
        //$sanitizer = $this->container->get('tamas_astro.sanitizer');
        $sanitizedName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '-', $this->getScriptUserName());
        $sanitizedName = preg_replace('/ /','-',$sanitizedName);
        $sanitizedAuthor = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '-', $this->createdBy->getUsernameCanonical());
        $sanitizedAuthor = preg_replace('/ /','-',$sanitizedAuthor);
        $sanitizedDate = $this->updatedAt->format('Y-m-d');
        $fullName = "DISHAS_".$sanitizedAuthor.'_'.$sanitizedName.'_'.$sanitizedDate;
        return $fullName;
    }

    /**
     * Set scriptOriginalName
     *
     * @param string $scriptOriginalName
     *
     * @return PythonScript
     */
    public function setScriptOriginalName($scriptOriginalName)
    {
        $this->scriptOriginalName = $scriptOriginalName;

        return $this;
    }

    /**
     * Get scriptOriginalName
     *
     * @return string
     */
    public function getScriptOriginalName()
    {
        return $this->scriptOriginalName;
    }

    /**
     * Set scriptSize
     *
     * @param integer $scriptSize
     *
     * @return PythonScript
     */
    public function setScriptSize($scriptSize)
    {
        $this->scriptSize = $scriptSize;

        return $this;
    }

    /**
     * Get scriptSize
     *
     * @return integer
     */
    public function getScriptSize()
    {
        return $this->scriptSize;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return PythonScript
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set scriptUserName
     *
     * @param string $scriptUserName
     *
     * @return PythonScript
     */
    public function setScriptUserName($scriptUserName)
    {
        $this->scriptUserName = $scriptUserName;

        return $this;
    }

    /**
     * Get scriptUserName
     *
     * @return string
     */
    public function getScriptUserName()
    {
        return $this->scriptUserName;
    }
}
