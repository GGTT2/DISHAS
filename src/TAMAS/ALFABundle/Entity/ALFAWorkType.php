<?php

namespace TAMAS\ALFABundle\Entity;

use TAMAS\AstroBundle\Form\WorkType;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorkType
 *
 * @ORM\Table(name="alfa_work_type")
 * @ORM\Entity(repositoryClass="TAMAS\ALFABundle\Repository\WorkTypeRepository")
 */
class ALFAWorkType
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
     * @ORM\Column(name="work_type", type="text")
     */
    private $workType;


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
     * Set workType.
     *
     * @param string $workType
     *
     * @return WorkType
     */
    public function setWorkType($workType)
    {
        $this->workType = $workType;

        return $this;
    }

    /**
     * Get workType.
     *
     * @return string
     */
    public function getWorkType()
    {
        return $this->workType;
    }
}
