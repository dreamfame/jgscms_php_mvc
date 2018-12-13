<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",2,"name");
	$id = $_REQUEST['id'];
	$sql = "delete from ".$db_table." where id = '$id'";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	if($result)
	{
		echo "1";
	}
	else
	{
		echo "0";
	}
?>
