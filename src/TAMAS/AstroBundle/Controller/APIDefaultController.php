<?php

namespace TAMAS\AstroBundle\Controller;

class APIDefaultController extends TAMASController {
    
    /**
     * selectData
     *
     * This method selects a data depending on an id and an entity name and returns the selected object or false if the data is not found in the database.
     *
     * @param numeric $id
     * @param string $entity
     * @return boolean|object of entity $entity with id $id or false
     */
    protected function selectData($id, $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $thatRepo = $em->getRepository('TAMASAstroBundle:' . $this->formattedEntityName($entity));
    
        if ($object = $thatRepo->find($id)) return $object;
        else return false;
    }

    /*
    *
    * Select data from the db by an array of fields
    *
    */ 
    protected function selectDataBy($selected_fields, $entity) {
        $em = $this->getDoctrine()->getManager();
        $thatRepo = $em->getRepository('TAMASAstroBundle:' . $this->formattedEntityName($entity));

        if ($object = $thatRepo->findBy($selected_fields)) return $object;
        else return false;
    }
    
    /*
     *
     * Format an entity name correctly to get its repository or instantiate it
     *
     */
    protected function formattedEntityName($entity) {
        switch(strtoupper($entity)) {
            case 'HISTORICALACTOR':
                return 'HistoricalActor';
                break;
            case 'SECONDARYSOURCE':
                return 'SecondarySource';
                break;
            case 'ASTRONOMICALOBJECT':
                return 'AstronomicalObject';
                break;
            case 'EDITEDTEXT':
                return 'EditedText';
                break;
            case 'FORMULADEFINITION':
                return 'FormulaDefinition';
                break;
            case 'MATHEMATICALPARAMETE':
                return 'MathematicalParameter';
                break;
            case 'NUMBERUNIT':
                return 'NumberUnit';
                break;
            case 'ORIGINALTEXT':
                return 'OriginalText';
                break;
            case 'PDFFILE':
                return 'PDFFile';
                break;
            case 'PRIMARYSOURCE';
                return 'PrimarySource';
                break;
            case 'PYTHONSCRIPT':
                return 'PythonScript';
                break;
            case 'TABLECONTENT':
                return 'TableContent';
                break;
            case 'TABLETYPE':
                return 'TableType';
                break;
            case 'TYPEOFNUMBER':
                return 'TypeOfNumber';
                break;
            case 'USERINTERFACETEXT':
                return 'UserInterfaceText';
                break;
            default:
                return ucfirst(strtolower($entity));
                break;
        }
    }

    protected function isInteger($val)
    {
        if (!is_scalar($val) || is_bool($val)) {
            return false;
        }
        if (is_float($val + 0) && ($val + 0) > PHP_INT_MAX) {
            return false;
        }
        return is_float($val) ? false : preg_match('~^((:?+|-)?[0-9]+)$~', $val);
    }

    protected function isFloat($val)
    {
        if (!is_scalar($val)) {
            return false;
        }
        return is_float($val + 0);
    }
    
}