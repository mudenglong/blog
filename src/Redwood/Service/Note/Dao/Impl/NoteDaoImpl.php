<?php

namespace Redwood\Service\Note\Dao\Impl;

use Redwood\Service\Common\BaseDao;
use Redwood\Service\Note\Dao\NoteDao;

class NoteDaoImpl extends BaseDao implements NoteDao
{
    protected $table = 'note';

	public function getNote($id)
	{
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

	public function addNote($note)
	{  
        $affected = $this->getConnection()->insert($this->table, $note);
        if ($affected <= 0) {
            throw $this->createDaoException('Insert note error.');
        }
        var_dump('now begin at NoteDaoImpl.php : editNote function');
        return $this->getNote($this->getConnection()->lastInsertId());
	}

}