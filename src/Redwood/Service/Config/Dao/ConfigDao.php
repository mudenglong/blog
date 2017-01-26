<?php

namespace Redwood\Service\Config\Dao;

interface ConfigDao
{
    public function createConfig(array $fields);
    public function updateConfig($cid);
    public function deleteConfig($cid, $uid);
    public function listConfig($uid);
}
