<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class StoreServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function StoreServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",10,"name");
			$this->conn = $this->db->Open($this->dbase);
		}
		
		public function GetStore($phone){
		    $sql = "select * from ".$this->db_table." where phone = '$phone'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}

		public function GetStoreNum($pid){
		    $sql = "select count(id) as storeNum from ".$this->db_table." where pid = '$pid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $n = mysqli_fetch_row($result);
		    return $n[0]['storeNum'];
		}
		
		public function AddStore($pid,$phone){
		    $sql = "insert into ".$this->db_table."(pid,phone) values('$pid','$phone')";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
	}
?>
