<?php
	include 'ajax_template.php';
	include '../Extensions/LoadXmlData.php';
	require_once '../Extensions/Security.php';
	$xc = new XmlControl();
	$db = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
	$db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",0,"name");
	session_start();
	$username = $_SESSION["username"];
	if($username=="")
	{
		echo "用户信息已失效，请重新登录";
		return;
	}
	$code = Security::encrypt($_REQUEST['code']);
	$sql = "select password from ".$db_table." where username = '$username'";
	$result = AjaxGetData::RunSqlTwoParam($db, $sql);
	$r = mysqli_fetch_row($result);
	if($code == $r[0])
	{
		echo "1";
	}
	else
	{
		echo "0";
	}
?>