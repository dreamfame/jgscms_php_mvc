<?php
	interface IDBHelper
	{
		function DBaseConnection();
		
		function Open($tableName);
		
		function Close($conn);
		
		function CloseDB($conn);
		
		function ExeSql($sql,$conn);
	}
?>