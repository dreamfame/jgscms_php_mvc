<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
require_once '../Model/User.php';
require_once '../DataBaseHandle/UserServer.php';
header("Content-Type: text/html;charset=utf-8");
//session_start();
error_reporting(0);
Class UserControl
{
    public function JudgeOperate($operate)
    {
        switch($operate)
        {
            case "list":
                UserControl::GetAll();
                break;
            case "add":
                UserControl::AddUser();
                break;
            case "query":
                UserControl::GetUser();
                break;
			case "json":
				UserControl::UpdateUserJson();
				break;
            case "new":
                UserControl::GetNewUser();
                break;
        }
    }

    public function GetAll()
    {
        $as = new UserServer();
        $result = $as->GetAll();
        $re = array('state'=>'0','content'=>"未获取到数据");
        $jsonfile = fopen("../View/json/userList.json", "w") or die("Unable to open file!");
        while ($u = mysqli_fetch_array($result)) {
            $re['state'] = '1';
            $row[] = array('id' => $u['id'],'openid' => $u['openid'],  'wx' => $u['wx'], 'nickname' => $u['nickname'], 'avatar' => $u['avatar'],'gender' => $u['gender'], 'city' => $u['city'], 'country' => $u['country'],'created_at'=>$u['created_at'],'auth'=>$u['auth']);
            $re['content'] = $row;
            if (flock($jsonfile, LOCK_EX)) {//加写锁 
                ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                rewind($jsonfile); // 倒回文件指针的位置 
                fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                flock($jsonfile, LOCK_UN); //解锁 
            }
        }
        fclose($jsonfile);
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
        return;
    }

    public function GetUser(){
        $as = new UserServer();
        $wherelist = array();
        if($_REQUEST['openid']!=""||$_REQUEST['openid']!=null){
            $wherelist[] = "openid = '{$_REQUEST['openid']}'";
        }
        if($_REQUEST['id']!=""||$_REQUEST['id']!=null){
            $wherelist[] = "id = '{$_REQUEST['id']}'";
        }
        //组装查询条件
        if(count($wherelist) > 0){
            $where = " where ".implode(' and ' , $wherelist);
        }
        //判断查询条件
        $where = isset($where) ? $where : '';
        $result = $as->QueryUser($where);
        $re = array('state'=>'0','content'=>"未获取到数据");
        while ($u = mysqli_fetch_array($result)) {
            $re['state'] = '1';
            $row[] = array('id' => $u['id'],'openid' => $u['openid'],  'wx' => $u['wx'], 'nickname' => $u['nickname'], 'avatar' => $u['avatar'],'gender' => $u['gender'], 'city' => $u['city'], 'country' => $u['country'],'created_at'=>$u['created_at'],'auth'=>$u['auth']);
            $re['content'] = $row;
        }
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
        return;
    }

    public function GetNewUser(){
        $as = new UserServer();
        $wherelist = array();
        if($_GET['time']!=""||$_GET['time']!=null){
            $wherelist[] = "created_at between '{$_GET['time']}' and '{$_GET['time']}'";
        }
        //组装查询条件
        if(count($wherelist) > 0){
            $where = " where ".implode(' and ' , $wherelist);
        }
        //判断查询条件
        $where = isset($where) ? $where : '';
        $result = $as->QueryUser($where);
        $re = array('state'=>'0','content'=>"未获取到数据");
        $jsonfile = fopen("../View/json/userList.json", "w") or die("Unable to open file!");
        while ($u = mysqli_fetch_array($result)) {
            $re['state'] = '1';
            $row[] = array('id' => $u['id'],'openid' => $u['openid'],  'wx' => $u['wx'], 'nickname' => $u['nickname'], 'avatar' => $u['avatar'],'gender' => $u['gender'], 'city' => $u['city'], 'country' => $u['country'],'created_at'=>$u['created_at'],'auth'=>$u['auth']);
            $re['content'] = $row;
            if (flock($jsonfile, LOCK_EX)) {//加写锁 
                ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                rewind($jsonfile); // 倒回文件指针的位置 
                fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                flock($jsonfile, LOCK_UN); //解锁 
            }
        }
        fclose($jsonfile);
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
        return;
    }

    public function UpdateUserJson(){
        $as = new UserServer();
        $result = $as->GetAll();
        $jsonfile = fopen("../View/json/userList.json", "w") or die("Unable to open file!");
        while ($u = mysqli_fetch_array($result)) {
            $re['state'] = '1';
            $row[] = array('id' => $u['id'],'openid' => $u['openid'],  'wx' => $u['wx'], 'nickname' => $u['nickname'], 'avatar' => $u['avatar'],'gender' => $u['gender'], 'city' => $u['city'], 'country' => $u['country'],'created_at'=>$u['created_at']);
            $re['content'] = $row;
            if (flock($jsonfile, LOCK_EX)) {//加写锁 
                ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                rewind($jsonfile); // 倒回文件指针的位置 
                fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                flock($jsonfile, LOCK_UN); //解锁 
            }
        }
        fclose($jsonfile);
    }

    public function AddUser()
    {
        $user = new User();
        $user->openid = $_REQUEST['openid'];
        $user->wx = $_REQUEST['wx'];
        $user->nickname = $_REQUEST['nickname'];
        $user->avatar = $_REQUEST['avatar'];
        $user->city = $_REQUEST['city'];
        $user->country = $_REQUEST['country'];
        $user->gender = $_REQUEST['gender'];
        date_default_timezone_set('PRC');
        $user->created_at = date('Y-m-d H:i:s', time());
        $user->auth = $_REQUEST['auth'];
        $as = new UserServer();
        $re = array('state'=>'0','content'=>'添加失败,');
        $result = $as->InsertUser($user);
        if($result==""){
            $re['state'] = '1';
            $re['content'] = '添加成功';
        }
        else{
            $re['state'] = '0';
            $re['content'] = '添加失败，错误信息：'.$result;
        }
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
    }
}
?>
