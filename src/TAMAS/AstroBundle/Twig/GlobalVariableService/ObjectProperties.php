<?php

/**
 * TAMASObjectProperties is a service that helps displaying global information about the entity managed in the page. 
 * This $properties variable is made accessible for all twig templates - see \app\config\config.yml @twig
 */

namespace TAMAS\AstroBundle\Twig\GlobalVariableService;

class ObjectProperties {

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;
    public $properties;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
        $this->properties = $this->em->getRepository(\TAMAS\AstroBundle\Entity\Definition::class)->getObjectAttributeByEntityName();
    }
    
    /**
     * 
     * @return array (of definition objects).
     */
    public function getProperties(){
        return $this->properties;
    }

}
