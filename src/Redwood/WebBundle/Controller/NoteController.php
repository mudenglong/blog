<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class NoteController extends BaseController
{

    public function inboxShowAction() 
    {
    	$user = $this->getCurrentUser();
        return $this->render('RedwoodWebBundle:Note:inbox_show.html.twig', array(
            'user' => $user,
        ));
    }

    public function indexAction() 
    {
    	$user = $this->getCurrentUser();
        return $this->render('RedwoodWebBundle:Note:base.html.twig', array(
            'user' => $user,
        ));
    }
}