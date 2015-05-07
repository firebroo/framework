<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 20:01
 */
class aboutView {
    public function display(array $arr) {
        foreach($arr as $key => $value) {
            echo $value['id']."\t".$value['name']."<br/>";
        }

    }
}
$view = new aboutView();
