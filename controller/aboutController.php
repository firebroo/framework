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
$action = $_REQUEST['act'];
if(actionHelper::isAllowedAction('aboutController',$action)) {
    $about = new aboutController($view,$service);
    $about->index($_REQUEST);
}else {
    exit('not allowed action');
}
class aboutController
{
    private $view;
    private $service;

    public function __construct(aboutView $view,aboutService $service)
    {
        $this->view = $view;
        $this->service = $service;
    }

    public function index(array $request)
    {
        $resultModel = $this->service->show($request);
        $this->view->display($resultModel);
    }
}


