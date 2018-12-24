<?php
	require_once 'DBHelper.php';
	require_once '../Extensions/ReturnQueryCondition.php';
	require_once '../Extensions/Security.php';
	require_once '../Extensions/LoadXmlData.php';
	header("Content-Type: text/html;charset=utf-8");
	Class MessageServer
	{
		public $db;
		public $conn;
		public $dbase;
		public $db_table;
		
		
		public function MessageServer()
		{
			$this->db = new DBHelper();
			$xc = new XmlControl();
			$this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
			$this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",11,"name");
			$this->conn = $this->db->Open($this->dbase);
		}

        public function GetAll(){
            $sql = "select message.id,msg,msg_time,status,`user`.nickname as user_nickname,`user`.avatar as user_head from message LEFT JOIN `user` on `user`.id = message.uid";
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
