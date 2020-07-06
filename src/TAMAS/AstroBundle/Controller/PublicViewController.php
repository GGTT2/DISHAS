<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use TAMAS\AstroBundle\DISHASToolbox\VisualisationDefinitions;
use Symfony\Component\HttpFoundation as HTTP;
use TAMAS\AstroBundle\Entity as E;

class PublicViewController extends TAMASController
{
    use PublicDTIUtils;

    // ============================================ Common method of the class ============================================ /

    /**
     * selectData
     *
     * This method selects a data depending on an id and an entity name. It returns false if the data is not found in the database.
     *
     * @param $id
     * @param string $entity
     * @param $session
     * @return object|boolean (object of entity $entity with id $id)
     */
    public function selectData($id, $entity, $session = null)
    {
        $em = $this->getDoctrine()->getManager();
        $thatRepo = $em->getRepository('TAMASAstroBundle:' . $entity);
        $thatObject = $thatRepo->find($id);
        $isPublic = true;
        if (method_exists($thatObject, "getPublic")){
            $isPublic = $thatObject->getPublic() == 0 ? false : true;
        }

        if ($thatObject) {
            if ($isPublic){
                return $thatObject;
            } else {
                $session->getFlashBag()->add("danger", 'The ' . $entity . ' n° ' . $id . ' is not available for the public yet');
                return false;
            }
        } else {
            $session->getFlashBag()->add("danger", 'The ' . $entity . ' n° ' . $id . ' does not exist in the database');
            return false;
        }
    }

