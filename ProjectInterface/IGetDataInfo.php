<?php
interface IGetDataInfo
{
	function GetDataFromDB();
	
	function DataManage($result);
	
	function ReturnDataToClient($model);
}
?>