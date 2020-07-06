<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use TAMAS\AstroBundle\Controller\TAMASController;


class AdminViewController extends TAMASController
{
    use DTIUtils;
    
    protected $spec = [
        'editDelete' => false,
        'adminInfo' => false
    ];

    // ================================================================= Common method of the class =======================================================//

    /**
     * selectData
     *
     * This method selects a data depending on an id and an entity name.
     * It returns false if the data is not found in the database or if the data is not accessible by the current logged in user.
     *
     * @param $id
     * @param $entity
     * @param null $session
     * @return bool|object|null (object of entity $entity with id $id)
     */
    public function selectData($id, $entity, $session = null)
    {
        $em = $this->getDoctrine()->getManager();
        $thatRepo = $em->getRepository('TAMASAstroBundle:' . $entity);
        $thatObject = $thatRepo->find($id);
        if ($thatObject) {
            $user = $this->getUser();
            if (! $this->checkViewAccess($session, $thatObject, $user)) {
                return false;
            }
            return $thatObject;
        } else {
            $session->getFlashBag()->add("danger", "The $entity n° $id does not exist in the database");
            return false;
        }
    }

    /**
     * checkViewAccess
     *
     * This method checks if the user has the right to access this page depending on (1) whether is is the creator of the item and (2) if he is a super administrator.
     * It is used in the case that the object is a tagged as not public (draft version).
     *
     * @param object $session
     * @param object $object : the object to visualised
     * @param object $user : logged-in user
     * @return boolean : true if the rights are granted to this user; false if not.
     */
    public function checkViewAccess($session, $object, $user)
    {
        if (! method_exists($object, 'getPublic')) {
            return true;
        }
        if ($object->getPublic() == 0 && $user != $object->getCreatedBy() && ! ($user->hasRole('ROLE_SUPER_ADMIN'))) {
            $session->getFlashBag()->add("danger", "You don't have the permission to access this page: this item is still a draft. Please contact the administrator of the website or " . $object->getCreatedBy()
                ->getUserName() . ", the owner of this object.");
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * adminPanelAction
     *
     * This controller method calls a template to be inserted into view pages.
     * It returns the view of the admin panel that is displayed on every view pages.
     *
     * @param $object (the object that is displayed on the view page).
     * @return \Symfony\Component\HttpFoundation\Response (the view ot the pannel).
     * @throws \ReflectionException
     */
    public function adminPanelAction($object)
    {
        $entity = (new \ReflectionClass($object))->getShortName();
        $render = [
            'entity' => $entity,
            'object' => $object
        ];
        // Managing specific rootings : parameterSet and editedText that have an extra argument
        if ($entity == "ParameterSet") {
            $render += [
                'tableId' => "null"
            ];
        } elseif ($entity == "EditedText") {
            $render += [
                'type' => "null"
            ];
        }
        return $this->render('TAMASAstroBundle:ViewObject:adminPannel.html.twig', $render);
    }

    // ================================================================= General method of the class =======================================================//

    /**
     * adminViewOriginalTextAction
     *
     * returns the view of the page view of originalText (admin user side)
     * This implies collecting the requested parameterSet + formated objects that are linked to it (editedText and places in this case).
     *
     * @param integer $id (originalText.id)
     * @return object (the view of the page or the adminHome in case of exception)
     */
    public function adminViewOriginalTextAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $thatOriginalText = $this->selectData($id, 'OriginalText', $request->getSession());
        if (! $thatOriginalText) {
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $formatedEditedText = "";
        if ($thatOriginalText->getEditedTexts()->toArray()) {
            $formatedEditedText = $this->generateSpec('EditedText', $thatOriginalText->getEditedTexts());
        }
        $formatedOriginalTextPlace = $em->getRepository(\TAMAS\AstroBundle\Entity\OriginalText::class)->getListOriginalTextPlaces([
            $thatOriginalText
        ]);
        return $this->render('TAMASAstroBundle:ViewObject:adminViewOriginalText.html.twig', array(
            'originalText' => $thatOriginalText,
            'editedTexts' => $formatedEditedText,
            'placesToDisplay' => [
                'original text' => $formatedOriginalTextPlace
            ],
            'objectEntityName' => "originalText",
            'visualization' => [
                'map'
            ],
            'action' => 'view'
        ));
    }

    /**
     * adminViewParameterSetAction
     *
     * returns the view of the page view of parameter (admin user side).
     * This implies collecting the requested parameterSet + formatted objects that are linked to it (editedText and originalText in this case).
     *
     * @param integer $id (parameterSet.id)
     * @return object (the view of the page or the adminHome in case of exception)
     */
    public function adminViewParameterSetAction(Request $request, $id)
    {
        $editedTextRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\EditedText::class);
        $thatParameterSet = $this->selectData($id, 'ParameterSet', $request->getSession());
        if (! $thatParameterSet) {
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $editedTextByParameters = $editedTextRepo->getEditedTextByParameter($thatParameterSet);
        $formatedEditedTextByParameter = $this->generateSpec("EditedText", $editedTextByParameters);
        $originalTexts = [];
        if ($editedTextByParameters) {
            foreach ($editedTextByParameters as $editedText) {
                if ($editedText->getOriginalTexts()->toArray()) {
                    foreach ($editedText->getOriginalTexts() as $originalText) {
                        $originalTexts[] = $originalText;
                    }
                }
            }
        }
        $formatedOriginalTextByParameter = $this->generateSpec("OriginalText", $originalTexts);
        return $this->render('TAMASAstroBundle:ViewObject:adminViewParameterSet.html.twig', [
            'parameterSet' => $thatParameterSet,
            'editedTexts' => $formatedEditedTextByParameter,
            'originalTexts' => $formatedOriginalTextByParameter,
            'objectEntityName' => "parameterSet",
            'action' => "view",
            'spec' => $this->spec
        ]);
    }

    /**
     * adminViewWorkAction
     *
     * returns the view of the page view of work (admin user side)
     * This implies collecting the requested parameterSet + formated objects that are linked to it (originalText, primarySource and place in this case).
     *
     * @param integer $id
     *            (work.id)
     * @return object (the view of the page or the adminHome in case of exception)
     */
    public function adminViewWorkAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $originalTextRepo = $em->getRepository(\TAMAS\AstroBundle\Entity\OriginalText::class);
        $thatWork = $this->selectData($id, 'Work', $request->getSession());

        if (! $thatWork) {
            return $this->redirectToRoute('tamas_astro_adminHome');
        }

        $workRepo = $em->getRepository(\TAMAS\AstroBundle\Entity\Work::class);
        $primarySources = $workRepo->getPrimarySources($thatWork);

        //$originalTexts = $workRepo->getOriginalTexts($thatWork);
        $originalTexts = $thatWork->getOriginalTexts();

        $formatedOriginalTexts = $this->generateSpec("OriginalText", $originalTexts);
        $formatedPrimarySources = $this->generateSpec("PrimarySource", $primarySources);

        $originalTextsPlaces = $originalTextRepo->getListOriginalTextPlaces($originalTexts);
        $workPlace = $em->getRepository(\TAMAS\AstroBundle\Entity\Work::class)->getListWorkPlaces([
            $thatWork
        ]);
        return $this->render('TAMASAstroBundle:ViewObject:adminViewWork.html.twig', array(
            'work' => $thatWork,
            'originalTexts' => $formatedOriginalTexts,
            'primarySources' => $formatedPrimarySources,
            'visualization' => [
                'map'
            ],
            'placesToDisplay' => [
                'original text' => $originalTextsPlaces,
                'work' => $workPlace
            ],
            'objectEntityName' => "work",
            'action' => "view",
            'spec' => $this->spec
        ));
    }

    /**
     * adminViewPrimarySourceAction
     *
     * returns the view of the page view of primary source (admin user side)
     * This implies collecting the requested parameterSet + formated objects that are linked to it (place, historicalActor, work and originalText in this case).
     *
     * @param integer $id
     *            (primarySource.id)
     * @return object (the view of the page or the adminHome in case of exception)
     */
    public function adminViewPrimarySourceAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $originalTextRepo = $em->getRepository(\TAMAS\AstroBundle\Entity\OriginalText::class);
        $thatPrimarySource = $this->selectData($id, 'PrimarySource', $request->getSession());

        if (! $thatPrimarySource) {
            return $this->redirectToRoute('tamas_astro_adminHome');
        }

        $originalTexts = $originalTextRepo->findBy([
            'primarySource' => $thatPrimarySource
        ]);
        $formatedOriginalTexts = $this->generateSpec('OriginalText', $originalTexts);
        $works = [];
        foreach ($originalTexts as $originalText) {
            if ($originalText->getWork())
                $works[] = $originalText->getWork();
        }
        $formatedWorks = $this->generateSpec('Work', $works);
        $originalTextsPlaces = $originalTextRepo->getListOriginalTextPlaces($originalTexts);
        $worksPlaces = $em->getRepository(\TAMAS\AstroBundle\Entity\Work::class)->getListWorkPlaces($works);
        $formatedPrimarySource = $em->getRepository(\TAMAS\AstroBundle\Entity\PrimarySource::class)->getFormatedList([
            $thatPrimarySource
        ])[0];
        return $this->render('TAMASAstroBundle:ViewObject:adminViewPrimarySource.html.twig', [
            'primarySource' => $formatedPrimarySource,
            'works' => $formatedWorks,
            'originalTexts' => $formatedOriginalTexts,
            'placesToDisplay' => [
                'original text' => $originalTextsPlaces,
                'work' => $worksPlaces
            ],
            'primarySourceObject' => $thatPrimarySource,
            'objectEntityName' => "primarySource",
            'visualization' => [
                'map'
            ],
            'action' => "view",
            'spec' => $this->spec
        ]);
    }

