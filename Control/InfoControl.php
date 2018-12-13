<?php
	require_once '../Model/Info.php';
	require_once '../DataBaseHandle/InfoServer.php';
	require_once '../DataBaseHandle/IntegralServer.php';
	header("Content-Type: text/html;charset=utf-8");
	session_start();
	Class InfoControl
	{
	    
	    private $destinationPath;
	    private $tempPath;
	    private $save_name;
	    
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "get":
					$this->GetInfo();
					break;
				case "accept":
				    $this->IntoInfo();
					break;
				case "update":
				    $this->UpdateInfo();
				    break;
				case "updateimg":
				    $this->UpdateImage();break;
			}
		}
		
		public function IntoInfo(){
		    $info = new Info();
		    $info->id = $_REQUEST['phone'];
		    $is = new InfoServer();
		    $pass = $is->validatePhone($info->id);
		    if(!$pass)return;
		    $info->nickname = $_REQUEST['nickname'];
		    $info->height = $_REQUEST['height'];
		    $info->weight = $_REQUEST['weight'];
		    $info->location = $_REQUEST['locations'];
		    $info->vocation = $_REQUEST['vocation'];
		    $info->company = $_REQUEST['company'];
		    $info->headimg = "Resources/customer/default.jpg";
		    $info->createAt = "20".date('y-m-d',time());
		    $result = $is->InsertInfo($info);
		    $ics = new IntegralServer();
		    $ics->InsertDefault($info->id);
		    $re = array('state'=>'0','content'=>"信息添加失败");
		    if($result){
		         $re['state'] = "1";
		         $re['content'] = "信息添加成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function GetInfo(){
		    $id = $_REQUEST['phone'];
		    $is = new InfoServer();
		    $ics = new IntegralServer();
		    $result = $is->GetInfo($id);
		    $result1 = $ics->GetIntegrals($id);
		    $integral = mysqli_fetch_row($result1);
		    $re = array('state'=>'0','content'=>null);
		    while($i = mysqli_fetch_array($result)){
		        $re['state'] = "1";
		        $row[] = array('nickname'=>$i['nickname'],'height'=>$i['height'],'weight'=>$i['weight'],'location'=>$i['location'],'vocation'=>$i['vocation'],'company'=>$i['company'],'headimg'=>$i['headimg'],'integral'=>$integral[2],'time'=>$integral[3]);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function UploadFileToServer()
		{
		    move_uploaded_file ($this->tempPath,iconv ( "UTF-8", "gb2312", $this->destinationPath ) );
		}
		
		public function UpdateInfo(){
		    $info = new Info();
		    $info->id = $_REQUEST['phone'];
		    $info->nickname = $_REQUEST['nickname'];
		    $info->height = $_REQUEST['height'];
		    $info->weight = $_REQUEST['weight'];
		    $info->location = $_REQUEST['locations'];
		    $info->vocation = $_REQUEST['vocation'];
		    $info->company = $_REQUEST['company'];
		    $is = new InfoServer();
		    $result = $is->UpdateInfo($info);
		    $re = array('state'=>'0','content'=>"信息更新失败");
		    if($result){
		        $re['state'] = "1";
		        $re['content'] = "信息更新成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function UpdateImage(){
		    $is = new InfoServer();
		    $id = $_REQUEST['phone'];
		    $data = $_FILES["img"];
		    $this->save_name = md5(time().mt_rand(10, 99));
		    if($data!=""){
		        $n = strrpos($data["name"],"?");
		        $name = substr($data["name"],0,$n);
		        $this->destinationPath = "../Resources/customer/".$this->save_name.$name;
		        $info->img = $this->destinationPath;
		        $this->tempPath = $data["tmp_name"];
		    }
		    if(is_uploaded_file($data["tmp_name"]))
		    {
		        $this->UploadFileToServer();
		    }
		    $headimg = str_replace("../","",$this->destinationPath);
		    $result = $is->UpdateImg($id,$headimg);
		    $re = array('state'=>'0','content'=>"头像更新失败");
		    if($result){
		        $re['state'] = "1";
		        $re['content'] = "头像更新成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
	}
	$ic = new InfoControl();
	$ic->JugdeOperate();
?>
