<?php
namespace Redwood\WebBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

class LoginController extends BaseController
{

    public function indexAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('RedwoodWebBundle:Login:index.html.twig',array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
            'targetPath' => $this->getTargetPath($request)
        ));
    }


    private function getTargetPath($request)
    {
        if ($request->getSession()->has('_target_path')) {
            $targetPath = $request->getSession()->get('_target_path');
        } else {
            $targetPath = $request->headers->get('Referer');
        }

        if ($targetPath == $this->generateUrl('login', array(), true)) {
            return $this->generateUrl('jswidget_show');
        }

        $url = explode('?', $targetPath);

        // if ($url[0] == $this->generateUrl('partner_logout', array(), true)) {
        //     return $this->generateUrl('homepage');
        // }

        
        // if ($url[0] == $this->generateUrl('password_reset_update', array(), true)) {
        //     $targetPath = $this->generateUrl('homepage', array(), true);
        // }

        return $targetPath;
    }
}