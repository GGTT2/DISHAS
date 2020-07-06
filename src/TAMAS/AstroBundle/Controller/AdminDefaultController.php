<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class AdminDefaultController extends TAMASController
{

    /* _________________________________ gestion tools _______________________ */

    /**
     * adminHomeAction
     *
     * returns the view of the page adminHome (admin user side)
     *
     * @return object
     */
    public function adminHomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $private_announcement = $em->getRepository(\TAMAS\AstroBundle\Entity\UserInterfaceText::class)->findOneBy([
            'textName' => 'private_announcement'
        ]);
        return $this->render('TAMASAstroBundle:AdminDefault:adminHome.html.twig', [
            'private_announcement' => $private_announcement
        ]);
    }

    /**
     * adminGlossaryAction
     *
     * returns the vue of the page adminGlossary (admin user side)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminDocumentationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $model_documentations = $em->getRepository(\TAMAS\AstroBundle\Entity\PDFFile::class)->getModelDocumentation();
        $documentations = $em->getRepository(\TAMAS\AstroBundle\Entity\PDFFile::class)->getGeneralDocumentation();

        $formulaDefinitions = $this->generateSpec("FormulaDefinition");
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\FormulaDefinitionViewType::class, null);
        return $this->render('TAMASAstroBundle:AdminDefault:adminDocumentation.html.twig', [
            "form" => $form->createView(),
            "documentations" => $documentations,
            "model_documentations" => $model_documentations,
            "formulaDefinitions" => $formulaDefinitions, 
            'objectEntityName' => "formulaDefinition",
            "spec" => ['editDelete' => true, 'adminInfo'=> false]
        ]);
    }

    /**
     * adminSpaceAction
     *
     * returns the view of the page adminSpace (admin user side)
     *
     * @return object
     */
    public function adminSpaceAction()
    {
        return $this->render('TAMASAstroBundle:AdminDefault:adminSpace.html.twig',
            ['spec' => false]);
    }

    /**
     * adminContactMembersAction
     *
     * This method manages the sending of e-mails from members to members by DISHAS webmail host.
     */
    public function adminContactUserAction(Request $request, $id = null)
    {
        $session = $request->getSession();
        $container = $this->container;
        $usermail = $this->getUser()->getEmail();
        $userName = $this->getUser()->getUsername();
        $form = $this->createFormBuilder()
            ->add('contact', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'required' => true,
            'class' => \TAMAS\AstroBundle\Entity\Users::class,
            'choice_label' => 'username'
        ))
            ->add('message', TextareaType::class, [
            'attr' => [
                'class' => 'ckeditor'
            ],
            'required' => false
        ])
            ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $messageUser = $data['message'];
            $sendToId = $data['contact'];
            $sendTo = $container->get('fos_user.user_manager')
                ->findUserBy([
                'id' => $sendToId
            ])
                ->getEmail();
            $sendFrom = $usermail;
            $subject = "Message from DISHAS web application";
            $message = (new \Swift_Message($subject))->setFrom("admin.DISHAS-noreply@obspm.fr")
                ->setTo($sendTo)
                ->setBody($this->renderView('TAMASAstroBundle:Email:contact.html.twig', [
                'message' => $messageUser,
                'from' => $userName,
                'address' => $sendFrom
            ]), 'text/html');
            $mailer = $this->get('mailer');
            $success = $mailer->send($message);
            $spool = $mailer->getTransport()->getSpool();
            $transport = $this->get('swiftmailer.transport.real');

            $spool->flushQueue($transport);

            if (! $success) {
                $session->getFlashBag()->add("danger", 'The e-mail could not be sent.');
            } else {
                $session->getFlashBag()->add("success", 'The e-mail was sent correctly.');
            }
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        return $this->render('TAMASAstroBundle:AdminDefault:adminContactUser.html.twig', array(
            'form' => $form->createView(),
            'id' => $id
        ));
    }
}
