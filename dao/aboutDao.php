<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 23:28
 */
include(dirname(dirname(__FILE__)) . "/core/mysqlImpl.php");
class aboutDao {
    private static $Db;
    public function __construct() {
        aboutDao::$Db = DbCore::getInstance();
    }
    public static function aboutSelect($id) {
        $sql = "select username from USER WHERE  id=".$id;
        return aboutDao::$Db->select($sql);
    }
}