<?php
    error_reporting(0);
	require_once '../Model/Postcard.php';
	require_once '../DataBaseHandle/PostcardServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	Class PostcardControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    PostcardControl::GetAll();
					break;
				case "add":
                    PostcardControl::AddPostcard();
					break;
				case "del":
                    PostcardControl::DelPostcard();
					break;
				case "edit":
                    PostcardControl::UpdatePostcard();
					break;
				case "query":
                    PostcardControl::GetPostcard();
					break;
				case "gettype":
					PostcardControl::GetType();
					break;
                case "top":
                    PostcardControl::GoTop();
                    break;
                case "show":
                    PostcardControl::ChangeShow();
                    break;
                case "map":
                    PostcardControl::UploadMap();
                    break;
			}
		}

		public function GetAll()
		{
            $ss = new PostcardServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/PostcardList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'wx'=>$n['wx'],'pic' => $n['pic'], 'date' => $n['date'], 'wishes' => $n['wishes']);
                $re['content'] = $row;
                if (flock($jsonfile, LOCK_EX)) {//加写锁 
                    ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                    rewind($jsonfile); // 倒回文件指针的位置 
                    fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                    flock($jsonfile, LOCK_UN); //解锁 
                }
            }
            fclose($jsonfile);
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

        public function GetType()
        {
            $ss = new PostcardServer();
            $result = $ss->GetType();
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[]= array('id'=>$n['id'],'name'=>$n['name']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetPostcard(){
		    $id = $_REQUEST['id'];
		    $ss = new PostcardServer();
            $result = $ss->GetPostcardById($id);
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

		public function UpdatePostcardJson(){
            $ss = new PostcardServer();
            $result = $ss->GetAll();
            $jsonfile = fopen("../View/json/PostcardList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'wx'=>$n['wx'],'pic' => $n['pic'], 'date' => $n['date'], 'wishes' => $n['wishes']);
                $re['content'] = $row;
                if (flock($jsonfile, LOCK_EX)) {//加写锁 
                    ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                    rewind($jsonfile); // 倒回文件指针的位置 
                    fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                    flock($jsonfile, LOCK_UN); //解锁 
                }
            }
            fclose($jsonfile);
		}

		public function AddPostcard()
		{
            $re = array('state'=>'0','content'=>'添加失败');
            if(empty($_REQUEST['wx'])||empty($_REQUEST['name'])||empty($_REQUEST['wishes'])||empty($_REQUEST['date'])||empty($_REQUEST['pic']))
            {
                $re['state'] = '0';
                $re['content'] = '数据有误';
                echo json_encode($re,JSON_UNESCAPED_UNICODE);
                return;
            }
			$Postcard = new Postcard();
            $Postcard->wx = $_REQUEST['wx'];
            $Postcard->name = $_REQUEST['name'];
            $Postcard->wishes = $_REQUEST['wishes'];
            $Postcard->date = $_REQUEST['date'];
            $Postcard->pic = $_REQUEST['pic'];
            $ss = new PostcardServer();
            $result = $ss->InsertPostcard($Postcard);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdatePostcard()
		{
            $Postcard = new Postcard();
            $Postcard->id = $_REQUEST['id'];
            $Postcard->name = $_REQUEST['name'];
            $Postcard->created_at = $_REQUEST['created_at'];
            $Postcard->brief = $_REQUEST['brief'];
            $Postcard->recommend = $_REQUEST['recommend'];
            $Postcard->intro = $str = str_replace('\'', '\"', $_REQUEST['intro']);
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new PostcardServer();
			$result = $ss->UpdatePostcard($Postcard,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelPostcard(){
			$id = $_REQUEST['id'];
            $ss = new PostcardServer();
            $result=$ss->DeletePostcard($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	PostcardControl::UpdatePostcardJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function GoTop(){
		    $id = $_REQUEST['id'];
            $top = $_REQUEST['top'];
            $ss = new PostcardServer();
            $Postcard = new Postcard();
            $Postcard->id = $id;
            $Postcard->top = $top;
            $result = $ss->UpdatePostcard($Postcard,"top");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                PostcardControl::UpdatePostcardJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function ChangeShow(){
            $id = $_REQUEST['id'];
            $show = $_REQUEST['show'];
            $ss = new PostcardServer();
            $Postcard = new Postcard();
            $Postcard->id = $id;
            $Postcard->show = $show;
            $result = $ss->UpdatePostcard($Postcard,"isshow");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                PostcardControl::UpdatePostcardJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function UploadMap(){
            $re = array('state'=>'0','content'=>'');
            $src = "";
            $id = $_REQUEST['Postcardid'];
            $allowedExts = array("gif", "jpeg", "jpg", "png");
            $temp = explode(".", $_FILES["uploadfile"]["name"]);
            $extension = end($temp);
            if ((($_FILES["uploadfile"]["type"] == "image/gif")
                    || ($_FILES["uploadfile"]["type"] == "image/jpeg")
                    || ($_FILES["uploadfile"]["type"] == "image/jpg")
                    || ($_FILES["uploadfile"]["type"] == "image/pjpeg")
                    || ($_FILES["uploadfile"]["type"] == "image/x-png")
                    || ($_FILES["uploadfile"]["type"] == "image/png"))
                && in_array($extension, $allowedExts)){
                if ($_FILES["uploadfile"]["error"] > 0)
                {
                    $re['state']='0';
                    $re['content']='文件上传失败：'.$_FILES["uploadfile"]["error"];
                }
                else
                {
                    $filename ="../View/images/".time().$_FILES["uploadfile"]["name"];
                    $src = "/images/".time().$_FILES["uploadfile"]["name"];
                    $filename =iconv("UTF-8","gb2312",$filename);
                    //检查文件或目录是否存在
                    if(file_exists($filename))
                    {
                        $re['state']='0';
                        $re['content']='文件已存在';
                    }
                    else{
                        move_uploaded_file($_FILES["uploadfile"]["tmp_name"],$filename);
                        $Postcard = new Postcard();
                        $Postcard->id = $id;
                        $Postcard->Postcard_map = $src;
                        $as = new PostcardServer();
                        $result = $as->UpdatePostcard($Postcard,"Postcard_map");
                        if($result) {
                            PostcardControl::UpdatePostcardJson();
                            $re['state']='1';
                            $re['content']='修改成功';
                        }
                    }
                }
            }
            else{
                $re['state']='0';
                $re['content']='非法的文件格式';
            }
            echo "<script type='text/javascript'>if(".$re['state']."=='0'){alert('".$re['content']."')}else{window.parent.document.getElementById('am".$id."').src='".$src."'}</script>";
            //echo "<script type='text/javascript'>callback('".$re['state']."','".$re['content']."','".$src."');</script>";
        }
	}
?>
