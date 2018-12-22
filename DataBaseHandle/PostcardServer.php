<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class PostcardServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function PostcardServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",9,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select * from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetType(){
            $sql = "select id,name from Postcard_type";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetPostcardById($id){
            $sql = "select * from ".$this->db_table." where id = '$id'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertPostcard($Postcard){
            $sql = "insert into ".$this->db_table."(wx,name,pic,date,wishes) values('$Postcard->wx','$Postcard->name','$Postcard->pic','$Postcard->date','$Postcard->wishes')";
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

		public function UpdatePostcard($Postcard,$field){
            $sql = "";
            if($field=="top"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Postcard->top' where id = '$Postcard->id'";
            }
            else if($field=="isshow"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Postcard->show' where id = '$Postcard->id'";
			}
			else if($field=="all"){
                $sql = "update " . $this->db_table . " set name = '$Postcard->name',created_at = '$Postcard->created_at',brief = '$Postcard->brief',recommend = '$Postcard->recommend',intro = '$Postcard->intro' where id = '$Postcard->id'";
            }
            else if($field=="Postcard_map"){
                $sql = "update " . $this->db_table . " set ".$field." = '$Postcard->Postcard_map' where id = '$Postcard->id'";
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

		public function DeletePostcard($id)
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
