<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class ImgServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function ImgServer()
		{
			$this->db = new DBHelper("景点图库表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",3,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function InsertImg($img){
            $sql = "insert into ".$this->db_table."(scenic_id,name,picSrc) values";
			$num = count($img);
			if($num==0) {
                return false;
            }
            else if($num==1){
                $scenic_id = $img[0]['scenic_id'];
                $name =  $img[0]['name'];
                $src = $img[0]['src'];
                $sql = $sql."('$scenic_id','$name','$src')";
            }
			else{
				foreach($img as $k => $imgs){
                    $scenic_id = $img[$k]['scenic_id'];
                    $name =  $img[$k]['name'];
                    $src = $img[$k]['src'];
                    $sql = $sql."('$scenic_id','$name','$src'),";
				}
				$sql = substr($sql, 0, -1);
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

		public function GetImgById($userId)
		{
            $sql = "select id,username,role from ".$this->db_table." where id = '$userId'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

		public function GetImg($userId)
		{
			$sql = "select id,username,password,nickname,phone,email from ".$this->db_table." where username = '$userId'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

		public function GetAll(){
            $sql = "select * from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetImgByScenicId($scenic_id){
            $sql = "select * from ".$this->db_table." where scenic_id = '$scenic_id'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function DeleteImg($id){
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
