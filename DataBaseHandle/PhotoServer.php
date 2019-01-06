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
			$sql = "select photo.id,user.nickname as nickname,uid,des,photo.created_at,praise,comment,img1,img2,img3,img4,img5,img6,img7,img8,img9,verify,operator,top,private from photo LEFT JOIN user on photo.uid = user.openid";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function GetShow(){
            $sql = "select photo.id,user.nickname as nickname,user.avatar as avatar,uid,des,photo.created_at,praise,comment,img1,img2,img3,img4,img5,img6,img7,img8,img9,verify,operator,top,private from ".$this->db_table." left join user on uid = openid where verify = 1 and private = 0 order by photo.top,photo.created_at desc limit 50";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetName(){
            $sql = "select id,name from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryPhoto($where){
            $sql = "select photo.id,user.nickname as nickname,user.avatar as avatar,uid,des,photo.created_at,praise,comment,img1,img2,img3,img4,img5,img6,img7,img8,img9,verify,operator,top,private from ".$this->db_table."  left join user on uid = openid".$where." order by photo.created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetWaitPhoto($where){
            $sql = "select photo.id,user.nickname as nickname,uid,des,photo.created_at,praise,comment,img1,img2,img3,img4,img5,img6,img7,img8,img9,verify,operator,top,private from photo LEFT JOIN user on photo.uid = user.openid".$where." order by photo.created_at,photo.top desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

		public function InsertPhoto($Photo){
            $sql = "insert into ".$this->db_table."(uid,des,created_at,praise,comment,img1,img2,img3,img4,img5,img6,img7,img8,img9,verify,operator,top,private) values('$Photo->uid','$Photo->des','$Photo->created_at','$Photo->praise','$Photo->comment','$Photo->img1','$Photo->img2','$Photo->img3','$Photo->img4','$Photo->img5','$Photo->img6','$Photo->img7','$Photo->img8','$Photo->img9','$Photo->verify','$Photo->operator','$Photo->top','$Photo->private')";
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

        public function GetPhotoById($id){
            $sql = "select uid,created_at from photo where id = '$id'";
            $result = $this->db->ExeSql($sql, $this->conn);
            $photo = mysqli_fetch_object($result);
            return $photo;
        }
	}
?>
