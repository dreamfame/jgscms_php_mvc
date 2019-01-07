<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
require_once '../Model/Sysmsg.php';
require_once '../DataBaseHandle/SysmsgServer.php';
require_once '../DataBaseHandle/PhotoServer.php';
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set('PRC');
error_reporting(0);
Class SysmsgControl
{
    public function JudgeOperate($operate)
    {
        switch ($operate) {
            case "query":
                SysmsgControl::GetMessage();
                break;
            case "del":
                SysmsgControl::DelMessage();
                break;
            case "update":
                SysmsgControl::UpdateMessage();
                break;
        }
    }

    public function GetMessage(){
        $openid = $_REQUEST['openid'];
        $ss = new SysmsgServer();
        $result = $ss->GetMsgByOpenid($openid);
        $re = array('state'=>'0','content'=>"未获取数据");
        while ($n = mysqli_fetch_array($result)) {
            $re['state'] = '1';
            $row[] = array('id' => $n['id'],'openid'=>$n['openid'],'created_at'=>$n['created_at'],'see'=>$n['see'],'title'=>$n['title'],'content'=>$n['content']);
            $re['content'] = $row;
        }
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
        return;
    }

    public function UpdateMessage(){
        $id = $_REQUEST['id'];
        $re = array('state'=>'0','content'=>'修改失败');
        $ss = new SysmsgServer();
        $result = $ss->UpdateMessage($id);
        if($result==""){
            $re['state'] = '1';
            $re['content'] = '修改成功';
        }
        else{
            $re['state'] = '0';
            $re['content'] = '修改失败,'.$result;
        }
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
        return;
    }

    public function AddMsg($id,$verify){
        $sysmsg = new Sysmsg();
        $ps = new PhotoServer();
        $photo = $ps->GetPhotoById($id);
        if($verify=="0"){
            return;
        }
        else if($verify=="1"){
            $sysmsg->title = "审核结果";
            $sysmsg->content = "您在".$photo->created_at."分享的照片审核通过了";
        }
        else if($verify=="2"){
            $sysmsg->title = "审核结果";
            $sysmsg->content = "您在".$photo->created_at."分享的照片未通过审核";
        }
        $sysmsg->openid = $photo->uid;
        $sysmsg->see = 0;
        date_default_timezone_set('PRC');
        $sysmsg->created_at = date('Y-m-d H:i:s', time());
        $ss = new SysmsgServer();
        $result = $ss->InsertMsg($sysmsg);
        $re = array('state'=>'0','content'=>'消息添加成功');
        if($result==""){
            $re['state'] = "1";
            $re['content'] = "消息添加成功";
        }
        else{
            $re['state'] = "0";
            $re['content'] = "消息添加失败，"+$result;
        }
        //echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    public function DelMessage(){
        $id = $_REQUEST['id'];
        $ss = new SysmsgServer();
        $result=$ss->DeleteMsg($id);
        $re = array('state'=>'0','content'=>'删除失败');
        if($result=="") {
            $re['state']='1';
            $re['content']='删除成功';
        }
        else{
            $re['state']='0';
            $re['content']='删除失败'.+$result;
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }
}
?>
