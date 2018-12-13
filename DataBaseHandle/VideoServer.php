<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class VideoServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function VideoServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",4,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

	   public function GetTotalPages($condition,$param1){
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
		
		public function validateName($name){
		    $sql = "select id from ".$this->db_table." where name = '$name'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function getNameById($id){
		    $sql = "select name from ".$this->db_table." where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $name = mysqli_fetch_row($result);
		    return $name[0];
		}
		
		public function GetVideoByCondition($condition,$conditionText,$page,$pageSize){
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
		
		public function GetClientVideo($condition,$value){
			if(isset($condition)){
				$sql = "select * from ".$this->db_table." where ".$condition." = '$value'";
			}else{
				$sql = "select * from ".$this->db_table;
			}
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		public function UpdateImage($v){
			$sql = "update video set gifsrc='$v->src' where id = '$v->id'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}
		
		public function UpdateVideo($video)
		{
			$sql = "update ".$this->db_table." set type = '$video->type',name = '$video->name',intro = '$video->intro',gifsrc = '$video->gifsrc',pngsrc = '$video->pngsrc' where id = '$video->id'";
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

		public function GetVideo($id)
		{
			$sql = "select * from ".$this->db_table." where id = '$id'";
			$result = $this->db->ExeSql($sql, $this->conn);
			return $result;
		}

		public function GetVideoList($id)
		{
			$sql = "select * from ".$this->db_table." where id = '$id'";
			$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}

		public function InsertVideo($video)
		{
			$sql = "insert into ".$this->db_table."(name,pngsrc,gifsrc,type,intro) values('$video->name','$video->pngsrc','$video->gifsrc','$video->type','$video->intro')";
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
		
		public function DeleteVideo($d){
		    $sql = "delete from ".$this->db_table." where id = '$d'";
		    $sql1 = "delete from planlist where videoid = '$d'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $this->db->ExeSql($sql1,$this->conn);
		    return $result;
		}
		
		public function TypeQuery($type){
		    if($type==""){
		        $sql = "select * from ".$this->db_table;
		    }
		    else{
		        $sql = "select * from ".$this->db_table." where type like '%$type%'";
		    }
		    $result = $this->db->ExeSql($sql, $this->conn);
		    return $result;
		}
	}
?>
