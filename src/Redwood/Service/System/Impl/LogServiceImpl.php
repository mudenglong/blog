<?php
namespace Redwood\Service\System\Impl;

use Redwood\Service\System\LogService;
use Redwood\Service\Common\BaseService;

class LogServiceImpl extends BaseService implements LogService
{   

    public function info($module, $action, $message)
    {
        return $this->addLog('info', $module, $action, $message);
    }

    protected function addLog($level, $module, $action, $message)
    {
        return $this->getLogDao()->addLog(array(
            'module' => $module,
            'action' => $action,
            'message' => $message,
            'userId' => $this->getCurrentUser()->id,
            'ip' => $this->getCurrentUser()->currentIp,
            'createdTime' => time(),
            'level' => $level,
        ));     
    }

    protected function getLogDao()
    {
        return $this->createDao('System.LogDao');
    }   

}