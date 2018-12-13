<?php
	Class AjaxGetData
	{
		public static function RunSqlTwoParam($dbName,$sql)
		{
			$conn = mysqli_connect("localhost", "root", "~QQ!.1.2.3", $dbName);
			mysqli_query($conn, "set Names UTF8");
			$result = mysqli_query($conn,$sql);
			mysqli_close($conn);
			return $result;
		}
	}
?>