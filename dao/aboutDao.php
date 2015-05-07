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
        aboutDao::$Db = DbMysqlImpl::getInstance();
        aboutDao::$table = $table;
    }
    public static function aboutSelect($name) {
        return aboutDao::$Db->select()->from(aboutDao::$table)->where(array('name ='=>$name))->queryAll();
    }
}