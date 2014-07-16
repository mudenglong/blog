<?php

namespace Redwood\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends BaseController
{
    public function indexAction()
    {
    	$user = $this->getCurrentUser();
    	// var_dump($user);
        return $this->render('RedwoodWebBundle:Default:index.html.twig', array('name' => 'testtest'));
    }
}
