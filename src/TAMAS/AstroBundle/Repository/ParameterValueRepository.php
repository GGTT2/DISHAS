<?php
namespace TAMAS\AstroBundle\Repository;


use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;
/**
 * ParameterValueRepository
 */
class ParameterValueRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * List of the parameterValue attributes that must be checked when searching for matches.
     *
     * @var array
     */
    private $_attributes = [
        'valueFloat',
        'valueOriginalBase',
        'range1InitialFloat',
        'range1InitialOriginalBase',
        'range1FinalFloat',
        'range1FinalOriginalBase',
        'range2InitialFloat',
        'range2InitialOriginalBase',
        'range2FinalFloat',
        'range2FinalOriginalBase',
        'range3InitialFloat',
        'range3InitialOriginalBase',
        'range3FinalFloat',
        'range3FinalOriginalBase',
        'parameterFormat'
    ];

    /**
     * getListByValue
     *
     * This method is used by findForParameterSetBySingleValue.
     * It queries the database. The query depends on the $value (the needle string)
     * and $option (stars with, ends with, contains).
     * It returns the matching $parameterValue objects.
     *
     * @param string $value
     * @param string $option
     * @return array (array of parameterValue)
     */
    public function getListByValue($value, $option)
    {
        if ($option == "start") {
            $needle = $value . "%";
        } elseif ($option == "end") {
            $needle = "%" . $value;
        } else {
            $needle = "%" . $value . "%";
        }
        return $this->createQueryBuilder('v')
            ->select('v')
            ->where('v.valueFloat LIKE :value')
            ->orWhere('v.valueOriginalBase LIKE :value')
            ->orWhere('v.range1InitialFloat LIKE :value')
            ->orWhere('v.range1InitialOriginalBase LIKE :value')
            ->orWhere('v.range1FinalFloat LIKE :value')
            ->orWhere('v.range1FinalOriginalBase LIKE :value')
            ->orWhere('v.range2InitialFloat LIKE :value')
            ->orWhere('v.range2InitialOriginalBase LIKE :value')
            ->orWhere('v.range2FinalFloat LIKE :value')
            ->orWhere('v.range2FinalOriginalBase LIKE :value')
            ->orWhere('v.range3InitialFloat LIKE :value')
            ->orWhere('v.range3InitialOriginalBase LIKE :value')
            ->orWhere('v.range3FinalFloat LIKE :value')
            ->orWhere('v.range3FinalOriginalBase LIKE :value')
            ->setParameters([
            'value' => $needle
        ])
            ->getQuery()
            ->getResult();
    }

    /**
     * findForParameterSetBySingleValue
     *
     * This method searches the matches between $value (the needle string), and $option (starts with, ends with, contains).
     *
     * @param string $value
     * @param string $option
     * @return array (list of parameterSet id that match the $value and $option)
     */
    public function findForParameterSetBySingleValue($value, $option)
    {
        $parameterValues = $this->getListByValue($value, $option);
        $parameterSetList = [];
        foreach ($parameterValues as $parameterValue) {
            $parameterSetList[] = $parameterValue->getParameterSet()->getId();
        }
        return array_unique($parameterSetList);
    }

    /**
     * findForParameterSet
     *
     * this method is used by admin controller method addParameterSetAction(). It is called by hasDuplicate (method of parameterSetRepository).
     * It checks if the parameters values are already in the database and returns an array of parameterSet ids which contain this values if these parameterSets have the same tableType as the compared one.
     * If param $action is "findDuplicate", we compare every field, even the "null" one. If @action = search, we compare only the entered values.
     * Function isDuplicate treat this array of id, comparing it to the other findForParameterSet answer (one for each value).
     *
     * @param object $value
     * @param object $tableType
     * @param string $action
     * @return array or null (array contains parameterSet ids : Integer).
     */
    public function findForParameterSet($value, $tableType, $action)
    {
        $parameterSets = array();
        $findArray = [];
        foreach ($this->_attributes as $attribute) {
            $getter = 'get' . ucfirst($attribute);
            $valueGetter = $value->{$getter}();
            if ($action == "findDuplicate") {
                $findArray[$attribute] = $valueGetter;
            } elseif ($action == "search") {
                if (isset($valueGetter) && $valueGetter !== null) {
                    $findArray[$attribute] = $valueGetter;
                }
            }
        }
        if (count($findArray) >= 1) {
            $mySubAnswer = $this->findBy($findArray);
            foreach ($mySubAnswer as $parameter) {
                if ($parameter->getParameterSet()->getTableType() == $tableType) {
                    $parameterSets[] = $parameter->getParameterSet()->getId();
                }
            }
            return $parameterSets;
        } else {
            return null;
        }
    }

    /**
     * getDependancies
     *
     * This method is part of the process of forcing deletion of an object.
     * We need to know what are the related fields that are linked to parameterValue (in order to unlink it before deleting it)
     *
     * @return array
     */
    public function getDependancies()
    {
        return [];
    }

    public function getFormatedList($parameterValues)
    {
        $response = [];
        foreach ($parameterValues as $parameterValue) {
            $id = $parameterValue->getId();
            $type = "Unknown type";
            $name = "Unnamed parameter";
            $parameterSet = [];
            if ($parameterValue->getParameterFormat()) {
                $type = $parameterValue->getParameterFormat()->getTableType()->__toString();
                $name = $parameterValue->getParameterFormat()->getParameterName();
            }
            $parameterValueOriginal = $parameterValue->getValueOriginalBase();
            $parameterValueFloat = $parameterValue->getValueFloat();
            if ($parameterValue->getParameterSet()) {
                $thatParameterSet = $parameterValue->getParameterSet();
                $parameterSet['id'] = $thatParameterSet->getId();
                $parameterSet['entity'] = 'parameterSet';
                $parameterSet['title'] = (string) $thatParameterSet; 
            }
            $parameterValueCreated = $parameterValue->getCreated();
            $parameterValueUpdated = $parameterValue->getUpdated();
            $response[] = [
                'id' => $id,
                'tableType' => $type,
                'parameterName' => $name,
                'created' => $parameterValueCreated->format('d-m-Y'),
                'updated' => $parameterValueUpdated->format('d-m-Y'),
                'parameterSet' => $parameterSet,
                'parameterValueOrig' => $parameterValueOriginal,
                'parameterValue' => $parameterValueFloat
            ];
        }
        return $response;
    }

    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of editedTexts: both the list of data (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     * @param array $parameterValues : collection of all the parameter values to be listed
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($parameterValues, \TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListColumnSpec $spec)
    {
        $fieldList = [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('tableType', 'Table type'),
            new TAMASListTableTemplate('parameterName', 'Parameter name'),
            new TAMASListTableTemplate('parameterValue', 'Parameter value (float)'),
            new TAMASListTableTemplate('parameterValueOrig', 'Parameter value (original)'),
            new TAMASListTableTemplate('parameterSet', 'Parameter set', ['class'=>['link']]),
            new TAMASListTableTemplate('created', 'Created', [], 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated', [], 'adminInfo'),
        ];

        $data = $this->getFormatedList($parameterValues);

        return [
            'fieldList' => $fieldList,
            'data' => $data,
            'option' => [
                'noButton'
            ]
        ];
    }
}
