<?php
	require_once '../Model/Activity.php';
	require_once '../DataBaseHandle/ActivityServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	Class ActivityControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    ActivityControl::GetAll();
					break;
				case "add":
                    ActivityControl::AddActivity();
					break;
				case "del":
                    ActivityControl::DelActivity();
					break;
				case "edit":
                    ActivityControl::UpdateActivity();
					break;
				case "query":
                    ActivityControl::GetActivity();
					break;
				case "gettype":
					ActivityControl::GetType();
					break;
                case "enable":
                    ActivityControl::GoEnable();
                    break;
                case "pic":
                    ActivityControl::UploadPic();
                    break;
			}
		}

		public function GetAll()
		{
            $ss = new ActivityServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/activityList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'pic'=>$n['pic'],'date' => $n['date'], 'join' => $n['join'], 'intro' => $n['intro'], 'prize_way' => $n['prize_way'],'prize'=>$n['prize'],'phone'=>$n['phone'],'enable'=>$n['enable'],'num'=>$n['num']);
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
            $ss = new ActivityServer();
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

        public function GetActivity(){
		    $id = $_REQUEST['id'];
		    $ss = new ActivityServer();
            $result = $ss->GetActivityById($id);
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

		public function UpdateActivityJson(){
            $ss = new ActivityServer();
            $result = $ss->GetAll();
            $jsonfile = fopen("../View/json/activityList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'pic'=>$n['pic'],'date' => $n['date'], 'join' => $n['join'], 'intro' => $n['intro'], 'prize_way' => $n['prize_way'],'prize'=>$n['prize'],'phone'=>$n['phone'],'enable'=>$n['enable'],'num'=>$n['num']);
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

		public function AddActivity()
		{
			$activity = new Activity();
            $activity->name = $_REQUEST['name'];
            $activity->intro = $_REQUEST['intro'];
            $activity->enable = $_REQUEST['enable'];
            $activity->date = $_REQUEST['date'];
            $activity->join = $_REQUEST['join'];
            $activity->prize = $_REQUEST['prize'];
            $activity->prize_way = $_REQUEST['prize_way'];
            $activity->phone = $_REQUEST['phone'];
            $activity->pic = $_REQUEST['pic'];
            $activity->num = 0;
			$ss = new ActivityServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ss->InsertActivity($activity);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateActivity()
		{
            $activity = new Activity();
            $activity->id = $_REQUEST['id'];
            $activity->name = $_REQUEST['name'];
            $activity->date = $_REQUEST['date'];
            $activity->phone = $_REQUEST['phone'];
            $activity->prize = $_REQUEST['prize'];
            $activity->intro = $_REQUEST['intro'];
            $activity->prize_way = $_REQUEST['prize_way'];
            $activity->join = $_REQUEST['join'];
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new ActivityServer();
			$result = $ss->UpdateActivity($activity,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelActivity(){
			$id = $_REQUEST['id'];
            $ss = new ActivityServer();
            $result=$ss->DeleteActivity($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	ActivityControl::UpdateActivityJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function GoEnable(){
		    $id = $_REQUEST['id'];
            $enable = $_REQUEST['enable'];
            $ss = new ActivityServer();
            $activity = new Activity();
            $activity->id = $id;
            $activity->enable = $enable;
            $result = $ss->UpdateActivity($activity,"enable");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                ActivityControl::UpdateActivityJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function UploadPic(){
            $re = array('state'=>'0','content'=>'');
            $src = "";
            $id = $_REQUEST['activityid'];
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
                        $activity = new Activity();
                        $activity->id = $id;
                        $activity->pic = $src;
                        $as = new ActivityServer();
                        $result = $as->UpdateActivity($activity,"pic");
                        if($result) {
                            ActivityControl::UpdateActivityJson();
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
            echo "<script type='text/javascript'>if(".$re['state']."=='0'){alert('".$re['content']."')}else{window.parent.document.getElementById('ap".$id."').src='".$src."'}</script>";
            //echo "<script type='text/javascript'>callback('".$re['state']."','".$re['content']."','".$src."');</script>";
        }
	}
?>
