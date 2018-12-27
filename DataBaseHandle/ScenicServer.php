<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class ScenicServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function ScenicServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",2,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select scenic.id,scenic.area_id,area.name as area_name,scenic.see,scenic.name,scenic.brief,scenic.intro,scenic.recommend,scenic.isshow,scenic.top,scenic.created_at,scenic.updated_at from scenic LEFT JOIN area on scenic.area_id = area.id order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetShow(){
            $sql = "select scenic.id,scenic.area_id,area.name as area_name,scenic.see,scenic.name,scenic.brief,scenic.intro,scenic.recommend,scenic.isshow,scenic.top,scenic.created_at,scenic.updated_at from scenic LEFT JOIN area on scenic.area_id = area.id where scenic.isshow = 1 order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function VerifyName($area_id,$name){
            $sql = "select id from ".$this->db_table." where area_id = '$area_id' and name = '$name'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetName(){
            $sql = "select id,name from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryScenic($where){
            $sql = "select * from ".$this->db_table.$where." order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertScenic($scenic){
            $sql = "insert into ".$this->db_table."(area_id,name,recommend,brief,intro,isshow,top,updated_at,created_at,see) values('$scenic->area_id','$scenic->name','$scenic->recommend','$scenic->brief','$scenic->intro','$scenic->isshow','$scenic->top','$scenic->updated_at','$scenic->created_at','$scenic->see')";
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

		public function UpdateScenic($scenic,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$scenic->top' where id = '$scenic->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$scenic->show' where id = '$scenic->id'";
			}
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set name = '$scenic->name',created_at = '$scenic->created_at',brief = '$scenic->brief',intro = '$scenic->intro',recommend = '$scenic->recommend' where id = '$scenic->id'";
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

		public function DeleteScenic($id)
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

        public function BatchDeleteScenic($str){
            $sql="delete from ".$this->db_table." where id in {$str}";
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
