<?php
/**
 * Created by PhpStorm.
 * User: Liu Liu
 */
require_once '../Model/Photo.php';

class TestControl
{
    public function Test(){
        $i = 1;
        $p = new Photo();
        $a = "img".$i;
        $p->$a = "dsadasda";
        echo $p->img1;
    }
}