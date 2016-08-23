<?php
namespace Redwood\WebBundle\Listener;

use Redwood\Service\Common\ServiceKernel;
use Redwood\Service\Common\AccessDeniedException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class KernelRequestListener
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    // 主要是添加 表单的防止跨域攻击 token校验
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
        //     $blacklistIps = ServiceKernel::instance()->createService('System.SettingService')->get('blacklist_ip');

        //     if (isset($blacklistIps['ips'])) {
        //         $blacklistIps = $blacklistIps['ips'];

        //         if (in_array($request->getClientIp(), $blacklistIps)) {
        //             throw new AccessDeniedException('您的IP已被列入黑名单，访问被拒绝，如有疑问请联系管理员！');
        //         }
        //     }
        // }

        if (($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) && ($request->getMethod() == 'POST')) {
            if (stripos($request->getPathInfo(), '/mapi') === 0) {
                return;
            }

            $whiteList = $this->container->hasParameter('route_white_list') ? $this->container->getParameter('route_white_list') : array();


            // 在白名单中的, 不用校验
            if (in_array($request->getPathInfo(), $whiteList)) {
                return;
            }

            if ($request->isXmlHttpRequest()) {
                $token = $request->headers->get('X-CSRF-Token');
            } else {
                $token = $request->request->get('_csrf_token', '');
            }

            $request->request->remove('_csrf_token');

            $expectedToken = $this->container->get('form.csrf_provider')->generateCsrfToken('site');

            if ($token != $expectedToken) {
            // @todo 需要区分ajax的response

                $response = $this->container->get('templating')->renderResponse('RedwoodWebBundle:Default:message.html.twig', array(
                    'type'     => 'error',
                    'message'  => '页面已过期，请重新提交数据！(csrf_token)',
                    'goto'     => '',
                    'duration' => 0
                ));

                $event->setResponse($response);
              
            }
        }
    }
}
