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
    public function __construct() {
        aboutDao::$Db = DbMysqlImpl::getInstance();
    }
    public static function aboutSelect($name) {
        return aboutDao::$Db->select()->from('user')->where(array('name ='=>$name))->queryAll();
    }
}