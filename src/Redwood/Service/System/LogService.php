<?php
namespace Redwood\Service\System;

interface LogService
{
    /**
     * 记录一般日志
     * @param  string $module  模块
     * @param  string $action  操作
     * @param  string $message 记录的详情
     */
    public function info($module, $action, $message);

}