<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 20:03
 */
include "boot.php";
include "di/di.php";
$action = $_REQUEST['act'] ? $_REQUEST['act'] : "";
if (actionHelper::isAllowedAction('userController', $action)) {
    IOC::register($controller, function () {
        $di = new Container();
        $about = $di->getInstance('userController');
        $about->setService($di->getInstance('userService'));
        $about->setView($di->getInstance('userView'));
        return $about;
    });
    $about = IOC::resolve($controller);
    $about->$action($_REQUEST);
} else {
    exit('not allowed action');
}