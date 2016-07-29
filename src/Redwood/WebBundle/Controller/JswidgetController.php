<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class JswidgetController extends BaseController
{

    public function indexAction() 
    {   
        $user = $this->getCurrentUser();
        var_dump($user);
        return $this->render('RedwoodWebBundle:Jswidget:index.html.twig', array(
            'user' => $user,
        ));
    }

    public function showAction(Request $request, $id) 
    {   
        $user = $this->getCurrentUser();
        $jswidget = $this->getJswidgetService()->getJswidget($id);
        return $this->render('RedwoodWebBundle:Jswidget:content.html.twig', array(
            'user'     => $user,
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