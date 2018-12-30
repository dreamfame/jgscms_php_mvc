<?php
	require_once '../Model/Scenic.php';
	require_once '../DataBaseHandle/ScenicServer.php';
	header("Content-Type: text/html;charset=utf-8");
	error_reporting(0);
	//session_start();
	Class ScenicControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    ScenicControl::GetList();
					break;
                case "all":
                    ScenicControl::GetAll();
                    break;
				case "add":
                    ScenicControl::AddScenic();
					break;
				case "del":
                    ScenicControl::DelScenic();
					break;
                case "batchDel":
                    ScenicControl::BatchDelScenic();
                    break;
				case "edit":
                    ScenicControl::UpdateScenic();
					break;
				case "query":
                    ScenicControl::GetScenic();
					break;
                case "top":
                    ScenicControl::GoTop();
                    break;
                case "show":
                    ScenicControl::ChangeShow();
                    break;
                case "name":
                    ScenicControl::GetName();
                    break;
                case "id_name":
                    ScenicControl::GetIdAndName();
                    break;
                case "verify_name":
                    ScenicControl::VerifyName();
                    break;
                case "verify_id_name":
                    ScenicControl::VerifyIdName();
                    break;
			}
		}

		public function GetList()
		{
            $ss = new ScenicServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>"未获取数据");
            $jsonfile = fopen("../View/json/scenicList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'area_id'=>$n['area_id'],'area_name'=>$n['area_name'] ,'name' => $n['name'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
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
            $ss = new ScenicServer();
            $result = $ss->GetShow();
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'area_id'=>$n['area_id'],'area_name'=>$n['area_name'] ,'name' => $n['name'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetIdAndName()
        {
            $ss = new ScenicServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'area_id' => $n['area_id'],'name' => $n['name']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetName()
        {
            $ss = new ScenicServer();
            $result = $ss->GetName();
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
		    $ss = new ScenicServer();
            $result = $ss->QueryScenic($where);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'area_name'=>$n['area_name'] ,'name' => $n['name'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
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
                $row[] = array('id' => $n['id'],'area_name'=>$n['area_name'] ,'name' => $n['name'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
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

        public function VerifyName(){
		    $area_id = $_REQUEST['area_id'];
            $name = $_REQUEST['name'];
            $ss = new ScenicServer();
            $result = $ss->VerifyName($area_id,$name);
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
            $area_id = $_REQUEST['area_id'];
            $name = $_REQUEST['name'];
            $ss = new ScenicServer();
            $result = $ss->VerifyName($area_id,$name);
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

		public function AddScenic()
		{
			$scenic = new Scenic();
            $scenic->name = $_REQUEST['name'];
            $scenic->area_id = $_REQUEST['area_id'];
            $scenic->intro = $str = str_replace('\'', '\"', $_REQUEST['intro']);
            $scenic->isshow = $_REQUEST['show'];
            $scenic->top = $_REQUEST['top'];
            $scenic->created_at = $_REQUEST['created_at'];
            $scenic->updated_at = $_REQUEST['created_at'];
            $scenic->brief = $_REQUEST['brief'];
            $scenic->recommend = $_REQUEST['recommend'];
            $scenic->see = 0;
			$ss = new ScenicServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ss->InsertScenic($scenic);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateScenic()
		{
            $scenic = new Scenic();
            $scenic->id = $_REQUEST['id'];
            $scenic->name = $_REQUEST['name'];
            $scenic->created_at = $_REQUEST['created_at'];
            $scenic->brief = $_REQUEST['brief'];
            $scenic->recommend = $_REQUEST['recommend'];
            $scenic->intro = $str = str_replace('\'', '\"', $_REQUEST['intro']);
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new ScenicServer();
			$result = $ss->UpdateScenic($scenic,"all");
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

        public function BatchDelScenic(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new ScenicServer();
            $result=$as->BatchDeleteScenic($str);
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
            $scenic = new Scenic();
            $scenic->id = $id;
            $scenic->top = $top;
            $result = $ss->UpdateScenic($scenic,"top");
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
            $scenic = new Scenic();
            $scenic->id = $id;
            $scenic->show = $show;
            $result = $ss->UpdateScenic($scenic,"isshow");
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
