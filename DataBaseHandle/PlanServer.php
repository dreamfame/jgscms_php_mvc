<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class PlanServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function PlanServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",6,"name");
			$this->conn = $this->db->Open($this->dbase);
		}
		
		public function validateTime($id,$time){
			$sql = "select * from ".$this->db_table." where userId='$id' and time='$time'";
			$result = $this->db->ExeSql($sql,$this->conn);
			$row = mysqli_fetch_row($result);
			if($row[0]=="")
			{
				$this->db->Close($this->conn);
				return false;
			}
			$this->db->Close($this->conn);
			return true;
		}
		
		public function Insertplan($plan)
		{
			$sql = "insert into ".$this->db_table."(userId,time,sbsz,lbyd,sjhw,zbny,kxyd,k1,k2,k3,k4,k5,rt1,rt2,rt3,rt4,rt5) values('$plan->userId','$plan->time','$plan->sbsz','$plan->lbyd','$plan->sjhw','$plan->zbny',0,'$plan->k1','$plan->k2','$plan->k3','$plan->k4','$plan->k5','$plan->rt1','$plan->rt2','$plan->rt3','$plan->rt4','$plan->rt5')";
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
		
		public function Updateplan($plan){
			$sql = "update ".$this->db_table." set sbsz = '$plan->sbsz',lbyd = '$plan->lbyd',kxyd='$plan->kxyd',sjhw = '$plan->sjhw',zbny = '$plan->zbny',k1 = '$plan->k1',k2 = '$plan->k2',k3 = '$plan->k3',k4 = '$plan->k4',k5 = '$plan->k5',rt1 = '$plan->rt1',rt2 = '$plan->rt2',rt3 = '$plan->rt3',rt4 = '$plan->rt4',rt5 = '$plan->rt5' where userId = '$plan->userId' and time = '$plan->time'";
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
		
		public function GetPlan($id,$date){
			if(isset($date)){
				$sql = "select * from ".$this ->db_table." where userid = '$id' and time = '$date'";
			}
			else{
				$sql = "select * from ".$this->db_table." where userid = '$id'";
			}
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		public function DelPlan($id,$date,$type){
			$sql = "update ".$this->db_table." set ".$type." = 0 where userid = '$id' and time = '$date'";
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
	}
?>
