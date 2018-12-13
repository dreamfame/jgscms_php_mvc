<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",8,"name");
	$sql = "select * from ".$db_table;
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	while($combo = mysqli_fetch_array($result))
	{
		$row[]= array('id'=>$combo['id'],'img'=>$combo['comboImg'],'name'=>$combo['name'],'price'=>$combo['price'],'list'=>$combo['goodsList'],'time'=>$combo['putaway'],'num'=>$combo['salesNum'],'des'=>$combo['description']);
	}
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>