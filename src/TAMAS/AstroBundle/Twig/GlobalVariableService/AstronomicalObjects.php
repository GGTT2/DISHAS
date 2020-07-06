<?php

/**
 * TAMASObjectProperties is a service that helps displaying global information about the entity managed in the page. 
 * This $properties variable is made accessible for all twig templates - see \app\config\config.yml @twig
 */

namespace TAMAS\AstroBundle\Twig\GlobalVariableService;

class AstronomicalObjects {

    public $astronomicalObject;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager) {
        $this->astronomicalObject = $entityManager->getRepository(\TAMAS\AstroBundle\Entity\AstronomicalObject::class)->getProperties();
    }
}
