<?php
require_once '../Model/User.php';
require_once '../DataBaseHandle/UserServer.php';
header("Content-Type: text/html;charset=utf-8");
Class PhoneVerification  
{
	public function DataManage()
	{
		$user = new User();
		$user->Id = $_REQUEST["phone"];
		$user->Phone = $_REQUEST["phone"];
		$this->validateData($user);
	}

	public function validateData($u)
	{
		$us = new UserServer();
		$r_phone = $us->GetUserByParam($u->Phone,"4");
		if($r_phone!=null)
		{
			$com[] = array('code'=>-1);
			echo json_encode($com,JSON_UNESCAPED_UNICODE);
			return;
		}
		else
		{
			$com[] = array('code'=>6210);
			echo json_encode($com,JSON_UNESCAPED_UNICODE);
		}
	}
}
$pv = new PhoneVerification();
$pv->DataManage();
?>