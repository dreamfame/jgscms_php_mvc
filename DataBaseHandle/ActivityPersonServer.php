<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class ActivityPersonServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function ActivityPersonServer()
		{
			$this->db = new DBHelper("活动人员表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",7,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll($activity_id){
			$sql = "select * from ".$this->db_table." where activity_id = '$activity_id' order by time desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetType(){
            $sql = "select id,name from ActivityPerson_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetActivityPersonById($id){
            $sql = "select * from ".$this->db_table." where id = '$id'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertActivityPerson($activityperson){
            $sql = "insert into ".$this->db_table."(activity_id,phone,nickname,time,prize) values('$activityperson->activity_id','$activityperson->phone','$activityperson->nickname','$activityperson->time','$activityperson->prize')";
            try{
                $result = $this->db->ExecSql($sql,$this->conn);
                return $result;
            }
            catch(Exception $e)
            {
                return false;
            }
            return false;
		}

		public function UpdateActivityPerson($activity,$field){
            $sql = "";
            if($field=="prize"){
                $sql = "update " . $this->db_table . " set ".$field." = '$activity->prize' where id = '$activity->id' and activity_id = '$activity->activity_id'";
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

		public function DeleteActivityPerson($id)
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
