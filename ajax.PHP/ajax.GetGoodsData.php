<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",2,"name");
	$type = $_REQUEST['type'];
	$sql = "select * from ".$db_table." where type = '$type'";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	while($goods = mysqli_fetch_array($result))
	{
		$row[] = array('id'=>$goods['id'],'type'=>$goods['type'],'stock'=>$goods['stock'],'capacity'=>$goods['capacity'],'title'=>$goods['name'],'time'=>$goods['putaway'],'price'=>$goods['price'],'details'=>$goods['description'],'icon'=>$goods['goodsImg'],'nums'=>$goods['salesNum']);
	}
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>