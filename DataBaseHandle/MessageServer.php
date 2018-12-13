<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class MessageServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		
		
		public function MessageServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",9,"name");
			$this->conn = $this->db->Open($this->dbase);
		}
		
		public function GetRemind(){
		    $sql = "select customerid from ".$this->db_table." where reply is null group by customerid";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetTotalRemind(){
		    $sql = "select count(id) from ".$this->db_table." where reply is null";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $num = mysqli_fetch_row($result);
		    return $num[0];
		}
		
		public function GetRemindNum($cid){
		    $sql = "select count(id) from ".$this->db_table." where reply is null and customerid = '$cid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $num = mysqli_fetch_row($result);
		    return $num[0];
		}

		public function GetMessage($userid,$page,$pageSize){
		    $b = ($page-1)*$pageSize;
		    $sql = "select id,customerid,specialid,message,reply,sendtime,replytime from ".$this->db_table." where customerid = '$userid' order by sendtime DESC limit ".$b.",".$pageSize;
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetReply($id){
		    $sql = "select id,customerid,specialid,message,reply,sendtime,replytime from ".$this->db_table." where customerid = '$id' and reply is not null and getMsg = 0";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function ReplyMessage($id,$sid,$reply){
		    date_default_timezone_set('PRC');
		    $replyTime = date('y-m-d g:i:s',time());
		    $sql = "update ".$this->db_table." set reply = '$reply',replytime = '$replyTime',specialid='$sid' where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function AddMessage($message){
		    $sql = "insert into ".$this->db_table."(customerid,message,sendtime) values('$message->customerid','$message->message','$message->sendtime')";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetTotalPages($userid){
		    $sql = "select count(id) as total from ".$this->db_table." where customerid = '$userid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}

		public function DeleteRecommend($dp)
		{
			$sql="delete from ".$this->db_table." where id= '$dp'";
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
		

		public function GetMessageByCondition($condition,$conditionText,$page,$pageSize){
			$b = ($page-1)*$pageSize;
			if($condition==""){
				$sql = "select * from ".$this->db_table;
			}
			else{
				if($conditionText==""){
					$sql = "select * from ".$this->db_table." limit ".$b.",".$pageSize;
				}
				else{
					$sql = "select * from ".$this->db_table." where ".$condition." = ". $conditionText.  " limit ".$b. ", ".$pageSize;
				}
			}
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function ChangeState($n){
		    $sql = "update ".$this->db_table." set getMsg = 1 where id = '$n'";
		    $result = $this->db->ExeSql($sql, $this->conn);
		    return $result;
		}
	
		public function UpdateRecommendBy($recommend)
		{
	
			$sql = "update ".$this->db_table." set shouwan = '$recommend->shouwan',shoubi = '$recommend->shoubi',yaobu = '$recommend->yaobu',zhoubu = '$recommend->zhoubu' where userId = '$recommend->userId'";
			
			
			
			 echo($recommend->shouwan);
			 echo(    $recommend->userId);
			 echo(   shouwan);
		
			
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

		public function GetUserRecommendList($userId)
		{
			$sql = "select * from ".$this->db_table;

			if(isset($userId) && !isset($arg1)){
				$sql = "select * from ".$this->db_table." where userId = '$userId'";
			}

			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}

		public function InsertRecommend($recommend)
		{
			$sql = "insert into ".$this->db_table."(userId,shouwan,shoubi,yaobu,zhoubu) values('$recommend->userId','$recommend->shouwan','$recommend->shoubi','$recommend->yaobu','$recommend->zhoubu')";
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
		
		public function GetTotalRecord($condition,$param1){
			if($condition==""||$condition==null){
				$sql = "select count(id) as total from ".$this->db_table;
			}else{
				if($param1==""){
					$sql = "select count(id) as total from ".$this->db_table;
				}
				else if($param1!=""){
					$sql = "select count(id) as total from ".$this->db_table." where ".$condition." = '$param1'";
				}
			}
				
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		
	}
?>
