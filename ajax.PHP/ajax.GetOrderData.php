<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",15,"name");
	$state = $_REQUEST['state'];
	if($state!=""){
		$sql = "select * from ".$db_table." where state = '$state'";
	}
	else
	{
		$sql = "select * from ".$db_table." where state <> 0";
	}
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	while($order = mysqli_fetch_array($result))
	{
		$row[]= array('id'=>$order['id'],'phone'=>$order['userId'],'createTime'=>$order['createTime'],'finishTime'=>$order['finishTime'],'state'=>$order['state'],'price'=>$order['totalPrice']);
	}
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>