<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../ProjectInterface/IDBHelper.php';
	require_once '../Model/Log.php';
	require_once 'LogServer.php';
	session_start();
	Class DBHelper implements IDBHelper
	{
		private $serverName;
		private $username;
		private $password;
		private $dbStr;
		public function DBHelper($param)
		{
			$this->serverName = "localhost";
			$this->username = "root";
			$this->password = "root";
			$this->dbStr = $param;
		}

		public function DBaseConnection()
		{
			$conn = mysql_connect($this->serverName,$this->username,$this->password);
			return $conn;
		}

		public function Open($dbName)
		{
			$conn = mysqli_connect($this->serverName,$this->username,$this->password,$dbName);
			return $conn;
		}

		public function CloseDB($conn)
		{
			mysql_close($conn);
		}

		public function Close($conn)
		{
			mysqli_close($conn);
		}

        public function ExeSqlTest($sql,$conn)
        {
            mysqli_query($conn, "set Names UTF8");
            $result = mysqli_query($conn,$sql) or die("SQL错误：".mysqli_error($conn));
            return $result;
        }

        public function RecoLog($sql){
            $log = new Log();
            if(isset($_SESSION["operator"])){
                $log->username = $_SESSION["operator"];
            }else{
                $log->username = "未知";
            }
            date_default_timezone_set('PRC');
            $log->time = date('Y-m-d H:i:s', time());
            $content = "";
            if(substr($sql , 0 , 6)=="insert"){
                $content = "插入".$this->dbStr;
            }
            else if(substr($sql , 0 , 6)=="update"){
                $content = "更新".$this->dbStr;
            }
            else if(substr($sql , 0 , 6)=="select"){
                $content = "查询".$this->dbStr;
            }
            else if(substr($sql , 0 , 6)=="delete"){
                $content = "删除".$this->dbStr;
            }
            $log->content = $content;
            $ls = new LogServer();
            $ls->RecordLog($log);
		}

        public function ExeSql($sql,$conn)
        {
			//$this->RecoLog($sql);
            mysqli_query($conn, "set Names UTF8");
            $result = mysqli_query($conn,$sql);
            return $result;
        }

        public function ExcuteSql($sql,$conn){
            mysqli_query($conn, "set Names UTF8");
            $result = "";
            if(!mysqli_query($conn,$sql)){
                $result = mysqli_error($conn);
            }
            return $result;
		}

		public function ExecSql($sql,$conn)
		{
            //$this->RecoLog($sql);
			mysqli_query($conn, "set Names UTF8");
			$result = "";
			if(!mysqli_query($conn,$sql)){
				$result = mysqli_error($conn);
			}
            return $result;
		}

		public function UpdateSql($sql, $conn)
		{
			mysqli_query($conn, "set Names UTF8");
			$result = mysqli_query($conn,$sql);
			if(mysqli_affected_rows())
			{
				return true;
			}
			else
			{
				return false;
			}
		}

	}
?>
