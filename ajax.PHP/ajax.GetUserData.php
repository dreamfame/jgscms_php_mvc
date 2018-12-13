<?php
	include 'ajax_template.php';
	$id = $_REQUEST["id"];
	if($id==1){
		$sql = "select name,sex,phone,email,balance,place from vip";
		$result = AjaxGetData::RunSqlTwoParam("papaba", $sql);
		while($vip = mysqli_fetch_array($result)) {
			$row[]= array('name'=>$vip['name'],'sex'=>$vip['sex'],'phone'=>$vip['phone'],'email'=>$vip['email'],'place'=>$vip['place']);
		}
	}
	else
	{
		$sql = "select user.expenditure,vip.name,vip.sex,vip.phone,vip.email,vip.place from user LEFT JOIN vip ON user.id = vip.userId order by user.expenditure desc";
		//$sql = "select id,name,age,birth,sex,qq,headImg,2dbc,expenditure from user";
		$result = AjaxGetData::RunSqlTwoParam("papaba", $sql);
		while($user = mysqli_fetch_array($result))
		{
			$row[] = array('expenditure'=>$user['expenditure'],'name'=>$user['name'],'sex'=>$user['sex'],'phone'=>$user['phone'],'email'=>$user['email'],'place'=>$user['place']);
		}
	}
	echo json_encode($row,JSON_UNESCAPED_UNICODE);
?>