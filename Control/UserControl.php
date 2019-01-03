<?php
require_once '../Model/User.php';
require_once '../DataBaseHandle/UserServer.php';
header("Content-Type: text/html;charset=utf-8");
//session_start();
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
            case "del":
                UserControl::DelUser();
                break;
            case "edit":
                UserControl::UpdateUser();
                break;
            case "query":
                UserControl::GetUser();
                break;
            case "login":
                UserControl::ValidateLogin();
                break;
            case "role":
                UserControl::ChangeRole();
                break;
            case "status":
                UserControl::ChangeStatus();
                break;
            case "reset":
                UserControl::ResetPwd();
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
        $re = array('state'=>'0','content'=>null);
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
        $re = array('state'=>'0','content'=>null);
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
        $user->created_at = $_REQUEST['time'];
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

    public function UpdateUser()
    {
        $user = new User();
        $user->userId = $_REQUEST['userid'];
        $user->password = $_REQUEST['password'];
        $re = array('state'=>'0','content'=>'修改失败');
        $as = new UserServer();
        $result = $as->UpdateUser($user);
        if($result){
            $re['state'] = '1';
            $re['content'] = '修改成功';
        }
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
        return;
    }

    public function GetUser()
    {
        $userid = $_REQUEST['userid'];
        $as = new UserServer();
        $result = $as->GetUserById($userid);
        $re = array('state'=>'0','content'=>null);
        while ($u = mysqli_fetch_array($result))
        {
            $re['state'] = '1';
            $row[]= array('username'=>$u['username'],'role'=>$u['role']);
            $re['content'] = $row;
        }
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
        return;
    }

    public function DelUser(){
        $id = $_REQUEST['id'];
        $as = new UserServer();
        $result=$as->DeleteUser($id);
        $re = array('state'=>'0','content'=>'删除失败');
        if($result) {
            UserControl::UpdateUserJson();
            $re['state']='1';
            $re['content']='删除成功';
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    public function ValidateLogin(){
        $userid = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        $as = new UserServer();
        $result = $as->GetUser($userid);
        $re = array('state'=>'0','content'=>null);
        $a = mysqli_fetch_row($result);
        if($a[1]==""){
            $re['state'] = "0";
            $re['content']="用户名不存在";
        }
        else{
            if(password_verify($password, $a[2])){
                $_SESSION["name"] = $userid;
                $re['state'] = "1";
                $re['content'] = $a[3];
            }
            else{
                $re['state'] = "0";
                $re['content'] = "密码错误";
            }
        }
        echo json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    public function ChangeRole(){
        $username = $_REQUEST['username'];
        $role = $_REQUEST['role'];
        $as = new UserServer();
        $user = new User();
        $user->username = $username;
        $user->role = $role;
        $result = $as->UpdateUser($user,"role");
        $re = array('state'=>'0','content'=>'修改失败');
        if($result) {
            UserControl::UpdateUserJson();
            $re['state']='1';
            $re['content']='修改成功';
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    public function ChangeStatus(){
        $username = $_REQUEST['username'];
        $status = $_REQUEST['status'];
        $as = new UserServer();
        $user = new User();
        $user->username = $username;
        $user->role = $status;
        $result = $as->UpdateUser($user,"status");
        $re = array('state'=>'0','content'=>'修改失败');
        if($result) {
            UserControl::UpdateUserJson();
            $re['state']='1';
            $re['content']='修改成功';
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    public function ResetPwd(){
        $username = $_REQUEST['username'];
        $password = password_hash("666666",PASSWORD_DEFAULT);
        $as = new UserServer();
        $user = new User();
        $user->username = $username;
        $user->password = $password;
        $result = $as->UpdateUser($user,"password");
        $re = array('state'=>'0','content'=>'修改失败');
        if($result) {
            UserControl::UpdateUserJson();
            $re['state']='1';
            $re['content']='修改成功';
        }
        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
    }
}
?>
