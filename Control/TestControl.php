<?php
/**
 * Created by PhpStorm.
 * User: Liu Liu
 */
require_once '../Extensions/ImageMerge.php';
header("Content-Type: text/html;charset=utf-8");
Class TestControl
{
    public function Test(){
        echo "da";
        ImageMerge::MergePic();
    }
}