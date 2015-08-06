<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/6
 * Time: 10:22
 */
class userView
{
    public function showUserSave($result)
    {
        echo $result;
    }

    public function showUserDelete($result)
    {
        echo $result;
    }

    public function showUserUpdate($result)
    {
        echo $result;
    }

    public function showUserSelect($result) {
        if (gettype($result) == 'string') {
            echo $result;
        }
        if (gettype($result) == 'array') {
            foreach($result as $key => $user) {
                foreach($user as $key2 => $value) {
                    echo $key2.":\t".$value."</br>";
                }
            }
        }
    }
}
