<?php

namespace Redwood\Service\Config\Dao\Impl;

use Redwood\Service\Common\BaseDao;
use Redwood\Service\Config\Dao\ConfigDao;


/**
 *
 * 绘图表配置的DAO
 *
 */

class ConfigDaoImpl extends BaseDao implements ConfigDao
{

    protected $table = 'quotation_chart';

    public function createConfig(array $fields)
    {
        $affected = $this->getConnection()->insert($this->table, $fields);
        if ($affected <=0 ) {
            throw $this->createDaoException('Insert chart config error.');
        }
        return $this->getConfig($this->getConnection()->lastInsertId());
    }

    public function getConfig($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function updateConfig($cid)
    {
    }

    public function deleteConfig($cid, $uid)
    {
        return $this->getConnection()->update($this->table, array('isvalid' => 0), array('id' => $cid, 'userId' => $uid));
    }

    public function listConfig($uid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE userId = ? AND isvalid = 1";
        return  $this->getConnection()->fetchAll($sql, array($uid)) ? : null;
    }


}
