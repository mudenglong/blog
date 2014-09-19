<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Redwood\Common\FileToolkit;

class KindeditorController extends BaseController
{

    public function uploadAction(Request $request) 
    {
        // try {
            $finalPath2 = '/var/www/edusoho/web/files/user/2014/09-15/';
            
            var_dump(is_writable($finalPath2));
            $finalPath = '/var/www/fastnote/app/cache/dev/../../../web/files/note/2014/09-15';
            
            var_dump(is_writable($finalPath));
            // return false;

            $group = $request->request->get('group');
            $file = $request->files->get('file');
  
            if (!FileToolkit::isImageFile($file)) {
                return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, gif, png格式的文件。');
            }

            $record = $this->getFileService()->uploadFile($group, $file);
            $response = array(
                'error' => 0,
                'url' => $this->get('redwood.twig.web_extension')->sqlUriConvertWebUri($record['uri'])
            );
            
        // } catch (\Exception $e) {
        //     $response = array(
        //         'error' => 1,
        //         'message' => '文件上传失败！'
        //     );
        // }

        return new Response(json_encode($response));
    }
}