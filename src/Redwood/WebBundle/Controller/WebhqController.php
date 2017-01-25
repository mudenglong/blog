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
        $configId = $request->query->get('cid');
        $userId = $user->id;
        $this->getConfigService()->deleteConfig($configId, $userId);
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


    /**
     *
     *生成绘图配置的压缩文件
     */


    public function generateAction(Request $request)
    {
        $json = $request->query->get('json');
        $json = '{
            "fsprice": {
            "nowp": "#6A9FD3",
            "avp": "#E5A045",
            "nowpClose": "rgba(141,168,248, .05)"
            },
            "crossColor": "#143C14",
            "fsvol": {
                "upColor": "#D85342",
                "downColor": "#6CA584",
                "eqColor": "#CCCCCC"
             }
        }';

        $folder = md5($json);
        if ($this->getFileService()->existZipFile($folder . '.zip')) {
            return $this->render('RedwoodWebBundle:Webhq:download.html.twig', array('filename' => $folder));
        }
        $path = '';
        $this->getFileService()->makeFolder($folder);
        if ($path = $this->getFileService()->existJsFolder($folder)) {
            $this->getFileService()->removeFolder($path);
        }
        $path = $this->getFileService()->makeFolder($folder);
        $originalConfig = $this->getFileService()->getConfigFromFile("config.json");
        $newConfig = $this->getFileService()->getConfigFromJson($json);
        $realConfig = $this->getFileService()->modifyConfig($originalConfig, $newConfig);
        $buildJs = $this->renderView('RedwoodWebBundle:Webhq:build.js.twig', array(
            'configs' => $realConfig,
            'name' => $folder,
        ));
        $entranceJs = $this->renderView('RedwoodWebBundle:Webhq:entrance.js.twig', array(
            'configs' => $realConfig
        ));
        $this->getFileService()->writeConfigJs($path, 'build.js', $buildJs);
        $this->getFileService()->writeConfigJs($path, 'entrance.js', $entranceJs);
        $this->getFileService()->generateJs($path);
        $releasePath = $path . "/release/";
        $this->getFileService()->zipFolder($releasePath, $folder);
        return $this->render('RedwoodWebBundle:Webhq:download.html.twig', array('filename' => $folder));
    }

	
}
