<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table1 = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",4,"name");
	$db_table2 = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",5,"name");
	$phone = $_REQUEST['phone'];
	$sql = "select consume.id,consume.time,consume.amount,consume.paymethod,consume.consumption from ".$db_table2." where consume.phone='$phone' union select recharge.id,recharge.time,recharge.money,recharge.paymethod,recharge.consumption from ".$db_table1." where recharge.phone = '$phone' ORDER BY time DESC";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	while($bill = mysqli_fetch_array($result)) {
		$row[]= array('id'=>$bill['id'],'time'=>$bill['time'],'amount'=>$bill['amount'],'paymethod'=>$bill['paymethod'],'consumption'=>$bill['consumption']);
	}
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>