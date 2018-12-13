<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",9,"name");
	$id = $_REQUEST['id'];
	$sql = "select * from ".$db_table." where goodsList = '$id'";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	while($goodsList = mysqli_fetch_array($result)) {
		$row[]= array('id'=>$goodsList['id'],'name'=>$goodsList['goodsName'],'price'=>$goodsList['goodsPrice'],'num'=>$goodsList['goodsNum'],'img'=>$goodsList['goodsImg'],'goodsList'=>$goodsList['goodsList']);
	}
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>