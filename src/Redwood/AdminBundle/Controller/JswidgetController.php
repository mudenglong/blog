<?php 

namespace Redwood\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;

class JswidgetController extends BaseController
{

    public function indexAction() 
    {   
        $user = $this->getCurrentUser();
        return $this->render('RedwoodAdminBundle:Jswidget:index.html.twig', array(
            'user' => $user
        ));

    }

}