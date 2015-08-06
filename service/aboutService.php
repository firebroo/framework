<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 23:19
 */
include(dirname(dirname(__FILE__)) . '/dao/aboutDao.php');
class aboutService
{
    private $aboutDao;
    public function __construct($dao) {
        $this->aboutDao = $dao;
    }
    public function show($request)
    {
        $name = $request['name'];
        $result = $this->aboutDao->aboutSelect($name);
        return $result;
    }

    public function saveUser($request) {
        $id = $request['id'];
        $name = $request['name'];
        return $this->aboutDao->aboutInsert($id, $name)?"save Customer information successful":"error";
    }
}

$service = new aboutService(new aboutDao('user'));