    /**
     * Check if, in case there is a user connected, the user is allowed to modified the entity given as parameter
     * @param $entity object to be visualised
     * @return bool
     */
    public function checkIfEditable($entity){
        $user = $this->getUser();
        if ($user){
            if (! method_exists($entity, 'getCreatedBy')) {
                return false;
            }

            if ($user != $entity->getCreatedBy() && ! ($user->hasRole('ROLE_SUPER_ADMIN'))) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }


    // ============================================ General method of the class ============================================ /

    /**
     * viewWorkAction
     *
     * returns the view of the page view of work
     * This implies collecting the requested parameterSet + formatted objects that are linked to it
     * (originalText, primarySource and place in this case).
     *
     * @param Request $request
     * @param $id
     * @return HTTP\RedirectResponse|HTTP\Response
     */
    public function viewWorkAction(Request $request, $id)
    {
        $thatWork = $this->selectData($id, 'Work', $request->getSession());

        if (!$thatWork) {
            return $this->redirectToRoute('tamas_astro_historicalNavigation');
        }

        $workRepo = $this->getDoctrine()->getManager()->getRepository(E\Work::class);

        return $this->render('TAMASAstroBundle:PublicView:viewTemplate.html.twig', array(
            'entity' => $thatWork,
            'visualizationTitle'=> ucfirst(E\PrimarySource::getInterfaceName(true))." containing the <i>".$thatWork->getTitle()."</i>",
            'visualizationDefinition' => VisualisationDefinitions::getDefinition("columnChart"),
            'data' => $workRepo->getColumnChartData($thatWork),
            'metadata' => $workRepo->getMetadataTable($thatWork),
            'visualization' => [
                'columnChart'
            ],
            'objectEntityName' => "work",
            'currentNode' => "W-rec",
            'editable' => $this->checkIfEditable($thatWork)
        ));
    }

    /**
     * viewPrimarySourceAction
     *
     * returns the view of the page view of primary source
     *
     * @param Request $request
     * @param $id
     * @return HTTP\RedirectResponse|HTTP\Response
     */
    public function viewPrimarySourceAction(Request $request, $id)
    {
        $thatPrimarySource = $this->selectData($id, 'PrimarySource', $request->getSession());

        if (!$thatPrimarySource) {
            return $this->redirectToRoute('tamas_astro_historicalNavigation');
        }

        $primarySourceRepo = $this->getDoctrine()->getManager()->getRepository(E\PrimarySource::class);

        $sourceType = $thatPrimarySource->getPrimType() == "ep" ? "early printed" : "manuscript";

        return $this->render('TAMASAstroBundle:PublicView:viewTemplate.html.twig', [
            'entity' => $thatPrimarySource,
            'visualizationTitle'=> "Content of the $sourceType ". $thatPrimarySource->getShelfmark(),
            'visualizationDefinition' => VisualisationDefinitions::getDefinition("barChart"),
            'data' => $primarySourceRepo->getBarChartData($thatPrimarySource),
            'objectEntityName' => "primarySource",
            'visualization' => [
                'barChart'
            ],
            'action' => "view",
            'metadata' => $primarySourceRepo->getMetadataTable($thatPrimarySource),
            'currentNode' => "PS-rec",
            'editable' => $this->checkIfEditable($thatPrimarySource)
        ]);
    }

    /**
     * viewOriginalTextAction
     *
     * returns the view of the page view of originalText
     *
     * @param Request $request
     * @param $id (originalText.id)
     * @return HTTP\RedirectResponse|HTTP\Response (the view of the page or the Home in case of exception)
     */
    public function viewOriginalTextAction(Request $request, $id)
    {
        $thatOriginalText = $this->selectData($id, 'OriginalText', $request->getSession());
        if (!$thatOriginalText || $thatOriginalText->getPublic() == 0) {
            return $this->redirectToRoute('tamas_astro_historicalNavigation');
        }
        $originalTextRepo = $this->getDoctrine()->getManager()->getRepository(E\OriginalText::class);

        return $this->render('TAMASAstroBundle:PublicView:viewTemplate.html.twig', array(
            'entity' => $thatOriginalText,
            'visualizationTitle'=> "Sites of creation and copy of the <i>".$thatOriginalText->getTitle()."</i> table",
            'visualizationDefinition' => VisualisationDefinitions::getDefinition("chronoMap-OI-rec"),
            'data' => $originalTextRepo->getChronoMapData($thatOriginalText),
            'objectEntityName' => "originalText",
            'visualization' => ['chronoMap'],
            'action' => 'view',
            'metadata' => $originalTextRepo->getMetadataTable($thatOriginalText),
            'currentNode' => "OI-rec",
            'editable' => $this->checkIfEditable($thatOriginalText)
        ));
    }

    // ============================================ ASTRONOMICAL NAVIGATION ============================================ /

    /**
     * viewTableEditionAction
     *
     * returns the view of the page of a record of a table edition
     *
     * @param Request $request
     * @param $id
     * @return HTTP\RedirectResponse|HTTP\Response
     */
    public function viewTableEditionAction(Request $request, $id)
    {
        $thatEditedText = $this->selectData($id, 'EditedText', $request->getSession());
        if (!$thatEditedText || $thatEditedText->getPublic() == 0) {
            return $this->redirectToRoute('tamas_astro_astronomicalNavigation');
        }

        $em = $this->getDoctrine()->getManager();
        $editTextRepo = $em->getRepository(E\EditedText::class);
        $paramSetRepo = $em->getRepository(E\ParameterSet::class);
        $mathParamRepo = $em->getRepository(E\MathematicalParameter::class);
        $tableTypeRepo = $em->getRepository(E\TableType::class);
        $meanMotionRepo = $em->getRepository(E\MeanMotion::class);

        $tableContents = [];
        $parameterSetBoxes = [];
        $parameterSets = [];
        $mathParamBoxes = [];
        $tableType = "";
        $formulaBoxes = [];
        $localParamBoxes = [];

        $comments = [
            "edit" => $thatEditedText->getComment() ? $thatEditedText->getComment() : null,
            "astro" => []
        ];
        foreach ($thatEditedText->getTableContents()->toArray() as $tableContent){
            if ($tableContent->getPublic()){

                $tableContents[] = $tableContent->toPublicTable();

                if ($tableContent->getComment()){
                    if ($tableContent->getMeanMotion()){
                        $mm = $tableContent->getMeanMotion();
                        $unit = $tableContent->getArgument1NumberUnit()->getUnit();
                        $subUnit = $mm->getSubTimeUnit() ? " (".$mm->getSubTimeUnit()->getSubType().")" : "";
                        $comments["astro"]["$unit$subUnit"] = $tableContent->getComment();

                        $localParamBoxes[] = $meanMotionRepo->getBoxData($mm);
                    } else {
                        $comments["astro"][] = $tableContent->getComment();
                    }
                }

                if (strlen($tableType) == 0 && $tableContent->getTableType()){
                    $tableType = $tableContent->getTableType();
                    $formulaBoxes = $tableTypeRepo->getFormulaDefinitionBoxes($tableContent->getTableType());
                }

                foreach ($tableContent->getParameterSets() as $param){
                    $parameterSets[] = $param->getId();
                    $parameterSetBoxes[] = $paramSetRepo->getBoxData($param);
                }

                if ($tableContent->getMathematicalParameter()){
                    $mathParamBoxes[] = $mathParamRepo->getBoxData($tableContent->getMathematicalParameter());
                }
            }
        }


        return $this->render('TAMASAstroBundle:PublicView:TableEditionView/viewTableEdition.html.twig', [
            'entity' => $thatEditedText,
            'tableContents' => $tableContents,
            'currentNode' => "TE-rec",
            'objectEntityName' => "editedText",
            'editionType' => $thatEditedText->getType(),
            'metadata' => $editTextRepo->getMetadataTable($thatEditedText),
            'parameterSets' => $parameterSetBoxes,
            'mathematicalParameters' => $mathParamBoxes,
            'localParameters' => array_filter($localParamBoxes),
            'tableType' => $tableType,
            'formulaBoxes' => $formulaBoxes,
            'comments' => $comments,

            // VISUALIZATION DATA SETS
            'chronoMapData' => $editTextRepo->getChronoMapData($thatEditedText),
            'chronoMapDefinition' => VisualisationDefinitions::getDefinition("chronoMap-TE-rec"),
            'graphData' => $editTextRepo->getGraphData($thatEditedText),
            'graphDefinition' => VisualisationDefinitions::getDefinition("graph"),
            'editable' => $this->checkIfEditable($thatEditedText)
        ]);
    }

    /**
     * viewTableTypeAction
     *
     * returns the view of the page Table type
     *
     * @param Request $request
     * @param $id
     * @return HTTP\Response
     */
    public function viewTableTypeAction(Request $request, $id)
    {
        $thatTableType = $this->selectData($id, 'TableType', $request->getSession());
        if (!$thatTableType) {
            return $this->redirectToRoute('tamas_astro_astronomicalNavigation');
        }
        $tableTypeRepo = $this->getDoctrine()->getManager()->getRepository(E\TableType::class);

        return $this->render('TAMASAstroBundle:PublicView:viewTemplate.html.twig', [
            'entity' => $thatTableType,
            'metadata' => $tableTypeRepo->getMetadataTable($thatTableType),
            'visualizationTitle'=> ucfirst(E\ParameterSet::getInterfaceName(true))." and ".E\EditedText::getInterfaceName(true)." related to this ".E\TableType::getInterfaceName()." in the database",
            'visualizationDefinition' => VisualisationDefinitions::getDefinition("treemap-param"),
            'visualization' => ['treeMap'],
            'currentNode' => "TT-rec",
            'astroObject' => [
                "title" => ucfirst($thatTableType->getAstronomicalObject()->getObjectName()),
                "name" => lcfirst(str_replace(" ", "", ucwords($thatTableType->getAstronomicalObject()->getObjectName()))),
                "definition" => $thatTableType->getAstronomicalObject()->getObjectDefinition()
            ],
            'data' => $tableTypeRepo->getParameterData($thatTableType),
            'objectEntityName' => "tableType",
            'editable' => false
        ]);
    }

    /**
     * viewFormulaDefinition
     *
     * returns the view of the page view of formula definition
     *
     * @param Request $request
     * @param null $id
     * @return HTTP\Response
     */
    public function viewFormulaDefinitionAction(Request $request, $id = null)
    {
        $thatFormula = $this->selectData($id, 'FormulaDefinition', $request->getSession());
        if (!$thatFormula) {
            return $this->redirectToRoute('tamas_astro_astronomicalNavigation');
        }
        $formulaRepo = $this->getDoctrine()->getManager()->getRepository(E\FormulaDefinition::class);
        $paramFormatRepo = $this->getDoctrine()->getManager()->getRepository(E\ParameterFormat::class);

        $formulaJSON = $thatFormula->getFormulaJSON();
        $paramFormats = [];
        if ($formulaJSON){
            if (isset($formulaJSON["parameters"])){
                $paramFormatIds = array_keys($formulaJSON["parameters"]);
                foreach ($paramFormatIds as $id){
                    $paramFormat = $paramFormatRepo->find(preg_replace("/[^0-9]/", "", $id));
                    $paramFormats[$id] = $paramFormat->toPublicString();
                }
            }
        }

        return $this->render('TAMASAstroBundle:PublicView:viewTemplate.html.twig', array(
            'entity' => $thatFormula,
            'parameterFormats' => $paramFormats,
            'metadata' => $formulaRepo->getMetadataTable($thatFormula),
            'visualization' => ['formulaDefinition'],
            'currentNode' => "TM-rec",
            'objectEntityName' => "formulaDefinition",
            'editable' => $this->checkIfEditable($thatFormula)
        ));
    }


    /**
     * viewParameterSetAction
     *
     * returns the view of the page parameter set
     *
     * @param Request $request
     * @param $id
     * @return HTTP\RedirectResponse|HTTP\Response
     */
    public function viewParameterSetAction(Request $request, $id)
    {
        $thatParamSet = $this->selectData($id, 'ParameterSet', $request->getSession());
        if (!$thatParamSet) {
            return $this->redirectToRoute('tamas_astro_astronomicalNavigation');
        }

        $em = $this->getDoctrine()->getManager();
        $paramSetRepo = $em->getRepository(E\ParameterSet::class);

        $visualizationTitle = "Places where the ".E\ParameterSet::getInterfaceName()." was used in tables";
        $visualizationDefinition = VisualisationDefinitions::getDefinition("chronoMap-AP-rec");


        return $this->render('TAMASAstroBundle:PublicView:viewTemplate.html.twig', [
            'entity' => $thatParamSet,
            'visualizationTitle'=> $visualizationTitle,
            'visualizationDefinition' => $visualizationDefinition,
            'data' => $paramSetRepo->getChronoMapData($thatParamSet),
            'visualization' => ['chronoMap'],
            'currentNode' => "AP-rec",
            'objectEntityName' => "parameterSet",
            'metadata' => $paramSetRepo->getMetadataTable($thatParamSet),
            'editable' => $this->checkIfEditable($thatParamSet)
        ]);
    }

}