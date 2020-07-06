<?php

//Symfony\src\TAMAS\AstroBundle\Repository\TableTypeRepository.php

namespace TAMAS\AstroBundle\Repository;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\DISHASToolbox\QueryGenerator as QG;
use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;
use TAMAS\AstroBundle\Entity as E;


/**
 * TableTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TableTypeRepository extends \Doctrine\ORM\EntityRepository {
    /* _________________________________________________________ add data  ______________________________________ */

    /**
     * prepareListForForm
     *
     * This method is used when the current entity is linked to a parent form.
     * It returns a custom DQL query builder (not the result!) which will be queried from the formType and findForAutofill.
     *
     * @param null $option
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function prepareListForForm($option = null) {
        if ($option) {
            return $this->createQueryBuilder('t')
                            ->leftJoin('t.astronomicalObject', 'a')
                            ->addSelect('a')
                            ->where('a.id LIKE :term')
                            ->setParameter('term', $option)
                            ->orderBy('a.id, t.tableTypeName', 'ASC');
        } else {
            return $this->createQueryBuilder('t')
                            ->leftJoin('t.astronomicalObject', 'a')
                            ->addSelect('a')
                            ->orderBy('a.id, t.tableTypeName', 'ASC');
        }
    }

    /**
     * This method is triggered in the parent entity form type by ajax. It returns the getLabel list of object of the entity class.
     * In some case an option can be passed to modify the query to the database. 
     * For instance, we can pass the astronomicalObject.ic to query specific object depending on the parent editedText. 
     * 
     * @param integer (astronomicalObject.id)
     * @return array (array of tableType objects matching the query).
     */
    public function findForAutofill($option) {
        $queryResult = $this->prepareListForForm($option)
                ->getQuery()
                ->getResult();
        $arrayResult = [];
        foreach ($queryResult as $tableType) {
            $arrayResult[] = ['id' => $tableType->getId(), 
            'title' => (string) $tableType, 
            'attr' => [ 'multiple-content' => $tableType->getAcceptMultipleContent() ? 'true' : 'false']
        ];
        }
        return $arrayResult;
    }

    /**
     * This method get the Table contents that are associated with the table type given as parameter
     *
     * @param E\TableType $tableType
     * @param boolean $onlyPublic : if only public table contents must be returned
     * @return mixed
     */
    public function getTableContents(E\TableType $tableType, $onlyPublic = true){
        /*$query = 'SELECT t FROM TAMAS\AstroBundle\Entity\TableContent t WHERE t.tableType = '.$tableType->getId();
        $tables = $this->getEntityManager()->createQuery($query)->getResult();*/

        $qb = $this->getEntityManager()->createQueryBuilder();
        $tables = $qb->select('t')
            ->from('TAMAS\AstroBundle\Entity\TableContent', 't')
            ->where('t.tableType = '.$tableType->getId());

        if ($onlyPublic){
            $tables->andWhere('t.public = 1');
        }

        return $tables->getQuery()
                      ->getResult();
    }

    /**
     * This method get the Edited texts that are associated with the table type
     * given as parameter
     *
     * @param E\TableType $tableType
     * @param boolean $onlyPublic : if only public table contents must be returned
     * @return mixed
     */
    public function getEditedTexts(E\TableType $tableType, $onlyPublic = true){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $tables = $qb->select('e')
            ->from('TAMAS\AstroBundle\Entity\EditedText', 'e')
            ->where('e.tableType = '.$tableType->getId());

        if ($onlyPublic){
            $tables->andWhere('e.public = 1');
        }

        return $tables->getQuery()
            ->getResult();
    }

    /**
     * This method get the Parameter sets that are associated with the table type given as parameter
     *
     * @param E\TableType $tableType
     * @return mixed
     */
    public function getParameterSets(E\TableType $tableType){
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('p')
            ->from('TAMAS\AstroBundle\Entity\ParameterSet', 'p')
            ->where('p.tableType = '.$tableType->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * This method get the Formula Definitions that are associated with the table type given as parameter
     *
     * @param E\TableType $tableType
     * @return mixed
     */
    public function getFormulaDefinitions(E\TableType $tableType){
        /*return $this->getEntityManager()
        ->getRepository(E\FormulaDefinition::class)->findBy(['tableType' => $tableType]);*/

        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('f')
            ->from('TAMAS\AstroBundle\Entity\FormulaDefinition', 'f')
            ->where('f.tableType = '.$tableType->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns an array that will be used in the "exploding pie chart" to describe the usage of the different astronomical objects
     * @param E\TableType $tableType
     * @return array
     */
    public function getTableTypeNumbers(E\TableType $tableType)
    {
        return [
                "name" => ucfirst($tableType->getTableTypeName()),
                "value" => count($this->getTableContents($tableType))/* + count($this->getParameterSets($tableType))*/
            ];
    }

    /**
     * Returns an array of boxData that can be used to generate all formula definition boxes associated with a table type
     * @param E\TableType $tableType
     * @return array|mixed
     */
    public function getFormulaDefinitionBoxes(E\TableType $tableType){
        $formulas = $this->getFormulaDefinitions($tableType);
        $boxes = [];

        if (count($formulas) == 0){
            return $boxes;
        }
        $formulaRepo = $this->getEntityManager()->getRepository(E\FormulaDefinition::class);

        foreach ($formulas as $formula){
            $boxes[] = $formulaRepo->getBoxData($formula);
        }

        return $boxes;
    }

    /**
     *
     * @param E\TableType $tableType
     * @return array
     */
    public function getBoxData(E\TableType $tableType){
        return [
            "id" => $tableType->getId(),
            "astroId" => ucfirst($tableType->getAstronomicalObject()->getId()),
            "title" => $tableType->toPublicTitle(),
            "def" => ucfirst($tableType->getAstroDefinition())
        ];
    }

    /**
     * Data sets used to generate the treemap on the astronomical navigation page
     * @return array
     */
    public function getTreemapData()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $tableTypes = $qb->select('t')
            ->from('TAMAS\AstroBundle\Entity\TableType', 't')
            ->getQuery()
            ->getResult();

        $astroObjects = [];
        $tableTypesBoxes = [];
        foreach ($tableTypes as $type) {
            $typeId = "tt".$type->getId();
            $typeName = $type->getTableTypeName();
            $tableTypesBoxes[$typeId] = $this->getBoxData($type);

            $astroObject = $type->getAstronomicalObject();
            $astroId = "ao".$astroObject->getId();

            $numberOfParam = count($type->getParameterSets());
            if (!isset($astroObjects[$astroId])){
                $astroObjects[$astroId] = [
                    "name" => ucfirst($astroObject->getObjectName()),
                    "id" => $astroId,
                    "def" => $astroObject->getObjectDefinition(),
                    "nbParam" => 0,
                    "nbEdition" => 0,
                    "color" => $astroObject->getColor(),
                    "typeIds" => [],
                    "children" => []
                ];
            }
            $astroObjects[$astroId]["typeIds"][] = $typeId;
            $astroObjects[$astroId]["nbParam"] += $numberOfParam;

            $astroObjects[$astroId]["children"][$typeId] = [
                "name" => ucfirst($typeName),
                "nbParam" => $numberOfParam,
                "nbEdition" => 0,
                "id" => [$typeId],
                "count" => $numberOfParam + 1, // we add one in case there is no parameter set associated
            ];
        }

        return ["astroObjects" => $astroObjects, "typeBoxes" => $tableTypesBoxes];
    }

    /**
     * This method returns an array of data that can be used to generate a tree map in the table type record page
     * It contains information of the usage of each parameter set in the table contents of the database
     *
     * @param E\TableType $tableType
     * @return array
     */
    public function getParameterData(E\TableType $tableType){

        $typeName = ucfirst($tableType->getTableTypeName());
        $editedTextRepo = $this->getEntityManager()->getRepository(E\EditedText::class);
        $paramSetRepo = $this->getEntityManager()->getRepository(E\ParameterSet::class);

        $allEditions = $this->getEditedTexts($tableType);
        $params = $this->getParameterSets($tableType);

        if (count($allEditions) == 0 && count($params) == 0){
            return [
                "chart" => [[
                    "type" => "Treemap",
                    "data" => []
                ]],
                "box" => []
            ];
        }

        $data = [];
        $box = [];
        $data[$typeName] = [
            "id" => $tableType->getId(),
            "name" => $typeName,
            "objectName" => $tableType->getAstronomicalObject()->getObjectName(),
            "color" => $tableType->getAstronomicalObject()->getColor(),
            "nbEdition" => 0,
            "nbParam" => 0,
            "editionIds" => [],
            "children" => []
        ];

        foreach ($params as $param){
            $paramId = "ap".$param->getId();
            $box[$paramId] = $paramSetRepo->getBoxData($param);

            $tables = $param->getTableContents()->toArray();
            $paramData = [
                "id" => $param->getId(),
                "name" => $param->getStringValues(false),
                "count" => $tables ? count($tables) + 1 : 1,
                "nbEdition" => 0,
                "editionIds" => [$paramId]
            ];

            if ($tables){
                foreach ($tables as $table){
                    $edition = $table->getEditedText();
                    if ($edition){
                        if ($edition->getPublic()){
                            $editionId = "et".$edition->getId();
                            $box[$editionId] = $editedTextRepo->getBoxData($edition);
                            $data[$typeName]["editionIds"][] = $editionId;
                            $paramData["editionIds"][] = $editionId;
                            $data[$typeName]["nbEdition"] += 1;
                            $paramData["nbEdition"] += 1;
                        }
                    }
                }
            }
            if (count($paramData["editionIds"]) != 0){
                $data[$typeName]["children"][] = $paramData;
            }
            $data[$typeName]["editionIds"][] = $paramId;
            $data[$typeName]["nbParam"] += 1;
        }

        if ($allEditions){
            $paramData = [
                "id" => 0,
                "name" => "No parameter",
                "count" => 1,
                "nbEdition" => 0,
                "editionIds" => []
            ];
            foreach ($allEditions as $edition){
                $editionId = "et".$edition->getId();
                if (!isset($box[$editionId])){
                    $box[$editionId] = $editedTextRepo->getBoxData($edition);

                    $data[$typeName]["editionIds"][] = $editionId;
                    $data[$typeName]["nbEdition"] += 1;
                    $paramData["editionIds"][] = $editionId;
                    $paramData["nbEdition"] += 1;
                    $paramData["count"] += 1;
                }
            }
            if ($paramData["nbEdition"] != 0){
                $data[$typeName]["children"][] = $paramData;
            }
        }

        return [
            "chart" => [[
                "type" => "Treemap",
                "data" => array_values($data)
            ]],
            "box" => $box
        ];
    }

    /**
     * getMetadataTable
     *
     * this methods returns an array containing all the metadata of a table type necessary to constitute
     * the sidebar on the visualization page of a table type record.
     *
     * @param E\TableType $type
     * @return array
     * @throws \Exception
     */
    public function getMetadataTable(E\TableType $type)
    {
        $metadata = [];
        $metadata["entity"] = "original_text";

        /* UPPER PART OF THE SIDEBAR */
        $metadata["title"] = $type->toPublicString();

        $metadata["subtitle"] = "<i>".ucfirst($type->getAstroDefinition())."</i>";

        $metadataTable = ["val" => [], "search" => ["json" => [], "hover" => "", "title" => []]];
        $urlTable = ["html" => "", "id" => "", "path" => ""];

        /* TABLE TYPE (astroObject + table type) */
        $metadata["related models"] = $metadataTable;
        $models = $this->getFormulaDefinitions($type);
        $typeName = E\TableType::getInterfaceName();
        if ($models){
            foreach ($models as $index => $model){
                $metadata["related models"]["val"][] = $urlTable;
                $metadata["related models"]["val"][$index]["html"] = $model->toPublicTitle();
                $metadata["related models"]["val"][$index]["id"] = $model->getId();
                $metadata["related models"]["val"][$index]["path"] = "tamas_astro_viewFormulaDefinition";
            }
        } else {
            $metadata["related models"]["val"][] = "<span class='noInfo'>No table models for this $typeName</span>";
        }

        /* RELATED ORIGINAL TEXTS */
        $qb = $this->getEntityManager()->createQueryBuilder();
        $nbOrigText = $qb->select('count(o)')
            ->from('TAMAS\AstroBundle\Entity\OriginalText', 'o')
            ->where('o.tableType = '.$type->getId())
            ->getQuery()
            ->getSingleScalarResult();

        $oiLabels = E\OriginalText::getInterfaceName(true);
        $metadata[$oiLabels] = $metadataTable;
        $metadata[$oiLabels]["val"][] = $nbOrigText > 1 ? "$nbOrigText $oiLabels associated" : "$nbOrigText ".E\OriginalText::getInterfaceName()." associated";
        $metadata[$oiLabels]["search"]["hover"] = "Find all $oiLabels associated with this $typeName";

        if ($nbOrigText != 0){
            $filter = QG::newMatchFilter("table_type.keyword", $type->__toString());
            $query = QG::setFilters([$filter]);
            $metadata[$oiLabels]["search"]["json"][] = $query;
            $metadata[$oiLabels]["search"]["title"][] = "All $oiLabels whose subject is « ".$type->toPublicTitle()." »";
        }

        return $metadata;
    }

    /* ___________________________________________________________________ Draft _________________________________________________ */

    /* public function findForAutocomplete($term) {
      return $this->prepareListForForm($term)
      ->getQuery()
      ->getResult();
      } */
}
