<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class IntegralServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function IntegralServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",7,"name");
			$this->conn = $this->db->Open($this->dbase);
		}
		
		public function InsertDefault($id){
		    $sql = "insert into integral(userid,integral,time) values('$id',0,0)";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetIntegrals($userid,$client){
		    if($userid==""||$userid==null)
		    {
		        if($client=="1"){
		            $sql = "select * from ".$this->db_table." order by integral desc limit 0,5";
		        }else{
		          $sql = "select * from ".$this->db_table." order by integral desc";
		        }
		    }else{
		        $sql = "select * from ".$this->db_table." where userid = '$userid'";
		    }
     		$result = $this->db->ExeSql($sql,$this->conn);
			return $result;
		}
		
		public function GetMyRank($userid,$type){
		    if($type=="time"){
		        $sql = "select * from ".$this->db_table." order by time desc";
		    }else{
		        $sql = "select * from ".$this->db_table." order by integral desc";
		    }
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $num = 1;
		    while($r = mysqli_fetch_array($result)){
		        if($r['userid']==$userid){
		            break;
		        }
		        else{
		            $num++;
		        }
		    }
		    return $num;
		}
		
		public function GetTimes($userid,$client){
		    if($userid==""||$userid==null)
		    {
		        if($client=="1"){
		            $sql = "select * from ".$this->db_table." order by time desc limit 0,5";
		        }else{
		          $sql = "select * from ".$this->db_table." order by time desc";
		        }
		    }else{
		        $sql = "select * from ".$this->db_table." where userid = '$userid'";
		    }
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function UpdateIntegral($userid,$score){
		    $sql = "update ".$this->db_table." set integral = ".$score." where userid = '$userid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function UpdateTime($userid,$time){
		    $sql = "update ".$this->db_table." set time = ".$time." where userid = '$userid'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
	}
?>
