<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/6
 * Time: 15:32
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__));
define('CONTROLLER_PATH', ROOT_PATH . DS . 'controller');
define('DB_PATH', ROOT_PATH . DS . 'db');
define('DAO_PATH', ROOT_PATH . DS . 'dao');
define('VIEW_PATH', ROOT_PATH . DS . 'view');
define('SERVICE_PATH', ROOT_PATH . DS . 'service');
define('HELPER_PATH', ROOT_PATH . DS . 'helper');
define('IOC_PATH', ROOT_PATH . DS . 'ioc');

$controller = $_GET['controller'] ? $_GET['controller'] : "";
$action = $_REQUEST['act'] ? $_REQUEST['act'] : "";
if (!file_exists(CONTROLLER_PATH . DS . $controller . "Controller.php")) {
    exit("Controller not exist");
}
include CONTROLLER_PATH . DS . $controller . "Controller.php";
include VIEW_PATH . DS . $controller . "View.php";
include SERVICE_PATH . DS . $controller . "Service.php";
include DAO_PATH . DS . $controller . "Dao.php";
include DB_PATH . "/DbMysqlImpl.php";
include HELPER_PATH . "/actionHelper.php";
include IOC_PATH . "/Ioc.php";




