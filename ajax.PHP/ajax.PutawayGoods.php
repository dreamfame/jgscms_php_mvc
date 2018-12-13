<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",2,"name");
	$operate = $_REQUEST['operate'];
	if($operate=="1"){
		$name = $_REQUEST["name"];
		$id = $_REQUEST["id"];
		$sqlv = "select * from ".$db_table." where id <> '$id' and name = '$name'";
		$r = AjaxGetData::RunSqlTwoParam($db, $sqlv);
		if(mysqli_fetch_row($r)==null)
		{
			$des = $_REQUEST["des"];
			$sales = $_REQUEST["sales"];
			$price = $_REQUEST["price"];
			$stock = $_REQUEST['stock'];
			$time = date('y-m-d');
			$sql = "update ".$db_table." set name = '$name',stock = '$stock',price = '$price',description = '$des',putaway = '$time',salesNum = '$sales' where id = '$id'";
			$result = AjaxGetData::RunSqlTwoParam($db, $sql);
			if($result)
			{
				echo "20".$time;
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "-1";
		}
	}
	else
	{
		$id = $_REQUEST["id"];
		$n = "";
		$sql1 = "update ".$db_table." set putaway = '$n' where id = '$id'";
		$result = AjaxGetData::RunSqlTwoParam($db, $sql1);
		if($result)
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
?>