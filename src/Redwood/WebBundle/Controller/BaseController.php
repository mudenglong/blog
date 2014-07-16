<?php

namespace Redwood\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Redwood\Service\Common\ServiceKernel;
use Redwood\Service\User\CurrentUser;

abstract class BaseController extends Controller
{
	/**
     * 获得当前用户
     * 
     * 如果当前用户为游客，那么返回id为0, nickanme为"游客", currentIp为当前IP的CurrentUser对象。
     * 不能通过empty($this->getCurrentUser())的方式来判断用户是否登录。
     */
    protected function getCurrentUser()
    {
        return $this->getUserService()->getCurrentUser();
    }

    protected function isAdminOnline()
    {
        return $this->get('security.context')->isGranted('ROLE_ADMIN');
    }

    public function getUser()
    {
        throw new \RuntimeException('获得当前登录用户的API变更为：getCurrentUser()。');
    }

	protected function authenticateUser ($user)
    {
        $currentUser = new CurrentUser();
        $currentUser->fromArray($user);

        $token = new UsernamePasswordToken($currentUser, null, 'main', $currentUser['roles']);
        $this->container->get('security.context')->setToken($token);

        $loginEvent = new InteractiveLoginEvent($this->getRequest(), $token);
        $this->get('event_dispatcher')->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
    }
    
    protected function sendEmail ($receiver, $title, $body, $format = 'text/plain') {
        $siteName = $this->container->getParameter('site_name');
        $emailFrom = $this->container->getParameter('mailer_account');

        try {
            $mailer = $this->get('mailer');
            $email = \Swift_Message::newInstance();
            $email->setSubject($title);
            $email->setFrom(array($emailFrom => $siteName));
            $email->setTo($receiver);
            $email->setBody($body, $format);
            
            $mailer->send($email);
        } catch (\Exception $e) {
            // @todo log it.
        }

        return true;
    }

    //页面中用到的 ajax 刷新页面，都是用这个方法的
    protected function createJsonResponse($data)
    {
        return new JsonResponse($data);
    }

    protected function getServiceKernel()
    {
        return ServiceKernel::instance();
    }

    protected function getUserService()
    {
        return $this->getServiceKernel()->createService('User.UserService');
    }

    protected function getFileService()
    {
        return $this->getServiceKernel()->createService('Content.FileService');        
    }

    protected function getLogService()
    {
        return $this->getServiceKernel()->createService('System.LogService');
    }
}
