<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class RecommendServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		
		
		public function RecommendServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",3,"name");
			$this->conn = $this->db->Open($this->dbase);
		}	

		public function GetRecommendByCondition($condition,$conditionText,$page,$pageSize){
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

		public function GetRecommend($id)
		{
			$sql = "select * from ".$this->db_table." where id = '$id'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function GetRecommends($page){
			$b = ($page-1)*10;
			$e = ($page)*10;
			$sql = "select * from ".$this->db_table." limit ".$b.",".$e;
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		public function GetTotalPages($condition,$param1){
			if($condition==""||$condition==null){
				$sql = "select count(id) as total from ".$this->db_table;
			}else{
				if($param1==""){
					$sql = "select count(id) as total from ".$this->db_table;
				}
				else{
					$sql = "select count(id) as total from ".$this->db_table." where ".$condition." = '$param1'";
				}
			}
				
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		public function GetPTotalPages($pid){
		    $sql = "select count(id) as total from ".$this->db_table." where planlistid = '$pid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetPlanlist($pid){
		    $sql = "select * from planlist where planlistid = '$pid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function AddPlan($vid,$times,$pid){
		    $sql = "insert into planlist(planlistid,videoid,times) values('$pid','$vid','$times')";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function ValidatePlan($vid,$pid){
		    $sql = "select id from planlist where videoid = '$vid' and planlistid = '$pid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function DelPlan($id){
		    $sql = "delete from planlist where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetList($condition,$conditionText){
		    if($condition==""){
		        $sql = "select * from ".$this->db_table;
		    }
		    else{
		        if($conditionText==""){
		            $sql = "select * from ".$this->db_table;
		        }
		        else{
		            $sql = "select * from ".$this->db_table." where ".$condition." = '$conditionText'";
		        }
		    }
		    $result = $this->db->ExeSql($sql, $this->conn);
		    return $result;
		}
	}
?>
