<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/6
 * Time: 10:21
 */
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

    public function __construct(userService $userService, userView $userView)
    {
        $this->service = $userService;
        $this->view = $userView;
    }

    public function saveUser($request)
    {
        $result = $this->service->saveUser($request);
        $this->view->showUserSave($result);
    }

    public function deleteUser($request)
    {
        $result = $this->service->deleteUser($request);
        $this->view->showUserDelete($result);
    }

    public function updateUser($request)
    {
        $result = $this->service->updateUser($request);
        $this->view->showUserUpdate($result);
    }

    public function selectUser($request)
    {
        $result = $this->service->selectUser($request);
        $this->view->showUserSelect($result);
    }
}
