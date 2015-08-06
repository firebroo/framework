<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 20:03
 */
include "boot.php";
$action = $_REQUEST['act']?$_REQUEST['act']:"";
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