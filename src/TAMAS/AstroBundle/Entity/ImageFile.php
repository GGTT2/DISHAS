<?php

namespace TAMAS\AstroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * ImageFile
 *
 * @ORM\Table(name="image_file")
 * @ORM\Entity(repositoryClass="TAMAS\AstroBundle\Repository\ImageFileRepository")
 * @UniqueEntity(
 *     fields={"fileUserName"},
 *     errorPath="fileUserName",
 *     message="This name of image is alredy taken"
 * )
 * @Vich\Uploadable
 */
class ImageFile
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
     * @Vich\UploadableField(mapping="image_file", fileNameProperty="fileName", size="fileSize", originalName="fileOriginalName")
     * @Assert\File(maxSize="1M")
     * @var File
     */
    private $imageFile;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $fileName;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $fileUserName;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $fileOriginalName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integerest
     */
    private $fileSize;
    
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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setImageFile($file = null)
    {
        $this->imageFile = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
        
        return $this;
    }
    
    public function getImageFile() {
        return $this->imageFile;
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return ImageFile
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
     * @return ImageFile
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
     * @return ImageFile
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
     * @return ImageFile
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
     * Set fileName
     *
     * @param string $fileName
     *
     * @return ImageFile
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
    
    /**
     * This methode is called to determined the file name (as stored on the drive) of the file
     * 
     * @return string
     */
    public function getFullName() {
        //$sanitizer = $this->container->get('tamas_astro.sanitizer');
        $sanitizedName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '-', $this->getFileUserName());
        $sanitizedName = preg_replace('/ /','-',$sanitizedName);
        $sanitizedAuthor = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '-', $this->createdBy->getUsernameCanonical());
        $sanitizedAuthor = preg_replace('/ /','-',$sanitizedAuthor);
        $sanitizedDate = $this->updatedAt->format('Y-m-d');
        $fullName = "DISHAS_".$sanitizedAuthor.'_'.$sanitizedName.'_'.$sanitizedDate;
        return $fullName;
    }

    /**
     * Set fileOriginalName
     *
     * @param string $fileOriginalName
     *
     * @return ImageFile
     */
    public function setFileOriginalName($fileOriginalName)
    {
        $this->fileOriginalName = $fileOriginalName;

        return $this;
    }

    /**
     * Get fileOriginalName
     *
     * @return string
     */
    public function getFileOriginalName()
    {
        return $this->fileOriginalName;
    }

    /**
     * Set fileSize
     *
     * @param integer $fileSize
     *
     * @return ImageFile
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return integer
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ImageFile
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
     * Set fileUserName
     *
     * @param string $fileUserName
     *
     * @return ImageFile
     */
    public function setFileUserName($fileUserName)
    {
        $this->fileUserName = $fileUserName;

        return $this;
    }

    /**
     * Get fileUserName
     *
     * @return string
     */
    public function getFileUserName()
    {
        return $this->fileUserName;
    }
    
    /**
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context) {
        if ($this->imageFile && ! in_array($this->imageFile->getMimeType(), array(
            'image/jpeg',
            'image/svg',
            'image/gif',
            'image/png'
        ))) {
            $context
                ->buildViolation('Wrong file type (jpg,gif,png)')
                ->atPath('imageFile')
                ->addViolation()
            ;
            //return false;
        }
        //return true;
    }
}
