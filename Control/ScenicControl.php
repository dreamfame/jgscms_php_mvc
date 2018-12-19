<?php
	require_once '../Model/Scenic.php';
	require_once '../DataBaseHandle/ScenicServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	Class ScenicControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    ScenicControl::GetAll();
					break;
				case "add":
                    ScenicControl::AddScenic();
					break;
				case "del":
                    ScenicControl::DelScenic();
					break;
				case "edit":
                    ScenicControl::UpdateScenic();
					break;
				case "query":
                    ScenicControl::GetScenic();
					break;
				case "gettype":
					ScenicControl::GetType();
					break;
                case "top":
                    ScenicControl::GoTop();
                    break;
                case "show":
                    ScenicControl::ChangeShow();
                    break;
			}
		}

		public function GetAll()
		{
            $ss = new ScenicServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/scenicList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'], 'breif' => $n['breif'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
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
            $ss = new ScenicServer();
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

        public function GetScenic(){
		    $id = $_REQUEST['id'];
		    $ss = new ScenicServer();
            $result = $ss->GetScenicById($id);
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'title' => $n['title'], 'content' => $n['content'], 'type' => $n['type'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'operator'=>$n['operator'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'abstract'=>$n['abstract'],'keyword'=>$n['keyword'],'pic'=>$n['pic']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

		public function UpdateScenicJson(){
            $ss = new ScenicServer();
            $result = $ss->GetAll();
            $jsonfile = fopen("../View/json/scenicList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'title' => $n['title'], 'content' => $n['content'], 'type' => $n['type'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'operator'=>$n['operator'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'abstract'=>$n['abstract'],'keyword'=>$n['keyword'],'pic'=>$n['pic']);
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

		public function AddScenic()
		{
			$Scenic = new Scenic();
            $Scenic->title = $_REQUEST['title'];
            $Scenic->type = $_REQUEST['type'];
            $Scenic->content = $str = str_replace('\'', '\"', $_REQUEST['content']);
            $Scenic->show = $_REQUEST['show'];
            $Scenic->top = $_REQUEST['top'];
            $Scenic->created_at = $_REQUEST['created_at'];
            $Scenic->updated_at = $_REQUEST['created_at'];
            $Scenic->operator = $_REQUEST['operator'];
            $Scenic->keyword = $_REQUEST['keyword'];
            $Scenic->abstract = $_REQUEST['abstract'];
            $Scenic->pic = $_REQUEST['pic'];
            $Scenic->see = 0;
			$ss = new ScenicServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ss->IssertScenic($Scenic);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateScenic()
		{
			$Scenic = new Scenic();
            $Scenic->id = $_REQUEST['id'];
            $Scenic->title = $_REQUEST['title'];
            $Scenic->operator = $_REQUEST['operator'];
            $Scenic->created_at = $_REQUEST['created_at'];
            $Scenic->type = $_REQUEST['type'];
            $Scenic->keyword = $_REQUEST['keyword'];
            $Scenic->abstract = $_REQUEST['abstract'];
            $Scenic->pic = $_REQUEST['pic'];
            $Scenic->content = $str = str_replace('\'', '\"', $_REQUEST['content']);
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new ScenicServer();
			$result = $ss->UpdateScenic($Scenic,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelScenic(){
			$id = $_REQUEST['id'];
            $ss = new ScenicServer();
            $result=$ss->DeleteScenic($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	ScenicControl::UpdateScenicJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function GoTop(){
		    $id = $_REQUEST['id'];
            $top = $_REQUEST['top'];
            $ss = new ScenicServer();
            $Scenic = new Scenic();
            $Scenic->id = $id;
            $Scenic->top = $top;
            $result = $ss->UpdateScenic($Scenic,"top");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                ScenicControl::UpdateScenicJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function ChangeShow(){
            $id = $_REQUEST['id'];
            $show = $_REQUEST['show'];
            $ss = new ScenicServer();
            $Scenic = new Scenic();
            $Scenic->id = $id;
            $Scenic->show = $show;
            $result = $ss->UpdateScenic($Scenic,"isshow");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                ScenicControl::UpdateScenicJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
	}
?>
