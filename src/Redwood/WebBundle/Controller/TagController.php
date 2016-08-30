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
        $jswidgets = $paginator = null;

        $tag = $this->getTagService()->getTag($id);

        if(!$tag){ 
            return $this->createMessageResponse('info', "非常抱歉，标签id:{$id} 未找到, 15秒后将自动跳转到标签首页.",'', 15,$this->generateUrl('tag')); 
        }

        $conditions = array(
            // 'status' => 'published',
            'tagId' => $tag['id']
        );

        $paginator = new Paginator(
            $this->get('request'),
            $this->getJswidgetService()->searchJswidgetCount($conditions)
            , 30
        );       

        $searchJswidget = $this->getJswidgetService()->searchJswidget(
            $conditions,
            'viewest',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($searchJswidget, 'userId'));


        return $this->render('RedwoodWebBundle:Jswidget:searchList.html.twig', array(
            'jswidgets' => $searchJswidget,
            'users' => $users,
            'paginator' => $paginator,
            'filter' => 'tags'
        ));

    }

    public function latestTagAction(Request $request)
    {
        $tags = $this->getTagService()->searchTags(
            array(),
            'latest',
            0,
            10
        );

        return $this->render('RedwoodWebBundle:Jswidget:jswidget-block.html.twig', array(
            'tags' => $tags,
            'type' => 'tagLatest'
        ));
    }


    public function matchAction(Request $request)
    {   
        $data = array();
        $queryString = $request->query->get('q');

        $tags = $this->getTagService()->getTagByLikeName($queryString);

        foreach ($tags as $tag) {
            $data[] = array('id' => $tag['id'],  'name' => $tag['name'] );
        }

        return $this->createJsonResponse($data);
    }

    
    


   

}