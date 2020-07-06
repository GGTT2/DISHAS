<?php
namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;

/**
 * SecondarySourceRepository
 */
class SecondarySourceRepository extends \Doctrine\ORM\EntityRepository
{

    /* ___________________________________________________________________ General _________________________________________________________ */

    /**
     * formatParentSource
     *
     * This method formats the "parent" of journalArticle and bookChapter type of secondarySource : journal and collectiveBook properties.
     *
     * @param object $secondarySource
     * @return string (string of secondarySource related collectiveBook or journal properties)
     */
    public function formatParentSource($secondarySource)
    {
        if (! ($secondarySource->getSecType() == "journalArticle" || $secondarySource->getSecType() == "bookChapter") || ! $secondarySource) {
            return "";
        }
        $parent = "Undefined";
        $vol = "";
        $pageRange = "";
        if ($secondarySource->getSecType() == "journalArticle" && $secondarySource->getJournal()) {
            $parent = (string) $secondarySource->getJournal();
            if ($secondarySource->getSecVolume()) {
                $vol = ", " . $secondarySource->getSecVolume();
            }
        } elseif ($secondarySource->getSecType() == "bookChapter" && $secondarySource->getCollectiveBook()) {
            $parent = (string) $secondarySource->getCollectiveBook();
        }
        if ($secondarySource->getSecPageRange()) {
            $pageRange = ", p. " . $secondarySource->getSecPageRange();
        }
        return $parent . $vol . $pageRange;
    }

    /* _________________________________________________________ add data ______________________________________ */

    /**
     * prepareListForForm
     *
     * This method is used when the current entity is linked to a parent form.
     * It returns a custom DQL query builder (not the result!) which will be queried from the formType and findForAutofill.
     *
     * @param null $option
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function prepareListForForm($option = null)
    {
        $query = $this->createQueryBuilder('ss')
            ->leftJoin('ss.historians', 'hi')
            ->addSelect('hi');

        if ($option == "collectiveBook") {
            $query->where('ss.secType =:option')->setParameter('option', "anthology");
        }
        $query->orderBy('ss.secTitle');
        return $query;
    }

    /**
     * findForAutofill
     *
     * This method is triggered in the parent entity form type by ajax. It returns the getLabel list of object of the entity class.
     *
     * @param $option
     * @return array (each value of the array contains an id and a title which are used in the ajax request to populate the select choice of the form such as <selec><option = "id">title</option>).
     */
    public function findForAutofill($option = null)
    {
        $entities = $this->prepareListForForm($option)
            ->getQuery()
            ->getResult();
        $answers = array();
        foreach ($entities as $entity) {
            $answers[] = [
                "id" => $entity->getId(),
                "title" => (string) $entity
            ];
        }
        return $answers;
    }

    /* __________________________________________________ list data ___________________________________________________ */

