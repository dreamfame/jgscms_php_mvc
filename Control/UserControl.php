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
        $user->username = $_REQUEST['username'];
        $user->password = password_hash("666666", PASSWORD_DEFAULT);
        $user->nickname = $_REQUEST['nickname'];
        $user->role = $_REQUEST['role'];
        $user->age = 0;
        $user->head_pic = "default.jpg";
        $user->phone = $_REQUEST['phone'];
        $user->email = $_REQUEST['email'];
        $user->status = $_REQUEST['status'];
        $user->updated_at = time();
        $user->created_at = time();
        $user->password_reset_token = md5($user->username.UserControl::key,false);
        $as = new UserServer();
        $result = $as->GetUser($user->username);
        $temp = false;
        $re = array('state'=>'0','content'=>'添加失败,');
        while ($u = mysqli_fetch_array($result))//检查用户名
        {
            $temp = true;
            $re['content'] = $re['content'].'已存在该用户!';
            $row[]= array('username'=>$u['username'],'password'=>$u['password']);
        }
        $condition="nickname";
        $content = $user->nickname;
        $result1 = $as->GetUserByCondition($condition,$content);
        while ($u = mysqli_fetch_array($result1))//检查昵称
        {
            $temp = true;
            $re['content'] = $re['content'].'昵称已存在!';
            $row[]= array('username'=>$u['username'],'password'=>$u['password']);
        }
        $condition="phone";
        $content = $user->phone;
        $result2 = $as->GetUserByCondition($condition,$content);
        while ($u = mysqli_fetch_array($result2))//检查手机号
        {
            $temp = true;
            $re['content'] = $re['content'].'手机号已被使用!';
            $row[]= array('username'=>$u['username'],'password'=>$u['password']);
        }
        $condition="email";
        $content = $user->email;
        $result3 = $as->GetUserByCondition($condition,$content);
        while ($u = mysqli_fetch_array($result3))//检查邮箱
        {
            $temp = true;
            $re['content'] = $re['content'].'邮箱已被使用!';
            $row[]= array('username'=>$u['username'],'password'=>$u['password']);
        }
        if(!$temp){
            $row = $as->InsertUser($user);
            $re['state'] = '1';
            $re['content'] = '添加成功';
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
        }
        else{
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
        }
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
