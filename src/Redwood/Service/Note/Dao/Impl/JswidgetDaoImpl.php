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

	public function addJswidget($jswidget)
	{  
        $affected = $this->getConnection()->insert($this->table, $jswidget);
        if ($affected <= 0) {
            throw $this->createDaoException('Insert jswidget error.');
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

    public function waveJswidget($id, $field, $diff)
    {
        $fields = array('views');
        // $fields = array('hitNum', 'noteNum');

        if (!in_array($field, $fields)) {
            throw \InvalidArgumentException(sprintf("%s字段不允许增减，只有%s才被允许增减", $field, implode(',', $fields)));
        }
        $sql = "UPDATE {$this->getTable()} SET {$field} = {$field} + ? WHERE id = ? LIMIT 1";

        $result = $this->getConnection()->executeQuery($sql, array($diff, $id));
        return $result;
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

        if (isset($conditions['tagId'])) {
            $tagId = (int) $conditions['tagId'];

            if (!empty($tagId)) {
                $conditions['tagsLike'] = "%|{$conditions['tagId']}|%";
            }

            unset($conditions['tagId']);
        }

        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->table, 'jswidget')
            ->andWhere('title LIKE :title')
            ->andWhere('tags LIKE :tagsLike')
            ->andWhere('compatible = :compatible')
            ->andWhere('type = :type')
            ->andWhere('userId = :userId');
    }




}