<?php
namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\Entity as E;
use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;

/**
 * TableContentRepository
 */
class TableContentRepository extends \Doctrine\ORM\EntityRepository
{
    /* _________________________________________________________ add data ______________________________________ */
    /**
     * prepareListForForm
     *
     * This method is used when the current entity is linked to a parent form.
     * It returns a custom DQL querybuilder (not the result!) which will be queried from the formType and findForAutofill.
     *
     * @param {integer} $option
     *           (the tableType identifier attributed to the queried tableContent).
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function prepareListForForm($option)
    { // add a condition: where editedText is null!
      // This function searches for the table content that are already linked to the editedText.
        $query = $this->createQueryBuilder("t")
            ->leftJoin('t.editedText', 'et')
            ->addSelect('et')
            ->andWhere('et.id = :option');
        $query->setParameters([
            'option' => $option
        ]);
        return $query;
    }

    /**
     * This function should be used as the preceding one.
     * It helps creating list of tableContent in forms.
     * It is used in the DTI to generate the list of possible duplicates : all the table of the same table type
     * Except the tableContent that are draft and not propriety of the user.
     *
     * @param $options
     * @return array
     */
    public function prepareListOfDuplicateForForm($options)
    {
        $tableTypeId = $options['tableTypeId'];
        $tableContent = $options['tableContent'];
        $user = $options['user'];

        if (! $user) { // There is no determined users from the public interface
            return [];
        }
        // find all table contents that are public + same table type + not the current tableContent
        $list = $this->findBy([
            'public' => 1,
            'tableType' => $tableTypeId
        ]);
        //Find all the table contents that are private but property of the user + same table type
        $list += $this->findBy([
            'public' => 0,
            'tableType' => $tableTypeId,
            'createdBy' => $user
        ]);
        $thisTableContentKeys = \TAMAS\AstroBundle\DISHASToolbox\GenericTools::getObjectKeyInArray($tableContent, $list);
        foreach ($thisTableContentKeys as $key) {
            unset($list[$key]);
        }
        return $list;
    }

    /**
     * findForAutofill
     *
     * This method is triggered in the parent entity form type by ajax. It returns the getLabeld list of object of the entity class.
     * In some case an option can be passed to modify the query to the database.
     * For instance, we can pass the tableContent.tableType.id to query specific object tableContent depending on the tableType.
     *
     * @param integer $option
     *            (a tableTypeId).
     * @return array (each value of the array contains an id and a title which are used in the ajax request to populate the select choice of the form such as <selec><option = "id">title</option>).
     */
    public function findForAutofill($option)
    {
        $entities = $this->prepareListForForm($option)
            ->getQuery()
            ->getResult();
        $answers = [];
        foreach ($entities as $entity) {
            $answers[] = [
                "id" => $entity->getId(),
                "title" => (string) $entity,
                "tableTypeId" => $entity->getTableType()->getId()
            ];
        }
        return $answers;
    }

    /* __________________________________________________ list data ___________________________________________________ */

    /**
     * getList
     *
     * This method is roughly equivalent to findAll(), but it lowers the number of queries to the database by selecting only the field that we are interested in displaying.
     *
     * @return array of object table contents
     */
    public function getList(bool $public = false)
    {
        $queryResult = $this->createQueryBuilder('c')
            ->leftJoin('c.tableType', 't')
            ->addSelect('t')
            ->leftJoin('t.astronomicalObject', 'a')
            ->addSelect('a')
            ->leftJoin('c.parameterSets', 'ps')
            ->addSelect('ps')
            ->leftJoin('c.editedText', 'et')
            ->addSelect('et')
            ->leftJoin('c.meanMotion', 'mm')
            ->addSelect('mm')
            ->leftJoin('ps.parameterValues', 'pv')
            ->addSelect('pv')
            ->leftJoin('c.entryNumberUnit', 'nu')
            ->addSelect('nu')
            ->leftJoin('c.createdBy', 'u')
            ->addSelect('u')
            ->leftJoin('et.secondarySource', 'ss')
            ->addSelect('ss')
            ->leftJoin('c.mathematicalParameter', 'mp')
            ->addSelect('mp');
        if ($public === true) {
            $queryResult = $queryResult->where('c.public =:public')->setParameter('public', true);
        }
        $queryResult = $queryResult->getQuery()->getResult();
        return $queryResult;
    }

