<?php

namespace Redwood\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
{
    public function indexAction()
    {
		var_dump("hfas");exit;
    	$user = $this->getCurrentUser();
    	// var_dump($user);
        return $this->render('RedwoodWebBundle:Default:index.html.twig', array('name' => 'testtest'));
    }

    public function testhomeAction()
    {
        // var_dump($user);
        return $this->render('RedwoodWebBundle:Default:testhome.html.twig', array('name' => 'testtest'));
    }

	public function helloAction()
	{
		return new Response("hello world");
	}
}
