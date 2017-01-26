<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;
use Redwood\Common\ArrayToolkit;
use Redwood\WebBundle\Form\JswidgetForm;

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
        $tagsArr = $this->getTagService()->getTagsByIds($jswidget['tags']);

        $this->getJswidgetService()->waveJswidget($id, 'views', +1);

        return $this->render('RedwoodWebBundle:Jswidget:content.html.twig', array(
            'jswidget' => $jswidget,
            'tags' => $tagsArr,
            'author'   => $this->getUserService()->getUser($jswidget['userId'])
        ));
    } 


    public function latestJswidgetAction(Request $request)
    {

        $jswidgets = $this->getJswidgetService()->searchJswidget(array(), 'latest', 0, 10);
        return $this->render('RedwoodWebBundle:Jswidget:jswidget-block.html.twig', array(
            'jswidgets' => $jswidgets,
            'type' => 'latest'
        ));
    }

    public function viewestJswidgetAction(Request $request)
    {

        $jswidgets = $this->getJswidgetService()->searchJswidget(array(), 'viewest', 0, 10);
        return $this->render('RedwoodWebBundle:Jswidget:jswidget-block.html.twig', array(
            'jswidgets' => $jswidgets,
            'type' => 'viewest'
        ));
    }

    public function editAction(Request $request, $id) 
    {   
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return $this->createMessageResponse('info', "你好像忘了登录哦？ 10秒后将自动跳转到登录页面.",'', 10,$this->generateUrl('login')); 
        }
        
        $jswidget = $this->getJswidgetService()->getJswidget($id);
        if ($user['id'] != $jswidget['userId']) {
            return $this->createMessageResponse('info', "您不是原作者, 10秒后将自动跳转到展示页.",'', 10,$this->generateUrl('jswidget_show'));
        }

        if(empty($jswidget)){ 
            return $this->createMessageResponse('info', "非常抱歉，组件id:{$id} 未找到, 15秒后将自动跳转到组件首页.",'', 15,$this->generateUrl('jswidget_show')); 
        }

        if ($request->getMethod() == 'POST') {
              
            try {
                $updateJswidget = $request->request->all();

                $jswidget = $this->getJswidgetService()->updateJswidget($jswidget['id'], $updateJswidget);

                // 提交成功跳转到 组件页面
                return $this->redirect($this->generateUrl('jswidget_content', array(
                    'id' => $jswidget['id']
                )));

            } catch (\Exception $e) {
                return $this->createMessageResponse('error', '更新组件出错');
            }

        }

        $tagsArr = $this->getTagService()->getTagsByIds($jswidget['tags']);
        $tags = ArrayToolkit::column($tagsArr, 'name');
        $tags = join($tags, ',');

        return $this->render('RedwoodWebBundle:Jswidget:create.html.twig', array(
            'user' => $user,
            'tags' => $tags,
            'jswidget' => $jswidget
        ));
    }

    // 使用手册
    public function manualAction(Request $request) {
        
        return $this->render('RedwoodWebBundle:Jswidget:manual.html.twig');
    }

    // markdown 使用手册
    public function markdownRulesAction()
    {
        return $this->render('RedwoodWebBundle:Jswidget:markdownRule.html.twig');
    }

    // markdown 使用手册
    public function feedbackAction()
    {
        return $this->render('RedwoodWebBundle:Jswidget:feedback.html.twig');
    }

    // 处理数据同下面的searchjsonAction, 渲染不同
    public function searchAction(Request $request, $filter) {

        $jswidgets = $paginator = null; 
        $data = array();
        $data['widgets'] = array();
        $sort = 'latest';
        $conditions = array();
        // 所有兼容性compatible 字段
        $compatibleArr = array( 'unsure','pc6','all', 'pc7','mobile');
        // 所有type
        $typeArr = array( 'js','css');

        if ($filter == 'normal') {
            $keywords = $request->query->get('q');
            $keywords = $this->filterKeyWord(trim($keywords)); 
            $conditions = array(
                'title'      => $keywords
            );
        }elseif($filter == 'latest'){
            $conditions = array();
        }elseif ($filter == 'viewest') {
            $sort = 'viewest';
        }elseif(in_array($filter, $compatibleArr)){
            $conditions = array(
                'compatible'      => $filter
            );
            $sort = 'viewest';
        }elseif(in_array($filter, $typeArr)){
            $conditions = array(
                'type'      => $filter
            );
        }

        $res = $this->searchNormal($conditions, $sort);

        return $this->render('RedwoodWebBundle:Jswidget:searchList.html.twig', array(
            'jswidgets' => $res['jswidgets'],
            'users' => $res['users'],
            'paginator' => $res['paginator'],
            'filter' => $filter
        ));
    }

    protected function searchNormal($conditions, $sort = 'latest')
    {
        $paginator = new Paginator(
            $this->get('request'),
            $this->getJswidgetService()->searchJswidgetCount($conditions),
            30
        );
        $jswidgets = $this->getJswidgetService()->searchJswidget(
            $conditions,
            $sort,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($jswidgets, 'userId'));

        return array(
                'paginator' => $paginator, 
                'jswidgets' => $jswidgets, 
                'users' => $users 
            );
    }

    // 处理数据同上面的searchAction, 渲染不同
    public function searchjsonAction(Request $request) {
        $jswidgets = $paginator = null; 
        $currentUser = $this->getCurrentUser();
        $data = array();
        $data['widgets'] = array();

        $keywords = $request->query->get('q');
     
        $keywords = $this->filterKeyWord(trim($keywords)); 

        $conditions = array(
            'title'      => $keywords
        );

        $res = $this->searchNormal($conditions);
        $paginator = $res['paginator'];
        $jswidgets = $res['jswidgets'];
        $users = $res['users'];

        $data['status'] = 'success';
        $data['pages'] = $paginator->getOffsetCount()+1;
        $data['terms'] = $keywords;
        $data['count'] = $paginator->getPerPageCount();
        foreach ($jswidgets as $jswidget) {
            $data['widgets'][] = array(
                    'id' => $jswidget['id'],  
                    'title' => $jswidget['title'],  
                    'views' => $jswidget['views'],  
                    'compatible' => $jswidget['compatible'],  
                    'type' => $jswidget['type'],  
                    'createTime' => $jswidget['createTime'],  
                    'description' => $jswidget['description'],  
                    'username' => $users[$jswidget['userId']]['username'] 
                    );
        }

        return $this->createJsonResponse($data);
    }

    public function listAction(Request $request) {
        $fields = $request->query->all();
        $user = $this->getCurrentUser();

        if (!$user) {
            return $this->redirect($this->generateUrl('jswidget_show'));
        }

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
            'latest',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('RedwoodWebBundle:Jswidget:list.html.twig', array(
            'jswidgets' => $jswidget,
            'paginator' => $paginator
        ));

    }

    public function deleteAction(Request $request, $id) {

        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return $this->createMessageResponse('info', "你好像忘了登录哦？ 10秒后将自动跳转到登录页面.",'', 10,$this->generateUrl('login')); 
        }
        
        $jswidget = $this->getJswidgetService()->getJswidget($id);
        if ($user['id'] != $jswidget['userId']) {
            return $this->createMessageResponse('info', "您不是原作者, 10秒后将自动跳转到展示页.",'', 10,$this->generateUrl('jswidget_show'));
        }

        $this->getJswidgetService()->deleteJswidget($id);
        return $this->createJsonResponse(true);
    }


    public function createAction(Request $request) 
    {   
    	$user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            return $this->createMessageResponse('info', "你好像忘了登录哦？ 10秒后将自动跳转到登录页面.",'', 10,$this->generateUrl('login')); 
        }

        if ($request->getMethod() == 'POST') {
            // try {
                $jswidget = $request->request->all();
                $jswidget = $this->getJswidgetService()->createJswidget($jswidget);

                // 提交成功跳转到 组件页面
                return $this->redirect($this->generateUrl('jswidget_content', array(
                    'id' => $jswidget['id']
                )));

            // } catch (\Exception $e) {
            //     return $this->createMessageResponse('error', '创建组件出错');
            // }

        }

        return $this->render('RedwoodWebBundle:Jswidget:create.html.twig', array(
            'user' => $user,
            'tags' => array()
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


    // @todo 已作废
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
