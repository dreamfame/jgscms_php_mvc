<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class LogServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function LogServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",13,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function RecordLog($log){

        }
	}
?>
