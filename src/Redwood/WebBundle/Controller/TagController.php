<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;
use Redwood\Common\ArrayToolkit;

class TagController extends BaseController
{

    /**
     * 取所有标签
     * @return [type] [description]
     */
    public function indexAction()
    {     
        $conditions = array(
            'name' => ''
        );

        $tags = $this->getTagService()->searchTags(
            $conditions,
            'latest',
            0,
            $this->getTagService()->searchTagCount($conditions)
        );

        return $this->render('RedwoodWebBundle:Tag:index.html.twig',array(
            'tags'=>$tags
        ));
    }

    public function showAction(Request $request, $id)
    {
        $tag = $this->getTagService()->getTag($id);

        if(!$tag){ 
            return $this->createMessageResponse('info', "非常抱歉，标签id:{$id} 未找到, 15秒后将自动跳转到标签首页.",'', 15,$this->generateUrl('tag')); 
        }
        exit;

        if($tag) {  
            $conditions = array(
                // 'status' => 'published',
                'tagId' => $tag['id']
            );

            $paginator = new Paginator(
                $this->get('request'),
                $this->getJswidgetService()->searchJswidgetCount($conditions)
                , 12
            );       

            $courses = $this->getJswidgetService()->searchJswidget(
                $conditions,
                'latest',
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount()
            );
        }



        var_dump('dddd');

    }

    
    


   

}