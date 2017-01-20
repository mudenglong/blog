<?php

namespace Redwood\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Redwood\Service\Common\ServiceKernel;
use Redwood\Service\Common\AccessDeniedException;

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

    protected function setFlashMessage($level, $message)
    {
        $this->get('session')->getFlashBag()->add($level, $message);
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

    /**
     * 创建消息提示响应
     * @todo 理解
     * @param  string  $type        消息类型：info, warning, error
     * @param  string  $message     消息内容
     * @param  string  $title       消息抬头
     * @param  integer $duration    消息显示持续的时间
     * @param  string  $goto        消息跳转的页面
     * @return Response
     */
    protected function createMessageResponse($type, $message, $title = '', $duration = 0, $goto = null)
    {
        if (!in_array($type, array('info', 'warning', 'error'))) {
            throw new \RuntimeException('type不正确');
        }

        return $this->render('RedwoodWebBundle:Default:message.html.twig', array(
            'type' => $type,
            'message' => $message,
            'title' => $title,
            'duration' => $duration,
            'goto' => $goto,
        ));
    }

    protected function createAccessDeniedException($message = null)
    {
        if ($message) {
            return new AccessDeniedException($message);
        } else {
            return new AccessDeniedException();
        }
    }

    protected function createNamedFormBuilder($name, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->createNamedBuilder($name, 'form', $data, $options);
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

    protected function getNoteService()
    {
        return $this->getServiceKernel()->createService('Note.NoteService');
    }

    // 获得组件库服务
    protected function getJswidgetService()
    {
        return $this->getServiceKernel()->createService('Note.JswidgetService');
    }

    protected function getTagService()
    {
        return $this->getServiceKernel()->createService('Taxonomy.TagService');
    }

    // 图表组件服务
    protected function getConfigService()
    {
        return $this->getServiceKernel()->createService('Config.ConfigService');
    }

}
