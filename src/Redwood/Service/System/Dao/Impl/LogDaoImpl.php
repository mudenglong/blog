<?php

namespace Redwood\Service\System\Dao\Impl;

use Redwood\Service\Common\BaseDao;
use Redwood\Service\System\Dao\LogDao;

class LogDaoImpl extends BaseDao implements LogDao 
{
    protected $table = 'log';

    public function addLog($log)
    {
        $affected = $this->getConnection()->insert($this->table, $log);
        if ($affected <= 0) {
            throw $this->createDaoException('Insert log error.');
        }
    }

  
}