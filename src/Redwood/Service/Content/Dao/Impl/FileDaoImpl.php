<?php

namespace Redwood\Service\Content\Dao\Impl;

use Redwood\Service\Common\BaseDao;
use Redwood\Service\Content\Dao\FileDao;

class FileDaoImpl extends BaseDao implements FileDao
{
    protected $table = 'file';

	public function getFile($id)
	{
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

	public function findFiles($start, $limit)
	{
        $this->filterStartLimit($start, $limit);
		$sql = "SELECT * FROM {$this->table} ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
        return $this->getConnection()->fetchAll($sql);
	}

	public function findFileCount()
	{
		$sql = "SELECT COUNT(*) FROM {$this->table}";
        return $this->getConnection()->fetchColumn($sql);
	}

	public function addFile($file)
	{
        if ($this->getConnection()->insert($this->table, $file) <= 0) {
            throw $this->createDaoException('Insert file error.');
        }
        return $this->getFile($this->getConnection()->lastInsertId());
	}

	public function deleteFile($id)
	{
		return $this->getConnection()->delete($this->table, array('id' => $id));
	}

}