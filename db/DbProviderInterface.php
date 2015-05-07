<?php
/**
 * Created by PhpStorm.
 * User: beibei
 * Date: 2015/5/5
 * Time: 17:26
 */

interface DbProviderInterface
{
    public static function getInstance($config = null);

    public static function unsetInstance();

    public function insert($tableName, array $columns);

    public function update($tableName, array $columns, array $where = null);

    public function delete($tableName, $where = null);

    public function fetchAll($tableName, $columns = '*', array $where = array(), $order = null, $limit = null, $offset = null);

    public function fetchRow($tableName, $columns = '*', array $where = array(), $order = null);

    public function fetchCount($tableName, $columns = '*', array $where = array());

}