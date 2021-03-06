<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
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
			$this->db = new DBHelper("明信片表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",9,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select postcard.id,user.nickname as nickname,postcard.wx,postcard.name,postcard.pic,postcard.date,postcard.wishes from postcard LEFT JOIN user on postcard.wx = user.openid";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

        public function QueryPostcard($where){
            $sql = "select * from ".$this->db_table.$where;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryCombinePostcard($openid){
            $sql = "select postcard.id,user.nickname as nickname,user.avatar as avatar,postcard.wx,postcard.name,postcard.pic,postcard.date,postcard.wishes from postcard LEFT JOIN user on postcard.wx = user.openid where postcard.wx = '$openid' order by date desc";
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

        public function BatchDeletePostcard($str){
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
