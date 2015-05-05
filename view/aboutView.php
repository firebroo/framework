<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 20:01
 */
class aboutView {
    public function display(array $arr) {
        echo $arr['username'];
    }
}
$view = new aboutView();