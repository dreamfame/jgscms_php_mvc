<?php
/**
 * Created by PhpStorm.
 * User: liuliu
 * Date: 2019/1/9
 * Time: 17:33
 */

Class ImageMerge{
    static public function MergeText(){

    }

    static public function MergePic(){
        $bigImgPath = '../View/images/a.png';
        $qCodePath = '../View/images/c.png';
        $qCodePath = ImageMerge::resize($qCodePath,260,340);
        $image = "../View/images/1.png";
        $bigImg = imagecreatefromstring(file_get_contents($bigImgPath));
        $qCodeImg = imagecreatefromstring(file_get_contents($qCodePath));

        list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($qCodePath);
        imagecopymerge($bigImg, $qCodeImg, 145, 200, 0, 0, 260, 340, 100);

        list($bigWidth, $bigHight, $bigType) = getimagesize($bigImgPath);


        switch ($bigType) {
            case 1: //gif
                header('Content-Type:image/gif');
                imagegif($bigImg,$image);
                break;
            case 2: //jpg
                header('Content-Type:image/jpg');
                imagejpeg($bigImg,$image);
                break;
            case 3: //jpg
                header('Content-Type:image/png');
                imagepng($bigImg,$image);
                break;
            default:
                # code...
                break;
        }

        imagedestroy($bigImg);
        imagedestroy($qCodeImg);
    }

    static function resize($src, $width_value=535, $height_value=400) {
        $temp = pathinfo($src);
        $filename = time().'.jpg';
        $dir = "../View/images/";    //文件所在的文件夹
        $savepath = $dir."new".$filename; //缩略图保存路径
        //获取图片的基本信息
        $info = getimagesize($src);
        $width = $info[0];      //获取图片宽度
        $height = $info[1];     //获取图片高度
        if(($width/$height) >= ($width_value/$height_value)){ //宽度优先
            $w_mid = $width_value;						  //压缩后图片的宽度
            $h_mid = intval($width_value * $height/$width);//等比缩放图片高度
            $mid_x = 0;
            $mid_y = intval(($height_value-$h_mid)/2);
        }else{											//高度优先
            $w_mid = intval($height_value * $width/$height);							//压缩后图片的宽度
            $h_mid = $height_value;//等比缩放图片高度
            $mid_x = intval(($width_value-$w_mid)/2);
            $mid_y = 0;
        }

        $temp_img = imagecreatetruecolor($width_value , $height_value);		//创建画布
        $white = imagecolorallocate($temp_img, 255, 255, 255);
        imagefill($temp_img, 0, 0, $white);
        $im = ImageMerge::create($src);
        imagecopyresampled($temp_img, $im, $mid_x, $mid_y, 0, 0, $w_mid, $h_mid, $width, $height);
        imagejpeg($temp_img,$savepath, 100);
        imagedestroy($im);

        return $savepath;
    }

    /**
     * 创建图片，返回资源类型
     * @param  string $src 图片路径
     * @return resource $im 返回资源类型
     */
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