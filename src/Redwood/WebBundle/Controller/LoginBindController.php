<?php
namespace Redwood\WebBundle\Controller;

use Redwood\Common\SimpleValidator;
use Symfony\Component\HttpFoundation\Request;
use Redwood\Component\OAuthClient\OAuthClientFactory;

class LoginBindController extends BaseController
{

    public function indexAction (Request $request, $type)
    {
        if ($request->query->has('_target_path')) {
            $request->getSession()->set('_target_path', $request->query->get('_target_path'));
        }

        $client = $this->createOAuthClient($type);
        
        $callbackUrl = $this->generateUrl('login_bind_callback', array('type' => $type), true);

        $url = $client->getAuthorizeUrl($callbackUrl);
        return $this->redirect($url);
    }

    public function callbackAction(Request $request, $type)
    {
        $code = $request->query->get('code');
        $callbackUrl = $this->generateUrl('login_bind_callback', array('type' => $type), true);
        $token = $this->createOAuthClient($type)->getAccessToken($code, $callbackUrl);
        $bind = $this->getUserService()->getUserBindByTypeAndFromId($type, $token['userId']);

        if ($bind) {
            $user = $this->getUserService()->getUser($bind['toId']);
            if (empty($user)) {
                $this->setFlashMessage('danger','绑定的用户不存在，请重新绑定。');
                return $this->redirect($this->generateUrl('register'));
            }
            $this->authenticateUser($user);
            $goto = $request->getSession()->get('_target_path', '') ? : $this->generateUrl('homepage');
            return $this->redirect($goto);
        } else {
            $request->getSession()->set('oauth_token', $token);

            return $this->redirect($this->generateUrl('login_bind_choose', array('type'  => $type)));
        }

    }

    public function chooseAction(Request $request, $type)
    {
        $token = $request->getSession()->get('oauth_token');
        $client = $this->createOAuthClient($type);
        $oauthUser = $client->getUserInfo($token);
        $name = $this->mateName($type);

        return $this->render('RedwoodWebBundle:Login:bind-choose.html.twig', array(
            'oauthUser' => $oauthUser,
            'type' => $type,
            'name' => $name
        ));
    }

    public function newSetAction(Request $request, $type)
    {
        $setData = $request->request->all();
        if (isset($setData['set_bind_emailOrMobile']) && !empty($setData['set_bind_emailOrMobile'])) {
            if (SimpleValidator::email($setData['set_bind_emailOrMobile'])) {
                $setData['email'] = $setData['set_bind_emailOrMobile'];
            }

            unset($setData['set_bind_emailOrMobile']);
        }

        $token = $request->getSession()->get('oauth_token');

        if (empty($token)) {
            $response = array('success' => false, 'message' => '页面已过期，请重新登录。');
            goto response;
        }

        $client                 = $this->createOAuthClient($type);
        $oauthUser              = $client->getUserInfo($token);
        $oauthUser['createdIp'] = $request->getClientIp();
        if (empty($oauthUser['id'])) {
            $response = array('success' => false, 'message' => '网络超时，获取用户信息失败，请重试。');
            goto response;
        }

        // if (!$this->getAuthService()->isRegisterEnabled()) {
        //     $response = array('success' => false, 'message' => '注册功能未开启，请联系管理员！');
        //     goto response;
        // }
        
        $user = $this->generateUser($type, $token, $oauthUser, $setData);
        if (empty($user)) {
            $response = array('success' => false, 'message' => '登录失败，请重试！');
            goto response;
        }

        $this->authenticateUser($user);

        // if (!empty($oauthUser['avatar'])) {
        //     $this->getUserService()->changeAvatarFromImgUrl($user['id'], $oauthUser['avatar']);
        // }

        $response = array('success' => true, '_target_path' => $this->generateUrl('jswidget_show'));

        response:

        return $this->createJsonResponse($response);
    }




    protected function mateName($type)
    {
        switch ($type) {
            case 'weixinweb':
                return '微信注册帐号';
                break;
            case 'weixinmob':
                return '微信注册帐号';
                break;
            case 'weibo':
                return '微博注册帐号';
                break;
            case 'qq':
                return 'QQ注册账号';
                break;
            case 'gitlab':
                return 'Gitlab注册账号';
                break;
            default:
                return '';
        }
    }

    protected function generateUser($type, $token, $oauthUser, $setData)
    {
        $registration      = array();

        if (!empty($setData['username']) && !empty($setData['email'])) {
            $registration['username']      = $setData['username'];
            $registration['email']         = $setData['email'];
        } else {
            // @todo 之后完善 没有username 信息的 第三方登录
            return;
        }

        $registration['password']  = substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, 8);
        $registration['token']     = $token;
        $registration['createdIp'] = $oauthUser['createdIp'];

        $user = $this->getAuthService()->register($registration, $type);
        return $user;
    }

    public function existAction(Request $request, $type)
    {
        $token = $request->getSession()->get('oauth_token');
        $client = $this->createOAuthClient($type);

        $oauthUser = $client->getUserInfo($token);

        $data = $request->request->all();
        $user = $this->getUserService()->getUserByEmail($data['email']);
        if (empty($user)) {
            $response = array('success' => false, 'message' => '该Email地址尚未注册');
        } elseif(!$this->getUserService()->verifyPassword($user['id'], $data['password'])) {
            $response = array('success' => false, 'message' => '密码不正确，请重试！');
        } elseif ($this->getUserService()->getUserBindByTypeAndUserId($type, $user['id'])) {
            $response = array('success' => false, 'message' => "该{{ $this->setting('site.name') }}帐号已经绑定了该第三方网站的其他帐号，如需重新绑定，请先到账户设置中取消绑定！");
        } else {
            $response = array('success' => true, '_target_path' => $request->getSession()->get('_target_path', $this->generateUrl('homepage')));
            $this->getUserService()->bindUser($type, $oauthUser['id'], $user['id'], $token);
            $this->authenticateUser($user);
        }

        return $this->createJsonResponse($response);
    }

    private function createOAuthClient($type)
    {

        // if (empty($settings)) {
        //     throw new \RuntimeException('第三方登录系统参数尚未配置，请先配置。');
        // }

        // if (empty($settings) or !isset($settings[$type.'_enabled']) or empty($settings[$type.'_key']) or empty($settings[$type.'_secret'])) {
        //     throw new \RuntimeException("第三方登录({$type})系统参数尚未配置，请先配置。");
        // }

        // if (!$settings[$type.'_enabled']) {
        //     throw new \RuntimeException("第三方登录({$type})未开启");
        // }

        // $config = array('key' => $settings[$type.'_key'], 'secret' => $settings[$type.'_secret']);
        $key = $this->container->getParameter('gitlab_key');
        $secret = $this->container->getParameter('gitlab_secret');
        $config = array(
                'key' => $key, 
                'secret' => $secret
            );
        $client = OAuthClientFactory::create($type, $config);

        return $client;
    }

    private function getAuthService()
    {
        return $this->getServiceKernel()->createService('User.AuthService');
    }

}