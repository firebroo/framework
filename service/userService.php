<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/6
 * Time: 10:22
 */
class userService
{
    private $userDao;

    public function __construct($dao)
    {
        $this->userDao = $dao;
    }

    public function saveUser($request)
    {
        $id = $request['id'];
        $name = $request['name'];
        return $this->userDao->userInsert($id, $name) ? "save customer information successful" : "error";
    }

    public  function deleteUser($request) {
        $id = $request['id'];
        return $this->userDao->userDelete($id)? "delete customer information successful" : "error";
    }
}
