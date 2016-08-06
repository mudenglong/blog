<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;
use Redwood\Common\ArrayToolkit;

class JswidgetController extends BaseController
{

    public function indexAction() 
    {   
        $user = $this->getCurrentUser();
        return $this->render('RedwoodWebBundle:Jswidget:index.html.twig', array(
            'user' => $user,
        ));
    }

    public function showAction(Request $request, $id) 
    {   
        $jswidget = $this->getJswidgetService()->getJswidget($id);

        if(!$jswidget){ 
            return $this->createMessageResponse('info', "非常抱歉，组件id:{$id} 未找到, 15秒后将自动跳转到组件首页.",'', 15,$this->generateUrl('jswidget_show')); 
        }

        return $this->render('RedwoodWebBundle:Jswidget:content.html.twig', array(
            'jswidget' => $jswidget,
            'author'   => $this->getUserService()->getUser($jswidget['userId'])
        ));
    } 

    public function editAction(Request $request, $id) 
    {   
        $user = $this->getCurrentUser();
        $jswidget = $this->getJswidgetService()->getJswidget($id);

        $form = $this->creatJswidgetForm($jswidget);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
              
                try {
                    $jswidget = $form->getData();
                    $jswidget = $this->getJswidgetService()->updateJswidget($jswidget);

                    // 提交成功跳转到 组件页面
                    return $this->redirect($this->generateUrl('jswidget_content', array(
                        'id' => $jswidget['id']
                    )));

                } catch (\Exception $e) {
                    return $this->createMessageResponse('error', '创建组件出错');
                }

            }
        }

        return $this->render('RedwoodWebBundle:Jswidget:create.html.twig', array(
            'user' => $user,
            'jswidget' => $jswidget,
            'form' => $form->createView()
        ));
    }

    public function searchAction(Request $request) {
        $jswidgets = $paginator = null; 
        $currentUser = $this->getCurrentUser();
        $data = array();

        $keywords = $request->query->get('q');
     
        $keywords = $this->filterKeyWord(trim($keywords)); 

        $conditions = array(
            'title'      => $keywords
        );

        $paginator = new Paginator(
            $this->get('request'),
            $this->getJswidgetService()->searchJswidgetCount($conditions),
            20
        );

        $jswidgets = $this->getJswidgetService()->searchJswidget(
            $conditions,
            array('createTime', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($jswidgets, 'userId'));

        // foreach ($jswidgets as $jswidget) {
        //     $data[] = array(
        //             'id' => $jswidget['id'],  
        //             'title' => $jswidget['title'],  
        //             'view' => $jswidget['view'],  
        //             'admire' => $jswidget['admire'],  
        //             'createTime' => $jswidget['createTime'],  
        //             'description' => $jswidget['description'],  
        //             'view' => $jswidget['view'],  
        //             'username' => $users[$jswidget['userId']]['username'] 
        //             );
        // }

        // return $this->createJsonResponse($data);

        return $this->render('RedwoodWebBundle:Jswidget:searchList.html.twig', array(
            'jswidgets' => $jswidgets,
            'users' => $users,
            'paginator' => $paginator,
        ));
    }

    public function listAction(Request $request) {
        $fields = $request->query->all();
        $user = $this->getCurrentUser();

        $conditions = array(
            'userId' => $user['id'],
            'title' => ''
        );

        if(!empty($fields)){
            $conditions = $fields;
        }

        $paginator = new Paginator(
            $this->get('request'),
            $this->getJswidgetService()->searchJswidgetCount($conditions),
            20
        );

        $jswidget = $this->getJswidgetService()->searchJswidget(
            $conditions,
            array('createTime', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('RedwoodWebBundle:Jswidget:list.html.twig', array(
            'jswidgets' => $jswidget,
            'paginator' => $paginator,
        ));

    }

    public function deleteAction(Request $request, $id) {
       $this->getJswidgetService()->deleteJswidget($id);
       return $this->createJsonResponse(true);
    }

    public function createAction(Request $request) 
    {   
    	$user = $this->getCurrentUser();
        $form = $this->creatJswidgetForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
              
                try {
                    $jswidget = $form->getData();
                    $jswidget = $this->getJswidgetService()->createJswidget($jswidget);
                    // 提交成功跳转到 组件页面
                    return $this->redirect($this->generateUrl('jswidget_content', array(
                        'id' => $jswidget['id']
                    )));

                } catch (\Exception $e) {
                    return $this->createMessageResponse('error', '创建组件出错');
                }

            }
        }

        return $this->render('RedwoodWebBundle:Jswidget:create.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    private function filterKeyWord($keyword)
    {
        $keyword = str_replace('<', '', $keyword);
        $keyword = str_replace('>', '', $keyword);
        $keyword = str_replace("'", '', $keyword);
        $keyword = str_replace("\"", '', $keyword);
        $keyword = str_replace('=', '', $keyword);
        $keyword = str_replace('&', '', $keyword);
        $keyword = str_replace('/', '', $keyword);
        return $keyword;
    }


    private function creatJswidgetForm($data = array())
    {
        return $this->createNamedFormBuilder('jswidget', $data)
            ->add('title', 'text')
            ->add('description', 'text')
            ->add('url', 'text')
            ->add('iframeUrl', 'text')
            ->add('content', 'textarea')
            ->getForm();
    }


   

}