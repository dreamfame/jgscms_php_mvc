<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",7,"name");
	$name = $_REQUEST["name"];
	$sql = "select * from ".$db_table." where name = '$name'";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	$intro = mysqli_fetch_array($result);
	$row[]= array('intro'=>$intro['intro'],'img1'=>$intro['Img1'],'img2'=>$intro['Img2'],'img3'=>$intro['Img3'],'img4'=>$intro['Img4'],'theme'=>$intro['theme'],'businessTime'=>$intro['businessTime'],'consume'=>$intro['consume']);
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>