<?php
namespace TAMAS\AstroBundle\Twig;

use TAMAS\AstroBundle\DISHASToolbox\GenericTools;

class AppExtension extends \Twig_Extension
{

    //Declaration of the filters object. These filter will then be accessible in each twig template. 
    public function getFilters()
    {
        parent::getFilters();
        return [
            new \Twig_SimpleFilter('replaceNull', [
                $this,
                'replaceNull'
            ]),
            new \Twig_SimpleFilter('determinant', [
                $this,
                'determinant'
            ]),
            new \Twig_SimpleFilter('plural', [
                $this,
                'plural'
            ]),
            new \Twig_SimpleFilter('ucfirst', [
                $this,
                'ucfirst'
            ]),
            new \Twig_SimpleFilter('push', [
                $this,
                'push'
            ]),
            new \Twig_SimpleFilter('cast_to_array', [
                $this,
                'cast_to_array'
            ]),
            new \Twig_SimpleFilter('stringDate', [
                $this, 
                'stringDate'
            ]),
            new \Twig_SimpleFilter('uniqueEntity', [$this, 'uniqueEntity'])
        ];
    }
    
    /*############################ Rules for each filter #################### */

    public function replaceNull($replaceNull, $replacement = null)
    {
        if (! $replacement) {
            $replacement = "–";
        }
        if (! isset($replaceNull) || empty($replaceNull) || $replaceNull == null || $replaceNull == "" || ! $replaceNull) {
            return $replacement;
        }
        return $replaceNull;
    }

    public function determinant($string)
    {
        return GenericTools::toDeterminer($string);
    }

    public function plural($string)
    {
        return GenericTools::toPlural($string);
    }

    public function ucfirst($string)
    {
        return ucfirst($string);
    }

    /**
     * Push a new value into the array
     * @param array $array
     * @param $value
     * @return array
     */
    public function push(array $array, $value)
    {
        array_push($array, $value);
        return $array;
    }

    public function getName()
    {
        return 'AppExtension';
    }

    /**
     * transforms an object into an array
    **/ 
    public function cast_to_array($stdClassObject)
    {
        /*
         * $response = array();
         * foreach ($stdClassObject as $key => $value) {
         * $response[] = array($key, $value);
         * }
         */
        return (array) $stdClassObject;
    }

    /**
     * Turns a php data time object to a string. By default the format in d-m-Y
     * @param $date
     * @param String|null $format
     * @return string
     */
    public function stringDate($date, String $format = null)
    {
        if(!$format){
            $format = 'd-m-Y';
        }
        if (is_object($date) && is_a($date, "DateTime")) {
            return $date->format($format);
        }
        return $date;
    }

    /**
     * This method checks if the form contains a "uniqueEntity" error
     * @param \Symfony\Component\Form\FormView $form
     * @return bool
     */
    public function uniqueEntity(\Symfony\Component\Form\FormView $form){
        $form = $form->vars['errors']->getForm();

        foreach ($form->getErrors(true) as $value) {
            if(is_a($value->getCause()->getConstraint(),'Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity' )){
                return false;
            }
        }
        return true;

    }
}
