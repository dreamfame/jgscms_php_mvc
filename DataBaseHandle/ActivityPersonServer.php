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
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",7,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select * from ".$this->db_table;
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
                $this->db->ExeSql($sql,$this->conn);
                return true;
            }
            catch(Exception $e)
            {
                return false;
            }
            return false;
		}

		public function UpdateActivityPerson($activity,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$activity->top' where id = '$activity->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$activity->show' where id = '$activity->id'";
			}
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set name = '$activity->name',created_at = '$activity->created_at',brief = '$activity->brief',recommend = '$activity->recommend',intro = '$activity->intro' where id = '$activity->id'";
            }
            else if($field=="activity_map"){
                $sql = "update " . $this->db_table . " set ".$field." = '$activity->activity_map' where id = '$activity->id'";
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
