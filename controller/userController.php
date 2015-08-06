<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/6
 * Time: 10:21
 */
/*include(dirname(dirname(__FILE__)) . '/index.php');
include VIEW_PATH . "/userView.php";
include SERVICE_PATH . "/userService.php";
include DAO_PATH . "/userDao.php";
include DB_PATH . "/DbMysqlImpl.php";
include HELPER_PATH . "/actionHelper.php";
include IOC_PATH . "/Ioc.php";
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
}*/

class userController
{
    private $view;
    private $service;

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param mixed $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    public function __construct()
    {
    }

    public function saveUser($request)
    {
        $result = $this->service->saveUser($request);
        $this->view->showUserSave($result);
    }

    public function deleteUser()
    {

    }

    public function updateUser()
    {

    }

    public function selectUser()
    {
    }
}
