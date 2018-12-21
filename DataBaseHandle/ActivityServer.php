<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class ActivityServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function ActivityServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",6,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select activity.id,num,activity.name,activity.pic,activity.date,activity.join,activity.intro,activity.prize,activity.prize_way,activity.phone,activity.`enable` from activity";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetType(){
            $sql = "select id,name from Activity_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetActivityById($id){
            $sql = "select * from ".$this->db_table." where id = '$id'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertActivity($activity){
            $sql = "insert into ".$this->db_table."(name,date,pic,intro,enable,prize,prize_way,activity.join,phone) values('$activity->name','$activity->date','$activity->pic','$activity->intro','$activity->enable','$activity->prize','$activity->prize_way','$activity->join','$activity->phone')";
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

		public function UpdateActivity($activity,$field){
            $sql = "";
            if($field=="enable"){
                $sql = "update " . $this->db_table . " set ".$field." = '$activity->enable' where id = '$activity->id'";
            }
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set name = '$activity->name',date = '$activity->date',phone = '$activity->phone',prize = '$activity->prize',intro = '$activity->intro',prize_way = '$activity->prize_way',activity.join = '$activity->join' where id = '$activity->id'";
            }
            else if($field=="pic"){
                $sql = "update " . $this->db_table . " set ".$field." = '$activity->pic' where id = '$activity->id'";
            }
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

		public function DeleteActivity($id)
        {
            $sql="delete from ".$this->db_table." where id= '$id'";
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
