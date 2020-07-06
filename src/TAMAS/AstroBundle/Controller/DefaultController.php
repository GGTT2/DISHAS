<?php

//Symfony\src\TAMAS\AstroBundle\Controller\DefaultController.php

namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller {
    /* =============================== USER SIDE (simple page) =============== */

    /**
     * indexAction
     * 
     * returns the view of the page index (public user side)
     *  
     * @return object
     */
    public function indexAction() {
        $public_about = $this->getDoctrine()->getManager()->getRepository(\TAMAS\AstroBundle\Entity\UserInterfaceText::class)->findOneBy(['textName' => 'public_about']);

        return $this->render('TAMASAstroBundle:Default:index.html.twig', [
            'public_about' => $public_about,
            'currentNode' => "dishas"
            ]);
    }

    /**
     * aboutAction
     * 
     * returns the view of the page about (public user side)
     * 
     * @return object
     */
    public function aboutAction() {
        $public_about = $this->getDoctrine()->getManager()->getRepository(\TAMAS\AstroBundle\Entity\UserInterfaceText::class)->findOneBy(['textName' => 'public_about']);
        return $this->render('TAMASAstroBundle:Default:about.html.twig', [
            'public_about' => $public_about,
            'currentNode' => "about"
        ]);
    }

    /**
     * contactAction
     * 
     * returns the view of the page contact (public user side)
     * 
     * @return object
     */
    public function contactAction() {
        $public_contact = $this->getDoctrine()->getManager()->getRepository(\TAMAS\AstroBundle\Entity\UserInterfaceText::class)->findOneBy(['textName' => 'public_contact']);
        return $this->render('TAMASAstroBundle:Default:contact.html.twig', [
            'public_contact' => $public_contact,
            'currentNode' => "contact"
        ]);
    }

    /**
     * teamAction
     * 
     * returns the view of the page team (public user side)
     * 
     * @return object
     */
    public function teamAction() {
        $team = $this->getDoctrine()->getManager()->getRepository(\TAMAS\AstroBundle\Entity\TeamMember::class)->getList();
        return $this->render('TAMASAstroBundle:Default:team.html.twig', [
            'currentNode' => "team",
            'team' => $team
            ]);
    }

    /**
     * partnersAction
     * 
     * returns the view of the page team (public user side)
     * 
     * @return object
     */
    public function partnersAction() {
        return $this->render('TAMASAstroBundle:Default:partners.html.twig', [
            'currentNode' => "partners"
            ]);
    }

    #========================================== ALFA ========================== */
    /**
     * alfaAboutAction
     * 
     * returns the view of the page about (public user side)
     * 
     * @return object
     */

    public function alfaAboutAction() {
        return $this->render('TAMASAstroBundle:ALFA:about.html.twig');
    }

    /* =================================== AJAX AUTOCOMPLETE ================= */

    /**
     * autocompleteEntitiesAction
     * 
     * returns a json string interpreted by autocomplete library.
     * This method allows autocomplete for most of entities - provinding a help when filling forms.
     * Data come from TAMAS database. 
     * 
     * @param Request $request (get string enterred in form field)
     * @param string $entityName
     * @return Json string containing list of result
     */
    public function autocompleteEntitiesAction(Request $request, $entityName) {
        $term = trim(strip_tags($request->get('term')));
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TAMASAstroBundle:' . ucfirst($entityName))->findForAutocomplete($term);
        $response = new JsonResponse();
        return $response->setData($entities);
    }

    /**
     * autofillEntitiesAction
     */
    public function autofillEntitiesAction($entityName, $option=null) {
        
        $entities = $this->getDoctrine()->getManager()->getRepository('TAMASAstroBundle:' . ucfirst($entityName))->findForAutofill($option);
        $response = new JsonResponse();
        return $response->setData($entities);
    }


    /**
     * This method loads the tree of relation between edited text and original item from the database
     * It is automatically loaded from the "add editedText" page
     * 
     * @param Request $request
     * @return {Json}
     */
    public function loadTreeAction(Request $request) {
        $dependanceTree = $this->getDoctrine()->getManager()->getRepository(\TAMAS\AstroBundle\Entity\EditedText::class)->getDependanceTree();
        
        //$graph = new \TAMAS\AstroBundle\Graph\TAMASGraph;
        //$graph->loadJSONTree($dependanceTree);

        $response = new JsonResponse();
        return $response->setData($dependanceTree);
    }

    /**
     * This method get the json of all the table content OR the table content with id = id
     * To be improved in a more consistant API.
     * It is automatically loaded from the "add tableContent" page (helps to generate a table from a duplicate).
     * 
     * TODO : pas sûr que cette fonction soit nécessaire maintenant que les tables sont uniformément JSON-ifiée. 
     *
     * @param Request $request
     * @param id : id of the specific table content
     * @return {Json}
     */
    public function getTableContentJsonAction(Request $request, $id = null) {
        $tableContentRepo = $this->getDoctrine()->getManager()->getRepository(\TAMAS\AstroBundle\Entity\TableContent::class);
        $result = null;
        if($id){
            $tableContentJson = $tableContentRepo->find($id);
            if($tableContentJson){
                $result = $tableContentJson->toJson();
            }
        } else {
            $tableContents = $tableContentRepo->getList();
            $result = [];
            foreach ($tableContents as $tableContent){
                $result[] = $tableContent->toJson();
            }
        }        
        $response = new JsonResponse();
        return $response->setData($result);
    }

}
