<?php

namespace Redwood\Service\Taxonomy\Dao\Impl;

use Redwood\Service\Common\BaseDao;
use Redwood\Service\Taxonomy\Dao\TagDao;

class TagDaoImpl extends BaseDao implements TagDao
{
    protected $table = 'tag';

	public function getTag($id)
	{
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

    public function getTagByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = ? LIMIT 1";
        return $this->getConnection()->fetchAssoc($sql, array($name));
    }

    public function getTagByLikeName($name)
    {
        $name = "%{$name}%";
        $sql  = "SELECT * FROM {$this->table} WHERE name LIKE ?";
        return $this->getConnection()->fetchAll($sql, array($name));
    }

    public function getTagsByNames(array $names)
    {
        $conditions = array();
        if (empty($names)) {
            return array();
        }else{
            $conditions['names'] = $names;
        }

        $builder = $this->createTagQueryBuilder($conditions)
            ->select('*');

        return $builder->execute()->fetchAll() ? : array();
    }

    public function getTagsByIds(array $ids)
    {
        $conditions = array();
        if (empty($ids)) {
            return array();
        }else{
            $conditions['tagIds'] = $ids;
        }

        $builder = $this->createTagQueryBuilder($conditions)
            ->select('*');

        return $builder->execute()->fetchAll() ? : array();
    }



    public function addTag($tag)
	{  
        $affected = $this->getConnection()->insert($this->table, $tag);
        if ($affected <= 0) {
            throw $this->createDaoException('Insert tag error.');
        }
        return $this->getTag($this->getConnection()->lastInsertId());
	} 

    public function deleteTag($id)
    {
        return $this->getConnection()->delete($this->table, array('id' => $id));
    }

    public function updateTag($id, $tag)
    {

        $this->getConnection()->update($this->table, $tag, array('id' => $id));
        return $this->getTag($id);
    }

    public function searchTagCount(array $conditions){
        $builder = $this->createTagQueryBuilder($conditions)
            ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchTags($conditions, $orderBy, $start, $limit)
    {
        $builder = $this->createTagQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);

        return $builder->execute()->fetchAll() ? : array();  
    }

    private function createTagQueryBuilder($conditions)
    {
        if(isset($conditions['keywordType'])) {
            $conditions[$conditions['keywordType']]=$conditions['keyword'];
            unset($conditions['keywordType']);
            unset($conditions['keyword']);
        }

        if (isset($conditions['name'])) {
            $conditions['name'] = "%{$conditions['name']}%";
        }

        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->table, 'tag')
            ->andWhere('name LIKE :name')
            ->andWhere('id IN (:tagIds)')
            ->andWhere('name IN (:names)')
            ->andWhere('id = :id');
    }




}