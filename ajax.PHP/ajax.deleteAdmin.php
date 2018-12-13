<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",0,"name");
	$username = $_REQUEST["username"];
	$sql = "delete from ".$db_table." where username = '$username'";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	echo $result;
?>