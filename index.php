<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 20:03
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

include VIEW_PATH . "/userView.php";
include SERVICE_PATH . "/userService.php";
include DAO_PATH . "/userDao.php";
include DB_PATH . "/DbMysqlImpl.php";
include HELPER_PATH . "/actionHelper.php";
include IOC_PATH . "/Ioc.php";
include CONTROLLER_PATH. "/userController.php";

$action = $_REQUEST['act'];
if (actionHelper::isAllowedAction('userController', $action)) {
    IOC::register('user', function () {
        $about = new userController();
        $about->setService(new userService(new userDao('user')));
        $about->setView(new view());
        return $about;
    });
    $about = IOC::resolve('user');
    $about->$action($_REQUEST);
} else {
    exit('not allowed action');
}