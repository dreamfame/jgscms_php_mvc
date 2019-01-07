<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
require_once '../DataBaseHandle/ScanServer.php';
error_reporting(0);
class ScanControl
{
    public function JudgeOperate($operate)
    {
        switch($operate)
        {
            case "news":
                ScanControl::scanNews();
                break;
            case "area":
                ScanControl::scanArea();
                break;
            case "scenic":
                ScanControl::scanScenic();
                break;
        }
    }

    public function scanNews(){
        $id = $_REQUEST['id'];
        $ss = new ScanServer();
        $result=$ss->Scan($id,"news");
        $re = array('state'=>'0','content'=>'error');
        if($result=="") {
            $re['state']='1';
            $re['content']='success';
        }
        else{
            $re['content']='error:'+$result;
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    public function scanArea(){
        $id = $_REQUEST['id'];
        $ss = new ScanServer();
        $result=$ss->Scan($id,"area");
        $re = array('state'=>'0','content'=>'error');
        if($result=="") {
            $re['state']='1';
            $re['content']='success';
        }
        else{
            $re['content']='error:'+$result;
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    public function scanScenic(){
        $id = $_REQUEST['id'];
        $ss = new ScanServer();
        $result=$ss->Scan($id,"scenic");
        $re = array('state'=>'0','content'=>'error');
        if($result=="") {
            $re['state']='1';
            $re['content']='success';
        }
        else{
            $re['content']='error:'+$result;
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }
}