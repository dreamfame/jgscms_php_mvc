<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../Model/Images.php';
	require_once '../DataBaseHandle/ImgServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
    error_reporting(0);
	Class ImgControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
					ImgControl::GetList();
					break;
                case "all":
                    ImgControl::GetAll();
                    break;
				case "pic":
					ImgControl::GetImgByScenicId();
					break;
				case "add":
                    ImgControl::AddImg();
					break;
				case "del":
					ImgControl::DelImg();
					break;
				case "query":
                    ImgControl::GetImg();
					break;
                case "upload":
                    ImgControl::UploadImg();
                    break;
                case "batchDel":
                    ImgControl::BatchDelImg();
                    break;
			}
		}

		public function GetList()
		{
            $is = new ImgServer();
            $result = $is->GetAll();
            $re = array('state'=>'0','content'=>"未获取数据");
            $jsonfile = fopen("../View/json/images.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'scenic_id' => $u['scenic_id'], 'name' => $u['name'], 'src' => $u['picSrc']);
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
            $re = array('state'=>'0','content'=>"未获取数据");
            if(empty($_REQUEST["scenic_id"])){
                $re['content'] = "参数有误";
                echo json_encode($re,JSON_UNESCAPED_UNICODE);
                return;
            }
            $scenic_id = $_REQUEST["scenic_id"];
            $is = new ImgServer();
            $result = $is->GetImgByScenicId($scenic_id);
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'scenic_id' => $u['scenic_id'], 'name' => $u['name'], 'src' => $u['picSrc']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetImgByScenicId()
        {
            $re = array('state'=>'0','content'=>"未获取数据");
            if(empty($_REQUEST["scenic_id"])){
                $re['content'] = "参数有误";
                return;
            }
        	$scenic_id = $_REQUEST["scenic_id"];
            $is = new ImgServer();
            $result = $is->GetImgByScenicId($scenic_id);
            $jsonfile = fopen("../View/json/images.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'scenic_id' => $u['scenic_id'], 'name' => $u['name'], 'src' => $u['picSrc']);
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

		public function UpdateImgJson(){
            $as = new ImgServer();
            $result = $as->GetAll();
            $jsonfile = fopen("../View/json/images.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'scenic_id' => $u['scenic_id'], 'name' => $u['name'], 'src' => $u['picSrc']);
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

		public function AddImg()
        {
            $Img = new Img();
            $Img->username = $_REQUEST['username'];
            $Img->password = password_hash("666666", PASSWORD_DEFAULT);
            $Img->nickname = $_REQUEST['nickname'];
            $Img->role = $_REQUEST['role'];
            $Img->age = 0;
            $Img->head_pic = "default.jpg";
            $Img->phone = $_REQUEST['phone'];
            $Img->email = $_REQUEST['email'];
            $Img->status = $_REQUEST['status'];
            $Img->updated_at = time();
            $Img->created_at = time();
            $Img->password_reset_token = md5($Img->username . ImgControl::key, false);
            $as = new ImgServer();
            $result = $as->GetImg($Img->username);
            $temp = false;
            $re = array('state' => '0', 'content' => '添加失败,');
            while ($u = mysqli_fetch_array($result))//检查用户名
            {
                $temp = true;
                $re['content'] = $re['content'] . '已存在该用户!';
                $row[] = array('username' => $u['username'], 'password' => $u['password']);
            }
            $condition = "nickname";
            $content = $Img->nickname;
            $result1 = $as->GetImgByCondition($condition, $content);
            while ($u = mysqli_fetch_array($result1))//检查昵称
            {
                $temp = true;
                $re['content'] = $re['content'] . '昵称已存在!';
                $row[] = array('username' => $u['username'], 'password' => $u['password']);
            }
            $condition = "phone";
            $content = $Img->phone;
            $result2 = $as->GetImgByCondition($condition, $content);
            while ($u = mysqli_fetch_array($result2))//检查手机号
            {
                $temp = true;
                $re['content'] = $re['content'] . '手机号已被使用!';
                $row[] = array('username' => $u['username'], 'password' => $u['password']);
            }
            $condition = "email";
            $content = $Img->email;
            $result3 = $as->GetImgByCondition($condition, $content);
            while ($u = mysqli_fetch_array($result3))//检查邮箱
            {
                $temp = true;
                $re['content'] = $re['content'] . '邮箱已被使用!';
                $row[] = array('username' => $u['username'], 'password' => $u['password']);
            }
            if (!$temp) {
                $row = $as->InsertImg($Img);
                $re['state'] = '1';
                $re['content'] = '添加成功';
                echo json_encode($re, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode($re, JSON_UNESCAPED_UNICODE);
            }
        }

		public function GetImg()
		{
			$userid = $_REQUEST['userid'];
			$as = new ImgServer();
			$result = $as->GetImgById($userid);
			$re = array('state'=>'0','content'=>null);
			while ($u = mysqli_fetch_array($result))
			{
				$re['state'] = '1';
				$row[]= array('username'=>$u['username'],'role'=>$u['role']);
				$re['content'] = $row;
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelImg(){
			$id = $_REQUEST['id'];
            $as = new ImgServer();
            $result=$as->DeleteImg($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	ImgControl::UpdateImgJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function BatchDelImg(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new ImgServer();
            $result=$as->BatchDeleteImg($str);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
                ImgControl::UpdateImgJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

		public function UploadImg(){
            $re = array('state'=>'0','content'=>'','msg'=>'');
            $src = "";
            $id = $_REQUEST['scenic_id'];
            $allowedExts = array("gif", "jpeg", "jpg", "png");
            $file = $_FILES['uploadfile'];
            $name = $file['name'];
            $total = 0;
            $success = 0;
            $failed = 0;
            $upload_path = "../View/images/scenicImgs/";
            foreach ($name as $k=>$names){
                $type = strtolower(substr($names,strrpos($names,'.')+1));//得到文件类型，并且都转化成小写
                $total++;
                //把非法格式的图片去除
                if (!in_array($type,$allowedExts)){
                    $failed++;
                    unset($name[$k]);
                }
            }
            foreach ($name as $k=>$item){
                if (move_uploaded_file($file['tmp_name'][$k],$upload_path.time().$name[$k])){
                    $src = "/images/scenicImgs/".time().$name[$k];
                    $row[] = array('scenic_id'=>$id,'name'=>$name[$k],'src'=>$src);
                    $success++;
                }else{
                    $failed++;
                }
            }
            $is = new ImgServer();
            $result = $is->InsertImg($row);
            if($result) {
                ImgControl::UpdateImgJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            $re['msg'] = "共上传图片".$total."张，成功".$success."张，失败".$failed."张";
            echo "<script type='text/javascript'>window.parent.location.reload();window.parent.closeMark('".$re['msg']."','".$total."','".$success."','".$failed."');</script>";
            //echo "<script type='text/javascript'>callback('".$re['state']."','".$re['content']."','".$src."');</script>";
        }
	}
?>
