<?php 

namespace Redwood\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class SettingController extends BaseController
{

    public function siteAction(Request $request)
    {
        $site = array();

        return $this->render('RedwoodAdminBundle:System:site.html.twig', array(
            'site'=>$site
        ));
    }

    public function loginConnectAction(Request $request)
    {
        $loginConnect = array();
        // $loginConnect = $this->getSettingService()->get('login_bind', array());

        // $default = array(
        //     'login_limit'=>0,
        //     'enabled'=>0,
        //     'weibo_enabled'=>0,
        //     'weibo_key'=>'',
        //     'weibo_secret'=>'',
        //     'qq_enabled'=>0,
        //     'qq_key'=>'',
        //     'qq_secret'=>'',
        //     'renren_enabled'=>0,
        //     'renren_key'=>'',
        //     'renren_secret'=>'',
        //     'verify_code' => '',
        // );

        // $loginConnect = array_merge($default, $loginConnect);
        // if ($request->getMethod() == 'POST') {
        //     $loginConnect = $request->request->all();
        //     $this->getSettingService()->set('login_bind', $loginConnect);
        //     $this->getLogService()->info('system', 'update_settings', "更新登录设置", $loginConnect);
        //     $this->setFlashMessage('success','登录设置已保存！');
        // }

        return $this->render('RedwoodAdminBundle:System:login-connect.html.twig', array(
            'loginConnect' => $loginConnect
        ));
    }




}