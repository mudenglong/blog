<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class WebhqController extends BaseController
{

    public function indexAction() 
    {   
        return $this->render('RedwoodWebBundle:Webhq:index.html.twig');
    }

}