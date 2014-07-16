<?php

namespace Redwood\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;

class UserController extends BaseController
{
	public function indexAction(Request $request)
	{
		$fields = $request->query->all();

		$conditions = array(
			'roles' => '',
			'keywordType' => '',
			'keyword' => ''
		);

		if(!empty($fields)){
            $conditions =$fields;
        }

		$paginator = new Paginator(
			$this->get('request'),
			$this->getUserService()->searchUserCount($conditions),
			10
		);

		$users = $this->getUserService()->searchUsers(
			$conditions,
            array('createdTime', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
		);

		return $this->render('RedwoodAdminBundle:User:index.html.twig', array(
			'users' => $users,
			'paginator' => $paginator,
		));
	}

	public function showAction($id)
	{
		$user = $this->getUserService()->getUser($id);
        return $this->render('RedwoodAdminBundle:User:show-modal.html.twig', array(
            'user' => $user,
        ));
	}

	// @todo 目前没有用
	public function editAction(Request $request, $id)
	{
		$user = $this->getUserService()->getUser($id);
		if($request->getMethod() == 'POST')
		{
			$user = $this->getUserService->updateUserProfile($user['id'], $request->request->all());
			$this-getLogService()->info('user', 'edit', "管理员编辑用户资料 {$user['nickname']} (#{$user['id']})");
			// @todo
			// return $this->redirect($this->generateUrl('settings'));
		}

		return $this->render('RedwoodAdminBundle:User:edit-modal.html.twig', array(
            'user' => $user,
        ));
	}

	public function rolesAction(Request $request, $id)
	{
		if(false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
		{
			throw $this->createAccessDeniedException();
		}

		$user = $this->getUserService()->getUser($id);
		if($request->getMethod() == 'POST')
		{
			$roles = $request->request->get('roles');
			$this->getUserService()->changeUserRoles($user['id'], $roles);
			return $this->redirect($this->generateUrl('admin_user'));
		}

		return $this->render('RedwoodAdminBundle:User:roles-modal.html.twig', array(
			'user' => $user,
		));
	}

	public function lockAction($id)
    {
        $this->getUserService()->lockUser($id);
        return $this->render('RedwoodAdminBundle:User:user-table-tr.html.twig', array(
            'user' => $this->getUserService()->getUser($id),
        ));
    }

    public function unlockAction($id)
    {
        $this->getUserService()->unlockUser($id);
        
        return $this->render('RedwoodAdminBundle:User:user-table-tr.html.twig', array(
            'user' => $this->getUserService()->getUser($id),
        ));
    }

    public function sendPasswordResetEmailAction(Request $request, $id)
    {
        $user = $this->getUserService()->getUser($id);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }
        $siteName = $this->container->getParameter('site_name');
        $token = $this->getUserService()->makeToken('password-reset', $user['id'], strtotime('+1 day'));
        $this->sendEmail(
            $user['email'],
            "重设{$user['username']}在{$siteName}的密码",
            $this->renderView('RedwoodWebBundle:PasswordReset:reset.html.twig', array(
                'user' => $user,
                'token' => $token,
            )), 'text/html'
        );

        $this->getLogService()->info('user', 'send_password_reset', "管理员给用户 ${user['username']}({$user['id']}) 发送密码重置邮件");

        return $this->createJsonResponse(true);
    }

    public function sendEmailVerifyEmailAction(Request $request, $id)
    {
        $user = $this->getUserService()->getUser($id);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }
        $token = $this->getUserService()->makeToken('email-verify', $user['id'], strtotime('+1 day'));
        $this->sendEmail(
            $user['email'],
            "请激活你的帐号，完成注册",
            $this->renderView('RedwoodWebBundle:Register:verify-email.html.twig', array(
                'user' => $user,
                'token' => $token,
            )), 'text/html'
        );

        $this->getLogService()->info('user', 'send_email_verify', "管理员给用户 ${user['username']}({$user['id']}) 发送Email验证邮件");

        return $this->createJsonResponse(true);
    }
}