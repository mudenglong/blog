<?php
namespace Redwood\Service\Common;

use PDO,
    Redwood\Common\DaoException;

abstract class BaseDao
{
    protected $connection;

    protected $table = null;

    protected $primaryKey = 'id';

    // UPDATE enterprise SET viewedNum = viewedNum + ? WHERE id = ?
    // 在现在的数据上增加值
    protected function wave ($id, $fields) 
    {
        $sql = "UPDATE {$this->getTable()} SET ";
        $fieldStmts = array();
        foreach (array_keys($fields) as $field) {
            $fieldStmts[] = "{$field} = {$field} + ? ";
        }
        $sql .= join(',', $fieldStmts);
        $sql .= "WHERE id = ?";

        $params = array_merge(array_values($fields), array($id));
        return $this->getConnection()->executeUpdate($sql, $params);
    }

    public function getTable()
    {
        if($this->table){
            return $this->table;
        }else{
            return self::TABLENAME;
        }
    }

    public function getConnection ()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    protected function createDaoException($message = null, $code = 0) 
    {
        return new DaoException($message, $code);
    }

    protected function createDynamicQueryBuilder($conditions)
    {
        return new DynamicQueryBuilder($this->getConnection(), $conditions);
    }

}