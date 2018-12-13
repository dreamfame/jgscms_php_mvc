<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class UserServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function UserServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",1,"name");
			$this->conn = $this->db->Open($this->dbase);
		}
		
		public function ChangeImage($id,$pic){
			$sql = "update user set headImg='$pic' where userId = '$id'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
	    public function getNameById($id){
		    $sql = "select name from ".$this->db_table." where userId = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $name = mysqli_fetch_row($result);
		    return $name[0];
		}
		
	   public function GetTotalPages($condition,$param1){
			if($condition==""||$condition==null){
				$sql = "select count(userId) as total from ".$this->db_table;
			}else{
				if($param1==""){
					$sql = "select count(userId) as total from ".$this->db_table;
				}
				else if($param1!=""){
					$sql = "select count(userId) as total from ".$this->db_table." where ".$condition." = '$param1'";
				}
			}
				
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}

		public function VerificationUserInfo($user)
		{
			$sql = "select * from ".$this->db_table." where userId = '$user->userId'";
			$result = $this->db->ExeSql($sql,$this->conn);
			$row = mysqli_fetch_row($result);
			if($row[0]=="")
			{
				$this->db->Close($this->conn);
				return false;
			}
			if(Security::encrypt($user->Password)!=$row[2])
			{
				$this->db->Close($this->conn);
				return false;
			}
			$this->db->Close($this->conn);
			return true;
		}

		public function UpdateUser($user)
		{
			$sql = "update ".$this->db_table." set phone = '$user->phone',sex = '$user->sex',name = '$user->name',birth = '$user->birth' where userId = '$user->userId'";
			try {
				$result = $this->db->ExeSql($sql, $this->conn);
				return $result;
			} catch (Exception $e) {
				return false;
			}
			return false;
		}

		public function GetUser($userId)
		{
			$sql = "select * from ".$this->db_table." where userId = '$userId'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}

		public function GetUserList($page)
		{
		    $b = ($page-1)*10;
		    $e = 10;
			$sql = "select * from ".$this->db_table." limit ".$b.",".$e;
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		public function GetUserByCondition($condition,$conditionText,$page,$pageSize){
		    $b = ($page-1)*$pageSize;
			if($condition==""){
				$sql = "select * from ".$this->db_table." limit ".$b.",".$pageSize;
			}
			else{
				if($conditionText==""){
					$sql = "select * from ".$this->db_table." limit ".$b.",".$pageSize;
				}
				else{
					$sql = "select * from ".$this->db_table." where ".$condition." = '$conditionText' limit ".$b. ", ".$pageSize;
				}
			}
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}

		public function InsertUser($user)
		{
			$sql = "insert into ".$this->db_table."(userId,password,name,birth,sex,phone,headImg) values('$user->userId','$user->password','$user->name','$user->birth','$user->sex','$user->phone','$user->headImg')";
            $result = $this->db->ExeSql($sql,$this->conn);
            return $result;
		}
		
		public function validateOldPwd($id,$pwd){
		    $sql = "select password from ".$this->db_table." where userId = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $db_pwd = mysqli_fetch_row($result)[0];
		    if($db_pwd==$pwd){
		        return true;
		    }else{
		        return false;
		    }
		}
		
		public function changeCode($id,$pwd){
		    $sql = "update ".$this->db_table." set password = '$pwd' where userId = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function validatePhone($phone){
		    $sql = "select userId from ".$this->db_table." where phone = '$phone'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $r = mysqli_fetch_row($result);
		    return $r[0];
		}
	}
?>
