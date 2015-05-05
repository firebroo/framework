<?php
/**
 * Created by PhpStorm.
 * User: beibei
 * Date: 2015/5/5
 * Time: 17:26
 */

/**
 * Created by PhpStorm.
 * User: beibei
 * Date: 2015/5/5
 * Time: 8:51
 */
interface DbProviderInterface
{
    public static function getInstance($config = null);

    public static function unsetInstance();



    public function insert($tableName, array $columns);

    public function update($tableName, array $columns, array $where = null);


    public function delete($tableName, $where = null);

    public function select($columns = '*');

    public function from($tableName);

    public function join($tableName, $on);

    public function leftJoin($tableName, $on);

    public function rightJoin($tableName, $on);

    public function getJoinStr();

    public function where(array $conditions);

    public function getWhere();

    public function having(array $conditions);

    public function getHaving();

    public function group($group);

    public function order($order);

    public function limit($limit);

    public function offset($offset);

    public function buildQuery();

    public function _unsetSqlQuery();

    public function fetchAll($tableName, $columns = '*', array $where = array(), $order = null, $limit = null, $offset = null);

    public function fetchRow($tableName, $columns = '*', array $where = array(), $order = null);

    public function fetchCount($tableName, $columns = '*', array $where = array());

    public function query($sql = null);

    public function queryAll();

    public function queryRow();

}