<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Redwood\Common\FileToolkit;

use Imagine\Gd\Imagine;

class WeckerController extends BaseController
{    
    public function htmlAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('html', 'file')
            ->getForm();
        
        if($request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $data = $form->getData();
                $file = $data['html'];

                $filenamePrefix = date('Y') .  date('m-d') . date('His');
                $hash = substr(md5($filenamePrefix.time()),-8);
                $ext = $file->getClientOriginalExtension();

                $filename = $filenamePrefix.$hash.'.'.$ext;
                $directory = $this->container->getParameter('redwood.upload.public_directory').'/htmlTemp';
                $file = $file->move($directory, $filename);

                $fileName = str_replace('.', '!', $file->getFilename());
  
                return $this->redirect($this->generateUrl('wecker_html_crop', array(
                    'file' => $fileName,
                    )
                ));
            }
        }

        return $this->render('RedwoodWebBundle:Wecker:show.html.twig',array(
            'form' => $form->createView(),
        ));
    }

    public function htmlCropAction(Request $request)
    {


        $filename = $request->query->get('file');
        $filename = str_replace('!', '.', $filename);

        $pictureFilePath = $this->container->getParameter('redwood.upload.public_directory') . '/htmlTemp/' . $filename;

        if($request->getMethod()=='POST')
        {
         
            $options  = $request->request->all();
            $postData = $options['postData'];
            $lines    = $postData['lines'];
            $naturalWidth = $postData['imageNaturalWidth'];
            $naturalHeight = $postData['imageNaturalHeight'];

            $boxs= '';
            if (array_key_exists("boxs",$postData)) 
            { 
                $boxs = $postData['boxs'] ;
                foreach ($boxs as &$box) {
                    $box['marginLeft'] = -(round((int)$naturalWidth/2)-((int)$box['x']));
                }
            }

            $imagesRecords = $this->getFileService()->uploadHtmlPic($pictureFilePath, $options);
            // test data
            // $imagesRecords = array(
            //                     'imagesInfos' => array('img0.jpg','img1.jpg','img2.jpg','img3.jpg','img4.jpg','img5.jpg','img6.jpg'),
            //                     'secret' => '2014/06-24/1a36cd185697',
            //                 );

            $divCoors = $this->getFileService()->getCropDivCoordsByLines($lines, $naturalHeight);
            foreach ($divCoors as $key => $value) {
                foreach ($imagesRecords['imagesInfos'] as $imagesRecord) {
                    $divCoors[$key]['filename'] = $imagesRecords['imagesInfos'][$key];
                }
            }

            $html = $this->renderView('RedwoodWebBundle:Wecker:pageView.html.twig', array(
                'imagesInfos' => $divCoors,
                'boxs' => $boxs ? $boxs:'',
            ));
            $cropDirPath = 'public://cropHtml/'. $imagesRecords['secret'];
            $this->getFileService()->writeFile($cropDirPath, $html);
            $this->getFileService()->zipFolder($cropDirPath);

            return $this->createJsonResponse(array(
                'status' => 'success', 
                'secret' => base64_encode($imagesRecords['secret']),
            ));
   
        }

        try {
            $imagine = new Imagine();
            $image = $imagine->open($pictureFilePath);
        } catch (\Exception $e) {
            @unlink($pictureFilePath);
            return $this->createMessageResponse('error', '该文件为非图片格式文件，请重新上传。');
        }

        $naturalSize = $image->getSize();
        $scaledSize = $naturalSize->widen(1000)->heighten(1000);
        $pictureUrl = $this->container->getParameter('redwood.upload.public_url_path') . '/htmlTemp/' . $filename;

        return $this->render('RedwoodWebBundle:Wecker:html-crop.html.twig', array(
            'pictureUrl' => $pictureUrl,
            'naturalSize' => $naturalSize,
            'scaledSize' => $scaledSize,
        ));
    }


    public function zipGetAction(Request $request)
    {
        
        $secret = base64_decode($request->query->get('data'));
        if ($secret) {
            $result = $this->getFileService()->downloadZip($secret);
        }

        return $this->createJsonResponse(array(
            'status' => $result, 
        ));

    }

}