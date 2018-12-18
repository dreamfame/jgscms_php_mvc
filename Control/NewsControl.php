<?php
	require_once '../Model/News.php';
	require_once '../DataBaseHandle/NewsServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	Class NewsControl
	{
		public function JugdeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    NewsControl::GetAll();
					break;
				case "add":
                    NewsControl::AddNews();
					break;
				case "del":
                    NewsControl::DelAdmin();
					break;
				case "edit":
                    NewsControl::UpdateAdmin();
					break;
				case "query":
                    NewsControl::GetAdmin();
					break;
				case "gettype":
					NewsControl::GetType();
					break;
			}
		}

		public function GetAll()
		{
            $ns = new NewsServer();
            $result = $ns->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/newsList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'title' => $n['title'], 'content' => $n['content'], 'type' => $n['type'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'operator'=>$n['operator'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'abstract'=>$n['abstract'],'keyword'=>$n['keyword']);
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
            $ns = new NewsServer();
            $result = $ns->GetType();
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

		public function UpdateAdminJson(){
            $as = new AdminServer();
            $result = $as->GetAll();
            $jsonfile = fopen("../View/json/adminList.json", "w") or die("Unable to open file!");
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

		public function AddNews()
		{
			$news = new News();
            $news->title = $_REQUEST['title'];
            $news->type = $_REQUEST['type'];
            $news->content = $str = str_replace('\'', '\"', $_REQUEST['content']);
            $news->show = $_REQUEST['show'];
            $news->top = $_REQUEST['top'];
            $news->created_at = $_REQUEST['created_at'];
            $news->updated_at = $_REQUEST['created_at'];
            $news->operator = $_REQUEST['operator'];
            $news->keyword = $_REQUEST['keyword'];
            $news->abstract = $_REQUEST['abstract'];
            $news->see = 0;
			$ns = new NewsServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ns->InsertNews($news);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateAdmin()
		{
			$admin = new Admin();
			$admin->userId = $_REQUEST['userid'];
			$admin->password = $_REQUEST['password'];
			$re = array('state'=>'0','content'=>'修改失败');
			$as = new AdminServer();
			$result = $as->UpdateAdmin($admin);
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function GetAdmin()
		{
			$userid = $_REQUEST['userid'];
			$as = new AdminServer();
			$result = $as->GetAdminById($userid);
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

		public function DelAdmin(){
			$id = $_REQUEST['id'];
            $as = new AdminServer();
            $result=$as->DeleteAdmin($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	AdminControl::UpdateAdminJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}
	}
?>
