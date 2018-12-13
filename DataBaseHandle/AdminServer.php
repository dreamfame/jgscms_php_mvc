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
			$this->db = new DBHelper();
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
			$sql = "insert into ".$this->db_table." values('$admin->userId','$admin->password')";
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

		public function UpdateAdmin($admin)
		{
			$sql = "update ".$this->db_table." set password = '$admin->password' where userId = '$admin->userId'";
			try{
				$this->db->ExeSql($sql,$this->conn);
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
			return true;
		}

		public function GetAdmin($userId)
		{
			$sql = "select id,username,password,nickname from ".$this->db_table." where username = '$userId'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}
	}
?>
