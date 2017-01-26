<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class WebhqController extends BaseController
{

    public function indexAction() 
    {   
        return $this->render('RedwoodWebBundle:Webhq:index.html.twig');
    }
    
    public function buildAction() 
    {   
        return $this->render('RedwoodWebBundle:Webhq:build.html.twig');
    }

    public function startAction() 
    {   
        return $this->render('RedwoodWebBundle:Webhq:start.html.twig');
    }

}