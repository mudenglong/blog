<?php

namespace Redwood\Service\Note\Dao\Impl;

use Redwood\Service\Common\BaseDao;
use Redwood\Service\Note\Dao\JswidgetDao;

class JswidgetDaoImpl extends BaseDao implements JswidgetDao
{
    protected $table = 'jswidget';

	public function getJswidget($id)
	{
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

	public function addJswidget($note)
	{  
        $affected = $this->getConnection()->insert($this->table, $note);
        if ($affected <= 0) {
            throw $this->createDaoException('Insert note error.');
        }
        return $this->getJswidget($this->getConnection()->lastInsertId());
	}

    public function updateJswidget($id, $jswidget)
    {
        var_dump('dddddd');
        var_dump($id);

        $this->getConnection()->update($this->table, $jswidget, array('id' => $id));
        return $this->getJswidget($id);
    }

}