<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;

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
            return $this->createMessageResponse('info', "非常抱歉，组件id:{$id}未找到, 10秒后将跳转到组件首页.",'',10,$this->generateUrl('jswidget_show')); 
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

    private function creatJswidgetForm($data = array())
    {
        return $this->createNamedFormBuilder('jswidget', $data)
            ->add('title', 'text')
            ->add('description', 'text')
            ->add('url', 'text')
            ->add('content', 'textarea')
            ->getForm();
    }


   

}