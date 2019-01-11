<?php
/**
 * Created by PhpStorm.
 * User: liuliu
 * Date: 2019/1/9
 * Time: 17:33
 */

Class ImageMerge{
    static public function MergeText($orginPath,$savePath,$fontPath="C://WINDOWS//Fonts//STXINGKA.TTF",$size=20,$left,$top,$content){
        $bigImgPath = $orginPath;
        $image = $savePath;
        $img = imagecreatefromstring(file_get_contents($bigImgPath));

        $font = $fontPath;//字体
        $black = imagecolorallocate($img, 0, 0, 0);//字体颜色 RGB

        $fontSize = $size;   //字体大小
        $circleSize = 0; //旋转角度
        $left = $left;      //左边距
        $top = $top;       //顶边距

        imagefttext($img, $fontSize, $circleSize, $left, $top, $black, $font, $content);

        list($bgWidth, $bgHight, $bgType) = getimagesize($bigImgPath);
        switch ($bgType) {
            case 1: //gif
                //header('Content-Type:image/gif');
                imagegif($img,$image);
                break;
            case 2: //jpg
                //header('Content-Type:image/jpg');
                imagejpeg($img,$image);
                break;
            case 3: //jpg
                //header('Content-Type:image/png');
                imagepng($img,$image);
                break;
            default:
                break;
        }
        imagedestroy($img);

        return $image;
    }

    static public function MergePic($bigImage="../View/images/postcard/bg.png",$smallImage,$savaPath,$resizeWidth,$resizeHeight,$x,$y){
        $bigImgPath = $bigImage;
        $qCodePath = $smallImage;
        $qCodePath = ImageMerge::resize($qCodePath,$resizeWidth,$resizeHeight);
        $image = $savaPath;
        $bigImg = imagecreatefromstring(file_get_contents($bigImgPath));
        $qCodeImg = imagecreatefromstring(file_get_contents($qCodePath));

        list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($qCodePath);
        imagecopymerge($bigImg, $qCodeImg, $x, $y, 0, 0, $resizeWidth, $resizeHeight, 100);

        list($bigWidth, $bigHight, $bigType) = getimagesize($bigImgPath);


        switch ($bigType) {
            case 1: //gif
                //header('Content-Type:image/gif');
                imagegif($bigImg,$image);
                break;
            case 2: //jpg
                //header('Content-Type:image/jpg');
                imagejpeg($bigImg,$image);
                break;
            case 3: //jpg
                //header('Content-Type:image/png');
                imagepng($bigImg,$image);
                break;
            default:
                # code...
                break;
        }

        imagedestroy($bigImg);
        imagedestroy($qCodeImg);
        return $image;
    }

    static function resize($src, $width=535, $height=400) {
        $temp = pathinfo($src);
        $filename = time().'.jpg';
        $dir = "../View/images/";    //文件所在的文件夹
        $savepath = $dir."new".$filename; //缩略图保存路径
        //获取图片的基本信息
        $info = getimagesize($src);
        if($info[0] == $width && $info[1] == $height){
            //如果分辨率一样，直接返回原图
            return $src;
        }
        switch ($info['mime']) {
            case 'image/jpeg':
                //header('Content-Type:image/jpeg');
                $image_wp = imagecreatetruecolor($width, $height);
                $image_src = imagecreatefromjpeg($src);
                imagecopyresampled($image_wp, $image_src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
                imagedestroy($image_src);
                imagejpeg($image_wp, $savepath);
                break;
            case 'image/png':
                //header('Content-Type:image/png');
                $image_wp = imagecreatetruecolor($width, $height);
                $image_src = imagecreatefrompng($src);
                imagecopyresampled($image_wp, $image_src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
                imagedestroy($image_src);
                imagejpeg($image_wp, $savepath);
                break;
            case 'image/gif':
                //header('Content-Type:image/gif');
                $image_wp = imagecreatetruecolor($width, $height);
                $image_src = imagecreatefromgif($src);
                imagecopyresampled($image_wp, $image_src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
                imagedestroy($image_src);
                imagejpeg($image_wp, $savepath);
                break;
        }
        return $savepath;
    }

    static function create($src)
    {
        $info = getimagesize($src);
        switch ($info[2]) {
            case 1:
                $im = imagecreatefromgif($src);
                break;
            case 2:
                $im = imagecreatefromjpeg($src);
                break;
            case 3:
                $im = imagecreatefrompng($src);
                break;
        }
        return $im;
    }
}