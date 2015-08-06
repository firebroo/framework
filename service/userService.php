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

    public function __construct(userDao $dao)
    {
        $this->userDao = $dao;
    }

    public function saveUser($request)
    {
        $id = $request['id'];
        $name = $request['name'];
        $sex = $request['sex'];
        return $this->userDao->userInsert($id, $name, $sex) ? "save customer information successful" : "user already exist!";
    }

    public function deleteUser($request)
    {
        $id = $request['id'];
        return $this->userDao->userDelete($id) ? "delete customer information successful" : "user not exist, delete fail";
    }

    public function updateUser($request)
    {
        $id = $request['id'];
        $name = $request['name'];
        $sex = $request['sex'];
        return $this->userDao->userUpdate($name, $id, $sex) ? "update customer information successful" : "user not exist, update fail";
    }

    public function selectUser($request) {
        $id = $request['id'];
        $user =  $this->userDao->userSelect($id);
        return $user? $user:"user not exist";
    }
}
