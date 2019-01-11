<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once '../Model/Route.php';
	require_once '../DataBaseHandle/RouteServer.php';
	require_once '../Extensions/NumUtil.php';
	header("Content-Type: text/html;charset=utf-8");
header('cache-control:private');
    error_reporting(0);
	//session_start();
	Class RouteControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    RouteControl::GetList();
					break;
                case "all":
                    RouteControl::GetAll();
                    break;
				case "add":
                    RouteControl::AddRoute();
					break;
				case "del":
                    RouteControl::DelRoute();
					break;
                case "batchDel":
                    RouteControl::BatchDelRoute();
                    break;
				case "edit":
                    RouteControl::UpdateRoute();
					break;
				case "query":
                    RouteControl::GetRoute();
					break;
                case "search":
                    RouteControl::GetRoutes();
                    break;
                case "verify_name":
                    RouteControl::VerifyName();
                    break;
                case "verify_id_name":
                    RouteControl::VerifyIdName();
                    break;
                case "pic":
                    RouteControl::UploadPic();
                    break;
                case "condition":
                    RouteControl::ConditionSearch();
                    break;
			}
		}

		public function GetList()
		{
            $ss = new RouteServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>"未获取数据");
            $jsonfile = fopen("../View/json/routeList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'pic'=>$n['pic'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
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
            $ss = new RouteServer();
            $result = $ss->GetAll();
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'pic'=>$n['pic'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function VerifyName(){
		    $area_id = $_REQUEST['area_id'];
            $name = $_REQUEST['name'];
            $time = $_REQUEST['time'];
            $type = $_REQUEST['type'];
            $ss = new RouteServer();
            $result = $ss->VerifyName($area_id,$name,$time,$type);
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
            $ss = new RouteServer();
            $result = $ss->VerifyIdName($area_id,$name);
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

        public function GetType()
        {
            $ss = new RouteServer();
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

        public function GetRoutes(){
            $wherelist = array();
            if($_REQUEST['id']!=""||$_REQUEST['id']!=null){
                $wherelist[] = "route.id = '{$_REQUEST['id']}'";
            }
            if($_REQUEST['name']!=""||$_REQUEST['name']!=null){
                $wherelist[] = "route.name like '%{$_REQUEST['name']}%'";
            }
            if($_REQUEST['area_id']!=""||$_REQUEST['area_id']!=null){
                $wherelist[] = "area_id = '{$_REQUEST['area_id']}'";
            }
            if($_REQUEST['type']!=""||$_REQUEST['type']!=null){
                $wherelist[] = "type = '{$_REQUEST['type']}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
            $ss = new RouteServer();
            $result = $ss->QueryRoutes($where);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'pic'=>$n['pic'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
            }
            $re['content'] = $row;
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function ConditionSearch(){
            $condition = $_REQUEST['condition'];
            if(strpos($condition,'/') !==false){
                $conditionArray  = explode("/",$condition);
            }
            else{
                $conditionArray = array($condition);
            }
            $ss = new RouteServer();
            $result = $ss->QueryConditionRoutes($conditionArray);
            $re = array('state'=>'0','content'=>"未获取数据");
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'pic'=>$n['pic'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
            }
            $re['content'] = $row;
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetRoute(){
            $wherelist = array();
            if($_REQUEST['id']!=""||$_REQUEST['id']!=null){
                $wherelist[] = "route.id = '{$_REQUEST['id']}'";
            }
            if($_REQUEST['name']!=""||$_REQUEST['name']!=null){
                $wherelist[] = "route.name like '%{$_REQUEST['name']}%'";
            }
            if($_REQUEST['area_id']!=""||$_REQUEST['area_id']!=null){
                $wherelist[] = "area_id = '{$_REQUEST['area_id']}'";
            }
            if($_REQUEST['type']!=""||$_REQUEST['type']!=null){
                $wherelist[] = "type = '{$_REQUEST['type']}'";
            }
            //组装查询条件
            if(count($wherelist) > 0){
                $where = " where ".implode(' and ' , $wherelist);
            }
            //判断查询条件
            $where = isset($where) ? $where : '';
		    $ss = new RouteServer();
            $result = $ss->QueryRoute($where);
            $re = array('state'=>'0','content'=>"未获取数据");
            $route = array();
            $routes = array();
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('type'=>$n['type'],'name'=>$n['name'],'time'=>$n['time'],'route'=>$n['route']);
                $route[] =  array('type'=>$n['type'],'name'=>$n['name']);
                //$row[] = array('id' => $n['id'],'pic'=>$n['pic'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
            }
            $route = RouteControl::array_unset_tt($route,"name");
            for($i=0;$i<count($route);$i++){
                for($j=0;$j<count($row);$j++){
                    if($row[$j]['name']==$route[$i]['name']&&$row[$j]['type']==$route[$i]['type']){
                        if($row[$j]['time']=="上午")
                        {
                            $row[$j]['time'] = "0";
                        }
                        else if($row[$j]['time']=="下午"){
                            $row[$j]['time'] = "9999";
                        }
                        else{
                            $row[$j]['time'] = NumUtil::findNum($row[$j]['time']);
                        }
                        $routes[] = array('time'=>$row[$j]['time'],'route'=>$row[$j]['route']);
                    }
                }
                $time = array();
                foreach ($routes as $r)
                {
                    $time[]  = $r['time'];
                }
                array_multisort($time, SORT_ASC, $routes);
                for($m=0;$m<count($routes);$m++){
                    if($routes[$m]['time']=="0"){
                        $routes[$m]['time'] = "上午";
                    }
                    else if($routes[$m]['time']=="9999"){
                        $routes[$m]['time'] = "下午";
                    }
                    else{
                        $routes[$m]['time'] = "第".NumUtil::zhuan($routes[$m]['time'])."天";
                    }
                }
                $route[$i]['routes'] = $routes;
                $routes = array();
            }
            $re['content'] = $route;
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function mySort($a,$b){
            if ($a['time']<$b['time']) return 1;
            else return -1;
        }

        function array_unset_tt($arr,$key){
            //建立一个目标数组
            $res = array();
            foreach ($arr as $value) {
                //查看有没有重复项
                if(isset($res[$value[$key]])){
                    unset($value[$key]);  //有：销毁
                }else{
                    $res[$value[$key]] = $value;
                }
            }
            foreach ($res as $value)
            {
                $newres[] = array('type'=>$value['type'],'name'=>$value['name']);
            }
            return $newres;
        }

		public function UpdateRouteJson(){
            $ss = new RouteServer();
            $result = $ss->GetAll();
            $jsonfile = fopen("../View/json/routeList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'pic'=>$n['pic'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
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

		public function AddRoute()
		{
			$route = new Route();
			$route->area_id = $_REQUEST['area_id'];
            $route->name = $_REQUEST['name'];
            $route->type = $_REQUEST['type'];
            $route->time = $_REQUEST['time'];
            $route->created_at = $_REQUEST['created_at'];
            $route->pic = $_REQUEST['pic'];
            $route->route = $_REQUEST['route'];
			$ss = new RouteServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ss->InsertRoute($route);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateRoute()
		{
            $route = new Route();
            $route->id = $_REQUEST['id'];
            $route->area_id = $_REQUEST['area_id'];
            $route->name = $_REQUEST['name'];
            $route->route = $_REQUEST['route'];
			$re = array('state'=>'0','content'=>'修改失败');
			$ss = new RouteServer();
			$result = $ss->UpdateRoute($route,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelRoute(){
			$id = $_REQUEST['id'];
            $ss = new RouteServer();
            $result=$ss->DeleteRoute($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	RouteControl::UpdateRouteJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

        public function BatchDelRoute(){
            $id = $_REQUEST['del_id'];
            $str = implode("','",$id);
            $str = "('{$str}')";
            $as = new RouteServer();
            $result=$as->BatchDeleteRoute($str);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
                RouteControl::UpdateRouteJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function UploadPic(){
            $re = array('state'=>'0','content'=>'');
            $src = "";
            $id = $_REQUEST['routeid'];
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
                       /* $source =  $filename;
                        $dst_img = $filename;
                        $percent = 0.5;
                        $image = (new ImageCompress($source,$percent))->compressImg($dst_img);*/
                        $route = new Route();
                        $route->id = $id;
                        $route->pic = $src;
                        $as = new RouteServer();
                        $result = $as->UpdateRoute($route,"pic");
                        if($result) {
                            RouteControl::UpdateRouteJson();
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
