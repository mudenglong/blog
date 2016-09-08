<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\WebBundle\Form\RegisterForm;

class RegisterController extends BaseController
{
    public function successAction(Request $request, $id, $hash) {
        $user = $this->checkHash($id, $hash);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }

        // var_dump($this->getCurrentUser());
        return $this->render('RedwoodWebBundle:Register:success.html.twig', array(
            'user' => $user,
            'hash' => $hash,
            // 'emailLoginUrl' => $this->getEmailLoginUrl($user['email']),
        ));
    }

    public function indexAction(Request $request)
    {

		$form = $this->createForm(new RegisterForm());

		if ($request->isMethod('POST')) 
		{
		   $form->bind($request);
		   if ($form->isValid()) {

				$registration = $form->getData();
				$user = $this->getUserService()->register($registration);
				$this->authenticateUser($user);

                if($this->container->getParameter('is_mail_server_open')){
                    $this->sendVerifyEmail($user);
                };
				return $this->redirect($this->generateUrl('register_success', array(
                    'id' => $user['id'], 
                    'hash' => $this->makeHash($user),
                )));
		   }

		}
		return $this->render('RedwoodWebBundle:Register:index.html.twig', array(
			'form' => $form->createView(),
		));
	}

	private function sendVerifyEmail($user) {
        $token = $this->getUserService()->makeToken('email-verify', $user['id'], strtotime('+1 day'));
        $siteName = $this->container->getParameter('site_name');
        $emailTitle = "验证{$user['username']}在{$siteName}的电子邮箱";
        $emailBody = $this->renderView('RedwoodWebBundle:Register:verify-email.html.twig', array(
            'user' => $user,
            'token' => $token,
        ));

        $this->sendEmail($user['email'], $emailTitle, $emailBody, 'text/html');
    }

    /*
    * 第三方登录后会用到
    */
    public function usernameCheckAction(Request $request)
    {
        $username   = $request->query->get('value');
        $randomName = $request->query->get('randomName');
        list($result, $message) = $this->getAuthService()->checkUsername($username, $randomName);
        return $this->validateResult($result, $message);
    }

    /*
    * 第三方登录后会用到
    */
    public function emailCheckAction(Request $request)
    {
        $email = $request->query->get('value');
        $email = str_replace('!', '.', $email);
        list($result, $message) = $this->getAuthService()->checkEmail($email);
        return $this->validateResult($result, $message);
    }


    public function emailVerifyAction(Request $request, $token)
    {

        $token = $this->getUserService()->getTokenByToken('email-verify', $token);
        if (empty($token)) {
            $currentUser = $this->getCurrentUser();
            if (empty($currentUser)) {
                return $this->render('RedwoodWebBundle:Register:email-verify-error.html.twig');
            } else {
            	// @todo
                // return $this->redirect($this->generateUrl('settings'));
                return $this->render('RedwoodWebBundle:Register:email-verify-error.html.twig');
                
            }
        }

        $user = $this->getUserService()->getUser($token['userId']);
        if (empty($user)) {
            return $this->createNotFoundException();
        }

        $this->getUserService()->setEmailVerified($user['id']);

        $this->getUserService()->deleteToken('email-verify', $token['token']);

        return $this->render('RedwoodWebBundle:Register:email-verify-success.html.twig');
    }

    protected function validateResult($result, $message)
    {
        if ($result == 'success') {
            $response = array('success' => true, 'message' => '');
        } else {
            $response = array('success' => false, 'message' => $message);
        }

        return $this->createJsonResponse($response);
    }

	private function makeHash($user)
    {
        $string = $user['id'] . $user['email'] . $this->container->getParameter('secret');
        return md5($string);
    }

    private function checkHash($userId, $hash)
    {
        $user = $this->getUserService()->getUser($userId);
        if (empty($user)) {
            return false;
        }

        if ($this->makeHash($user) !== $hash) {
            return false;
        }

        return $user;
    }

    protected function getAuthService()
    {
        return $this->getServiceKernel()->createService('User.AuthService');
    }


}
