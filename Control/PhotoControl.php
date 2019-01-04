<?php
	require_once '../Model/Photo.php';
	require_once '../DataBaseHandle/PhotoServer.php';
require_once '../DataBaseHandle/PraiseServer.php';
	require_once '../Extensions/Security.php';
	header("Content-Type: text/html;charset=utf-8");
	error_reporting(0);
	//session_start();
	Class PhotoControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    PhotoControl::GetList();
					break;
                case "all":
                    PhotoControl::GetAll();
                    break;
				case "add":
                    PhotoControl::AddPhoto();
					break;
				case "del":
                    PhotoControl::DelPhoto();
					break;
                case "batchDel":
                    PhotoControl::BatchDelPhoto();
                    break;
				case "edit":
                    PhotoControl::UpdatePhoto();
					break;
				case "query":
                    PhotoControl::GetPhoto();
					break;
                case "top":
                    PhotoControl::GoTop();
                    break;
                case "name":
                    PhotoControl::GetName();
                    break;
                case "json":
                    PhotoControl::UpdatePhotoJson();
                    break;
                case "wait":
                    PhotoControl::GetWaitPhoto();
                    break;
                case "verify":
                    PhotoControl::VerifyPhoto();
                    break;
                case "praise":
                    PhotoControl::Praise();
                    break;
                case "unpraise":
                    PhotoControl::UnPraise();
                    break;
                case "allpraise":
                    PhotoControl::GetPraise();
                    break;
			}
		}

		public function GetList()
		{
            $ss = new PhotoServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>"未获取数据");
            $jsonfile = fopen("../View/json/PhotoList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'top'=>$n['top'] ,'nickname'=>$n['nickname'],'uid' => $n['uid'], 'des' => $n['des'], 'praise' => $n['praise'], 'comment' => $n['comment'], 'img1' => $n['img1'],'img2'=>$n['img2'],'img3'=>$n['img3'],'img4'=>$n['img4'],'img5'=>$n['img5'],'img6'=>$n['img6'],'img7'=>$n['img7'],'img8'=>$n['img8'],'img9'=>$n['img9'],'created_at'=>$n['created_at'],'verify'=>$n['verify'],'operator'=>$n['operator']);
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
            $ss = new PhotoServer();
            $result = $ss->GetShow();
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $r = array();
                array_push($r,$n['img1'],$n['img2'],$n['img3'],$n['img4'],$n['img5'],$n['img6'],$n['img7'],$n['img8'],$n['img9']);
                $r = array_filter($r);
                $row[] = array('avatar'=>$n['avatar'],'nickname'=>$n['nickname'],'id' => $n['id'],'top'=>$n['top'] ,'uid' => $n['uid'], 'des' => $n['des'], 'praise' => $n['praise'], 'comment' => $n['comment'], 'img' => $r,'created_at'=>$n['created_at'],'verify'=>$n['verify'],'operator'=>$n['operator']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetName()
        {
            $ss = new PhotoServer();
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

        public function GetWaitPhoto(){
            $wherelist = array();
            if($_POST['verify']!=""||$_POST['verify']!=null){
                $wherelist[] = "verify = '{$_POST['verify']}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
            $ss = new PhotoServer();
            $result = $ss->GetWaitPhoto($where);
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/PhotoList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'top'=>$n['top'] ,'nickname'=>$n['nickname'],'uid' => $n['uid'], 'des' => $n['des'], 'praise' => $n['praise'], 'comment' => $n['comment'], 'img1' => $n['img1'],'img2'=>$n['img2'],'img3'=>$n['img3'],'img4'=>$n['img4'],'img5'=>$n['img5'],'img6'=>$n['img6'],'img7'=>$n['img7'],'img8'=>$n['img8'],'img9'=>$n['img9'],'created_at'=>$n['created_at'],'verify'=>$n['verify'],'operator'=>$n['operator']);
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

        public function GetPhoto(){
            $wherelist = array();
            if($_REQUEST['verify']!=""||$_REQUEST['verify']!=null){
                $wherelist[] = "verify = '{$_REQUEST['verify']}'";
            }
            if($_REQUEST['id']!=""||$_REQUEST['id']!=null){
                $wherelist[] = "photo.id = '{$_REQUEST['id']}'";
            }
            if($_REQUEST['uid']!=""||$_REQUEST['uid']!=null){
                $wherelist[] = "uid = '{$_REQUEST['uid']}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
            $ss = new PhotoServer();
            $result = $ss->QueryPhoto($where);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $r = array();
                array_push($r,$n['img1'],$n['img2'],$n['img3'],$n['img4'],$n['img5'],$n['img6'],$n['img7'],$n['img8'],$n['img9']);
                $r = array_filter($r);
                $row[] = array('avatar'=>$n['avatar'],'nickname'=>$n['nickname'],'id' => $n['id'],'top'=>$n['top'] ,'uid' => $n['uid'], 'des' => $n['des'], 'praise' => $n['praise'], 'comment' => $n['comment'], 'img' => $r,'created_at'=>$n['created_at'],'verify'=>$n['verify'],'operator'=>$n['operator']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

		public function UpdatePhotoJson(){
            $ss = new PhotoServer();
            $result = $ss->GetAll();
            $jsonfile = fopen("../View/json/PhotoList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'top'=>$n['top'] ,'uid' => $n['uid'], 'des' => $n['des'], 'praise' => $n['praise'], 'comment' => $n['comment'], 'img1' => $n['img1'],'img2'=>$n['img2'],'img3'=>$n['img3'],'img4'=>$n['img4'],'img5'=>$n['img5'],'img6'=>$n['img6'],'img7'=>$n['img7'],'img8'=>$n['img8'],'img9'=>$n['img9'],'created_at'=>$n['created_at'],'verify'=>$n['verify'],'operator'=>$n['operator']);
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

		public function AddPhoto()
		{
			$Photo = new Photo();
            $Photo->uid = $_REQUEST['wx'];
            $Photo->des = $_REQUEST['intro'];
            $Photo->praise = 0;
            $Photo->comment = 0;
            $Photo->created_at = $_REQUEST['created_at'];
            $Photo->operator = "";
            $Photo->top = 0;
            $Photo->verify = 0;
            $Photo->private = $_REQUEST['pri'];
            $images = explode("|",$_REQUEST["img"]);
            $Photo->img1 = "";
            $Photo->img2 = "";
            $Photo->img3 = "";
            $Photo->img4 = "";
            $Photo->img5 = "";
            $Photo->img6 = "";
            $Photo->img7 = "";
            $Photo->img8 = "";
            $Photo->img9 = "";
            for($i=1;$i<=count($images);$i++)
            {
                $img = "img".$i;
                $Photo->$img = $images[$i-1];
            }
			$ss = new PhotoServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ss->InsertPhoto($Photo);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdatePhoto()
		{
            $Photo = new Photo();
            $Photo->id = $_REQUEST['id'];
            $Photo->name = $_REQUEST['name'];
            $Photo->created_at = $_REQUEST['created_at'];
            $Photo->brief = $_REQUEST['brief'];
            $Photo->recommend = $_REQUEST['recommend'];
            $Photo->intro = $str = str_replace('\'', '\"', $_REQUEST['intro']);
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new PhotoServer();
			$result = $ss->UpdatePhoto($Photo,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelPhoto(){
			$id = $_REQUEST['id'];
            $ss = new PhotoServer();
            $result=$ss->DeletePhoto($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	PhotoControl::UpdatePhotoJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function BatchDelPhoto(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new PhotoServer();
            $result=$as->BatchDeletePhoto($str);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
                PhotoControl::UpdatePhotoJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

		public function GoTop(){
		    $id = $_REQUEST['id'];
            $top = $_REQUEST['top'];
            $ss = new PhotoServer();
            $Photo = new Photo();
            $Photo->id = $id;
            $Photo->top = $top;
            $result = $ss->UpdatePhoto($Photo,"top");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                PhotoControl::UpdatePhotoJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function ChangeShow(){
            $id = $_REQUEST['id'];
            $show = $_REQUEST['show'];
            $ss = new PhotoServer();
            $Photo = new Photo();
            $Photo->id = $id;
            $Photo->show = $show;
            $result = $ss->UpdatePhoto($Photo,"isshow");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                PhotoControl::UpdatePhotoJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function VerifyPhoto(){
            $id = $_REQUEST['id'];
            $verify = $_REQUEST['verify'];
            $operator = Security::decrypt($_REQUEST['operator']);
            $ss = new PhotoServer();
            $Photo = new Photo();
            $Photo->id = $id;
            $Photo->verify = $verify;
            $Photo->operator = $operator;
            $result = $ss->UpdatePhoto($Photo,"verify");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                PhotoControl::UpdatePhotoJson();
                $re['state']='1';
                $re['content']= $operator;
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function Praise(){
		    $openid = $_REQUEST['openid'];
		    $photo_id = $_REQUEST['photo_id'];
		    $praise = new Praise();
		    $praise->openid = $openid;
		    $praise->photo_id = $photo_id;
            date_default_timezone_set('PRC');
            $praise->created_at = date('Y-m-d H:i:s', time());
		    $ps = new PraiseServer();
            $re = array('state'=>'0','content'=>'点赞失败');
		    $result = $ps->InsertPraise($praise);
		    if($result==""){
                $re['state'] = "1";
                $re['content'] = "点赞成功";
            }
            else{
                $re['state'] = "0";
                $re['content'] = "点赞失败，"+$result;
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function UnPraise(){
            $id = $_REQUEST['id'];
            $ps = new PraiseServer();
            $re = array('state'=>'0','content'=>'取消点赞失败');
            $result = $ps->DeletePraise($id);
            if($result==""){
                $re['state'] = "1";
                $re['content'] = "取消点赞成功";
            }
            else{
                $re['state'] = "0";
                $re['content'] = "取消点赞失败，"+$result;
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function GetPraise(){
            $wherelist = array();
            if($_REQUEST['id']!=""||$_REQUEST['id']!=null){
                $id = $_REQUEST['id'];
                $wherelist[] = "id = '{$id}'";
            }
            if($_REQUEST['openid']!=""||$_REQUEST['openid']!=null){
                $openid = $_REQUEST['openid'];
                $wherelist[] = "openid = '{$openid}'";
            }
            if($_REQUEST['photo_id']!=""||$_REQUEST['photo_id']!=null){
                $photo_id = $_REQUEST['photo_id'];
                $wherelist[] = "photo_id = '{$photo_id}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
            $ps = new PraiseServer();
            $result = $ps->QueryPraise($where);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'openid' => $n['openid'],'photo_id'=>$n['photo_id'],'created_at' => $n['created_at']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }
	}
?>
