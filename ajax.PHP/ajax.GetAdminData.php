<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",0,"name");
	$sql = "select * from ".$db_table." where level = 2";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	while($admin = mysqli_fetch_array($result)) {
		$row[]= array('name'=>$admin['name'],'username'=>$admin['username'],'password'=>$admin['password'],'phone'=>$admin['phone']);
	}
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>