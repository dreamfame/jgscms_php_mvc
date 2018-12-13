<?php
	include 'ajax_template.php';
	$username = $_REQUEST['username'];
	$name = $_REQUEST['name'];
	$age = $_REQUEST['age'];
	$birth = $_REQUEST['birth'];
	$sex = $_REQUEST['sex'];
	$qq = $_REQUEST['qq'];
	$sql = "update user set name ='$name',age='$age',birth='$birth',sex='$sex',qq='$qq' where username = '$username'";
	$result = AjaxGetData::RunSqlTwoParam("papaba", $sql);
	echo $result;
?>