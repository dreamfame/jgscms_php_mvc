<?php
/**
 * Created by PhpStorm.
 * User: liuliu
 * Date: 2019/1/9
 * Time: 17:33
 */

Class ImageMerge{
    static public function MergePic(){

    }

    static public function MergeText(){
        $bigImgPath = '../View/images/1545309416default.png';
        $qCodePath = '../View/images/1545309636alipay.jpg';

        $bigImg = imagecreatefromstring(file_get_contents($bigImgPath));
        $qCodeImg = imagecreatefromstring(file_get_contents($qCodePath));

        list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($qCodePath);
        imagecopymerge($bigImg, $qCodeImg, 200, 300, 0, 0, $qCodeWidth, $qCodeHight, 100);

        list($bigWidth, $bigHight, $bigType) = getimagesize($bigImgPath);


        switch ($bigType) {
            case 1: //gif
                header('Content-Type:image/gif');
                imagegif($bigImg);
                break;
            case 2: //jpg
                header('Content-Type:image/jpg');
                imagejpeg($bigImg);
                break;
            case 3: //jpg
                header('Content-Type:image/png');
                imagepng($bigImg);
                break;
            default:
                # code...
                break;
        }

        imagedestroy($bigImg);
        imagedestroy($qCodeImg);
    }
}