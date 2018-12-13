<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class CustomerServer
	{
		public $db;
		public $conn;
		public $dbase="insai";
		public $db_table="customer";
		public function CustomerServer()
		{
			$this->db = new DBHelper();
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetCustomer($page)
		{
		    $b = ($page-1)*20;
		    $e = 20;
		    $sql = "select id,name,sex,phone,telephone,birth from ".$this->db_table." limit ".$b.",".$e;
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function getNameById($id){
		     $sql = "select name from ".$this->db_table." where id = '$id'";
		     $result = $this->db->ExeSql($sql,$this->conn);
		     $name = mysqli_fetch_row($result);
		     return $name[0];
		}
		
		public function getSpNameById($id){
		    $sql = "select name from specialist where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $name = mysqli_fetch_row($result);
		    return $name[0];
		}
		
		public function getPhoneById($id){
		    $sql = "select phone from customer where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $name = mysqli_fetch_row($result);
		    return $name[0];
		}
		
		public function getIdByPhone($phone){
		    $sql = "select id from ".$this->db_table." where phone = '$phone'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $id = mysqli_fetch_row($result);
		    return $id[0];
		}
		
		public function getNameByPhone($phone){
		    $sql = "select name from ".$this->db_table." where phone = '$phone'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $name = mysqli_fetch_row($result);
		    return $name[0];
		}
		
		public function GetTotalRecord($condition,$param1){
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
		
		public function GetCustomerByCondition($conditionType,$conditionText,$page){
		    $b = ($page-1)*20;
		    $e = 20;
		    if($conditionType=="0"){
		        $sql = "select id,name,sex,phone,telephone,birth from ".$this->db_table." limit ".$b.",".$e;
		    }
		    else
		    {
		        if($conditionText==""){
		            $sql = "select id,name,sex,phone,telephone,birth from ".$this->db_table." limit ".$b.",".$e;
		        }
		        else{
		            $sql = "select id,name,sex,phone,telephone,birth from ".$this->db_table." where ".$conditionType." = '$conditionText' limit ".$b.",".$e;
		        }
		    }
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
	}
?>
