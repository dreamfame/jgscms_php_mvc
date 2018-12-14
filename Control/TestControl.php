<?php
/**
 * Created by PhpStorm.
 * User: Liu Liu
 * Date: 2018/12/14
 * Time: 15:22
 */
require_once '../DataBaseHandle/AdminServer.php';

class TestControl
{
    public function Test(){
        $re = array('state'=>'0','content'=>'添加失败');
        echo $re['content'];
    }
}