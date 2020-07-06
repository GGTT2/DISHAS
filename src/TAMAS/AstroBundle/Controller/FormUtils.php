<?php 
namespace TAMAS\AstroBundle\Controller;

trait FormUtils {
    
    /**
     * isEmpty
     *
     * This function is called from AdminController in order to check whether at least one field of the form is filled or not.
     * We don't send empty form.
     * This method accepts two arguments, the form fields value and the exception fields (the fields that are automatically filled in such as the drop-down select list when it is a compulsory field).
     *
     * @param array $formArray
     * @param array $exceptionFields
     * @return boolean (true means that the form is empty)
     */
    protected function isEmpty($formArray, $exceptionFields) {
        foreach ($formArray as $key => $value) {
            if (is_array($value)) {
                if ($this->isEmpty($value, $exceptionFields) == false) {
                    return false;
                } else {
                    continue;
                }
            }
            if ($value == '' || $value == null || $key == '_token' || $key == 'submit' || in_array($key, $exceptionFields)) {
                continue;
            } else {
                return false;
            }
        }
        return true;
    }
}