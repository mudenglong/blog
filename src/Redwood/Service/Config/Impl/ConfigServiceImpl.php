<?php

namespace Redwood\Service\Config\Impl;

use Redwood\Service\Common\BaseService;
use Redwood\Service\Config\ConfigService;

class ConfigServiceImpl extends BaseService implements ConfigService
{
    public function createConfig(array $fields)
    {
        return  $this->getConfigDao()->createConfig($fields); 
    }


    public function createDao($name)
    {
        return $this->getKernel()->createDao($name);
    }

    public function getConfigDao()
    {
        return $this->createDao('Config.ConfigDao');
    }

    public function updateConfig($cid)
    {
            
    }

    public function deleteConfig($cid, $uid)
    {
        return $this->getConfigDao()->DeleteConfig($cid, $uid); 
    }

    public function listConfig($uid)
    {
        return  $this->getConfigDao()->listConfig($uid);
    }

}
