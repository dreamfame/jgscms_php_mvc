<?php
	require_once '../Model/HeadImg.php';
	require_once '../DataBaseHandle/HeadImgServer.php';
	header("Content-Type: text/html;charset=utf-8");
	Class HeadImgControl
	{
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "add":
					$this->AddAdmin();
					break;
				case "del":
					break;
				case "edit":
					$this->UpdateAdmin();
					break;
				case "query":
					$this->GetHeadImg();
					break;
				case "update":
					$this->changeImg();
					break;
			}
		}
		
		public function changeImg(){
			$id = $_REQUEST['id'];
			$src = $_REQUEST['src'];
			$hs = new HeadImgServer();
			$h = new HeadImg();
			$h->userId = $id;
			$h->headImg = $src;
			$hs->ChangeImage($h);
			echo "<script>window.location.href='http://192.168.2.101:8080/Dumbbell_Project/APP/Client/DumbBell/Scene/InfoSettings.html';</script>";
		}

		public function GetHeadImg()
		{
			$userid = $_REQUEST['userid'];
			$hs = new HeadImgServer();
			$result = $hs->GetHeadImg($userid);
			$re = array('state'=>'0','content'=>null);
			while ($h = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('userId'=>$h['userId'],'headimg'=>$h['headimg']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}	
	}
	$hc = new HeadImgControl();
	$hc->JugdeOperate();
?>
