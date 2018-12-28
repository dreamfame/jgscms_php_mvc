<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 * Date: 2018/12/28
 * Time: 14:28
 */
$jsonfile = fopen("../View/json/openid.json", "w") or die("Unable to open file!");
$row = array('openid' => $_REQUEST['openid']);
if (flock($jsonfile, LOCK_EX)) {//加写锁 
    ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
    rewind($jsonfile); // 倒回文件指针的位置 
    fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
    flock($jsonfile, LOCK_UN); //解锁 
}
fclose($jsonfile);
$json_string = file_get_contents('../View/json/openid.json');
$data = json_decode($json_string, true);
if($data!=null){
    if($data['openid']!="") {
        echo "1";
    }
    else{
        echo "0";
    }
}
else{
    echo "0";
}