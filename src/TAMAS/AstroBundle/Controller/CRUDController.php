<?php

// src/TAMAS/AstroBundle/Controller/CRUDController.php

namespace TAMAS\AstroBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CRUDController extends Controller {

    /**
     * This method can be overloaded in your custom CRUD controller.
     * It's called from createAction.
     *
     * @param Request $request
     * @param mixed   $object
     *
     * @return Response|null
     */
    public function preEdit(Request $request, $object) {


        if ($object->getTableType() !== null && empty($object->getParameterValues()->toArray())) {
            $em = $this->getDoctrine()->getManager();
            $tableTypeId = $object->getTableType()->getId();
            $parameterFormats = $em->getRepository(\TAMAS\AstroBundle\Entity\ParameterFormat::class)->findBy(array('tableType' => $tableTypeId));
            foreach ($parameterFormats as $format) {
                $parameterValue = new \TAMAS\AstroBundle\Entity\ParameterValue;
                $parameterValue->setParameterFormat($format);
                $object->addParameterValue($parameterValue);
            }
            return;
        }
    }

//     /**
//     * Create action.
//     *
//     * @return Response
//     *
//     * @throws AccessDeniedException If access is not granted
//     */
//    public function createAction()
//    {
//        $request = $this->getRequest();
//        // the key used to lookup the template
//        $templateKey = 'edit';
//
//        $this->admin->checkAccess('create');
//
//        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());
//
//        if ($class->isAbstract()) {
//            return $this->render(
//                'SonataAdminBundle:CRUD:select_subclass.html.twig',
//                array(
//                    'base_template' => $this->getBaseTemplate(),
//                    'admin' => $this->admin,
//                    'action' => 'create',
//                ),
//                null,
//                $request
//            );
//        }
//
//        $newObject = $this->admin->getNewInstance();
//
//        $preResponse = $this->preCreate($request, $newObject);
//        if ($preResponse !== null) {
//            return $preResponse;
//        }
//
//        $this->admin->setSubject($newObject);
//
//        /** @var $form \Symfony\Component\Form\Form */
//        $form = $this->admin->getForm();
//        $form->setData($newObject);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted()) {
//            //TODO: remove this check for 4.0
//            if (method_exists($this->admin, 'preValidate')) {
//                $this->admin->preValidate($newObject);
//            }
//            $isFormValid = $form->isValid();
//
//            // persist if the form was valid and if in preview mode the preview was approved
//            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
//                $submittedObject = $form->getData();
//                $this->admin->setSubject($submittedObject);
//                $this->admin->checkAccess('create', $submittedObject);
//
//                try {
//                    $newObject = $this->admin->create($submittedObject);
//
//                    if ($this->isXmlHttpRequest()) {
//                        return $this->renderJson(array(
//                            'result' => 'ok',
//                            'objectId' => $this->admin->getNormalizedIdentifier($newObject),
//                        ), 200, array());
//                    }
//
//                    $this->addFlash(
//                        'sonata_flash_success',
//                        $this->trans(
//                            'flash_create_success',
//                            array('%name%' => $this->escapeHtml($this->admin->toString($newObject))),
//                            'SonataAdminBundle'
//                        )
//                    );
//
//                    // redirect to edit mode
//                    return $this->redirectTo($newObject);
//                } catch (ModelManagerException $e) {
//                    $this->handleModelManagerException($e);
//
//                    $isFormValid = false;
//                }
//            }
//
//            // show an error message if the form failed validation
//            if (!$isFormValid) {
//                if (!$this->isXmlHttpRequest()) {
//                    $this->addFlash(
//                        'sonata_flash_error',
//                        $this->trans(
//                            'flash_create_error',
//                            array('%name%' => $this->escapeHtml($this->admin->toString($newObject))),
//                            'SonataAdminBundle'
//                        )
//                    );
//                }
//            } elseif ($this->isPreviewRequested()) {
//                // pick the preview template if the form was valid and preview was requested
//                $templateKey = 'preview';
//                $this->admin->getShow();
//            }
//        }
//
//        $formView = $form->createView();
//        // set the theme for the current Admin Form
//        $this->setFormTheme($formView, $this->admin->getFormTheme());
//
//        return $this->render($this->admin->getTemplate($templateKey), array(
//            'action' => 'create',
//            'form' => $formView,
//            'object' => $newObject,
//        ), null);
//    }
}
