<?php
namespace Redwood\WebBundle\Twig\Extension;

class DataDict
{
	private static $dict = array(
		'userRole' => array(
			'ROLE_USER' => '会员',
			'ROLE_EDITOR' => '编辑者',
			'ROLE_ADMIN' => '管理员',
			'ROLE_SUPER_ADMIN' => '超级管理员'
		),
		'userKeyWordType' => array(
			'username' => '昵称',
			'email' => '邮件地址',
			'loginIp' => '登陆IP'
		),
		// 'logLevel' => array(
		// 	'info' => '提示',
		// 	'warning' => '警告',
		// 	'error' => '错误'
		// ),
		// 'logLevel:html' => array(
		// 	'info' => '<span>提示</span>',
		// 	'warning' => '<span class="text-warning">警告</span>',
		// 	'error' => '<span class="text-danger">错误</span>'
		// ),
		// 'userType' => array(
		// 	'default' => '网站注册',
		// 	'weibo' => '微博登录',
		// 	'renren' => '人人连接',
		// 	'qq' => 'QQ登录',
		// 	'douban' => '豆瓣连接'
		// ),
	);

	public static function dict($type)
	{
		return isset(self::$dict[$type]) ? self::$dict[$type] : array();
	}

	public static function text($type, $key)
	{
		if (!isset(self::$dict[$type])) {
			return null;
		}

		if (!isset(self::$dict[$type][$key])) {
			return null;
		}

		return self::$dict[$type][$key];
	}

}