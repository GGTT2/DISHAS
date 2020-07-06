<?php 

namespace TAMAS\AstroBundle\Controller;

/**
*DISHASTableInterface trait
*/
trait DTIUtils{
    /**
     * generateInterface
     *
     * This function generates the interface specs depending on the table content to display in the interface
     *
     * @param \TAMAS\AstroBundle\Entity\TableContent $tableContent
     * @param \Symfony\Component\Form\Form $form
     * @return array (list of spec)
     */
    public function generateInterface(\TAMAS\AstroBundle\Entity\TableContent $tableContent, \Symfony\Component\Form\Form $form)
    {
        $em = $this->getDoctrine()->getManager();
        $typeOfNumbers =$em ->getRepository(\TAMAS\AstroBundle\Entity\TypeOfNumber::class)->findAll();
        $models =$em ->getRepository(\TAMAS\AstroBundle\Entity\FormulaDefinition::class)->findBy([
            'tableType' => $tableContent->getTableType()
        ]);
        // Generation of the model list. For json encoding purpose and compatibility with javascript we add a compulsory foo=>bar, creating a dictionnary instead of a simple array.
        $modelDictionnary = [
            'foo' => 'bar'
        ];
        foreach ($models as $model) {
            $modelDictionnary[$model->getId()] = $em ->getRepository(\TAMAS\AstroBundle\Entity\FormulaDefinition::class)->getPublicObject($model);
        }
        $modelDictionnary = json_encode($modelDictionnary);
        
        $comparedTable = [];
        
        // we add the tables that we wish to compare
        if ($tableContent->getEditedText() && $tableContent->getEditedText()->getType() == "b" &&
            $tableContent->getEditedText()->getRelatedEditions()) {
            foreach ($tableContent->getEditedText()
                ->getRelatedEditions()
                ->toArray() as $relatedEdition) {
                    foreach ($relatedEdition->getTableContents()->toArray() as $thatTableContent) {
                        //var_dump($thatTableContent->toJson()); ATTENTION, ce n'est pas du JSON ! 
                        
                        $currentTable = [
                            'json' => $thatTableContent->toJson(),
                            'editedTextId' => $relatedEdition->getId(),
                            'editedTextTitle' => $relatedEdition->getTitle()
                        ];
                        if ($currentTable['json'] != null) {
                            $comparedTable[$thatTableContent->getId()] = $currentTable;
                        }
                    }
                }
        }
        return [
            'tableContent' => $tableContent,
            'tableContentJSON' => json_encode($tableContent->toJson()),
            'typeOfNumbers' => $typeOfNumbers,
            'models' => $modelDictionnary,
            'comparedTable' => json_encode($comparedTable),
            'form' => $form->createView()
        ];
    }
    
    
}
