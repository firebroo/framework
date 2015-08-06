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
    private static $table = 'user';

    public function __construct()
    {
        static::$Db = DbMysqlImpl::getInstance();
    }

    public function userSelect($id)
    {
        return static::$Db->select()->from(static::$table)->where(array('id =' => $id))->queryAll();
    }

    public function userInsert($id, $name, $sex)
    {
        $count = static::$Db->fetchCount('user', '*', array('id = ' => $id));
        if (!$count) {
            static::$Db->insert(static::$table, array("id" => $id, "name" => $name, "sex" => $sex));
            return true;
        }
        return false;
    }

    public function userDelete($id)
    {
        $count = static::$Db->fetchCount('user', '*', array('id = ' => $id));
        if ($count > 0) {
            static::$Db->delete(static::$table, array('id = ' => $id));
            return true;
        }
        return false;
    }

    public function userUpdate($name, $id, $sex)
    {
        $count = static::$Db->fetchCount('user', '*', array('id = ' => $id));
        if ($count > 0) {
            static::$Db->update(static::$table, array('name' => $name, 'sex' => $sex), array('id =' => $id));
            return true;
        }
        return false;
    }
}