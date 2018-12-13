<?php
	include 'ajax_template.php';
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	$email = $_REQUEST['email'];
	$phone = $_REQUEST['phone'];
	$sql = "update user set password ='$password',email='$email',phone='$phone' where username = '$username'";
	$result = AjaxGetData::RunSqlTwoParam("papaba", $sql);
	echo $result;
?>