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
                case "batchDel":
                    PostcardControl::BatchDelPostcard();
                    break;
				case "query":
                    PostcardControl::GetPostcard();
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
            $wherelist = array();
            if(!empty($_POST['wx'])){
                $wherelist[] = "wx like '%{$_POST['wx']}%'";
            }
            if(!empty($_POST['name'])){
                $wherelist[] = "name like '%{$_POST['name']}%'";
            }
            if(!empty($_POST['timea'])&&!empty($_POST['timeb'])){
                $wherelist[] = " date between '{$_POST['timea']}' and '{$_POST['timeb']}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
		    $ss = new PostcardServer();
            $result = $ss->QueryPostcard($where);
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'wx' => $n['wx'], 'name' => $n['name'], 'date' => $n['date'], 'pic' => $n['pic'], 'wishes' => $n['wishes']);
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

        public function BatchDelPostcard(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new PostcardServer();
            $result=$as->BatchDeletePostcard($str);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
                NewsControl::UpdatePostcardJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
	}
?>
