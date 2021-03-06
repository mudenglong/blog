<?php
namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PasswordResetController extends BaseController
{

    public function indexAction(Request $request)
    {
        $user = $this->getCurrentUser();

        $data = array('email' => '');
        if ($user->isLogin()) {
            $data['email'] = $user['email'];
        }

        $form = $this->createFormBuilder($data)
            ->add('email', 'email')
            ->getForm();

        $error = null;

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->getUserService()->getUserByEmail($data['email']);

                if ($user) {
                    $token = $this->getUserService()->makeToken('password-reset', $user['id'], strtotime('+1 day'));


                    $siteName = $this->container->getParameter('site_name');

                    $emailTitle = "重设{$user['username']}在{$siteName}的密码";
                    $emailBody = $this->renderView('RedwoodWebBundle:PasswordReset:reset.txt.twig', array(
                            'user' => $user,
                            'token' => $token,
                        ));

                    $this->sendEmail($user['email'], $emailTitle, $emailBody, 'text/html');

                    $this->getLogService()->info('user', 'password-reset', "{$user['email']}向发送了找回密码邮件。");

                    return $this->render('RedwoodWebBundle:PasswordReset:sent.html.twig', array(
                        'user' => $user,
                        'emailLoginUrl' => $this->getEmailLoginUrl($user['email']),
                    ));
                } else {
                    $error = '该邮箱地址没有注册过帐号';
                }
            }
        }

        return $this->render("RedwoodWebBundle:PasswordReset:index.html.twig", array(
            'form' => $form->createView(),
            'error' => $error,
        ));
    }

    public function updateAction(Request $request)
    {
        $token = $this->getUserService()->getTokenByToken('password-reset', $request->query->get('token'));
        if (empty($token)) {
            return $this->render('RedwoodWebBundle:PasswordReset:error.html.twig');
        }

        $form = $this->createFormBuilder()
            ->add('password', 'password')
            ->add('confirmPassword', 'password')
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->getUserService()->changePassword($token['userId'], $data['password']);

                $this->getUserService()->deleteToken('password-reset', $token['token']);

                return $this->render('RedwoodWebBundle:PasswordReset:success.html.twig');

            }
        }

        return $this->render('RedwoodWebBundle:PasswordReset:update.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function getEmailLoginUrl ($email)
    {
        $host = substr($email, strpos($email, '@') + 1);
        
        if ($host == 'hotmail.com') {
            return 'http://www.' . $host;
        }
        
        if ($host == 'gmail.com') {
            return 'http://mail.google.com';
        }
        
        return 'http://mail.' . $host;
    }

}