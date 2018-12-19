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

		public function InsertScenic($Scenic){
            $sql = "insert into ".$this->db_table."(type,title,content,isshow,top,updated_at,created_at,operator,see,keyword,abstract,pic) values('$Scenic->type','$Scenic->title','$Scenic->content','$Scenic->show','$Scenic->top','$Scenic->updated_at','$Scenic->created_at','$Scenic->operator','$Scenic->see','$Scenic->keyword','$Scenic->abstract','$Scenic->pic')";
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

		public function UpdateScenic($Scenic,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Scenic->top' where id = '$Scenic->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Scenic->show' where id = '$Scenic->id'";
			}
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set title = '$Scenic->title',operator = '$Scenic->operator',created_at = '$Scenic->created_at',type = '$Scenic->type',keyword = '$Scenic->keyword',abstract = '$Scenic->abstract',content = '$Scenic->content',pic = '$Scenic->pic' where id = '$Scenic->id'";
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
