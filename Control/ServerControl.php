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
        $us = new UserServer();
        $wx = "";
        $openid = "";
        set_time_limit(0);//无限请求超时时间
        while (true){
            //sleep(1);
            if(isset($_SESSION['openid'])&&!empty($_SESSION['openid'])){
                $openid = $_SESSION['openid'];
            }
            else{
                $openid = "";
            }
            usleep(500000);//0.5秒
            if($openid!=""&&$wx==""){
                $wx = $us->GetWx($openid);
            }
            //若得到数据则马上返回数据给客服端，并结束本次请求
            if($wx!=""&&$openid!=""){
                $arr=array('success'=>"1",'name'=>$wx);
                echo json_encode($arr);
                exit();
            }
            //服务器($_POST['time']*0.5)秒后告诉客服端无数据
            else{
                $arr=array('success'=>"0",'name'=>'');
                echo json_encode($arr);
                exit();
            }
        }
    }

    public function server_close(){
        unset($_SESSION['openid']);
        if(isset($_SESSION['openid'])&&!empty($_SESSION['openid'])){
            $arr=array('success'=>"0",'name'=>"");
            echo json_encode($arr);
        }
        else{
            $arr=array('success'=>"1",'name'=>"");
            echo json_encode($arr);
        }
    }
}