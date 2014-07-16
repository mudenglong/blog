<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Redwood\Service\Common\ServiceKernel;
use Redwood\Service\User\CurrentUser;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
//移动下面这段到末尾 增加boot（） 方法???
// $response = $kernel->handle($request);
// $response->send();
// $kernel->terminate($request, $response);

$kernel->boot(); 

// init Class ServiceKernel
$serviceKernel = ServiceKernel::create($kernel->getEnvironment(), $kernel->isDebug());
$serviceKernel->setParameterBag($kernel->getContainer()->getParameterBag());
$serviceKernel->setConnection($kernel->getContainer()->get('database_connection'));

$currentUser = new CurrentUser();
$currentUser->fromArray(array(
    'id' => 0,
    'username' => '游客',
    'currentIp' =>  $request->getClientIp(),
    'roles' => array(),
));
$serviceKernel->setCurrentUser($currentUser);
// END: init Class ServiceKernel

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);