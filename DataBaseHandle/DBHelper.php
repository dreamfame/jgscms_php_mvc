<?php
	require_once '../ProjectInterface/IDBHelper.php';

	Class DBHelper implements IDBHelper
	{
		private $serverName;
		private $username;
		private $password;
		public function DBHelper()
		{
			$this->serverName = "localhost";
			$this->username = "root";
			$this->password = "root";
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

        public function ExeSql($sql,$conn)
        {
            mysqli_query($conn, "set Names UTF8");
            $result = mysqli_query($conn,$sql);
            return $result;
        }

		public function ExecSql($sql,$conn)
		{
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
