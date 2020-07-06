<?php
namespace TAMAS\ALFABundle\Repository;

use TAMAS\ALFABundle\Entity\ALFALibrary;

/**
 * LibraryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LibraryRepository extends \Doctrine\ORM\EntityRepository
{

    public function getLabel(ALFALibrary $library)
    { // typer ic
        $city = '';
        if ($library -> getCity()){
            $city = $library->getCity().' ';
        }
        return $city.$library->getName();
    }
}