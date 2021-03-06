<?php
namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate as ListTemplate;
use TAMAS\AstroBundle\Entity as E;
use TAMAS\AstroBundle\DISHASToolbox\QueryGenerator as QG;

/**
 * MathematicalParameterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MathematicalParameterRepository extends \Doctrine\ORM\EntityRepository
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
        return $this->createQueryBuilder('m')->orderBy('m.argument1Shift, m.argument2Shift, m.entryShift', 'ASC');
    }

    /**
     * findForAutofill
     *
     * This function is used for autofill of a select field purpose. It gives an array of answers.
     *
     * @return array : each value of the array contains an id and a title which are used in the ajax request to populate the select choice of the form such as <selec><option = "id">title</option>.
     */
    public function findForAutofill()
    {
        $queryResult = $this->prepareListForForm()
            ->getQuery()
            ->getResult();
        $answers = [];
        foreach ($queryResult as $mathematicalParam) {

            $answers[] = [
                'title' => (string) $mathematicalParam,
                'id' => $mathematicalParam->getId(),
                'jsonData' => $this->getJson($mathematicalParam)
            ];
        }
        return $answers;
    }

    /* __________________________________________________ list data ___________________________________________________ */

    /**
     * getList
     *
     * This method is roughly equivalent to findAll(), but it lowers the number of queries to the
     * database by selecting only the field that we are interested in displaying.
     *
     * @return array of object mathematical parameters
     */
    public function getList()
    {
        return $this->createQueryBuilder('m')
            ->getQuery()
            ->getResult();
    }

    /**
     * This function generates the specs for listing a given collection of editedTexts: both the list of data
     * (pre-treated for the front library) and the spec of the table (adapted to the front library).
     * @param $mathematicalParameters
     * @return array
     */
    public function getObjectList($mathematicalParameters)
    {
        $fieldList = [
            new ListTemplate('id', '#'),
            new ListTemplate('argShift', 'Argument 1 Shift'),
            new ListTemplate('arg2Shift', 'Argument 2 Shift'),
            new ListTemplate('entryShift', 'Entry Shift'),
            new ListTemplate('entryShift2', 'Entry Shift along arg2'),
            new ListTemplate('argDisplacement', 'Argument 1 Displacement'),
            new ListTemplate('arg2Displacement', 'Argument 2 Displacement'),
            new ListTemplate('entryDisplacement', 'Entry Displacement'),
            new ListTemplate('argDisplacementFloat', 'Argument 1 Displacement (float)'),
            new ListTemplate('arg2DisplacementFloat', 'Argument 2 Displacement (float)'),
            new ListTemplate('entryDisplacementFloat', 'Entry Displacement (float)'),
            new ListTemplate('created', 'Created', [], 'adminInfo'),
            new ListTemplate('updated', 'Updated', [], 'adminInfo'),
            new ListTemplate('buttons', '', [], 'editDelete')
        ];

        $data = $mathematicalParameters;
        if ($mathematicalParameters !== null) {
            $data = $this->getList();
        }
        $refinedData = [];
        foreach ($data as $d) {
            $argShift = $d->getArgument1Shift();
            $arg2Shift = $d->getArgument2Shift();
            $entryShift = $d->getEntryShift();
            $entryShift2 = $d->getEntryShift2();
            $argDisplacement = $d->getArgument1DisplacementOriginalBase();
            $entryDisplacement = $d->getEntryDisplacementOriginalBase();
            $argDisplacementFloat = $d->getArgument1DisplacementFloat();
            $entryDisplacementFloat = $d->getEntryDisplacementFloat();
            $arg2Displacement = $d->getArgument2DisplacementOriginalBase();
            $arg2DisplacementFloat = $d->getArgument2DisplacementFloat();
            $created = '';
            $updated = '';
            $createdBy = [];
            $updatedBy = [];
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
            if ($argDisplacement) {
                $argDisplacement .= " (" . $d->getTypeOfNumberEntry()->getTypeName() . ")";
            }
            if ($entryDisplacement) {
                $entryDisplacement .= " (" . $d->getTypeOfNumberEntry()->getTypeName() . ")";
            }

            $refinedData[] = [
                'argShift' => $argShift,
                'arg2Shift' => $arg2Shift,
                'entryShift' => $entryShift,
                'entryShift2' => $entryShift2,
                'argDisplacement' => $argDisplacement,
                'arg2Displacement' => $arg2Displacement,
                'entryDisplacement' => $entryDisplacement,
                'argDisplacementFloat' => $argDisplacementFloat,
                'arg2DisplacementFloat' => $arg2DisplacementFloat,
                'entryDisplacementFloat' => $entryDisplacementFloat,
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
     * To be improved in the merging of list logic
     *
     * @param {array} of $mathematicalParameter objects
     * @return {array} of $mathematicalParameter objects
     */
    public function getFormatedList($mathematicalParameters = null)
    {
        if ($mathematicalParameters) {
            return $mathematicalParameters;
        }
        return [];
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
            E\TableContent::class => [
                'mathematicalParameter' => [
                    'unlinkMethod' => 'setMathematicalParameter',
                    'oneToMany' => true
                ]
            ]
        ];
    }

    /**
     * This method generates the metadata associated with a record of a parameter set
     * in order to be displayed in a box
     *
     * @param E\MathematicalParameter $param
     * @return array
     */
    public function getBoxData(E\MathematicalParameter $param){
        $paramId = $param->getId();

        $query = QG::newMatchFilter("table_contents.mathematical_parameter.id", $paramId);

        $defRepo = $this->getEntityManager()->getRepository(E\Definition::class);
        $mathParamName = $defRepo->getInterfaceName("mathematicalParameter");
        $editedTextName = $defRepo->getInterfaceName("editedText", true);

        $def = $param->getTypeOfParameterDefinition();
        $typeName = $param->getParameterTypeName();
        $type = "$typeName
                 <button type='button' class='btn' data-container='body' data-toggle='popover' data-placement='bottom' data-html='true' data-trigger='focus' 
                         data-original-title='About this type of parameter' 
                         data-content='$def' 
                         title='' style='background-color: rgb(250,250,250); padding: 0;'>
                     <img src='/img/question.svg' style='height: 16px; margin-left: 6px; margin-top: -5px' alt='Question mark'>
                 </button>";
        return [
            "title" => ucfirst($mathParamName)." n°$paramId",
            "type" => $type,
            "value" => $param->getParamValues(false),
            "number" => $param->getTypeOfNumbers() ? $param->getTypeOfNumbers() : "Unknown type of number",
            "query" => [
                "query" => QG::setFilters([$query]),
                "hover" => "Find all $editedTextName that use this $mathParamName",
                "title" => "All $editedTextName that use the $mathParamName n°$paramId",
                "entity" => "edited_text"
                ]
        ];
    }

    public function getJson(E\MathematicalParameter $mathematicalParam)
    {
        $tableau = [
            'nargs' => $mathematicalParam->getArgNumber(),
            'entryDisplacementFloat' => $mathematicalParam->getEntryDisplacementFloat(),
            'argumentDisplacementFloat' => $mathematicalParam->getArgument1DisplacementFloat(),
            'entryShift' => $mathematicalParam->getEntryShift(),
            'argumentShift' => $mathematicalParam->getArgument1Shift()
        ];
        if ($mathematicalParam->getArgNumber() > 1) {
            $tableau['argument2DisplacementFloat'] = $mathematicalParam->getArgument2DisplacementFloat();
            $tableau['argument2Shift'] = $mathematicalParam->getArgument2Shift();
            $tableau['entryShift2'] = $mathematicalParam->getEntryShift2();
        }
        return json_encode($tableau);
    }
}
