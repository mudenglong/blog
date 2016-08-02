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

    public function deleteJswidget($id)
    {
        return $this->getConnection()->delete($this->table, array('id' => $id));
    }

    public function updateJswidget($id, $jswidget)
    {

        $this->getConnection()->update($this->table, $jswidget, array('id' => $id));
        return $this->getJswidget($id);
    }

    public function searchJswidgetCount(array $conditions){
        $builder = $this->createJswidgetQueryBuilder($conditions)
            ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchJswidget($conditions, $orderBy, $start, $limit)
    {
        $builder = $this->createJswidgetQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);

        return $builder->execute()->fetchAll() ? : array();  
    }

    private function createJswidgetQueryBuilder($conditions)
    {
        if(isset($conditions['keywordType'])) {
            $conditions[$conditions['keywordType']]=$conditions['keyword'];
            unset($conditions['keywordType']);
            unset($conditions['keyword']);
        }

        if (isset($conditions['title'])) {
            $conditions['title'] = "%{$conditions['title']}%";
        }

        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->table, 'jswidget')
            ->andWhere('title LIKE :title')
            ->andWhere('userId = :userId');
    }




}