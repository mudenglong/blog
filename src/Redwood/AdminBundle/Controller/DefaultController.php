<?php
namespace Redwood\AdminBundle\Controller;

class DefaultController extends BaseController
{

    public function indexAction()
    {
        return $this->render('RedwoodAdminBundle:Default:index.html.twig');
    }
   
}

