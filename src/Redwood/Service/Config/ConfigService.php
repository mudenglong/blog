<?php

namespace Redwood\Service\Config;


/**
 *
 *  组件有关的service
 *
 */
interface ConfigService
{
    public function createConfig(array $fields);
    public function deleteConfig($cid, $uid);
    public function listConfig($uid);
}
