<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 * Date: 2018/12/28
 * Time: 14:04
 */
require_once '../DataBaseHandle/UserServer.php';
header("Content-Type: text/html;charset=utf-8");
if (!session_id()) session_start();
error_reporting(0);
class ServerControl
{
    public function server_open(){
        $openid = "";
        set_time_limit(0);//无限请求超时时间
        while (true){
            //sleep(1);
            $json_string = file_get_contents('../View/json/openid.json');
            $data = json_decode($json_string, true);
            if($data!=null){
                $openid = $data['openid'];
            }
            else{
                $openid = "";
            }
            usleep(500000);//0.5秒
            if($openid!=""){
                $arr[] = array('success'=>"1",'openid'=>$openid);
                echo json_encode($arr);
                exit();
            }
            else{
                $arr[] = array('success'=>"0",'name'=>'');
                echo json_encode($arr);
                exit();
            }
        }
    }

    public function getOpenId(){
        $open_id = $_REQUEST['openid'];
        $jsonfile = fopen("../View/json/openid.json", "w") or die("Unable to open file!");
        $row = array('openid' => $open_id);
        if (flock($jsonfile, LOCK_EX)) {//加写锁 
            ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
            rewind($jsonfile); // 倒回文件指针的位置 
            fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
            flock($jsonfile, LOCK_UN); //解锁 
        }
        fclose($jsonfile);
        echo $open_id;
    }

    public function server_close(){
        $jsonfile = fopen("../View/json/openid.json", "w") or die("Unable to open file!");
        $row = array('openid' => "");
        if (flock($jsonfile, LOCK_EX)) {//加写锁 
            ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
            rewind($jsonfile); // 倒回文件指针的位置 
            fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
            flock($jsonfile, LOCK_UN); //解锁 
        }
        fclose($jsonfile);
        $json_string = file_get_contents('../View/json/openid.json');
        $data = json_decode($json_string, true);
        if($data!=null&&$data["openid"]==""){
            echo "0";
        }
        else{
            echo "1";
        }
    }
}