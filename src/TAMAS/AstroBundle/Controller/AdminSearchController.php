<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TAMAS\AstroBundle\Entity as E;

class AdminSearchController extends TAMASController
{
    use FormUtils;

    public function adminSimpleSearchParameterSetAction(Request $request)
    {
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\SearchParameterValueType::class, null);

        $get = $request->query->get("admin");
        $admin = isset($get) ? $get : false;

        return $this->render('TAMASAstroBundle:SearchObject:adminSimpleSearchParameterSet.html.twig', [
            'admin' => $admin,
            'form' => $form->createView()
        ]);
    }
}
