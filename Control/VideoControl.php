<?php
	header('Access-Control-Allow-Origin：*');
	require_once '../Model/Video.php';
	require_once '../DataBaseHandle/VideoServer.php';
	require_once '../DataBaseHandle/PlanServer.php';
	header("Content-Type: text/html;charset=utf-8");
	Class VideoControl
	{
		private $destinationPath;
		private $thumbdestinationPath;
		private $thumbtempPath;
		private $tempPath;
		private $addName;
		
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "add":
					$this->AddVideo();
					break;
				case "del":
				    $this->DeleteVideo();break;
					break;
				case "edit":
					$this->UpdateVideo();
					break;
				case "query":
					$this->GetVideo();
					break;
				case "conditionQuery":
					$this->queryCondition();
					break;
				case "ClientQuery":
					$this->ClientQuery();break;
				case "updateImage":
					$this->UpdateImage();break;
				case "paging":
					$this->GetTotalRecord();break;
				case "TypeQuery":
				    $this->TypeQuery();break;
				case "uploadImg":
				    $this->AcceptImg();break;
				case "uploadthumb":
				    $this->Thumb();break;
				case "validateRename":
				    $this->validateName();
				    break;
				case "DelServer":
				    $this->DelServerVideo();
				    break;
			}
		}
		
		public function DeleteVideo(){
		    $vs = new VideoServer();
		    $d=$_REQUEST['id'];
		    $result=$vs->DeleteVideo($d);
		    $re = array('state'=>'0','content'=>'删除失败');
		    if($result) {
		        $re['state']='1';
		        $re['content']='删除成功';
		    }
		    echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function DelServerVideo(){
		    $img = $_REQUEST["img"];
		    $gif = $_REQUEST['gif'];
		    $imgstr = "../Resources/action/".substr($img,strripos($img,"/"));
		    $gifstr = "../Resources/action/".substr($gif,strripos($img,"/"));
		    unlink($imgstr);
		    unlink($gifstr);
		}
		
		public function AddVideo(){
		    $video = new Video();
		    $video->name = $_REQUEST["name"];
		    $video->type = $_REQUEST["type"];
		    $video->gifsrc = $_REQUEST["pic"];
		    $video->pngsrc = $_REQUEST["thumb"];
		    $video->intro = $_REQUEST["intro"];
		    $vs = new VideoServer();
		    $result = $vs->InsertVideo($video);
		    $re = array('state'=>'0','content'=>"添加失败");
		    if($result){
		        $re['state'] = "1";
		        $re['content'] = "添加成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function validateName(){
		    $name = $_REQUEST["name"];
		    $vs = new VideoServer();
		    $result = $vs->validateName($name);
		    $re = array('state'=>'0','content'=>"名称已存在");
		    if($result != null){
		    $r = mysqli_fetch_row($result);
    		    if($r[0]==""||$r[0]==null){
    		        $re['state'] = "1";
    		        $re['content'] = "名称可用";
    		    }
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
			
		public function ClientQuery(){
			$condition = $_REQUEST["condition"];
			$value = $_REQUEST["value"];
			$vs = new VideoServer();
			$result = $vs->GetClientVideo($condition,$value);
			$re = array('state'=>'0','content'=>null);
			while ($v = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$v['id'],'type'=>$v['type'],'name'=>$v['name'],'src'=>$v['src'],'reason'=>$v['reason'],'destination'=>$v['destination'],'suggest'=>$v['suggest']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function queryCondition(){
		    $page = $_REQUEST["page"];
		    $pageSize = $_REQUEST["pageSize"];
			$condition = $_REQUEST["condition"];
			$conditionText = $_REQUEST["conditionText"];
			$vs = new VideoServer();
			$result = $vs->GetVideoByCondition($condition,$conditionText,$page,$pageSize);
			$result1 = $vs->GetTotalPages($condition,$conditionText);
			$re = array('state'=>'0','content'=>null,'totalPages'=>0);
			while($r = mysqli_fetch_array($result1))
			{
			    $re['totalPages'] = $r["total"];
			}
			while ($v = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$v['id'],'name'=>$v['name'],'pngsrc'=>$v['pngsrc'],'gifsrc'=>$v['gifsrc'],'type'=>$v['type'],'intro'=>$v['intro']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function UpdateImage(){
			$num = $_REQUEST['index'];
			$id = $_REQUEST['id'];
			$this->addName = date("Y").date("m").date("j").date("H").date("i").date("s");
			$uploadfile = $_FILES["changeImage".$num];
			$this->destinationPath = "../Resources/action"."\\".$this->addName.$uploadFile["name"];
		    $this->tempPath = $uploadFile["tmp_name"];
			if(is_uploaded_file($uploadfile["tmp_name"]))
			{
				$vs = new VideoServer();
				$v = new Video();
				$v->id = $id;
				$v->src = "/Resources/action/".$this->addName.$uploadFile["name"];
				$vs->UpdateImage($v);
				$this->UploadFileToServer();
				echo "<script>window.location.href='../View/video.html';</script>";
			}
		}
		
		public function UploadFileToServer()
		{
			move_uploaded_file ($this->tempPath,iconv ( "UTF-8", "gb2312", $this->destinationPath ) );
		}

		public function UpdateVideo()
		{
			$v = new Video();
			$v->id = $_REQUEST["id"];
			$v->type = $_REQUEST["type"];
			$v->name = $_REQUEST["name"];
			$v->intro = $_REQUEST["intro"];
			$v->gifsrc = $_REQUEST["pic"];
		    $v->pngsrc = $_REQUEST["thumb"];
			$re = array('state'=>'0','content'=>null);
			$vs = new VideoServer();
			$result = $vs->UpdateVideo($v);
			if($result){
				$re['state'] = '1';
				$re['content'] = "更新成功";
			}else{
				$re['state'] = '0';
				$re['content'] = "更新失败";
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function GetVideo()
		{ 
		    $id = $_REQUEST["id"];
			$vs = new VideoServer();
			$result = $vs->GetVideoList($id);
			$re = array('state'=>'0','content'=>null);
		    while ($v = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$v['id'],'name'=>$v['name'],'pngsrc'=>$v['pngsrc'],'gifsrc'=>$v['gifsrc'],'type'=>$v['type'],'intro'=>$v['intro']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}
		
		public function TypeQuery(){
		    $type = $_REQUEST["type"];
		    $vs = new VideoServer();
		    $result = $vs->TypeQuery($type);
		    $re = array('state'=>'0','content'=>null);
		    while ($v = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        $row[]= array('id'=>$v['id'],'name'=>$v['name'],'pngsrc'=>$v['pngsrc'],'gifsrc'=>$v['gifsrc'],'type'=>$v['type'],'intro'=>$v['intro']);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		    return;
		}
		
		public function AcceptImg(){
		    $uploadFile = $_FILES['avatar'];
		    $this->addName = date("Y").date("m").date("j").date("H").date("i").date("s");
		    $this->destinationPath = "../Resources/action"."\\".$this->addName.$uploadFile["name"];
		    $this->tempPath = $uploadFile["tmp_name"];
		    if(is_uploaded_file($uploadFile["tmp_name"]))
		    {
		        $src = "/Resources/action/".$this->addName.$uploadFile["name"];
		        $this->UploadFileToServer();
		        $showsrc = "..".$src;
		        echo "<script type='text/javascript'>window.top.document.getElementById('previewImg').setAttribute('src','".$showsrc."');</script>";
		    }
		}
		
		public function Thumb(){
		    $uploadFile = $_FILES['thumb'];
		    $this->addName = date("Y").date("m").date("j").date("H").date("i").date("s");
		    $this->destinationPath = "../Resources/action"."\\".$this->addName.$uploadFile["name"];
		    $this->tempPath = $uploadFile["tmp_name"];
		    if(is_uploaded_file($uploadFile["tmp_name"]))
		    {
		        $src = "/Resources/action/".$this->addName.$uploadFile["name"];
		        $this->UploadFileToServer();
		        $showsrc = "..".$src;
		        echo "<script type='text/javascript'>window.top.document.getElementById('previewthumbImg').setAttribute('src','".$showsrc."');</script>";
		    }
		}
	}
	$vc = new VideoControl();
	$vc->JugdeOperate();
?>