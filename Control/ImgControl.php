<?php
	require_once '../Model/Images.php';
	require_once '../DataBaseHandle/ImgServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	Class ImgControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
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
			}
		}

		public function GetAll()
		{
            $is = new ImgServer();
            $result = $is->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/images.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'scenic_id' => $u['scenic_id'], 'name' => $u['name'], 'src' => $u['picStr']);
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

        public function GetImgByScenicId()
        {
        	$scenic_id = $_REQUEST["scenic_id"];
            $is = new ImgServer();
            $result = $is->GetImgByScenicId($scenic_id);
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/images.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'scenic_id' => $u['scenic_id'], 'name' => $u['name'], 'src' => $u['picStr']);
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
            $jsonfile = fopen("../View/json/ImgList.json", "w") or die("Unable to open file!");
            while ($u = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $u['id'], 'username' => $u['username'], 'nickname' => $u['nickname'], 'age' => $u['age'], 'phone' => $u['phone'], 'email' => $u['email'], 'status' => $u['status'], 'role' => $u['role']);
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
	}
?>
