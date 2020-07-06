<?php

namespace TAMAS\ALFABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TAMASALFABundle:Default:index.html.twig');
    }
    
    public function aboutAction()
    {
        return $this->render('TAMASALFABundle:Default:about.html.twig');
    }
}
