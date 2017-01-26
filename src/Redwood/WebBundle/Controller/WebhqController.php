<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Redwood\Service\Content\Impl\FileServiceImpl;

class WebhqController extends BaseController
{

    public function indexAction() 
    {   
        return $this->render('RedwoodWebBundle:Webhq:index.html.twig');
    }
    
    public function buildAction() 
    {   
        return $this->render('RedwoodWebBundle:Webhq:build.html.twig');
    }

    public function startAction() 
    {   
        return $this->render('RedwoodWebBundle:Webhq:start.html.twig');
    }

    /**
     *
     * 添加绘图配置
     */
    public function addConfigAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return $this->createMessageResponse('info', "你好像忘了登录哦？ 10秒后将自动跳转到登录页面.", '', 10, $this->generateUrl('login'));
        }
        $config = $request->query->all();
        $config['userid'] = $user->id;
        $this->getConfigService()->createConfig($config);
        return $this->render('RedwoodWebBundle:Webhq:build.html.twig');
    }


    /**
     *
     * 删除绘图配置
     */
    public function deleteConfigAction(Request $request)
    {
        $user = $this->getCurrentUser(); 
        $cid = $request->query->get('cid');
        $uid = $user->id;
        $this->getConfigService()->deleteConfig($cid, $uid);
        return $this->redirect($this->generateUrl('webhq_list'));
    }

    /**
     *绘图配置列表
     */
    public function listConfigAction()
    {
        $user = $this->getCurrentUser();
        $data = $this->getConfigService()->listConfig($user->id);
        return $this->render('RedwoodWebBundle:Webhq:list.html.twig', array('datas' => $data));
    }
	
}
