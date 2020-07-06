<?php 

namespace TAMAS\AstroBundle\Controller;

/**
*DISHASTableInterface trait
*/
trait PublicDTIUtils{
    /**
     * generateTableInfo
     *
     * This function generates the interface specs depending on the table content to display in the interface
     *
     * @param \TAMAS\AstroBundle\Entity\TableContent $tableContent
     * @param $editionType string : letter corresponding to the type of the current edition
     * @return array (list of spec)
     */
    public function generateTableInfo(\TAMAS\AstroBundle\Entity\TableContent $tableContent, $editionType)
    {
        $em = $this->getDoctrine()->getManager();
        $typeOfNumbers =$em ->getRepository(\TAMAS\AstroBundle\Entity\TypeOfNumber::class)->findAll();
        
        $comparedTable = [];
        
        // we add the table that we wish to compare
        if ($tableContent->getEditedText()
            && $tableContent->getEditedText()->getType() == "b"
            && $tableContent->getEditedText()->getRelatedEditions())
        {
            foreach ($tableContent->getEditedText()->getRelatedEditions()->toArray() as $edition)
            {
                foreach ($edition->getTableContents()->toArray() as $thatTableContent)
                {
                    $currentTable = [
                        'json' => $thatTableContent->toJson(),
                        'editedTextId' => $edition->getId(),
                        'editedTextTitle' => $edition->getTitle()
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
            'comparedTable' => json_encode($comparedTable),
            'editionType' => $editionType
        ];
    }
    
    
}
