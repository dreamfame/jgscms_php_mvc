<?php
	require_once '../Model/Admin.php';
	require_once '../DataBaseHandle/AdminServer.php';
require_once '../Extensions/Security.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
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
			$admin->head_pic = "default.jpg";
			$admin->phone = $_REQUEST['phone'];
			$admin->email = $_REQUEST['email'];
			$admin->status = $_REQUEST['status'];
			$admin->updated_at = time();
			$admin->created_at = time();
			$admin->password_reset_token = md5($admin->username.AdminControl::key,false);
			$as = new AdminServer();
			$result = $as->GetAdmin($admin->username);
			$temp = false;
            $re = array('state'=>'0','content'=>'添加失败,');
			while ($u = mysqli_fetch_array($result))//检查用户名
			{
				$temp = true;
				$re['content'] = $re['content'].'已存在该用户!';
				$row[]= array('username'=>$u['username'],'password'=>$u['password']);
			}
			$condition="nickname";
			$content = $admin->nickname;
            $result1 = $as->GetAdminByCondition($condition,$content);
            while ($u = mysqli_fetch_array($result1))//检查昵称
            {
                $temp = true;
                $re['content'] = $re['content'].'昵称已存在!';
                $row[]= array('username'=>$u['username'],'password'=>$u['password']);
            }
            $condition="phone";
            $content = $admin->phone;
            $result2 = $as->GetAdminByCondition($condition,$content);
            while ($u = mysqli_fetch_array($result2))//检查手机号
            {
                $temp = true;
                $re['content'] = $re['content'].'手机号已被使用!';
                $row[]= array('username'=>$u['username'],'password'=>$u['password']);
            }
            $condition="email";
            $content = $admin->email;
            $result3 = $as->GetAdminByCondition($condition,$content);
            while ($u = mysqli_fetch_array($result3))//检查邮箱
            {
                $temp = true;
                $re['content'] = $re['content'].'邮箱已被使用!';
                $row[]= array('username'=>$u['username'],'password'=>$u['password']);
            }
			if(!$temp){
				$row = $as->InsertAdmin($admin);
				$re['state'] = '1';
				$re['content'] = '添加成功';
				echo json_encode($re,JSON_UNESCAPED_UNICODE);
			}
			else{
                echo json_encode($re,JSON_UNESCAPED_UNICODE);
			}
		}

		public function UpdateAdmin()
		{
			$admin = new Admin();
			$admin->userId = $_REQUEST['userid'];
			$admin->password = $_REQUEST['password'];
			$re = array('state'=>'0','content'=>'修改失败');
			$as = new AdminServer();
			$result = $as->UpdateAdmin($admin);
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
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
                    $re = array('state'=>'0','nickname'=>null,'username'=>null);
				    $_SESSION["name"] = $userid;
					$re['state'] = "1";
					$re['nickname'] = $a[3];
					$re['username'] = Security::encrypt($a[1]);
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
            if($result) {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function ChangeStatus(){
            $username = $_REQUEST['username'];
            $status = $_REQUEST['status'];
            $as = new AdminServer();
            $admin = new Admin();
            $admin->username = $username;
            $admin->role = $status;
            $result = $as->UpdateAdmin($admin,"status");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
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
            if($result) {
                AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}
	}
?>
