<?php
	require_once '../Model/User.php';
	require_once '../DataBaseHandle/UserServer.php';
	header("Content-Type: text/html;charset=utf-8");
	Class UserControl
	{
		private $destinationPath;
		private $thumbdestinationPath;
		private $thumbtempPath;
		private $tempPath;
		private $addName;
		
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "add":
					$this->AddUser();
					break;
				case "del":
					break;
				case "edit":
					$this->UpdateUser();
					break;
				case "query":
					$this->GetUser();
					break;
				case "login":
					$this->ValidateLogin();
					break;
				case "conditionQuery":
					$this->queryCondition();
					break;
				case "uploadImg":
					$this->changeImg();
					break;
				case "updateImage":
					$this->UpdateImage();
					break;
				case "paging":
				    $this->GetTotalRecord();
				    break;
				case "private":
				    $this->GetSelfInfo();
				    break;
				case "changeCode":
				    $this->ChangeCode();
				    break;
				case "reg":
				    $this->Reg();
				    break;
				case "validatePhone":
				    $this->validatePhone();
				    break;
			}
		}
		
		public function GetTotalRecord(){
		    $condition = $_REQUEST["condition"];
		    $param1 = $_REQUEST["conditionText1"];
		    $param2 = $_REQUEST["conditionText2"];
		    $us = new UserServer();
		    $result = $us->GetTotalRecord($condition,$param1,$param2);
		    $re = array('state'=>'0','content'=>'null');
		    while($r = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        $re['content'] = $r["total"];
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function changeImg(){
		    $uploadFile = $_FILES['avatar'];
		    $this->addName = date("Y").date("m").date("j").date("H").date("i").date("s");
		    $this->destinationPath = "../Resources/headimg"."\\".$this->addName.$uploadFile["name"];
		    $this->tempPath = $uploadFile["tmp_name"];
		    if(is_uploaded_file($uploadFile["tmp_name"]))
		    {
		        $src = "/Resources/headimg/".$this->addName.$uploadFile["name"];
		        $this->UploadFileToServer();
		        $showsrc = "..".$src;
		        echo "<script type='text/javascript'>window.top.document.getElementById('previewImg').setAttribute('src','".$showsrc."');</script>";
		    }
		}
		
		public function UpdateImage(){
		    session_start();
		    $userid = $_SESSION['sid'];
		    $pic = $_REQUEST['pic'];
		    $us = new UserServer();
		    $result = $us->ChangeImage($userid,$pic);
		    if($result){
		       echo '上传头像成功';
		    }
		    else{
		       echo "上传头像失败";
		    }
		}
		
		public function UploadFileToServer()
		{
			move_uploaded_file ($this->tempPath,iconv ( "UTF-8", "gb2312", $this->destinationPath ) );
		}
		
		
		public function queryCondition(){
			$page = $_REQUEST["page"];
		    $pageSize = $_REQUEST["pageSize"];
			$condition = $_REQUEST["condition"];
			$conditionText = $_REQUEST["conditionText"];
			$us = new UserServer();
			$result = $us->GetUserByCondition($condition,$conditionText,$page,$pageSize);
		    $result1 = $us->GetTotalPages($condition,$conditionText);
			$re = array('state'=>'0','content'=>null,'totalPages'=>0);
			while($r = mysqli_fetch_array($result1))
			{
			    $re['totalPages'] = $r["total"];
			}
			while ($u = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('userId'=>$u['userId'],'name'=>$u['name'],'birth'=>$u['birth'],'sex'=>$u['sex'], 'phone'=>$u['phone'],'headimg'=>$u['headImg']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function AddUser()
		{
			$user = new User();
			$user->userId = $user->phone = $_REQUEST['phone'];
			$user->userName = $_REQUEST['username'];
			$user->password = $_REQUEST['password'];
			$user->sex = $_REQUEST['sex'];
			$us = new UserServer();
			$result = $us->GetUser($user->userId);
			$re = array('state'=>'0','content'=>'已存在该手机号用户');
			$temp = false;
			while ($u = mysqli_fetch_array($result))
			{
				$temp = true;
				$row[]= array('userId'=>$u['userId'],'userName'=>$u['userName'],'password'=>$u['password'],'sex'=>$u['sex'], 'phone'=>$u['phone'],'headimg'=>$u['headImg']);
			}
			if(!$temp){
				$row = $us->InsertUser($user);
				$re['state'] = '1';
				$re['content'] = '注册成功';
				echo json_encode($re,JSON_UNESCAPED_UNICODE);
				return;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function UpdateUser()
		{
			$us = new UserServer();
			$user = new User();
			session_start();
			$user->userId = $_SESSION['sid'];
			$user->sex = $_REQUEST['sex'];
			$user->name = $_REQUEST['name'];
			$user->phone = $_REQUEST["phone"];
			$user->birth = $_REQUEST['birth'];
			$re = array('state'=>'0','content'=>'保存失败');
			$result = $us->UpdateUser($user);
			if($result){
				$re['state'] = '1';
				$re['content'] = '保存成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function GetUser()
		{
			$userid = $_REQUEST['userid'];
			$us = new UserServer();
			if($userid==""){
				$result = $us->GetUserList();
			}
			else{
				$result = $us->GetUser($userid);
			}
			$re = array('state'=>'0','content'=>null);
			while ($u = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('userId'=>$u['userId'],'userName'=>$u['userName'],'name'=>$u['name'],'birth'=>$u['birth'],'sex'=>$u['sex'], 'phone'=>$u['phone'],'headimg'=>$u['headImg']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}
		
		public function GetSelfInfo(){
		    session_start();
		    $userid = $_SESSION['sid'];
		    $us = new UserServer();
		    $result = $us->GetUser($userid);
		    $re = array('state'=>'0','content'=>null);
		    while ($u = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        $row[]= array('userId'=>$u['userId'],'userName'=>$u['userName'],'name'=>$u['name'],'birth'=>$u['birth'],'sex'=>$u['sex'], 'phone'=>$u['phone'],'headimg'=>$u['headImg']);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function changeCode(){
		    session_start();
		    $userid = $_SESSION['sid'];
		    $oldpwd = $_REQUEST['oldpwd'];
		    $newpwd = $_REQUEST['newpwd'];
		    $us = new UserServer();
		    $result = $us->validateOldPwd($userid,$oldpwd);
		    if($result){
		        $r = $us->changeCode($userid,$newpwd);
		        if($r){
		            echo "修改密码成功";
		        }else{
		            echo "修改密码失败";
		        }
		    }
		    else{
		        echo "原密码错误";
		    }
		}
		
		public function ValidateLogin(){
			$userid = $_REQUEST['userid'];
			$password = $_REQUEST['pwd'];
			$us = new UserServer();
			$result = $us->GetUser($userid);
			$re = array('state'=>'0','content'=>null);
			$u = mysqli_fetch_row($result);
			if($u[0]==""){
				$re['content']="用户名不存在";
			}
			else{
				$re['state'] = "1";
				$re['content'] = "";
				/*if($u[1] == $password){
					$re['state'] = "1";
					$re['content'] = "";
				}
				else{
					$re['content'] = "密码错误";
				}*/
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function Reg(){
		    $user = new User();
		    $user->phone = $_REQUEST["phone"];
		    $user->password = $_REQUEST["password"];
		    $user->name = $_REQUEST["name"];
		    $user->sex = $_REQUEST["sex"];
		    $user->birth = $_REQUEST["birth"];
		    $user->headImg = "default.jpg";
		    $us = new UserServer();
		    $result = $us->InsertUser($user);
		    if($result){
		        echo "1";
		    }
		    else{
		        echo "0";
		    }
		}
		
		public function validatePhone(){
		    $phone = $_REQUEST["phone"];
		    $us = new UserServer();
		    $u = $us->validatePhone($phone);
		    if($u!=""||$u!=null){
		        echo "手机号已被注册";   
		    }
		    else{
		        echo "手机号可以使用";
		    }
		}
	}
	$uc = new UserControl();
	$uc->JugdeOperate();
?>
