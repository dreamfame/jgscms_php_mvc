<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class InfoServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function InfoServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",0,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function InsertInfo($info)
		{
			$sql = "insert into info values('$info->id','$info->nickname','$info->height','$info->weight','$info->location','$info->vocation','$info->company','$info->headimg','$info->createAt')";
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
		
		public function GetNick($phone){
		    $sql = "select nickname from info where id = '$phone'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $nick = mysqli_fetch_row($result);
		    return $nick[0];
		}
		
		public function validatePhone($phone){
		    $sql = "select id from info where phone = '$phone'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $a = mysqli_fetch_row($result);
		    if($a[0]==""||$a==null){
		        return true;
		    }
		    else{
		        return false;
		    }
		}
		
		public function GetInfo($id){
		    $sql = "select * from info where id = '$id'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function UpdateInfo($info){
		    $sql = "update info set nickname = '$info->nickname',height = '$info->height',weight = '$info->weight',location = '$info->location',vocation = '$info->vocation',company = '$info->company' where id = '$info->id'";
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
		
		public function GetUserNum(){
		    $sql = "select count(id) from info";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $u = mysqli_fetch_row($result);
		    return $u[0];
		}
		
		public function GetChartData(){
		    $sql = "select day(createAt) as day,count(id) as num from info group by createAt";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    return $result;
		}
		
		public function GetTodayNewUserNum(){
		    $date = "20".date('y-m-d',time());
		    $sql = "select count(id) from info where createAt = '$date'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $u = mysqli_fetch_row($result);
		    return $u[0];
		}
		
		public function GetYesNewUserNum(){
		    $date = date("Y-m-d",strtotime("-1 day"));
		    $sql = "select count(id) from info where createAt = '$date'";
		    $result = $this->db->ExeSql($sql,$this->conn);
		    $u = mysqli_fetch_row($result);
		    return $u[0];
		}
		
		public function UpdateImg($id,$img){
		    $sql = "update info set headimg = '$img' where id = '$id'";
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
	}
?>