    /**
     * adminViewEditedTextAction
     *
     * returns the view of the page view of edited text (admin user side)
     * This implies collecting the requested parameterSet + formated objects that are linked to it (originalText and editedText (related edition) in this case).
     *
     * @param integer $id
     *            (editedText.id)
     * @return object (the view of the page or the adminHome in case of exception)
     */
    public function adminViewEditedTextAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $thatEditedText = $this->selectData($id, 'EditedText', $request->getSession());
        if (! $thatEditedText) {
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $formattedOriginalText = $this->generateSpec('OriginalText', $thatEditedText->getOriginalTexts());
        $formattedRelatedEdition = $this->generateSpec('EditedText', $thatEditedText->getRelatedEditions());

        $formattedTableContents = [];
        if ($thatEditedText->getTableContents()->toArray()) {
            $formattedTableContents = $em->getRepository(\TAMAS\AstroBundle\Entity\TableContent::class)->getFormatedList($thatEditedText->getTableContents());
        }
        $dependanceTree = $em->getRepository(\TAMAS\AstroBundle\Entity\EditedText::class)->getDependanceTree();

        return $this->render('TAMASAstroBundle:ViewObject:adminViewEditedText.html.twig', array(
            'editedText' => $thatEditedText,
            'originalTexts' => $formattedOriginalText,
            'editedTexts' => $formattedRelatedEdition,
            'tableContents' => $formattedTableContents,
            'objectEntityName' => "editedText",
            'action' => "view",
            'visualization' => [
                'graph'
            ],
            'tree' => $dependanceTree,
            'spec' => $this->spec
        ));
    }

