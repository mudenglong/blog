<?php 

namespace Redwood\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;

class TagController extends BaseController
{

    public function indexAction(Request $request) 
    {      

        $conditions = array('from' => 'widget');
        $total = $this->getTagService()->searchTagCount($conditions);
        $paginator = new Paginator($request, $total, 20);

        $tags = $this->getTagService()->searchTags(
                $conditions, 
                array('createdTime', 'DESC'),
                $paginator->getOffsetCount(), 
                $paginator->getPerPageCount());

        return $this->render('RedwoodAdminBundle:Tag:index.html.twig', array(
            'tags' => $tags,
            'paginator' => $paginator
        ));

    }

    public function checkNameAction()
    {
        if (1) {
            $response = array('success' => true, 'message' => '');
        } else {
            $response = array('success' => false, 'message' => '标签已存在');
        }
        return $this->createJsonResponse($response);
    }

    public function createAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $tag = $request->request->all();
            $tag['origin'] = 'widget';

            $tag = $this->getTagService()->createTag($tag);
            return $this->render('RedwoodAdminBundle:Tag:list-tr.html.twig', array(
                'tag' => $tag
            ));
        }

        return $this->render('RedwoodAdminBundle:Tag:tag-modal.html.twig', array(
            'tag' => array('id' => 0, 'name' => '')
        ));


            // $user = $this->getCurrentUser();
            // $form = $this->creatJswidgetForm();
            // if ($request->getMethod() == 'POST') {
            //     $form->bind($request);

            //     if ($form->isValid()) {
                  
            //         try {
            //             $jswidget = $form->getData();
            //             $jswidget = $this->getJswidgetService()->createJswidget($jswidget);
            //             // 提交成功跳转到 组件页面
            //             return $this->redirect($this->generateUrl('jswidget_content', array(
            //                 'id' => $jswidget['id']
            //             )));

            //         } catch (\Exception $e) {
            //             return $this->createMessageResponse('error', '创建组件出错');
            //         }

            //     }
            // }
    }


    public function deleteAction($id)
    {
        # code...
    }
    //     public function deleteAction(Request $request, $id) {
    //    $this->getJswidgetService()->deleteJswidget($id);
    //    return $this->createJsonResponse(true);
    // }



    public function updateAction(Request $request, $id)
    {
        $tag = $this->getTagService()->getTag($id);

        if (empty($tag)) {
            throw $this->createNotFoundException("标签id#{$id}不存在");
        }

        if ('POST' == $request->getMethod()) {
            $tag = $this->getTagService()->updateTag($id, $request->request->all());
            return $this->render('RedwoodAdminBundle:Tag:list-tr.html.twig', array(
                'tag' => $tag
            ));
        }

        return $this->render('RedwoodAdminBundle:Tag:tag-modal.html.twig', array(
            'tag' => $tag
        ));
    }

}