    /**
     * getList
     *
     * This method is roughly equivalent to findAll(), but it lowers the number of queries to the database by
     * selecting only the field that we are interested in displaying.
     *
     * @return array of object secondarySource
     */
    public function getList()
    {
        return $this->createQueryBuilder('ss')
            ->leftJoin('ss.historians', 'hi')
            ->addSelect('hi')
            ->leftJoin('ss.journal', 'j')
            ->addSelect('j')
            ->leftJoin('ss.collectiveBook', 's')
            ->addSelect('s')
            ->orderBy('ss.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * getFormatedList
     *
     * This method formats an array of a given entity objects in order to show it on a list (a table in our twig interface).
     * This format is not an easy task from the front-end, and is much easier when it is possible to call other method from different repository (e.g. : getLabel).
     * Hence, this method lowers the number of code line / the number of query to the database / make the method mutualized and so lower the amont of mistake in case of evolution of the code.
     *
     * @param array $secondarySources
     *            (array of secondarySource objects)
     * @return array (formatted secondarySource properties)
     */
    public function getFormatedList($secondarySources)
    {
        $result = [];
        if (! $secondarySources || empty($secondarySources)) {
            return $result;
        }
        foreach ($secondarySources as $secondarySource) {
            $id = $secondarySource->getId();
            $title = $secondarySource->getTitle();
            $authors = [];
            $date = "";
            $type = "";
            $issued = $this->formatParentSource($secondarySource);
            $created = "";
            $updated = "";
            $createdBy = [];
            $updatedBy = [];
            if (! empty($secondarySource->getHistorians())) {
                foreach ($secondarySource->getHistorians() as $historian) {
                    $authors[] = (string) $historian;
                }
            }
            if ($secondarySource->getSecPubDate()) {
                $date = $secondarySource->getSecPubDate();
            }
            if ($secondarySource->getSecType()) {
                $type = $secondarySource->getSecType();
            }
            if ($secondarySource->getCreated()) {
                $created = $secondarySource->getCreated();
            }
            if ($secondarySource->getUpdated()) {
                $updated = $secondarySource->getUpdated();
            }
            if ($secondarySource->getCreatedBy()) {
                $createdBy = [
                    'id' => $secondarySource->getCreatedBy()->getId(),
                    'username' => $secondarySource->getCreatedBy()->getUserName()
                ];
            }
            if ($secondarySource->getUpdatedBy()) {
                $updatedBy = [
                    'id' => $secondarySource->getUpdatedBy()->getId(),
                    'username' => $secondarySource->getUpdatedBy()->getUsername()
                ];
            }
            $result[] = [
                'id' => $id,
                'title' => $title,
                'authors' => $authors,
                'date' => $date,
                "type" => $type,
                "issued" => $issued,
                "created" => $created->format('d-m-Y'),
                "updated" => $updated->format('d-m-Y'),
                "createdBy" => $createdBy, 
                "updatedBy" => $updatedBy
            ];
        }
        return $result;
    }

    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of editedTexts: both the list of data (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     * @param array $secondarySources
     *            : collection of all the secondary sources to be listed
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($secondarySources)
    {
        $fieldList = [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('title', 'Title'),
            new TAMASListTableTemplate('authors', 'Author(s)', ['class'=>['list']]),
            new TAMASListTableTemplate('date', 'Date'),
            new TAMASListTableTemplate('type', 'Type'),
            new TAMASListTableTemplate('created', 'Created', [] , 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated', [], 'adminInfo'),
            new TAMASListTableTemplate('buttons', '', [], 'editDelete')
        ];
       
        $data = $this->getFormatedList($secondarySources);

        return [
            'fieldList' => $fieldList,
            'data' => $data
        ];
    }

    /* ________________________________________________________________________ Form Validation _____________________________________________________________________ */

    /**
     * findUniqueEntity
     *
     * This method is used by UniqueEntity validation rule. UniqueEntity doesn't work well for many to many relation embedded entity.
     *
     * @param array $criteria
     *            (value from the form to compare)
     * @return array (array of secondarySource objects matching the criteria)
     */
    public function findUniqueEntity($criteria)
    {
        $title = $criteria['secTitle'];

        $historians = $criteria['historians'];
        $volume = $criteria['secVolume'];
        if ($volume !== null) {
            $queryResult = $this->createQueryBuilder('s')
                ->leftJoin('s.historians', 'h')
                ->addSelect('h')
                ->where('s.secTitle =:title')
                ->andWhere('h.id IN (:historians)')
                ->andWhere('s.secVolume =:volume')
                ->setParameters([
                'historians' => $historians,
                'title' => $title,
                'volume' => $volume
            ])
                ->getQuery()
                ->getResult();
        } elseif ($volume == null) {
            $queryResult = $this->createQueryBuilder('s')
                ->leftJoin('s.historians', 'h')
                ->addSelect('h')
                ->where('s.secTitle =:title')
                ->andWhere('h.id IN (:historians)')
                ->setParameters([
                'historians' => $historians,
                'title' => $title
            ])
                -> // , 'volume' => $volume, 'title' => $title
            getQuery()
                ->getResult();
        }
        return $queryResult;
    }

    /**
     * findUniqueEntity2
     *
     * This method is used by UniqueEntity validation rule. UniqueEntity doesn't work well for many to many relation embedded entity.
     *
     * @param array $criteria
     *            : value from the form to compare
     * @return array of secondarySources matching the criteria
     */
    public function findUniqueEntity2($criteria)
    {
        if ($criteria['secIdentifier'] !== null) {
            return $queryResult = $this->createQueryBuilder('s')
                ->where('s.secIdentifier =:id')
                ->setParameters([
                'id' => $criteria['secIdentifier']
            ])
                ->getQuery()
                ->getResult();
        }
        return [];
    }

    /**
     * getDependancies
     *
     * This method is part of the process of forcing deletion of an object.
     * We need to know what are the related fields that are linked to place (in order to unlink it before deleting it)
     *
     * @return array
     */
    public function getDependancies()
    {
        return [
            \TAMAS\AstroBundle\Entity\EditedText::class => [
                'secondarySource' => [
                    'unlinkMethod' => 'setSecondarySource',
                    'oneToMany' => true
                ]
            ],
            \TAMAS\AstroBundle\Entity\SecondarySource::class => [
                'collectiveBook' => [
                    'unlinkMethod' => 'setCollectiveBook',
                    'oneToMany' => true
                ]
            ]
        ];
    }

    /* _____________________________________________________________________ Draft _____________________________________________________________ */
/**
 * findForAutocomplete
 *
 * This function is used for auto completion purpose. It gives an array of answers that start with the entered value in the form.
 *
 * @param string $term
 * @return array
 */
    /*
     * public function findForAutocomplete($term) {
     * $queryResult = $this->prepareListForForm()
     * ->getQuery()
     * ->getResult();
     *
     * $arrayResult = [];
     * foreach ($queryResult as $result) {
     * $authors = "";
     * $year = "";
     * $volume = "";
     * $title = "";
     * if (!empty($result->getHistorians())) {
     * foreach ($result->getHistorians() as $historian) {
     * if ($historian->getLastName()) {
     * $authors = $authors . ", " . $historian->getLastName();
     * }
     * }
     * }
     * $result->getSecPubDate() ? $year = " (" . $result->getSecPubDate() . ")" : $year;
     * $result->getSecVolume() ? $volume = " vol. " . $result->getSecVolume() : $volume;
     * if ($result->getSmallSecTitle()) {
     * $title = $result->getSmallSecTitle();
     * } elseif ($result->getSecTitle()) {
     * $title = $result->getSecTitle();
     * }
     * $id = $result->getId();
     * $arrayResult[] = ['value' => $title, 'label' => $title . $volume . $authors . $year, 'id' => $id];
     * }
     * return $arrayResult;
     * }
     */
}