    /**
     * getFormattedList
     *
     * This method formats an array of a given entity objects in order to show it on a list (a table in our twig interface).
     * This format is not an easy task from the front-end, and is much easier when it is possible to call other method from different repository (e.g. : getLabel).
     * Hence, this method lowers the number of code line / the number of query to the database / make the method mutualized and so lower the amont of mistake in case of evolution of the code.
     *
     * @param array $tableContents (array of tableContent objects).
     * @return array $formattedList (array formatted $tableContent objects properties).
     */
    public function getFormatedList($tableContents)
    {
        $formattedList = [];
        if (empty($tableContents)) {
            return $formattedList;
        }
        foreach ($tableContents as $tableContent) {
            $id = $tableContent->getId();
            $tableType = "";
            $editedText = [];
            /*$argument1 = [
                "name" => '',
                "typeOfNumber" => '',
                "numberUnit" => ''
            ];
            $argument2 = [
                "name" => '',
                "typeOfNumber" => '',
                "numberUnit" => ''
            ];
            $argument3 = [
                "name" => '',
                "typeOfNumber" => '',
                "numberUnit" => ''
            ];*/
            $created = "";
            $updated = "";
            $updatedBy = [];
            $createdBy = [];
            $public = $tableContent->getPublic();
            $parameterSets = [];
            $mathematicalParameter = '';
            $entry = [
                "typeOfNumber" => '',
                "numberUnit" => ''
            ];
            if ($tableContent->getCreated()) {
                $created = $tableContent->getCreated();
            }
            if ($tableContent->getUpdated()) {
                $updated = $tableContent->getUpdated();
            }
            if ($tableContent->getCreatedBy()) {
                $createdBy = [
                    'id' => $tableContent->getCreatedBy()->getId(),
                    'username' => $tableContent->getCreatedBy()->getUsername()
                ];
            }
            if ($tableContent->getUpdatedBy()) {
                $updatedBy = [
                    'id' => $tableContent->getUpdatedBy()->getId(),
                    'username' => $tableContent->getUpdatedBy()->getUsername()
                ];
            }
            if ($tableContent->getTableType()) {
                $tableType = (string) $tableContent->getTableType();
            }
            if ($tableContent->getEditedText()) {
                $editedText = [
                    'title' => (string) $tableContent->getEditedText(),
                    'id' => $tableContent->getEditedText()->getId(),
                    'entity' => 'editedText',
                    'public' => $tableContent->getEditedText()->getPublic()
                ];
            }
            /*$arguments = [
                "1",
                "2",
                "3"
            ];
            foreach ($arguments as $argument) {
                if ($tableContent->{"getArgument" . $argument . "Name"}()) {
                    ${"argument" . $argument}['name'] = $tableContent->{"getArgument" . $argument . "Name"}();
                }
                if ($tableContent->{"getArgument" . $argument . "TypeOfNumber"}() && $tableContent->{"getArgument" . $argument . "TypeOfNumber"}()->getTypeName()) {
                    ${"argument" . $argument}['typeOfNumber'] = $tableContent->{"getArgument" . $argument . "TypeOfNumber"}()->getTypeName();
                }
                if ($tableContent->{"getArgument" . $argument . "NumberUnit"}() && $tableContent->{"getArgument" . $argument . "NumberUnit"}()->getUnit()) {
                    ${"argument" . $argument}['numberUnit'] = $tableContent->{"getArgument" . $argument . "NumberUnit"}()->getUnit();
                }
            }*/
            if ($tableContent->getEntryTypeOfNumber()) {
                $entry['typeOfNumber'] = $tableContent->getEntryTypeOfNumber()->getTypeName();
            }
            if ($tableContent->getEntryNumberUnit()) {
                $entry['numberUnit'] = $tableContent->getEntryNumberUnit()->getUnit();
            }
            if ($tableContent->getParameterSets()->toArray()) {
                foreach ($tableContent->getParameterSets() as $parameterSet) {
                    $parameterSets[] = [
                        'id' => $parameterSet->getId(),
                        'entity' => 'parameterSet',
                        'title' => $parameterSet->getTitle()
                    ];
                }
            }
            if ($tableContent->getMathematicalParameter()) {
                $mathematicalParameter = (string) $tableContent->getMathematicalParameter();
            }
            $title = [
                'id' => $tableContent->getId(),
                'entity' => 'tableContent',
                'title' => $tableType,
                'public' => $public
            ];
            $formattedList[] = [
                'id' => $id,
                'title' => $title,
                'tableType' => $tableType,
                'editedText' => $editedText,
                'download' => $id,
                'args' => $tableContent->getArgNames(),
                'argNb' => $tableContent->getArgNumber() ?? "?",
                /*'argument1' => $argument1,
                'arg1Name' => $argument1['name'],
                'argument2' => $argument2,
                'arg2Name' => $argument2['name'],
                'argument3' => $argument3,*/
                'entry' => $entry,
                'parameterSets' => $parameterSets,
                'mathematicalParameter' => $mathematicalParameter,
                'created' => $created->format('d-m-Y'),
                'updated' => $updated->format('d-m-Y'),
                'authority' => $createdBy['username'],
                'createdBy' => $createdBy,
                'updatedBy' => $updatedBy,
                'public' => $public
            ];
        }
        return $formattedList;
    }

    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of editedTexts: both the list of data (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     *  ⤓
     * @param array $tableContents : collection of all the table contents to be listed
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($tableContents, \TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListColumnSpec $spec)
    {
        $link = 'link';
        if ($spec->link === false) {
            $link = '';
        }
        $paramNames = ucfirst(E\ParameterSet::getInterfaceName(true));
        $editedTextNames = ucfirst(E\EditedText::getInterfaceName(true));
        $tableContentNames = ucfirst(E\TableContent::getInterfaceName(true));
        $fieldList = [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('title', "$tableContentNames", ['class'=>[$link]]),
            new TAMASListTableTemplate('download', 'Download'),
            new TAMASListTableTemplate('editedText', "$editedTextNames", ['class'=>[$link]]),
            new TAMASListTableTemplate('argNb', 'Arg. nb.'),
            new TAMASListTableTemplate('args', 'Argument(s)'),
            new TAMASListTableTemplate('parameterSets', "$paramNames", ['class'=>[$link, 'list']]),
            new TAMASListTableTemplate('created', 'Created', ['class'=>['no-visible']], 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated', ['class'=>['no-visible']], 'adminInfo'),
            new TAMASListTableTemplate('authority', 'Created by', ['class'=>['no-visible']], 'adminInfo')
        ];
        if ($spec->public === false) {
            $fieldList[] = new TAMASListTableTemplate('buttons', '', [], 'editDelete');
        }
        $data = $this->getFormatedList($tableContents);

        return [
            'fieldList' => $fieldList,
            'data' => $data
        ];
    }

    public function getFormulaDefinition($tableContent)
    {
        $result = [];
        if ($tableContent->getTableType()) {
            $result = $this->getDoctrine()
                ->getManager()
                ->getRepository(\TAMAS\AstroBundle\Entity\FormulaDefinition::class)
                ->findBy([
                "tableType" => $tableContent->getTableType()
            ]);
        }
        return $result;
    }

    /**
     * getDependancies
     *
     * This method is part of the process of forcing deletion of an object.
     * We need to know what are the related fields that are linked to tableContent (in order to unlink it before deleting it)
     *
     * @return array
     */
    public function getDependancies()
    {
        return [];
    }

    public function createJSONQueryBuilder($tc)
    {
        $qb = $this->createQueryBuilder($tc);
        return $qb->select($tc);
    }

    /**
     * This method returns the metadata associated with the astronomical context of an edition
     * @param \TAMAS\AstroBundle\Entity\TableContent $table
     * @return array
     * @throws \Exception
     */
    public function getAstronomicalMetadata(\TAMAS\AstroBundle\Entity\TableContent $table)
    {
        if (!($table->getPublic()) || !($table->getParameterSets())){
            return [];
        }

        $paramSetRepo = $this->getEntityManager()->getRepository(\TAMAS\AstroBundle\Entity\ParameterSet::class);

        $metadata = [];
        $metadata["entity"] = "edited_text";
        $metadata["tab"] = ucfirst(E\ParameterSet::getInterfaceName());;

        $paramSets = $table->getParameterSets()->toArray();
        if (count($paramSets) == 0) {
            return [];
        }

        $metadata["params"] = [];
        foreach ($paramSets as $param){
            $metadata["params"][] = $paramSetRepo->getTableMetadata($param);
        }

        return $metadata;
    }
}
