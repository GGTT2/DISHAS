<?php

namespace TAMAS\ALFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Author
 *
 * @ORM\Table(name="alfa_author")
 * @ORM\Entity(repositoryClass="TAMAS\ALFABundle\Repository\AuthorRepository")
 */
class ALFAAuthor
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
     * @var string
     *
     * @ORM\Column(name="canonical_name", type="text")
     */
    private $canonicalName;


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
     * Set canonicalName.
     *
     * @param string $canonicalName
     *
     * @return Author
     */
    public function setCanonicalName($canonicalName)
    {
        $this->canonicalName = $canonicalName;

        return $this;
    }

    /**
     * Get canonicalName.
     *
     * @return string
     */
    public function getCanonicalName()
    {
        return $this->canonicalName;
    }
}
