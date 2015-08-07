<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 20:03
 */
include "boot.php";
include "di/di.php";

if (actionHelper::isAllowedAction($controller.'Controller', $action)) {
    /*
    IOC::register($controller, function() use ($controller) {
        $di = new Container();
        $about = $di->getInstance($controller.'Controller');
        $about->setService($di->getInstance($controller.'Service'));
        $about->setView($di->getInstance($controller.'View'));
        return $about;
    });
    $about = IOC::resolve($controller);
    $about->$action($_REQUEST);
    */
    $di = new Container();
    $user = $di->getInstance($controller."Controller");
    //var_dump($user);
    $user->$action($_REQUEST);
} else {
    exit('not allowed action');
}
