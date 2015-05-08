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
        self::$Db = DbMysqlImpl::getInstance();
        self::$table = $table;
    }
    public static function aboutSelect($name) {
        return self::$Db->select()->from(aboutDao::$table)->where(array('name ='=>$name))->queryAll();
    }
}