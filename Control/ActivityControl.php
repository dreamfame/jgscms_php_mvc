<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../Model/Activity.php';
	require_once '../DataBaseHandle/ActivityServer.php';
	header("Content-Type: text/html;charset=utf-8");
    header('cache-control:private');
	error_reporting(0);
	//session_start();
	Class ActivityControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    ActivityControl::GetList();
					break;
                case "all":
                    ActivityControl::GetAll();
                    break;
				case "add":
                    ActivityControl::AddActivity();
					break;
				case "del":
                    ActivityControl::DelActivity();
					break;
                case "batchDel":
                    ActivityControl::BatchDelActivity();
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
                case "verify_name":
                    ActivityControl::VerifyName();
                    break;
                case "verify_id_name":
                    ActivityControl::VerifyIdName();
                    break;
			}
		}

		public function GetList()
		{
            $ss = new ActivityServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>"未获取数据");
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

        public function GetAll()
        {
            $ss = new ActivityServer();
            $result = $ss->GetShow();
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'pic'=>$n['pic'],'date' => $n['date'], 'join' => $n['join'], 'intro' => $n['intro'], 'prize_way' => $n['prize_way'],'prize'=>$n['prize'],'phone'=>$n['phone'],'enable'=>$n['enable'],'num'=>$n['num']);
                $re['content'] = $row;
            }
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
            $wherelist = array();
            if($_REQUEST['id']!=""||$_REQUEST['id']!=null){
                $wherelist[] = "id = '{$_REQUEST['id']}'";
            }
            if($_REQUEST['name']!=""||$_REQUEST['name']!=null){
                $wherelist[] = "name like '%{$_REQUEST['name']}%'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
		    $ss = new ActivityServer();
            $result = $ss->QueryActivity($where);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'pic'=>$n['pic'],'date' => $n['date'], 'join' => $n['join'], 'intro' => $n['intro'], 'prize_way' => $n['prize_way'],'prize'=>$n['prize'],'phone'=>$n['phone'],'enable'=>$n['enable'],'num'=>$n['num']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function VerifyName(){
            $name = $_REQUEST['name'];
            $ss = new ActivityServer();
            $result = $ss->VerifyName($name);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id']);
                $re['content'] = $row;
            }
            echo $re['state'];
            return;
        }

        public function VerifyIdName(){
            $id = $_REQUEST['id'];
            $name = $_REQUEST['name'];
            $ss = new ActivityServer();
            $result = $ss->VerifyName($name);
            $state = "0";
            while ($n = mysqli_fetch_array($result))
            {
                if($n['id']==$id){
                    $state = 0;
                }
                else{
                    $state = 1;
                }
            }
            echo $state;
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

        public function BatchDelActivity(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new ActivityServer();
            $result=$as->BatchDeleteActivity($str);
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
                        $source =  $filename;
                        $dst_img = $filename;
                        $percent = 0.5;
                        $image = (new ImageCompress($source,$percent))->compressImg($dst_img);
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
