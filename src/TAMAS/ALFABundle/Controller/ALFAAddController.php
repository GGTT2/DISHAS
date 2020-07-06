<?php

// src/TAMAS/ALFABundle/Controller/ALFAAddController.php

namespace TAMAS\ALFABundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;
use TAMAS\AstroBundle\Controller\AdminAddController as BaseController;

class ALFAAddController extends BaseController {

    /**
     * ALFAAddXMLFileAction
     * 
     * This method deals with adding and uploading a xml to the database
     * 
     * @param Request $request : form
     * @param Integer $id (or null) : identifier of the xmlFile object to edit if not null.
     * @return Object : render view of form or redirection to admin home page
     */
    public function ALFAAddXMLFileAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        
        $session = $request->getSession();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($id) {
            $new = false;
            $xmlFile = $em->getRepository('TAMASALFABundle:ALFAXMLFile')->find($id);
            if (!$xmlFile) {
                return $this->redirectToRoute('tamas_astro_adminHome');
            }
            $currentXML = $xmlFile->getXMLFile();
        } else {
            $new = true;
            $xmlFile = new \TAMAS\ALFABundle\Entity\ALFAXMLFile;
            $xmlFile->setCreatedBy($user);
        }

        $form = $this->get('form.factory')->create(\TAMAS\ALFABundle\Form\ALFAXMLFileType::class, $xmlFile);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($xmlFile->getXMLFile() == null) {
                $xmlFile->setXMLFile($currentXML);
            }
            //$this->persistData($xmlFile, $session, $new);
            $em->persist($xmlFile);
            $em->flush();
            //Here we must alter the script and call stringHeader
            return $this->redirectToRoute('tamas_astro_alfa_home');
        }
        $new == true ? $action = "add" : $action = "edit";
        return $this->render('TAMASALFABundle:Add:ALFAAddXMLFile.html.twig', array('form' => $form->createView(), 'xmlFile' => $xmlFile, 'objectEntityName' => "xmlFile", 'action' => $action));
    }

}
