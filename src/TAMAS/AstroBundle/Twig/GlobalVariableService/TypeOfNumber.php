<?php

/**
 * TAMASObjectProperties is a service that helps displaying global information about the entity managed in the page. 
 * This $properties variable is made accessible for all twig templates - see \app\config\config.yml @twig
 */

namespace TAMAS\AstroBundle\Twig\GlobalVariableService;

class TypeOfNumber {

    /**
     *
     * @var EntityManager
     */
    private $em;
    private $typeOfNumber;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
        $this->typeOfNumber = json_encode($this->em->getRepository(\TAMAS\AstroBundle\Entity\TypeOfNumber::class)->getNameToBase());
    }
    
    /**
     * 
     * @return array (of definition objects).
     */
    public function getTypeOfNumber(){
        return $this->typeOfNumber;
    }

}
