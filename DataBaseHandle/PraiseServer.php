<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once 'DBHelper.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class PraiseServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		public function PraiseServer()
		{
			$this->db = new DBHelper("分享图点赞表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",14,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function GetAll(){
			$sql = "select * from ".$this->db_table;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

		public function InsertPraise($praise){
            $sql = "insert into ".$this->db_table."(openid,photo_id,created_at) values('$praise->openid','$praise->photo_id','$praise->created_at')";
            $result = $this->db->ExecSql($sql,$this->conn);
            return $result;
		}

		public function DeletePraise($openid,$photo_id)
        {
            $sql="delete from ".$this->db_table." where openid= '$openid' and photo_id = '$photo_id'";
            $result = $this->db->ExecSql($sql,$this->conn);
            return $result;
        }

        public function QueryPraise($where){
            $sql = "select * from ".$this->db_table.$where;
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryUserPraise($where){
            $sql = "select praise.id,user.nickname as nickname,user.avatar as avatar,praise.created_at,praise.openid,praise.photo_id from praise left join user on praise.openid = user.openid".$where."order by praise.created_at desc";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function QueryMyPraise($where){
			$sql = "select user.avatar as avatar,user.nickname as nickname,uid,img1,img2,img3,img4,img5,img6,img7,img8,img9,des,praise from (select uid,img1,img2,img3,img4,img5,img6,img7,img8,img9,des,praise from photo left join praise on praise.photo_id = photo.id".$where.") as p left join user on user.openid = p.uid";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}
	}
?>
