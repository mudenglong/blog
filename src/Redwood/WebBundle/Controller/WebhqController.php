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


    /**
     *
     *生成绘图配置的压缩文件
     */
     private function generate()
     {

         $path = $_SERVER['DOCUMENT_ROOT'];
         $path .= "/js/webHQ222";
         $cmd = "{$path}/projects/161122-new-wap/node {$path}/r.js -o {$path}/projects/161122-new-wap/build.js";  
         shell_exec($cmd);
         $zip = new \ZipArchive();
         $time = time();
         $filename = md5($time) . '.zip';
         if ($zip->open("{$path}/projects/161122-new-wap/release/zip/{$filename}",\ZIPARCHIVE::CREATE) !== true) {
            exit('无法打开文件，创建文件失败');
         }
         $filePath = "{$path}/projects/161122-new-wap/release/dist/wapa.min.js";
         $file = "wapa.min.js";
         $zip->addFile($filePath, $file);
         $zip->close();
         $can_open = fopen("{$path}/projects/161122-new-wap/release/zip/{$filename}",'r');
         if ($can_open) {
            return $this->render('RedwoodWebBundle:Webhq:download.html.twig', array('filename' => $filename));
         } else {
            throw $this->createServiceException("生成失败");
         }
     }

     public function testAction()
     {
        return $this->generate();
     }

     public function downloadZipAction()
     {
        
     }

     public function zipAction()
     {
     }

	
}
