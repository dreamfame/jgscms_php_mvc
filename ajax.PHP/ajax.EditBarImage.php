<?php
	$uploadPath = "../Resources/";
	$num = $_REQUEST['num'];
	$file = "file".$num;
	if(move_uploaded_file($_FILES[$file]['tmp_name'],$uploadPath)){
		$res["msg"] = "ok";
	}else{
		$res["error"] = "error";
	}
	echo json_encode($res,JSON_UNESCAPED_UNICODE);
?>