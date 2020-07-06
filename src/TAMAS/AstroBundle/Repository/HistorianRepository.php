<?php
namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;
use TAMAS\AstroBundle\Entity\FormulaDefinition;

/**
 * HistorianRepository
 */
class HistorianRepository extends \Doctrine\ORM\EntityRepository
{

    /* __________________________________________ add data _____________________________________________ */

    /**
     * prepareListForForm
     *
     * This method is used when the current entity is linked to a parent form. It returns a custom DQL
     * query builder (not the result!) which will be queried from the formType and findForAutofill.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function prepareListForForm()
    {
        return $this->createQueryBuilder('h')->orderBy('h.lastName', 'ASC');
    }


    /**
     * findForAutofill
     *
     * This method is triggered in the parent entity form type by ajax. It returns the getLabel list of object of the entity class
     *
     * @return array : each value of the array contains an id and a title which are used in the ajax request to populate the select choice of the form such as <selec><option = "id">title</option>.
     */
    public function findForAutofill()
    {
        $queryResult = $this->prepareListForForm()
            ->getQuery()
            ->getResult();
        $arrayResult = [];
        foreach ($queryResult as $result) {
            $arrayResult[] = [
                'title' => (string) $result,
                'id' => $result->getId()
            ];
        }
        return $arrayResult;
    }

    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of editedTexts: both the list of data (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     * @param array $historians: collection of all the historians
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($historians)
    {
        
        $fieldList= [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('title', 'Name'),
            new TAMASListTableTemplate('created', 'Created',[] , 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated',[], 'adminInfo'),
            new TAMASListTableTemplate('buttons', '',[], 'editDelete')
        ];
       
        $data = $historians;
        if($historians === null){
            $data = $this->getList();
        }
        $refinedData = [];
        foreach ($data as $d) {
            $created = '';
            $updated = '';
            $createdBy = [];
            $updatedBy = [];
            $title = $d->getLastName();
            if ($d->getFirstName()) {
                $title .= " (" . $d->getFirstName() . ")";
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
                'title' => $title,
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

    /* __________________________________________________ list data ___________________________________________________ */

    /**
     * getList
     *
     * This method is roughly equivalent to findAll(), but it lowers the number of queries to the database by selecting only the field that we are interested in displaying.
     *
     * @return array of object historians
     */
    public function getList()
    {
        return $this->createQueryBuilder('h')
            ->getQuery()
            ->getResult();
    }

    /**
     * This function is temporary used for the ajax call of the list of entity.
     * To be improved in the merging of list logic //TODO
     *
     * @param {array} of $historian objects
     * @return {array} of $historian objects
     */
    public function getFormatedList($historians = null)
    {
        if ($historians) {
            return $historians;
        }
        return [];
    }

    /**
     * getDependencies
     *
     * This method is part of the process of forcing deletion of an object.
     * We need to know what are the related fields that are linked to historian (in order to unlink it before deleting it)
     *
     * @return array
     */
    public function getDependancies()
    {
        return [
            \TAMAS\AstroBundle\Entity\SecondarySource::class => [
                'historians' => [
                    'unlinkMethod' => 'removeHistorian',
                    'oneToMany' => false
                ]
            ],
            \TAMAS\AstroBundle\Entity\EditedText::class => [
                'historian' => [
                    'unlinkMethod' => 'setHistorian',
                    'oneToMany' => true
                ]
            ],
            FormulaDefinition::class=>[
                'author' => [
                    'unlinkMethod'=> 'setAuthor',
                    'oneToMany'=> true
                ]
            ]
        ];
    }

    /* ___________________________________________________ draft _________________________________________________________ */
/**
 * This function is used for auto completion purpose.
 * It gives an array of answers that start with the entered value in the form.
 *
 * @param string $term
 * @return array
 */
    /*
     * public function findForAutocomplete($term) {
     *
     * $queryResult = $this->createQueryBuilder('h')
     * ->select('h.lastName', 'h.firstName', 'h.id')
     * ->where('h.lastName LIKE :term')
     * ->setParameter('term', $term . '%')
     * ->orderBy('h.lastName')
     * ->getQuery()
     * ->getResult();
     *
     * $arrayResult = [];
     * foreach ($queryResult as $result) {
     * $arrayResult[] = ['value' => $result['lastName'], 'label' => $result['lastName'] . ' (' . $result['firstName'] . ')', 'id' => $result['id'], 'field2' => $result['firstName']];
     * }
     * return $arrayResult;
     * }
     */
}
