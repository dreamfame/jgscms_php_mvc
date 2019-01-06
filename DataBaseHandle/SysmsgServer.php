<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class SysmsgServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		
		
		public function SysmsgServer()
		{
			$this->db = new DBHelper("系统消息表");
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",15,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

		public function InsertMsg($sysmsg){
            $sql = "insert into ".$this->db_table."(openid,title,content,created_at,see) values('$sysmsg->openid','$sysmsg->title','$sysmsg->content','$sysmsg->created_at','$sysmsg->see')";
            $result = $this->db->ExecSql($sql,$this->conn);
            return $result;
		}

		public function DeleteMsg($id){
            $sql="delete from ".$this->db_table." where id= '$id'";
            $result = $this->db->ExecSql($sql,$this->conn);
            return $result;
		}

        public function GetMsgByOpenid($openid){
            $sql = "select * from".$this->db_table." where openid = '$openid'";
            $result = $this->db->ExeSql($sql, $this->conn);
            return $result;
        }

        public function GetMsg($id){
			$sql = "select message.id,reply,reply_time,`admin`.username as admin_username,`admin`.head_pic as admin_head from message LEFT JOIN `admin` on `admin`.id = message.admin_id where 1=1 and message.id = '$id'";
			$result = $this->db->ExeSql($sql, $this->conn);
            return $result;
		}

		public function UpdateMessage($message,$field){
            $sql = "";
            if($field=="reply"){
                $sql = "update " . $this->db_table . " set admin_id = '$message->admin_id',reply = '$message->reply',reply_time = '$message->reply_time',status=1 where id = '$message->id'";
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
	}
?>
