<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class AreaServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function AreaServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",4,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select * from ".$this->db_table." order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetType(){
            $sql = "select id,name from Area_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryArea($where){
            $sql = "select * from ".$this->db_table.$where." order by created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertArea($area){
            $sql = "insert into ".$this->db_table."(name,recommend,brief,intro,isshow,top,updated_at,created_at,see,area_map) values('$area->name','$area->recommend','$area->brief','$area->intro','$area->isshow','$area->top','$area->updated_at','$area->created_at','$area->see','$area->area_map')";
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

		public function UpdateArea($area,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$area->top' where id = '$area->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$area->show' where id = '$area->id'";
			}
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set name = '$area->name',created_at = '$area->created_at',brief = '$area->brief',recommend = '$area->recommend',intro = '$area->intro' where id = '$area->id'";
            }
            else if($field=="area_map"){
                $sql = "update " . $this->db_table . " set ".$field." = '$area->area_map' where id = '$area->id'";
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

		public function DeleteArea($id)
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

        public function BatchDeleteArea($str){
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
