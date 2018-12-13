<?php
	require_once '../DataBaseHandle/StoreServer.php';
	require_once '../DataBaseHandle/PictureServer.php';
	header("Content-Type: text/html;charset=utf-8");
	session_start();
	Class StoreControl
	{
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "get":
					$this->GetStore();
					break;
				case "getNum":
				    $this->GetStoreNum();
				    break;
				case "store":
				    $this->InsertRecord();
			}
		}
		
		public function GetStore(){
		    $phone = $_REQUEST["phone"];
		    $ss = new StoreServer();
		    $result = $ss->GetStore($phone);
		    $re = array('state'=>'0','content'=>null);
		    while($s = mysqli_fetch_array($result)){
		        $re['state'] = '1';
		        $ps = new PictureServer();
		        $r = $ps->GetPicture($s['pid']);
		        while ($p = mysqli_fetch_array($result)){
				    $row[]= array('id'=>$p['id'],'title'=>$p['title'],'content'=>$p['content'],'sender'=>$p['sender'],'senddate'=>$p['senddate'],'pic'=>$p['picsrc']);
		        }
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function GetStoreNum(){
		    $pid = "11";
		    $ss = new StoreServer();
		    echo $ss->GetStoreNum($pid);
		}
		
		public function InsertStore(){
		    $pid = $_REQUEST["pid"];
		    $phone = $_REQUEST["phone"];
		    $ss = new StoreServer();
		    $result = $ss->AddStore($pid,$phone);
		    $re = array('state'=>'0','content'=>"收藏失败");
		    if(result){
		        $re['state'] = "1";
		        $re["content"]= "收藏成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
	}
	$sc = new StoreControl();
	$sc->JugdeOperate();
?>
