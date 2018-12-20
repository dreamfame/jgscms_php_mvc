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
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",3,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function InsertImg($Img)
		{
			$sql = "insert into ".$this->db_table."(username,head_pic,nickname,password,age,phone,password_reset_token,email,updated_at,created_at,role,status) values('$Img->username','$Img->head_pic','$Img->nickname','$Img->password','$Img->age','$Img->phone','$Img->password_reset_token','$Img->email','$Img->updated_at','$Img->created_at','$Img->role','$Img->status')";
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
