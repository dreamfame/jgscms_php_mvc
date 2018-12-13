<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class SportsServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function SportsServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",2,"name");
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
		
		public function GetTotalRecord($condition,$param1,$param2){
		    if($condition==""||$condition==null){
		      $sql = "select count(id) as total from ".$this->db_table;
		    }else{
		        if($param1==""&&$param2==""){
		            $sql = "select count(id) as total from ".$this->db_table;
		        }
		        else if($param1!=""&&$param2==""){
		            $sql = "select count(id) as total from ".$this->db_table." where ".$condition." = '$param1'";
		        }
		        else{
		            $sql =  "select count(id) as total from ".$this->db_table." where time>='$param1' and time<='$param2'";
		        }
		    }
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}

		public function UpdateSportsByTime($sports)
		{
			$sql = "update ".$this->db_table." set shouwan = '$sports->shouwan',shoubi = '$sports->shoubi',yaobu = '$sports->yaobu',zhoubu = '$sports->zhoubu' where userId = '$sports->userId' and time = '$sports->time'";
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
		
		public function GetSportsByCondition($condition,$conditionText1,$conditionText2,$page)
		{
		    $b = ($page-1)*10;
		    $e = 10;
			if($condition==""){
				$sql = "select * from ".$this->db_table." limit ".$b.",".$e;
			}
			else{
				if($conditionText1==""){
					$sql = "select * from ".$this->db_table." limit ".$b.",".$e;
				}
				else{
					if($conditionText2==""){
						$sql = "select * from ".$this->db_table." where ".$condition." = '$conditionText1' limit ".$b.",".$e;
					}
					else{
						$sql = "select * from ".$this->db_table." where time>='$conditionText1' and time<='$conditionText2' limit ".$b.",".$e;
					}
				}
			}
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function GetSportsByTime($userid, $time)
		{
			$sql = "select * from ".$this->db_table." where userId='$userid' and time='$time'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}

		public function GetSports($id)
		{
			$sql = "select * from ".$this->db_table." where id = '$id'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function DelSports($id){
		    $sql = "delete from ".$this->db_table." where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetSportsList($page){
		    $b = ($page-1)*10;
		    $e = 10;
			$sql = "select * from ".$this->db_table." limit ".$b.",".$e;
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}

		public function GetUserSportsList($arg0, $value0, $arg1, $value1)
		{
			$sql = "select * from ".$this->db_table;
			if(isset($arg0) && !isset($arg1)){
				$sql = "select * from ".$this->db_table." where ".$arg0."=".$value0;
			} else if(!isset($arg0) && isset($arg1)){
				$sql = "select * from ".$this->db_table." where ".$arg1."=".$value1;
			} else if(isset($arg0) && isset($arg1)){
				$sql = "select * from ".$this->db_table." where ".$arg0."=".$value0." and ".$arg1."=".$value1;
			}
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}

		public function InsertSports($sports)
		{
			$sql = "insert into ".$this->db_table."(userId,time,sbsz,lbyd,sjhw,zbny,kxyd,finishtime) values('$sports->userId','$sports->time','$sports->sbsz','$sports->lbyd','$sports->sjhw','$sports->zbny',0,'$sports->finishtime')";
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
		
		public function UpdateSports($sports){
			$sql = "update ".$this->db_table." set sbsz = '$sports->sbsz',lbyd = '$sports->lbyd',sjhw = '$sports->sjhw',zbny = '$sports->zbny',kxyd = '$sports->kxyd' where id = '$sports->id'";
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
	}
?>
