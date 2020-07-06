<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminSelectController extends Controller {

    /**
     * adminSelectTableTypeAction
     * 
     * returns the view of the page select table type (depending on celestial object)
     * This method is the first step before adding a new edition / a new original doc. 
     * It enables the admin to select a table type depending on a list of astronomical objects
     * 
     * @param Request $request (form to select the table type)
     * @return object (view of the page of selection or redirection to adminAddOriginalText)
     */
    public function adminSelectTableTypeAction($action, Request $request) {
        $astronomicalObject = new \TAMAS\AstroBundle\Entity\AstronomicalObject();
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\AstronomicalObjectType::class, $astronomicalObject);
        if ($request->isMethod('POST')&& $form->handleRequest($request)->isValid() ) { //
             $id = $request->request->all()['tamas_astrobundle_astronomicalobject']['tableTypes'][0];
            if (isset($action) && $action == "search") {
                return $this->redirectToRoute('tamas_astro_adminSearchParameterSet', array('id' => $id));
            } else if (isset($action) && $action == "add") {
                return $this->redirectToRoute('tamas_astro_adminAddParameterSet', array('tableId' => $id));
            } else if(isset($action) && $action == "inputTableContent"){
                return $this->redirectToRoute('tamas_astro_publicTableContent', ['tableTypeId' => $id]);
            }
        }
        if(isset($action) && $action == "inputTableContent"){
            return $this->render('TAMASAstroBundle:SelectObject:publicSelectTableType.html.twig', array('form' => $form->createView(), 'action' => $action));
        }
        return $this->render('TAMASAstroBundle:SelectObject:adminSelectTableType.html.twig', array('form' => $form->createView(), 'action' => $action));
    }

    /**
     * adminSelectEditedTypeAction
     * 
     * this function returns the view if the page "select type of edition". 
     * This method is the first step before creating a new edition
     * 
     * @return object (view of the page of selection. The redirection is made directly by a link in the template to an url addEditedText. This method is easier in this situation since we only have to get on character).
     */
    public function adminSelectEditedTypeAction() {
        return $this->render('TAMASAstroBundle:SelectObject:adminSelectEditedType.html.twig');
    }

}