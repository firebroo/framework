<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/5
 * Time: 0:25
 */
class actionHelper
{
    public static function isAllowedAction($className, $action)
    {
        $methods = get_class_methods($className);
        if (!in_array($action, $methods)) {
            return false;
        }
        return true;
    }
}