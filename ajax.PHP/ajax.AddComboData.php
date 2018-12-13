<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",8,"name");
	$name = $_REQUEST["name"];
	$img = $_REQUEST["img"];
	$nothing = "无";
	$sql = "insert into combo(name,type,price,description,salesNum,comboImg) values('$name',1,0,'$nothing',0,'$img')";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	$sqlelse = "select id from combo order by id desc";
	$r = AjaxGetData::RunSqlTwoParam($db, $sqlelse);
	$id = mysqli_fetch_array($r);
	echo $id['id'];
?>