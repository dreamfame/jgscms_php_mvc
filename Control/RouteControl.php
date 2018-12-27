<?php
	require_once '../Model/Route.php';
	require_once '../DataBaseHandle/RouteServer.php';
	header("Content-Type: text/html;charset=utf-8");
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
                case "verify_name":
                    RouteControl::VerifyName();
                    break;
                case "verify_id_name":
                    RouteControl::VerifyIdName();
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
                $row[] = array('id' => $n['id'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
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
                $row[] = array('id' => $n['id'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function VerifyName(){
		    $area_id = $_REQUEST['area_id'];
            $name = $_REQUEST['name'];
            $ss = new RouteServer();
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
            $ss = new RouteServer();
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

        public function GetRoute(){
            $wherelist = array();
            if($_REQUEST['id']!=""||$_REQUEST['id']!=null){
                $wherelist[] = "id = '{$_REQUEST['id']}'";
            }
            if($_REQUEST['name']!=""||$_REQUEST['name']!=null){
                $wherelist[] = "name like '%{$_REQUEST['name']}%'";
            }
            if($_REQUEST['area_id']!=""||$_REQUEST['area_id']!=null){
                $wherelist[] = "area_id = '{$_REQUEST['area_id']}'";
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
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'area_id'=>$n['area_id'],'area_name' => $n['area_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

		public function UpdateRouteJson(){
            $ss = new RouteServer();
            $result = $ss->GetAll();
            $jsonfile = fopen("../View/json/routeList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'],'scenic_id'=>$n['scenic_id'], 'scenic_name' => $n['scenic_name'], 'route' => $n['route'], 'type' => $n['type'], 'name' => $n['name'], 'time' => $n['time'],'created_at'=>$n['created_at']);
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
                NewsControl::UpdateRouteJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
	}
?>
