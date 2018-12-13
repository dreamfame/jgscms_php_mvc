<?php
	require_once '../Model/Admin.php';
	require_once '../DataBaseHandle/AdminServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	Class AdminControl
	{
		public function JugdeOperate($operate)
		{
			switch($operate)
			{
				case "add":
                    AdminControl::AddAdmin();
					break;
				case "del":
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
			}
		}

		public function AddAdmin()
		{
			$admin = new Admin();
			$admin->userId = $_REQUEST['userid'];
			$admin->password = $_REQUEST['password'];
			$as = new AdminServer();
			$result = $as->GetAdmin($admin->userId);
			$re = array('state'=>'0','content'=>'已存在该用户');
			$temp = false;
			while ($u = mysqli_fetch_array($result))
			{
				$temp = true;
				$row[]= array('userId'=>$u['userId'],'password'=>$u['password']);
			}
			if(!$temp){
				$row = $as->InsertAdmin($admin);
				$re['state'] = '1';
				$re['content'] = '注册成功';
				echo json_encode($re,JSON_UNESCAPED_UNICODE);
				return;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
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
			$userid = $_REQUEST['userid'];
			$as = new AdminServer();
			$result = $as->GetAdmin($userid);
			$re = array('state'=>'0','content'=>null);
			while ($u = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('userId'=>$u['userId'],'password'=>$u['password']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
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
	}
?>
