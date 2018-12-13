 <?php
	header('Access-Control-Allow-Origin：*');
	require_once '../Model/Picture.php';
	require_once '../DataBaseHandle/PictureServer.php';
	require_once '../DataBaseHandle/UserServer.php';
	require_once '../DataBaseHandle/StoreServer.php';
	header("Content-Type: text/html;charset=utf-8");
	Class PictureControl
	{
		private $addName;
		private $destinationPath;
		private $tempPath;
		
		public function JugdeOperate()
		{
			$operate = $_REQUEST["operate"];
			switch($operate)
			{
				case "add":
					$this->AddPicture();
					break;
				case "del":
					break;
				case "edit":
					$this->UpdatePicture();
					break;
				case "query":
					$this->GetPicture();
					break;
				case "conditionQuery":
					$this->queryCondition();
					break;
				case "ClientQuery":
					$this->ClientQuery();break;
				case "updateImage":
					$this->UpdateImage();break;
				case "deletepicture":
				    	$this->DeletePicture();break;
				case "editpicture":
				    	$this->EditPicture();break;	
				case "paging":
				        $this->GetTotalRecord();break;
				case "uploadImg":
			            $this->AcceptImg();break;
			    case "validateRename":
			            $this->validateName();
			            break;
			    case "DelServer":
			        $this->DelServerPic();
			        break;
			    case "yesterday":
			        $this->GetYesterday();
			        break;
			}
		}
		
		public function UpdatePicture(){
		    $id = $_REQUEST["id"];
		    $content = $_REQUEST["content"];
		    $ps = new PictureServer();
		    $result = $ps->UpdateContent($id,$content);
		    if($result){
		        echo 1;
		    }
		    else{
		        echo 0;
		    }
		}
		
		public function GetYesterday(){
		    $date = $_REQUEST['day'];
		    $ps = new PictureServer();
		    $result = $ps->GetYesterday($date);
		    $re = array('state'=>'0','content'=>null);
		    while ($p = mysqli_fetch_array($result))
		    {
		        $re['state'] = '1';
		        $row[]= array('id'=>$p['id'],'title'=>$p['title'],'content'=>$p['content'],'sender'=>$p['sender'],'senddate'=>$p['senddate'],'pic'=>$p['picsrc']);
		        $re['content'] = $row;
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function GetTotalRecord(){
			$condition = $_REQUEST["condition"];
			$param1 = $_REQUEST["conditionText1"];
			$ss = new PictureServer();
			$result = $ss->GetTotalRecord($condition,$param1);
			$re = array('state'=>'0','content'=>'null');
			while($r = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$re['content'] = $r["total"];
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function ClientQuery(){
			$condition = $_REQUEST["condition"];
			$conditionText1 = $_REQUEST["conditionText1"];
			$conditionText2 = $_REQUEST["conditionText2"];
			$ps = new PictureServer();
			$result = $ps->GetClientPicture($condition,$conditionText1,$conditionText2);
			$re = array('state'=>'0','content'=>null);
			$ss = new StoreServer();
			while ($p = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$num = $ss->GetStoreNum($p['id']);
				$row[]= array('id'=>$p['id'],'title'=>$p['title'],'content'=>$p['content'],'sender'=>$p['sender'],'senddate'=>$p['senddate'],'pic'=>$p['picsrc'],'store'=>$num);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		
		}
		
		public function queryCondition(){
			$page = $_REQUEST["page"];
			$pageSize = $_REQUEST["pageSize"];
			$condition = $_REQUEST["condition"];
			$conditionText1 = $_REQUEST["conditionText1"];
			$conditionText2 = $_REQUEST["conditionText2"];
			$ps = new PictureServer();
			$result = $ps->GetPictureByCondition($condition,$conditionText1,$conditionText2,$pageSize,$page);
			$result1 = $ps->GetTotalPages($condition,$conditionText1,$conditionText2);
			$re = array('state'=>'0','content'=>null,'totalPages'=>0);
			while($r = mysqli_fetch_array($result1))
			{
			    $re['totalPages'] = $r["total"];
			}
			while ($p = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$p['id'],'title'=>$p['title'],'content'=>$p['content'],'sender'=>$p['sender'],'senddate'=>$p['senddate'],'pic'=>$p['picsrc']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function AcceptImg(){
		    $uploadFile = $_FILES['avatar'];
		    $this->addName = date("Y").date("m").date("j").date("H").date("i").date("s");
		    $this->destinationPath = "../Resources/picture"."\\".$this->addName.$uploadFile["name"];
		    $this->tempPath = $uploadFile["tmp_name"];
		    if(is_uploaded_file($uploadFile["tmp_name"]))
		    {
		        $src = "/Resources/picture/".$this->addName.$uploadFile["name"];
		        $this->UploadFileToServer();
		        $showsrc = "..".$src;
		        echo "<script type='text/javascript'>window.top.document.getElementById('previewImg').setAttribute('src','".$showsrc."');</script>";
		    }
		}
		
		public function AddPicture()
		{
			$picture = new Picture();
			$us = new UserServer();
		    $picture->title = $_REQUEST["name"];
		    $picture->picsrc = $_REQUEST["pic"];
		    session_start();
		    $picture->sender = $us->getNameById($_SESSION["sid"]);
		    $picture->senddate = "20".date('y-m-d',time());
		    $picture->content = $_REQUEST["content"];
		    $ps = new PictureServer();
		    $result = $ps->InsertPicture($picture);
		    $re = array('state'=>'0','content'=>"添加失败");
		    if($result){
		        $re['state'] = "1";
		        $re['content'] = "添加成功";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function validateName(){
		    $name = $_REQUEST["name"];
		    $ps = new PictureServer();
		    $result = $ps->validateName($name);
		    $r = mysqli_fetch_row($result);
		    $re = array('state'=>'0','content'=>"标题已存在");
		    if($r[0]==""||$r[0]==null){
		        $re['state'] = "1";
		        $re['content'] = "标题可用";
		    }
		    echo json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function UpdateImage(){ 
			$num = $_REQUEST['index'];
			$id = $_REQUEST['id'];
			$this->addName = date("Y").date("m").date("j").date("H").date("i").date("s");
			$uploadfile = $_FILES["changeImage".$num];
			//$imgStyle = substr($this->addName.$uploadfile["name"],strpos($this->addName.$uploadfile["name"],'.'));
			//$newname=substr($uploadfile["name"],0,strpos($uploadfile["name"],'.'));
			$this->destinationPath = "../Resources/picture/".$this->addName.$uploadfile["name"];//.$imgStyle;
			$this->tempPath = $uploadfile["tmp_name"];
			if(is_uploaded_file($uploadfile["tmp_name"]))
			{
				$ps = new PictureServer();
				$p = new Picture();
				$p->id = $id;
				$p->src = "../Resources/picture/".$this->addName.$uploadfile["name"];//.$imgStyle;
				$result=$ps->UpdateImage($p);
				$this->UploadFileToServer();
				$re = array('state'=>'0','content'=>'修改失败');
			    if($result) {
    				$re['state']='1';
    				$re['content']='修改成功';
			     }
		        echo  json_encode($re,JSON_UNESCAPED_UNICODE);
			}
		}
 
		public function DeletePicture(){
			 $pd = new PictureServer();
			 $d=$_REQUEST['id'];
			 $result=$pd->DeletePicture($d);
			 $re = array('state'=>'0','content'=>'删除失败');
			 if($result) {
				$re['state']='1';
				$re['content']='删除成功';
			}
			echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}
		
		public function DelServerPic(){
		    $img = $_REQUEST["img"];
		    $imgstr = "../Resources/picture/".substr($img,strripos($img,"/"));
		    unlink($imgstr);
		}

		public function EditPicture(){
			$editid=$_REQUEST['id'];
			$newname=$_REQUEST['new']; 
			$pe=new PictureServer();
			$result=$pe->EditPicture($newname,$editid);
			$re = array('state'=>'0','content'=>'修改失败');
			if($result) {
				$re['state']='1';
				$re['content']='修改成功';
			}
			echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function UploadFileToServer()
		{
			move_uploaded_file ($this->tempPath,iconv ( "UTF-8", "gb2312", $this->destinationPath ) );
		}

		public function GetPicture()
		{
			$ps = new PictureServer();
			$result = $ps->GetPictureList();
			$re = array('state'=>'0','content'=>null);
			while ($p = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('id'=>$p['id'],'sender'=>$p['sender'],'src'=>$p['aimsrc'],'senddate'=>$p['senddate'],'imgname'=>$p['imgname']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}
	}
	$vc = new PictureControl();
	$vc->JugdeOperate();
?>
 