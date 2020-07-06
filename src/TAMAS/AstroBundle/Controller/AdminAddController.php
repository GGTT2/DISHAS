<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation as HTTP;
use Exception;
use TAMAS\AstroBundle\Entity\MeanMotion;
use TAMAS\AstroBundle\Entity\TableContent;
use TAMAS\AstroBundle\Entity as E;


class AdminAddController extends Controller
{
    use DTIUtils, FormUtils;
    

    // ==================================================================== Common methods =======================================================//

    /**
     * selectData
     *
     * This method selects a data depending on an id and an entity name and returns the selected object or false if the data is not found in the database.
     *
     * @param $session
     * @param number $id
     * @param string $entity
     * @return bool|object|null of entity $entity with id $id or false
     * @throws Exception
     */
    private function selectData($session, $id, $entity)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $thatRepo = $em->getRepository('TAMASAstroBundle:' . $entity);
        }catch(Exception $e){
            throw new Exception('The type of entity is not correct');
        }
        if ($id && $thatRepo->find($id)) {
            $user = $this->getUser();
            $creator = $thatRepo->find($id)->getCreatedBy();
            if (! $this->checkEditAccess($session, $creator, $user)) {
                return false;
            }
            return $thatRepo->find($id);
        } else {
            $session->getFlashBag()->add("danger", 'The ' . $entity . ' n° ' . $id . ' does not exist in the database.');
            return false;
        }
    }

    /**
     * checkForms
     *
     * This method checks if the form is not null.
     * Note: originally this method also checked for duplicate. Now we deal with duplicate with [at]Assert in the entity class.
     * We keep that method instead of directly calling the parent method formChecker if at one point we decide to do further verification before sending the form.
     *
     * @param object $request
     *            (the request object with the details of the form and the session)
     * @param $exceptionFields
     * @return boolean (returns true or nothing. True means that the form is empty)
     */
    private function checkForms($request, $exceptionFields)
    {
        $formData = $request->request->all(); /* we control the fields of the form, not the object: some of the properties of the object are automatically filled, like author, date... */
        $session = $request->getSession();
        if ($this->isEmpty($formData, $exceptionFields)) {
            $session->getFlashBag()->add("danger", "The form is empty. Please go back and fill at least one field of the form.");
            return true;
        }
        return false;
    }

    /**
     * persistData
     *
     * This method persists an object in the database and send a flashbag message of success.
     * It deals both with new object (creation) and already persisted object (edition).
     *
     * @param object $data : the object to persist
     * @param object $session : the request session
     * @param boolean $new : weather it is a new object or an edition of a previous object.
     * @param null $flashbag
     * @param bool $checkIdentity
     * @return bool
     * @throws \ReflectionException
     */
    private function persistData($data, $session, $new, $flashbag = null, $checkIdentity = true)
    {
        // by default, only the creator of the data and super admins are allowed to update a data.
        if (! $new && $checkIdentity && method_exists($data, "getCreatedBy")) {
            if (! $this->checkEditAccess($session, $data->getCreatedBy(), $this->getUser())) {
                return false;
            }
        }
        // persisting the data
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();

        // Creation of the "success" message
        $entity = (new \ReflectionClass($data))->getShortName();
        $entityDef = $objectEntityName = $em->getRepository(\TAMAS\AstroBundle\Entity\Definition::class)
            ->findOneBy([
                'objectEntityName' => $entity
            ]);
        if ($entityDef){
            $objectEntityName = $entityDef->getObjectUserInterfaceName();
        } else {
            $objectEntityName = "record";
        }

        if ($new) {
            $message = "A new " . $objectEntityName . " was successfully inserted into the database (id n°" . $data->getId() . ").";
        } else {
            $message = "The " . $objectEntityName . " (id n°" . $data->getId() . ") was correctly updated in the database.";
        }
        if ($flashbag === null || $flashbag) {
            $session->getFlashBag()->add("success", $message);
        }
    }

    /**
     * CheckEditAccess
     *
     * This method checks if the user has the right to access this page depending on (1) whether is is the creator of the item and (2) if he is a super administrator.
     *
     * @param object $session
     * @param object $creator : creator of the item
     * @param object $user : logged-in user
     * @return boolean : true if the rights are granted to this user; false if not.
     */
    public function checkEditAccess($session, $creator, $user)
    {
        if ($user != $creator && ! ($user->hasRole('ROLE_SUPER_ADMIN'))) {
            $session->getFlashBag()->add("danger", "You don't have the permission to access this page. Please contact the administrator of the website or " . $creator->getUserName() . ", the owner of the item you try to edit");
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $data
     * @param $request
     * @param $form
     * @param $session
     * @param $new
     * @return string|HTTP\RedirectResponse
     * @throws \ReflectionException
     */
    public function persistAndRedirect($data, $request, $form, $session, $new, $formCheck = [])
    {
        // once the form is submitted, we redirect to home
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, $formCheck)) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            // when checking if a form is empty, we exclude the select fields that are compulsory and are always filled, whether intentionally or by mistake
            $this->persistData($data, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        // otherwise we redirect to the form with corresponding action
        return $new == true ? "add" : "edit";
    }

    // ==================================================================== General methods =======================================================//

    /**
     * adminAddOriginalTextAction
     *
     * This methods deals with adding and editing object of class OriginalText
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the OriginalText object to edit if not null.
     * @return Object : render view of form or redirection to admin home page
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     * @throws Exception
     */
    public function adminAddOriginalTextAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) { /* we check first if the router passed an $id. If $id : we are doing an edition. Else, we are doing an addition */
            $originalText = $this->selectData($session, $id, "OriginalText");
            if (! $originalText) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $new = false;
        } else {
            $new = true;
            $originalText = new \TAMAS\AstroBundle\Entity\OriginalText();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\OriginalTextType::class, $originalText); /* we create the form with the object and the entity manager */


        //$action = $this->persistAndRedirect($originalText, $request, $form, $session, $new, ["textType", "language", "script"]);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [
                "textType",
                "language",
                "script"
            ])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($originalText, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddOriginalText.html.twig', [
            'originalText' => $originalText,
            'form' => $form->createView(),
            'objectEntityName' => "originalText",
            'action' => $action
        ]);
    }

    /**
     * adminAddParameterSetAction
     *
     * This methods deals with adding and editing object of class ParameterSet
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $tableId : identifier of the tableType linked to this parameter set
     * @param Integer $id (or null) : identifier of the ParameterSet object to edit if not null.
     * @throws NotFoundHttpException
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException  (when there is no tableType linked to this $tableId)
     */
    public function adminAddParameterSetAction(Request $request, $tableId = null, $id = null)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $parameterSetRep = $em->getRepository(\TAMAS\AstroBundle\Entity\ParameterSet::class);
        $typeOfNumbers = $em->getRepository(\TAMAS\AstroBundle\Entity\TypeOfNumber::class)->findAll();
        if ($id) {
            $new = false;
            $parameterSet = $this->selectData($session, $id, "ParameterSet");
            if (! $parameterSet) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            if ($tableId === 0) {
                return $this->redirectToRoute('tamas_astro_adminSelectTableType', [
                    'action' => 'add'
                ]);
            }
            $new = true;
            $thatTableType = $em->getRepository(\TAMAS\AstroBundle\Entity\TableType::class)->find($tableId); /* we need the table ID to build the correct form. We have to create first an object with the correct attribute and pass it to the form builder */
            if (! $thatTableType) {
                $session->getFlashBag()->add("danger", 'The table type n° ' . $tableId . ' does not exist in the database');
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $parameterSet = $parameterSetRep->createParameterSet($tableId); /* we delegate the creation of the object parameter set to a method of the parameterSet repository */
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\ParameterSetType::class, $parameterSet);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, ["typeOfNumber"])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            if ($parameterSetRep->hasDuplicate($parameterSet, $session->getFlashBag()) == false) {
                $this->persistData($parameterSet, $session, $new);
            }
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";

        return $this->render('TAMASAstroBundle:AddObject:adminAddParameterSet.html.twig', [
            'form' => $form->createView(),
            'parameterSet' => $parameterSet,
            'objectEntityName' => "parameterSet",
            'action' => $action,
            'typeOfNumbers' => $typeOfNumbers
        ]);
    }

    /**
     * adminAddSecondarySourceAction
     *
     * This methods deals with adding and editing object from SecondarySource entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the SecondarySource object to edit if not null.
     * @param null $collectiveBook
     * @return HTTP\RedirectResponse|HTTP\Response : render view of form or redirection to admin home page
     * @throws \ReflectionException
     */
    public function adminAddSecondarySourceAction(Request $request, $id, $collectiveBook = null)
    {
        $session = $request->getSession();
        if ($id) {
            $secondarySource = $this->selectData($session, $id, "SecondarySource");
            if (! $secondarySource) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $new = false;
        } else {
            $new = true;
            $secondarySource = new \TAMAS\AstroBundle\Entity\SecondarySource();
        }
        if (isset($collectiveBook) && $collectiveBook == true) {
            $secondarySource->setSecType("anthology");
            $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\CollectiveBookType::class, $secondarySource);
        } else {
            $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\SecondarySourceType::class, $secondarySource);
        }


        //$action = $this->persistAndRedirect($secondarySource, $request, $form, $session, $new, ["secType"]);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, ["secType"])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($secondarySource, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddSecondarySource.html.twig', array(
            'form' => $form->createView(),
            'secondarySources' => $secondarySource,
            'objectEntityName' => "secondarySource",
            'action' => $action
        ));
    }

    public function adminViewGraphTestAction()
    {
        $dependanceTree = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\EditedText::class)
            ->getDependanceTree();
        return $this->render('TAMASAstroBundle:AddObject:adminViewGraph.html.twig', array(
            'dependanceTree' => $dependanceTree
        ));
    }

    /**
     * adminAddEditedTextAction
     *
     * This methods deals with adding and editing object from EditedText entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the EditedText object to edit if not null.
     * @param null $type
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddEditedTextAction(Request $request, $type = null, $id = null)
    {
        $session = $request->getSession();
        if(!$type && !$id){
            return $this->redirectToRoute('tamas_astro_adminSelectEditedType');
        }

        if ($id) {
            $editedText = $this->selectData($session, $id, "EditedText");
            if (! $editedText) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $new = false;
        } else {
            $new = true;
            $editedText = new \TAMAS\AstroBundle\Entity\EditedText();
            $editedText->setType($type); //types are checked by the route requirements
        }

        $thatRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\EditedText::class);
        $dependanceTree = $thatRepo->getDependanceTree(); //list of the related edition on which is based a givent edited text, represented as a tree. 

        // We define what related edition is/are allowed depending on the type of the current edition text. It is not used by the form at first - this is built in the form type - but the $option is used in the refresh ajax request
        $option = $thatRepo->findRelatedEditionTypes($editedText);
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\EditedTextType::class, $editedText);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            /********************************* Management of the graph logic : an editedText graph must be not-cyclic *******************/
            $graph = new \TAMAS\AstroBundle\DISHASToolbox\Graph\TAMASGraph();
            $graph->loadJSONTree($dependanceTree);
            try {
                if ($new) {
                    $thisNode = $graph->addNode("new", []);
                    $nodeId = "new";
                } else {
                    $nodeId = "e" . $editedText->getId();
                    $thisNode = $graph->getNode($nodeId);
                }

                foreach ($editedText->getRelatedEditions() as $relatedEdition) {
                    $relatedId = 'e' . $relatedEdition->getId();
                    $graph->connect($thisNode, $graph->getNode($relatedId));
                }
                foreach ($editedText->getOriginalTexts() as $original) {
                    $originalId = 'o' . $original->getId();
                    $graph->connect($thisNode, $graph->getNode($originalId));
                }
            } catch (Exception $e) {
                $session->getFlashBag()->add("danger", 'A mistake in the source edition occurred. Please check the documentation.');
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            
            /***************************** Saving of the entity ****************************************/
            $this->persistData($editedText, $session, $new);
            
            /***************************** Generation of the associated table content ******************/
            if ($form->get("submitValue")->getData()==="true") {
                $thatTableContent = new TableContent();
                $thatTableContent->setEditedText($editedText);
                $thatTableContent->setTableType($editedText->getTableType());
                $thatTableContent->setPublic(false);
                //if table type is a meanMotion, we add a mean motion object.
                if($thatTableContent->getTableType()->getAcceptMultipleContent()){
                    $thatTableContent->setMeanMotion(new MeanMotion());
                }
                $this->persistData($thatTableContent, $session, true);
                return $this->redirectToRoute('tamas_astro_adminEditTableContent', [
                    'id' => $thatTableContent->getId()
                ]);
            }

            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";
        return $this->render('TAMASAstroBundle:AddObject:adminAddEditedText.html.twig', array(
            'form' => $form->createView(),
            'dependanceTree' => $dependanceTree,
            'editedText' => $editedText,
            'option' => $option,
            'objectEntityName' => "editedText",
            'action' => $action
        ));
    }

    /**
     * adminAddPlaceAction
     *
     * This methods deals with adding and editing object from Place entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the Place object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response : render view of form or redirection to admin home page
     * @throws \ReflectionException
     */
    public function adminAddPlaceAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $place = $this->selectData($session, $id, "Place");
            if (! $place) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $new = false;
        } else {
            $new = true;
            $place = new \TAMAS\AstroBundle\Entity\Place();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\PlaceType::class, $place);


        //$action = $this->persistAndRedirect($place, $request, $form, $session, $new);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($place, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddPlace.html.twig', array(
            'form' => $form->createView(),
            'place' => $place,
            'objectEntityName' => "place",
            'action' => $action
        ));
    }

    /**
     * adminAddPlaceAction
     *
     * This methods deals with adding and editing object from Place entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request: form
     * @param Integer $id (or null) : identifier of the Place object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response : render view of form or redirection to admin home page
     * @throws \ReflectionException
     */
    public function adminAddHistoricalActorAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $new = false;
            $historicalActor = $this->selectData($session, $id, "HistoricalActor");
            if (! $historicalActor) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $historicalActor = new \TAMAS\AstroBundle\Entity\HistoricalActor();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\HistoricalActorType::class, $historicalActor);


        //$action = $this->persistAndRedirect($historicalActor, $request, $form, $session, $new, ["secType"]);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($historicalActor, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddHistoricalActor.html.twig', array(
            'form' => $form->createView(),
            'historicalActor' => $historicalActor,
            'objectEntityName' => "historicalActor",
            'action' => $action
        ));
    }

    /**
     * adminAddPrimarySourceAction
     *
     * This methods deals with adding and editing object from PrimarySource entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the PrimarySource object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response : render view of form or redirection to admin home page
     * @throws \ReflectionException
     */
    public function adminAddPrimarySourceAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $new = false;
            $primarySource = $this->selectData($session, $id, "PrimarySource");
            if (! $primarySource) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $primarySource = new \TAMAS\AstroBundle\Entity\PrimarySource();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\PrimarySourceType::class, $primarySource);


        //$action = $this->persistAndRedirect($primarySource, $request, $form, $session, $new);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($primarySource, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddPrimarySource.html.twig', array(
            'form' => $form->createView(),
            'primarySource' => $primarySource,
            'objectEntityName' => "primarySource",
            'action' => $action
        ));
    }

    /**
     * adminAddLibraryAction
     *
     * This methods deals with adding and editing object from Library entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the Library object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddLibraryAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $new = false;
            $library = $this->selectData($session, $id, "Library");
            if (! $library) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $library = new \TAMAS\AstroBundle\Entity\Library();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\LibraryType::class, $library);


        //$action = $this->persistAndRedirect($library, $request, $form, $session, $new);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($library, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddLibrary.html.twig', array(
            'form' => $form->createView(),
            'library' => $library,
            'objectEntityName' => "library",
            'action' => $action
        ));
    }

    /**
     * adminAddWorkAction
     *
     * This methods deals with adding and editing object from Work entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the Work object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddWorkAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $new = false;
            $work = $this->selectData($session, $id, "Work");
            if (! $work) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $work = new \TAMAS\AstroBundle\Entity\Work();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\WorkType::class, $work);


        //$action = $this->persistAndRedirect($work, $request, $form, $session, $new);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($work, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddWork.html.twig', array(
            'form' => $form->createView(),
            'work' => $work,
            'objectEntityName' => "work",
            'action' => $action
        ));
    }

    /**
     * adminAddHistorianAction
     *
     * This methods deals with adding and editing object from Historian entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the Historian object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddHistorianAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $new = false;
            $historian = $this->selectData($session, $id, "Historian");
            if (! $historian) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $historian = new \TAMAS\AstroBundle\Entity\Historian();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\HistorianType::class, $historian);


        //$action = $this->persistAndRedirect($historian, $request, $form, $session, $new);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($historian, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddHistorian.html.twig', array(
            'form' => $form->createView(),
            'historian' => $historian,
            'objectEntityName' => "historian",
            'action' => $action
        ));
    }

    /**
     * adminAddJournalAction
     *
     * This methods deals with adding and editing object from Journal entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the Journal object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddJournalAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $new = false;
            $journal = $this->selectData($session, $id, "Journal");
            if (! $journal) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $journal = new \TAMAS\AstroBundle\Entity\Journal();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\JournalType::class, $journal);


        //$action = $this->persistAndRedirect($journal, $request, $form, $session, $new);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($journal, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddJournal.html.twig', array(
            'form' => $form->createView(),
            'journal' => $journal,
            'objectEntityName' => "journal",
            'action' => $action
        ));
    }

    /**
     * adminAddTableContent
     *
     * This methods deals with adding and editing object from table content entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the table content object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddTableContentAction(Request $request, $id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $tableContent = $this->selectData($session, $id, "TableContent");
        if (! $tableContent || ! $tableContent->getTableType()) {
            $session->getFlashBag()->add("danger", 'This '.E\TableContent::getInterfaceName().' does not exist in the database or is not linked to a '.E\EditedText::getInterfaceName());
            return $this->redirectToRoute('tamas_astro_adminHome');
        }

        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\TableContentType::class, $tableContent, ['em' => $em, 'user'=> $this->getUser()]);


        //$this->persistAndRedirect($tableContent, $request, $form, $session, false);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($tableContent, $session, false);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }


        //$interface = $this->container->get('tamas_astro.dishasTableInterface');
        $spec = $this->generateInterface($tableContent, $form);
        return $this->render('TAMASAstroBundle:DishasTableInterface:main.html.twig', $spec);
    }

    /**
     * adminAddTableContentAjax
     *
     * This method updates a table content item via an Ajax Post request.
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the table content object to edit if not null.
     * @return JsonResponse
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function adminAddTableContentAjaxAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $tableContent = $this->selectData($session, $id, "TableContent");    

        if (!$id || !$tableContent){
            $response = new JsonResponse();
            return $response->setData(false);
            throw new \ErrorException('This table content does not exist');
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\TableContentType::class, $tableContent, [
            'em' => $em,
            'user' => $this->getUser()
        ]);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid() ) {
            $this->persistData($tableContent, $session, false, false);
            $response = new JsonResponse();
            return $response->setData($id);
        }
        $response = new JsonResponse();
        return $response->setData(false);
    }

    /**
     * adminAddMathematicalParameter
     *
     * This methods deals with adding and editing object from table content entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the mathematical parameters object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddMathematicalParameterAction(Request $request, $id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $typeOfNumbers = $em->getRepository(\TAMAS\AstroBundle\Entity\TypeOfNumber::class)->findAll();
        if ($id) {
            $new = false;
            $mathematicalParameter = $this->selectData($session, $id, "MathematicalParameter");
            if (! $mathematicalParameter) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $mathematicalParameter = new \TAMAS\AstroBundle\Entity\MathematicalParameter();
            $mathematicalParameter->setTypeOfParameter(0);
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\MathematicalParameterType::class, $mathematicalParameter);


        //$action = $this->persistAndRedirect($mathematicalParameter, $request, $form, $session, $new, ['argNumber', 'typeOfParameter', 'typeOfNumberArgument1', 'typeOfNumberArgument2', 'typeOfNumberEntry']);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, ['argNumber', 'typeOfParameter', 'typeOfNumberArgument1', 'typeOfNumberArgument2', 'typeOfNumberEntry'])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }

            $this->persistData($mathematicalParameter, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddMathematicalParameter.html.twig', array(
            'form' => $form->createView(),
            'mathematicalParameter' => $mathematicalParameter,
            'objectEntityName' => "mathematicalParameter",
            'typeOfNumbers' => $typeOfNumbers,
            'action' => $action
        ));
    }

    /**
     * adminAddUserInterfaceText
     *
     * This methods deals with adding and editing object from Place entity
     * It returns a redirection (either to the form or to admin home).
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the Place object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddUserInterfaceTextAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($id) {
            $new = false;
            $userInterfaceText = $this->selectData($session, $id, "UserInterfaceText");
            if (! $userInterfaceText) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $userInterfaceText = new \TAMAS\AstroBundle\Entity\UserInterfaceText();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\UserInterfaceTextType::class, $userInterfaceText);


        //$action = $this->persistAndRedirect($userInterfaceText, $request, $form, $session, $new, ['textContent']);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            // if ($this->checkForms($request, ['textContent'])) {
            // return $this->redirectToRoute('tamas_astro_adminHome');
            // }
            $this->persistData($userInterfaceText, $session, $new);
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddUserInterfaceText.html.twig', array(
            'form' => $form->createView(),
            'userInterfaceText' => $userInterfaceText,
            'objectEntityName' => "userInterfaceText",
            'action' => $action
        ));
    }

    /**
     * stringHeader
     *
     * @return String : return the header which is to be appended to an uploaded python script
     */
    public function pythonScriptWithHeader($pythonScript)
    {
        $res = '';
        $handle1 = fopen('pythonheader/pythonheader_first.py', 'r');
        while ($line = fgets($handle1)) {
            $res = $res . $line;
        }
        // ici on rajoute le nom de l'auteur et son commentaire
        $res = $res . PHP_EOL . '"""' . PHP_EOL . $pythonScript->getCreatedBy()->getUsernameCanonical() . PHP_EOL . '"""' . PHP_EOL;

        $handle2 = fopen('pythonheader/pythonheader_second.py', 'r');
        while ($line = fgets($handle2)) {
            $res = $res . $line;
        }
        // ici on colle son script
        $scriptHandle = fopen('pythonscripts/' . $pythonScript->getScriptName(), 'r');
        while ($line = fgets($scriptHandle)) {
            $res = $res . $line;
        }

        $handle3 = fopen('pythonheader/pythonheader_third.py', 'r');
        while ($line = fgets($handle3)) {
            $res = $res . $line;
        }

        fclose($handle1);
        fclose($handle2);
        fclose($scriptHandle);
        fclose($handle3);
        return $res;
    }

    /**
     * adminAddPythonScriptAction
     *
     * This method deals with adding and uploading a pythonScript to the database
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the pythonScript object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddPythonScriptAction(Request $request, $id)
    {
        $session = $request->getSession();
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();
        if ($id) {
            $new = false;
            $pythonScript = $this->selectData($session, $id, "PythonScript");
            if (! $pythonScript) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
        } else {
            $new = true;
            $pythonScript = new \TAMAS\AstroBundle\Entity\PythonScript();
            $pythonScript->setCreatedBy($user);
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\PythonScriptType::class, $pythonScript);


        //$action = $this->persistAndRedirect($pythonScript, $request, $form, $session, $new);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($pythonScript, $session, $new);
            // Here we must alter the script and call stringHeader
            /*
             * echo "<pre>";
             * echo $this->pythonScriptWithHeader($pythonScript);
             * echo "</pre>";
             * die;
             */
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";


        return $this->render('TAMASAstroBundle:AddObject:adminAddPythonScript.html.twig', array(
            'form' => $form->createView(),
            'pythonScript' => $pythonScript,
            'objectEntityName' => "pythonScript",
            'action' => $action
        ));
    }

    /**
     * adminAddPDFFileAction
     *
     * This method deals with adding and uploading a pdf to the database
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the pdfFile object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddPDFFileAction(Request $request, $id)
    {
        $session = $request->getSession();
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();
        if ($id) {
            $new = false;
            $pdfFile = $this->selectData($session, $id, "PDFFile");
            if (! $pdfFile) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $currentPDF = $pdfFile->getPDFFile();
        } else {
            $new = true;
            $pdfFile = new \TAMAS\AstroBundle\Entity\PDFFile();
            $pdfFile->setCreatedBy($user);
        }

        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\PDFFileType::class, $pdfFile);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($pdfFile->getPDFFile() == null) {
                $pdfFile->setPDFFile($currentPDF);
            }
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($pdfFile, $session, $new);
            // Here we must alter the script and call stringHeader
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";
        return $this->render('TAMASAstroBundle:AddObject:adminAddPDFFile.html.twig', array(
            'form' => $form->createView(),
            'pdfFile' => $pdfFile,
            'objectEntityName' => "pdfFile",
            'action' => $action
        ));
    }

    /**
     * adminAddFormulaDefinition
     *
     * This method deals with adding and uploading a formula definition to the database
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the formulaDefinition object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddFormulaDefinitionAction(Request $request, $id)
    {
        $session = $request->getSession();
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();
        if ($id) {
            $new = false;
            $formulaDefinition = $this->selectData($session, $id, "FormulaDefinition");
            if (! $formulaDefinition) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $currentImage = $formulaDefinition->getImage();
        } else {
            $new = true;
            $formulaDefinition = new \TAMAS\AstroBundle\Entity\FormulaDefinition();
            $formulaDefinition->setCreatedBy($user);
            $formulaDefinition->setImage(new \TAMAS\AstroBundle\Entity\ImageFile());
            $currentImage = null;
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\FormulaDefinitionType::class, $formulaDefinition);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if (! $formulaDefinition->getImage() || ! $formulaDefinition->getImage()->getImageFile()) {
                $formulaDefinition->setImage($currentImage);
            } else {
                $formulaDefinition->getImage()->setCreatedBy($user);
                $formulaDefinition->getImage()->setFileUserName($formulaDefinition->getName());
            }
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $this->persistData($formulaDefinition, $session, $new);
            // Here we must alter the script and call stringHeader
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        $new == true ? $action = "add" : $action = "edit";
        return $this->render('TAMASAstroBundle:AddObject:adminAddFormulaDefinition.html.twig', array(
            'form' => $form->createView(),
            'formulaDefinition' => $formulaDefinition,
            'objectEntityName' => "formulaDefinition",
            'action' => $action
        ));
    }

    /**
     * adminAddTeamMember
     *
     * This method deals with adding and uploading a formula definition to the database
     *
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the formulaDefinition object to edit if not null.
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddTeamMemberAction(Request $request, $id)
    {
        $session = $request->getSession();
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();
        if ($id) {
            $new = false;
            $teamMember = $this->selectData($session, $id, "TeamMember");
            if (! $teamMember) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $currentImage = $teamMember->getPicture();
        } else {
            $new = true;
            $teamMember = new \TAMAS\AstroBundle\Entity\TeamMember();
            $teamMember->setCreatedBy($user);
            $teamMember->setPicture(new \TAMAS\AstroBundle\Entity\ImageFile());
            $currentImage = null;
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\TeamMemberType::class, $teamMember);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if (! $teamMember->getPicture() || ! $teamMember->getPicture()->getImageFile()) {
                $teamMember->setPicture($currentImage);
            } else {
                $teamMember->getPicture()->setCreatedBy($user);
                $teamMember->getPicture()->setFileUserName($teamMember->getLastName().'_'.$teamMember->getFirstName());
            }
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminListTeamMember');
            }
            $this->persistData($teamMember, $session, $new);
            // Here we must alter the script and call stringHeader
            return $this->redirectToRoute('tamas_astro_adminListTeamMember');
        }
        $new == true ? $action = "add" : $action = "edit";
        return $this->render('TAMASAstroBundle:AddObject:adminAddTeamMember.html.twig', array(
            'form' => $form->createView(),
            'teamMember' => $teamMember,
            'objectEntityName' => "teamMember",
            'action' => $action
        ));
    }

    /**
     * adminAddTeamMember
     *
     * This method deals with adding and uploading a definition to the database
     *
     * @param Request $request : form
     * @param Integer $id (or null)
     * @return HTTP\RedirectResponse|HTTP\Response
     * @throws \ReflectionException
     */
    public function adminAddDefinitionAction(Request $request, $id)
    {
        $session = $request->getSession();
        $defRepo = $this->getDoctrine()->getManager()->getRepository(E\Definition::class);
        if ($id) {
            $new = false;
            $definition = $defRepo->find($id);
            if (! $definition) {
                return $this->redirectToRoute('tamas_astro_adminDocumentation');
            }
        } else {
            $new = true;
            $definition = new \TAMAS\AstroBundle\Entity\Definition();
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\DefinitionType::class, $definition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($this->checkForms($request, [])) {
                return $this->redirectToRoute('tamas_astro_adminDocumentation');
            }
            $this->persistData($definition, $session, $new);
            // Here we must alter the script and call stringHeader
            return $this->redirectToRoute('tamas_astro_adminDocumentation');
        }
        $action = $new ? "add" : "edit";
        return $this->render('TAMASAstroBundle:AddObject:adminAddDefinition.html.twig', array(
            'form' => $form->createView(),
            'definition' => $definition,
            'objectEntityName' => "definition",
            'action' => $action
        ));
    }
}
