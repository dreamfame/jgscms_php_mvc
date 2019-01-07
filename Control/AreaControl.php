<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../Model/Area.php';
	require_once '../DataBaseHandle/AreaServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
    error_reporting(0);
	Class AreaControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    AreaControl::GetList();
					break;
                case "all":
                    AreaControl::GetAll();
                    break;
				case "add":
                    AreaControl::AddArea();
					break;
				case "del":
                    AreaControl::DelArea();
					break;
                case "batchDel":
                    AreaControl::BatchDelArea();
                    break;
				case "edit":
                    AreaControl::UpdateArea();
					break;
				case "query":
                    AreaControl::GetArea();
					break;
				case "gettype":
					AreaControl::GetType();
					break;
                case "top":
                    AreaControl::GoTop();
                    break;
                case "show":
                    AreaControl::ChangeShow();
                    break;
                case "map":
                    AreaControl::UploadMap();
                    break;
                case "id_name":
                    AreaControl::GetIdAndName();
                    break;
                case "verify_name":
                    AreaControl::VerifyName();
                    break;
                case "verify_id_name":
                    AreaControl::VerifyIdName();
                    break;
			}
		}

		public function GetList()
		{
            $ss = new AreaServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>"未获取数据");
            $jsonfile = fopen("../View/json/areaList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'area_map'=>$n['area_map'],'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
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
            $ss = new AreaServer();
            $result = $ss->GetShow();
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'area_map'=>$n['area_map'],'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetIdAndName()
        {
            $ss = new AreaServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetType()
        {
            $ss = new AreaServer();
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

        public function GetArea(){
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
		    $ss = new AreaServer();
            $result = $ss->QueryArea($where);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'],'area_map'=>$n['area_map'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

		public function UpdateAreaJson(){
            $ss = new AreaServer();
            $result = $ss->GetAll();
            $jsonfile = fopen("../View/json/areaList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'name' => $n['name'], 'brief' => $n['brief'], 'intro' => $n['intro'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'recommend'=>$n['recommend']);
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
		    $name = $_REQUEST['name'];
            $ss = new AreaServer();
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
            $ss = new AreaServer();
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

		public function AddArea()
		{
			$area = new Area();
            $area->name = $_REQUEST['name'];
            $area->intro = $str = str_replace('\'', '\"', $_REQUEST['intro']);
            $area->isshow = $_REQUEST['show'];
            $area->top = $_REQUEST['top'];
            $area->created_at = $_REQUEST['created_at'];
            $area->updated_at = $_REQUEST['created_at'];
            $area->brief = $_REQUEST['brief'];
            $area->recommend = $_REQUEST['recommend'];
            $area->see = 0;
            $area->area_map = $_REQUEST['pic'];
			$ss = new AreaServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ss->InsertArea($area);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateArea()
		{
            $area = new Area();
            $area->id = $_REQUEST['id'];
            $area->name = $_REQUEST['name'];
            $area->created_at = $_REQUEST['created_at'];
            $area->brief = $_REQUEST['brief'];
            $area->recommend = $_REQUEST['recommend'];
            $area->intro = $str = str_replace('\'', '\"', $_REQUEST['intro']);
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new AreaServer();
			$result = $ss->UpdateArea($area,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelArea(){
			$id = $_REQUEST['id'];
            $ss = new AreaServer();
            $result=$ss->DeleteArea($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	AreaControl::UpdateAreaJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function BatchDelArea(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new AreaServer();
            $result=$as->BatchDeleteArea($str);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
                AreaControl::UpdateAreaJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

		public function GoTop(){
		    $id = $_REQUEST['id'];
            $top = $_REQUEST['top'];
            $ss = new AreaServer();
            $area = new Area();
            $area->id = $id;
            $area->top = $top;
            $result = $ss->UpdateArea($area,"top");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                AreaControl::UpdateAreaJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function ChangeShow(){
            $id = $_REQUEST['id'];
            $show = $_REQUEST['show'];
            $ss = new AreaServer();
            $area = new Area();
            $area->id = $id;
            $area->show = $show;
            $result = $ss->UpdateArea($area,"isshow");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                AreaControl::UpdateAreaJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function UploadMap(){
            $re = array('state'=>'0','content'=>'');
            $src = "";
            $id = $_REQUEST['areaid'];
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
                        $area = new Area();
                        $area->id = $id;
                        $area->area_map = $src;
                        $as = new AreaServer();
                        $result = $as->UpdateArea($area,"area_map");
                        if($result) {
                            AreaControl::UpdateAreaJson();
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
