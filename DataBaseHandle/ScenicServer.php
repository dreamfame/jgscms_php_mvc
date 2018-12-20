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
			$sql = "select * from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetType(){
            $sql = "select id,name from Scenic_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetScenicById($id){
            $sql = "select * from ".$this->db_table." where id = '$id'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertScenic($scenic){
            $sql = "insert into ".$this->db_table."(name,recommend,brief,intro,isshow,top,updated_at,created_at,see) values('$scenic->name','$scenic->recommend','$scenic->brief','$scenic->intro','$scenic->isshow','$scenic->top','$scenic->updated_at','$scenic->created_at','$scenic->see')";
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
	}
?>
