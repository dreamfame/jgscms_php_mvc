<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../Model/Admin.php';
    require_once '../Model/LoginStatus.php';
	require_once '../DataBaseHandle/AdminServer.php';
	require_once '../Extensions/Security.php';
    require_once '../Extensions/IPInfo.php';
	require_once '../DataBaseHandle/LoginStatusServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	error_reporting(0);
	Class AdminControl
	{
        const key = "made with liuliu";

        const salt = "_66ll_";

		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
					AdminControl::GetAll();
					break;
				case "add":
                    AdminControl::AddAdmin();
					break;
				case "del":
					AdminControl::DelAdmin();
					break;
                case "batchDel":
                    AdminControl::BatchDelAdmin();
                    break;
				case "edit":
                    AdminControl::UpdateAdmin();
					break;
				case "query":
                    AdminControl::GetAdmin();
					break;
				case "login":
                    AdminControl::ValidateLogin();
					break;
				case "role":
					AdminControl::ChangeRole();
					break;
				case "status":
					AdminControl::ChangeStatus();
					break;
				case "reset":
					AdminControl::ResetPwd();
					break;
				case "pwd":
					AdminControl::UpdatePwd();
					break;
                case "verify":
                    AdminControl::VerifyInfo();
                    break;
                case "verify_edit":
                    AdminControl::VerifyEditInfo();
                    break;
                case "verify_pwd":
                    AdminControl::VerifyPwd();
                    break;
			}
		}

		public function GetAll()
		{
            $as = new AdminServer();
            $result = $as->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/adminList.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'username' => $u['username'], 'nickname' => $u['nickname'], 'age' => $u['age'], 'phone' => $u['phone'], 'email' => $u['email'], 'status' => $u['status'], 'role' => $u['role']);
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

		public function VerifyInfo(){
            $wherelist = array();
            if(!empty($_REQUEST['name'])){
                $username = $_REQUEST['name'];
                $wherelist[] = "username = '{$username}'";
            }
            if(!empty($_REQUEST['nickname'])){
                $nickname = $_REQUEST['nickname'];
                $wherelist[] = "nickname = '{$nickname}'";
            }
            if(!empty($_REQUEST['phone'])){
                $phone = $_REQUEST['phone'];
                $wherelist[] = "phone = '{$phone}'";
            }
            if(!empty($_REQUEST['email'])){
                $email = $_REQUEST['email'];
                $wherelist[] = "email = '{$email}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
            $as = new AdminServer();
            $result = $as->VerifyInfo($where);
            $n = mysqli_fetch_object($result);
            echo $n->result;
            return;
        }

        public function VerifyEditInfo(){
		    $username = Security::decrypt($_REQUEST['username']);
            $wherelist = array();
            if(!empty($_REQUEST['nickname'])){
                $nickname = $_REQUEST['nickname'];
                $wherelist[] = "nickname = '{$nickname}'";
            }
            if(!empty($_REQUEST['phone'])){
                $phone = $_REQUEST['phone'];
                $wherelist[] = "phone = '{$phone}'";
            }
            if(!empty($_REQUEST['email'])){
                $email = $_REQUEST['email'];
                $wherelist[] = "email = '{$email}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
            $as = new AdminServer();
            $result = $as->VerifyEditInfo($where,$username);
            $n = mysqli_fetch_object($result);
            echo $n->result;
            return;
        }

        public function VerifyPwd(){
            $username = Security::decrypt($_REQUEST['username']);
            $password = $_REQUEST['password'];
            $as = new AdminServer();
            $result = $as->VerifyPwd($username);
            $n = mysqli_fetch_object($result);
            if(password_verify($password,$n->password))
            {
                echo "1";
                return ;
            }
            else{
                echo "0";
                return;
            }
        }

		public function UpdateAdminJson(){
            $as = new AdminServer();
            $result = $as->GetAll();
            $jsonfile = fopen("../View/json/adminList.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'username' => $u['username'], 'nickname' => $u['nickname'], 'age' => $u['age'], 'phone' => $u['phone'], 'email' => $u['email'], 'status' => $u['status'], 'role' => $u['role']);
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

		public function AddAdmin()
		{
			$admin = new Admin();
			$admin->username = $_REQUEST['username'];
			$admin->password = password_hash("666666", PASSWORD_DEFAULT);
			$admin->nickname = $_REQUEST['nickname'];
			$admin->role = $_REQUEST['role'];
			$admin->age = 0;
			$admin->head_pic = $_REQUEST['head_pic'];
			$admin->phone = $_REQUEST['phone'];
			$admin->email = $_REQUEST['email'];
			$admin->status = $_REQUEST['status'];
			$admin->openid = $_REQUEST["openid"];
			$admin->updated_at = time();
			$admin->created_at = time();
			$admin->password_reset_token = md5($admin->username.AdminControl::key,false);
			$as = new AdminServer();
            $re = array('state'=>'0','content'=>'添加失败,');
            $result = $as->InsertAdmin($admin);
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

		public function UpdateAdmin()
		{
			$admin = new Admin();
			$admin->username = $_REQUEST['username'];
			$admin->nickname = $_REQUEST['nickname'];
			$admin->age = $_REQUEST['age'];
			$admin->phone = $_REQUEST['phone'];
			$admin->email = $_REQUEST['email'];
			$admin->head_pic = $_REQUEST['head_pic'];
			$re = array('state'=>'0','content'=>'修改失败');
			$as = new AdminServer();
			$result = $as->UpdateAdmin($admin,"all");
            if($result == "") {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            else{
                $re['content']='修改失败，错误信息：'.$result;
            }
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function GetAdmin()
		{
            $wherelist = array();
            if(!empty($_REQUEST['username'])){
            	$username = Security::decrypt($_REQUEST['username']);
                $wherelist[] = "username = '{$username}'";
            }
            if($_REQUEST['userid']!=""||$_REQUEST['userid']!=null){
                $id = $_REQUEST['userid'];
                $wherelist[] = "id = '{$id}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
			$as = new AdminServer();
			$result = $as->QueryAdmin($where);
			$re = array('state'=>'0','content'=>null);
			while ($u = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
                $row[] = array('id' => $u['id'], 'username' => $u['username'], 'nickname' => $u['nickname'], 'age' => $u['age'], 'phone' => $u['phone'], 'email' => $u['email'], 'status' => $u['status'], 'role' => $u['role'],'head_pic'=>$u['head_pic']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelAdmin(){
			$id = $_REQUEST['id'];
            $as = new AdminServer();
            $result=$as->DeleteAdmin($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function BatchDelAdmin(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new AdminServer();
            $result=$as->BatchDeleteAdmin($str);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
		
		public function ValidateLogin(){
			$userid = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			$as = new AdminServer();
			$result = $as->GetAdmin($userid);
			$re = array('state'=>'0','content'=>null);
			$a = mysqli_fetch_row($result);
			if($a[1]==""){
                $re['state'] = "0";
				$re['content']="用户名不存在";
			}
			else{
				if(password_verify($password, $a[2])){
				    if($a[8]=="1") {
				        if(isset( $_SESSION['session_id'])){
                            //待开发，短信验证限制单用户多地登录
                        }
                        $re = array('state' => '0', 'nickname' => null, 'username' => null);
                        $re['state'] = "1";
                        $re['nickname'] = $a[3];
                        $re['username'] = Security::encrypt($a[1]);
                        $re['head_pic'] = $a[6];
                        $re['role'] = $a[7];
                        session_start();
                        $is_login = 1;
                        $_SESSION['operator'] = $userid;
                        $_SESSION["session_id"] = base64_encode(time() + 1200 );
                        $client_ip = IPControl::getClientIp();
                        $ls = new LoginStatus();
                        $ls->username = $userid;
                        $ls->is_login = $is_login;
                        $ls->client_ip = $client_ip;
                        $ls->session_id = $_SESSION["session_id"];
                        $lss = new LoginStatusServer();
                        $res = $lss->LogIn($ls);
                        if($res==""){

                        }
                    }
                    else{
                        $re['state'] = "0";
                        $re['content'] = "此用户已被禁用";
                    }
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
			$as = new AdminServer();
			$admin = new Admin();
			$admin->username = $username;
			$admin->role = $role;
			$result = $as->UpdateAdmin($admin,"role");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result == "") {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            else{
                $re['content']='修改失败，错误信息：'.$result;
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function ChangeStatus(){
            $username = $_REQUEST['username'];
            $status = $_REQUEST['status'];
            $as = new AdminServer();
            $admin = new Admin();
            $admin->username = $username;
            $admin->status = $status;
            $result = $as->UpdateAdmin($admin,"status");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result == "") {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            else{
                $re['content']='修改失败，错误信息：'.$result;
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function ResetPwd(){
			$username = $_REQUEST['username'];
			$password = password_hash("666666",PASSWORD_DEFAULT);
            $as = new AdminServer();
            $admin = new Admin();
            $admin->username = $username;
            $admin->password = $password;
            $result = $as->UpdateAdmin($admin,"password");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result == "") {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            else{
                $re['content']='修改失败，错误信息：'.$result;
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function UpdatePwd(){
            $username = Security::decrypt($_REQUEST['username']);
            $newpwd = $_REQUEST['newpwd'];
            $re = array('state'=>'0','content'=>'修改失败');
            $admin = new Admin();
            $admin->username = $username;
            $admin->password = password_hash($newpwd,PASSWORD_DEFAULT);
            $as = new AdminServer();
            $result = $as->UpdateAdmin($admin,"password");
            if($result == "") {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
			}
			else{
                $re['content']='修改失败，错误信息：'.$result;
			}
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
	}
?>
