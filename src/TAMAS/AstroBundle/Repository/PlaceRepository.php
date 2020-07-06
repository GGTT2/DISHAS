<?php

// Symfony\src\TAMAS\AstroBundle\Repository\PlaceRepository.php
namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;

/**
 * PlaceRepository
 */
class PlaceRepository extends \Doctrine\ORM\EntityRepository
{

    /* _________________________________________________________ add data ______________________________________ */

    /**
     * prepareListForForm
     *
     * This method prepares the query used in the form type and findForAutofill
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function prepareListForForm()
    {
        return $this->createQueryBuilder('p')->orderBy('p.placeName');
    }


    /**
     * findForAutofill
     *
     * This method is triggered in the parent entity form type by ajax. It returns the getLabel list of object of the entity class.
     *
     * @return array (each value of the array contains an id and a title which are used in the ajax request to populate the select choice of the form such as <selec><option = "id">title</option>).
     */
    public function findForAutofill()
    {
        $entities = $this->prepareListForForm()
            ->getQuery()
            ->getResult();
        $answers = [];
        foreach ($entities as $entity) {
            $answers[] = [
                "id" => $entity->getId(),
                "title" => (string) $entity
            ];
        }
        return $answers;
    }

    /* ________________________________________________________________________ list data ________________________________________________________________ */

    /**
     * getList
     *
     * This method is roughly equivalent to findAll(), but it lowers the number of queries to the database by selecting only the field that we are interested in displaying.
     *
     * @return array of object places
     */
    public function getList()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.createdBy', 'c')
            ->addSelect('c')
            ->orderBy('p.placeName, p.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * getFormatedList
     *
     * This method formats an array of a given entity objects in order to show it on a list (a table in our twig interface). This format is not an easy task from the front-end, and is much easier when it is possible to call other method from different repository (e.g. : getLabel).
     * Hence, this method lowers the number of code line / the number of query to the database / make the method mutualized and so lower the amont of mistake in case of evolution of the code.
     *
     * @param array $places
     *            (array of place objects).
     * @return array (array of the formated place objects).
     */
    public function getFormatedList($places)
    {
        $formattedPlaces = [];
        if (empty($places)) {
            return $formattedPlaces;
        }
        foreach ($places as $place) {
            $id = $place->getId();
            $placeLat = "";
            $placeLong = "";
            $placeName = (string) $place;
            $created = "";
            $updated = "";
            $updatedBy = [];
            $createdBy = [];
            if ($place->getCreated()) {
                $created = $place->getCreated()->format('d/m/y');
            }
            if ($place->getUpdated()) {
                $updated = $place->getUpdated()->format('d/m/y');
            }
            if ($place->getCreatedBy()) {
                $createdBy = [
                    'id' => $place->getCreatedBy()->getId(),
                    'username' => $place->getCreatedBy()->getUserName()
                ];
            }
            if ($place->getUpdatedBy()) {
                $updatedBy = [
                    'id' => $place->getUpdatedBy()->getId(),
                    'username' => $place->getUpdatedBy()->getUsername()
                ];
            }
            if ($place->getPlaceLat()) {
                $placeLat = $place->getPlaceLat();
            }
            if ($place->getPlaceLong()) {
                $placeLong = $place->getPlaceLong();
            }
            $formattedPlaces[] = [
                'id' => $id,
                'placeLat' => $placeLat,
                'placeLong' => $placeLong,
                'placeName' => $placeName,
                'createdBy' => $createdBy,
                'updatedBy' => $updatedBy,
                'created' => $created,
                'updated' => $updated,
                'buttons' => [
                    "edit" => "",
                    "delete" => ""
                ]
            ];
        }

        return $formattedPlaces;
    }

    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of editedTexts: both the list of data (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     * @param array $places
     *            : collection of all the places to be listed
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($places)
    {        
        $fieldList = [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('placeName', 'Name'),
            new TAMASListTableTemplate('placeLat', 'Latitude'),
            new TAMASListTableTemplate('placeLong', 'Longitude'),
            new TAMASListTableTemplate('created', 'Created',[] , 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated',[], 'adminInfo'),
            new TAMASListTableTemplate('buttons', '', [], 'editDelete')
        ];
        
        $data = $this->getFormatedList($places);
        return [
            'fieldList' => $fieldList,
            'data' => $data
        ];
    }

    /* __________________________________________________________________________ other tools ______________________________________________________________ */

    /**
     * getPlacesForMap
     *
     * This method preformats the places for being displayed in a map.
     *
     * @param array $places
     * @return array (formatted values of place objects to display on a map).
     */
    public function getPlacesForMap($places)
    {
        $placesForMap = [];
        foreach ($places as $place) {
            $data = new \TAMAS\AstroBundle\DISHASToolbox\Map\PlaceViz();
            $data->title = (string) $place;
            $data->long = $place->getPlaceLong();
            $data->lat = $place->getPlaceLat();
            $data->allPlaces[] = new \TAMAS\AstroBundle\DISHASToolbox\Map\SubPlaceName($data->title);;
            $placesForMap[] = $data;
        }
        
        foreach ($placesForMap as &$placeForMap){
            foreach($places as $place){
                if($place->getPlaceLat() == $placeForMap->lat && $place->getPlaceLong() == $placeForMap->long){
                    $placeForMap->allPlaces[] = new \TAMAS\AstroBundle\DISHASToolbox\Map\SubPlaceName((string) $place);
                }
            }
            $placeForMap->allPlaces = array_unique($placeForMap->allPlaces);
        }unset($placeForMap);
        return $placesForMap;

    }

    /**
     * getDependencies
     *
     * This method is part of the process of forcing deletion of an object.
     * We need to know what are the related fields that are linked to place (in order to unlink it before deleting it)
     *
     * @return array
     */
    public function getDependancies()
    {
        return [
            \TAMAS\AstroBundle\Entity\OriginalText::class => [
                'place' => [
                    'unlinkMethod' => 'setPlace',
                    'oneToMany' => true
                ]
            ],
            \TAMAS\AstroBundle\Entity\Work::class => [
                'place' => [
                    'unlinkMethod' => 'setPlace',
                    'oneToMany' => true
                ]
            ],
            \TAMAS\AstroBundle\Entity\HistoricalActor::class => [
                'place' => [
                    'unlinkMethod' => 'setPlace',
                    'oneToMany' => true
                ]
            ]
        ];
    }
}
