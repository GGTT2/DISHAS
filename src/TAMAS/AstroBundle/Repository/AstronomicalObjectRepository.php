<?php
//Symfony\src\TAMAS\AstroBundle\Repository\AstronomicalObjectRepository.php

namespace TAMAS\AstroBundle\Repository;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools;
use TAMAS\AstroBundle\Entity as E;
use Doctrine\ORM as Doctrine;

/**
 * AstronomicalObjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AstronomicalObjectRepository extends Doctrine\EntityRepository
{
    /**
     * This methods returns an array that can be use to generate a "box record" with the box template
     * @param E\AstronomicalObject $astronomicalObject
     * @return array
     */
    public function getBoxData(E\AstronomicalObject $astronomicalObject){
        return [
            "id" => $astronomicalObject->getId(),
            "title" => ucfirst($astronomicalObject->getObjectName()),
            "def" => $astronomicalObject->getObjectDefinition()
        ];
    }

    /**
     * Get a dictionary of entity definition by entity key
     * @return array[]
     */
    public function getProperties() {
        $objects = $this->findAll();
        $properties = [];
        foreach ($objects as $object) {
            if ($object->getObjectName()){
                $properties[$object->getId()]["name"] = GenericTools::toCamel($object->getObjectName());
                $properties[$object->getId()]["title"] = $object->getObjectName();
            }
            if ($object->getColor()){
                $properties[$object->getId()]["color"] = $object->getColor();
            }
            if ($object->getObjectDefinition()){
                $properties[$object->getId()]["definition"] = $object->getObjectDefinition();
            }
        }
        return $properties;
    }

    /**
     * This method is used to generate a dataset to be used in the exploding pie chart
     * allowing to visualize the usage of each astronomical object and table type associated
     * @return array
     */
    public function getPieChartData(){
        $data = [];
        $tableTypeRepo = $this->getEntityManager()->getRepository(E\TableType::class);
        $astroObjects = $this->createQueryBuilder('a')->getQuery()->getResult();

        foreach ($astroObjects as $object){
            $dataObject = [
                "object" => ucfirst($object->getObjectName()),
                "color" => $object->getColor(),
                "sum" => 0,
                "subData" => []
            ];
            foreach ($object->getTableTypes()->toArray() as $tableType){
                $tableTypeData = $tableTypeRepo->getTableTypeNumbers($tableType);

                if ($tableTypeData["value"] != 0){
                    $dataObject["sum"] += $tableTypeData["value"];
                    $dataObject["subData"][] = $tableTypeData;
                }
            }
            if ($dataObject["sum"] != 0){
                $data[] = $dataObject;
            }
        }

        return [
            "chart" => [
                ["type" => "Pie",
                 "data" => $data]
            ],
            "box" => []
        ];
    }
}