    /**
     * adminViewFormulaDefinition
     *
     * returns the view of the page view of formula definition (admin user side)
     *
     * @param integer $id
     *            (editedText.id)
     * @return object (the view of the page or the adminHome in case of exception)
     */
    public function adminViewFormulaDefinitionAction(Request $request, $id = null, $idTableType = null)
    {
        $em = $this->getDoctrine()->getManager();
        if ($idTableType) {
            $theseFormulaDefinitions = $em->getRepository(\TAMAS\AstroBundle\Entity\FormulaDefinition::class)->findBy([
                'tableType' => $idTableType
            ]);
        } elseif ($id) {
            $theseFormulaDefinitions = [
                $this->selectData($id, 'FormulaDefinition', $request->getSession())
            ];
        } else {
            $theseFormulaDefinitions = [];
        }
        return $this->render('TAMASAstroBundle:ViewObject:adminViewFormulaDefinitions.html.twig', array(
            'formulaDefinitions' => $theseFormulaDefinitions,
            'objectEntityName' => "formulaDefinition",
            'action' => "view"
        ));
    }

    /**
     * adminViewTableContent
     *
     * returns the view of the page view of table content (admin user side)
     *
     * @param integer $id
     *            (tableContent.id)
     * @return object (the view of the page or the adminHome in case of exception)
     */
    public function adminViewTableContentAction(Request $request, $id)
    {
        $tableContent = $this->selectData($id, 'TableContent', $request->getSession());
        if (! $tableContent) {
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\TableContentType::class, $tableContent, [
            'em' => $this->getDoctrine()->getManager(),
            'disabled' => true
        ]);

        //$interface = $this->container->get('tamas_astro.dishasTableInterface');
        $spec = $this->generateInterface($tableContent, $form);
        $spec['readonly'] = true;
        return $this->render('TAMASAstroBundle:DishasTableInterface:main.html.twig', $spec);
    }
}
