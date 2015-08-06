<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 23:28
 */
include(dirname(dirname(__FILE__)) . "/db/DbMysqlImpl.php");
class aboutDao {
    private static $Db;
    private static $table;
    public function __construct($table) {
        static::$Db = DbMysqlImpl::getInstance();
        static::$table = $table;
    }
    public static function aboutSelect($name) {
        return self::$Db->select()->from(static::$table)->where(array('name ='=>$name))->queryAll();
    }

    public static function aboutInsert($id, $name) {
        static::$Db->insert('user', array("id"=>$id, "name"=>$name));
        return true;
    }
}