<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 20:41
 */
include(dirname(dirname(__FILE__)) . '/boot.php');
include VIEW_PATH . "/aboutView.php";
include SERVICE_PATH . "/aboutService.php";
include HELPER_PATH . "/actionHelper.php";
include IOC_PATH . "/Ioc.php";
$action = $_REQUEST['act'];
if (actionHelper::isAllowedAction('aboutController', $action)) {
    IOC::register('about', function () use ($service, $view) {
        $about = new aboutController();
        $about->setService($service);
        $about->setView($view);
        return $about;
    });
    $about = IOC::resolve('about');
    $about->index($_REQUEST);
} else {
    exit('not allowed action');
}

class aboutController
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

    public function index(array $request)
    {
        $resultModel = $this->service->show($request);
        $this->view->display($resultModel);
    }
}


