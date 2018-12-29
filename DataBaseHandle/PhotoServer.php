<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class PhotoServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function PhotoServer()
		{
			$this->db = new DBHelper("分享图库表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",8,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select * from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetShow(){
            $sql = "select * from ".$this->db_table." order by created_at,top desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetName(){
            $sql = "select id,name from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryPhoto($where){
            $sql = "select * from ".$this->db_table.$where;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertPhoto($Photo){
            $sql = "insert into ".$this->db_table."(name,recommend,brief,intro,isshow,top,updated_at,created_at,see) values('$Photo->name','$Photo->recommend','$Photo->brief','$Photo->intro','$Photo->isshow','$Photo->top','$Photo->updated_at','$Photo->created_at','$Photo->see')";
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

		public function UpdatePhoto($Photo,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Photo->top' where id = '$Photo->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Photo->show' where id = '$Photo->id'";
			}
			else if($field=="verify"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Photo->verify',operator = '$Photo->operator' where id = '$Photo->id'";
            }
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set name = '$Photo->name',created_at = '$Photo->created_at',brief = '$Photo->brief',intro = '$Photo->intro',recommend = '$Photo->recommend' where id = '$Photo->id'";
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

		public function DeletePhoto($id)
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

        public function BatchDeletePhoto($str){
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
