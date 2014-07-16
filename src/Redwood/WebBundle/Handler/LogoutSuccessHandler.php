<?php
 
namespace Redwood\WebBundle\Handler;

use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Redwood\Service\Common\ServiceKernel;

class LogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
	public function onLogoutSuccess(Request $request)
	{
		return parent::onLogoutSuccess($request);
	}
}