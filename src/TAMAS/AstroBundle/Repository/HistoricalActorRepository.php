<?php

// Symfony\src\TAMAS\AstroBundle\Repository\HistoricalActorRepository.php
namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;

/**
 * HistoricalActorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HistoricalActorRepository extends \Doctrine\ORM\EntityRepository
{

    /* _________________________________________________________ add data ______________________________________ */

    /**
     * prepareListForForm
     *
     * This method is used when the current entity is linked to a parent form. It returns a custom DQL querybuilder
     * (not the result!) which will be queried from the formType and findForAutofill.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function prepareListForForm()
    {
        return $this->createQueryBuilder('h')->orderBy('h.actorName');
    }

    /**
     * findForAutofill
     *
     * This method is triggered in the parent entity form type by ajax. It returns the getLabel list of object of the entity class.
     *
     * @return array : each value of the array contains an id and a title which are used in the ajax request to populate the select choice of the form such as <selec><option = "id">title</option>.
     */
    public function findForAutofill()
    {
        $entities = $this->prepareListForForm()
            ->getQuery()
            ->getResult();
        $answer = array();
        foreach ($entities as $entity) {
            $id = $entity->getId();
            $answer[] = [
                "id" => $id,
                "title" => (string) $entity
            ];
        }
        return $answer;
    }

    /* __________________________________________________ list data ___________________________________________________ */

    /**
     * getList
     *
     * This method is roughly equivalent to findAll(), but it lowers the number of queries to the database by
     * selecting only the field that we are interested in displaying.
     *
     * @return array (historicalActor objects)
     */
    public function getList()
    {
        return $this->prepareListForForm()
            ->getQuery()
            ->getResult();
    }

    /**
     * getListActorPlaces
     *
     * This method formats the places of flourishment of the actors.
     * For each actor, we store its place, but also the other name of the same place and the other actors associated to this place.
     * This method enables to create interactive maps.
     *
     * @param $historicalActors
     * @return array (actor associated with places with 'actorName', 'long', 'lat', 'name': place name, 'allName': other places associated to this long/lat, 'allId': actors and actor id associated to this place).
     */
    public function getListActorPlaces($historicalActors)
    {
        $actorPlaces = [];
        foreach ($historicalActors as $actor) {
            if ($actor->getActorName()) {
                $actorName = $actor->getActorName();
            } else {
                $actorName = "Unknown actor n°" . $actor->getId();
            }
            if ($actor->getPlace() && $actor->getPlace()->getPlaceLong() && $actor->getPlace()->getPlaceLat() && $actor->getPlace()->getPlaceName()) {
                $actorPlaces[] = [
                    'title' => (string) $actor,
                    'long' => $actor->getPlace()->getPlaceLong(),
                    'lat' => $actor->getPlace()->getPlaceLat(),
                    'name' => $actor->getPlace()->getPlaceName(),
                    'id' => $actor->getId(),
                    'allName' => [],
                    'allId' => []
                ];
            }
        }
        foreach ($actorPlaces as &$actorPlace) {
            foreach ($historicalActors as $actor) {
                if ($actor->getPlace() && $actor->getPlace()->getPlaceLong() && $actor->getPlace()->getPlaceLat() && $actor->getPlace()->getPlaceName()) {
                    $actorName = (string) $actor;
                    if ($actorPlace['long'] == $actor->getPlace()->getPlaceLong() && $actorPlace['lat'] == $actor->getPlace()->getPlaceLat()) {
                        $actorPlace['allName'][] = $actor->getPlace()->getPlaceName();
                        $actorPlace['allId'][] = [
                            'id' => $actor->getId(),
                            'title' => $actorName
                        ];
                    }
                }
            }
            $actorPlace['allName'] = array_unique($actorPlace['allName']);
        }
        unset($actorPlace);
        $placesPerActors = [];
        foreach ($actorPlaces as $actorPlace) {
            $data = new \TAMAS\AstroBundle\DISHASToolbox\Map\PlaceViz();
            $data->title = $actorPlace['title'];
            $data->long = $actorPlace['long'];
            $data->lat = $actorPlace['lat'];
            $data->name = $actorPlace['name'];
            foreach ($actorPlace['allName'] as $place) {
                $data->allPlaces[] = new \TAMAS\AstroBundle\DISHASToolbox\Map\SubPlaceName($place);
            }
            foreach ($actorPlace['allId'] as $otherData) {
                $data->allObjects[] = new \TAMAS\AstroBundle\DISHASToolbox\Map\SubObject($otherData['id'], $otherData['title']);
            }
            $placesPerActors[] = $data;
        }
        return $placesPerActors;
    }

    
    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of editedTexts: both the list of data (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     * @param array $actors
     *            : collection of all the edited texts
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($actors, \TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListColumnSpec $spec)
    {
        $fieldList = [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('actorName', 'Name'),
            new TAMASListTableTemplate('tpq', 'Term. Post Quem'),
            new TAMASListTableTemplate('taq', 'Term. Ante Quem'),
            new TAMASListTableTemplate('place', 'Place of activity'),
            new TAMASListTableTemplate('viafId', 'Viaf Id'),
            new TAMASListTableTemplate('created', 'Created', [] , 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated', [], 'adminInfo'),
            new TAMASListTableTemplate('buttons', '', [], 'editDelete')
        ];
       
        $data = $actors;
        if ($actors === null) {
            $data = $this->getList();
        }
        $refinedData = [];
        foreach ($data as $d) {
            $created = '';
            $updated = '';
            $createdBy = [];
            $updatedBy = [];
            $actorName = '';
            $tpq = '';
            $taq = '';
            $viafId = '';
            $place = '';
            if ($d->getActorName()) {
                $actorName = (string) $d;
            }
            if ($d->getTpq()) {
                $tpq = $d->getTpq();
            }
            if ($d->getTaq()) {
                $taq .= $d->getTaq();
            }
            if ($d->getViafIdentifier()) {
                $viafId = $d->getViafIdentifier();
            }
            if ($d->getPlace()) {
                $place = $d->getPlace()->getPlaceName();
            }
            if ($d->getCreated()) {
                $created = $d->getCreated()->format('d/m/y');
            }
            if ($d->getUpdated()) {
                $updated = $d->getUpdated()->format('d/m/y');
            }
            if ($d->getCreatedBy()) {
                $createdBy = [
                    'id' => $d->getCreatedBy()->getId(),
                    'username' => $d->getCreatedBy()->getUserName()
                ];
            }
            if ($d->getUpdatedBy()) {
                $updatedBy = [
                    'id' => $d->getUpdatedBy()->getId(),
                    'username' => $d->getUpdatedBy()->getUsername()
                ];
            }
            $refinedData[] = [
                'actorName' => $actorName,
                'place' => $place,
                'tpq' => $tpq,
                'taq' => $taq,
                'viafId' => $viafId,
                'created' => $created,
                'updated' => $updated,
                'createdBy' => $createdBy,
                'updatedBy' => $updatedBy,
                'id' => $d->getId()
            ];
        }
        return [
            'fieldList' => $fieldList,
            'data' => $refinedData
        ];
    }

    /**
     * This function is temporary used for the ajax call of the list of entity.
     * To be improved in the merging of list logic //TODO
     *
     * @param
     *            {array} of $historicalActor objects
     * @return {array} of $historicalActor objects
     */
    public function getFormatedList($historicalActors = null)
    {
        if ($historicalActors) {
            return $historicalActors;
        }
        return [];
    }

    /**
     * getDependancies
     *
     * This method is part of the process of forcing deletion of an object.
     * We need to know what are the related fields that are linked to historical actor (in order to unlink it before deleting it)
     *
     * @return array
     */
    public function getDependancies()
    {
        return [
            \TAMAS\AstroBundle\Entity\OriginalText::class => [
                'historicalActor' => [
                    'unlinkMethod' => 'setHistoricalActor',
                    'oneToMany' => true
                ]
            ],
            \TAMAS\AstroBundle\Entity\Work::class => [
                'historicalActors' => [
                    'unlinkMethod' => 'removeHistoricalActor',
                    'oneToMany' => false
                ],
                'translator' => [
                    'unlinkMethod' => 'setTranslator',
                    'oneToMany' => true
                ]
            ]
        ];
    }

    /**
     * getDates
     *
     * This method returns a string containing the life and death dates of an historical actor.
     *
     * @param \TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor
     * @return string|null
     */
    public function getDates(\TAMAS\AstroBundle\Entity\HistoricalActor $historicalActor)
    {
        if (! $historicalActor)
            return null;

        if ($historicalActor->getTpq()){
            $tpq = $historicalActor->getTpq();
        } else {
            $tpq = '?';
        }

        if ($historicalActor->getTaq()){
            $taq = $historicalActor->getTaq();
        } else {
            $taq = '?';
        }

        $tpqTaq = "(" . strval($tpq) . "-" . strval($taq) . ")";

        return $tpqTaq;

    }

    /* _____________________________________________________________________ Draft __________________________________________________________________ */
/**
 * This function is used for autocompletion purpose.
 * It gives an array of answers that start with the entered value in the form.
 *
 * @param string $term
 * @return array
 */
    /*
     * public function findForAutocomplete($term) {
     * $queryResult = $this->createQueryBuilder('h')
     * ->select('h.actorName', 'h.tpq', 'h.taq', 'h.id')
     * ->where('h.actorName LIKE :term')
     * ->setParameter('term', $term . '%')
     * ->orderBy('h.actorName')
     * ->getQuery()
     * ->getResult();
     *
     * $arrayResult = [];
     * foreach ($queryResult as $result) {
     * $arrayResult[] = ['value' => $result['actorName'], 'label' => $result['actorName'] . ' (' . $result['tpq'] . '-' . $result['taq'] . ')', 'id' => $result['id'], 'form_field' => 'primarySource'];
     * }
     * return $arrayResult;
     * }
     *
     */
}
