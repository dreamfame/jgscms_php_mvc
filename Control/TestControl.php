<?php
/**
 * Created by PhpStorm.
 * User: Liu Liu
 */
require_once '../Extensions/ImageCompress.php';
header("Content-Type: text/html;charset=utf-8");
Class TestControl
{
    public function Test(){
        $path = '../View/images/scenicImgs';///当前目录
        $handle = opendir($path); //当前目录
        while (false !== ($file = readdir($handle))) { //遍历该php文件所在目录

            list($filesname,$kzm)=explode(".",$file);//获取扩展名

            if($kzm=="gif" or $kzm=="jpg" or $kzm=="JPG" or $kzm=="png") { //文件过滤

                if (!is_dir('./'.$file)) { //文件夹过滤

                    $array[]=$file;//把符合条件的文件名存入数组
                    $source =  $path."/".$file;//原图片名称
                    $dst_img = $path."/".$file;//压缩后图片的名称
                    $percent = 0.5;  #原图压缩，不缩放，但体积大大降低
                    $image = (new ImageCompress($source,$percent))->compressImg($dst_img);
                }

            }

        }

    }
}