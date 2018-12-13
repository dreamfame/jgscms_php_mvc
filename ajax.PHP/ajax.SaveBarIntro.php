<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",7,"name");
	$name = $_REQUEST["name"];
	$type = $_REQUEST["type"];
	if($type==1)
	{
		$intro = $_REQUEST["intro"];
		$sql = "update ".$db_table." set intro = '$intro' where name = '$name'";
	}
	else if($type==2)
	{
		$theme = $_REQUEST["theme"];
		$sql = "update ".$db_table." set theme = '$theme' where name = '$name'";
	}
	else if($type==3)
	{
		$businessTime = $_REQUEST["businessTime"];
		$consume = $_REQUEST["consume"];
		$sql = "update ".$db_table." set businessTime = '$businessTime',consume = '$consume' where name = '$name'";
	}
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	echo $result;
?>