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

    public function createAction(Request $request)
    {
        $user = $this->getCurrentUser();

        $form = $this->creatNoteForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $note = $form->getData();

                $note = $this->getNoteService()->createNote($note);

                // var_dump($note);
            }
        }
        return $this->render('RedwoodWebBundle:Note:create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function creatNoteForm($data = array())
    {
        return $this->createNamedFormBuilder('note', $data)
            ->add('title', 'text')
            ->add('content', 'textarea')
            ->getForm();
    }

}