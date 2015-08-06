<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/6
 * Time: 10:27
 */
class userDao
{
    private static $Db;
    private static $table;

    public function __construct($table)
    {
        static::$Db = DbMysqlImpl::getInstance();
        static::$table = $table;
    }

    public function userSelect($name)
    {
        return static::$Db->select()->from(static::$table)->where(array('name =' => $name))->queryAll();
    }

    public function userInsert($id, $name)
    {
        static::$Db->insert('user', array("id" => $id, "name" => $name));
        return true;
    }

    public function userDelete($id)
    {
        static::$Db->delete('user', array('id = ' => $id));
        return true;
    }
}