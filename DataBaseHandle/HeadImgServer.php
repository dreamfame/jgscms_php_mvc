<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class HeadImgServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function HeadImgServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",5,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetHeadImg($userId)
		{
			$sql = "select * from ".$this->db_table." where userId = '$userId'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function ChangeImage($h){
			$sql = "update headimg set headimg='$h->headImg' where userid = '$h->userId'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
	}
?>
