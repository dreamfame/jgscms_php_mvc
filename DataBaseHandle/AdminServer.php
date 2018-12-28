<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class AdminServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function AdminServer()
		{
			$this->db = new DBHelper("管理员表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",0,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function VerificationAdmin($admin)
		{
			$sql = "select * from ".$this->db_table." where username = '$admin->username'";
			$result = $this->db->ExeSql($sql,$this->conn);
			$row = mysqli_fetch_row($result);
			if($row[0]=="")
			{
				$this->db->Close($this->conn);
				return false;
			}
			if(Security::encrypt($admin->Password)!=$row[1])
			{
				$this->db->Close($this->conn);
				return false;
			}
			$this->db->Close($this->conn);
			return true;
		}

		public function InsertAdmin($admin)
		{
			$sql = "insert into ".$this->db_table."(username,head_pic,nickname,password,age,phone,password_reset_token,email,updated_at,created_at,role,status) values('$admin->username','$admin->head_pic','$admin->nickname','$admin->password','$admin->age','$admin->phone','$admin->password_reset_token','$admin->email','$admin->updated_at','$admin->created_at','$admin->role','$admin->status')";
			$result = $this->db->ExecSql($sql,$this->conn);
			return $result;
		}

		public function UpdateAdmin($admin,$field)
		{
			$sql = "";
			if($field=="role"){
				$sql = "update " . $this->db_table . " set ".$field." = '$admin->role' where username = '$admin->username'";
			}
			else if($field=="status"){
                $sql = "update " . $this->db_table . " set ".$field." = '$admin->status' where username = '$admin->username'";
			}
            else if($field=="password"){
                $sql = "update " . $this->db_table . " set ".$field." = '$admin->password' where username = '$admin->username'";
            }
            else if($field=="all"){
            	if($admin->head_pic==""){
                    $sql = "update " . $this->db_table . " set nickname = '$admin->nickname',age = '$admin->age',phone = '$admin->phone',email = '$admin->email' where username = '$admin->username'";
				}
				else{
                    $sql = "update " . $this->db_table . " set nickname = '$admin->nickname',age = '$admin->age',phone = '$admin->phone',email = '$admin->email',head_pic = '$admin->head_pic' where username = '$admin->username'";
                }
			}
			$result = $this->db->ExecSql($sql,$this->conn);
            return $result;
		}

        public function VerifyInfo($where)
        {
            $sql = "select if( exists(select id from ".$this->db_table.$where." limit 1), 1, 0) as result";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function VerifyPwd($username)
        {
            $sql = "select password from ".$this->db_table." where username = '$username' limit 1";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function VerifyEditInfo($where,$username){
            $sql = "select if( exists(select id from ".$this->db_table.$where." and username <> '$username' limit 1 ), 1, 0) as result";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

		public function QueryAdmin($where)
		{
            $sql = "select * from ".$this->db_table.$where;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

		public function GetAdmin($userId)
		{
			$sql = "select id,username,password,nickname,phone,email,head_pic,role,status from ".$this->db_table." where username = '$userId'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetAdminByCondition($condition,$content)
		{
            $sql = "select id,username,password,nickname,phone,email from ".$this->db_table." where ".$condition." = '$content'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function GetAll(){
            $sql = "select id,username,nickname,phone,email,age,status,role from ".$this->db_table." where id <> 1";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

		public function DeleteAdmin($id){
            $sql="delete from ".$this->db_table." where id= '$id'";
            try{
                $this->db->ExeSql($sql,$this->conn);
                return true;
            }
            catch(Exception $e)
            {
                return false;
            }
            return false;
		}

		public function BatchDeleteAdmin($str){
            $sql="delete from ".$this->db_table." where id in {$str}";
            try{
                $this->db->ExeSql($sql,$this->conn);
                return true;
            }
            catch(Exception $e)
            {
                return false;
            }
            return false;
		}
	}
?>
